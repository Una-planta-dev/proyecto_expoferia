<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('America/El_Salvador');
require_once 'conexion.php';

// Verificar que sea un alumno logueado
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_ingresado = trim($_POST['codigo_pin'] ?? '');
    $id_estudiante = $_SESSION['usuario_id'];

    if (empty($codigo_ingresado)) {
        echo "<script>alert('Por favor ingresa el código de asistencia.'); window.location='panel_alumno.php';</script>";
        exit();
    }

    try {
        // 1. Obtener todos los profesores registrados para verificar el código de 5 minutos
        $stmtProf = $conexion->query("SELECT id_profesor FROM profesor");
        $profesores = $stmtProf->fetchAll(PDO::FETCH_ASSOC);

        $id_profesor_encontrado = null;
        $tiempo_actual = time();

        $bloques_a_probar = [
            floor($tiempo_actual / 300),
            floor($tiempo_actual / 300) - 1
        ];

        foreach ($profesores as $prof) {
            $id_prof = $prof['id_profesor'];
            
            foreach ($bloques_a_probar as $bloque) {
                $semilla = $id_prof . '_' . $bloque;
                $codigo_hash = strtoupper(substr(md5($semilla), 0, 6)); 
                $codigo_manual_generado = "SYN-" . date('Ymd') . "-" . $codigo_hash;

                if (strcasecmp($codigo_ingresado, $codigo_manual_generado) === 0) {
                    $id_profesor_encontrado = $id_prof;
                    break 2;
                }
            }
        }

        if (!$id_profesor_encontrado) {
            echo "<script>alert('Error: El código ingresado es incorrecto o ya expiró.'); window.location='panel_alumno.php';</script>";
            exit();
        }

        // 2. Buscar la clase asociada a este profesor
        $stmtClase = $conexion->prepare("
            SELECT c.id_clase FROM clase c 
            INNER JOIN asignacion a ON c.id_asignacion = a.id_asignacion 
            WHERE a.id_profesor = :id_profesor 
            ORDER BY c.id_clase DESC LIMIT 1
        ");
        $stmtClase->execute([':id_profesor' => $id_profesor_encontrado]);
        $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);

        $id_clase = null;
        if ($clase) {
            $id_clase = $clase['id_clase'];
        } else {
            $stmtFallback = $conexion->query("SELECT id_clase FROM clase ORDER BY id_clase DESC LIMIT 1");
            $fallbackClase = $stmtFallback->fetch(PDO::FETCH_ASSOC);
            if ($fallbackClase) {
                $id_clase = $fallbackClase['id_clase'];
            } else {
                $conexion->exec("INSERT INTO clase (fecha, hora_inicio, hora_fin) VALUES (CURDATE(), '07:00:00', '12:00:00')");
                $id_clase = $conexion->lastInsertId();
            }
        }

        $fecha_hoy = date('Y-m-d');

        // 3. Intentar registrar la asistencia (Si ya existe debido al índice único, lo atrapamos limpiamente)
        try {
            $insert = $conexion->prepare("
                INSERT INTO asistencia (id_estudiante, id_clase, fecha_registro, hora_registro, estado) 
                VALUES (:id_estudiante, :id_clase, CURDATE(), CURTIME(), 'Presente')
            ");
            
            $insert->execute([
                ':id_estudiante' => $id_estudiante,
                ':id_clase' => $id_clase
            ]);

            echo "<script>alert('¡Asistencia registrada exitosamente!'); window.location='panel_alumno.php';</script>";
            exit();

        } catch (PDOException $e) {
            // Si el código de error es 1062 (Entrada duplicada por la llave única)
            if ($e->getCode() == '23000' || strpos($e->getMessage(), '1062') !== false) {
                echo "<script>alert('Ya has registrado tu asistencia anteriormente.'); window.location='panel_alumno.php';</script>";
                exit();
            } else {
                throw $e; // Si es otro error de base de datos, lo relanzamos
            }
        }

    } catch (Exception $e) {
        echo "<script>alert('Error en el sistema: " . addslashes($e->getMessage()) . "'); window.location='panel_alumno.php';</script>";
        exit();
    }
} else {
    header("Location: panel_alumno.php");
    exit();
}

