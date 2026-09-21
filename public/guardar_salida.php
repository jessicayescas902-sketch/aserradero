<?php

include '../config/database.php';
session_start();

function postDecimal($value): float
{
    $value = str_replace(',', '.', trim((string) $value));

    if ($value === '' || !is_numeric($value)) {
        throw new Exception('La cantidad debe ser numérica.');
    }

    return (float) $value;
}

try {

    $fecha = trim($_POST['fecha'] ?? '');
    $folio = trim($_POST['folio'] ?? '');
    $destinatario = trim($_POST['destinatario'] ?? '');
    $cantidad = postDecimal($_POST['cantidad'] ?? '');
    $especie = strtolower(trim($_POST['especie'] ?? ''));

    if ($fecha === '') {
        throw new Exception('La fecha es obligatoria.');
    }

    if ($folio === '') {
        throw new Exception('El folio es obligatorio.');
    }

    if ($destinatario === '') {
        throw new Exception('El destinatario es obligatorio.');
    }

    if ($cantidad <= 0) {
        throw new Exception('La cantidad debe ser mayor que cero.');
    }

    if (!in_array($especie, ['pino', 'encino'], true)) {
        throw new Exception('La especie no es válida.');
    }

    $stmt = $conexion->prepare(
        'INSERT INTO salidas
        (fecha, folio, destinatario, cantidad, especie)
        VALUES (?, ?, ?, ?, ?)'
    );

    $stmt->execute([
        $fecha,
        $folio,
        $destinatario,
        $cantidad,
        $especie
    ]);

    $_SESSION['mensaje'] =
        'La salida de ' . ucfirst($especie) . ' se guardó correctamente.';

    } catch (Throwable $e) {

        echo '<h2>Error al guardar la salida</h2>';
    
        echo '<pre>';
        echo htmlspecialchars($e->getMessage());
        echo '</pre>';
    
        echo '<h3>Datos recibidos:</h3>';
    
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
    
        echo '<h3>Base de datos:</h3>';
    
        echo '<pre>';
        echo htmlspecialchars(
            $conexion->query('SELECT DATABASE()')->fetchColumn()
        );
        echo '</pre>';
    
        exit();
    }

if ($especie === 'encino') {
    header('Location: salidas_encino.php');
} else {
    header('Location: salidas_pino.php');
}

exit();