<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar tipo de madera</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 40px;
        }

        .contenedor {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            overflow: hidden;
        }

        .titulo {
            background: #278b54;
            color: white;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .contenido {
            padding: 40px;
            text-align: center;
        }

        .contenido h2 {
            margin-bottom: 30px;
            color: #333;
        }

        .opciones {
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .opcion {
            display: block;
            width: 220px;
            padding: 30px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 24px;
            font-weight: bold;
            color: white;
            background: #278b54;
            transition: 0.2s;
        }

        .opcion:hover {
            transform: translateY(-3px);
            opacity: 0.9;
        }

        .volver {
            display: inline-block;
            margin-top: 35px;
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <div class="titulo">
        Registrar reembarque forestal 
    </div>

    <div class="contenido">

        <h2>¿Qué tipo de madera es?</h2>

        <div class="opciones">

            <a href="salidas_pino.php" class="opcion">
                🌲 Pino
            </a>

            <a href="salidas_encino.php" class="opcion">
                🌳 Encino
            </a>

        </div>

        <a href="/aserradero/aserradero/public/dashboard.php" class="volver">
            ← Volver
        </a>

    </div>

</div>

</body>
</html>