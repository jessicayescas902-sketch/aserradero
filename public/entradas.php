<?php
include '../config/database.php';
session_start();

// productos
$productos = $conexion->query("SELECT id, nombre FROM productos")->fetchAll(PDO::FETCH_ASSOC);

// proveedores
$proveedores = $conexion->query("SELECT id, nombre FROM proveedores")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Entrada</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4>Registrar Entrada</h4>
        </div>

        <div class="card-body">

            <form action="guardar_entradas.php" method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>Producto</label>
                        <select name="producto_id" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php foreach($productos as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= $p['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Proveedor</label>
                        <select name="proveedor_id" class="form-control" required>
                            <option value="">Seleccione</option>
                            <?php foreach($proveedores as $pr): ?>
                                <option value="<?= $pr['id'] ?>">
                                    <?= $pr['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Costo</label>
                        <input type="number" step="0.01" name="costo" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Fecha</label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label>Observaciones</label>
                        <textarea name="observaciones" class="form-control"></textarea>
                    </div>

                </div>

                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-secondary">← Volver</a>
                    <button class="btn btn-success">Guardar Entrada</button>
                </div>

            </form>

        </div>
    </div>
</div>

</body>
</html>