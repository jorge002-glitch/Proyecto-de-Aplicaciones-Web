<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

$query = $conn->query("
    SELECT t.*, 
        CONCAT(c.NOMBRE, ' ', c.APELLIDO_PATERNO) AS CONDUCTOR
    FROM TAXIS t
    LEFT JOIN CONDUCTORES c ON t.CVE_CONDUCTOR = c.CVE_CONDUCTOR
    ORDER BY t.CVE_TAXI ASC
");
?>
<div class="card">
    <h2>Taxis</h2>

    <a href="agregar.php" class="button" style="margin-bottom:15px;">Agregar Taxi</a>

    <table class="table">
        <thead>
            <tr>
                <th>CVE</th>
                <th>Placa</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Conductor</th>
                <th style="width:130px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($t = $query->fetch_assoc()): ?>
            <tr>
                <td><?= $t['CVE_TAXI'] ?></td>
                <td><?= $t['PLACA'] ?></td>
                <td><?= $t['MARCA'] ?></td>
                <td><?= $t['MODELO'] ?></td>
                <td><?= $t['ANO'] ?></td>
                <td><?= $t['CONDUCTOR'] ?: 'Sin asignar' ?></td>

                <td>
                    <a class="btn-edit" href="editar.php?id=<?= $t['CVE_TAXI'] ?>">Editar</a>
                    <a class="btn-del" href="eliminar.php?id=<?= $t['CVE_TAXI'] ?>"
                       onclick="return confirm('¿Eliminar este taxi?');">Eliminar</a>
                </td>
            </tr>
            <?php endwhile ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
