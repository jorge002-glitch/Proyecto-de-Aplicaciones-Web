<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");

include '../../conexion.php';

if (!isset($_GET["id"])) header("Location: index.php");

$id = intval($_GET["id"]);

// Validar si está usado en mantenimientos
$check = $conn->prepare("SELECT * FROM MANTENIMIENTO WHERE CVE_PIEZA=?");
$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    die("<script>alert('No se puede eliminar: la pieza está asignada a un mantenimiento.'); window.location='index.php';</script>");
}

$conn->query("DELETE FROM PIEZAS WHERE CVE_PIEZA=$id");

header("Location: index.php");
exit;
