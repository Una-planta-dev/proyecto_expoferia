<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    if (empty($correo) || empty($password)) {
        header("Location: login.php?error=campos_vacios");
        exit();
    }

    try {
        // 1. Agregamos 'rol' y 'nombre' a la consulta SQL
        $query = "SELECT id, nombre, correo, password, rol FROM usuarios WHERE correo = :correo";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Verificación de contraseña
            if (password_verify($password, $usuario['password'])) {
                
                // Guardar datos clave en la sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];

                // 3. Redirección condicional según el rol
                if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'docente') {
                    header("Location: panel_admin.php");
                } else {
                    header("Location: estudiante.php");
                }
                exit();

            } else {
                header("Location: login.php?error=credenciales_incorrectas");
                exit();
            }
        } else {
            header("Location: login.php?error=credenciales_incorrectas");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: login.php?error=error_sistema");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
