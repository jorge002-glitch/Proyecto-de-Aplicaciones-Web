<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");

include '../../conexion.php';
include '../../includes/menu.php';

$query = $conn->query("
    SELECT t.*, 
        CONCAT(c.NOMBRE, ' ', c.APELLIDO_PATERNO) AS CONDUCTOR,
        tx.PLACA AS TAXI,
        e.DESCRIPCION AS ESTADO
    FROM TARIFAS t
    LEFT JOIN CONDUCTORES c ON c.CVE_CONDUCTOR = t.CVE_CONDUCTOR
    LEFT JOIN TAXIS tx ON tx.CVE_TAXI = t.CVE_TAXI
    LEFT JOIN ESTADOS_CUOTA e ON e.CVE_ESTADO_CUOTA = t.CVE_ESTADO_CUOTA
    ORDER BY t.CVE_TARIFAS DESC
");
?>
<div class="card">
    <h2>Tarifas</h2>

    <a href="agregar.php" class="button" style="margin-bottom:15px;">Agregar Tarifa</a>

    <table class="table">
        <thead>
            <tr>
                <th>CVE</th>
                <th>Conductor</th>
                <th>Taxi</th>
                <th>Fecha</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Tarifa Total</th>
                <th>Cuota</th>
                <th>Estado</th>
                <th style="width:130px;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($r = $query->fetch_assoc()): ?>
            <tr>
                <td><?= $r['CVE_TARIFAS'] ?></td>
                <td><?= $r['CONDUCTOR'] ?></td>
                <td><?= $r['TAXI'] ?></td>
                <td><?= date("Y-m-d", strtotime($r['FECHA'])) ?></td>
                <td><?= $r['HORA_ENTRADA'] ?></td>
                <td><?= $r['HORA_SALIDA'] ?></td>
                <td>$<?= number_format($r['TARIFA_TOTAL'], 2) ?></td>
                <td>$<?= number_format($r['CUOTA'], 2) ?></td>
                <td><?= $r['ESTADO'] ?></td>

                <td>
                    <a class="btn-edit" href="editar.php?id=<?= $r['CVE_TARIFAS'] ?>">Editar</a>
                    <a class="btn-del" href="eliminar.php?id=<?= $r['CVE_TARIFAS'] ?>"
                       onclick="return confirm('¿Eliminar esta tarifa?');">Eliminar</a>
                </td>
            </tr>
            <?php endwhile ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
