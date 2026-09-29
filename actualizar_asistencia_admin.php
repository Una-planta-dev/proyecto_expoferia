<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

// Validar que sea administrador
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'administrador') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asistencia_id = $_POST['asistencia_id'] ?? null;
    $nuevo_estado = $_POST['estado'] ?? null;
    $nuevo_comentario = $_POST['comentario'] ?? null;

    if ($asistencia_id && $nuevo_estado) {
        
        // UNIFICACIÓN DE ESTADOS: Si llega cualquier variante de justificado, lo estandarizamos
        $estado_limpio = trim($nuevo_estado);
        if (strcasecmp($estado_limpio, 'Justificada') === 0 || strcasecmp($estado_limpio, 'Justificado') === 0) {
            $estado_limpio = 'Justificado'; // O 'Justificada', el que prefieras usar globalmente
        }

        try {
            // CORREGIDO: Tabla 'asistencia' y columna 'id_asistencia'
            $sql = "UPDATE asistencia SET estado = :estado, observacion = :comentario WHERE id_asistencia = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':estado' => $estado_limpio,
                ':comentario' => $nuevo_comentario,
                ':id' => $asistencia_id
            ]);

            // Redirigir de vuelta al panel con éxito
            header("Location: panel_admin.php?exito=1");
            exit();
        } catch (PDOException $e) {
            echo "Error al actualizar: " . $e->getMessage();
        }
    }
}
?>