<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

$query = $conn->query("
    SELECT p.*, 
           CONCAT(pr.NOMBRE, ' ', pr.APELLIDO_PATERNO) AS PROVEEDOR
    FROM PIEZAS p
    LEFT JOIN PROVEEDORES pr ON pr.CVE_PROVEEDORES = p.CVE_PROVEEDORES
    ORDER BY p.CVE_PIEZA ASC
");
?>

<div class="card">
    <h2>Piezas</h2>

    <a class="button" href="agregar.php" style="margin-bottom:14px;">Agregar Pieza</a>

    <table class="table">
        <thead>
            <tr>
                <th>CVE</th>
                <th>Pieza</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Proveedor</th>
                <th style="width:130px;">Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php while($r = $query->fetch_assoc()): ?>
            <tr>
                <td><?= $r['CVE_PIEZA'] ?></td>
                <td><?= htmlspecialchars($r['NOMBRE']) ?></td>
                <td><?= htmlspecialchars($r['DESCRIPCION']) ?></td>
                <td>$<?= number_format($r['PRECIO'], 2) ?></td>
                <td><?= $r['CANTIDAD'] ?></td>
                <td><?= htmlspecialchars($r['PROVEEDOR']) ?></td>
                <td>
                    <a class="btn-edit" href="editar.php?id=<?= $r['CVE_PIEZA'] ?>">Editar</a>
                    <a class="btn-del" href="eliminar.php?id=<?= $r['CVE_PIEZA'] ?>"
                       onclick="return confirm('¿Eliminar esta pieza?');">Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>