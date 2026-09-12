<?php
header('Content-Type: application/json');
require_once 'conexion.php';

try {
    // Usamos DATE_FORMAT para convertir la hora a formato de 12 horas con AM/PM
    $sql = "SELECT e.nombre, e.apellido, CONCAT(s.grado, ' ', s.seccion) AS grado_seccion, 
                DATE_FORMAT(a.hora_registro, '%h:%i:%s %p') AS hora_registro, 'Presente' as estado 
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