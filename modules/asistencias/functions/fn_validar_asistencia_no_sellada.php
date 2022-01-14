<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

//var_dump($_SESSION);

if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => ""
	);
}else{
	// Declaración de variables.
	$semanaActual = "";
	$sede = "";

	$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);
	$semanaActual = (isset($_POST["semanaActual"]) && $_POST["semanaActual"] != "") ? mysqli_real_escape_string($Link, $_POST["semanaActual"]) : "";

	$sede = (isset($_POST["sede"]) && $_POST["sede"] != "") ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
	$complemento = (isset($_POST["complemento"]) && $_POST["complemento"] != "") ? mysqli_real_escape_string($Link, $_POST["complemento"]) : "";

	// Validar que la asistencia no este sellada
	$anno = $annoAsistencia2D; 
	$mes = $mesAsistencia;
	$dia = $diaAsistencia;
	$mesTablaAsistencia = $mes;
	$annoTablaAsistencia = $anno;
	include 'fn_validar_existencias_tablas.php';

	$consulta = "select * from asistencia_enc$mes$anno where estado = \"2\" and mes = \"$mes\" and semana = \"$semanaActual\" and dia = \"$dia\" and cod_sede = \"$sede\" and complemento = \"$complemento\" ";

	$resultado = $Link->query($consulta);
	if($resultado->num_rows > 0){
		$resultadoAJAX = array(
			"estado" => 1,
			"mensaje" => "Ya no se puede hacer cambios en esta asistencia."
		);
	}else{
		$resultadoAJAX = array(
			"estado" => 0,
			"mensaje" => ""
		);
	}
}
echo json_encode($resultadoAJAX);