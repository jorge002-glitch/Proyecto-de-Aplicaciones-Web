<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

if (!isset($_GET["id"])) header("Location: index.php");
$id = intval($_GET["id"]);

$errores = [];

// Obtener taxi
$stmt = $conn->prepare("SELECT * FROM TAXIS WHERE CVE_TAXI=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$taxi = $stmt->get_result()->fetch_assoc();

if (!$taxi) header("Location: index.php");

// Obtener lista conductores
$conductores = $conn->query("SELECT * FROM CONDUCTORES ORDER BY NOMBRE ASC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $placa = trim($_POST["PLACA"]);
    $marca = trim($_POST["MARCA"]);
    $modelo = trim($_POST["MODELO"]);
    $ano    = intval($_POST["ANO"]);
    $conductor = intval($_POST["CVE_CONDUCTOR"]);

    if ($placa === "" || $marca === "" || $modelo === "" || !$ano) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Validar placa repetida
    $check = $conn->prepare("SELECT * FROM TAXIS WHERE PLACA=? AND CVE_TAXI<>?");
    $check->bind_param("si", $placa, $id);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $errores[] = "La placa ya está registrada en otro taxi.";
    }

    if (empty($errores)) {

        $update = $conn->prepare("
            UPDATE TAXIS SET
                PLACA=?, MARCA=?, MODELO=?, ANO=?, CVE_CONDUCTOR=?
            WHERE CVE_TAXI=?
        ");

        $update->bind_param("sssiii", $placa, $marca, $modelo, $ano, $conductor, $id);

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
    <h2>Editar Taxi</h2>

    <?php if($errores): ?>
        <div class="alert-error">
            <?php foreach($errores as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <label>Placa</label>
        <input class="input" name="PLACA" value="<?= $taxi['PLACA'] ?>">

        <label>Marca</label>
        <input class="input" name="MARCA" value="<?= $taxi['MARCA'] ?>">

        <label>Modelo</label>
        <input class="input" name="MODELO" value="<?= $taxi['MODELO'] ?>">

        <label>Año</label>
        <input class="input" type="number" name="ANO" value="<?= $taxi['ANO'] ?>">

        <label>Conductor</label>
        <select class="input" name="CVE_CONDUCTOR">
            <option value="">— Sin asignar —</option>
            <?php while($c = $conductores->fetch_assoc()): ?>
                <option value="<?= $c['CVE_CONDUCTOR'] ?>" 
                    <?= $c['CVE_CONDUCTOR'] == $taxi['CVE_CONDUCTOR'] ? "selected" : "" ?>>
                    <?= $c['NOMBRE'] . " " . $c['APELLIDO_PATERNO'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button class="button">Actualizar</button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
