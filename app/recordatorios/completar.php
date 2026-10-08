<?php
require_once "../config/db.php";

$id = $_GET["id"] ?? null;

if (!$id || !ctype_digit($id)) {
    die("ID inválido.");
}

pg_query_params(
    $conn,
    "UPDATE recordatorios
     SET estado = 'Completado'
     WHERE id = $1",
    [$id]
);

header("Location: listar.php");
exit;