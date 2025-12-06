<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';

if (!isset($_GET['id'])) header("Location: index.php");

$id = intval($_GET['id']);

// Evitar borrar si está relacionado con TAXIS o TARIFAS
$check = $conn->prepare("SELECT * FROM TAXIS WHERE CVE_CONDUCTOR=?");
$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    die("<script>alert('No se puede eliminar: el conductor está asignado a taxis.');window.location='index.php';</script>");
}

$conn->query("DELETE FROM CONDUCTORES WHERE CVE_CONDUCTOR = $id");

header("Location: index.php");
exit;
