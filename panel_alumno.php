<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion.php';

// Validar que exista sesión y sea un alumno
if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
    header("Location: login.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'] ?? 'Estudiante';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S Y N C A | Portal del Estudiante</title>
    <link rel="icon" type="image/png" href="logo-removebg-preview.png">
    <!-- Librería para escanear códigos QR con la cámara -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #12121c;
            color: #f3f4f6;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            width: 100%;
            background-color: #0d0d15;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #2e2a45;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: 2px;
            color: #ffffff;
        }

        .navbar-brand span {
            color: #a855f7;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.95rem;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid #4b5563;
            color: #f3f4f6;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: #27272a;
            border-color: #a855f7;
            color: #ffffff;
        }

        .main-wrapper {
            width: 100%;
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 24px;
        }

        .synca-card {
            background-color: #1a1a27;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #2e2a45;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .tab-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 24px;
        }

        .tab-btn {
            background: #242438;
            border: 1px solid #2e2a45;
            color: #9ca3af;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: #6c2bd9;
            color: #ffffff;
            border-color: #a855f7;
        }

        .section-content {
            display: none;
        }

        .section-content.active {
            display: block;
        }

        .pin-section {
            text-align: center;
        }

        .pin-section h3 {
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #ffffff;
        }

        .pin-section p {
            color: #9ca3af;
            font-size: 0.85rem;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .pin-input {
            width: 100%;
            background: #12121c;
            border: 2px dashed #a855f7;
            border-radius: 8px;
            padding: 14px;
            color: #ffffff;
            font-size: 1.4rem;
            text-align: center;
            letter-spacing: 6px;
            margin-bottom: 20px;
            outline: none;
            font-weight: bold;
        }

        .btn-submit {
            width: 100%;
            background: #6c2bd9;
            color: #ffffff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #5b21b6;
        }

        #reader {
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #2e2a45;
            background: #12121c;
        }
        
        #reader video {
            border-radius: 8px;
        }

        .table-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .custom-table th {
            color: #a855f7;
            font-size: 0.8rem;
            padding: 12px;
            border-bottom: 1px solid #2e2a45;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .custom-table td {
            padding: 16px 12px;
            font-size: 0.9rem;
            border-bottom: 1px solid #222235;
        }

        .empty-state {
            text-align: center;
            color: #9ca3af;
            padding: 50px 0;
            font-size: 0.9rem;
        }

        @media (max-width: 900px) {
            .main-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <header class="top-navbar">
        <div class="navbar-brand">
            S Y N C A <span>| Portal del Estudiante</span>
        </div>
        <div class="user-info">
            <span>👨‍🎓 <?php echo htmlspecialchars($nombre_usuario); ?></span>
            <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
        </div>
    </header>

    <main class="main-wrapper">
        
        <section class="synca-card">
            <!-- Por defecto inicia seleccionado Código PIN -->
            <div class="tab-group">
                <button type="button" class="tab-btn" id="btn-tab-qr" onclick="cambiarTab('qr')">📷 Escanear QR</button>
                <button type="button" class="tab-btn active" id="btn-tab-pin" onclick="cambiarTab('pin')">🔑 Código PIN</button>
            </div>

            <!-- SECCIÓN ESCANER QR (Oculta por defecto) -->
            <div id="seccion-qr" class="section-content">
                <div class="pin-section">
                    <h3>Apunta con tu cámara</h3>
                    <p>Enfoca el código QR generado por tu docente para registrar tu asistencia automáticamente:</p>
                    
                    <div id="reader"></div>
                    <p id="qr-resultado" style="margin-top: 15px; font-size: 0.85rem; color: #a855f7;"></p>
                </div>
            </div>

            <!-- SECCIÓN CÓDIGO PIN (Visible por defecto con formato numeral) -->
            <div id="seccion-pin" class="section-content active">
                <div class="pin-section">
                    <h3>Ingresar Código de Clase</h3>
                    <p>Si no puedes escanear el QR, digita aquí el PIN o código de la clase:</p>
                    
                    <form action="procesar_asistencia.php" method="POST">
                        <input type="text" name="codigo_pin" class="pin-input" placeholder="# # # # # #" required autocomplete="off">
                        <button type="submit" class="btn-submit">Registrar Entrada</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="synca-card">
            <h2 class="table-title">Mi Historial de Asistencias</h2>

            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora de Entrada</th>
                        <th>Docente / Asignatura</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="historial-asistencia-body">
                    <tr>
                        <td colspan="4" class="empty-state">
                            Cargando asistencias...
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

    </main>

    <script>
        let html5QrCode = null;

        function cambiarTab(tipo) {
            const tabQr = document.getElementById('btn-tab-qr');
            const tabPin = document.getElementById('btn-tab-pin');
            const secQr = document.getElementById('seccion-qr');
            const secPin = document.getElementById('seccion-pin');

            if (tipo === 'qr') {
                tabQr.classList.add('active');
                tabPin.classList.remove('active');
                secQr.classList.add('active');
                secPin.classList.remove('active');
                iniciarEscanner(); // Solo aquí se enciende la cámara
            } else {
                tabPin.classList.add('active');
                tabQr.classList.remove('active');
                secPin.classList.add('active');
                secQr.classList.remove('active');
                detenerEscanner(); // Se apaga la cámara al salir de la pestaña
            }
        }

        function iniciarEscanner() {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }
            
            // Verificamos si ya está escaneando para no relanzarlo en bucle
            if (!html5QrCode.isScanning) {
                html5QrCode.start(
                    { facingMode: "environment" },
                    {
                        fps: 10,
                        qrbox: { width: 220, height: 220 }
                    },
                    async (decodedText) => {
                        document.getElementById('qr-resultado').innerText = "¡Código detectado! Procesando...";
                        await detenerEscanner();

                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'procesar_asistencia.php';

                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'codigo_pin';
                        input.value = decodedText;

                        form.appendChild(input);
                        document.body.appendChild(form);
                        form.submit();
                    },
                    (errorMessage) => {}
                ).catch(err => {
                    console.error("No se pudo iniciar la cámara:", err);
                    document.getElementById('qr-resultado').innerText = "⚠️ No se pudo acceder a la cámara o permisos denegados.";
                });
            }
        }

        async function detenerEscanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                try {
                    await html5QrCode.stop();
                } catch (err) {
                    console.error("Error al detener el escáner:", err);
                }
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            async function cargarHistorialAlumno() {
                try {
                    const response = await fetch('obtener_asistencias_alumno.php');
                    const textResponse = await response.text();
                    
                    let result;
                    try {
                        result = JSON.parse(textResponse);
                    } catch (e) {
                        return;
                    }

                    const tbody = document.getElementById('historial-asistencia-body');
                    if (!tbody) return;

                    if (result.success) {
                        if (!result.data || result.data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="4" class="empty-state">No tienes asistencias registradas todavía.</td></tr>`;
                        } else {
                            let html = '';
                            result.data.forEach(item => {
                                html += `
                                    <tr>
                                        <td>${item.fecha}</td>
                                        <td style="color: #a855f7; font-weight: 600;">${item.hora_registro}</td>
                                        <td>${item.asignatura}</td>
                                        <td><span style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid #22c55e; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">${item.estado}</span></td>
                                    </tr>`;
                            });
                            tbody.innerHTML = html;
                        }
                    }
                } catch (error) {
                    console.error("Error de red:", error);
                }
            }

            cargarHistorialAlumno();
            setInterval(cargarHistorialAlumno, 3000);
        });
    </script>
</body>
</html>