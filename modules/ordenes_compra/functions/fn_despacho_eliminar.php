<?php
require_once '../../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

//var_dump($_POST);

// 	Con el numero de despacho se liminan
//	Él movimiento
//	El movimiento det
// 	El despacho det
// 	Y se cambia el estado del encabezado del despacho

$numero = $_POST['despacho'];
$tipo = 'DES';
$anno = date('y');

$annoi = substr($_POST['annoi'], 2);
$mesi = $_POST['mesi'];
if($mesi < 10){
  $mesi = "0".$mesi;
}

echo "1";


//Eliminando el detalle del movimiento
$consulta = "delete from productosmovdet$mesi$annoi where Documento = '$tipo' and Numero = $numero ";
$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));


//Eliminando el encabezado del movimiento
$consulta = "delete from productosmov$mesi$annoi where Documento = '$tipo' and Numero = $numero ";
$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

//Eliminando el detalle del despacho
$consulta = "delete from despachos_det$mesi$annoi where Tipo_Doc = '$tipo' and Num_Doc = $numero ";
$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

//Actualizando es estado del despacho
$consulta = "update despachos_enc$mesi$annoi set Estado = 0 where Tipo_Doc = '$tipo' and Num_Doc = $numero";
$Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

//Cerrando la conexión a la base de datos.
$Link->close();
