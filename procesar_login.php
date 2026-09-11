<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($correo) || empty($password)) {
        echo "<script>alert('Por favor, llena todos los campos.'); window.location.href='login.php';</script>";
        exit();
    }

    try {
        // 1. BUSCAR EN ALUMNOS
        $stmt_est = $conexion->prepare("SELECT * FROM estudiante WHERE correo_institucional = :correo");
        $stmt_est->execute([':correo' => $correo]);
        $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

        if ($estudiante) {
            $pass_bd = $estudiante['contraseina'] ?? $estudiante['contraseña'] ?? $estudiante['password'] ?? '';

            if ($password === $pass_bd || password_verify($password, $pass_bd)) {
                // Guardar las claves exactas en la sesión
                $_SESSION['usuario_id'] = $estudiante['id_estudiante'];
                $_SESSION['nombre'] = $estudiante['nombre'] . ' ' . $estudiante['apellido'];
                $_SESSION['rol'] = 'alumno';

                header("Location: panel_alumno.php");
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='login.php';</script>";
                exit();
            }
        }

        // 2. BUSCAR EN PROFESORES
        $stmt_prof = $conexion->prepare("SELECT * FROM profesor WHERE correo_institucional = :correo");
        $stmt_prof->execute([':correo' => $correo]);
        $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);

        if ($profesor) {
            $pass_bd = $profesor['contraseña'] ?? $profesor['contraseina'] ?? $profesor['password'] ?? '';

            if ($password === $pass_bd || password_verify($password, $pass_bd)) {
                $_SESSION['usuario_id'] = $profesor['id_profesor'];
                $_SESSION['nombre'] = $profesor['nombre'] . ' ' . $profesor['apellido'];
                $_SESSION['rol'] = 'docente';

                header("Location: panel_profesor.php");
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='login.php';</script>";
                exit();
            }
        }

        // CORREO NO ENCONTRADO
        echo "<script>alert('El correo no se encuentra registrado.'); window.location.href='login.php';</script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Error BD: " . addslashes($e->getMessage()) . "'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>