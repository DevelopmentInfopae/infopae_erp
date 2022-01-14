<?php
require_once 'fn_fecha_asistencia.php';
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

$mesTablaAsistencia = $mes;
$annoTablaAsistencia = $anno;

include 'fn_validar_existencias_tablas.php';

$consulta = "SELECT f.tipo_doc, f.num_doc, CONCAT(f.ape1, ' ', f.ape2, ' ', f.nom1, ' ', f.nom2) AS nombre, g.nombre AS grado, f.nom_grupo AS grupo, a.asistencia, a.repite, a.consumio, a.repitio FROM focalizacion$semanaActual f LEFT JOIN grados g ON g.id = f.cod_grado left join asistencia_det$mes$anno a on f.tipo_doc = a.tipo_doc and f.num_doc = a.num_doc WHERE 1 = 1 and a.dia = $dia ";
if($sede != "" ){
	$consulta .= " and f.cod_sede = $sede ";
}
if($complemento != "" ){
	$consulta .= " and f.Tipo_complemento = \"$complemento\" ";
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

if($resultado){
	if($resultado->num_rows > 0){
		$banderaRegistros++;
		while($row = $resultado->fetch_assoc()) {
		$data[] = $row;
		}
	}
}
// echo($consulta)

