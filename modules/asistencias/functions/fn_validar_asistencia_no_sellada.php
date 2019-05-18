<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

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

	// Validar que la asistencia no este sellada
	$fecha = date("Y-m-d H:i:s");
	$anno = date("y"); 
	$mes = date("m");
	$dia = intval(date("d"));
	$consulta = "select * from asistencia_enc$mes$anno where estado = \"2\" and mes = \"$mes\" and semana = \"$semanaActual\" and dia = \"$dia\" and cod_sede = \"$sede\"";
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