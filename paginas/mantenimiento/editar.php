<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

if (!isset($_GET['id'])) header('Location: index.php');
$id = intval($_GET['id']);
$errores = [];

// Obtener mantenimiento
$stmt = $conn->prepare("SELECT * FROM MANTENIMIENTO WHERE CVE_MANTENIMIENTO = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mant = $stmt->get_result()->fetch_assoc();
if (!$mant) header('Location: index.php');

// Dropdowns
$taxis = $conn->query("SELECT CVE_TAXI, PLACA FROM TAXIS ORDER BY PLACA");
$proveedores = $conn->query("SELECT CVE_PROVEEDORES, CONCAT(NOMBRE,' ',APELLIDO_PATERNO) AS NOMBRE FROM PROVEEDORES ORDER BY NOMBRE");
$piezas = $conn->query("SELECT CVE_PIEZA, NOMBRE FROM PIEZAS ORDER BY NOMBRE");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cve_taxi = intval($_POST['CVE_TAXI'] ?? 0);
    $cve_proveedor = intval($_POST['CVE_PROVEEDORES'] ?? 0);
    $fecha = trim($_POST['FECHA'] ?? '');
    $tipo = trim($_POST['TIPO'] ?? '');
    $descripcion = trim($_POST['DESCRIPCION'] ?? '');
    $costo = floatval($_POST['COSTO'] ?? 0);
    $km = intval($_POST['KILOMETRAJE'] ?? 0);
    $cve_pieza = intval($_POST['CVE_PIEZA'] ?? 0);

    if ($cve_taxi <= 0 || $cve_proveedor <= 0 || $fecha === '' || $tipo === '') {
        $errores[] = "Completa los campos obligatorios: Taxi, Proveedor, Fecha y Tipo.";
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("
            UPDATE MANTENIMIENTO SET 
                CVE_TAXI = ?, 
                CVE_PROVEEDORES = ?, 
                FECHA = ?, 
                TIPO = ?, 
                DESCRIPCION = ?, 
                COSTO = ?, 
                KILOMETRAJE = ?, 
                CVE_PIEZA = ?
            WHERE CVE_MANTENIMIENTO = ?
        ");
        $stmt->bind_param("iisssdiii", $cve_taxi, $cve_proveedor, $fecha, $tipo, $descripcion, $costo, $km, $cve_pieza, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Mantenimiento actualizado');window.location='index.php';</script>";
            exit;
        } else {
            $errores[] = "Error al actualizar: " . $stmt->error;
        }
    }
}
?>

<div class="card">
  <h2>Editar Mantenimiento</h2>

  <?php if (!empty($errores)): ?>
    <div class="alert error">
      <ul style="margin:0;padding-left:18px;">
        <?php foreach($errores as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <table class="table" style="width:100%;">
      <tr>
        <td><label>Taxi *</label></td>
        <td>
          <select class="input" name="CVE_TAXI" required>
            <option value="">Seleccione…</option>
            <?php while($tx = $taxis->fetch_assoc()): ?>
              <option value="<?= $tx['CVE_TAXI'] ?>" <?= $tx['CVE_TAXI'] == $mant['CVE_TAXI'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($tx['PLACA']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td><label>Proveedor *</label></td>
        <td>
          <select class="input" name="CVE_PROVEEDORES" required>
            <option value="">Seleccione…</option>
            <?php while($p = $proveedores->fetch_assoc()): ?>
              <option value="<?= $p['CVE_PROVEEDORES'] ?>" <?= $p['CVE_PROVEEDORES'] == $mant['CVE_PROVEEDORES'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['NOMBRE']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td><label>Fecha *</label></td>
        <td><input class="input" type="datetime-local" name="FECHA" value="<?= date('Y-m-d\TH:i', strtotime($mant['FECHA'])) ?>" required></td>
      </tr>
      <tr>
        <td><label>Tipo *</label></td>
        <td><input class="input" name="TIPO" value="<?= htmlspecialchars($mant['TIPO']) ?>" required></td>
      </tr>
      <tr>
        <td><label>Descripción</label></td>
        <td><textarea class="input" name="DESCRIPCION"><?= htmlspecialchars($mant['DESCRIPCION']) ?></textarea></td>
      </tr>
      <tr>
        <td><label>Costo</label></td>
        <td><input class="input" type="number" step="0.01" name="COSTO" value="<?= $mant['COSTO'] ?>"></td>
      </tr>
      <tr>
        <td><label>Kilometraje</label></td>
        <td><input class="input" type="number" name="KILOMETRAJE" value="<?= $mant['KILOMETRAJE'] ?>"></td>
      </tr>
      <tr>
        <td><label>Pieza usada</label></td>
        <td>
          <select class="input" name="CVE_PIEZA">
            <option value="">Seleccione…</option>
            <?php while($pz = $piezas->fetch_assoc()): ?>
              <option value="<?= $pz['CVE_PIEZA'] ?>" <?= $pz['CVE_PIEZA'] == $mant['CVE_PIEZA'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($pz['NOMBRE']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:right; padding-top:20px;">
          <button class="button">Guardar Cambios</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>