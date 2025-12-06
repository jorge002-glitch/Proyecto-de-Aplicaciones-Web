<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';

if (!isset($_GET["id"])) header("Location: index.php");

$id = intval($_GET["id"]);

// Validar relaciones con TARIFAS
$check1 = $conn->prepare("SELECT * FROM TARIFAS WHERE CVE_TAXI=?");
$check1->bind_param("i", $id);
$check1->execute();
if ($check1->get_result()->num_rows > 0) {
    die("<script>alert('No se puede eliminar: el taxi está relacionado con tarifas.'); window.location='index.php';</script>");
}

// Validar con MANTENIMIENTO
$check2 = $conn->prepare("SELECT * FROM MANTENIMIENTO WHERE CVE_TAXI=?");
$check2->bind_param("i", $id);
$check2->execute();
if ($check2->get_result()->num_rows > 0) {
    die("<script>alert('No se puede eliminar: el taxi está registrado en mantenimientos.'); window.location='index.php';</script>");
}

$conn->query("DELETE FROM TAXIS WHERE CVE_TAXI=$id");

header("Location: index.php");
exit;
