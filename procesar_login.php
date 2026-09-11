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
        // Consulta usando los datos reales de la BD
        $query = "SELECT id_profesor, nombre, apellido, correo_institucional, contraseña FROM profesor WHERE correo_institucional = :correo";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $profesor = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar la contraseña (texto plano o hash)
            if ($password === $profesor['contraseña'] || password_verify($password, $profesor['contraseña'])) {
                
                // Asignar variables de sesión
                $_SESSION['usuario_id'] = $profesor['id_profesor'];
                $_SESSION['id_profesor'] = $profesor['id_profesor'];
                $_SESSION['nombre'] = $profesor['nombre'] . ' ' . $profesor['apellido'];
                $_SESSION['rol'] = 'docente';

                session_write_close();

                // Redirección inmediata por JavaScript (salta cualquier bloqueo de cabeceras)
                echo "<script>window.location.href = 'panel_profesor.php';</script>";
                exit();
            } else {
                echo "<script>alert('Contraseña incorrecta.'); window.location.href='login.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('El correo no se encuentra registrado.'); window.location.href='login.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error en el sistema: " . addslashes($e->getMessage()) . "'); window.location.href='login.php';</script>";
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}