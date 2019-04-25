<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// Declaración de variables.
$data = [];
$semanaActual = "";
$sede = "";
$grado = "";
$grupo = "";
$fecha = date("Y-m-d H:i:s");
$anno = date("y"); 
$mes = date("m");
$dia = intval(date("d"));

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

// var_dump($_POST);

$semanaActual = (isset($_POST["semanaActual"]) && $_POST["semanaActual"] != "") ? mysqli_real_escape_string($Link, $_POST["semanaActual"]) : "";

$sede = (isset($_POST["sede"]) && $_POST["sede"] != "") ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";



$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

$grado = (isset($_POST["grado"]) && $_POST["grado"] != "") ? mysqli_real_escape_string($Link, $_POST["grado"]) : "";

$grupo = (isset($_POST["grupo"]) && $_POST["grupo"] != "") ? mysqli_real_escape_string($Link, $_POST["grupo"]) : "";











// $consulta = "SELECT f.tipo_doc, f.num_doc, CONCAT(f.ape1, ' ', f.ape2, ' ', f.nom1, ' ', f.nom2) AS nombre, g.nombre AS grado, f.nom_grupo AS grupo, a.* FROM focalizacion$semanaActual f left join grados g on g.id = f.cod_grado left join Asistencia$mes$anno a on a.tipo_doc = f.tipo_doc and a.num_doc = f.num_doc WHERE 1 = 1 AND f.cod_sede = $sede and a.dia = \"$dia\" and a.mes = \"$mes\"ORDER BY f.cod_grado , f.nom_grupo , f.ape1 ";





$consulta = "SELECT f.tipo_doc, f.num_doc, CONCAT(f.ape1, ' ', f.ape2, ' ', f.nom1, ' ', f.nom2) AS nombre, g.nombre AS grado, f.nom_grupo AS grupo, a.asistencia, a.repite, a.consumio, a.repitio FROM focalizacion$semanaActual f LEFT JOIN grados g ON g.id = f.cod_grado left join Asistencia$mes$anno a on f.tipo_doc = a.tipo_doc and f.num_doc = a.num_doc and a.dia = $dia WHERE 1 = 1  ";

if($sede != "" ){
	$consulta .= " and f.cod_sede = $sede ";
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