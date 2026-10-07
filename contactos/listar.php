<?php
require_once "../config/db.php";

$resultado = pg_query($conn, "SELECT * FROM contactos ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactos - AgendaLocal</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <h1>Contactos</h1>

    <a href="crear.php">Nuevo contacto</a>
    <br><br>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

        <?php while ($fila = pg_fetch_assoc($resultado)): ?>

            <tr>
                <td><?php echo htmlspecialchars($fila["id"]); ?></td>
                <td><?php echo htmlspecialchars($fila["nombre"]); ?></td>
                <td><?php echo htmlspecialchars($fila["telefono"] ?? ""); ?></td>
                <td><?php echo htmlspecialchars($fila["correo"] ?? ""); ?></td>

                <td>
                    <a href="editar.php?id=<?php echo $fila["id"]; ?>">
                        Editar
                    </a>

                    |

                    <a href="eliminar.php?id=<?php echo $fila["id"]; ?>">
                        Eliminar
                    </a>
                </td>
            </tr>

        <?php endwhile; ?>

        </tbody>
    </table>

</body>
</html>