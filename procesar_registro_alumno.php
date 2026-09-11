<?php
$host = "localhost";
$usuario = "root";
$password = "";
$database = "proyecto_expoferia";

try {
    $conexion = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $usuario, $password);
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

    $check = $conexion->prepare("SELECT id_estudiante FROM estudiante WHERE correo_institucional = :correo OR nie = :nie");
    $check->execute([':correo' => $correo, ':nie' => $nie]);

    if ($check->rowCount() > 0) {
        echo "<script>
        alert('El correo o NIE ya están registrados.');
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