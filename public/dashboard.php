<?php
require_once("../includes/auth.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">Sistema de Inventario - Aserradero</span>
    <div class="text-white">
        Bienvenido, <?php echo $_SESSION["nombre"]; ?> |
        <a href="logout.php" class="text-warning text-decoration-none">Salir</a>
    </div>
</nav>

<!-- Contenido -->
<div class="container mt-5">

    <div class="row g-4">

        <div class="col-md-4">
            <a href="productos.php" class="text-decoration-none">
                <div class="card text-center shadow p-4">
                    <h5>Productos</h5>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="entradas.php" class="text-decoration-none">
                <div class="card text-center shadow p-4">
                    <h5>Entradas</h5>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="salidas.php" class="text-decoration-none">
                <div class="card text-center shadow p-4">
                    <h5>Salidas</h5>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="existencias.php" class="text-decoration-none">
                <div class="card text-center shadow p-4">
                    <h5>Existencias</h5>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="proveedores.php" class="text-decoration-none">
                <div class="card text-center shadow p-4">
                    <h5>Proveedores</h5>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="reportes.php" class="text-decoration-none">
                <div class="card text-center shadow p-4">
                    <h5>Reportes</h5>
                </div>
            </a>
        </div>

    </div>

</div>

</body>
</html>