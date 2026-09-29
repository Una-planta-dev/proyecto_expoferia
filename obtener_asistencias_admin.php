<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

try {
    // Consulta corregida para traer el estado real de la base de datos
    $sql = "SELECT 
                a.id_asistencia,
                a.hora_registro,
                a.fecha_registro,
                COALESCE(NULLIF(a.estado, ''), 'A tiempo') AS estado,
                COALESCE(a.observacion, '') AS observacion,
                COALESCE(e.nombre, p.nombre, 'Usuario') AS nombre,
                COALESCE(e.apellido, p.apellido, '') AS apellido,
                COALESCE(s.grado, 'Segundo Año') AS grado,
                COALESCE(s.seccion, 'Software') AS seccion
            FROM asistencia a
            LEFT JOIN estudiante e ON a.id_estudiante = e.id_estudiante
            LEFT JOIN profesor p ON a.id_profesor = p.id_profesor
            LEFT JOIN matricula m ON e.id_estudiante = m.id_estudiante 
            LEFT JOIN seccion s ON m.id_seccion = s.id_seccion 
            ORDER BY a.id_asistencia DESC
            LIMIT 50";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'total' => count($asistencias),
        'data' => $asistencias
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>