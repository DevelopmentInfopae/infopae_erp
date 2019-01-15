<?php
session_start();
$respuesta = '';
//var_dump($_SESSION);
include("../db/conexion.php");
$mysqli = new mysqli($Hostname , $Username,   $Password,  $Database);
if ($mysqli->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$mysqli->set_charset("utf8");
date_default_timezone_set('America/Bogota');
$fecha = date("Y-m-d H:m:s"); 
$dispositivo = mysqli_real_escape_string($mysqli, $_POST['dispositivo']);
$usuario = mysqli_real_escape_string($mysqli, $_SESSION['id_usuario']);
$error = mysqli_real_escape_string($mysqli, $_POST['respuesta']);
$consulta = " insert into log_errores_carga_archivos (fecha, id_dispositivo, id_usuario, error) values ('$fecha','$dispositivo','$usuario','$error')";
$mysqli ->query($consulta) or die ('Fallo la inserción en el log de errores. '. mysqli_error($Link));
mysqli_close($mysqli);
$respuesta = "1";    
echo json_encode(array("respuesta"=>$respuesta));