<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Synca</title>
    <link rel="stylesheet" href="estilos.css?v=2.2">
    <link rel="icon" type="image/png" href="logo-removebg-preview.png">
</head>
<body>
    
    <div id="splash-screen">
        <div class="splash-content">
            <img src="logo-removebg-preview.png" alt="logo" class="splash-logo">
            <h1 class="splash-title">Synca</h1>
        </div>
    </div>

    <div class="login-container">
        <!-- Parte izquierda -->
        <div class="login-banner">
            <div class="banner-content">
                <h2>Sistema de Control de Asistencia</h2>
                <p id="typewriter-text" class="explicacion"></p>
            </div>
        </div>

        <!-- Parte derecha -->
        <div class="login-form">
            <img src="logo-removebg-preview.png" alt="logo Synca" class="logo">
            <h2>Inicio de Sesión</h2>
            <p class="subtitulo">Ingrese su información para acceder</p>

            <form action="procesar_login.php" method="post">
                <div class="input-group">
                    <label for="correo">Correo Institucional</label>
                    <input type="email" name="correo" id="correo" placeholder="Ejemplo@correo.com" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                </div>

                <div class="form-actions">
                    <label class="remember">
                        <input type="checkbox" name="remember"> Recordarme 
                    </label>
                    <a href="#" class="forgot-pass">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit">Iniciar sesión</button>
                <p class="registro-link">¿No tienes una cuenta?</p>
                <a href="registro_opciones.php" class="btn-registro">Regístrate aquí</a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Efecto máquina de escribir
            const text = "Proyecto creado por estudiantes de segundo año de software para la expoferia 2026, el proyecto es un registro de control de asistencia para los estudiantes de la institución utilizando códigos QR para modernizar la forma de toma de asistencia en el aula, creando un método más limpio, ordenado y fácil tanto para los alumnos como para los maestros.";
            const textElement = document.getElementById("typewriter-text");
            let i = 0;
            const speed = 15;
            const pauseTime = 20000;

            function typeEffect() {
                if (textElement && i < text.length) {
                    textElement.textContent += text.charAt(i);
                    i++;
                    setTimeout(typeEffect, speed);
                } else if (textElement) {
                    setTimeout(() => {
                        textElement.textContent = "";
                        i = 0;
                        typeEffect();
                    }, pauseTime);
                }
            }

            setTimeout(typeEffect, 300);

            // Splash Screen
            const splash = document.getElementById("splash-screen");
            if (splash) {
                setTimeout(() => {
                    splash.classList.add("hidden");
                    setTimeout(() => {
                        splash.style.display = "none";
                    }, 800);
                }, 500);
            }
        });
    </script>
</body>
</html>
</html>
