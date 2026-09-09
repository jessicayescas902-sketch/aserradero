<?php

include '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tipo_madera.php');
    exit;
}

$especie = trim($_POST['especie'] ?? '');

$destino = match ($especie) {
    'Pino' => 'entradas_pino.php',
    'Encino' => 'entradas_encino.php',
    default => 'tipo_madera.php',
};

try {
    // =========================
    // RECIBIR DATOS DEL FORMULARIO
    // =========================

    $fecha = trim($_POST['fecha'] ?? '');
    $folio = trim($_POST['folio'] ?? '');

    $proveedorId = filter_input(
        INPUT_POST,
        'proveedor_id',
        FILTER_VALIDATE_INT
    );

    $cantidadTexto = str_replace(
        ',',
        '.',
        trim($_POST['cantidad'] ?? '')
    );

    $cantidad = is_numeric($cantidadTexto)
        ? (float) $cantidadTexto
        : 0;

    // =========================
    // VALIDACIONES
    // =========================

    $fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);

    if (
        !$fechaValida ||
        $fechaValida->format('Y-m-d') !== $fecha
    ) {
        throw new RuntimeException('Selecciona una fecha válida.');
    }

    if ($folio === '') {
        throw new RuntimeException('El folio es obligatorio.');
    }

    if (!$proveedorId) {
        throw new RuntimeException('Selecciona un proveedor válido.');
    }

    if ($cantidad <= 0) {
        throw new RuntimeException('La cantidad debe ser mayor que cero.');
    }

    if (!in_array($especie, ['Pino', 'Encino'], true)) {
        throw new RuntimeException('La especie de madera no es válida.');
    }

    // =========================
    // INICIAR TRANSACCIÓN
    // =========================

    $conexion->beginTransaction();

    // =========================
    // VERIFICAR PROVEEDOR
    // =========================

    $stmt = $conexion->prepare(
        'SELECT id
         FROM proveedores
         WHERE id = ?
         FOR UPDATE'
    );

    $stmt->execute([$proveedorId]);

    if (!$stmt->fetchColumn()) {
        throw new RuntimeException(
            'El proveedor seleccionado no existe.'
        );
    }

    // =========================
    // VERIFICAR FOLIO DUPLICADO
    // =========================

    $stmt = $conexion->prepare(
        'SELECT id
         FROM entradas
         WHERE folio = ?
         LIMIT 1'
    );

    $stmt->execute([$folio]);

    if ($stmt->fetchColumn()) {
        throw new RuntimeException(
            'Ese folio de remisión ya fue registrado.'
        );
    }

    // =========================
    // GUARDAR ENTRADA Y ESPECIE
    // =========================

    $stmt = $conexion->prepare(
        'INSERT INTO entradas
         (fecha, folio, proveedor_id, cantidad, especie)
         VALUES (?, ?, ?, ?, ?)'
    );

    $stmt->execute([
        $fecha,
        $folio,
        $proveedorId,
        number_format($cantidad, 3, '.', ''),
        $especie
    ]);

    $conexion->commit();

    $_SESSION['mensaje'] =
        'Remisión de ' . $especie . ' registrada correctamente.';

} catch (Throwable $e) {
    if (
        isset($conexion) &&
        $conexion->inTransaction()
    ) {
        $conexion->rollBack();
    }

    $_SESSION['error'] = $e->getMessage();
}

header('Location: ' . $destino);
exit;