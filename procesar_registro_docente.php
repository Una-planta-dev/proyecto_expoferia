<<<<<<< HEAD
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
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $clave = $_POST['password'];

    if (empty($nombre) || empty($apellido) || empty($telefono) || empty($correo) || empty($clave)) {
        die("Todos los campos son obligatorios.");
    }


$check = $conexion->prepare("SELECT id_profesor FROM profesor WHERE correo_institucional = :correo");
$check ->execute([':correo' => $correo]);

if ($check->rowCount() > 0) {
    echo "<script>
    alert('El correo ya esta registrado.');
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
=======
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
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $clave = $_POST['password'];

    if (empty($nombre) || empty($apellido) || empty($telefono) || empty($correo) || empty($clave)) {
        die("Todos los campos son obligatorios.");
    }


$check = $conexion->prepare("SELECT id_profesor FROM profesor WHERE correo_institucional = :correo");
$check ->execute([':correo' => $correo]);

if ($check->rowCount() > 0) {
    echo "<script>
    alert('El correo ya esta registrado.');
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
>>>>>>> f7028d993f5ab001964b9e75507ad411aa3842f6
?>