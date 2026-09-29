<?php
// ==============================================================================
// CONFIGURACIÓN PARA SERVIDOR REMOTO (INFINITYFREE / PRODUCCIÓN)
// ==============================================================================
//$host = "sql111.infinityfree.com";
//$dbname = "if0_43019044_proyecto_expoferia";
//$username = "if0_43019044";
//$password = "My5EHZe0qGBU";

// ==============================================================================
// CONFIGURACIÓN PARA ENTORNO LOCAL (XAMPP / DESARROLLO LOCAL)
// Si estás trabajando de forma local en tu máquina, descomenta las 4 líneas de abajo
// y comenta las 4 líneas de arriba de InfinityFree.
// ==============================================================================
 $host = "localhost";
 $dbname = "proyecto_expoferia";
 $username = "root";
 $password = "";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error crítico de conexión a la base de datos: " . $e->getMessage());
}
?>