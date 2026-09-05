<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro_docente</title>
    <link rel="stylesheet" href="estilos.css?v=4">
</head>
<body class="login-body">
    <link rel="icon" type="image/png" href="logo-removebg-preview.png">

<div class="registro-card-animated">
    <div class="registro-box">
        
    <h2>Registro de Docentes</h2>
    <p class="subtitulo">Ingrese sus datos para registrar su cuenta</p>

    <form action="procesar_registro_docente.php" method="POST" class="form-columna">
      <div class="form-fila-doble">
        <div class="input-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre">
        </div>
        <div class="input-group">
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido">
        </div>
      </div>
        <div class="input-group">
            <label for="telefono">Ingrese su Numero de telefono</label>
            <input type="text" name="telefono" id="telefono">
        </div>

           <div class="input-group">
            <label for="correo">Ingrese su correo institucional</label>
            <input type="email" name="correo" id="correo">
           </div>
           <div class="input-group">
            <label for="password">Ingrese su contraseña</label>
            <input type="password" name="password" id="password">
           </div>

        <button type="submit" class="btn-enviar">Crear cuenta</button>
    </form>
    <div class="volver-box">
        <a href="registro_opciones.php" class="btn-volver">Volver a las opciones</a>
    </div>
    </div>
</div>
    
</body>
</html>