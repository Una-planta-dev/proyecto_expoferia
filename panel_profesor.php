<?php
ob_start();
session_start();
date_default_timezone_set('America/El_Salvador');

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

// Formato 12 Horas con minutos
$hora_12h = date('h:i A'); 
$fecha_actual = date('d/m/Y');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencia - Docente | SYNCA</title>
    <link rel="icon" type="image/png" href="logo-removebg-preview.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0f111a;
            color: #e2e8f0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        .navbar-docente {
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

        .qr-container {
            background: #ffffff;
            padding: 16px;
            border-radius: 16px;
            display: inline-block;
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.2);
        }

        .codigo-alternativo {
            border: 2px dashed #8b5cf6;
            border-radius: 12px;
            padding: 16px;
            background-color: #131522;
            text-align: center;
        }

        .btn-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: #ffffff;
            border: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-purple:hover {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            color: #ffffff;
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.4);
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

        /* Estilo corregido para la tabla oscura */
        .table-dark-custom {
            color: #e2e8f0;
            --bs-table-bg: transparent;
        }

        .table-dark-custom thead th {
            background-color: #131522 !important;
            color: #a855f7 !important;
            border-bottom: 2px solid #2d3248 !important;
            padding: 14px 12px;
        }

        .table-dark-custom tbody td {
            background-color: transparent !important;
            color: #cbd5e1 !important;
            border-bottom: 1px solid #2d3248 !important;
            padding: 16px 12px;
        }

        .badge-purple {
            background-color: #3b0764;
            color: #d8b4fe;
            border: 1px solid #6b21a8;
        }

        .time-badge {
            color: #a855f7;
            font-weight: 600;
        }

        .text-subtle {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar-docente d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="brand-logo">S Y N C A</span>
            <span class="text-subtle fs-6">| Panel de Control</span>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-light">👨‍🏫 Prof. <b><?= htmlspecialchars($nombre_profesor) ?></b></span>
            <a href="logout.php" class="btn btn-sm btn-outline-custom">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container-fluid py-4 px-4">
        
        <!-- Banner Superior -->
        <div class="banner-purple d-flex justify-content-between align-items-center shadow-lg">
            <div>
                <h4 class="fw-bold mb-1">Sistema de Control de Asistencia</h4>
                <p class="mb-0 text-light-50 small">Gestión y registro digital por código QR para la expoferia 2026.</p>
            </div>
            <span class="badge badge-purple fs-6 px-3 py-2">Estado: Clase Activa</span>
        </div>

        <div class="row g-4">

            <!-- Columna Izquierda: QR del Día -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 text-center">
                    <h5 class="fw-bold text-light mb-1">Código QR de la Clase</h5>
                    <p class="text-subtle small mb-3">Muestra este código a tus estudiantes para registrar su asistencia.</p>

                    <div class="mb-3">
                        <div class="qr-container">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=190x190&data=ASISTENCIA_DOCENTE_<?= $id_profesor ?>_<?= date('h-i-A') ?>" alt="Código QR Asistencia" class="img-fluid">
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-subtle">Generado a las: </small>
                        <span class="time-badge"><?= $hora_12h ?></span>
                    </div>

                    <div class="codigo-alternativo mb-3">
                        <small class="text-subtle d-block mb-1">Código Alternativo Manual:</small>
                        <h3 class="mb-0 fw-bold text-light" style="letter-spacing: 2px;">SYN-<?= date('Ymd') ?>-<?= str_replace([' ', ':'], '', $hora_12h) ?></h3>
                    </div>

                    <button class="btn btn-purple w-100 py-2 mt-2" onclick="location.reload();">Generar / Actualizar Código</button>
                </div>
            </div>

            <!-- Columna Derecha: Tabla Asistencia -->
            <div class="col-lg-8">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-light mb-0">Registro de Asistencia del Día</h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-dark border border-secondary text-light px-3 py-2">Fecha: <?= $fecha_actual ?></span>
                            <span class="badge bg-dark border border-purple text-purple px-3 py-2 time-badge" id="reloj-12h"><?= $hora_12h ?></span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark-custom align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Grado / Sección</th>
                                    <th>Hora de Entrada (12h)</th>
                                    <th class="text-end">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="py-3">
                                            <p class="mb-1 text-light">Esperando que los alumnos escaneen el código QR...</p>
                                            <small class="text-subtle">Los registros aparecerán automáticamente en esta lista.</small>
                                        </div>
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
    <script>
        function actualizarReloj12H() {
            const ahora = new Date();
            let horas = ahora.getHours();
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const ampm = horas >= 12 ? 'PM' : 'AM';
            
            horas = horas % 12;
            horas = horas ? horas : 12;
            const horasStr = String(horas).padStart(2, '0');
            
            document.getElementById('reloj-12h').textContent = `${horasStr}:${minutos} ${ampm}`;
        }
        setInterval(actualizarReloj12H, 1000);
    </script>
</body>
</html>