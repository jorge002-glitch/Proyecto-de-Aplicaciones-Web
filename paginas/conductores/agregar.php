<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Campos
    $nombre = trim($_POST["NOMBRE"]);
    $ap = trim($_POST["APELLIDO_PATERNO"]);
    $am = trim($_POST["APELLIDO_MATERNO"]);
    $lic = trim($_POST["LICENCIA"]);
    $tel = trim($_POST["TELEFONO"]);
    $dir = trim($_POST["DIRECCION"]);

    // Validación
    if ($nombre === "" || $ap === "" || $am === "" || $lic === "" || $tel === "" || $dir === "") {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Evitar duplicados
    $check = $conn->prepare("SELECT * FROM CONDUCTORES WHERE LICENCIA = ? OR TELEFONO = ?");
    $check->bind_param("ss", $lic, $tel);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $errores[] = "Ya existe un conductor con la misma licencia o teléfono.";
    }

    // Insertar si no hay errores
    if (empty($errores)) {
        $stmt = $conn->prepare("INSERT INTO CONDUCTORES 
        (NOMBRE, APELLIDO_PATERNO, APELLIDO_MATERNO, LICENCIA, TELEFONO, DIRECCION)
        VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nombre, $ap, $am, $lic, $tel, $dir);

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
    <h2>Agregar Conductor</h2>

    <?php if($errores): ?>
        <div class="alert-error">
            <?php foreach($errores as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Nombre</label>
        <input class="input" name="NOMBRE">

        <label>Apellido Paterno</label>
        <input class="input" name="APELLIDO_PATERNO">

        <label>Apellido Materno</label>
        <input class="input" name="APELLIDO_MATERNO">

        <label>Licencia</label>
        <input class="input" name="LICENCIA">

        <label>Teléfono</label>
        <input class="input" name="TELEFONO">

        <label>Dirección</label>
        <input class="input" name="DIRECCION">

        <button class="button">Guardar</button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
