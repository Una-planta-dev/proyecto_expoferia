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
            background-color: #0b0914;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        p, span, h1, h2, h3, h4, h5, h6, label {
            color: #ffffff !important;
        }

        .navbar-custom {
            background-color: #05030a;
            border-bottom: 1px solid #2d1f4e;
        }

        .card-custom {
            background-color: #120e1f;
            border: 1px solid #2d1f4e;
            border-radius: 12px;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            border-color: #6f42c1;
        }

        .btn-purple {
            background-color: #6f42c1;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-purple:hover {
            background-color: #59359a;
            color: white;
            box-shadow: 0 0 15px rgba(111, 66, 193, 0.5);
        }
    </style>
</head>
<body>

    <!-- Barra de Navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="panel_admin.php">
                <img src="logo-removebg-preview.png" alt="Logo Synca" height="50" class="me-2">
                Synca Admin
            </a>
            
            <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Docente'); ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm px-3">Cerrar Sesión</a>
            </div>
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

            <!-- Módulo 2: Reportes y Listados -->
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

            <!-- Módulo 3: Control de Estudiantes -->
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

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>