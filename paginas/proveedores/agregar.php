<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre  = trim($_POST["NOMBRE"]);
    $ap      = trim($_POST["APELLIDO_PATERNO"]);
    $am      = trim($_POST["APELLIDO_MATERNO"]);
    $tel     = trim($_POST["TELEFONO"]);
    $dir     = trim($_POST["DIRECCION"]);

    if ($nombre === "" || $ap === "" || $am === "" || $tel === "" || $dir === "") {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Validar duplicado (nombre + apellido paterno + número)
    $check = $conn->prepare("SELECT * FROM PROVEEDORES WHERE NOMBRE=? AND APELLIDO_PATERNO=? AND TELEFONO=?");
    $check->bind_param("sss", $nombre, $ap, $tel);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $errores[] = "Este proveedor ya está registrado.";
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("INSERT INTO PROVEEDORES 
        (NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, TELEFONO, DIRECCION)
        VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param("sssss", $nombre, $ap, $am, $tel, $dir);

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
    <h2>Agregar Proveedor</h2>

    <?php if($errores): ?>
        <div class="alert-error">
            <?php foreach($errores as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Nombre</label>
        <input class="input" name="NOMBRE">

        <label>Tipo</label>
        <input class="input" name="APELLIDO_PATERNO">

        <label>Teléfono</label>
        <input class="input" name="TELEFONO">

        <label>Dirección</label>
        <input class="input" name="DIRECCION">

        <button class="button">Guardar</button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
