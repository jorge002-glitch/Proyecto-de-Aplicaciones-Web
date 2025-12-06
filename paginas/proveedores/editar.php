<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

if (!isset($_GET["id"])) header("Location: index.php");

$id = intval($_GET["id"]);
$errores = [];

// Traer proveedor
$stmt = $conn->prepare("SELECT * FROM PROVEEDORES WHERE CVE_PROVEEDORES=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) header("Location: index.php");

// Actualizar
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["NOMBRE"]);
    $ap     = trim($_POST["APELLIDO_PATERNO"]);
    $am     = trim($_POST["APELLIDO_MATERNO"]);
    $tel    = trim($_POST["TELEFONO"]);
    $dir    = trim($_POST["DIRECCION"]);

    if ($nombre === "" || $ap === "" || $am === "" || $tel === "" || $dir === "") {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Evitar duplicados
    $check = $conn->prepare("SELECT * FROM PROVEEDORES 
        WHERE NOMBRE=? AND APELLIDO_PATERNO=? AND TELEFONO=? AND CVE_PROVEEDORES <> ?");
    $check->bind_param("sssi", $nombre, $ap, $tel, $id);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $errores[] = "Otro proveedor con los mismos datos ya existe.";
    }

    if (empty($errores)) {
        $update = $conn->prepare("UPDATE PROVEEDORES SET 
            NOMBRE=?, APELLIDO_PATERNO=?, APELLIDO_MATERNO=?, TELEFONO=?, DIRECCION=?
            WHERE CVE_PROVEEDORES=?");

        $update->bind_param("sssssi", $nombre, $ap, $am, $tel, $dir, $id);

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
    <h2>Editar Proveedor</h2>

    <?php if($errores): ?>
    <div class="alert-error">
        <?php foreach($errores as $e) echo "<p>$e</p>"; ?>
    </div>
    <?php endif; ?>

    <form method="post">

        <label>Nombre</label>
        <input class="input" name="NOMBRE" value="<?= $data['NOMBRE'] ?>">

        <label>Apellido Paterno</label>
        <input class="input" name="APELLIDO_PATERNO" value="<?= $data['APELLIDO_PATERNO'] ?>">

        <label>Apellido Materno</label>
        <input class="input" name="APELLIDO_MATERNO" value="<?= $data['APELLIDO_MATERNO'] ?>">

        <label>Teléfono</label>
        <input class="input" name="TELEFONO" value="<?= $data['TELEFONO'] ?>">

        <label>Dirección</label>
        <input class="input" name="DIRECCION" value="<?= $data['DIRECCION'] ?>">

        <button class="button">Actualizar</button>

    </form>
</div>

<?php include '../../includes/footer.php'; ?>
