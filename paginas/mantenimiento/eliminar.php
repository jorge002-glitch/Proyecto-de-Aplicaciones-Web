<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';

if (!isset($_GET['id'])) header('Location: index.php');
$id = intval($_GET['id']);

// obtener piezas asociadas para restaurar stock
$qr = $conn->prepare("SELECT CVE_PIEZA, CANTIDAD FROM MANTENIMIENTO_PIEZAS WHERE CVE_MANTENIMIENTO = ?");
$qr->bind_param("i", $id);
$qr->execute();
$res = $qr->get_result();

while ($row = $res->fetch_assoc()) {
    $upd = $conn->prepare("UPDATE PIEZAS SET CANTIDAD = CANTIDAD + ? WHERE CVE_PIEZA = ?");
    $upd->bind_param("ii", $row['CANTIDAD'], $row['CVE_PIEZA']);
    $upd->execute();
}

// borrar registros de MANTENIMIENTO_PIEZAS
$del = $conn->prepare("DELETE FROM MANTENIMIENTO_PIEZAS WHERE CVE_MANTENIMIENTO = ?");
$del->bind_param("i", $id);
$del->execute();

// borrar mantenimiento
$del2 = $conn->prepare("DELETE FROM MANTENIMIENTO WHERE CVE_MANTENIMIENTO = ?");
$del2->bind_param("i", $id);
$del2->execute();

header('Location: index.php');
exit;
