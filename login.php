<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesion</title>
    <link rel="stylesheet" href="estilos.css?v=2.1">
</head>
<link rel="icon" type="image/png" href="logo-removebg-preview.png">
<body>
    
    <div id="splash-screen">
        <div class="splash-content">
    <img src="logo-removebg-preview.png" alt="logo" class="splash-logo">
    <h1 class="splash-title">Synca</h1>
    </div>
    </div>

    <div class="login-container">
        <!-- parte izquierda -->
         <div class="login-banner">
            <div class="banner-content">
                <h2>Sistema de control de asistencia</h2>
            <p id="typewriter-text" class="explicacion"></p>
          </div>
          </div>
            <!-- parte derecha-->
           <div class="login-form">
            <img src="logo-removebg-preview.png" alt="logo Synca" class="logo">
            <h2>Inicio de sesion</h2>
            <p class="subtitulo">Ingrese su informacion para acceder</p>
            <form action="procesar_login.php" method="post">
                <div class="input-group">
                    <label for="correo">Correo Institucional</label>
                    <input type="email" name="correo" id="correo" placeholder="Ejemplo@correo.com" required>
                </div>
                
                    <div class="input-group">
                        <label for="password">contraseña</label>
                        <input type="password" name="password" id="password" placeholder="12345678" required>
                    </div>
                    <div class="form-actions"><label class="remember">
                        <input type="checkbox" name="remember"> Recordarme </label>
                        <a href="#" class="forgot-pass">¿Olvidaste tu contraseña?</a>
                    </div>

                   <button type="submit">Iniciar sesion</button>
                   <p class="registro-link">¿No tienes una cuenta?</p>
                    <a href="registro_opciones.php" class="btn-registro">Registrate aqui</a>
            </form>
           </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", ()=> {
                const text = "Proyecto creado por estudiantes de segundo año de software para la expoferia 2026, el proyecto es un registro de control de asistencia para los estudiantes de la institucion utilizando codigos QR para modernizar la forma de toma de asistencia en el aula, creando un metodo más limpio, ordenado y facil tanto para los alumnos como para los maestros";
                const textElement = document.getElementById("typewriter-text");
                let i = 0;
                const speed = 15;
                const pauseTime = 20000;

                function typeEffect() {
                    if (textElement && i <text.length) {
                        textElement.textContent += text.charAt(i);
                        i++;
                        setTimeout(typeEffect, speed);
}
                        else if (textElement) {
                            setTimeout(() => {
                                textElement.textContent = "";
                                i = 0;
                                typeEffect();
                            }, pauseTime);
                        }
                    }
                setTimeout(typeEffect, 300);
            });
        
            document.addEventListener("DOMContentLoaded", () => {
                    const splash = document.getElementById("splash-screen");
                    if (splash) {
                        setTimeout (() => {
                        splash.classList.add("hidden");
                        setTimeout(() => {
                            splash.style.display = "none";
                        }, 800);
                    
                }, 500)
              }
            });
        </script>
</body>
</html>

/*esto es una preuba de git*/