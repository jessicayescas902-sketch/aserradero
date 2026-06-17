<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h4>Registrar Proveedor</h4>
        </div>

        <div class="card-body">
            <form action="guardar_proveedor.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="3"></textarea>
                </div>
 <a href="dashboard.php" class="btn btn-secondary">← Volver</a>
                <button type="submit" class="btn btn-success">Guardar</button>
                <button type="reset" class="btn btn-secondary">Limpiar</button>

            </form>
        </div>
    </div>
</div>

</body>
</html>