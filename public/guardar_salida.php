<?php

include '../config/database.php';
session_start();

try {

    /*
    |--------------------------------------------------------------------------
    | Verificar usuario
    |--------------------------------------------------------------------------
    */

    $usuario_id = $_SESSION['usuario_id']
        ?? $_SESSION['id']
        ?? null;

    if (!$usuario_id) {
        throw new Exception('Sesión de usuario no válida.');
    }


    /*
    |--------------------------------------------------------------------------
    | Datos generales
    |--------------------------------------------------------------------------
    */

    $tipo = $_POST['tipo'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $observaciones = trim($_POST['observaciones'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | Validar tipo
    |--------------------------------------------------------------------------
    */

    if (!in_array(
        $tipo,
        ['normal', 'remision_forestal'],
        true
    )) {
        throw new Exception('Tipo de salida inválido.');
    }

    if (!$fecha) {
        throw new Exception('La fecha es obligatoria.');
    }


    /*
    |--------------------------------------------------------------------------
    | Datos específicos
    |--------------------------------------------------------------------------
    */

    $cliente_id = null;


    /*
    |--------------------------------------------------------------------------
    | NOTA NORMAL
    |--------------------------------------------------------------------------
    */

    if ($tipo === 'normal') {

        $numero_nota = trim(
            $_POST['numero_nota'] ?? ''
        );

        $nombre_cliente = trim(
            $_POST['nombre_cliente'] ?? ''
        );

        $telefono = trim(
            $_POST['telefono'] ?? ''
        );

        $direccion = trim(
            $_POST['direccion'] ?? ''
        );

        $ciudad = trim(
            $_POST['ciudad'] ?? ''
        );


        if ($numero_nota === '') {
            throw new Exception(
                'El número de nota es obligatorio.'
            );
        }


        if ($nombre_cliente === '') {
            throw new Exception(
                'El nombre del cliente es obligatorio.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Productos de nota normal
        |--------------------------------------------------------------------------
        */

        $productos_id =
            $_POST['producto_id'] ?? [];

        $cantidades =
            $_POST['cantidad'] ?? [];


        if (
            !is_array($productos_id) ||
            !is_array($cantidades)
        ) {
            throw new Exception(
                'Datos de productos inválidos.'
            );
        }


        if (count($productos_id) === 0) {
            throw new Exception(
                'Debe agregar al menos un producto.'
            );
        }


        if (
            count($productos_id) !==
            count($cantidades)
        ) {
            throw new Exception(
                'Los datos de los productos no coinciden.'
            );
        }

    }


    /*
    |--------------------------------------------------------------------------
    | REMISIÓN FORESTAL
    |--------------------------------------------------------------------------
    */

    else {

        $cliente_id =
            $_POST['cliente_id'] ?? null;

        $folio_progresivo = trim(
            $_POST['folio_progresivo'] ?? ''
        );

        $folio_autorizado = trim(
            $_POST['folio_autorizado'] ?? ''
        );

        $fecha_vencimiento =
            $_POST['fecha_vencimiento'] ?? null;

        $destinatario = trim(
            $_POST['destinatario'] ?? ''
        );

        $domicilio_destino = trim(
            $_POST['domicilio_destino'] ?? ''
        );

        $municipio = trim(
            $_POST['municipio'] ?? ''
        );

        $entidad = trim(
            $_POST['entidad'] ?? ''
        );

        $medio_transporte = trim(
            $_POST['medio_transporte'] ?? ''
        );

        $marca_vehiculo = trim(
            $_POST['marca_vehiculo'] ?? ''
        );

        $tipo_vehiculo = trim(
            $_POST['tipo_vehiculo'] ?? ''
        );

        $placas = trim(
            $_POST['placas'] ?? ''
        );


        /*
        |--------------------------------------------------------------------------
        | Validaciones de remisión
        |--------------------------------------------------------------------------
        */

        if (!$cliente_id) {
            throw new Exception(
                'Debe seleccionar un cliente.'
            );
        }


        if ($folio_progresivo === '') {
            throw new Exception(
                'El folio progresivo es obligatorio.'
            );
        }


        if ($destinatario === '') {
            throw new Exception(
                'El destinatario es obligatorio.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Un solo producto para la remisión
        |--------------------------------------------------------------------------
        */

        $producto_id =
            $_POST['producto_id'] ?? null;

        $cantidad =
            $_POST['cantidad'] ?? null;


        if (!$producto_id) {
            throw new Exception(
                'Debe seleccionar un producto.'
            );
        }


        if (
            $cantidad === null ||
            $cantidad === ''
        ) {
            throw new Exception(
                'La cantidad es obligatoria.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Convertimos el producto único a arreglos
        |
        | Esto permite utilizar el mismo proceso de
        | validación y actualización de stock.
        |--------------------------------------------------------------------------
        */

        $productos_id = [
            $producto_id
        ];

        $cantidades = [
            $cantidad
        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Iniciar transacción
    |--------------------------------------------------------------------------
    */

    $conexion->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | Variables para cálculo
    |--------------------------------------------------------------------------
    */

    $total_general = 0;

    $detalles = [];

    $descripcion_productos = [];

    $volumen_total = 0;

    $unidades = [];


    /*
    |--------------------------------------------------------------------------
    | Procesar productos
    |--------------------------------------------------------------------------
    */

    foreach (
        $productos_id as $indice => $producto_id
    ) {

        $producto_id = (int) $producto_id;

        $cantidad = (float) $cantidades[$indice];


        /*
        |--------------------------------------------------------------------------
        | Validar producto
        |--------------------------------------------------------------------------
        */

        if ($producto_id <= 0) {
            throw new Exception(
                'Producto inválido.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validar cantidad
        |--------------------------------------------------------------------------
        */

        if ($cantidad <= 0) {
            throw new Exception(
                'La cantidad debe ser mayor que cero.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Consultar producto
        |
        | FOR UPDATE bloquea el registro durante
        | la transacción para evitar problemas
        | de stock.
        |--------------------------------------------------------------------------
        */

        $stmt = $conexion->prepare("
            SELECT
                id,
                nombre,
                unidad_medida,
                stock,
                precio
            FROM productos
            WHERE id = ?
            FOR UPDATE
        ");

        $stmt->execute([
            $producto_id
        ]);

        $producto =
            $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$producto) {
            throw new Exception(
                "El producto con ID {$producto_id} no existe."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Verificar stock
        |--------------------------------------------------------------------------
        */

        $stock =
            (float) $producto['stock'];


        if ($cantidad > $stock) {

            throw new Exception(
                "Stock insuficiente para {$producto['nombre']}. " .
                "Disponible: {$producto['stock']} " .
                "{$producto['unidad_medida']}."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Precio desde la base de datos
        |
        | NO confiamos en el precio enviado
        | por JavaScript.
        |--------------------------------------------------------------------------
        */

        $precio =
            (float) $producto['precio'];


        /*
        |--------------------------------------------------------------------------
        | Calcular importe
        |--------------------------------------------------------------------------
        */

        $importe =
            $cantidad * $precio;


        $total_general += $importe;


        /*
        |--------------------------------------------------------------------------
        | Guardar detalle temporal
        |--------------------------------------------------------------------------
        */

        $detalles[] = [
            'producto_id' => $producto_id,
            'cantidad' => $cantidad,
            'precio' => $precio,
            'total' => $importe
        ];


        /*
        |--------------------------------------------------------------------------
        | Información para remisión forestal
        |--------------------------------------------------------------------------
        */

        $descripcion_productos[] =
            $producto['nombre'];


        $volumen_total += $cantidad;


        $unidades[] =
            $producto['unidad_medida'];
    }


    /*
    |--------------------------------------------------------------------------
    | Insertar salida principal
    |--------------------------------------------------------------------------
    */

    $stmt = $conexion->prepare("
        INSERT INTO salidas
        (
            tipo,
            cliente_id,
            total,
            fecha,
            observaciones,
            usuario_id
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");


    $stmt->execute([
        $tipo,
        $cliente_id,
        $total_general,
        $fecha,
        $observaciones ?: null,
        $usuario_id
    ]);


    /*
    |--------------------------------------------------------------------------
    | Obtener ID de la salida
    |--------------------------------------------------------------------------
    */

    $salida_id =
        $conexion->lastInsertId();


    /*
    |--------------------------------------------------------------------------
    | Insertar detalles
    |--------------------------------------------------------------------------
    */

    $stmtDetalle = $conexion->prepare("
        INSERT INTO salida_detalles
        (
            salida_id,
            producto_id,
            cantidad,
            precio,
            total
        )
        VALUES (?, ?, ?, ?, ?)
    ");


    /*
    |--------------------------------------------------------------------------
    | Actualizar stock
    |--------------------------------------------------------------------------
    */

    $stmtStock = $conexion->prepare("
        UPDATE productos
        SET stock = stock - ?
        WHERE id = ?
    ");


    foreach ($detalles as $detalle) {

        /*
        | Insertar detalle
        */

        $stmtDetalle->execute([
            $salida_id,
            $detalle['producto_id'],
            $detalle['cantidad'],
            $detalle['precio'],
            $detalle['total']
        ]);


        /*
        | Descontar stock
        */

        $stmtStock->execute([
            $detalle['cantidad'],
            $detalle['producto_id']
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Guardar documento específico
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | NOTA NORMAL
    |--------------------------------------------------------------------------
    */

    if ($tipo === 'normal') {

        $stmt = $conexion->prepare("
            INSERT INTO notas_normales
            (
                salida_id,
                numero_nota,
                nombre_cliente,
                direccion,
                ciudad,
                telefono
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ");


        $stmt->execute([
            $salida_id,
            $numero_nota,
            $nombre_cliente,
            $direccion ?: null,
            $ciudad ?: null,
            $telefono ?: null
        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | REMISIÓN FORESTAL
    |--------------------------------------------------------------------------
    */

    else {

        /*
        |--------------------------------------------------------------------------
        | Como ahora solamente puede existir un producto,
        | tomamos directamente el primer elemento.
        |--------------------------------------------------------------------------
        */

        $descripcion_producto =
            $descripcion_productos[0];


        $unidad_medida =
            $unidades[0];


        /*
        |--------------------------------------------------------------------------
        | Volumen amparado
        |--------------------------------------------------------------------------
        */

        $volumen_amparado =
            $volumen_total;


        /*
        |--------------------------------------------------------------------------
        | Insertar remisión forestal
        |--------------------------------------------------------------------------
        */

        $stmt = $conexion->prepare("
            INSERT INTO remisiones_forestales
            (
                salida_id,
                folio_progresivo,
                folio_autorizado,
                fecha_expedicion,
                fecha_vencimiento,
                destinatario,
                domicilio_destino,
                municipio,
                entidad,
                descripcion_producto,
                volumen_amparado,
                unidad_medida,
                medio_transporte,
                marca_vehiculo,
                tipo_vehiculo,
                placas
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?
            )
        ");


        $stmt->execute([
            $salida_id,
            $folio_progresivo,
            $folio_autorizado ?: null,
            $fecha,
            $fecha_vencimiento ?: null,
            $destinatario,
            $domicilio_destino ?: null,
            $municipio ?: null,
            $entidad ?: null,
            $descripcion_producto,
            $volumen_amparado,
            $unidad_medida,
            $medio_transporte ?: null,
            $marca_vehiculo ?: null,
            $tipo_vehiculo ?: null,
            $placas ?: null
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Confirmar transacción
    |--------------------------------------------------------------------------
    */

    $conexion->commit();


    /*
    |--------------------------------------------------------------------------
    | Mensaje de éxito
    |--------------------------------------------------------------------------
    */

    $_SESSION['mensaje'] =
        'La salida se guardó correctamente.';


    header('Location: salidas.php');

    exit();


} catch (Exception $e) {


    /*
    |--------------------------------------------------------------------------
    | Cancelar transacción si existe
    |--------------------------------------------------------------------------
    */

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }


    /*
    |--------------------------------------------------------------------------
    | Guardar error
    |--------------------------------------------------------------------------
    */

    $_SESSION['error'] =
        $e->getMessage();


    header('Location: salidas.php');

    exit();
}
?>