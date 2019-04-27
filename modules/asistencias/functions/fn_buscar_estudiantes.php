<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// Declaración de variables.
$data = [];
$semanaActual = "";
$sede = "";
$nivel = "";
$grado = "";
$grupo = "";

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

//var_dump($_POST);

$semanaActual = (isset($_POST["semanaActual"]) && $_POST["semanaActual"] != "") ? mysqli_real_escape_string($Link, $_POST["semanaActual"]) : "";

$sede = (isset($_POST["sede"]) && $_POST["sede"] != "") ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";

$nivel = (isset($_POST["nivel"]) && $_POST["nivel"] != "") ? mysqli_real_escape_string($Link, $_POST["nivel"]) : "";

$grado = (isset($_POST["grado"]) && $_POST["grado"] != "") ? mysqli_real_escape_string($Link, $_POST["grado"]) : "";

$grupo = (isset($_POST["grupo"]) && $_POST["grupo"] != "") ? mysqli_real_escape_string($Link, $_POST["grupo"]) : "";


$banderaRegistros = 0;

include "fn_buscar_registros_asistencia.php"; 

if($banderaRegistros == 0){
	//var_dump($banderaRegistros);

	$consulta = " select f.tipo_doc, f.num_doc, concat(f.ape1, \" \", f.ape2, \" \", f.nom1, \" \", f.nom2) as nombre, g.nombre as grado, f.nom_grupo as grupo from focalizacion$semanaActual f left join grados g on g.id = f.cod_grado where 1=1 ";

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

	$resultado = $Link->query($consulta);
	if($resultado->num_rows > 0){
	  while($row = $resultado->fetch_assoc()) {
	    $data[] = $row;
	  }
	}

}

//echo "<br><br>$consulta<br><br>";

$output = [
  'sEcho' => 1,
  'iTotalRecords' => count($data),
  'iTotalDisplayRecords' => count($data),
  'banderaRegistros' => $banderaRegistros,
  'aaData' => $data
];

echo json_encode($output);