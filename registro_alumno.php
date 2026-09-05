<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro_estudiante</title>
    <link rel="stylesheet" href="estilos.css?v=4">
</head>
<link rel="icon" type="image/png" href="logo-removebg-preview.png">
<body class="login-body">

<div class="registro-card-animated">
    <div class="registro-box">
        
    <h2>Registro de Alumnos</h2>
    <p class="subtitulo">Ingrese sus datos para registrar su cuenta</p>

    <form action="procesar_registro_alumno.php" method="POST" class="form-columna">
        <div class="input-group">
            <label for="nombre">Nombre completo</label>
            <input type="text" name="nombre" id="nombre">
        </div>

        <div class="input-group">
            <label for="nie">Ingrese su Nie</label>
            <input type="text" name="nie" id="nie">
        </div>

            <div class="input-group">
                <label for="especialidad">Especialidad</label>
                <select name="especialidad" id="especialidad" class="input-select" required>
                    <option value="" disabled selected>Seleccione</option>
                    <option value="Desarrollo de Software">Software</option>
                    <option value="Electronica">Electronica</option>
                    <option value="Contabilidad">Contabilidad</option>
                    <option value="General">General</option>
                </select>
            </div>
            <div class="form-fila-doble">
                <div class="input-group">
                <label for="grado">Grado</label>
                <select name="grado" id="grado" class="input-select" required>
                    <option value="" disabled selected>Seleccione grado</option>
                    <option value="1° año">Primer año</option>
                    <option value="2° año">Segundo año</option>
                    <option value="3° año">Tercer año</option>
                </select>
            </div>
            <div class="input-group">
                <label for="seccion">Seccion</label>
                <select name="seccion" id="seccion" class="input-select">
                    <option value="" disabled selected>Seleccione Seccion</option>
                    <option value="A">Seccion A</option>
                    <option value="B">Seccion B</option>
                    <option value="C">seccion C</option>
                </select>
            </div>
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