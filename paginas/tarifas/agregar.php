<?php
session_start();
if (!isset($_SESSION['usuario'])) header("Location: ../../index.php");

include '../../conexion.php';
include '../../includes/menu.php';

$errores = [];

// Dropdowns
$conductores = $conn->query("SELECT * FROM CONDUCTORES ORDER BY NOMBRE ASC");
$taxis = $conn->query("SELECT * FROM TAXIS ORDER BY PLACA ASC");
$estados = $conn->query("SELECT * FROM ESTADOS_CUOTA ORDER BY DESCRIPCION ASC");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $conductor = intval($_POST["CVE_CONDUCTOR"]);
    $taxi = intval($_POST["CVE_TAXI"]);
    $estado = intval($_POST["CVE_ESTADO_CUOTA"]);

    $fecha = $_POST["FECHA"];
    $entrada = $_POST["HORA_ENTRADA"];
    $salida  = $_POST["HORA_SALIDA"];
    $tarifa  = floatval($_POST["TARIFA_TOTAL"]);
    $cuota   = floatval($_POST["CUOTA"]);

    if (!$conductor || !$taxi || !$fecha || !$entrada || !$salida || !$tarifa || !$cuota || !$estado) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    // Validar duplicado por conductor + fecha
    $check = $conn->prepare("
        SELECT * FROM TARIFAS 
        WHERE CVE_CONDUCTOR = ? AND FECHA = ?
    ");
    $check->bind_param("is", $conductor, $fecha);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $errores[] = "Este conductor ya tiene una tarifa registrada en esta fecha.";
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("
            INSERT INTO TARIFAS 
            (CVE_CONDUCTOR, CVE_TAXI, CVE_ESTADO_CUOTA, FECHA, HORA_ENTRADA, HORA_SALIDA, TARIFA_TOTAL, CUOTA)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("iiisssdd", 
            $conductor, $taxi, $estado, $fecha, $entrada, $salida, $tarifa, $cuota
        );

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
    <h2>Agregar Tarifa</h2>

    <?php if($errores): ?>
        <div class="alert-error">
            <?php foreach($errores as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <label>Conductor</label>
        <select class="input" name="CVE_CONDUCTOR">
            <option value="">Seleccione…</option>
            <?php while($c = $conductores->fetch_assoc()): ?>
                <option value="<?= $c['CVE_CONDUCTOR'] ?>">
                    <?= $c['NOMBRE'] . " " . $c['APELLIDO_PATERNO'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Taxi</label>
        <select class="input" name="CVE_TAXI">
            <option value="">Seleccione…</option>
            <?php while($x = $taxis->fetch_assoc()): ?>
                <option value="<?= $x['CVE_TAXI'] ?>">
                    <?= $x['PLACA'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Estado de Cuota</label>
        <select class="input" name="CVE_ESTADO_CUOTA">
            <option value="">Seleccione…</option>
            <?php while($e = $estados->fetch_assoc()): ?>
                <option value="<?= $e['CVE_ESTADO_CUOTA'] ?>">
                    <?= $e['DESCRIPCION'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Fecha</label>
        <input type="date" class="input" name="FECHA">

        <label>Hora Entrada</label>
        <input type="time" class="input" name="HORA_ENTRADA">

        <label>Hora Salida</label>
        <input type="time" class="input" name="HORA_SALIDA">

        <label>Tarifa Total ($)</label>
        <input class="input" type="number" step="0.01" name="TARIFA_TOTAL">

        <label>Cuota ($)</label>
        <input class="input" type="number" step="0.01" name="CUOTA">

        <button class="button">Guardar</button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
