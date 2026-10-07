<?php
require_once "../config/db.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");

    if ($nombre === "") {
        $mensaje = "El nombre es obligatorio.";
    } else {
        $sql = "INSERT INTO contactos (nombre, telefono, correo)
                VALUES ($1, $2, $3)";

        $resultado = pg_query_params(
            $conn,
            $sql,
            [$nombre, $telefono, $correo]
        );

        if ($resultado) {
            header("Location: listar.php");
            exit;
        } else {
            $mensaje = "No se pudo guardar el contacto.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo contacto - AgendaLocal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <h1>Nuevo contacto</h1>

    <?php if ($mensaje !== ""): ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required>
        <br><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono">
        <br><br>

        <label>Correo:</label><br>
        <input type="email" name="correo">
        <br><br>

        <button type="submit">Guardar contacto</button>
    </form>

    <br>

    <a href="listar.php">Volver a contactos</a>

</body>
</html>