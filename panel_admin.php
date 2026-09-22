<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('America/El_Salvador');

// 1. Cargar la conexión a la base de datos
require_once 'conexion.php';

// Validación de sesión para admin
$id_user = $_SESSION['id_admin'] ?? $_SESSION['usuario_id'] ?? null;
if (!$id_user) {
    header("Location: login.php");
    exit();
}

$nombre_admin = $_SESSION['nombre'] ?? 'Director General';
$mensaje_exito = '';
$mensaje_error = '';

// Mantener pestaña activa y filtros
$pestana_activa = $_REQUEST['tab'] ?? 'asistencias';
$fecha_filtro = $_REQUEST['fecha'] ?? date('Y-m-d');

// --- PROCESAR ACCIONES POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // Dar de baja / Eliminar Docente
    if ($accion === 'baja_docente') {
        $id_docente = intval($_POST['id_docente'] ?? 0);
        try {
            $stmt = $conexion->prepare("DELETE FROM profesor WHERE id_profesor = ?");
            $stmt->execute([$id_docente]);
            $mensaje_exito = "Profesor eliminado correctamente.";
        } catch (PDOException $e) {
            $mensaje_error = "Error al eliminar el profesor: " . $e->getMessage();
        }
    }

    // Dar de baja / Eliminar Alumno
    if ($accion === 'baja_alumno') {
        $id_alumno = intval($_POST['id_estudiante'] ?? 0);
        try {
            $stmt = $conexion->prepare("DELETE FROM estudiante WHERE id_estudiante = ?");
            $stmt->execute([$id_alumno]);
            $mensaje_exito = "Alumno eliminado correctamente.";
        } catch (PDOException $e) {
            $mensaje_error = "Error al eliminar el alumno: " . $e->getMessage();
        }
    }

    // Modificar Asistencia
    if ($accion === 'modificar_asistencia') {
        $id_asistencia = intval($_POST['id_asistencia'] ?? 0);
        $nuevo_estado = $_POST['estado'] ?? 'A tiempo';
        $nueva_observacion = trim($_POST['observacion'] ?? '');

        try {
            $stmt = $conexion->prepare("UPDATE asistencia SET estado = ?, observacion = ? WHERE id_asistencia = ?");
            $resultado = $stmt->execute([$nuevo_estado, $nueva_observacion, $id_asistencia]);
            if ($resultado) {
                $mensaje_exito = "Asistencia actualizada correctamente.";
            } else {
                $mensaje_error = "No se pudo actualizar el registro de asistencia.";
            }
        } catch (PDOException $e) {
            $mensaje_error = "Error al actualizar la asistencia: " . $e->getMessage();
        }
    }
}

// --- CONSULTAS A LA BASE DE DATOS ---
try {
    $totalProfesores = $conexion->query("SELECT COUNT(*) FROM profesor")->fetchColumn();
    $totalAlumnos = $conexion->query("SELECT COUNT(*) FROM estudiante")->fetchColumn();
    
    $stmtHoy = $conexion->prepare("SELECT COUNT(*) FROM asistencia WHERE DATE(fecha_registro) = ?");
    $stmtHoy->execute([$fecha_filtro]);
    $totalAsistenciasHoy = $stmtHoy->fetchColumn();
} catch (PDOException $e) {
    $totalProfesores = $totalAlumnos = $totalAsistenciasHoy = 0;
}

// Consulta Asistencias
try {
    $sqlAsistencias = "SELECT 
                        a.id_asistencia, 
                        COALESCE(e.nombre, p.nombre, 'Usuario') AS nombre, 
                        COALESCE(e.apellido, p.apellido, '') AS apellido, 
                        a.hora_registro, 
                        COALESCE(NULLIF(a.estado, ''), 'A tiempo') AS estado, 
                        COALESCE(a.observacion, '') AS observacion 
                       FROM asistencia a 
                       LEFT JOIN estudiante e ON a.id_estudiante = e.id_estudiante 
                       LEFT JOIN profesor p ON a.id_profesor = p.id_profesor 
                       WHERE DATE(a.fecha_registro) = ? 
                       ORDER BY a.id_asistencia DESC";
    $stmtAsis = $conexion->prepare($sqlAsistencias);
    $stmtAsis->execute([$fecha_filtro]);
    $listaAsistencias = $stmtAsis->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $listaAsistencias = [];
}

// Consulta Alumnos
try {
    $stmtAlumnos = $conexion->query("SELECT * FROM estudiante ORDER BY nombre ASC");
    $listaAlumnos = $stmtAlumnos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $listaAlumnos = [];
}

// Consulta Docentes (Flexible para evitar fallos si cambia el nombre de la tabla)
try {
    $stmtProf = $conexion->query("SELECT * FROM profesor ORDER BY nombre ASC");
    $listaProfesores = $stmtProf->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    try {
        $stmtProf = $conexion->query("SELECT id_docente AS id_profesor, nombre, apellido, usuario FROM docentes ORDER BY nombre ASC");
        $listaProfesores = $stmtProf->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $listaProfesores = [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Dirección | SYNCA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f111a;
            color: #e2e8f0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }
        .navbar-admin {
            background-color: #1a1c29;
            border-bottom: 1px solid #2d3248;
            padding: 14px 28px;
        }
        .brand-logo {
            color: #a855f7;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: 1px;
        }
        .card-custom {
            background-color: #1a1c29;
            border: 1px solid #2d3248;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .banner-purple {
            background: linear-gradient(135deg, #6b21a8 0%, #3b0764 100%);
            border-radius: 12px;
            padding: 20px;
            color: #ffffff;
            margin-bottom: 20px;
        }
        .stat-card {
            background-color: #131522;
            border: 1px solid #2d3248;
            border-radius: 12px;
            padding: 20px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #c084fc;
        }
        .btn-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: #ffffff;
            border: none;
            font-weight: 600;
        }
        .btn-purple:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            color: #ffffff;
        }
        .btn-outline-custom {
            border: 1px solid #3b4261;
            color: #94a3b8;
            background: transparent;
        }
        .btn-outline-custom:hover {
            background-color: #272a3d;
            color: #ffffff;
        }
        .table-dark-custom thead th {
            background-color: #131522 !important;
            color: #a855f7 !important;
            border-bottom: 2px solid #2d3248 !important;
        }
        .table-dark-custom tbody td {
            background-color: transparent !important;
            color: #cbd5e1 !important;
            border-bottom: 1px solid #2d3248 !important;
            vertical-align: middle;
        }
        .text-subtle {
            color: #94a3b8 !important;
        }
        .nav-tabs {
            border-bottom: 1px solid #2d3248;
        }
        .nav-tabs .nav-link {
            color: #94a3b8;
            border: none;
            font-weight: 600;
        }
        .nav-tabs .nav-link.active {
            background-color: #1a1c29;
            color: #c084fc;
            border-bottom: 3px solid #8b5cf6;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar-admin d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="brand-logo">S Y N C A</span>
            <span class="text-subtle fs-6">| Panel de Dirección</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-light">🛡️ Admin: <b><?= htmlspecialchars($nombre_admin) ?></b></span>
            <a href="logout.php" class="btn btn-sm btn-outline-custom">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container-fluid py-4 px-4">
        
        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success bg-dark text-success border-success alert-dismissible fade show" role="alert">
                <?= $mensaje_exito ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($mensaje_error)): ?>
            <div class="alert alert-danger bg-dark text-danger border-danger alert-dismissible fade show" role="alert">
                <?= $mensaje_error ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Banner Superior -->
        <div class="banner-purple d-flex justify-content-between align-items-center shadow-lg">
            <div>
                <h4 class="fw-bold mb-1">Resumen General de Asistencia</h4>
                <p class="mb-0 text-light-50 small">Control directivo y supervisión general de la expoferia 2026.</p>
            </div>
            <span class="badge bg-dark border border-light text-light px-3 py-2">Fecha: <?= date('d/m/Y', strtotime($fecha_filtro)) ?></span>
        </div>

        <!-- Tarjetas de Estadísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="text-subtle small d-block mb-1">Total Docentes</span>
                    <span class="stat-number"><?= count($listaProfesores) ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="text-subtle small d-block mb-1">Matrícula Activa Alumnos</span>
                    <span class="stat-number"><?= count($listaAlumnos) ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="text-subtle small d-block mb-1">Asistencias el <?= date('d/m/Y', strtotime($fecha_filtro)) ?></span>
                    <span class="stat-number"><?= $totalAsistenciasHoy ?></span>
                </div>
            </div>
        </div>

        <!-- Pestañas de Navegación -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?= $pestana_activa === 'asistencias' ? 'active' : '' ?>" href="?tab=asistencias&fecha=<?= urlencode($fecha_filtro) ?>">📜 Control y Justificaciones</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $pestana_activa === 'alumnos' ? 'active' : '' ?>" href="?tab=alumnos">👨‍🎓 Gestión de Alumnos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $pestana_activa === 'profesores' ? 'active' : '' ?>" href="?tab=profesores">👨‍🏫 Gestión de Docentes</a>
            </li>
        </ul>

        <!-- PESTAÑA 1: CONTROL Y JUSTIFICACIONES -->
        <?php if ($pestana_activa === 'asistencias'): ?>
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-light mb-0">Gestión de Asistencias</h5>
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="tab" value="asistencias">
                    <input type="date" name="fecha" class="form-control form-control-sm bg-dark text-light border-secondary" value="<?= htmlspecialchars($fecha_filtro) ?>">
                    <button type="submit" class="btn btn-purple btn-sm">Filtrar</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-dark-custom align-middle">
                    <thead>
                        <tr>
                            <th>Alumno / Usuario</th>
                            <th>Hora Registro</th>
                            <th>Estado Actual</th>
                            <th>Justificación / Observación</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaAsistencias)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-subtle">No hay registros de asistencia para esta fecha.</td></tr>
                        <?php else: ?>
                            <?php foreach ($listaAsistencias as $row): 
                                $estadoActual = trim($row['estado']);
                            ?>
                            <tr>
                                <td class="fw-semibold text-light"><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?></td>
                                <td><span class="badge bg-dark border border-secondary"><?= htmlspecialchars($row['hora_registro']) ?></span></td>
                                <form method="POST" action="?tab=asistencias&fecha=<?= urlencode($fecha_filtro) ?>">
                                    <input type="hidden" name="accion" value="modificar_asistencia">
                                    <input type="hidden" name="id_asistencia" value="<?= $row['id_asistencia'] ?>">
                                    <input type="hidden" name="tab" value="asistencias">
                                    <input type="hidden" name="fecha" value="<?= htmlspecialchars($fecha_filtro) ?>">
                                    <td>
                                        <select name="estado" class="form-select form-select-sm bg-dark text-light border-secondary" style="width: 130px;">
                                            <option value="A tiempo" <?= strtolower($estadoActual) === 'a tiempo' ? 'selected' : '' ?>>🟢 A tiempo</option>
                                            <option value="Tarde" <?= strtolower($estadoActual) === 'tarde' ? 'selected' : '' ?>>🟡 Tarde</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="observacion" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Escribir nota..." value="<?= htmlspecialchars($row['observacion']) ?>">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-purple btn-sm">Guardar</button>
                                    </td>
                                </form>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- PESTAÑA 2: GESTIÓN DE ALUMNOS -->
        <?php if ($pestana_activa === 'alumnos'): ?>
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-light mb-0">Gestión de Alumnos</h5>
                <span class="badge bg-purple px-3 py-2 border border-secondary">Total: <?= count($listaAlumnos) ?> Alumnos</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-dark-custom align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Alumno</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaAlumnos)): ?>
                            <tr><td colspan="3" class="text-center py-4 text-subtle">No hay alumnos registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($listaAlumnos as $alumno): 
                                $idAlumno = $alumno['id_estudiante'] ?? $alumno['id'] ?? 0;
                                $nombreAlumno = trim(($alumno['nombre'] ?? '') . ' ' . ($alumno['apellido'] ?? ''));
                            ?>
                            <tr>
                                <td class="text-subtle">#<?= htmlspecialchars($idAlumno) ?></td>
                                <td class="fw-semibold text-light"><?= htmlspecialchars($nombreAlumno) ?></td>
                                <td class="text-end">
                                    <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este alumno?');" style="display:inline;">
                                        <input type="hidden" name="accion" value="baja_alumno">
                                        <input type="hidden" name="id_estudiante" value="<?= htmlspecialchars($idAlumno) ?>">
                                        <input type="hidden" name="tab" value="alumnos">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- PESTAÑA 3: GESTIÓN DE DOCENTES -->
        <?php if ($pestana_activa === 'profesores'): ?>
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold text-light mb-0">Gestión de Docentes</h5>
                <span class="badge bg-purple px-3 py-2 border border-secondary">Total: <?= count($listaProfesores) ?> Docentes</span>
            </div>

            <div class="table-responsive">
                <table class="table table-dark-custom align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Usuario / Correo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaProfesores)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-subtle">No hay profesores registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($listaProfesores as $prof): 
                                $idDocente = $prof['id_profesor'] ?? $prof['id'] ?? 0;
                                $nombreDocente = trim(($prof['nombre'] ?? '') . ' ' . ($prof['apellido'] ?? ''));
                                $userDocente = $prof['usuario'] ?? $prof['correo'] ?? 'Docente';
                            ?>
                            <tr>
                                <td class="text-subtle">#<?= htmlspecialchars($idDocente) ?></td>
                                <td class="fw-semibold text-light"><?= htmlspecialchars($nombreDocente) ?></td>
                                <td><span class="badge bg-dark border border-secondary text-light"><?= htmlspecialchars($userDocente) ?></span></td>
                                <td class="text-end">
                                    <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este docente?');" style="display:inline;">
                                        <input type="hidden" name="accion" value="baja_docente">
                                        <input type="hidden" name="id_docente" value="<?= htmlspecialchars($idDocente) ?>">
                                        <input type="hidden" name="tab" value="profesores">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>