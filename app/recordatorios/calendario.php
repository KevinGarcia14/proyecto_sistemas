<?php
require_once "../config/db.php";

$mes = isset($_GET["mes"]) ? (int) $_GET["mes"] : (int) date("n");
$anio = isset($_GET["anio"]) ? (int) $_GET["anio"] : (int) date("Y");

if ($mes < 1) {
    $mes = 12;
    $anio--;
}

if ($mes > 12) {
    $mes = 1;
    $anio++;
}

$primerDia = strtotime("$anio-$mes-01");

$diasEnMes = (int) date("t", $primerDia);

$diaSemanaInicio = (int) date("N", $primerDia);

$nombreMes = strftime("%B", $primerDia);

$inicioMes = sprintf("%04d-%02d-01", $anio, $mes);
$finMes = sprintf("%04d-%02d-%02d", $anio, $mes, $diasEnMes);

$resultado = pg_query_params(
    $conn,
    "SELECT *
     FROM recordatorios
     WHERE fecha BETWEEN $1 AND $2
     ORDER BY fecha ASC, hora ASC",
    [$inicioMes, $finMes]
);

$recordatoriosPorDia = [];

while ($fila = pg_fetch_assoc($resultado)) {
    $dia = (int) date("j", strtotime($fila["fecha"]));

    if (!isset($recordatoriosPorDia[$dia])) {
        $recordatoriosPorDia[$dia] = [];
    }

    $recordatoriosPorDia[$dia][] = $fila;
}

$mesAnterior = $mes - 1;
$anioAnterior = $anio;

if ($mesAnterior < 1) {
    $mesAnterior = 12;
    $anioAnterior--;
}

$mesSiguiente = $mes + 1;
$anioSiguiente = $anio;

if ($mesSiguiente > 12) {
    $mesSiguiente = 1;
    $anioSiguiente++;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Calendario | AgendaLocal</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<div class="app-layout">

    <aside class="sidebar">

        <div class="brand">
            <div class="brand-icon">A</div>

            <div>
                <strong>AgendaLocal</strong>
                <span>Recordatorios</span>
            </div>
        </div>

        <nav class="menu">
            <a href="../index.php">Dashboard</a>
            <a href="calendario.php" class="active">Calendario</a>
            <a href="listar.php">Recordatorios</a>
            <a href="crear.php">Nuevo recordatorio</a>
        </nav>

        <div class="sidebar-footer">
            <button id="themeToggle" type="button">
                Cambiar tema
            </button>

            <a href="http://sitio1.local">
                Volver a la landing
            </a>
        </div>

    </aside>


    <main class="main-content">

        <header class="topbar">

            <div>
                <p class="eyebrow">Calendario</p>

                <h1>
                    <?php
                    echo ucfirst(
                        strftime("%B %Y", $primerDia)
                    );
                    ?>
                </h1>
            </div>

            <a
                href="crear.php"
                class="primary-button"
            >
                + Nuevo recordatorio
            </a>

        </header>


        <section class="panel calendar-panel">

            <div class="calendar-controls">

                <a
                    href="?mes=<?php echo $mesAnterior; ?>&anio=<?php echo $anioAnterior; ?>"
                    class="secondary-button"
                >
                    ← Anterior
                </a>

                <a
                    href="calendario.php"
                    class="secondary-button"
                >
                    Hoy
                </a>

                <a
                    href="?mes=<?php echo $mesSiguiente; ?>&anio=<?php echo $anioSiguiente; ?>"
                    class="secondary-button"
                >
                    Siguiente →
                </a>

            </div>


            <div class="calendar-weekdays">

                <span>Lunes</span>
                <span>Martes</span>
                <span>Miércoles</span>
                <span>Jueves</span>
                <span>Viernes</span>
                <span>Sábado</span>
                <span>Domingo</span>

            </div>


            <div class="calendar-month">

                <?php
                for ($i = 1; $i < $diaSemanaInicio; $i++):
                ?>

                    <div class="calendar-day empty"></div>

                <?php endfor; ?>


                <?php for ($dia = 1; $dia <= $diasEnMes; $dia++): ?>

                    <?php
                    $fechaActual = sprintf(
                        "%04d-%02d-%02d",
                        $anio,
                        $mes,
                        $dia
                    );

                    $esHoy = $fechaActual === date("Y-m-d");
                    ?>

                    <div class="calendar-day <?php echo $esHoy ? "today" : ""; ?>">

                        <div class="calendar-day-header">

                            <a
                                href="crear.php?fecha=<?php echo $fechaActual; ?>"
                                class="day-number"
                                title="Crear recordatorio para esta fecha"
                            >
                                <?php echo $dia; ?>
                            </a>

                        </div>


                        <div class="calendar-events">

                            <?php
                            if (isset($recordatoriosPorDia[$dia])):
                            ?>

                                <?php foreach ($recordatoriosPorDia[$dia] as $recordatorio): ?>

                                    <a
                                        href="detalle.php?id=<?php echo $recordatorio["id"]; ?>"
                                        class="calendar-event priority-border-<?php echo strtolower($recordatorio["prioridad"]); ?>"
                                    >

                                        <span class="event-time">
                                            <?php echo htmlspecialchars(substr($recordatorio["hora"], 0, 5)); ?>
                                        </span>

                                        <strong>
                                            <?php echo htmlspecialchars($recordatorio["titulo"]); ?>
                                        </strong>

                                    </a>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endfor; ?>

            </div>

        </section>

    </main>

</div>

<script src="../js/app.js"></script>

</body>
</html>