<?php
// Ruta absoluta desde la raíz del proyecto
$root = "/gestion_taxi/";
?>

<link rel="stylesheet" href="<?php echo $root; ?>css/estilos.css">

<div class="sidebar">

    <!-- MENÚ PRINCIPAL -->
    <a href="<?php echo $root; ?>dashboard.php">Dashboard</a>
    <a href="<?php echo $root; ?>paginas/conductores/index.php">Conductores</a>
    <a href="<?php echo $root; ?>paginas/taxis/index.php">Taxis</a>
    <a href="<?php echo $root; ?>paginas/proveedores/index.php">Proveedores</a>
    <a href="<?php echo $root; ?>paginas/piezas/index.php">Piezas</a>
    <a href="<?php echo $root; ?>paginas/mantenimiento/index.php">Mantenimiento</a>
    <a href="<?php echo $root; ?>paginas/tarifas/index.php">Tarifas</a>
    <a href="<?php echo $root; ?>paginas/documentos/index.php">Documentos</a>

    <div style="flex:1;"></div>

    <a class="logout-btn" href="<?php echo $root; ?>logout.php">Cerrar sesión</a>
</div>

<div class="content">
