<?php
require_once "../config/db.php";

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
            INSERT INTO recordatorios
            (titulo, descripcion, fecha, hora, categoria, prioridad)
            VALUES ($1, $2, $3, $4, $5, $6)
        ";

        $resultado = pg_query_params(
            $conn,
            $sql,
            [
                $titulo,
                $descripcion,
                $fecha,
                $hora,
                $categoria,
                $prioridad
            ]
        );

        if ($resultado) {
            header("Location: listar.php");
            exit;
        } else {
            $mensaje = "No se pudo guardar el recordatorio.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nuevo recordatorio | AgendaLocal</title>

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
            <a href="listar.php">Recordatorios</a>
            <a href="crear.php" class="active">Nuevo recordatorio</a>
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
                <p class="eyebrow">Nuevo</p>
                <h1>Crear recordatorio</h1>
            </div>

            <a href="listar.php" class="secondary-button">
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

                    <label for="titulo">
                        Título *
                    </label>

                    <input
                        type="text"
                        id="titulo"
                        name="titulo"
                        maxlength="150"
                        required
                        placeholder="Ej. Entregar proyecto final"
                    >

                </div>


                <div class="form-group full-width">

                    <label for="descripcion">
                        Descripción
                    </label>

                    <textarea
                        id="descripcion"
                        name="descripcion"
                        rows="5"
                        placeholder="Agrega detalles sobre el recordatorio..."
                    ></textarea>

                </div>


                <div class="form-group">

                    <label for="fecha">
                        Fecha *
                    </label>

                    <input
                        type="date"
                        id="fecha"
                        name="fecha"
                        required
                        value="<?php echo htmlspecialchars($_GET["fecha"] ?? ""); ?>"
                    >

                </div>


                <div class="form-group">

                    <label for="hora">
                        Hora *
                    </label>

                    <input
                        type="time"
                        id="hora"
                        name="hora"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="categoria">
                        Categoría *
                    </label>

                    <input
                        type="text"
                        id="categoria"
                        name="categoria"
                        list="categorias"
                        required
                        placeholder="Selecciona o escribe una categoría"
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

                    <label for="prioridad">
                        Prioridad *
                    </label>

                    <select
                        id="prioridad"
                        name="prioridad"
                        required
                    >
                        <option value="Baja">
                            Baja
                        </option>

                        <option value="Media" selected>
                            Media
                        </option>

                        <option value="Alta">
                            Alta
                        </option>
                    </select>

                </div>


                <div class="form-actions full-width">

                    <a
                        href="listar.php"
                        class="secondary-button"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Guardar recordatorio
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>

<script src="../js/app.js"></script>

</body>
</html>