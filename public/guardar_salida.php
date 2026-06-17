<?php
include '../config/database.php';
session_start();

try {
    $conexion->beginTransaction();

    $producto_id = $_POST['producto_id'];
    $cliente_id = $_POST['cliente_id'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];
    $fecha = $_POST['fecha'];
    $obs = $_POST['observaciones'];
    $usuario_id = $_SESSION['id'];

    // 🔍 verificar stock actual
    $stmt = $conexion->prepare("SELECT stock FROM productos WHERE id = ?");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        throw new Exception("Producto no existe");
    }

    if ($producto['stock'] < $cantidad) {
        throw new Exception("Stock insuficiente");
    }

    // 🧾 insertar salida
    $sql = "INSERT INTO salidas 
        (producto_id, cliente_id, cantidad, precio, fecha, observaciones, usuario_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        $producto_id,
        $cliente_id,
        $cantidad,
        $precio,
        $fecha,
        $obs,
        $usuario_id
    ]);

    // 📉 actualizar stock
    $stmt = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
    $stmt->execute([$cantidad, $producto_id]);

    $conexion->commit();

    header("Location: salidas.php?ok=1");
    exit();

} catch (Exception $e) {
    $conexion->rollBack();
    echo "Error: " . $e->getMessage();
}
?>