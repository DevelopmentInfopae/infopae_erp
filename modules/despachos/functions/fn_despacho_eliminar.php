<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

// 	Con el numero de despacho se liminan
//	Él movimiento
//	El movimiento det
// 	El despacho det
// 	Y se cambia el estado del encabezado del despacho

$numero = $_POST['despachos'];
$numero = trim($numero,",");
$arrayDespachos = explode(",", $numero);
$numeroBitacora = '' ;

foreach ($arrayDespachos as $key => $value) {
  $numeroBitacora .=  trim($value, "'") .",";
}
$numeroBitacora = trim($numeroBitacora, ",");
$tipo = 'DES';
$anno = date('y');

$annoi = substr($_POST['annoi'], 2);
$mesi = $_POST['mesi'];
if($mesi < 10){
  $mesi = "0".$mesi;
}

//Eliminando el detalle del movimiento
$consulta = "delete from productosmovdet$mesi$annoi where Documento = '$tipo' and Numero IN ($numero) "; 
$Link->query($consulta) or die ('Error al eliminar productos detalle '. mysqli_error($Link));

//Eliminando el encabezado del movimiento
$consulta = "delete from productosmov$mesi$annoi where Documento = '$tipo' and Numero IN ($numero) ";
$Link->query($consulta) or die ('Error al eliminar productos encabezado '. mysqli_error($Link));

//Eliminando el detalle del despacho
$consulta = "delete from despachos_det$mesi$annoi where Tipo_Doc = '$tipo' and Num_Doc IN ($numero) ";
$Link->query($consulta) or die ('Error al eliminar despachos detalle '. mysqli_error($Link));

//Actualizando es estado del despacho
$consulta = "update despachos_enc$mesi$annoi set Estado = 0 where Tipo_Doc = '$tipo' and Num_Doc IN ($numero)"; 
$Link->query($consulta) or die ('Error al actualizar despachos encabezado '. mysqli_error($Link));

//Insertando el registro en la bitacora
$consultaBitacora = " INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) 
                      VALUES ('" . date("Y-m-d H-i-s") . "', 
                              '" . $_SESSION["idUsuario"] . "', 
                              '55', 
                              ' Se eliminó los despachos Numero: <strong>".$numeroBitacora."</strong>' )";
    $Link->query($consultaBitacora) or die ('Error al insertar la bitacora '. mysqli_error($Link));

//Cerrando la conexión a la base de datos.
$Link->close();


echo "1";
