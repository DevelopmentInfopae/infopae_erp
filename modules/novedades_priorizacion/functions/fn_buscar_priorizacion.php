<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$semanas = (isset($_POST['semanas']) && $_POST['semanas'] != '') ? $_POST["semanas"] : "";
$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

$registros = 0;
//var_dump($_POST);

$semana = $semanas[0];
$consulta = " SELECT * FROM priorizacion$semana where cod_sede = '$sede' ";
//echo $consulta;
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$registros++;
		$cantEstudiantes = $row['cant_Estudiantes'];
		$numEstFocalizados = $row['num_est_focalizados'];
		$aps = $row['APS'];
		$aps1 = $row['Etario1_APS'];
		$aps2 = $row['Etario2_APS'];
		$aps3 = $row['Etario3_APS'];
		$cajmri = $row['CAJMRI'];
		$cajmri1 = $row['Etario1_CAJMRI'];
		$cajmri2 = $row['Etario2_CAJMRI'];
		$cajmri3 = $row['Etario3_CAJTRI'];
		$cajmps = $row['CAJMPS'];
		$cajmps1 = $row['Etario1_CAJMPS'];
		$cajmps2 = $row['Etario2_CAJMPS'];
		$cajmps3 = $row['Etario3_CAJMPS'];
	}// Termina el while
}//Termina el if que valida que si existan resultados

echo json_encode(array(
	"registros" => $registros,
	"cantEstudiantes" => $cantEstudiantes,
	"numEstFocalizados" => $numEstFocalizados,
	"aps" => $aps,
	"aps1" => $aps1,
	"aps2" => $aps2,
	"aps3" => $aps3,
	"cajmri" => $cajmri,
	"cajmri1" => $cajmri1,
	"cajmri2" => $cajmri2,
	"cajmri3" => $cajmri3,
	"cajmps" => $cajmps,
	"cajmps1" => $cajmps1,
	"cajmps2" => $cajmps2,
	"cajmps3" => $cajmps3
));
