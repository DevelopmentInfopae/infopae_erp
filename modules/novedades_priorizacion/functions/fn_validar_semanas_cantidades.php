<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);
$semanas = (isset($_POST['semanas']) && $_POST['semanas'] != '') ? $_POST["semanas"] : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";

$log = "";
$respuesta = 1;

$aux = 0;
$primeraSemana = array();

foreach ($semanas as $semana) {
	$consulta = "SELECT * FROM priorizacion$semana WHERE cod_sede = '$sede'";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	if($resultado->num_rows > 0) {
		$row = $resultado->fetch_assoc();
		$row['id'] = 0;

		if($aux == 0) {
			$primeraSemana = $row;
		} else {
			if($primeraSemana === $row) {
			}else{
				echo "<br>No<br>";
				$respuesta++;
				$log = "La semana $semana tiene valores diferentes.";
			}
		}
	}
	$aux++;
}

$respuesta_ajax = ["log"=>$log, "respuesta"=>$respuesta];
echo json_encode($respuesta_ajax);