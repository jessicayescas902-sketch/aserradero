<?php
include '../config/database.php';
session_start();

$especie = 'Pino';

$proveedores = $conexion->query(
    'SELECT id, nombre FROM proveedores ORDER BY nombre'
)->fetchAll(PDO::FETCH_ASSOC);

$mensaje = $_SESSION['mensaje'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['error']);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Entrada de Pino</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container mt-5">
    <section class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Registrar remisión de Pino 🌲</h4>
        </div>

        <div class="card-body">
            <?php if ($mensaje): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form action="guardar_entradas.php" method="post">
                <input type="hidden" name="especie" value="Pino">

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="fecha" class="form-label">Fecha *</label>
                        <input type="date" id="fecha" name="fecha"
                               class="form-control"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="folio" class="form-label">Folio oficial *</label>
                        <input type="text" id="folio" name="folio"
                               class="form-control" maxlength="50" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="proveedor_id" class="form-label">Proveedor *</label>
                        <select id="proveedor_id" name="proveedor_id"
                                class="form-select" required>
                            <option value="">Seleccione un proveedor</option>
                            <?php foreach ($proveedores as $proveedor): ?>
                                <option value="<?= (int) $proveedor['id'] ?>">
                                    <?= htmlspecialchars($proveedor['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="cantidad" class="form-label">Cantidad (m³) *</label>
                        <input type="number" id="cantidad" name="cantidad"
                               class="form-control" min="0.001"
                               step="0.001" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="tipo_madera.php" class="btn btn-secondary">← Volver</a>
                    <button type="submit" class="btn btn-success">Guardar entrada de Pino</button>
                </div>
            </form>
        </div>
    </section>
</main>
</body>
</html>