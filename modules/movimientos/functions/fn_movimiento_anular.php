<?php
session_start();
//var_dump($_POST);

//Parametros de fecha para saber que tablas afectar
date_default_timezone_set('America/Bogota');
$mes =  date('m');
$anno = date('y');
//echo '<br> Año actual: '.$anno.' Mes actual: '.$mes.'<br>';


$id= $_POST['id'];
$tablaMesAnno = $_POST['tabla'];

//var_dump($_POST);



require_once '../../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

$consulta = " update productosmov$tablaMesAnno set Anulado = '1' where id = '$id' ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($Link->affected_rows > 0){
  echo "1";
}
