<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");

include '../../conexion.php';
include '../../includes/menu.php';

if (!isset($_GET["id"])) header("Location: index.php");

$id = intval($_GET["id"]);
$errores = [];

$stmt = $conn->prepare("SELECT * FROM TARIFAS WHERE CVE_TARIFAS=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tarifa = $stmt->get_result()->fetch_assoc();

if (!$tarifa) header("Location: index.php");

// Dropdowns
$conductores = $conn->query("SELECT * FROM CONDUCTORES ORDER BY NOMBRE ASC");
$taxis = $conn->query("SELECT * FROM TAXIS ORDER BY PLACA ASC");
$estados = $conn->query("SELECT * FROM ESTADOS_CUOTA");
    
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $conductor = intval($_POST["CVE_CONDUCTOR"]);
    $taxi = intval($_POST["CVE_TAXI"]);
    $estado = intval($_POST["CVE_ESTADO_CUOTA"]);

    $fecha = $_POST["FECHA"];
    $entrada = $_POST["HORA_ENTRADA"];
    $salida  = $_POST["HORA_SALIDA"];
    $tarifa_total  = floatval($_POST["TARIFA_TOTAL"]);
    $cuota   = floatval($_POST["CUOTA"]);

    if (!$conductor || !$taxi || !$fecha || !$entrada || !$salida || !$tarifa_total || !$cuota || !$estado) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("
            UPDATE TARIFAS SET 
            CVE_CONDUCTOR=?, CVE_TAXI=?, CVE_ESTADO_CUOTA=?, FECHA=?, 
            HORA_ENTRADA=?, HORA_SALIDA=?, TARIFA_TOTAL=?, CUOTA=?
            WHERE CVE_TARIFAS=?
        ");

        $stmt->bind_param("iiisssdii", 
            $conductor, $taxi, $estado, $fecha, 
            $entrada, $salida, $tarifa_total, $cuota, $id
        );

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errores[] = "Error al actualizar: " . $stmt->error;
        }
    }
}
?>

<div class="card">
    <h2>Editar Tarifa</h2>

    <?php if($errores): ?>
        <div class="alert-error">
            <?php foreach($errores as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <label>Conductor</label>
        <select class="input" name="CVE_CONDUCTOR">
            <?php while($c = $conductores->fetch_assoc()): ?>
                <option value="<?= $c['CVE_CONDUCTOR'] ?>"
                    <?= $c['CVE_CONDUCTOR'] == $tarifa['CVE_CONDUCTOR'] ? 'selected' : '' ?>>
                    <?= $c['NOMBRE'] . " " . $c['APELLIDO_PATERNO'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Taxi</label>
        <select class="input" name="CVE_TAXI">
            <?php while($x = $taxis->fetch_assoc()): ?>
                <option value="<?= $x['CVE_TAXI'] ?>"
                    <?= $x['CVE_TAXI'] == $tarifa['CVE_TAXI'] ? 'selected' : '' ?>>
                    <?= $x['PLACA'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Estado de Cuota</label>
        <select class="input" name="CVE_ESTADO_CUOTA">
            <?php while($e = $estados->fetch_assoc()): ?>
                <option value="<?= $e['CVE_ESTADO_CUOTA'] ?>"
                    <?= $e['CVE_ESTADO_CUOTA'] == $tarifa['CVE_ESTADO_CUOTA'] ? 'selected' : '' ?>>
                    <?= $e['DESCRIPCION'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Fecha</label>
        <input type="date" class="input" name="FECHA" value="<?= $tarifa['FECHA'] ?>">

        <label>Hora Entrada</label>
        <input type="time" class="input" name="HORA_ENTRADA" value="<?= $tarifa['HORA_ENTRADA'] ?>">

        <label>Hora Salida</label>
        <input type="time" class="input" name="HORA_SALIDA" value="<?= $tarifa['HORA_SALIDA'] ?>">

        <label>Tarifa Total</label>
        <input class="input" type="number" step="0.01" name="TARIFA_TOTAL" value="<?= $tarifa['TARIFA_TOTAL'] ?>">

        <label>Cuota</label>
        <input class="input" type="number" step="0.01" name="CUOTA" value="<?= $tarifa['CUOTA'] ?>">

        <button class="button">Actualizar</button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
