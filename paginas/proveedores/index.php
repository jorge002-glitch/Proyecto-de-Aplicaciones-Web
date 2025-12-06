<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

$query = $conn->query("SELECT * FROM PROVEEDORES ORDER BY CVE_PROVEEDORES ASC");
?>
<div class="card">
    <h2>Proveedores</h2>

    <a href="agregar.php" class="button" style="margin-bottom:15px;">Agregar proveedor</a>

    <table class="table">
        <thead>
            <tr>
                <th>CVE</th>
                <th>Nombre completo</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th style="width:130px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($r = $query->fetch_assoc()): ?>
            <tr>
                <td><?= $r['CVE_PROVEEDORES'] ?></td>
                <td><?= $r['NOMBRE']." ".$r['APELLIDO_PATERNO']." ".$r['APELLIDO_MATERNO'] ?></td>
                <td><?= $r['TELEFONO'] ?></td>
                <td><?= $r['DIRECCION'] ?></td>

                <td>
                    <a class="btn-edit" href="editar.php?id=<?= $r['CVE_PROVEEDORES'] ?>">Editar</a>
                    <a class="btn-del" href="eliminar.php?id=<?= $r['CVE_PROVEEDORES'] ?>"
                       onclick="return confirm('¿Eliminar este proveedor?');">Eliminar</a>
                </td>
            </tr>
            <?php endwhile ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
