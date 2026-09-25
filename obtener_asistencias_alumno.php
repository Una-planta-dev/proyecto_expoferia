<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once 'conexion.php';

// Validar que el estudiante haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id_estudiante = $_SESSION['usuario_id'];

try {
    // Unimos la tabla asistencia con profesor para obtener su nombre y apellido real
    $sql = "SELECT DATE_FORMAT(a.fecha_registro, '%d/%m/%Y') AS fecha, 
                   DATE_FORMAT(a.hora_registro, '%h:%i:%s %p') AS hora_registro, 
                   CONCAT('Prof. ', p.nombre, ' ', p.apellido) AS asignatura, 
                   'Presente' as estado 
            FROM asistencia a 
            LEFT JOIN profesor p ON a.id_profesor = p.id_profesor 
            WHERE a.id_estudiante = :id_estudiante 
            ORDER BY a.fecha_registro DESC, a.hora_registro DESC";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id_estudiante' => $id_estudiante]);
    
    echo json_encode([
        'success' => true, 
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?> 