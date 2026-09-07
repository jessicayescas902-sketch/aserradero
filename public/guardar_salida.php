<?php

include '../config/database.php';
session_start();

function postDecimal($value): float
{
    $value = str_replace(',', '.', trim((string) $value));

    if ($value === '' || !is_numeric($value)) {
        throw new Exception('La cantidad debe ser numérica.');
    }

    return (float) $value;
}

try {
    $usuario_id = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;

    if (!$usuario_id) {
        throw new Exception('Sesión de usuario no válida.');
    }

    $tipo = $_POST['tipo'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $observaciones = trim($_POST['observaciones'] ?? '');

    if (!in_array($tipo, ['normal', 'remision_forestal'], true)) {
        throw new Exception('Tipo de salida inválido.');
    }

    if (!$fecha) {
        throw new Exception('La fecha es obligatoria.');
    }

    $cliente_id = null;

    if ($tipo === 'normal') {
        $numero_nota = trim($_POST['numero_nota'] ?? '');
        $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $ciudad = trim($_POST['ciudad'] ?? '');

        if ($numero_nota === '' || $nombre_cliente === '') {
            throw new Exception('El número de nota y el nombre del cliente son obligatorios.');
        }

        $productos_id = $_POST['producto_id'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];

        if (!is_array($productos_id) || !is_array($cantidades)
            || count($productos_id) === 0 || count($productos_id) !== count($cantidades)) {
            throw new Exception('Debe capturar al menos un producto válido.');
        }
    } else {
        $cliente_id = (int) ($_POST['cliente_id'] ?? 0);
        $bloque_id = (int) ($_POST['bloque_reembarque_id'] ?? 0);
        $folio_progresivo = trim($_POST['folio_progresivo'] ?? '');
        $folio_autorizado = trim($_POST['folio_autorizado'] ?? '');
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? '';
        $destinatario = trim($_POST['destinatario'] ?? '');
        $domicilio_destino = trim($_POST['domicilio_destino'] ?? '');
        $municipio = trim($_POST['municipio'] ?? '');
        $entidad = trim($_POST['entidad'] ?? '');
        $medio_transporte = trim($_POST['medio_transporte'] ?? '');
        $marca_vehiculo = trim($_POST['marca_vehiculo'] ?? '');
        $tipo_vehiculo = trim($_POST['tipo_vehiculo'] ?? '');
        $placas = trim($_POST['placas'] ?? '');
        $productos_id = [(int) ($_POST['producto_id_remision'] ?? 0)];
        $cantidades = [postDecimal($_POST['cantidad_remision'] ?? '')];

        if (!$cliente_id || !$bloque_id || $folio_progresivo === '' || $destinatario === '') {
            throw new Exception('Cliente, bloque mensual, folio progresivo y destinatario son obligatorios.');
        }
    }

    $conexion->beginTransaction();

    // En remisión se bloquea el bloque mensual: éste es el saldo autoritativo.
    if ($tipo === 'remision_forestal') {
        $stmt = $conexion->prepare(
            'SELECT id, saldo_actual, unidad_medida FROM bloques_reembarque WHERE id = ? AND activo = 1 FOR UPDATE'
        );
        $stmt->execute([$bloque_id]);
        $bloque = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$bloque) {
            throw new Exception('El bloque mensual ya no existe o está inactivo.');
        }

        $saldo_disponible = (float) $bloque['saldo_actual'];

        $stmt = $conexion->prepare(
            'SELECT 1 FROM remisiones_forestales WHERE bloque_reembarque_id = ? AND folio_progresivo = ? LIMIT 1'
        );
        $stmt->execute([$bloque_id, $folio_progresivo]);

        if ($stmt->fetchColumn()) {
            throw new Exception('Ese folio progresivo ya fue registrado en este bloque mensual.');
        }
    }

    $total_general = 0.0;
    $detalles = [];
    $stmtProducto = $conexion->prepare(
        'SELECT id, nombre, unidad_medida, stock, precio FROM productos WHERE id = ? FOR UPDATE'
    );

    foreach ($productos_id as $indice => $producto_id) {
        $producto_id = (int) $producto_id;
        $cantidad = postDecimal($cantidades[$indice] ?? '');

        if ($producto_id <= 0 || $cantidad <= 0) {
            throw new Exception('Producto y cantidad deben ser válidos.');
        }

        $stmtProducto->execute([$producto_id]);
        $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            throw new Exception('El producto seleccionado no existe.');
        }

        if ($tipo === 'remision_forestal'
            && strcasecmp(trim($producto['unidad_medida']), trim($bloque['unidad_medida'])) !== 0) {
            throw new Exception(
                "La unidad del producto ({$producto['unidad_medida']}) no coincide " .
                "con la del bloque ({$bloque['unidad_medida']})."
            );
        }

        if ($cantidad > (float) $producto['stock']) {
            throw new Exception("Stock insuficiente para {$producto['nombre']}. Disponible: {$producto['stock']} {$producto['unidad_medida']}.");
        }

        if ($tipo === 'remision_forestal') {
            if ($cantidad > $saldo_disponible) {
                throw new Exception("La cantidad amparada excede el saldo del bloque. Disponible: {$bloque['saldo_actual']} {$bloque['unidad_medida']}.");
            }

            $saldo_siguiente = round($saldo_disponible - $cantidad, 3);
        }

        $precio = (float) $producto['precio'];
        $importe = $cantidad * $precio;
        $total_general += $importe;
        $detalles[] = compact('producto_id', 'cantidad', 'precio', 'importe', 'producto');
    }

    $stmt = $conexion->prepare(
        'INSERT INTO salidas (tipo, cliente_id, total, fecha, observaciones, usuario_id) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$tipo, $cliente_id, $total_general, $fecha, $observaciones ?: null, $usuario_id]);
    $salida_id = $conexion->lastInsertId();

    $stmtDetalle = $conexion->prepare(
        'INSERT INTO salida_detalles (salida_id, producto_id, cantidad, precio, total) VALUES (?, ?, ?, ?, ?)'
    );
    $stmtStock = $conexion->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');

    foreach ($detalles as $detalle) {
        $stmtDetalle->execute([$salida_id, $detalle['producto_id'], $detalle['cantidad'], $detalle['precio'], $detalle['importe']]);
        $stmtStock->execute([$detalle['cantidad'], $detalle['producto_id']]);
    }

    if ($tipo === 'normal') {
        $stmt = $conexion->prepare(
            'INSERT INTO notas_normales (salida_id, numero_nota, nombre_cliente, direccion, ciudad, telefono) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$salida_id, $numero_nota, $nombre_cliente, $direccion ?: null, $ciudad ?: null, $telefono ?: null]);
    } else {
        $detalle = $detalles[0];
        $stmt = $conexion->prepare(
            'INSERT INTO remisiones_forestales
            (salida_id, bloque_reembarque_id, folio_progresivo, folio_autorizado, fecha_expedicion,
             fecha_vencimiento, destinatario, domicilio_destino, municipio, entidad,
             descripcion_producto, volumen_amparado, saldo_anterior, saldo_siguiente,
             unidad_medida, medio_transporte, marca_vehiculo, tipo_vehiculo, placas)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $salida_id, $bloque_id, $folio_progresivo, $folio_autorizado ?: null, $fecha,
            $fecha_vencimiento ?: null, $destinatario, $domicilio_destino ?: null,
            $municipio ?: null, $entidad ?: null, $detalle['producto']['nombre'],
            $detalle['cantidad'], $saldo_disponible, $saldo_siguiente,
            $detalle['producto']['unidad_medida'], $medio_transporte ?: null,
            $marca_vehiculo ?: null, $tipo_vehiculo ?: null, $placas ?: null
        ]);

        $stmt = $conexion->prepare('UPDATE bloques_reembarque SET saldo_actual = ? WHERE id = ?');
        $stmt->execute([$saldo_siguiente, $bloque_id]);
    }

    $conexion->commit();
    $_SESSION['mensaje'] = 'La salida se guardó correctamente.';
} catch (Throwable $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();
}

header('Location: salidas.php');
exit();
