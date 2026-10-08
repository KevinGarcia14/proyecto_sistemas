<?php

$host = "localhost";
$port = "5432";
$dbname = "agendalocal";
$user = "agenda_user";
$password = "Agenda2026!";

$conn = pg_connect(
    "host=$host port=$port dbname=$dbname user=$user password=$password"
);

if (!$conn) {
    die("No se pudo conectar con PostgreSQL.");
}