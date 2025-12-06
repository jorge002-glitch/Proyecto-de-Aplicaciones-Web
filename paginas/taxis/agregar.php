<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: ../../index.php');

include '../../conexion.php';
include '../../includes/menu.php';

$errores = [];

// Obtener conductores para la lista
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

    // Validar placas repetidas
    $check = $conn->prepare("SELECT * FROM TAXIS WHERE PLACA=? LIMIT 1");
    $check->bind_param("s", $placa);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $errores[] = "La placa ya está registrada.";
    }

    if (empty($errores)) {

        $stmt = $conn->prepare("
            INSERT INTO TAXIS (PLACA, MARCA, MODELO, ANO, CVE_CONDUCTOR)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("sssii", $placa, $marca, $modelo, $ano, $conductor);

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
    <h2>Agregar Taxi</h2>

    <?php if($errores): ?>
        <div class="alert-error">
            <?php foreach($errores as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <label>Placa</label>
        <input class="input" name="PLACA">

        <label>Marca</label>
        <input class="input" name="MARCA">

        <label>Modelo</label>
        <input class="input" name="MODELO">

        <label>Año</label>
        <input type="number" class="input" name="ANO" min="1990" max="2099">

        <label>Conductor</label>
        <select class="input" name="CVE_CONDUCTOR">
            <option value="">— Sin asignar —</option>
            <?php while($c = $conductores->fetch_assoc()): ?>
                <option value="<?= $c['CVE_CONDUCTOR'] ?>">
                    <?= $c['NOMBRE'] . " " . $c['APELLIDO_PATERNO'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button class="button">Guardar</button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
