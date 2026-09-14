<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once 'conexion.php';

// Verificar que sea un profesor logueado
if (!isset($_SESSION['id_profesor'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_asistencia = $_POST['id_asistencia'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $observacion = $_POST['observacion'] ?? '';

    if (!$id_asistencia || !$estado) {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        exit();
    }

    try {
        // Actualizar el estado y la observación en la base de datos
        $stmt = $conexion->prepare("
            UPDATE asistencia 
            SET estado = :estado, observacion = :observacion 
            WHERE id_asistencia = :id_asistencia
        ");
        $stmt->execute([
            ':estado' => $estado,
            ':observacion' => $observacion,
            ':id_asistencia' => $id_asistencia
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no válido']);
}
?>