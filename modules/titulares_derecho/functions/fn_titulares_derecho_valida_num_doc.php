<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';


$num_doc = $_POST['num_doc'];

$semanas="";

$consultarFocalizacion = "SELECT table_name AS tabla FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name like 'focalizacion%' ";
$resultadoFocalizacion = $Link->query($consultarFocalizacion);
if ($resultadoFocalizacion->num_rows > 0) {
	while ($focalizacion = $resultadoFocalizacion->fetch_assoc()) { 
		$validaEstudiante = "SELECT * FROM ".$focalizacion['tabla']." WHERE num_doc = ".$num_doc."";
		$resultadoValidaEstudiante = $Link->query($validaEstudiante);
		if ($resultadoValidaEstudiante->num_rows > 0) {
			$semanas.="Semana ".substr($focalizacion['tabla'], 12, 2).", ";
		}
	}
} 

if ($semanas != "") {
	$respuesta = '{"respuesta" : [{ "respuesta":"1", "semanas" : "'.trim($semanas, ", ").'"}]}';
	echo $respuesta;
} else {
	$respuesta = '{"respuesta" : [{ "respuesta":"0"}]}';
	echo $respuesta;
}

 ?>