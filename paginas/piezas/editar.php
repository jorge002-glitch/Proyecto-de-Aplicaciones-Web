<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

if (!isset($_GET["id"])) header("Location: index.php");
$id = intval($_GET["id"]);

$errores = [];

// Obtener pieza
$stmt = $conn->prepare("SELECT * FROM PIEZAS WHERE CVE_PIEZA=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pieza = $stmt->get_result()->fetch_assoc();

if (!$pieza) header("Location: index.php");

// Proveedores
$proveedores = $conn->query("SELECT CVE_PROVEEDORES, NOMBRE, APELLIDO_PATERNO FROM PROVEEDORES ORDER BY NOMBRE ASC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["NOMBRE"]);
    $descripcion = trim($_POST["DESCRIPCION"]);
    $precio = floatval($_POST["PRECIO"]);
    $cantidad = intval($_POST["CANTIDAD"]);
    $proveedor = intval($_POST["CVE_PROVEEDORES"]);

    if ($nombre === "" || $descripcion === "" || !$precio || $cantidad < 0 || !$proveedor) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Verificar duplicado
    $check = $conn->prepare("
        SELECT * FROM PIEZAS 
        WHERE NOMBRE=? AND CVE_PROVEEDORES=? AND CVE_PIEZA<>?
    ");
    $check->bind_param("sii", $nombre, $proveedor, $id);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $errores[] = "Otra pieza con el mismo nombre ya existe para este proveedor.";
    }

    if (empty($errores)) {
        $update = $conn->prepare("
            UPDATE PIEZAS SET 
                NOMBRE=?, DESCRIPCION=?, PRECIO=?, CANTIDAD=?, CVE_PROVEEDORES=?
            WHERE CVE_PIEZA=?
        ");
        $update->bind_param("ssdiii", $nombre, $descripcion, $precio, $cantidad, $proveedor, $id);

        if ($update->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errores[] = "Error al actualizar: " . $update->error;
        }
    }
}
?>

<div class="card">
  <h2>Editar Pieza</h2>

  <?php if($errores): ?>
  <div class="alert-error">
    <?php foreach($errores as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
  </div>
  <?php endif; ?>

  <form method="post">
    <table class="table" style="width:100%;">
      <tr>
        <td><label>Nombre de la Pieza</label></td>
        <td><input class="input" name="NOMBRE" value="<?= htmlspecialchars($pieza['NOMBRE']) ?>" required></td>
      </tr>
      <tr>
        <td><label>Descripción</label></td>
        <td><input class="input" name="DESCRIPCION" value="<?= htmlspecialchars($pieza['DESCRIPCION']) ?>" required></td>
      </tr>
      <tr>
        <td><label>Precio</label></td>
        <td><input class="input" type="number" step="0.01" name="PRECIO" value="<?= $pieza['PRECIO'] ?>" required></td>
      </tr>
      <tr>
        <td><label>Cantidad</label></td>
        <td><input class="input" type="number" min="0" name="CANTIDAD" value="<?= $pieza['CANTIDAD'] ?>" required></td>
      </tr>
      <tr>
        <td><label>Proveedor</label></td>
        <td>
          <select class="input" name="CVE_PROVEEDORES" required style="max-width: 300px;">
            <option value="">Seleccione…</option>
            <?php while($p = $proveedores->fetch_assoc()): ?>
              <option value="<?= $p['CVE_PROVEEDORES'] ?>"
                <?= $p['CVE_PROVEEDORES'] == $pieza['CVE_PROVEEDORES'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['NOMBRE'] . ' ' . $p['APELLIDO_PATERNO']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align:right; padding-top:20px;">
          <button class="button">Actualizar</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<?php include '../../includes/footer.php'; ?>