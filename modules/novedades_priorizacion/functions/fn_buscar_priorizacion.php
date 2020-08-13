<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$registros = 0;

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);
$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$semanas = (isset($_POST['semanas']) && $_POST['semanas'] != '') ? $_POST["semanas"] : "";


$semana = $semanas[0];
$consulta = "SELECT * FROM priorizacion$semana WHERE cod_sede = '$sede'";

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
		$cajmri3 = $row['Etario3_CAJMRI'];
		$cajtri = $row['CAJTRI'];
		$cajtri1 = $row['Etario1_CAJTRI'];
		$cajtri2 = $row['Etario2_CAJTRI'];
		$cajtri3 = $row['Etario3_CAJTRI'];
		$cajmps = $row['CAJMPS'];
		$cajmps1 = $row['Etario1_CAJMPS'];
		$cajmps2 = $row['Etario2_CAJMPS'];
		$cajmps3 = $row['Etario3_CAJMPS'];
		$cajtps = $row['CAJTPS'];
		$cajtps1 = $row['Etario1_CAJTPS'];
		$cajtps2 = $row['Etario2_CAJTPS'];
		$cajtps3 = $row['Etario3_CAJTPS'];
		$rpc = $row['RPC'];
		$rpc1 = $row['Etario1_RPC'];
		$rpc2 = $row['Etario2_RPC'];
		$rpc3 = $row['Etario3_RPC'];
	}
}

$respuesta_ajax = [
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
	"cajtri" => $cajtri,
	"cajtri1" => $cajtri1,
	"cajtri2" => $cajtri2,
	"cajtri3" => $cajtri3,
	"cajmps" => $cajmps,
	"cajmps1" => $cajmps1,
	"cajmps2" => $cajmps2,
	"cajmps3" => $cajmps3,
	"cajtps" => $cajtps,
	"cajtps1" => $cajtps1,
	"cajtps2" => $cajtps2,
	"cajtps3" => $cajtps3,
	"rpc" => $rpc,
	"rpc1" => $rpc1,
	"rpc2" => $rpc2,
	"rpc3" => $rpc3
];

echo json_encode($respuesta_ajax);