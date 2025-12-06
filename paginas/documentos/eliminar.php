<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");
include "../../conexion.php";

if (!isset($_GET['id']) || !isset($_GET['tipo'])) header("Location: index.php");

$id = intval($_GET['id']);
$tipo = $_GET['tipo'];

if ($tipo == "conductor") {
    $sql = "DELETE FROM DOCUMENTOS_CONDUCTORES WHERE CVE_DOCUMENTO = ?";
} else {
    $sql = "DELETE FROM DOCUMENTOS_TAXI WHERE CVE_DOCUMENTO = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php");
exit;
