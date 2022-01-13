<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

$anno = $annoasistencia; 
if(isset($_POST["mes"]) && $_POST["mes"] != ""){ $mes = mysqli_real_escape_string($Link, $_POST['mes']); }else{ $mes = $mesAsistencia; }
if(isset($_POST["dia"]) && $_POST["dia"] != ""){ $dia = mysqli_real_escape_string($Link, $_POST['dia']); }else{ $dia = $diaAsistencia; }
$sede = mysqli_real_escape_string($Link, $_POST['sede']);
$semana = mysqli_real_escape_string($Link, $_POST['semana']);
$id_usuario = mysqli_real_escape_string($Link, $_SESSION['id_usuario']);
$consumieron = [];
$noConsumieron = [];
$repitieron = [];
if(isset($_POST['consumieron'])){ $consumieron = $_POST['consumieron']; }
if(isset($_POST['noConsumieron'])){ $noConsumieron = $_POST['noConsumieron']; }
if(isset($_POST['repitieron'])){ $repitieron = $_POST['repitieron']; }
$tipo_doc = "";
$num_doc = "";
$consulta = "";
$consultaRegistro = "";
foreach ($consumieron as $consumio){
	$repitio = 0;
	$tipo_doc = mysqli_real_escape_string($Link, $consumio["tipoDocumento"]);
	$num_doc = mysqli_real_escape_string($Link, $consumio["documento"]);
	$consultaRegistro = " select * from asistencia_det$mes$anno where mes = \"$mes\" and dia = \"$dia\" and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\" ";
	$resultRegistro = $Link->query($consultaRegistro) or die ('Consulta si existe registro'. mysqli_error($Link));
	if($resultRegistro->num_rows >= 1){
		$consulta .= " update asistencia_det$mes$anno set consumio = 1 ";
		if(isset($repitieron[$num_doc])){
			$consulta .= " , repitio = 1 ";
		}else{
			$consulta .= " , repitio = 0 ";	
		}
		$consulta .= " where mes = \"$mes\" and dia = \"$dia\" and asistencia = 1 
		and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\"; ";
	}
	else{
		if(isset($repitieron[$num_doc])){
			$repitio = 1;
		}
		$consultaInsercion = " insert into asistencia_det$mes$anno (tipo_doc, num_doc, fecha, mes, semana, dia, asistencia, id_usuario, repite, consumio, repitio) values ( \"$tipo_doc\", \"$num_doc\", \"$fecha\", \"$mes\", \"$semana\", \"$dia\", \"1\", \"$id_usuario\", \"0\", \"0\", \"$repitio\" ) ";
		$Link->query($consultaInsercion) or die ('Inserción en asistencias'. mysqli_error($Link));
	}
}

foreach ($noConsumieron as $noConsumio){
	$tipo_doc = mysqli_real_escape_string($Link, $noConsumio["tipoDocumento"]);
	$num_doc = mysqli_real_escape_string($Link, $noConsumio["documento"]);
	$consultaRegistro = " select * from asistencia_det$mes$anno where mes = \"$mes\" and dia = \"$dia\" and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\" ";
	$resultRegistro = $Link->query($consultaRegistro) or die ('Consulta si existe registro'. mysqli_error($Link));
	if($resultRegistro->num_rows >= 1){
		$consulta .= " update asistencia_det$mes$anno set consumio = 0 ";	
		$consulta .= " , repitio = 0 ";
		$consulta .= " where mes = \"$mes\" and dia = \"$dia\" and asistencia = 1 and tipo_doc = \"$tipo_doc\" and num_doc = \"$num_doc\"; ";
	}
	else{
		$consultaInsercion = " insert into asistencia_det$mes$anno (tipo_doc, num_doc, fecha, mes, semana, dia, asistencia, id_usuario, repite, consumio, repitio) values ( \"$tipo_doc\", \"$num_doc\", \"$fecha\", \"$mes\", \"$semana\", \"$dia\", \"1\", \"$id_usuario\", \"0\", \"0\", \"0\" ) ";
		$Link->query($consultaInsercion) or die ('Inserción en asistencias'. mysqli_error($Link));
	}
}

//echo "<br>$consulta<br>";
if($consulta != "" && $consulta != null){
	$result = $Link->multi_query($consulta) or die ('Insert error'. mysqli_error($Link));
}

$resultadoAJAX = array(
	"state" => 1,
	"message" => "El registro se ha realizado con éxito.",
);

echo json_encode($resultadoAJAX);