<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

$errores = [];

// Lista de proveedores con nombre formateado
$proveedores = $conn->query("
    SELECT CVE_PROVEEDORES, 
           CONCAT(UCASE(LEFT(NOMBRE,1)), LCASE(SUBSTRING(NOMBRE,2)), ' ', APELLIDO_PATERNO) AS NOMBRE_FORMAT
    FROM PROVEEDORES
    ORDER BY NOMBRE ASC
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["NOMBRE"]);
    $descripcion = trim($_POST["DESCRIPCION"]);
    $precio = floatval($_POST["PRECIO"]);
    $cantidad = intval($_POST["CANTIDAD"]);
    $proveedor = intval($_POST["CVE_PROVEEDORES"]);

    if ($nombre === "" || $descripcion === "" || !$precio || $cantidad < 0 || !$proveedor) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Validar duplicado
    $check = $conn->prepare("
        SELECT * FROM PIEZAS WHERE NOMBRE=? AND CVE_PROVEEDORES=?
    ");
    $check->bind_param("si", $nombre, $proveedor);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $errores[] = "Esta pieza ya está registrada para este proveedor.";
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("
            INSERT INTO PIEZAS (NOMBRE, DESCRIPCION, PRECIO, CANTIDAD, CVE_PROVEEDORES)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $cantidad, $proveedor);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errores[] = "Error al guardar: " . $stmt->error;
        }
    }
}
?>

<div class="card">
    <h2>Agregar Pieza</h2>

    <?php if($errores): ?>
    <div class="alert-error">
        <?php foreach($errores as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
    </div>
    <?php endif; ?>

    <form method="post">

        <label>Nombre de la Pieza</label>
        <input class="input" name="NOMBRE" required>

        <label>Descripción</label>
        <input class="input" name="DESCRIPCION" required>

        <label>Precio</label>
        <input class="input" type="number" step="0.01" name="PRECIO" required>

        <label>Cantidad</label>
        <input class="input" type="number" min="0" name="CANTIDAD" required>

        <label>Proveedor</label>
        <select class="input" name="CVE_PROVEEDORES" 
             value="">Seleccione…</option>
            <?php while($p = $proveedores->fetch_assoc()): ?>
                <option value="<?= $p['CVE_PROVEEDORES'] ?>">
                    <?= htmlspecialchars($p['NOMBRE_FORMAT']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button class="button">Guardar</button>

    </form>
</div>

<?php include '../../includes/footer.php'; ?>