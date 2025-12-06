<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

// Listado con taxi y proveedor
$q = $conn->query("
  SELECT m.*, tx.PLACA, CONCAT(p.NOMBRE,' ',p.APELLIDO_PATERNO) AS PROVEEDOR
  FROM MANTENIMIENTO m
  LEFT JOIN TAXIS tx ON tx.CVE_TAXI = m.CVE_TAXI
  LEFT JOIN PROVEEDORES p ON p.CVE_PROVEEDORES = m.CVE_PROVEEDORES
  ORDER BY m.CVE_MANTENIMIENTO DESC
");
?>
<div class="card">
  <h2>Mantenimientos</h2>

  <a href="agregar.php" class="button" style="margin-bottom:14px;">Registrar mantenimiento</a>

  <table class="table">
    <thead>
      <tr>
        <th>CVE</th>
        <th>Taxi (placa)</th>
        <th>Proveedor</th>
        <th>Fecha</th>
        <th>Tipo</th>
        <th>Tipo reparación</th>
        <th>Costo</th>
        <th>Kilometraje</th>
        <th style="width:150px;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while($r = $q->fetch_assoc()): ?>
      <tr>
        <td><?= $r['CVE_MANTENIMIENTO'] ?></td>
        <td><?= htmlspecialchars($r['PLACA'] ?? '—') ?></td>
        <td><?= htmlspecialchars($r['PROVEEDOR'] ?? '—') ?></td>
        <td><?= !empty($r['FECHA']) ? date('Y-m-d', strtotime($r['FECHA'])) : '—' ?></td>
        <td><?= htmlspecialchars($r['TIPO']) ?></td>
        <td><?= htmlspecialchars($r['TIPO_REPARACION'] ?? '') ?></td>
        <td>$<?= number_format($r['COSTO'] ?? 0, 2) ?></td>
        <td><?= htmlspecialchars($r['KILOMETRAJE'] ?? '') ?></td>
        <td>
          <a class="btn-edit" href="editar.php?id=<?= $r['CVE_MANTENIMIENTO'] ?>">Editar</a>
          <a class="btn-del" href="eliminar.php?id=<?= $r['CVE_MANTENIMIENTO'] ?>"
            onclick="return confirm('¿Eliminar este mantenimiento? Esta acción restaurará el stock de piezas usadas.')">Eliminar</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include '../../includes/footer.php'; ?>
