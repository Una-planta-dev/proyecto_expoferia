<?php
session_start();
header('Content-Type: application/json');
require_once 'conexion.php';

// Verificar que el profesor haya iniciado sesión
if (!isset($_SESSION['id_profesor'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id_profesor = $_SESSION['id_profesor'];

try {
    // Seleccionamos grado y sección por separado, y también el id_asistencia y observación
    $sql = "SELECT 
                a.id_asistencia,
                e.nombre, 
                e.apellido, 
                s.grado AS grado, 
                s.seccion AS seccion, 
                DATE_FORMAT(a.hora_registro, '%h:%i:%s %p') AS hora_registro, 
                a.estado,
                a.observacion
            FROM asistencia a 
            INNER JOIN estudiante e ON a.id_estudiante = e.id_estudiante 
            LEFT JOIN matricula m ON e.id_estudiante = m.id_estudiante 
            LEFT JOIN seccion s ON m.id_seccion = s.id_seccion 
            WHERE DATE(a.fecha_registro) = CURDATE() 
            ORDER BY a.hora_registro DESC";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    
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