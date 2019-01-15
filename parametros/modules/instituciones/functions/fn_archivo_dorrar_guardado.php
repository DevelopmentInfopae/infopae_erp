<?php
//var_dump($_POST);
//$nombre = $_POST['nombre'];
//$ruta = "upload/".$nombre.".jpg";
//unlink($ruta);
$urlCompleta = $_POST['url'];
$url = $urlCompleta;
$url = substr($url,16);
//echo "<br>$url<br>";


include("../db/conexion.php");

$mysqli = new mysqli($Hostname , $Username,   $Password,  $Database);
if ($mysqli->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$mysqli->set_charset("utf8");

$consulta = " delete from fotos_inmueble where url = '$url' ";
$resultado = $mysqli->query($consulta) or die(mysqli_error($mysqli));
if($mysqli->affected_rows >= 1){
  echo '1';
  unlink($urlCompleta);
}
