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

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $fecha = $_POST["fecha"] ?? "";
    $hora = $_POST["hora"] ?? "";
    $categoria = trim($_POST["categoria"] ?? "");
    $prioridad = $_POST["prioridad"] ?? "Media";

    if ($titulo === "" || $fecha === "" || $hora === "" || $categoria === "") {
        $mensaje = "Completa todos los campos obligatorios.";
    } else {
        $sql = "
            UPDATE recordatorios
            SET titulo = $1,
                descripcion = $2,
                fecha = $3,
                hora = $4,
                categoria = $5,
                prioridad = $6
            WHERE id = $7
        ";

        $actualizado = pg_query_params(
            $conn,
            $sql,
            [
                $titulo,
                $descripcion,
                $fecha,
                $hora,
                $categoria,
                $prioridad,
                $id
            ]
        );

        if ($actualizado) {
            header("Location: detalle.php?id=" . $id);
            exit;
        }

        $mensaje = "No se pudo actualizar el recordatorio.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar recordatorio | AgendaLocal</title>
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
                <p class="eyebrow">Editar</p>
                <h1>Modificar recordatorio</h1>
            </div>

            <a
                href="detalle.php?id=<?php echo $id; ?>"
                class="secondary-button"
            >
                Volver
            </a>

        </header>


        <section class="panel form-panel">

            <?php if ($mensaje !== ""): ?>
                <div class="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="reminder-form">

                <div class="form-group full-width">
                    <label>Título *</label>

                    <input
                        type="text"
                        name="titulo"
                        required
                        value="<?php echo htmlspecialchars($recordatorio["titulo"]); ?>"
                    >
                </div>


                <div class="form-group full-width">
                    <label>Descripción</label>

                    <textarea
                        name="descripcion"
                        rows="5"
                    ><?php echo htmlspecialchars($recordatorio["descripcion"] ?? ""); ?></textarea>
                </div>


                <div class="form-group">
                    <label>Fecha *</label>

                    <input
                        type="date"
                        name="fecha"
                        required
                        value="<?php echo htmlspecialchars($recordatorio["fecha"]); ?>"
                    >
                </div>


                <div class="form-group">
                    <label>Hora *</label>

                    <input
                        type="time"
                        name="hora"
                        required
                        value="<?php echo htmlspecialchars(substr($recordatorio["hora"], 0, 5)); ?>"
                    >
                </div>


                <div class="form-group">
                    <label>Categoría *</label>

                    <input
                        type="text"
                        name="categoria"
                        list="categorias"
                        required
                        value="<?php echo htmlspecialchars($recordatorio["categoria"]); ?>"
                    >

                    <datalist id="categorias">
                        <option value="Estudio">
                        <option value="Trabajo">
                        <option value="Personal">
                        <option value="Reuniones">
                        <option value="Otros">
                    </datalist>
                </div>


                <div class="form-group">
                    <label>Prioridad *</label>

                    <select name="prioridad" required>
                        <option value="Baja" <?php echo $recordatorio["prioridad"] === "Baja" ? "selected" : ""; ?>>
                            Baja
                        </option>

                        <option value="Media" <?php echo $recordatorio["prioridad"] === "Media" ? "selected" : ""; ?>>
                            Media
                        </option>

                        <option value="Alta" <?php echo $recordatorio["prioridad"] === "Alta" ? "selected" : ""; ?>>
                            Alta
                        </option>
                    </select>
                </div>


                <div class="form-actions full-width">

                    <a
                        href="detalle.php?id=<?php echo $id; ?>"
                        class="secondary-button"
                    >
                        Cancelar
                    </a>

                    <button type="submit" class="primary-button">
                        Guardar cambios
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

<script src="../js/app.js"></script>

</body>
</html>