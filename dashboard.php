<?php
session_start();
if (!isset($_SESSION['usuario'])) header('Location: index.php');
include 'conexion.php';
include 'includes/menu.php';

/* CONSULTAS PARA RESUMEN */

// total conductores
$totalConductores = $conn->query("SELECT COUNT(*) AS total FROM CONDUCTORES")->fetch_assoc()['total'];

// total taxis
$totalTaxis = $conn->query("SELECT COUNT(*) AS total FROM TAXIS")->fetch_assoc()['total'];

// total tarifas
$totalTarifas = $conn->query("SELECT COUNT(*) AS total FROM TARIFAS")->fetch_assoc()['total'];

// tarifas de últimos 7 días
$tarifasDias = $conn->query("
    SELECT DATE(FECHA) AS fecha,
           SUM(TARIFA_TOTAL) AS total
    FROM TARIFAS
    GROUP BY DATE(FECHA)
    ORDER BY fecha ASC
    LIMIT 7
");

// últimos movimientos
$ultimos = $conn->query("
    (SELECT 'Conductor agregado' AS tipo, NOMBRE AS dato, CVE_CONDUCTOR AS id, 'CONDUCTORES' AS tabla FROM CONDUCTORES ORDER BY CVE_CONDUCTOR DESC LIMIT 3)
    UNION
    (SELECT 'Taxi agregado' AS tipo, PLACA AS dato, CVE_TAXI AS id, 'TAXIS' AS tabla FROM TAXIS ORDER BY CVE_TAXI DESC LIMIT 3)
    UNION
    (SELECT 'Tarifa registrada' AS tipo, TARIFA_TOTAL AS dato, CVE_TARIFAS AS id, 'TARIFAS' AS tabla FROM TARIFAS ORDER BY CVE_TARIFAS DESC LIMIT 3)
    ORDER BY id DESC
");
?>

<div class="dashboard">

    <h2 class="titulo">Bienvenido, <span><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? $_SESSION['usuario']) ?></span></h2>

    <!-- Tarjetas resumen -->
    <div class="tarjetas">

        <div class="tarjeta shadow">
            <h3><?= $totalConductores ?></h3>
            <p>Conductores</p>
        </div>

        <div class="tarjeta shadow">
            <h3><?= $totalTaxis ?></h3>
            <p>Taxis registrados</p>
        </div>

        <div class="tarjeta shadow">
            <h3><?= $totalTarifas ?></h3>
            <p>Tarifas realizadas</p>
        </div>

    </div>

    <!-- Gráfica simple -->
    <div class="card shadow" style="margin-top:20px">
        <h3>Tarifas de los últimos días</h3>
        <canvas id="graficaTarifas" height="120"></canvas>
    </div>

    <!-- Últimos movimientos -->
    <div class="card shadow" style="margin-top:20px">
        <h3>Últimos movimientos</h3>

        <ul class="movimientos">
            <?php while ($m = $ultimos->fetch_assoc()): ?>
                <li>
                    <strong><?= $m['tipo'] ?>:</strong>
                    <?= htmlspecialchars($m['dato']) ?>  
                    <span class="etiqueta"><?= $m['tabla'] ?></span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

</div>

<?php include 'includes/footer.php'; ?>

<script>

//   GRÁFICA SOLO CON CANVAS

let etiquetas = [];
let valores = [];

<?php while($r = $tarifasDias->fetch_assoc()): ?>
    etiquetas.push("<?= $r['fecha'] ?>");
    valores.push(<?= $r['total'] ?>);
<?php endwhile; ?>

const ctx = document.getElementById('graficaTarifas').getContext('2d');

function dibujarLinea(ctx, puntos, color = "blue") {
    ctx.beginPath();
    ctx.moveTo(40, 150 - puntos[0]);

    for (let i = 1; i < puntos.length; i++) {
        ctx.lineTo(40 + i * 50, 150 - puntos[i]);
    }

    ctx.strokeStyle = color;
    ctx.lineWidth = 2;
    ctx.stroke();
}

window.onload = function() {

    const canvas = document.getElementById('graficaTarifas');
    const ctx = canvas.getContext('2d');

    // Normalizar
    let max = Math.max(...valores, 1);
    let puntos = valores.map(v => (v / max) * 120);

    // Ejes
    ctx.beginPath();
    ctx.moveTo(40, 10);
    ctx.lineTo(40, 150);
    ctx.lineTo(400, 150);
    ctx.strokeStyle = "#444";
    ctx.stroke();

    dibujarLinea(ctx, puntos, "#1e88e5");
}
</script>
