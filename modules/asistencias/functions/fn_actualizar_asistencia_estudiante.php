<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

// var_dump($_POST);
//var_dump($_SESSION);

$anno = $annoAsistencia2D; 

if(isset($_POST["mes"]) && $_POST["mes"] != ""){
	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
} else{
	$mes = $mesAsistencia;
}

$semana = mysqli_real_escape_string($Link, $_POST['semana']);

if(isset($_POST["dia"]) && $_POST["dia"] != ""){
	$dia = mysqli_real_escape_string($Link, $_POST['dia']);
} else{
	$dia = $diaAsistencia;
}








$sede = mysqli_real_escape_string($Link, $_POST['sede']);
$complemento = mysqli_real_escape_string($Link, $_POST['complemento']);

$documento = mysqli_real_escape_string($Link, $_POST['documento']); 
$tipoDocumento = mysqli_real_escape_string($Link, $_POST['tipoDocumento']); 
$valor = mysqli_real_escape_string($Link, $_POST['valor']); 


$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);




$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;
include 'fn_validar_existencias_tablas.php';




$consultaRegistro = " select * from asistencia_det$mes$anno where mes = \"$mes\" and dia = \"$dia\" and tipo_doc = \"$tipoDocumento\" and num_doc = \"$documento\" and complemento = \"$complemento\" ";

$resultRegistro = $Link->query($consultaRegistro) or die ('Consulta si existe registro'. mysqli_error($Link));
// echo "<br><br>$consultaRegistro<br><br>"; 
if($resultRegistro->num_rows >= 1){
	$consulta = " update asistencia_det$mes$anno set asistencia = $valor, repite = 0, consumio = 0, repitio = 0 where asistencia_det$mes$anno.mes = \"$mes\" and asistencia_det$mes$anno.semana = \"$semana\" and asistencia_det$mes$anno.dia = \"$dia\" and asistencia_det$mes$anno.num_doc  = \"$documento\" and asistencia_det$mes$anno.tipo_doc  = \"$tipoDocumento\" and asistencia_det$mes$anno.complemento  = \"$complemento\" "; 
	
	$result = $Link->query($consulta) or die ('Actualización de asistencia'.$consulta. mysqli_error($Link));

}else{
	$consulta = " insert into asistencia_det$mes$anno ( tipo_doc, num_doc, fecha, mes, semana, dia, asistencia, id_usuario, complemento) values (\"$tipoDocumento\", \"$documento\", \"$fecha\", \"$mes\", \"$semana\", \"$dia\", \"$valor\", \"$id_usuario\", \"$complemento\" ) ";
	
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