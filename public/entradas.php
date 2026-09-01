<?php
include '../config/database.php';
session_start();

$proveedores = $conexion
    ->query("SELECT id, nombre FROM proveedores ORDER BY nombre")
    ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registrar Entrada</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Registrar entrada</h4>
        </div>

        <div class="card-body">

            <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['mensaje']) ?>
                </div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="guardar_entradas.php" method="POST">
                <div class="row">

                    <div class="col-md-3 mb-3">
                        <label for="fecha" class="form-label">Fecha</label>

                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="folio" class="form-label">Folio</label>

                        <input
                            type="text"
                            id="folio"
                            name="folio"
                            class="form-control"
                            maxlength="50"
                            required
                        >
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="proveedor_id" class="form-label">
                            Proveedor
                        </label>

                        <select
                            id="proveedor_id"
                            name="proveedor_id"
                            class="form-select"
                            required
                        >
                            <option value="">Seleccione un proveedor</option>

                            <?php foreach ($proveedores as $proveedor): ?>
                                <option value="<?= (int) $proveedor['id'] ?>">
                                    <?= htmlspecialchars(
                                        $proveedor['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="cantidad" class="form-label">
                            Cantidad
                        </label>

                        <input
                            type="number"
                            id="cantidad"
                            name="cantidad"
                            class="form-control"
                            min="0.001"
                            step="0.001"
                            placeholder="0.000"
                            required
                        >
                    </div>

                </div>

                <div class="d-flex justify-content-between">
                    <a href="dashboard.php" class="btn btn-secondary">
                        ← Volver
                    </a>

                    <button type="submit" class="btn btn-success">
                        Guardar entrada
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>