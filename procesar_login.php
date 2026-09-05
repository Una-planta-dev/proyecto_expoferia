<<<<<<< HEAD
<?php
 session_start();
 require_once 'conexion.php';
 if ($_SERVER["REQUEST_METHOD"] == 'POST') {
   $correo = trim($_POST['correo']);
   $password = trim($_POST['password']);
   if (empty($correo) || empty($password)) {
    header("Location: login.php?error=campos_vacios");
    exit();
    }
    try {
        $query = "SELECT id, correo, password FROM usuarios WHERE correo = :correo";
        $stmt = $conexion ->prepare($query);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $usuario['password'])){
            $_SESSION['usuario_id'] = $usuario['id'];
            header("Location: panel.php"); exit();
        } else {
            header("location: login.php?error=credenciales_incorrectas");
            exit();
        }
    } else {
        header("location: login.php?error=credenciales_incorrectas");
        exit();
    }
    }catch (PDOException $e) {
        header("Location: login.php?error=error_sistema");
        exit();
    }
 } else {
    header("Location: login.php");
    exit();
 }

=======
<?php
 session_start();
 require_once 'conexion.php';
 if ($_SERVER["REQUEST_METHOD"] == 'POST') {
   $correo = trim($_POST['correo']);
   $password = trim($_POST['password']);
   if (empty($correo) || empty($password)) {
    header("Location: login.php?error=campos_vacios");
    exit();
    }
    try {
        $query = "SELECT id, correo, password FROM usuarios WHERE correo = :correo";
        $stmt = $conexion ->prepare($query);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $usuario['password'])){
            $_SESSION['usuario_id'] = $usuario['id'];
            header("Location: panel.php"); exit();
        } else {
            header("location: login.php?error=credenciales_incorrectas");
            exit();
        }
    } else {
        header("location: login.php?error=credenciales_incorrectas");
        exit();
    }
    }catch (PDOException $e) {
        header("Location: login.php?error=error_sistema");
        exit();
    }
 } else {
    header("Location: login.php");
    exit();
 }

>>>>>>> f7028d993f5ab001964b9e75507ad411aa3842f6
