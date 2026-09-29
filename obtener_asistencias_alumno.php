<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');
require_once 'conexion.php';

// Validar que el estudiante haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$id_estudiante = $_SESSION['usuario_id'];

try {
    $sql = "SELECT 
                DATE_FORMAT(a.fecha_registro, '%d/%m/%Y') AS fecha, 
                DATE_FORMAT(a.hora_registro, '%h:%i:%s %p') AS hora_registro, 
                COALESCE(CONCAT('Prof. ', p.nombre, ' ', p.apellido), 'Docente de Turno') AS asignatura, 
                a.estado, 
                a.observacion AS comentario 
            FROM asistencia a 
            LEFT JOIN profesor p ON a.id_profesor = p.id_profesor 
            WHERE a.id_estudiante = :id_estudiante 
            ORDER BY a.fecha_registro DESC, a.hora_registro DESC";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id_estudiante' => $id_estudiante]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar los estados para inyectarles los colores exactos desde el servidor
    $data_procesada = [];
    foreach ($registros as $row) {
        $estRaw = trim($row['estado'] ?? 'Presente');
        $est = strtolower($estRaw);
        
        $badgeStyle = '';
        $icono = '🟢';
        $textoMostrar = $estRaw;

        if (str_contains($est, 'tarde')) {
            $badgeStyle = 'background: rgba(234, 179, 8, 0.15); color: #facc15; border: 1px solid #eab308;';
            $icono = '🟡';
        } elseif (str_contains($est, 'presente') || str_contains($est, 'a tiempo')) {
            $badgeStyle = 'background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid #22c55e;';
            $icono = '🟢';
            $textoMostrar = 'Presente';
        } elseif (str_contains($est, 'justificado')) {
            $badgeStyle = 'background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid #3b82f6;';
            $icono = '🔵';
        } else {
            // Injustificada u otros -> ROJO
            $badgeStyle = 'background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid #ef4444;';
            $icono = '🔴';
            $textoMostrar = 'Injustificada';
        }

        $row['badge_style'] = $badgeStyle;
        $row['icono'] = $icono;
        $row['texto_estado'] = $textoMostrar;

        $data_procesada[] = $row;
    }
    
    echo json_encode([
        'success' => true, 
        'data' => $data_procesada
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
?>