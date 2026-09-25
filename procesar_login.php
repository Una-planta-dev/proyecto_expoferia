<?php
ob_start();
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($correo) || empty($password)) {
        echo "<script>alert('Por favor completa todos los campos.'); window.location.href='login_docente.php';</script>";
        exit();
    }

    try {
        // CONSULTA EXCLUSIVA A LA TABLA PROFESOR
        $query_prof = "SELECT * FROM profesor WHERE correo_institucional = :correo";
        $stmt_prof = $conexion->prepare($query_prof);
        $stmt_prof->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt_prof->execute();

        if ($stmt_prof->rowCount() == 1) {
            $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);
            $pass_bd = $profesor['contraseña'] ?? $profesor['contraseina'] ?? '';

            // Verificar contraseña (soporta texto plano o hash de password_verify)
            if ($password === $pass_bd || password_verify($password, $pass_bd)) {
                
                // Variables de sesión exclusivas del docente
                $_SESSION['usuario_id'] = $profesor['id_profesor'];
                $_SESSION['id_profesor'] = $profesor['id_profesor'];
                $_SESSION['id'] = $profesor['id_profesor'];
                $_SESSION['nombre'] = $profesor['nombre'] . ' ' . $profesor['apellido'];
                $_SESSION['rol'] = 'docente';

                session_write_close();
                echo "<script>window.location.href = 'panel_profesor.php';</script>";
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='login_docente.php';</script>";
                exit();
            }
        } else {
            // Si el correo pertenece a un alumno o no existe en profesores, se deniega el acceso aquí
            echo "<script>alert('Acceso denegado. Este portal es exclusivo para docentes.'); window.location.href='login_docente.php';</script>";
            exit();
        }

    } catch (PDOException $e) {
        echo "<script>alert('Error en la base de datos: " . addslashes($e->getMessage()) . "'); window.location.href='login_docente.php';</script>";
        exit();
    }
} else {
    header("Location: login_docente.php");
    exit();
}
?>