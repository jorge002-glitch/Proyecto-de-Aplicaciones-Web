<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$errores = [];

// Validar campos vacíos
if (empty($email) || empty($password)) {
    $errores[] = "Todos los campos son obligatorios.";
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "Ingresa un correo electrónico válido.";
}

if (!empty($errores)) {
    echo "<script>alert('".implode("\\n", $errores)."'); window.location='index.php';</script>";
    exit;
}

// Buscar usuario
$stmt = $conn->prepare("SELECT CVE_USUARIO, NOMBRE, EMAIL, PASSWORD_HASH FROM usuarios WHERE EMAIL = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    echo "<script>alert('El usuario no existe'); window.location='index.php';</script>";
    exit;
}

// Validar contraseña
if (!password_verify($password, $user['PASSWORD_HASH'])) {
    echo "<script>alert('Contraseña incorrecta'); window.location='index.php';</script>";
    exit;
}

// Guardar sesión
$_SESSION['usuario'] = $user['NOMBRE']; 
$_SESSION['usuario_id'] = $user['CVE_USUARIO'];

header("Location: dashboard.php");
exit;
?>
