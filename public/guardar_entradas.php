<?php
include '../config/database.php';
session_start();

try {
    $conexion->beginTransaction();

    $producto_id = $_POST['producto_id'];
    $proveedor_id = $_POST['proveedor_id'];
    $cantidad = $_POST['cantidad'];
    $costo = $_POST['costo'];
    $fecha = $_POST['fecha'];
    $obs = $_POST['observaciones'];
    $usuario_id = $_SESSION['id'];

    // 🧾 insertar entrada
    $sql = "INSERT INTO entradas 
        (producto_id, proveedor_id, cantidad, costo, fecha, observaciones, usuario_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        $producto_id,
        $proveedor_id,
        $cantidad,
        $costo,
        $fecha,
        $obs,
        $usuario_id
    ]);

    // 📈 aumentar stock
    $stmt = $conexion->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");
    $stmt->execute([$cantidad, $producto_id]);

    $conexion->commit();

    header("Location: entradas.php?ok=1");
    exit();

} catch (Exception $e) {
    $conexion->rollBack();
    echo "Error: " . $e->getMessage();
}
?>