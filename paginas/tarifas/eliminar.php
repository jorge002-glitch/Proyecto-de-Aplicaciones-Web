<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");

include '../../conexion.php';

if (!isset($_GET["id"])) header("Location: index.php");

$id = intval($_GET["id"]);

$conn->query("DELETE FROM TARIFAS WHERE CVE_TARIFAS=$id");

header("Location: index.php");
exit;
