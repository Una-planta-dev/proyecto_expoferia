<?php
// 1. Iniciar búfer y sesión
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

    try {
        // --- PASO 1: BUSCAR EN LA TABLA DE ESTUDIANTES ---
        $query_est = "SELECT id_estudiante, nombre, apellido, correo_institucional, contraseina FROM estudiante WHERE correo_institucional = :correo";
        $stmt_est = $conexion->prepare($query_est);
        $stmt_est->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt_est->execute();

        if ($stmt_est->rowCount() == 1) {
            $estudiante = $stmt_est->fetch(PDO::FETCH_ASSOC);

            if ($password === $estudiante['contraseina'] || password_verify($password, $estudiante['contraseina'])) {
                $_SESSION['usuario_id'] = $estudiante['id_estudiante'];
                $_SESSION['id_estudiante'] = $estudiante['id_estudiante'];
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

        // --- PASO 2: SI NO ES ESTUDIANTE, BUSCAR EN LA TABLA DE PROFESORES ---
        $query_prof = "SELECT id_profesor, nombre, apellido, correo_institucional, contraseña FROM profesor WHERE correo_institucional = :correo";
        $stmt_prof = $conexion->prepare($query_prof);
        $stmt_prof->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt_prof->execute();

        if ($stmt_prof->rowCount() == 1) {
            $profesor = $stmt_prof->fetch(PDO::FETCH_ASSOC);

            if ($password === $profesor['contraseña'] || password_verify($password, $profesor['contraseña'])) {
                $_SESSION['usuario_id'] = $profesor['id_profesor'];
                $_SESSION['id_profesor'] = $profesor['id_profesor'];
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

        // --- PASO 3: SI NO SE ENCONTRÓ EN NINGUNA DE LAS DOS TABLAS ---
        echo "<script>alert('El correo no se encuentra registrado.'); window.location.href='login.php';</script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Error en el sistema: " . addslashes($e->getMessage()) . "'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>