<?php
session_start();//para saber cual es la sesion a destruir
include("db/conexion.php");





// Haciendo registro en el log
	$mysqli = new mysqli($Hostname , $Username,   $Password,  $Database);
	if ($mysqli->connect_errno) {
	  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}


	$logIdUsr = $_SESSION['id_usuario'];
	date_default_timezone_set('America/Bogota');
	$fecha = date('Y-m-d H:i:s');

	$consulta = " insert into log (id_usuario,fecha,descripcion) 
	values ('$logIdUsr','$fecha','Cerro sesion') ";
	//echo '<br>Consulta para el log: '.$consulta;    

	$mysqli->query($consulta);



	mysqli_close($mysqli);
// Termina hacer registro en el log






session_destroy();//con esto destruyes la sesion
echo "<script type='text/javascript'>
       	window.location.href= 'index.php';
      </script>";
 