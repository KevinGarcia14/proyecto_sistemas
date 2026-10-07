<?php
require_once "../config/db.php";

$id = $_GET["id"] ?? null;

if (!$id || !ctype_digit($id)) {
    die("ID inválido.");
}

$resultado = pg_query_params(
    $conn,
    "SELECT * FROM contactos WHERE id = $1",
    [$id]
);

$contacto = pg_fetch_assoc($resultado);

if (!$contacto) {
    die("Contacto no encontrado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $telefono = trim($_POST["telefono"] ?? "");
    $correo = trim($_POST["correo"] ?? "");

    if ($nombre !== "") {
        $sql = "UPDATE contactos
                SET nombre = $1, telefono = $2, correo = $3
                WHERE id = $4";

        pg_query_params(
            $conn,
            $sql,
            [$nombre, $telefono, $correo, $id]
        );

        header("Location: listar.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar contacto - AgendaLocal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <h1>Editar contacto</h1>

    <form method="POST">
        <label>Nombre:</label><br>
        <input
            type="text"
            name="nombre"
            value="<?php echo htmlspecialchars($contacto["nombre"]); ?>"
            required
        >
        <br><br>

        <label>Teléfono:</label><br>
        <input
            type="text"
            name="telefono"
            value="<?php echo htmlspecialchars($contacto["telefono"] ?? ""); ?>"
        >
        <br><br>

        <label>Correo:</label><br>
        <input
            type="email"
            name="correo"
            value="<?php echo htmlspecialchars($contacto["correo"] ?? ""); ?>"
        >
        <br><br>

        <button type="submit">Guardar cambios</button>
    </form>

    <br>

    <a href="listar.php">Cancelar</a>

</body>
</html>