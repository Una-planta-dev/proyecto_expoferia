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
        // 1. Incluimos el campo 'rol' en la consulta SQL
        $query = "SELECT id, correo, password, rol FROM usuarios WHERE correo = :correo";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $usuario['password'])) {
                // 2. Guardamos tanto usuario_id como id_profesor para compatibilidad
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['id_profesor'] = $usuario['id'];
                $_SESSION['rol'] = $usuario['rol'] ?? 'docente';

                // 3. Redirección según el rol o directa al panel del profesor
                if (isset($usuario['rol']) && $usuario['rol'] === 'alumno') {
                    header("Location: registro_alumno.php");
                } else {
                    header("Location: panel_profesor.php");
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

