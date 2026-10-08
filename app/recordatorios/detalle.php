<?php
require_once "../config/db.php";

$id = $_GET["id"] ?? null;

if (!$id || !ctype_digit($id)) {
    die("ID inválido.");
}

$resultado = pg_query_params(
    $conn,
    "SELECT * FROM recordatorios WHERE id = $1",
    [$id]
);

$recordatorio = pg_fetch_assoc($resultado);

if (!$recordatorio) {
    die("Recordatorio no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($recordatorio["titulo"]); ?>
        | AgendaLocal
    </title>

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
            <a href="calendario.php">Calendario</a>
            <a href="listar.php" class="active">Recordatorios</a>
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
                <p class="eyebrow">Detalle</p>

                <h1>
                    <?php echo htmlspecialchars($recordatorio["titulo"]); ?>
                </h1>
            </div>

            <a href="listar.php" class="secondary-button">
                Volver
            </a>

        </header>


        <section class="panel detail-panel">

            <div class="detail-header">

                <div>
                    <span
                        class="priority priority-<?php echo strtolower($recordatorio["prioridad"]); ?>"
                    >
                        <?php echo htmlspecialchars($recordatorio["prioridad"]); ?>
                    </span>

                    <span
                        class="record-status status-<?php echo strtolower($recordatorio["estado"]); ?>"
                    >
                        <?php echo htmlspecialchars($recordatorio["estado"]); ?>
                    </span>
                </div>

            </div>


            <div class="detail-grid">

                <div class="detail-item">
                    <span>Fecha</span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            date(
                                "d/m/Y",
                                strtotime($recordatorio["fecha"])
                            )
                        );
                        ?>
                    </strong>
                </div>


                <div class="detail-item">
                    <span>Hora</span>

                    <strong>
                        <?php echo htmlspecialchars(substr($recordatorio["hora"], 0, 5)); ?>
                    </strong>
                </div>


                <div class="detail-item">
                    <span>Categoría</span>

                    <strong>
                        <?php echo htmlspecialchars($recordatorio["categoria"]); ?>
                    </strong>
                </div>


                <div class="detail-item">
                    <span>Creado</span>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            date(
                                "d/m/Y H:i",
                                strtotime($recordatorio["fecha_creacion"])
                            )
                        );
                        ?>
                    </strong>
                </div>

            </div>


            <div class="detail-description">

                <span>Descripción</span>

                <p>
                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $recordatorio["descripcion"] ?: "Sin descripción."
                        )
                    );
                    ?>
                </p>

            </div>


            <div class="detail-actions">

                <a
                    href="editar.php?id=<?php echo $id; ?>"
                    class="secondary-button"
                >
                    Editar
                </a>

                <?php if ($recordatorio["estado"] !== "Completado"): ?>

                    <a
                        href="completar.php?id=<?php echo $id; ?>"
                        class="primary-button"
                    >
                        Marcar como completado
                    </a>

                <?php endif; ?>

                <a
                    href="eliminar.php?id=<?php echo $id; ?>"
                    class="danger-button"
                    onclick="return confirm('¿Seguro que deseas eliminar este recordatorio?');"
                >
                    Eliminar
                </a>

            </div>

        </section>

    </main>

</div>

<script src="../js/app.js"></script>

</body>
</html>