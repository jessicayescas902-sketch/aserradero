<?php
include '../config/database.php';

// Obtener productos registrados
$sql = "SELECT id, nombre, tipo, unidad_medida, stock 
        FROM productos 
        ORDER BY nombre ASC";

$stmt = $conexion->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Salida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0">Registrar Salida</h4>
        </div>

        <div class="card-body">

            <form action="guardar_salida.php" method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Producto</label>

                        <select name="producto_id" class="form-select" required>
                            <option value="">Seleccione un producto</option>

                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= htmlspecialchars($producto['id']) ?>">
                                    <?= htmlspecialchars($producto['nombre']) ?> 
                                    - Stock: <?= htmlspecialchars($producto['stock']) ?>
                                    <?= htmlspecialchars($producto['unidad_medida']) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cliente</label>
                        <input type="number" name="cliente_id" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="number" name="usuario_id" class="form-control" required>
                    </div>

                </div>

                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-secondary">← Volver</a>
                    <button type="submit" class="btn btn-success">Guardar Salida</button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>