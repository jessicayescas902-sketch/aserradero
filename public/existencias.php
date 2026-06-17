<?php
include '../config/database.php';

// Obtener productos con su stock actual
$sql = "
SELECT 
    id,
    nombre,
    tipo,
    unidad_medida,
    stock
FROM productos
ORDER BY nombre ASC
";

$stmt = $conexion->query($sql);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Existencias</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h3 class="mb-4">Existencias actuales</h3>

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Volver</a>

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Unidad de medida</th>
                <th>Stock actual</th>
            </tr>
        </thead>

        <tbody>

        <?php if (count($productos) > 0): ?>

            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?= htmlspecialchars($producto['id']) ?></td>
                    <td><?= htmlspecialchars($producto['nombre']) ?></td>
                    <td><?= htmlspecialchars($producto['tipo']) ?></td>
                    <td><?= htmlspecialchars($producto['unidad_medida']) ?></td>
                    <td>
                        <?php if ($producto['stock'] <= 0): ?>
                            <span class="badge bg-danger">
                                <?= htmlspecialchars($producto['stock']) ?>
                            </span>
                        <?php elseif ($producto['stock'] <= 5): ?>
                            <span class="badge bg-warning text-dark">
                                <?= htmlspecialchars($producto['stock']) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-success">
                                <?= htmlspecialchars($producto['stock']) ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="5" class="text-center">
                    No hay productos registrados.
                </td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>

</div>

</body>
</html>