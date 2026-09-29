<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');

require_once 'conexion.php';

// Verificar que el usuario sea un profesor
if (!isset($_SESSION['id_profesor'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_asistencia = $_POST['id_asistencia'] ?? null;
    $estado = $_POST['estado'] ?? null;
    $observacion = $_POST['observacion'] ?? '';

    // Validar datos mínimos
    if (!$id_asistencia || !$estado) {
        echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios.']);
        exit();
    }

    // Validar estados permitidos para evitar inyecciones o valores inválidos
    $estados_validos = ['Presente', 'Tarde', 'Injustificada', 'Justificado'];
    if (!in_array($estado, $estados_validos)) {
        echo json_encode(['success' => false, 'error' => 'Estado no válido.']);
        exit();
    }

    try {
        $sql = "UPDATE asistencia 
                SET estado = :estado, observacion = :observacion 
                WHERE id_asistencia = :id_asistencia";
        
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
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
}