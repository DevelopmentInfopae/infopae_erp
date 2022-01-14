<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

$data = [];
$mes = "";
$dia = "";
$semanaActual = "";
$sede = "";
$nivel = "";
$grado = "";
$grupo = "";
$anno = $annoAsistencia2D; 

if(isset($_POST["mes"]) && $_POST["mes"] != ""){ $mes = mysqli_real_escape_string($Link, $_POST["mes"]); }else{ $mes = $mesAsistencia; }
if(isset($_POST["dia"]) && $_POST["dia"] != ""){ $dia = mysqli_real_escape_string($Link, $_POST["dia"]); }else{ $dia = $diaAsistencia; }
$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);
$semanaActual = (isset($_POST["semanaActual"]) && $_POST["semanaActual"] != "") ? mysqli_real_escape_string($Link, $_POST["semanaActual"]) : "";
$sede = (isset($_POST["sede"]) && $_POST["sede"] != "") ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$complemento = (isset($_POST["complemento"]) && $_POST["complemento"] != "") ? mysqli_real_escape_string($Link, $_POST["complemento"]) : "";
$nivel = (isset($_POST["nivel"]) && $_POST["nivel"] != "") ? mysqli_real_escape_string($Link, $_POST["nivel"]) : "";
$grado = (isset($_POST["grado"]) && $_POST["grado"] != "") ? mysqli_real_escape_string($Link, $_POST["grado"]) : "";
$grupo = (isset($_POST["grupo"]) && $_POST["grupo"] != "") ? mysqli_real_escape_string($Link, $_POST["grupo"]) : "";
$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;

$consultaPlanillaDias = " SELECT D1,D2,D3,D4,D5,D6,D7,D8,D9,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22,D23,D24,D25,D26,D27,D28,D29,D30,D31 FROM planilla_dias WHERE mes = $mes ";
$respuestaPlanillaDias = $Link->query($consultaPlanillaDias);
if ($respuestaPlanillaDias->num_rows > 0) {
	$dataPlanillaDias = $respuestaPlanillaDias->fetch_assoc();
	$planillaDias = $dataPlanillaDias;
}

$D = array_search($dia, $planillaDias);

$consulta = "SELECT s.tipo_doc, 
					s.num_doc, 
					CONCAT(s.ape1, ' ', s.ape2, ' ', s.nom1, ' ', s.nom2) AS nombre, 
					g.nombre AS grado, 
					s.nom_grupo AS grupo,
					CASE
    					WHEN e.$D = 1 THEN '1'
    					WHEN e.$D = 0 THEN '0'
    					ELSE '0'
					END AS D
				FROM suplentes$semanaActual s 
				LEFT JOIN grados g on g.id = s.cod_grado 
				LEFT JOIN entregas_res_$mes$periodoActual e on e.num_doc = s.num_doc
				INNER JOIN sedes$periodoActual sed on sed.cod_sede = s.cod_sede
				WHERE 1 = 1 
				AND s.cod_sede = '$sede'
				AND sed.jornada != '6'
				 ";
// exit(var_dump($consulta));
if($nivel == 1 ){
	$consulta .= " and s.cod_grado < \"6\" ";
} else if($nivel == 2 ){
	$consulta .= " and s.cod_grado > \"5\" ";
}
if($grado != "" ){
	$consulta .= " and s.cod_grado = $grado ";
}
if($grupo != "" ){
	$consulta .= " and s.nom_grupo = $grupo ";
}
$consulta .= " ORDER BY s.cod_grado, s.nom_grupo, s.ape1, s.ape2, s.nom1, s.nom2 ";
// echo $consulta;
$resultado = $Link->query($consulta);
if($resultado){
	if($resultado->num_rows > 0){
	  	while($row = $resultado->fetch_assoc()) {
	    	$data[] = $row;
	  	}
	}

	$output = [
	  	'sEcho' => 1,
	  	'iTotalRecords' => count($data),
	  	'iTotalDisplayRecords' => count($data),
	  	'aaData' => $data
	];

	echo json_encode($output);
}