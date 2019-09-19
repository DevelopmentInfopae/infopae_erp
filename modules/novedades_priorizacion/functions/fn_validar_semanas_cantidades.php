<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$semanas = (isset($_POST['semanas']) && $_POST['semanas'] != '') ? $_POST["semanas"] : "";

//$semanas = (isset($_POST['semanas']) && $_POST['semanas'] != '') ? mysqli_real_escape_string($Link, $_POST["semanas"]) : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

$log = "";
$respuesta = 1;

$aux = 0;
$primeraSemana = array();
foreach ($semanas as $semana) {
	$consulta = "select * from priorizacion$semana where cod_sede = '$sede'";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		$row['id'] = 0;
		if($aux == 0){
			$primeraSemana = $row;
		}else{
			if($primeraSemana === $row){
				//echo "<br>Si<br>";
			}else{
				echo "<br>No<br>";
				// var_dump($primeraSemana);
				// var_dump($row);
				$respuesta++;
				$log = "La semana $semana tiene valores diferentes.";
			}

		}
	}
	$aux++;
}
echo json_encode(array("log"=>$log, "respuesta"=>$respuesta));
