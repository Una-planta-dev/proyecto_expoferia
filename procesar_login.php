<?php
ob_start();
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($correo) || empty($password)) {
        echo "<script>alert('Por favor completa todos los campos.'); window.location.href='login.php';</script>";
        exit();
    }

    // --- 🛡️ 0. VALIDACIÓN DE ADMINISTRADOR GENERAL ---
    if ($correo === 'admin@clases.edu.sv' && $password === '12345') {
        $_SESSION['id_admin'] = 1;
        $_SESSION['nombre'] = 'Director General';
        $_SESSION['rol'] = 'admin';

        session_write_close();
        echo "<script>window.location.href = 'panel_admin.php';</script>";
        exit();
    }

    try {
        // --- 1. BUSCAR EN ESTUDIANTES ---
        $query_est = "SELECT * FROM estudiante WHERE correo_institucional = :correo";
        $stmt_est = $conexion->prepare($query_est);
        $stmt_est->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt_est->execute();

        if ($stmt_est->rowCount() == 1) {
            $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);
            $pass_bd = $estudiante['contraseina'] ?? $estudiante['contraseña'] ?? '';

            if ($password === $pass_bd || password_verify($password, $pass_bd)) {
                // Variables de sesión completas para evitar rebotes
                $_SESSION['usuario_id'] = $estudiante['id_estudiante'];
                $_SESSION['id_estudiante'] = $estudiante['id_estudiante'];
                $_SESSION['id'] = $estudiante['id_estudiante'];
                $_SESSION['nombre'] = $estudiante['nombre'] . ' ' . $estudiante['apellido'];
                $_SESSION['rol'] = 'alumno';

                session_write_close();
                echo "<script>window.location.href = 'panel_alumno.php';</script>";
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='login.php';</script>";
                exit();
            }
        }

        // --- 2. BUSCAR EN PROFESORES ---
        $query_prof = "SELECT * FROM profesor WHERE correo_institucional = :correo";
        $stmt_prof = $conexion->prepare($query_prof);
        $stmt_prof->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt_prof->execute();

        if ($stmt_prof->rowCount() == 1) {
            $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);
            $pass_bd = $profesor['contraseña'] ?? $profesor['contraseina'] ?? '';

            if ($password === $pass_bd || password_verify($password, $pass_bd)) {
                // Variables de sesión completas
                $_SESSION['usuario_id'] = $profesor['id_profesor'];
                $_SESSION['id_profesor'] = $profesor['id_profesor'];
                $_SESSION['id'] = $profesor['id_profesor'];
                $_SESSION['nombre'] = $profesor['nombre'] . ' ' . $profesor['apellido'];
                $_SESSION['rol'] = 'docente';

                session_write_close();
                echo "<script>window.location.href = 'panel_profesor.php';</script>";
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='login.php';</script>";
                exit();
            }
        }

        // --- 3. SI NO EXISTE EN NINGUNA TABLA ---
        echo "<script>alert('El correo no se encuentra registrado.'); window.location.href='login.php';</script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Error en la base de datos: " . addslashes($e->getMessage()) . "'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>