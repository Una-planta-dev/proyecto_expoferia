<?php
session_start();
require_once 'conexion.php';

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigoIngresado = trim($_POST['codigo'] ?? '');
    $nuevaClave = $_POST['nueva_clave'] ?? '';

    if (isset($_SESSION['codigo_recuperacion']) && $codigoIngresado == $_SESSION['codigo_recuperacion']) {
        $correo = $_SESSION['correo_recuperacion'];
        $tipo = $_SESSION['tipo_usuario'];
        $claveHash = password_hash($nuevaClave, PASSWORD_BCRYPT);

        if ($tipo === 'estudiante') {
            // Actualiza la columna 'contraseina' en estudiante
            $stmt = $conexion->prepare("UPDATE estudiante SET contraseina = :clave WHERE correo_institucional = :correo");
            $stmt->execute([':clave' => $claveHash, ':correo' => $correo]);
        } else if ($tipo === 'profesor') {
            // Actualiza la columna 'contraseña' en profesor
            $stmt = $conexion->prepare("UPDATE profesor SET contraseña = :clave WHERE correo_institucional = :correo");
            $stmt->execute([':clave' => $claveHash, ':correo' => $correo]);
        }

        // Limpiar la sesión de recuperación
        unset($_SESSION['codigo_recuperacion'], $_SESSION['correo_recuperacion'], $_SESSION['tipo_usuario']);

        echo "<script>alert('¡Contraseña actualizada con éxito!'); window.location.href='login.php';</script>";
        exit();
    } else {
        $mensajeError = "El código ingresado es incorrecto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Verificar Código - Synca</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
        <h2>Verificación de Código</h2>
        <p>Ingresa el código de 5 dígitos enviado a tu correo institucional.</p>

        <?php if (!empty($mensajeError)): ?>
            <p style="color: red;"><?php echo $mensajeError; ?></p>
        <?php endif; ?>

        <form action="restablecer.php" method="POST">
            <label for="codigo">Código de 5 dígitos:</label>
            <input type="text" name="codigo" id="codigo" maxlength="5" placeholder="Ej: 58219" required>

            <label for="nueva_clave">Nueva Contraseña:</label>
            <input type="password" name="nueva_clave" id="nueva_clave" placeholder="Escribe tu nueva clave" required minlength="6">

            <button type="submit">Actualizar Contraseña</button>
        </form>
    </div>
</body>
</html>