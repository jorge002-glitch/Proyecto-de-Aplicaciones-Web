<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

// Obtener todos los conductores
$query = $conn->query("SELECT * FROM CONDUCTORES ORDER BY CVE_CONDUCTOR ASC");
?>
<div class="card">
    <h2>Conductores</h2>

    <a href="agregar.php" class="button" style="margin-bottom:15px;">Agregar conductor</a>

    <table class="table">
        <thead>
            <tr>
                <th>CVE</th>
                <th>Nombre Completo</th>
                <th>Licencia</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th style="width:130px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $query->fetch_assoc()): ?>
            <tr>
                <td><?= $row['CVE_CONDUCTOR'] ?></td>
                <td><?= $row['NOMBRE']." ".$row['APELLIDO_PATERNO']." ".$row['APELLIDO_MATERNO'] ?></td>
                <td><?= $row['LICENCIA'] ?></td>
                <td><?= $row['TELEFONO'] ?></td>
                <td><?= $row['DIRECCION'] ?></td>
                <td>
                    <a href="editar.php?id=<?= $row['CVE_CONDUCTOR'] ?>" class="btn-edit">Editar</a>
                    <a href="eliminar.php?id=<?= $row['CVE_CONDUCTOR'] ?>" class="btn-del"
                       onclick="return confirm('¿Seguro de eliminar este conductor?')">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
