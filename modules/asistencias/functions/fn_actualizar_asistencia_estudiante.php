<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// var_dump($_POST);
//var_dump($_SESSION);

$fecha = date("Y-m-d H:i:s");
$anno = date("y"); 
$mes = date("m");

$sede = mysqli_real_escape_string($Link, $_POST['sede']);
$semana = mysqli_real_escape_string($Link, $_POST['semana']);

$documento = mysqli_real_escape_string($Link, $_POST['documento']); 
$tipoDocumento = mysqli_real_escape_string($Link, $_POST['tipoDocumento']); 
$valor = mysqli_real_escape_string($Link, $_POST['valor']); 

$dia = intval(date("d"));

$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);

$consulta = " update Asistencia$mes$anno set asistencia = $valor, repite = 0, consumio = 0, repitio = 0 where Asistencia$mes$anno.mes = \"$mes\" and Asistencia$mes$anno.semana = \"$semana\" and Asistencia$mes$anno.dia = \"$dia\" and Asistencia$mes$anno.num_doc  = \"$documento\" and Asistencia$mes$anno.tipo_doc  = \"$tipoDocumento\"";
$result = $Link->query($consulta) or die ('Actualización de asistencia'.$consulta. mysqli_error($Link));
if($result && $Link->affected_rows <= 0 ){
	$consulta = " insert into Asistencia$mes$anno ( tipo_doc, num_doc, fecha, mes, semana, dia, asistencia, id_usuario ) values (\"$tipoDocumento\", \"$documento\", \"$fecha\", \"$mes\", \"$semana\", \"$dia\", \"$valor\", ) ";
	$result = $Link->query($consulta) or die ('Inserción de asistencia'.$consulta. mysqli_error($Link));
}
if($result){
	$resultadoAJAX = array(
		"state" => 1,
		"message" => "El registro se ha actualizado con éxito.",
  	);
}else{
	$resultadoAJAX = array(
		"state" => 2,
		"message" => "Error al hacer la actualización del registro.",
  	);
}
echo json_encode($resultadoAJAX);