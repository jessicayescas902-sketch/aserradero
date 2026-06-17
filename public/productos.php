<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Registrar Producto</h4>
        </div>

        <div class="card-body">

            <form action="guardar_producto.php" method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipo</label>
                        <input type="text" name="tipo" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unidad de Medida</label>
                        <input type="text" name="unidad_medida" class="form-control" placeholder="Ej: m3, kg, pieza" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" name="stock" class="form-control" min="0" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Stock Mínimo</label>
                        <input type="number" name="stock_minimo" class="form-control" min="0" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="dashboard.php">← Volver</a>
                    <button type="submit" class="btn btn-success">Guardar Producto</button>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>