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
        // Buscar si el correo pertenece a un estudiante
        $stmtEst = $conexion->prepare("SELECT id_estudiante, 'estudiante' AS tipo FROM estudiante WHERE correo_institucional = :correo");
        $stmtEst->execute([':correo' => $correo]);
        $usuario = $stmtEst->fetch(PDO::FETCH_ASSOC);

        // Si no es estudiante, buscar si pertenece a un profesor
        if (!$usuario) {
            $stmtProf = $conexion->prepare("SELECT id_profesor, 'profesor' AS tipo FROM profesor WHERE correo_institucional = :correo");
            $stmtProf->execute([':correo' => $correo]);
            $usuario = $stmtProf->fetch(PDO::FETCH_ASSOC);
        }

        if ($usuario) {
            // Generar código de 5 dígitos aleatorio
            $codigoVerificacion = rand(10000, 99999);

            // Guardar variables necesarias en la sesión
            $_SESSION['codigo_recuperacion'] = $codigoVerificacion;
            $_SESSION['correo_recuperacion'] = $correo;
            $_SESSION['tipo_usuario'] = $usuario['tipo'];

            $mail = new PHPMailer(true);

            try {
                // Configuración del servidor SMTP (Ajusta con tus credenciales)
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = ' syncacr2026@gmail.com'; // Correo emisor activo
                $mail->Password   = 'kmkq crnx ofvn cfdi'; // Contraseña de Aplicación de 16 letras
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Remitente y destinatario
                $mail->setFrom(' syncacr2026@gmail.com', 'Sistema Synca');
                $mail->addAddress($correo);

                // Contenido del mensaje HTML
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Código de Recuperación - Synca';
                $mail->Body    = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                        <h2>Sistema Synca - Recuperación de Contraseña</h2>
                        <p>Ingresa el siguiente código de 5 dígitos para restablecer tu contraseña:</p>
                        <div style='background-color: #6f42c1; color: white; font-size: 28px; font-weight: bold; letter-spacing: 5px; padding: 15px; text-align: center; border-radius: 8px; width: 200px; margin: 20px 0;'>
                            $codigoVerificacion
                        </div>
                        <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>
                    </div>
                ";

                $mail->send();
                header('Location: restablecer.php');
                exit();

            } catch (Exception $e) {
                echo "Error al enviar el correo: {$mail->ErrorInfo}";
            }
        } else {
            echo "El correo institucional no se encuentra registrado.";
        }
    } else {
        echo "Ingresa un correo válido.";
    }
}
?>