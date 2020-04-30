<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

$data = [];
$semanaActual = "";
$sede = "";
$grado = "";
$grupo = "";

$anno = $annoasistencia;

if(isset($_POST["mes"]) && $_POST["mes"] != ""){
	$mes = mysqli_real_escape_string($Link, $_POST["mes"]);
}else{
	$mes = $mesAsistencia;
}

if(isset($_POST["dia"]) && $_POST["dia"] != ""){
	$dia = mysqli_real_escape_string($Link, $_POST["dia"]);
}else{
	$dia = $diaAsistencia;
}

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

$semanaActual = (isset($_POST["semanaActual"]) && $_POST["semanaActual"] != "") ? mysqli_real_escape_string($Link, $_POST["semanaActual"]) : "";

$sede = (isset($_POST["sede"]) && $_POST["sede"] != "") ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$nivel = (isset($_POST["nivel"]) && $_POST["nivel"] != "") ? mysqli_real_escape_string($Link, $_POST["nivel"]) : "";
$grado = (isset($_POST["grado"]) && $_POST["grado"] != "") ? mysqli_real_escape_string($Link, $_POST["grado"]) : "";
$grupo = (isset($_POST["grupo"]) && $_POST["grupo"] != "") ? mysqli_real_escape_string($Link, $_POST["grupo"]) : "";



$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;
include 'fn_validar_existencias_tablas.php';



$consulta = "SELECT f.tipo_doc, f.num_doc, CONCAT(f.ape1, ' ', f.ape2, ' ', f.nom1, ' ', f.nom2) AS nombre, g.nombre AS grado, f.nom_grupo AS grupo, a.asistencia, a.repite, a.consumio, a.repitio FROM focalizacion$semanaActual f LEFT JOIN grados g ON g.id = f.cod_grado left join asistencia_det$mes$anno a on f.tipo_doc = a.tipo_doc and f.num_doc = a.num_doc and a.dia = $dia WHERE 1 = 1  ";

if($sede != "" ){
	$consulta .= " and f.cod_sede = $sede ";
}

if($nivel == 1 ){
	$consulta .= " and f.cod_grado < \"6\" ";
} else if($nivel == 2 ){
	$consulta .= " and f.cod_grado > \"5\" ";
}

if($grado != "" ){
	$consulta .= " and f.cod_grado = $grado ";
}

if($grupo != "" ){
	$consulta .= " and f.nom_grupo = $grupo ";
}

$consulta .= " order by f.cod_grado, f.nom_grupo, f.ape1 ";

//echo $consulta;

$resultado = $Link->query($consulta);
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