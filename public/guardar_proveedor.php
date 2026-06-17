<?php
include '../config/database.php';

if ($_POST) {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'] ?: null;
    $direccion = $_POST['direccion'] ?: null;

    $sql = "INSERT INTO proveedores (nombre, telefono, direccion) 
            VALUES (?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$nombre, $telefono, $direccion]);

    echo "Proveedor guardado correctamente";
}
?>