<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");
include "../../conexion.php";
include "../../includes/menu.php";

// Documentos de conductores
$sqlC = "
SELECT d.CVE_DOCUMENTO, d.TIPO, d.FECHA_VENCIMIENTO, d.ACHIVO AS ARCHIVO,
       c.NOMBRE AS TITULAR
FROM DOCUMENTOS_CONDUCTORES d
LEFT JOIN CONDUCTORES c ON c.CVE_CONDUCTOR = d.CVE_CONDUCTOR
ORDER BY d.CVE_DOCUMENTO DESC
";

$docsConductores = $conn->query($sqlC);

// Documentos de taxis
$sqlT = "
SELECT d.CVE_DOCUMENTO, d.TIPO, d.FECHA_VENCIMIENTO, d.ARCHIVO,
       t.PLACA AS TITULAR
FROM DOCUMENTOS_TAXI d
LEFT JOIN TAXIS t ON t.CVE_TAXI = d.CVE_TAXI
ORDER BY d.CVE_DOCUMENTO DESC
";
$docsTaxis = $conn->query($sqlT);
?>

<div class="card">
    <h3>Documentos registrados</h3>

    <a class="btn-add" href="agregar.php">Agregar documento</a>

    <h4 style="margin-top:25px;">Documentos de conductores</h4>
    <table class="table">
        <thead>
            <tr>
                <th>CVE</th>
                <th>Titular</th>
                <th>Tipo</th>
                <th>Vence</th>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($d = $docsConductores->fetch_assoc()): ?>
            <tr>
                <td><?= $d['CVE_DOCUMENTO'] ?></td>
                <td><?= htmlspecialchars($d['TITULAR']) ?></td>
                <td><?= htmlspecialchars($d['TIPO']) ?></td>
                <td><?= $d['FECHA_VENCIMIENTO'] ?></td>
                <td><?= htmlspecialchars($d['ARCHIVO']) ?></td>
                <td>
                    <a class="btn-edit" href="editar.php?id=<?= $d['CVE_DOCUMENTO'] ?>&tipo=conductor">Editar</a>
                    <a class="btn-delete" onclick="return confirm('¿Eliminar documento?');"
                       href="eliminar.php?id=<?= $d['CVE_DOCUMENTO'] ?>&tipo=conductor">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h4 style="margin-top:35px;">Documentos de taxis</h4>
    <table class="table">
        <thead>
            <tr>
                <th>CVE</th>
                <th>Placa</th>
                <th>Tipo</th>
                <th>Vence</th>
                <th>Archivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($d = $docsTaxis->fetch_assoc()): ?>
            <tr>
                <td><?= $d['CVE_DOCUMENTO'] ?></td>
                <td><?= htmlspecialchars($d['TITULAR']) ?></td>
                <td><?= htmlspecialchars($d['TIPO']) ?></td>
                <td><?= $d['FECHA_VENCIMIENTO'] ?></td>
                <td><?= htmlspecialchars($d['ARCHIVO']) ?></td>
                <td>
                    <a class="btn-edit" href="editar.php?id=<?= $d['CVE_DOCUMENTO'] ?>&tipo=taxi">Editar</a>
                    <a class="btn-delete" onclick="return confirm('¿Eliminar documento?');"
                       href="eliminar.php?id=<?= $d['CVE_DOCUMENTO'] ?>&tipo=taxi">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

</div>

<?php include "../../includes/footer.php"; ?>
