<?php
include '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: entradas.php');
    exit;
}

$fecha = trim($_POST['fecha'] ?? '');
$folio = trim($_POST['folio'] ?? '');
$proveedorId = filter_input(
    INPUT_POST,
    'proveedor_id',
    FILTER_VALIDATE_INT
);
$cantidad = filter_input(
    INPUT_POST,
    'cantidad',
    FILTER_VALIDATE_FLOAT
);

$errores = [];

$fechaValida = DateTime::createFromFormat('Y-m-d', $fecha);

if (!$fechaValida || $fechaValida->format('Y-m-d') !== $fecha) {
    $errores[] = 'Selecciona una fecha válida.';
}

if ($folio === '') {
    $errores[] = 'El folio es obligatorio.';
}

if (!$proveedorId || $proveedorId <= 0) {
    $errores[] = 'Selecciona un proveedor válido.';
}

if ($cantidad === false || $cantidad === null || $cantidad <= 0) {
    $errores[] = 'La cantidad debe ser mayor que cero.';
}

if ($errores) {
    $_SESSION['error'] = implode(' ', $errores);
    header('Location: entradas.php');
    exit;
}

try {
    // Verificar que el proveedor existe.
    $consultaProveedor = $conexion->prepare(
        "SELECT id FROM proveedores WHERE id = :id"
    );

    $consultaProveedor->execute([
        ':id' => $proveedorId
    ]);

    if (!$consultaProveedor->fetch()) {
        throw new RuntimeException('El proveedor seleccionado no existe.');
    }

    $consulta = $conexion->prepare(
        "INSERT INTO entradas (
            fecha,
            folio,
            proveedor_id,
            cantidad
        ) VALUES (
            :fecha,
            :folio,
            :proveedor_id,
            :cantidad
        )"
    );

    $consulta->execute([
        ':fecha' => $fecha,
        ':folio' => $folio,
        ':proveedor_id' => $proveedorId,
        ':cantidad' => number_format($cantidad, 3, '.', '')
    ]);

    $_SESSION['mensaje'] = 'Entrada registrada correctamente.';
} catch (Throwable $e) {
    error_log('Error al guardar entrada: ' . $e->getMessage());
    $_SESSION['error'] = 'No fue posible registrar la entrada.';
}

header('Location: entradas.php');
exit;