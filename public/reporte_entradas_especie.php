<?php
include '../config/database.php';

$especie = $_GET['especie'] ?? '';
$mes = $_GET['mes'] ?? '';

$total = 0;
$entradas = [];

// Obtener meses disponibles desde la tabla entradas
$sqlMeses = "
    SELECT DISTINCT 
        DATE_FORMAT(fecha, '%Y-%m') AS mes_valor
    FROM entradas
    WHERE fecha IS NOT NULL
    ORDER BY mes_valor DESC
";

$stmtMeses = $conexion->query($sqlMeses);
$meses = $stmtMeses->fetchAll(PDO::FETCH_ASSOC);

if (!empty($especie) && !empty($mes)) {

    // Total por especie y mes
    $sqlTotal = "
        SELECT SUM(cantidad) AS total_cantidad 
        FROM entradas 
        WHERE especie = :especie
        AND DATE_FORMAT(fecha, '%Y-%m') = :mes
    ";

    $stmtTotal = $conexion->prepare($sqlTotal);
    $stmtTotal->bindParam(':especie', $especie);
    $stmtTotal->bindParam(':mes', $mes);
    $stmtTotal->execute();

    $resultado = $stmtTotal->fetch(PDO::FETCH_ASSOC);
    $total = $resultado['total_cantidad'] ?? 0;

    // Detalle por especie y mes
    $sqlDetalle = "
        SELECT id, fecha, folio, proveedor_id, cantidad, especie
        FROM entradas
        WHERE especie = :especie
        AND DATE_FORMAT(fecha, '%Y-%m') = :mes
        ORDER BY fecha ASC
    ";

    $stmtDetalle = $conexion->prepare($sqlDetalle);
    $stmtDetalle->bindParam(':especie', $especie);
    $stmtDetalle->bindParam(':mes', $mes);
    $stmtDetalle->execute();

    $entradas = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Entradas por Especie y Mes</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos para impresión / PDF -->
    <style>
        @media print {
            body {
                background: white !important;
            }

            form,
            .btn,
            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }

            .card-header {
                background-color: #2E8B57 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .container {
                margin-top: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            table {
                font-size: 12px;
            }

            .alert {
                border: 1px solid #000 !important;
                color: #000 !important;
                background: #fff !important;
            }
        }
    </style>
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header text-white" style="background-color: #2E8B57;">
            <h4 class="mb-0">Reporte de Entradas por Especie y Mes</h4>
        </div>

        <div class="card-body">

            <form method="GET" class="row mb-4">

                <div class="col-md-4">
                    <label class="form-label">Seleccione especie</label>
                    <select name="especie" class="form-select" required>
                        <option value="">Seleccione una opción</option>

                        <option value="Pino" <?= ($especie == 'Pino') ? 'selected' : '' ?>>
                            Pino
                        </option>

                        <option value="Encino" <?= ($especie == 'Encino') ? 'selected' : '' ?>>
                            Encino
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Seleccione mes</label>
                    <select name="mes" class="form-select" required>
                        <option value="">Seleccione un mes</option>

                        <?php foreach ($meses as $m): ?>
                            <option 
                                value="<?= htmlspecialchars($m['mes_valor']) ?>"
                                <?= ($mes == $m['mes_valor']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($m['mes_valor']) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        Filtrar
                    </button>

                    <a href="reporte_entradas_especie.php" class="btn btn-secondary me-2">
                        Limpiar
                    </a>

                    <?php if (!empty($especie) && !empty($mes)): ?>
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            Imprimir reporte
                        </button>
                    <?php endif; ?>
                </div>

            </form>

            <?php if (!empty($especie) && !empty($mes)): ?>

                <div class="alert alert-info">
                    <strong>
                        Total de entradas de <?= htmlspecialchars($especie) ?> en <?= htmlspecialchars($mes) ?>:
                    </strong>

                    <?= number_format($total, 3) ?>
                </div>

                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Folio</th>
                            <th>Proveedor</th>
                            <th>Cantidad</th>
                            <th>Especie</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($entradas) > 0): ?>

                            <?php foreach ($entradas as $entrada): ?>
                                <tr>
                                    <td><?= htmlspecialchars($entrada['id']) ?></td>
                                    <td><?= htmlspecialchars($entrada['fecha']) ?></td>
                                    <td><?= htmlspecialchars($entrada['folio']) ?></td>
                                    <td><?= htmlspecialchars($entrada['proveedor_id']) ?></td>
                                    <td><?= number_format($entrada['cantidad'], 3) ?></td>
                                    <td><?= htmlspecialchars($entrada['especie']) ?></td>
                                </tr>
                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="text-center">
                                    No hay entradas registradas para esa especie en ese mes.
                                </td>
                            </tr>

                        <?php endif; ?>
                    </tbody>
                </table>

            <?php endif; ?>

            <a href="reportes.php" class="btn btn-secondary no-print">
                ← Volver
            </a>

        </div>
    </div>

</div>

</body>
</html>