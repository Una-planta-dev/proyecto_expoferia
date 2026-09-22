<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');

    if (!empty($correo)) {
        // Buscar si el correo existe en estudiante o profesor
        $stmtEst = $conexion->prepare("SELECT id_estudiante, 'estudiante' AS tipo FROM estudiante WHERE correo_institucional = :correo");
        $stmtEst->execute([':correo' => $correo]);
        $usuario = $stmtEst->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $stmtProf = $conexion->prepare("SELECT id_profesor, 'profesor' AS tipo FROM profesor WHERE correo_institucional = :correo");
            $stmtProf->execute([':correo' => $correo]);
            $usuario = $stmtProf->fetch(PDO::FETCH_ASSOC);
        }

        if ($usuario) {
            // Generar código de 5 dígitos
            $codigoVerificacion = rand(10000, 99999);

            // Guardar datos en la sesión para la verificación posterior
            $_SESSION['codigo_recuperacion'] = $codigoVerificacion;
            $_SESSION['correo_recuperacion'] = $correo;
            $_SESSION['tipo_usuario'] = $usuario['tipo']; // "estudiante" o "profesor"

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'syncacr2026@gmail.com';     // Tu correo emisor
                $mail->Password   = 'expoferia2026';  // Clave de aplicación de 16 caracteres
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('syncacr2026@gmail.com', 'Sistema Synca');
                $mail->addAddress($correo);

                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Código de Recuperación - Synca';
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                        <h2>Sistema Synca - Recuperación de Contraseña</h2>
                        <p>Has solicitado restablecer tu contraseña. Ingresa el siguiente código de 5 dígitos:</p>
                        <div style='background-color: #6f42c1; color: white; font-size: 28px; font-weight: bold; letter-spacing: 5px; padding: 15px; text-align: center; border-radius: 8px; width: 200px; margin: 20px 0;'>
                            $codigoVerificacion
                        </div>
                        <p>Si no solicitaste este cambio, ignora este correo.</p>
                    </div>
                ";

                $mail->send();
                header('Location: restablecer.php');
                exit();

            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
            }
        } else {
            echo "El correo institucional no está registrado en el sistema.";
        }
    } else {
        echo "Por favor ingresa un correo válido.";
    }
}
?>