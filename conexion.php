<?php
$host = "sql111.infinityfree.com";
$dbname = "if0_43019044_proyecto_expoferia";
$username = "if0_43019044";
$password = "My5EHZe0qGBU";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error crítico de conexión a la base de datos: " . $e->getMessage());
}
?>