<?php
include '../config.php';
require_once '../db/conexion.php';
date_default_timezone_set('America/Bogota');
$fecha = date('Y-m-d H:i:s');

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"]!="SI") {
  header("Location: index.php");
  exit();
} 

$usuario_nm = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);
$pass = mysqli_real_escape_string($Link, $_POST['pass']);
$pass1 = mysqli_real_escape_string($Link, $_POST['pass1']);
$pass2 = mysqli_real_escape_string($Link, $_POST['pass2']);

$vsql = " select * FROM usuarios WHERE id = \"$usuario_nm\" AND clave = \"$pass\" ";
$result = $Link->query($vsql);


if($result->num_rows > 0) {
  if( $pass1 == $pass2 ) {
  	$ssqlup = " UPDATE usuarios SET clave = \"$pass1\", nueva_clave = \"1\" where id = \"$usuario_nm\" AND clave = \"$pass\" ";
  	$Link->query($ssqlup);
	
    // Haciendo registro en el log
    $logIdUsr = $_SESSION['id_usuario'];
    $consulta = " insert into log (id_usuario,fecha,descripcion) values (\"$logIdUsr\",\"$fecha\",\"Cambio la contraseña\") ";
    $Link->query($consulta);
    // Termina hacer registro en el log  
 
    echo "1"; 
    unset($_SESSION['nueva_clave']);
  }                  
  else { 
    echo "3";
  }
}
else {
  echo "2"; 
}