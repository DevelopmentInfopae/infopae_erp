<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

$log = "";
$respuesta = "";

$semanas = array();
$consulta = " select distinct semana from planilla_semanas where mes = '$mes' order by semana asc ";
// echo $consulta;
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$semanas[] = $row['semana'];
	}// Termina el while
}//Termina el if que valida que si existan resultados
//var_dump($semanas);

// Buscando la existencia de tablas de prorización
$consulta = " SELECT TABLE_NAME AS tabla FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$Database' and TABLE_NAME LIKE 'priorizacion%' ";
//var_dump($consulta);
$resultado = $Link->query($consulta) or die ('Unable to execute query - Buscando Meses '. mysqli_error($Link));
$tablasPriorizacion = array();
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()) {
		$tablasPriorizacion[] =  substr($row['tabla'],12);
	}
}
//var_dump($tablasPriorizacion);

// Se va a revizar cada tabla de prirización
$semanasMostrar = array();
foreach ($semanas as $semana) {
	if (in_array($semana, $tablasPriorizacion)) {
		//echo "<br>Existe la tabla de priorización<br>";
		$consulta = " select * from priorizacion$semana where cod_sede = '$sede' ";
		//var_dump($consulta);
		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			$semanasMostrar[] = $semana;
		}
	}
}

//var_dump($semanasMostrar);
$aux = 0;
foreach ($semanasMostrar as $semanaMostrar) {
	$respuesta .= "<div class=\"checkboxSemana\"><input type=\"checkbox\" class=\"semana\" id=\"semana$aux\" name=\"semana$aux\" value=\"$semanaMostrar\">
	<label>$semanaMostrar</label></div>";
	$aux++;
}
echo json_encode(array("log"=>$log, "respuesta"=>$respuesta));
