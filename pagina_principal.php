<?php
// index.php - Página principal de presentación de Synca
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Synca - Sistema de Control de Asistencia</title>
    <link rel="icon" type="image/png" href="logo-removebg-preview.png">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0b0914; /* Fondo negro con un sutil tinte oscuro */
            color: #ffffff; /* Todo el texto base en blanco */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Forzar que todos los textos comunes sean blancos */
        p, span, h1, h2, h3, h4, h5, h6, label {
            color: #ffffff !important;
        }

        /* Barra de navegación */
        .navbar-custom {
            background-color: #05030a;
            border-bottom: 1px solid #2d1f4e;
        }

        /* Sección principal Hero con degradado morado y negro */
        .hero-section {
            background: linear-gradient(135deg, #130b24 0%, #05030a 100%);
            padding: 120px 0;
            border-bottom: 2px solid #6f42c1;
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

        /* Tarjetas de características */
        .feature-card {
            background-color: #120e1f;
            border: 1px solid #2d1f4e;
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(111, 66, 193, 0.2);
            border-color: #6f42c1;
        }

        /* Pie de página */
        footer {
            background-color: #05030a;
            border-top: 1px solid #2d1f4e;
        }
    </style>
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom px-4 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="pagina_principal.php">
    <img src="logo-removebg-preview.png" alt="Logo Synca" height="65" class="me-2 d-inline-block align-text-top">
    Synca
</a>
            <div class="d-flex">
                <a href="login.php" class="btn btn-purple px-4 fw-semibold text-white">Iniciar Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Sección Principal (Hero) -->
    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold mb-3">Bienvenido a Synca</h1>
            <p class="lead mb-4">El sistema inteligente de control de asistencia estudiantil mediante códigos QR.</p>
            <p class="small mb-5">Proyecto creado por estudiantes de segundo año de software para la Expoferia 2026.</p>
            <a href="login.php" class="btn btn-purple btn-lg fw-bold px-5 py-3 shadow text-white">Acceder al Sistema</a>
        </div>
    </header>

    <!-- Sección de Características -->
    <main class="container py-5">
        <div class="row text-center mb-5">
            <div class="col">
                <h2 class="fw-bold">¿Por qué Synca?</h2>
                <p>Modernizando el registro escolar de manera ágil, segura y tecnológica.</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 text-center">
                    <div class="card-body">
                        <div class="fs-1 mb-3">📱</div>
                        <h3 class="fs-4 fw-bold mb-3">Tecnología QR</h3>
                        <p>Los alumnos escanean el código dinámico del docente para registrar su asistencia al instante de forma automatizada.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 text-center">
                    <div class="card-body">
                        <div class="fs-1 mb-3">⚡</div>
                        <h3 class="fs-4 fw-bold mb-3">Tiempo Real</h3>
                        <p>Los profesores pueden visualizar la lista de asistencia al momento y modificar estados con total fluidez.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card p-4 h-100 text-center">
                    <div class="card-body">
                        <div class="fs-1 mb-3">🔒</div>
                        <h3 class="fs-4 fw-bold mb-3">Seguro y Privado</h3>
                        <p>Acceso estrictamente restringido por roles de usuario para mantener la integridad de los datos institucionales.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Pie de página -->
    <footer class="text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2026 Synca. Todos los derechos reservados. Expoferia de Software.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
