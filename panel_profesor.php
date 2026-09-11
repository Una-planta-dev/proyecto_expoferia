<?php
ob_start();
session_start();
require_once 'conexion.php';

// Verificar que el usuario sea un profesor
if (!isset($_SESSION['id_profesor'])) {
    header("Location: login.php");
    exit();
}

$id_profesor = $_SESSION['id_profesor'];

// Obtener datos del profesor
try {
    $stmt = $conexion->prepare("SELECT nombre, apellido FROM profesor WHERE id_profesor = :id");
    $stmt->execute([':id' => $id_profesor]);
    $profesor = $stmt->fetch(PDO::FETCH_ASSOC);
    $nombre_profesor = $profesor ? $profesor['nombre'] . ' ' . $profesor['apellido'] : ($_SESSION['nombre'] ?? 'Docente');
} catch (PDOException $e) {
    $nombre_profesor = $_SESSION['nombre'] ?? 'Docente';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia - Docente | Synca</title>
    <link rel="icon" type="image/png" href="logo-removebg-preview.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .navbar-docente {
            background-color: #0d6efd;
            color: white;
            padding: 12px 20px;
        }

        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .qr-container {
            background: white;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
        }

        .codigo-alternativo {
            border: 2px dashed #0d6efd;
            border-radius: 10px;
            padding: 15px;
            background-color: #f0f7ff;
            text-align: center;
        }

        .badge-grado {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 500;
        }

        .badge-especialidad {
            background-color: #cfe2ff;
            color: #084298;
        }

        .estado-select {
            min-width: 130px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <nav class="navbar-docente d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <strong>Control de Asistencia - Docente</strong>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span>👨‍🏫 Prof. <?= htmlspecialchars($nombre_profesor) ?></span>
            <a href="logout.php" class="btn btn-sm btn-light">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row g-4">

            <!-- Columna izquierda: QR -->
            <div class="col-lg-4">
                <div class="card card-custom p-4">
                    <h5 class="mb-1">Código QR del Día</h5>
                    <p class="text-muted small mb-3">Los alumnos pueden escanear este QR desde sus teléfonos.</p>

                    <div class="text-center mb-4">
                        <div class="qr-container shadow-sm mb-3">
                            <!-- Generador dinámico de QR de prueba -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=ASISTENCIA_DOCENTE_<?= $id_profesor ?>" alt="Código QR Asistencia" class="img-fluid">
                        </div>
                    </div>

                    <div class="codigo-alternativo">
                        <small class="text-muted d-block mb-1">Código alternativo de la clase:</small>
                        <h3 class="mb-0 fw-bold text-primary">SYN-<?= date('Ymd') ?></h3>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Registro y Lista -->
            <div class="col-lg-8">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Lista de Asistencia</h5>
                        <span class="badge bg-primary px-3 py-2">Fecha: <?= date('d/m/Y') ?></span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Grado / Especialidad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        Selecciona una materia o grupo para cargar los alumnos.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>