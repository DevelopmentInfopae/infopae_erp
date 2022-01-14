<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';
include 'fn_fecha_asistencia.php';

// Declaración de variables.
$data = [];
$mes = "";
$dia = "";
$semanaActual = "";
$sede = "";
$nivel = "";
$grado = "";
$grupo = "";
$anno = $annoAsistencia2D; 

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
$complemento = (isset($_POST["complemento"]) && $_POST["complemento"] != "") ? mysqli_real_escape_string($Link, $_POST["complemento"]) : "";
$nivel = (isset($_POST["nivel"]) && $_POST["nivel"] != "") ? mysqli_real_escape_string($Link, $_POST["nivel"]) : "";
$grado = (isset($_POST["grado"]) && $_POST["grado"] != "") ? mysqli_real_escape_string($Link, $_POST["grado"]) : "";
$grupo = (isset($_POST["grupo"]) && $_POST["grupo"] != "") ? mysqli_real_escape_string($Link, $_POST["grupo"]) : "";

$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;
include 'fn_validar_existencias_tablas.php';

$consulta = "SELECT f.tipo_doc, f.num_doc, CONCAT(f.ape1, ' ', f.ape2, ' ', f.nom1, ' ', f.nom2) AS nombre, g.nombre AS grado, f.nom_grupo AS grupo, a.*, r.num_doc AS favorito FROM focalizacion$semanaActual f left join grados g on g.id = f.cod_grado left join asistencia_det$mes$anno a on a.tipo_doc = f.tipo_doc and a.num_doc = f.num_doc left join repitentesfavoritos r ON r.num_doc = f.num_doc AND r.tipo_doc = f.tipo_doc WHERE 1 = 1 AND f.cod_sede = $sede AND f.Tipo_complemento = \"$complemento\" and a.asistencia = 1 and a.dia = \"$dia\" and a.mes = \"$mes\" ";
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
$consulta .= " ORDER BY r.num_doc desc, f.cod_grado asc , f.nom_grupo asc , f.ape1 asc ";

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