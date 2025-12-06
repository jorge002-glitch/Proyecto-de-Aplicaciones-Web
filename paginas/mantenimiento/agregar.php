<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

$errores = [];

// Dropdowns
$taxis = $conn->query("SELECT CVE_TAXI, PLACA FROM TAXIS ORDER BY PLACA");
$proveedores = $conn->query("SELECT CVE_PROVEEDORES, CONCAT(NOMBRE,' ',APELLIDO_PATERNO) AS NOMBRE FROM PROVEEDORES ORDER BY NOMBRE");
$piezas_all = $conn->query("SELECT CVE_PIEZA, NOMBRE, CANTIDAD FROM PIEZAS ORDER BY NOMBRE");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cve_taxi = intval($_POST['CVE_TAXI'] ?? 0);
    $cve_proveedor = intval($_POST['CVE_PROVEEDORES'] ?? 0);
    $fecha = trim($_POST['FECHA'] ?? '');
    $tipo = trim($_POST['TIPO'] ?? '');
    $tipo_repar = trim($_POST['TIPO_REPARACION'] ?? '');
    $descripcion = trim($_POST['DESCRIPCION'] ?? '');
    $costo = floatval($_POST['COSTO'] ?? 0);
    $km = intval($_POST['KILOMETRAJE'] ?? 0);

    // validaciones
    if ($cve_taxi <= 0 || $cve_proveedor <= 0 || $fecha === '' || $tipo === '') {
        $errores[] = "Completa los campos obligatorios: Taxi, Proveedor, Fecha y Tipo.";
    }

    // piezas opcionales
    $piezas = $_POST['pieza'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];

    if (!is_array($piezas) || !is_array($cantidades)) {
        $piezas = [];
        $cantidades = [];
    }

    // verificar stock suficiente antes de insertar
    foreach ($piezas as $i => $cve_pieza) {
        $cve_p = intval($cve_pieza);
        if ($cve_p <= 0) continue;
        $cant = intval($cantidades[$i] ?? 0);
        if ($cant <= 0) { $errores[] = "Cantidad inválida para una pieza seleccionada."; break; }

        $chk = $conn->prepare("SELECT CANTIDAD FROM PIEZAS WHERE CVE_PIEZA = ? LIMIT 1");
        $chk->bind_param("i", $cve_p);
        $chk->execute();
        $row = $chk->get_result()->fetch_assoc();
        if (!$row) { $errores[] = "La pieza seleccionada no existe."; break; }
        if ($row['CANTIDAD'] < $cant) {
            $errores[] = "Stock insuficiente para la pieza ID $cve_p (disponible: {$row['CANTIDAD']}).";
            break;
        }
    }

    if (empty($errores)) {
        $fecha_time = $fecha . ' 00:00:00';
        $ins = $conn->prepare("INSERT INTO MANTENIMIENTO (CVE_TAXI, CVE_PROVEEDORES, FECHA, TIPO, TIPO_REPARACION, DESCRIPCION, COSTO, KILOMETRAJE) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->bind_param("iissssdi", $cve_taxi, $cve_proveedor, $fecha_time, $tipo, $tipo_repar, $descripcion, $costo, $km);
        if ($ins->execute()) {
            $idMant = $ins->insert_id;

            // insertar piezas y actualizar stock
            foreach ($piezas as $i => $cve_pieza) {
                $cve_p = intval($cve_pieza);
                if ($cve_p <= 0) continue;
                $cant = intval($cantidades[$i] ?? 0);
                if ($cant <= 0) continue;

                $r = $conn->prepare("SELECT PRECIO FROM PIEZAS WHERE CVE_PIEZA = ? LIMIT 1");
                $r->bind_param("i", $cve_p);
                $r->execute();
                $row = $r->get_result()->fetch_assoc();
                $precio_unit = $row ? floatval($row['PRECIO']) : 0.0;

                $ins2 = $conn->prepare("INSERT INTO MANTENIMIENTO_PIEZAS (CVE_MANTENIMIENTO, CVE_PIEZA, CANTIDAD, PRECIO_UNITARIO) VALUES (?, ?, ?, ?)");
                $ins2->bind_param("iiid", $idMant, $cve_p, $cant, $precio_unit);
                $ins2->execute();

                $upd = $conn->prepare("UPDATE PIEZAS SET CANTIDAD = GREATEST(CANTIDAD - ?, 0) WHERE CVE_PIEZA = ?");
                $upd->bind_param("ii", $cant, $cve_p);
                $upd->execute();
            }

            echo "<script>alert('Mantenimiento registrado');window.location='index.php';</script>";
            exit;
        } else {
            $errores[] = "Error al guardar mantenimiento: " . $ins->error;
        }
    }
}
?>

<div class="card">
  <h2>Registrar Mantenimiento</h2>

  <?php if (!empty($errores)): ?>
    <div class="alert error">
      <ul style="margin:0;padding-left:18px;">
        <?php foreach($errores as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <label>Taxi *</label>
    <select class="input" name="CVE_TAXI" required>
      <option value="">Seleccione…</option>
      <?php
        $taxis = $conn->query("SELECT CVE_TAXI, PLACA FROM TAXIS ORDER BY PLACA");
        while($tx = $taxis->fetch_assoc()):
      ?>
        <option value="<?= $tx['CVE_TAXI'] ?>"><?= htmlspecialchars($tx['PLACA']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Proveedor *</label>
    <select class="input" name="CVE_PROVEEDORES" required>
      <option value="">Seleccione…</option>
      <?php while($p = $proveedores->fetch_assoc()): ?>
        <option value="<?= $p['CVE_PROVEEDORES'] ?>"><?= htmlspecialchars($p['NOMBRE']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Fecha *</label>
    <input class="input" type="date" name="FECHA" required>

    <label>Tipo *</label>
    <input class="input" name="TIPO" required>

    <label>Tipo de reparación</label>
    <input class="input" name="TIPO_REPARACION">

    <label>Descripción</label>
    <textarea class="input" name="DESCRIPCION"></textarea>

    <label>Costo</label>
    <input class="input" type="number" step="0.01" name="COSTO">

    <label>Kilometraje</label>
    <input class="input" type="number" name="KILOMETRAJE">

    <hr>

    <h4>Piezas usadas (opcional)</h4>
    <div id="piezas-area">
      <div class="pieza-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
        <select class="input" name="pieza[]">
          <option value="">-- Seleccione pieza --</option>
          <?php 
            $p2 = $conn->query("SELECT CVE_PIEZA, NOMBRE, CANTIDAD FROM PIEZAS ORDER BY NOMBRE");
            while($pz = $p2->fetch_assoc()){
              echo '<option value="'. $pz['CVE_PIEZA'] .'">'. htmlspecialchars($pz['NOMBRE'] . ' (stock: '.$pz['CANTIDAD'].')') .'</option>';
            }
          ?>
        </select>
        <input class="input" type="number" name="cantidad[]" value="1" min="1" style="width:110px;">
        <button type="button" class="button red" onclick="this.parentNode.remove()">Eliminar</button>
      </div>
    </div>

    <button type="button" class="button" onclick="agregarPieza()">+ Agregar otra pieza</button>

    <br><br>
    <button class="button" type="submit">Guardar mantenimiento</button>
  </form>
</div>

<script>
function agregarPieza(){
  const cont = document.getElementById('piezas-area');
  const div = document.createElement('div');
  div.className = 'pieza-row';
  div.style = 'display:flex;gap:8