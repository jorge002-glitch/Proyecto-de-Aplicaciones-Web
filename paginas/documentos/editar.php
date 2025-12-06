<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");
include "../../conexion.php";

if (!isset($_GET['id']) || !isset($_GET['tipo'])) header("Location: index.php");

$id = intval($_GET['id']);
$tipo = $_GET['tipo'];

include "../../includes/menu.php";

if ($tipo == "conductor") {
    $sql = "SELECT * FROM DOCUMENTOS_CONDUCTORES WHERE CVE_DOCUMENTO = ?";
} else {
    $sql = "SELECT * FROM DOCUMENTOS_TAXI WHERE CVE_DOCUMENTO = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipoDoc = $_POST['TIPO'];
    $vence = $_POST['FECHA_VENCIMIENTO'];
    $archivo = $_POST['ARCHIVO'];

    if (empty($tipoDoc) || empty($vence) || empty($archivo)) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    if (empty($errores)) {

        if ($tipo == "conductor") {
            $stmt = $conn->prepare("
                UPDATE DOCUMENTOS_CONDUCTORES 
                SET TIPO=?, FECHA_VENCIMIENTO=?, ARCHIVO=? 
                WHERE CVE_DOCUMENTO=?
            ");
        } else {
            $stmt = $conn->prepare("
                UPDATE DOCUMENTOS_TAXI 
                SET TIPO=?, FECHA_VENCIMIENTO=?, ARCHIVO=? 
                WHERE CVE_DOCUMENTO=?
            ");
        }

        $stmt->bind_param("sssi", $tipoDoc, $vence, $archivo, $id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errores[] = "Error al actualizar";
        }
    }
}

?>

<div class="card">
  <h3>Editar Documento</h3>

  <?php if (!empty($errores)): ?>
    <div class="alert-error">
      <?php foreach($errores as $e) echo "<p>• " . htmlspecialchars($e) . "</p>"; ?>
    </div>
  <?php endif; ?>

  <form method="POST">
    <table class="table" style="width:100%;">
      <tr>
        <td><label>Tipo:</label></td>
        <td><input class="input" type="text" name="TIPO" value="<?= htmlspecialchars($data['TIPO'] ?? '') ?>" required></td>
      </tr>
      <tr>
        <td><label>Fecha de vencimiento:</label></td>
        <td><input class="input" type="date" name="FECHA_VENCIMIENTO" value="<?= htmlspecialchars($data['FECHA_VENCIMIENTO'] ?? '') ?>" required></td>
      </tr>
      <tr>
        <td><label>Archivo (nombre o URL):</label></td>
        <td><input class="input" type="text" name="ARCHIVO" value="<?= htmlspecialchars($data['ARCHIVO'] ?? '') ?>" required></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:right; padding-top:20px;">
          <button class="button">Guardar Cambios</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<?php include "../../includes/footer.php"; ?>
