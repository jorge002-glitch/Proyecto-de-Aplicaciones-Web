<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';

if (!isset($_GET["id"])) header("Location: index.php");

$id = intval($_GET["id"]);

// Verificar si el proveedor está relacionado con MANTENIMIENTO
$check = $conn->prepare("SELECT * FROM MANTENIMIENTO WHERE CVE_PROVEEDORES=?");
$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    die("<script>alert('No se puede eliminar: el proveedor está relacionado con un mantenimiento.'); window.location='index.php';</script>");
}

// Verificar si está relacionado con PIEZAS
$check2 = $conn->prepare("SELECT * FROM PIEZAS WHERE CVE_PROVEEDOR=?");
$check2->bind_param("i", $id);
$check2->execute();

if ($check2->get_result()->num_rows > 0) {
    die("<script>alert('No se puede eliminar: este proveedor tiene piezas registradas.'); window.location='index.php';</script>");
}

$conn->query("DELETE FROM PROVEEDORES WHERE CVE_PROVEEDORES=$id");

header("Location: index.php");
exit;
