<?php

include '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: entradas.php');
    exit;
}

try {
    $fecha = trim($_POST['fecha'] ?? '');
    $folio = trim($_POST['folio'] ?? '');
    $folioInicial = trim($_POST['folio_inicial'] ?? '');
    $proveedorId = filter_input(INPUT_POST, 'proveedor_id', FILTER_VALIDATE_INT);
    $cantidadTexto = str_replace(',', '.', trim($_POST['cantidad'] ?? ''));
    $cantidad = is_numeric($cantidadTexto) ? (float) $cantidadTexto : 0;
    $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);

    if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha) throw new RuntimeException('Selecciona una fecha válida.');
    if ($folio === '') throw new RuntimeException('El folio es obligatorio.');
    if (!$proveedorId) throw new RuntimeException('Selecciona un proveedor válido.');
    if ($cantidad <= 0) throw new RuntimeException('La cantidad debe ser mayor que cero.');

    $anio = (int) substr($fecha, 0, 4);
    $mes = (int) substr($fecha, 5, 2);
    $conexion->beginTransaction();

    $stmt = $conexion->prepare('SELECT id FROM proveedores WHERE id = ? FOR UPDATE');
    $stmt->execute([$proveedorId]);
    if (!$stmt->fetchColumn()) throw new RuntimeException('El proveedor seleccionado no existe.');

    $stmt = $conexion->prepare('SELECT id FROM entradas WHERE folio = ? LIMIT 1');
    $stmt->execute([$folio]);
    if ($stmt->fetchColumn()) throw new RuntimeException('Ese folio de remisión ya fue registrado.');

    $stmt = $conexion->prepare('SELECT id, saldo_actual, coeficiente, folio_inicial FROM bloques_reembarque WHERE anio = ? AND mes = ? FOR UPDATE');
    $stmt->execute([$anio, $mes]);
    $bloque = $stmt->fetch(PDO::FETCH_ASSOC);
    $cantidadAprovechable = round($cantidad * 0.59, 3);

    if ($bloque) {
        if ($folioInicial !== '' && $folioInicial !== $bloque['folio_inicial']) {
            throw new RuntimeException('El primer folio no coincide con el bloque mensual ya creado.');
        }

        $coeficiente = (float) $bloque['coeficiente'];
        $cantidadAprovechable = round($cantidad * $coeficiente, 3);
        $stmt = $conexion->prepare(
            'UPDATE bloques_reembarque
             SET volumen_entrada = volumen_entrada + ?,
                 saldo_inicial = saldo_inicial + ?,
                 saldo_actual = saldo_actual + ?
             WHERE id = ?'
        );
        $stmt->execute([$cantidad, $cantidadAprovechable, $cantidadAprovechable, $bloque['id']]);
    } else {
        if ($folioInicial === '') {
            throw new RuntimeException('Captura el primer folio oficial de reembarque para abrir el bloque mensual.');
        }

        $stmt = $conexion->prepare(
            'INSERT INTO bloques_reembarque
             (anio, mes, folio_inicial, volumen_entrada, coeficiente, saldo_inicial, saldo_actual, unidad_medida, activo)
             VALUES (?, ?, ?, ?, 0.5900, ?, ?, "M3", 1)'
        );
        $stmt->execute([$anio, $mes, $folioInicial, $cantidad, $cantidadAprovechable, $cantidadAprovechable]);
    }

    $stmt = $conexion->prepare('INSERT INTO entradas (fecha, folio, proveedor_id, cantidad) VALUES (?, ?, ?, ?)');
    $stmt->execute([$fecha, $folio, $proveedorId, number_format($cantidad, 3, '.', '')]);

    $conexion->commit();
    $_SESSION['mensaje'] = 'Remisión recibida registrada. El bloque mensual aumentó ' . number_format($cantidadAprovechable, 3) . ' M3.';
} catch (Throwable $e) {
    if (isset($conexion) && $conexion->inTransaction()) $conexion->rollBack();
    $_SESSION['error'] = $e->getMessage();
}

header('Location: entradas.php');
exit;
