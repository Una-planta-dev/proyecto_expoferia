<?php
session_start();

// Validar que el usuario haya iniciado sesión y sea Administrador o Docente
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] !== 'admin' && $_SESSION['rol'] !== 'docente')) {
    header("Location: login.php?error=acceso_denegado");
    exit();
}

require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Synca</title>
    <link rel="icon" type="image/png" href="logo-removebg-preview.png">
    
    <!-- Bootstrap 5 CSS -->
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
            background-color: #59359a;
            color: white;
            box-shadow: 0 0 15px rgba(111, 66, 193, 0.5);
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

    <!-- Contenido Principal -->
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h1 class="fw-bold">Panel de Control</h1>
                <p class="text-secondary">Gestión de asistencias y generación de códigos QR de clase.</p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Módulo 1: Generación de QR -->
            <div class="col-md-4">
                <div class="card card-custom p-4 text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="fs-1 mb-3">📱</div>
                            <h3 class="fs-4 fw-bold mb-2">Generar Código QR</h3>
                            <p class="small text-muted">Crea una sesión de clase para que los alumnos escaneen su asistencia.</p>
                        </div>
                        <a href="#" class="btn btn-purple mt-3">Crear Sesión QR</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-4 text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="fs-1 mb-3">📊</div>
                            <h3 class="fs-4 fw-bold mb-2">Reporte de Asistencias</h3>
                            <p class="small text-muted">Consulta el historial completo de asistencia por materia o fecha.</p>
                        </div>
                        <a href="#" class="btn btn-purple mt-3">Ver Reportes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-4 text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="fs-1 mb-3">👥</div>
                            <h3 class="fs-4 fw-bold mb-2">Lista de Alumnos</h3>
                            <p class="small text-muted">Visualiza y administra los estudiantes registrados en tus clases.</p>
                        </div>
                        <a href="#" class="btn btn-purple mt-3">Gestionar Alumnos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>