<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña - Synca</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <div class="contenedor">
        <h2>Recuperar Contraseña</h2>
        <p>Ingresa tu correo institucional registrado para enviarte un enlace de recuperación.</p>
        
        <!-- Apunta directamente a tu procesar_recuperar.php -->
        <form action="procesar_recuperar.php" method="POST">
            <label for="correo">Correo Institucional:</label>
            <input type="email" name="correo" id="correo" placeholder="ejemplo@correo.com" required>
            
            <button type="submit">Enviar Enlace</button>
        </form>
        <a href="login.php">Volver al inicio de sesión</a>
    </div>
</body>
</html>