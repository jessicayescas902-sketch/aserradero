<?php

include '../config/database.php';
session_start();

try {

    $nombre = trim($_POST['nombre'] ?? '');
    $curp = strtoupper(trim($_POST['curp'] ?? ''));
    $codigo_identificacion = trim($_POST['codigo_identificacion'] ?? '');
    $rfn = trim($_POST['rfn'] ?? '');
    $domicilio = trim($_POST['domicilio'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre === '') {
        throw new Exception('El nombre es obligatorio.');
    }

    if ($curp !== '' && strlen($curp) !== 18) {
        throw new Exception('La CURP debe tener 18 caracteres.');
    }

    $stmt = $conexion->prepare(
        'INSERT INTO clientes
        (nombre, curp, codigo_identificacion, rfn, direccion, telefono)
        VALUES (?, ?, ?, ?, ?, ?)'
    );

    $stmt->execute([
        $nombre,
        $curp !== '' ? $curp : null,
        $codigo_identificacion !== '' ? $codigo_identificacion : null,
        $rfn !== '' ? $rfn : null,
        $domicilio !== '' ? $domicilio : null,
        $telefono !== '' ? $telefono : null
    ]);

    $_SESSION['mensaje'] = 'El cliente se guardó correctamente.';

} catch (Throwable $e) {

    $_SESSION['error'] = $e->getMessage();
}

header('Location: clientes.php');
exit();