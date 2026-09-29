<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_profesor'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_asistencia = $_POST['id_asistencia'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $observacion = $_POST['observacion'] ?? '';

    if ($id_asistencia && $estado) {
        try {
            $sql = "UPDATE asistencia SET estado = :estado, observacion = :observacion WHERE id_asistencia = :id_asistencia";
            $stmt = $conexion->prepare($sql);
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
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    }
}
?>