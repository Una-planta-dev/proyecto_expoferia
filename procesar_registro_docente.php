<?php
// ==============================================================================
// CONFIGURACIÓN PARA SERVIDOR REMOTO (INFINITYFREE / PRODUCCIÓN)
// ==============================================================================
$host = "sql111.infinityfree.com";
$dbname = "if0_43019044_proyecto_expoferia";
$username = "if0_43019044";
$password = "My5EHZe0qGBU";

// ==============================================================================
// CONFIGURACIÓN PARA ENTORNO LOCAL (XAMPP / DESARROLLO LOCAL)
// Si estás trabajando de forma local en tu máquina, descomenta las 4 líneas de abajo
// y comenta las 4 líneas de arriba de InfinityFree.
// ==============================================================================
// $host = "localhost";
// $dbname = "proyecto_expoferia";
// $username = "root";
// $password = "";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $clave = $_POST['password'];

    if (empty($nombre) || empty($apellido) || empty($telefono) || empty($correo) || empty($clave)) {
        die("Todos los campos son obligatorios.");
    }


$check = $conexion->prepare("
    SELECT 'profesor' AS rol FROM profesor WHERE correo_institucional = :correo
    UNION
    SELECT 'estudiante' AS rol FROM estudiante WHERE correo_institucional = :correo
");
$check->execute([':correo' => $correo]);

if ($check->rowCount() > 0) {
    echo "<script>
    alert('El correo ya está registrado en el sistema (como docente o estudiante).');
    window.history.back();
    </script>";
    exit;
}

$clave_encriptada = password_hash($clave, PASSWORD_BCRYPT);

$sql = "INSERT INTO profesor (nombre, apellido, telefono, correo_institucional, contraseña)
VALUES (:nombre, :apellido, :telefono, :correo, :clave)";

$stmt = $conexion->prepare($sql);
$stmt->execute([
    ':nombre' => $nombre,
    ':apellido' => $apellido,
    ':telefono' => $telefono,
    ':correo' => $correo,
    ':clave' => $clave_encriptada
]);

echo "<script>
alert('Docente registrado exitosamente!');
window.location.href = 'login.php';
</script>";
exit;

} else {
    header("Location:registro_docente.php");
    exit;
}
?>

#hola chicos esta es una prueba de git
#hola chicasos
