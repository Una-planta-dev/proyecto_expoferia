<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('America/El_Salvador');
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

// --- PROCESAR ACCIONES POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // 1. Dar de baja / Eliminar Docente
    if ($accion === 'baja_docente') {
        $id_docente = intval($_POST['id_docente'] ?? 0);
        try {
            $stmt = $conexion->prepare("DELETE FROM profesor WHERE id_profesor = ?");
            $stmt->execute([$id_docente]);
            $mensaje_exito = "Profesor dado de baja correctamente.";
        } catch (PDOException $e) {
            $mensaje_error = "No se pudo eliminar al profesor.";
        }
    }

    // 2. Dar de baja / Eliminar Alumno
    if ($accion === 'baja_alumno') {
        $id_alumno = intval($_POST['id_estudiante'] ?? 0);
        try {
            $stmt = $conexion->prepare("DELETE FROM estudiante WHERE id_estudiante = ?");
            $stmt->execute([$id_alumno]);
            $mensaje_exito = "Alumno dado de baja correctamente.";
        } catch (PDOException $e) {
            $mensaje_error = "No se pudo eliminar al alumno.";
        }
    }

    // 3. Modificar Estado y Observación de Asistencia
    if ($accion === 'modificar_asistencia') {
        $id_asistencia = intval($_POST['id_asistencia'] ?? 0);
        $nuevo_estado = $_POST['estado'] ?? 'A tiempo';
        $nueva_observacion = trim($_POST['observacion'] ?? '');

        try {
            $stmt = $conexion->prepare("UPDATE asistencia SET estado = ?, observacion = ? WHERE id_asistencia = ?");
            $stmt->execute([$nuevo_estado, $nueva_observacion, $id_asistencia]);
            $mensaje_exito = "Asistencia y observaciones actualizadas.";
        } catch (PDOException $e) {
            $mensaje_error = "Error al actualizar la asistencia.";
        }
    }
}

// --- FILTROS Y DATOS ---
$seccion_filtro = $_GET['seccion'] ?? '';
$fecha_filtro = $_GET['fecha'] ?? date('Y-m-d');
$pestana_activa = $_GET['tab'] ?? 'asistencias';

// Estadísticas rápidas
try {
    $totalProfesores = $conexion->query("SELECT COUNT(*) FROM profesor")->fetchColumn();
    $totalAlumnos = $conexion->query("SELECT COUNT(*) FROM estudiante")->fetchColumn();
    $totalAsistenciasHoy = $conexion->query("SELECT COUNT(*) FROM asistencia WHERE DATE(fecha_registro) = CURDATE()")->fetchColumn();
} catch (PDOException $e) {
    $totalProfesores = $totalAlumnos = $totalAsistenciasHoy = 0;
}

// Secciones disponibles
try {
    $secciones = $conexion->query("SELECT DISTINCT seccion FROM estudiante WHERE seccion IS NOT NULL AND seccion != '' ORDER BY seccion")->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $secciones = [];
}

// Lista de asistencias filtradas
$sqlAsistencias = "SELECT a.id_asistencia, e.nombre, e.apellido, e.grado, e.seccion, a.hora_registro, a.estado, a.observacion 
                   FROM asistencia a 
                   JOIN estudiante e ON a.id_estudiante = e.id_estudiante 
                   WHERE DATE(a.fecha_registro) = ?";
$paramsAsistencias = [$fecha_filtro];

if (!empty($seccion_filtro)) {
    $sqlAsistencias .= " AND e.seccion = ?";
    $paramsAsistencias[] = $seccion_filtro;
}
$sqlAsistencias .= " ORDER BY a.hora_registro DESC";

try {
    $stmtAsis = $conexion->prepare($sqlAsistencias);
    $stmtAsis->execute($paramsAsistencias);
    $listaAsistencias = $stmtAsis->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $listaAsistencias = [];
}

// Listas de usuarios
try {
    $listaAlumnos = $conexion->query("SELECT id_estudiante, nombre, apellido, grado, seccion FROM estudiante ORDER BY apellido ASC")->fetchAll(PDO::FETCH_ASSOC);
    $listaProfesores = $conexion->query("SELECT id_profesor, nombre, apellido, usuario FROM profesor ORDER BY apellido ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $listaAlumnos = $listaProfesores = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Dirección | SYNCA</title>
    <link rel="icon" type="image/png" href="logo-removebg-preview.png">
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($mensaje_error)): ?>
            <div class="alert alert-danger bg-dark text-danger border-danger alert-dismissible fade show" role="alert">
                <?= $mensaje_error ?>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Banner Superior -->
        <div class="banner-purple d-flex justify-content-between align-items-center shadow-lg">
            <div>
                <h4 class="fw-bold mb-1">Resumen General de Asistencia</h4>
                <p class="mb-0 text-light-50 small">Control directivo y supervisión general de la expoferia 2026.</p>
            </div>
            <span class="badge bg-dark border border-light text-light px-3 py-2">Fecha: <?= date('d/m/Y') ?></span>
        </div>

        <!-- Estadísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="text-subtle small d-block mb-1">Total Docentes</span>
                    <span class="stat-number"><?= $totalProfesores ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="text-subtle small d-block mb-1">Matrícula Activa Alumnos</span>
                    <span class="stat-number"><?= $totalAlumnos ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <span class="text-subtle small d-block mb-1">Asistencias Hoy</span>
                    <span class="stat-number"><?= $totalAsistenciasHoy ?></span>
                </div>
            </div>
        </div>

        <!-- Pestañas -->
        <ul class="nav nav-tabs mb-4 border-bottom border-secondary">
            <li class="nav-item">
                <a class="nav-link <?= $pestana_activa === 'asistencias' ? 'active' : '' ?>" href="?tab=asistencias">📋 Control y Justificaciones</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $pestana_activa === 'alumnos' ? 'active' : '' ?>" href="?tab=alumnos">👨‍🎓 Gestión de Alumnos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $pestana_activa === 'profesores' ? 'active' : '' ?>" href="?tab=profesores">👨‍🏫 Gestión de Docentes</a>
            </li>
        </ul>

        <!-- PESTAÑA 1: ASISTENCIAS Y SECCIONES -->
        <?php if ($pestana_activa === 'asistencias'): ?>
        <div class="card card-custom p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <h5 class="fw-bold text-light mb-0">Gestión de Asistencias y Observaciones por Sección</h5>
                
                <form method="GET" class="d-flex gap-2 align-items-center">
                    <input type="hidden" name="tab" value="asistencias">
                    <input type="date" name="fecha" class="form-control form-control-sm bg-dark text-light border-secondary" value="<?= htmlspecialchars($fecha_filtro) ?>">
                    
                    <select name="seccion" class="form-select form-select-sm bg-dark text-light border-secondary">
                        <option value="">Todas las Secciones</option>
                        <?php foreach ($secciones as $sec): ?>
                            <option value="<?= htmlspecialchars($sec) ?>" <?= $seccion_filtro === $sec ? 'selected' : '' ?>>Sección: <?= htmlspecialchars($sec) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn btn-purple btn-sm">Filtrar</button>
                    <a href="panel_admin.php?tab=asistencias" class="btn btn-outline-custom btn-sm">Limpiar</a>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-dark-custom align-middle">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Especialidad / Sección</th>
                            <th>Hora</th>
                            <th>Estado Actual</th>
                            <th>Justificación / Nota</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($listaAsistencias)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-subtle">No hay registros de asistencia para esta fecha o sección.</td></tr>
                        <?php else: ?>
                            <?php foreach ($listaAsistencias as $row): ?>
                            <tr>
                                <td class="fw-semibold text-light"><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellido']) ?></td>
                                <td>
                                    <?= htmlspecialchars($row['grado']) ?> 
                                    <span class="badge bg-secondary"><?= htmlspecialchars($row['seccion']) ?></span>
                                </td>
                                <td><span class="badge bg-dark border border-secondary"><?= htmlspecialchars($row['hora_registro']) ?></span></td>
                                
                                <form method="POST">
                                    <input type="hidden" name="accion" value="modificar_asistencia">
                                    <input type="hidden" name="id_asistencia" value="<?= $row['id_asistencia'] ?>">
                                    
                                    <td style="width: 150px;">
                                        <select name="estado" class="form-select form-select-sm bg-dark text-light border-secondary">
                                            <option value="A tiempo" <?= $row['estado'] === 'A tiempo' ? 'selected' : '' ?>>🟢 A tiempo</option>
                                            <option value="Tarde" <?= $row['estado'] === 'Tarde' ? 'selected' : '' ?>>🟡 Tarde</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="observacion" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Escribir nota..." value="<?= htmlspecialchars($row['observacion'] ?? '') ?>">
                                    </td>
                                    <td>
                                        <button type="submit" class="btn btn-purple btn-sm">✓</button>
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

        <!-- PESTAÑA 2: ALUMNOS -->
        <?php if ($pestana_activa === 'alumnos'): ?>
        <div class="card card-custom p-4">
            <h5 class="fw-bold text-light mb-3">Gestión de Alumnos</h5>
            <div class="table-responsive">
                <table class="table table-dark-custom align-middle">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Grado y Sección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listaAlumnos as $alumno): ?>
                        <tr>
                            <td class="fw-semibold text-light"><?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']) ?></td>
                            <td><?= htmlspecialchars($alumno['grado'] . ' - ' . $alumno['seccion']) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('¿Dar de baja a este alumno?');" style="display:inline;">
                                    <input type="hidden" name="accion" value="baja_alumno">
                                    <input type="hidden" name="id_estudiante" value="<?= $alumno['id_estudiante'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Retirar / Baja</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- PESTAÑA 3: PROFESORES -->
        <?php if ($pestana_activa === 'profesores'): ?>
        <div class="card card-custom p-4">
            <h5 class="fw-bold text-light mb-3">Personal Docente</h5>
            <div class="table-responsive">
                <table class="table table-dark-custom align-middle">
                    <thead>
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Usuario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listaProfesores as $prof): ?>
                        <tr>
                            <td class="fw-semibold text-light"><?= htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']) ?></td>
                            <td><?= htmlspecialchars($prof['usuario']) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirm('¿Dar de baja a este docente?');" style="display:inline;">
                                    <input type="hidden" name="accion" value="baja_docente">
                                    <input type="hidden" name="id_docente" value="<?= $prof['id_profesor'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Dar de baja</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>