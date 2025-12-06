<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");
include "../../conexion.php";
include "../../includes/menu.php";

$errores = [];

$tipo_doc = $_POST['tipo_doc'] ?? '';
$tipo = $_POST['TIPO'] ?? '';
$vence = $_POST['FECHA_VENCIMIENTO'] ?? '';
$archivo = $_POST['ARCHIVO'] ?? '';
$cve_conductor = $_POST['CVE_CONDUCTOR'] ?? '';
$cve_taxi = $_POST['CVE_TAXI'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tipo = trim($tipo);
    $archivo = trim($archivo);

    if ($tipo_doc === "conductor") {
        $id = intval($cve_conductor);
        if ($id <= 0) $errores[] = "Debe seleccionar un conductor.";
    }

    if ($tipo_doc === "taxi") {
        $id = intval($cve_taxi);
        if ($id <= 0) $errores[] = "Debe seleccionar un taxi.";
    }

    if (empty($tipo) || empty($vence) || empty($archivo)) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    if (empty($errores)) {
        if ($tipo_doc === "conductor") {
            $stmt = $conn->prepare("
                INSERT INTO DOCUMENTOS_CONDUCTORES (CVE_CONDUCTOR, TIPO, FECHA_VENCIMIENTO, ARCHIVO)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("isss", $id, $tipo, $vence, $archivo);
        }

        if ($tipo_doc === "taxi") {
            $stmt = $conn->prepare("
                INSERT INTO DOCUMENTOS_TAXI (CVE_TAXI, TIPO, FECHA_VENCIMIENTO, ARCHIVO)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("isss", $id, $tipo, $vence, $archivo);
        }

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errores[] = "Error: " . $stmt->error;
        }
    }
}

$conductores = $conn->query("SELECT CVE_CONDUCTOR, NOMBRE FROM CONDUCTORES");
$taxis = $conn->query("SELECT CVE_TAXI, PLACA FROM TAXIS");
?>

<div class="card">
  <h3>Agregar Documento</h3>

  <?php if (!empty($errores)): ?>
    <div class="alert-error">
      <?php foreach($errores as $e) echo "<p>• " . htmlspecialchars($e) . "</p>"; ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <table class="table" style="width:100%;">
      <tr>
        <td><label>Documento para:</label></td>
        <td>
          <select name="tipo_doc" onchange="this.form.submit()">
            <option value="">Seleccione...</option>
            <option value="conductor" <?= $tipo_doc === "conductor" ? "selected" : "" ?>>Conductor</option>
            <option value="taxi" <?= $tipo_doc === "taxi" ? "selected" : "" ?>>Taxi</option>
          </select>
        </td>
      </tr>

      <?php if ($tipo_doc === "conductor"): ?>
      <tr>
        <td><label>Conductor:</label></td>
        <td>
          <select name="CVE_CONDUCTOR">
            <option value="">Seleccione...</option>
            <?php while ($c = $conductores->fetch_assoc()): ?>
              <option value="<?= $c['CVE_CONDUCTOR'] ?>" <?= $cve_conductor == $c['CVE_CONDUCTOR'] ? "selected" : "" ?>>
                <?= htmlspecialchars($c['NOMBRE']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </td>
      </tr>
      <?php endif; ?>

      <?php if ($tipo_doc === "taxi"): ?>
      <tr>
        <td><label>Taxi (Placa):</label></td>
        <td>
          <select name="CVE_TAXI">
            <option value="">Seleccione...</option>
            <?php while ($t = $taxis->fetch_assoc()): ?>
              <option value="<?= $t['CVE_TAXI'] ?>" <?= $cve_taxi == $t['CVE_TAXI'] ? "selected" : "" ?>>
                <?= htmlspecialchars($t['PLACA']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </td>
      </tr>
      <?php endif; ?>

      <?php if (!empty($tipo_doc)): ?>
      <tr>
        <td><label>Tipo de documento:</label></td>
        <td><input class="input" name="TIPO" value="<?= htmlspecialchars($tipo) ?>" required></td>
      </tr>
      <tr>
        <td><label>Fecha de vencimiento:</label></td>
        <td><input class="input" type="date" name="FECHA_VENCIMIENTO" value="<?= htmlspecialchars($vence) ?>" required></td>
      </tr>
      <tr>
        <td><label>Archivo (nombre o URL):</label></td>
        <td><input class="input" name="ARCHIVO" value="<?= htmlspecialchars($archivo) ?>" required></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:right; padding-top:20px;">
          <button class="button">Guardar</button>
        </td>
      </tr>
      <?php endif; ?>
    </table>
  </form>
</div>

<?php include "../../includes/footer.php"; ?>