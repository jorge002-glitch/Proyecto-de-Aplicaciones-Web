<?php
session_start();
include 'conexion.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitizar entradas
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    // VALIDACIONES ------------------

    if (strlen($nombre) < 3) {
        $errores[] = "El nombre debe tener mínimo 3 caracteres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    if (strlen($pass) < 8 || 
        !preg_match('/[A-Z]/', $pass) || 
        !preg_match('/[0-9]/', $pass)) {

        $errores[] = "La contraseña debe tener mínimo 8 caracteres, una mayúscula y un número.";
    }

    // Verificar si el correo ya existe
    $check = $conn->prepare("SELECT EMAIL FROM usuarios WHERE EMAIL = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $errores[] = "El correo ya está registrado.";
    }

    // Registrar si no hay errores
    if (empty($errores)) {

        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (NOMBRE, EMAIL, PASSWORD_HASH, CREADO_EN) 
                VALUES (?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $nombre, $email, $hash);

        if ($stmt->execute()) {
            $_SESSION['usuario'] = $email;
            $_SESSION['usuario_id'] = $stmt->insert_id;
            header("Location: dashboard.php");
            exit;
        } else {
            $errores[] = "Error inesperado: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrarse</title>
    <link rel="stylesheet" href="css/estilos.css">

    <style>
        body {
            background: #f2f6fc;
            font-family: Arial, sans-serif;
        }

        .register-container {
            width: 380px;
            margin: 70px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #1a237e;
        }

        .register-container label {
            font-weight: bold;
            margin-top: 12px;
            display: block;
        }

        .register-container input {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: 0.3s;
        }

        .register-container input:focus {
            border-color: #1e88e5;
            outline: none;
            box-shadow: 0 0 6px rgba(30,136,229,0.4);
        }

        .button-primary {
            width: 100%;
            background: #1e88e5;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            margin-top: 20px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .button-primary:hover {
            background: #1565c0;
        }

        .errors-box {
            background: #ffebee;
            padding: 12px;
            border-left: 4px solid #c62828;
            color: #b71c1c;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        .register-container .link {
            text-align: center;
            display: block;
            margin-top: 15px;
            text-decoration: none;
            color: #1e88e5;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="register-container">

    <h2>Crear cuenta</h2>

    <?php if (!empty($errores)): ?>
        <div class="errors-box">
            <ul>
                <?php foreach($errores as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">

        <label>Nombre completo</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>">

        <label>Correo electrónico</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

        <label>Contraseña</label>
        <input type="password" name="password">

        <button class="button-primary" type="submit">Registrarse</button>
    </form>

    <a class="link" href="index.php">¿Ya tienes cuenta? Iniciar sesión</a>

</div>

</body>
</html>
