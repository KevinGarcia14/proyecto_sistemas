<?php
require_once "../config/db.php";

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

$buscar = trim($_GET["buscar"] ?? "");
$categoria = trim($_GET["categoria"] ?? "");
$prioridad = trim($_GET["prioridad"] ?? "");
$estado = trim($_GET["estado"] ?? "");
$orden = $_GET["orden"] ?? "fecha_asc";

$where = [];
$params = [];
$contador = 1;

if ($buscar !== "") {
    $where[] = "(titulo ILIKE $" . $contador . " OR descripcion ILIKE $" . $contador . ")";
    $params[] = "%" . $buscar . "%";
    $contador++;
}

if ($categoria !== "") {
    $where[] = "categoria = $" . $contador;
    $params[] = $categoria;
    $contador++;
}

if ($prioridad !== "") {
    $where[] = "prioridad = $" . $contador;
    $params[] = $prioridad;
    $contador++;
}

if ($estado !== "") {
    $where[] = "estado = $" . $contador;
    $params[] = $estado;
    $contador++;
}

$sql = "SELECT * FROM recordatorios";

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

switch ($orden) {
    case "fecha_desc":
        $sql .= " ORDER BY fecha DESC, hora DESC";
        break;

    case "prioridad":
        $sql .= "
            ORDER BY
            CASE prioridad
                WHEN 'Alta' THEN 1
                WHEN 'Media' THEN 2
                WHEN 'Baja' THEN 3
            END,
            fecha ASC,
            hora ASC
        ";
        break;

    case "titulo":
        $sql .= " ORDER BY titulo ASC";
        break;

    case "estado":
        $sql .= " ORDER BY estado ASC, fecha ASC, hora ASC";
        break;

    default:
        $sql .= " ORDER BY fecha ASC, hora ASC";
}

$resultado = pg_query_params($conn, $sql, $params);

$categorias = pg_query(
    $conn,
    "SELECT DISTINCT categoria
     FROM recordatorios
     ORDER BY categoria ASC"
);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Recordatorios | AgendaLocal</title>

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
                <p class="eyebrow">Agenda</p>
                <h1>Todos los recordatorios</h1>
            </div>

            <a href="crear.php" class="primary-button">
                + Nuevo recordatorio
            </a>

        </header>


        <section class="panel filters-panel">

            <form method="GET" class="filters-form">

                <div class="form-group search-field">
                    <label for="buscar">
                        Buscar
                    </label>

                    <input
                        type="search"
                        id="buscar"
                        name="buscar"
                        value="<?php echo htmlspecialchars($buscar); ?>"
                        placeholder="Buscar por título o descripción"
                    >
                </div>


                <div class="form-group">
                    <label for="categoria">
                        Categoría
                    </label>

                    <select id="categoria" name="categoria">

                        <option value="">
                            Todas
                        </option>

                        <?php while ($filaCategoria = pg_fetch_assoc($categorias)): ?>

                            <option
                                value="<?php echo htmlspecialchars($filaCategoria["categoria"]); ?>"
                                <?php echo $categoria === $filaCategoria["categoria"] ? "selected" : ""; ?>
                            >
                                <?php echo htmlspecialchars($filaCategoria["categoria"]); ?>
                            </option>

                        <?php endwhile; ?>

                    </select>
                </div>


                <div class="form-group">
                    <label for="prioridad">
                        Prioridad
                    </label>

                    <select id="prioridad" name="prioridad">
                        <option value="">Todas</option>
                        <option value="Baja" <?php echo $prioridad === "Baja" ? "selected" : ""; ?>>
                            Baja
                        </option>
                        <option value="Media" <?php echo $prioridad === "Media" ? "selected" : ""; ?>>
                            Media
                        </option>
                        <option value="Alta" <?php echo $prioridad === "Alta" ? "selected" : ""; ?>>
                            Alta
                        </option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="estado">
                        Estado
                    </label>

                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="Pendiente" <?php echo $estado === "Pendiente" ? "selected" : ""; ?>>
                            Pendiente
                        </option>
                        <option value="Completado" <?php echo $estado === "Completado" ? "selected" : ""; ?>>
                            Completado
                        </option>
                        <option value="Vencido" <?php echo $estado === "Vencido" ? "selected" : ""; ?>>
                            Vencido
                        </option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="orden">
                        Ordenar por
                    </label>

                    <select id="orden" name="orden">
                        <option value="fecha_asc" <?php echo $orden === "fecha_asc" ? "selected" : ""; ?>>
                            Fecha más próxima
                        </option>

                        <option value="fecha_desc" <?php echo $orden === "fecha_desc" ? "selected" : ""; ?>>
                            Fecha más lejana
                        </option>

                        <option value="prioridad" <?php echo $orden === "prioridad" ? "selected" : ""; ?>>
                            Prioridad
                        </option>

                        <option value="titulo" <?php echo $orden === "titulo" ? "selected" : ""; ?>>
                            Título
                        </option>

                        <option value="estado" <?php echo $orden === "estado" ? "selected" : ""; ?>>
                            Estado
                        </option>
                    </select>
                </div>


                <div class="filters-actions">

                    <button type="submit" class="primary-button">
                        Aplicar
                    </button>

                    <a href="listar.php" class="secondary-button">
                        Limpiar
                    </a>

                </div>

            </form>

        </section>


        <section class="panel reminders-panel">

            <?php if (pg_num_rows($resultado) === 0): ?>

                <div class="empty-state">
                    No se encontraron recordatorios.
                </div>

            <?php else: ?>

                <div class="table-wrapper">

                    <table class="reminders-table">

                        <thead>
                            <tr>
                                <th>Recordatorio</th>
                                <th>Fecha</th>
                                <th>Categoría</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php while ($recordatorio = pg_fetch_assoc($resultado)): ?>

                            <tr>

                                <td>
                                    <a
                                        href="detalle.php?id=<?php echo $recordatorio["id"]; ?>"
                                        class="reminder-title"
                                    >
                                        <?php echo htmlspecialchars($recordatorio["titulo"]); ?>
                                    </a>

                                    <small>
                                        <?php
                                        echo htmlspecialchars(
                                            mb_strimwidth(
                                                $recordatorio["descripcion"] ?? "",
                                                0,
                                                65,
                                                "..."
                                            )
                                        );
                                        ?>
                                    </small>
                                </td>


                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        date(
                                            "d/m/Y",
                                            strtotime($recordatorio["fecha"])
                                        )
                                    );
                                    ?>

                                    <small>
                                        <?php echo htmlspecialchars(substr($recordatorio["hora"], 0, 5)); ?>
                                    </small>
                                </td>


                                <td>
                                    <?php echo htmlspecialchars($recordatorio["categoria"]); ?>
                                </td>


                                <td>
                                    <span
                                        class="priority priority-<?php echo strtolower($recordatorio["prioridad"]); ?>"
                                    >
                                        <?php echo htmlspecialchars($recordatorio["prioridad"]); ?>
                                    </span>
                                </td>


                                <td>
                                    <span
                                        class="record-status status-<?php echo strtolower($recordatorio["estado"]); ?>"
                                    >
                                        <?php echo htmlspecialchars($recordatorio["estado"]); ?>
                                    </span>
                                </td>


                                <td>

                                    <div class="actions">

                                        <a
                                            href="detalle.php?id=<?php echo $recordatorio["id"]; ?>"
                                            class="action-link"
                                        >
                                            Ver
                                        </a>

                                        <a
                                            href="editar.php?id=<?php echo $recordatorio["id"]; ?>"
                                            class="action-link"
                                        >
                                            Editar
                                        </a>

                                        <?php if ($recordatorio["estado"] !== "Completado"): ?>

                                            <a
                                                href="completar.php?id=<?php echo $recordatorio["id"]; ?>"
                                                class="action-link success"
                                            >
                                                Completar
                                            </a>

                                        <?php endif; ?>

                                        <a
                                            href="eliminar.php?id=<?php echo $recordatorio["id"]; ?>"
                                            class="action-link danger"
                                            onclick="return confirm('¿Eliminar este recordatorio?');"
                                        >
                                            Eliminar
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>

    </main>

</div>

<script src="../js/app.js"></script>

</body>
</html>