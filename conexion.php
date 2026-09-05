<<<<<<< HEAD
<?php 
$host = "localhost";
$dbname = "proyecto_expoferia";
$username = "root";
$password = "";

try {$conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Conexion exitosa a la base de datos";
}catch (PDOException $e) {
    echo "ocurrio un error al conectar :(: " . $e->getMessage();

}
=======
<?php 
$host = "localhost";
$dbname = "proyecto_expoferia";
$username = "root";
$password = "";

try {$conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Conexion exitosa a la base de datos";
}catch (PDOException $e) {
    echo "ocurrio un error al conectar :(: " . $e->getMessage();

}
>>>>>>> f7028d993f5ab001964b9e75507ad411aa3842f6
?>