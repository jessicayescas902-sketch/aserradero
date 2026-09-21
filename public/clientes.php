<?php

include '../config/database.php';
session_start();

$mensaje = $_SESSION['mensaje'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['mensaje'], $_SESSION['error']);

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registrar cliente</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<main class="container my-5">

    <?php if ($mensaje): ?>
        <div class="alert alert-success">
            <?= e($mensaje) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow">

        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Registrar cliente</h4>
        </div>

        <div class="card-body">

            <form action="guardar_cliente.php" method="post">

                <div class="row g-3">

                    <!-- Nombre -->
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">
                            Nombre <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="nombre"
                            name="nombre"
                            class="form-control"
                            maxlength="150"
                            required
                        >
                    </div>

                    <!-- CURP -->
                    <div class="col-md-6">
                        <label for="curp" class="form-label">
                            CURP
                        </label>

                        <input
                            type="text"
                            id="curp"
                            name="curp"
                            class="form-control"
                            maxlength="18"
                            style="text-transform: uppercase;"
                        >
                    </div>

                    <!-- Código de identificación -->
                    <div class="col-md-6">
                        <label for="codigo_identificacion" class="form-label">
                            Código de identificación
                        </label>

                        <input
                            type="text"
                            id="codigo_identificacion"
                            name="codigo_identificacion"
                            class="form-control"
                            maxlength="50"
                        >
                    </div>

                    <!-- RFN -->
                    <div class="col-md-6">
                        <label for="rfn" class="form-label">
                            RFN
                        </label>

                        <input
                            type="text"
                            id="rfn"
                            name="rfn"
                            class="form-control"
                            maxlength="50"
                        >
                    </div>

                    <!-- Domicilio -->
                    <div class="col-md-12">
                        <label for="domicilio" class="form-label">
                            Domicilio
                        </label>

                        <input
                            type="text"
                            id="domicilio"
                            name="domicilio"
                            class="form-control"
                            maxlength="255"
                        >
                    </div>

                    <!-- Teléfono -->
                    <div class="col-md-6">
                        <label for="telefono" class="form-label">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            id="telefono"
                            name="telefono"
                            class="form-control"
                            maxlength="30"
                        >
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">

                    <a
                        href="/aserradero/aserradero/public/dashboard.php"
                        class="btn btn-secondary"
                    >
                        ← Dashboard
                    </a>

                    <button type="submit" class="btn btn-success">
                        Guardar cliente
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>

</body>
</html>