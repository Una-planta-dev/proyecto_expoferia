<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="estilos.css?v=5">
</head>
<body class="login-body">

    <div class="registro-card-animated">
        <div class="registro-box">
            
            <h2>Recuperar Contraseña</h2>
            <p class="subtitulo">Ingrese su correo institucional para recibir las instrucciones de restablecimiento</p>

            <form action="procesar_recuperar.php" method="POST" class="form-columna">
                
                <div class="input-group">
                    <label for="correo">Correo institucional</label>
                    <input type="email" name="correo" id="correo" placeholder="ejemplo@clases.edu.sv" required>
                </div>

                <button type="submit" class="btn-enviar">Enviar enlace de recuperación</button>

            </form>

            <div class="volver-box">
                <a href="login.php" class="btn-volver"></a>
            </div>

        </div>
    </div>

</body>
</html>