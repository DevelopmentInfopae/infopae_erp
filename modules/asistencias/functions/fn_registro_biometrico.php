<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$data = [];
$semanaActual = "";
$sede = "";
$grado = "";
$grupo = "";
$fecha = date("Y-m-d H:i:s");
$anno = date("y"); 

if(isset($_POST["mes"]) && $_POST["mes"] != ""){
	$mes = mysqli_real_escape_string($Link, $_POST["mes"]);
}else{
	$mes = "";
}

if(isset($_POST["dia"]) && $_POST["dia"] != ""){
	$dia = mysqli_real_escape_string($Link, $_POST["dia"]);
}else{
	$dia = "";
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






$consulta = " SELECT f.num_doc, CONCAT(f.nom1,' ',f.nom2,' ', f.ape1,' ', f.ape2) AS nombre, g.nombre as grado, f.nom_grupo AS grupo , DAY(br.fecha) AS dia, DATE_FORMAT(br.fecha, \"%d/%m/%Y %H:%i:%s\") AS fecha
FROM focalizacion$semanaActual f
LEFT JOIN grados g ON f.cod_grado = g.id
LEFT JOIN biometria b ON b.tipo_doc = f.tipo_doc AND b.num_doc = f.num_doc
LEFT JOIN biometria_reg br ON br.dispositivo_id = b.id_dispositivo AND br.usr_dispositivo_id = b.id_bioest
WHERE 1 = 1 AND f.cod_sede = $sede AND br.fecha IS NOT NULL ";



if($dia != ""){
	$consulta .= " AND DAY(br.fecha) = $dia ";
}
if($mes != ""){
	$consulta .= " AND MONTH(br.fecha) = $mes ";
}


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