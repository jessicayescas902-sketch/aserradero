<?php
require_once("../includes/auth.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

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

    <h3 class="mb-4 text-center">Módulo de Reportes</h3>

    <div class="row g-4 justify-content-center">

        <div class="col-md-4">
            <a href="reporte_entradas_especie.php" class="text-decoration-none">
                <div class="card text-center shadow p-4">
                    <h5>Reporte Entradas</h5>
                    <p class="text-muted mb-0">
                        Consultar entradas por especie y mes
                    </p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="reporte_salidas_especie.php" class="text-decoration-none">
                <div class="card text-center shadow p-4">
                    <h5>Reporte Salidas</h5>
                    <p class="text-muted mb-0">
                        Consultar salidas por especie y mes
                    </p>
                </div>
            </a>
        </div>

    </div>

    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-secondary">
            ← Volver al Dashboard
        </a>
    </div>

</div>

</body>
</html>