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
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $nie = trim($_POST['nie'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['password'] ?? '';

    if (empty($nombre) || empty($apellido) || empty($nie) || empty($correo) || empty($clave)) {
        echo "<script>
        alert('Por favor completa todos los campos, incluida la contraseña.');
        window.history.back();
        </script>";
        exit;
    }

 // Validar que el correo no esté registrado ni como docente ni como estudiante, o que el NIE ya exista
$check = $conexion->prepare("
    SELECT 'profesor' AS rol FROM profesor WHERE correo_institucional = :correo
    UNION
    SELECT 'estudiante' AS rol FROM estudiante WHERE correo_institucional = :correo OR nie = :nie
");
$check->execute([':correo' => $correo, ':nie' => $nie]);

if ($check->rowCount() > 0) {
    echo "<script>
        alert('El correo o el NIE ya se encuentran registrados en el sistema.');
        window.history.back();
    </script>";
    exit;
}

    $clave_encriptada = password_hash($clave, PASSWORD_BCRYPT);

    $sql = "INSERT INTO estudiante (nombre, apellido, nie, correo_institucional, contraseina)
            VALUES (:nombre, :apellido, :nie, :correo, :clave)";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':nie' => $nie,
        ':correo' => $correo,
        ':clave' => $clave_encriptada
    ]);

    echo "<script>
    alert('¡Alumno registrado exitosamente!');
    window.location.href = 'login.php';
    </script>";
    exit;

} else {
    header("Location: registro_alumno.php");
    exit;
}
?>