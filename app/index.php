<?php
require_once "config/db.php";

/* =========================
   ACTUALIZAR VENCIDOS
========================= */

pg_query(
    $conn,
    "UPDATE recordatorios
     SET estado = 'Vencido'
     WHERE estado = 'Pendiente'
     AND (
        fecha < CURRENT_DATE
        OR (fecha = CURRENT_DATE AND hora < CURRENT_TIME)
     )"
);


/* =========================
   ESTADÍSTICAS GENERALES
========================= */

$total = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*) FROM recordatorios"
    ),
    0,
    0
);

$pendientes = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*)
         FROM recordatorios
         WHERE estado = 'Pendiente'"
    ),
    0,
    0
);

$completados = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*)
         FROM recordatorios
         WHERE estado = 'Completado'"
    ),
    0,
    0
);

$vencidos = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*)
         FROM recordatorios
         WHERE estado = 'Vencido'"
    ),
    0,
    0
);


/* =========================
   RECORDATORIOS DE HOY
========================= */

$hoy = pg_query(
    $conn,
    "SELECT *
     FROM recordatorios
     WHERE fecha = CURRENT_DATE
     ORDER BY hora ASC"
);


/* =========================
   PRÓXIMOS RECORDATORIOS
========================= */

$proximos = pg_query(
    $conn,
    "SELECT *
     FROM recordatorios
     WHERE estado = 'Pendiente'
     AND fecha >= CURRENT_DATE
     ORDER BY fecha ASC, hora ASC
     LIMIT 5"
);


/* =========================
   ESTADÍSTICAS POR CATEGORÍA
========================= */

$categoriasStats = pg_query(
    $conn,
    "SELECT categoria, COUNT(*) AS total
     FROM recordatorios
     GROUP BY categoria
     ORDER BY total DESC, categoria ASC"
);

$maxCategoria = 1;
$categoriasData = [];

while ($fila = pg_fetch_assoc($categoriasStats)) {

    $categoriasData[] = $fila;

    if ((int)$fila["total"] > $maxCategoria) {
        $maxCategoria = (int)$fila["total"];
    }
}


/* =========================
   ESTADÍSTICAS POR PRIORIDAD
========================= */

$prioridadesStats = pg_query(
    $conn,
    "SELECT prioridad, COUNT(*) AS total
     FROM recordatorios
     GROUP BY prioridad"
);

$prioridades = [
    "Alta" => 0,
    "Media" => 0,
    "Baja" => 0
];

while ($fila = pg_fetch_assoc($prioridadesStats)) {

    if (isset($prioridades[$fila["prioridad"]])) {
        $prioridades[$fila["prioridad"]] =
            (int)$fila["total"];
    }
}

$maxPrioridad = max(
    1,
    $prioridades["Alta"],
    $prioridades["Media"],
    $prioridades["Baja"]
);


/* =========================
   PRÓXIMOS 7 DÍAS
========================= */

$estaSemana = pg_fetch_result(
    pg_query(
        $conn,
        "SELECT COUNT(*)
         FROM recordatorios
         WHERE fecha BETWEEN CURRENT_DATE
         AND CURRENT_DATE + INTERVAL '7 days'"
    ),
    0,
    0
);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        AgendaLocal | Dashboard
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<div class="app-layout">


    <!-- =========================
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <div class="brand">

            <div class="brand-icon">
                A
            </div>

            <div>
                <strong>
                    AgendaLocal
                </strong>

                <span>
                    Recordatorios
                </span>
            </div>

        </div>


        <nav class="menu">

            <a
                href="index.php"
                class="active"
            >
                Dashboard
            </a>


            <a
                href="recordatorios/calendario.php"
            >
                Calendario
            </a>


            <a
                href="recordatorios/listar.php"
            >
                Recordatorios
            </a>


            <a
                href="recordatorios/crear.php"
            >
                Nuevo recordatorio
            </a>

        </nav>


        <div class="sidebar-footer">

            <button
                id="themeToggle"
                type="button"
            >
                Cambiar tema
            </button>


            <a href="http://sitio1.local">
                Volver a la landing
            </a>

        </div>

    </aside>



    <!-- =========================
         CONTENIDO PRINCIPAL
    ========================== -->

    <main class="main-content">


        <!-- =========================
             ENCABEZADO
        ========================== -->

        <header class="topbar">

            <div>

                <p class="eyebrow">
                    Panel principal
                </p>

                <h1>
                    Tu agenda de hoy
                </h1>

            </div>


            <a
                href="recordatorios/crear.php"
                class="primary-button"
            >
                + Nuevo recordatorio
            </a>

        </header>



        <!-- =========================
             ESTADÍSTICAS
        ========================== -->

        <section class="stats-grid">


            <article class="stat-card">

                <span>
                    Total
                </span>

                <strong>
                    <?php echo $total; ?>
                </strong>

            </article>


            <article class="stat-card">

                <span>
                    Pendientes
                </span>

                <strong>
                    <?php echo $pendientes; ?>
                </strong>

            </article>


            <article class="stat-card">

                <span>
                    Completados
                </span>

                <strong>
                    <?php echo $completados; ?>
                </strong>

            </article>


            <article class="stat-card">

                <span>
                    Vencidos
                </span>

                <strong>
                    <?php echo $vencidos; ?>
                </strong>

            </article>


        </section>



        <!-- =========================
             HOY Y PRÓXIMOS
        ========================== -->

        <section class="dashboard-grid">


            <!-- HOY -->

            <article class="panel">


                <div class="panel-header">

                    <div>

                        <span>
                            Hoy
                        </span>

                        <h2>
                            Recordatorios del día
                        </h2>

                    </div>

                </div>


                <div class="reminder-list">


                    <?php if (pg_num_rows($hoy) === 0): ?>


                        <div class="empty-state">

                            No tienes recordatorios
                            para hoy.

                        </div>


                    <?php else: ?>


                        <?php
                        while (
                            $recordatorio =
                            pg_fetch_assoc($hoy)
                        ):
                        ?>


                            <a
                                class="reminder-item"
                                href="
                                recordatorios/detalle.php?id=
                                <?php echo $recordatorio["id"]; ?>
                                "
                            >


                                <div>


                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $recordatorio["titulo"]
                                        );
                                        ?>

                                    </strong>


                                    <span>

                                        <?php
                                        echo htmlspecialchars(
                                            substr(
                                                $recordatorio["hora"],
                                                0,
                                                5
                                            )
                                        );
                                        ?>

                                        ·

                                        <?php
                                        echo htmlspecialchars(
                                            $recordatorio["categoria"]
                                        );
                                        ?>

                                    </span>


                                </div>


                                <span
                                    class="
                                    priority
                                    priority-<?php
                                    echo strtolower(
                                        $recordatorio["prioridad"]
                                    );
                                    ?>
                                    "
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $recordatorio["prioridad"]
                                    );
                                    ?>

                                </span>


                            </a>


                        <?php endwhile; ?>


                    <?php endif; ?>


                </div>


            </article>



            <!-- PRÓXIMOS -->

            <article class="panel">


                <div class="panel-header">


                    <div>

                        <span>
                            Próximamente
                        </span>

                        <h2>
                            Siguientes recordatorios
                        </h2>

                    </div>


                    <a
                        href="recordatorios/listar.php"
                    >
                        Ver todos
                    </a>


                </div>


                <div class="reminder-list">


                    <?php if (pg_num_rows($proximos) === 0): ?>


                        <div class="empty-state">

                            No tienes recordatorios
                            próximos.

                        </div>


                    <?php else: ?>


                        <?php
                        while (
                            $recordatorio =
                            pg_fetch_assoc($proximos)
                        ):
                        ?>


                            <a
                                class="reminder-item"
                                href="
                                recordatorios/detalle.php?id=
                                <?php echo $recordatorio["id"]; ?>
                                "
                            >


                                <div>


                                    <strong>

                                        <?php
                                        echo htmlspecialchars(
                                            $recordatorio["titulo"]
                                        );
                                        ?>

                                    </strong>


                                    <span>

                                        <?php
                                        echo htmlspecialchars(
                                            date(
                                                "d/m/Y",
                                                strtotime(
                                                    $recordatorio["fecha"]
                                                )
                                            )
                                        );
                                        ?>

                                        ·

                                        <?php
                                        echo htmlspecialchars(
                                            substr(
                                                $recordatorio["hora"],
                                                0,
                                                5
                                            )
                                        );
                                        ?>

                                    </span>


                                </div>


                                <span class="status">

                                    <?php
                                    echo htmlspecialchars(
                                        $recordatorio["estado"]
                                    );
                                    ?>

                                </span>


                            </a>


                        <?php endwhile; ?>


                    <?php endif; ?>


                </div>


            </article>


        </section>



        <!-- =========================
             ANALÍTICA
        ========================== -->

        <section class="analytics-grid">


            <!-- GRÁFICA CATEGORÍAS -->

            <article class="panel">


                <div class="panel-header">

                    <div>

                        <span>
                            Distribución
                        </span>

                        <h2>
                            Recordatorios por categoría
                        </h2>

                    </div>

                </div>


                <?php
                if (
                    count($categoriasData) === 0
                ):
                ?>


                    <div class="empty-state">

                        Aún no hay información
                        para mostrar.

                    </div>


                <?php else: ?>


                    <div class="chart-list">


                        <?php
                        foreach (
                            $categoriasData
                            as $categoria
                        ):
                        ?>


                            <?php

                            $porcentaje =
                                (
                                    (int)$categoria["total"]
                                    /
                                    $maxCategoria
                                )
                                * 100;

                            ?>


                            <div class="chart-row">


                                <div class="chart-label">


                                    <span>

                                        <?php
                                        echo htmlspecialchars(
                                            $categoria["categoria"]
                                        );
                                        ?>

                                    </span>


                                    <strong>

                                        <?php
                                        echo (int)$categoria["total"];
                                        ?>

                                    </strong>


                                </div>


                                <div class="chart-track">


                                    <div
                                        class="
                                        chart-bar
                                        category-bar
                                        "
                                        style="
                                        width:
                                        <?php
                                        echo $porcentaje;
                                        ?>%;
                                        "
                                    >
                                    </div>


                                </div>


                            </div>


                        <?php endforeach; ?>


                    </div>


                <?php endif; ?>


            </article>



            <!-- GRÁFICA PRIORIDAD -->

            <article class="panel">


                <div class="panel-header">


                    <div>

                        <span>
                            Prioridad
                        </span>

                        <h2>
                            Distribución de prioridades
                        </h2>

                    </div>


                </div>


                <div class="chart-list">


                    <?php

                    foreach (
                        ["Alta", "Media", "Baja"]
                        as $nivel
                    ):

                        $cantidad =
                            $prioridades[$nivel];

                        $porcentaje =
                            (
                                $cantidad
                                /
                                $maxPrioridad
                            )
                            * 100;

                    ?>


                        <div class="chart-row">


                            <div class="chart-label">


                                <span>

                                    <?php
                                    echo $nivel;
                                    ?>

                                </span>


                                <strong>

                                    <?php
                                    echo $cantidad;
                                    ?>

                                </strong>


                            </div>


                            <div class="chart-track">


                                <div
                                    class="
                                    chart-bar
                                    priority-chart-<?php
                                    echo strtolower(
                                        $nivel
                                    );
                                    ?>
                                    "
                                    style="
                                    width:
                                    <?php
                                    echo $porcentaje;
                                    ?>%;
                                    "
                                >
                                </div>


                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>



                <!-- RESUMEN SEMANAL -->

                <div class="week-summary">


                    <span>
                        Próximos 7 días
                    </span>


                    <strong>
                        <?php echo $estaSemana; ?>
                    </strong>


                    <small>
                        recordatorios programados
                    </small>


                </div>


            </article>


        </section>


    </main>


</div>


<script src="js/app.js"></script>


</body>

</html>