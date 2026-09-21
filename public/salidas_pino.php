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

    <title>Salida de Pino</title>

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
            <h4 class="mb-0">Registrar salida de Pino</h4>
        </div>

        <div class="card-body">

            <form action="guardar_salida.php" method="post">

                <!-- Especie -->
                <input type="hidden" name="especie" value="pino">

                <div class="row g-3">

                    <div class="col-md-3">
                        <label for="fecha" class="form-label">
                            Fecha <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            id="fecha"
                            name="fecha"
                            class="form-control"
                            value="<?= date('Y-m-d') ?>"
                            required
                        >
                    </div>

                    <div class="col-md-3">
                        <label for="folio" class="form-label">
                            Folio <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="folio"
                            name="folio"
                            class="form-control"
                            maxlength="50"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="destinatario" class="form-label">
                            Destinatario o Código de Identificación
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="destinatario"
                            name="destinatario"
                            class="form-control"
                            maxlength="180"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="cantidad" class="form-label">
                            Cantidad <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            id="cantidad"
                            name="cantidad"
                            class="form-control"
                            step="0.001"
                            min="0.001"
                            required
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
                        Guardar salida de Pino
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>

</body>
</html>