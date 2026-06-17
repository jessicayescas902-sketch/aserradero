<?php
session_start();
require_once("../config/database.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = trim($_POST["password"]);

    $sql = "SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(":usuario", $usuario);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user["password"]) {
        $_SESSION["usuario_id"] = $user["id"];
        $_SESSION["nombre"] = $user["nombre"];
        $_SESSION["rol"] = $user["rol"];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Aserradero</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h2>Iniciar sesión</h2>

        <?php if ($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="login-container">
    <h2>Sistema de Aserradero</h2>
    <p class="login-subtitle">Acceso administrativo</p>

    <?php if ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Entrar al sistema</button>
    </form>
</div>
    </div>
</body>
</html>