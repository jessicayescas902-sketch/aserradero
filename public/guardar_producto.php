
<?php
include '../config/database.php';


if ($_POST) {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $unidad = $_POST['unidad_medida'];
    $stock = $_POST['stock'];
    $stock_minimo = $_POST['stock_minimo'];
    $precio = $_POST['precio'];


    $sql = "INSERT INTO productos 
            (nombre, tipo, unidad_medida, stock, stock_minimo, precio, creado_en) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$nombre, $tipo, $unidad, $stock, $stock_minimo, $precio]);

    echo "Producto guardado correctamente";
     
}
?>