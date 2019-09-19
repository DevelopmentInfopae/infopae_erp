<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$semanas = "";
	$num_doc = $_POST['num_doc'];

	$resultado_focalizacion = $Link->query("SELECT table_name AS nombre_tabla FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name like 'suplentes%' ");
	if ($resultado_focalizacion->num_rows > 0) {
		while ($registro_focalizacion = $resultado_focalizacion->fetch_assoc()) {
			$resultado_estudiante = $Link->query("SELECT * FROM ".$registro_focalizacion['nombre_tabla']." WHERE num_doc = ".$num_doc.";");
			if ($resultado_estudiante->num_rows > 0) {
				$semanas.="Semana ".substr($registro_focalizacion['nombre_tabla'], 12, 2).", ";
			}
		}
	}



	if ($semanas != "") {
		$respuesta = '{"respuesta" : [{ "respuesta":"1", "semanas" : "'.trim($semanas, ", ").'"}]}';
		echo $respuesta;
	} else {
		// Consulta que retorna si el estudiante ya esxiste como suplente.
		$resultado_existe_suplente = $Link->query("SELECT num_doc  FROM suplentes WHERE num_doc = '$num_doc';");
		if ($resultado_existe_suplente->num_rows > 0) {
			echo $respuesta = '{"respuesta" : [{ "respuesta":"1", "semanas" : "Suplentes"}]}';
			exit;
		}


		$respuesta = '{"respuesta" : [{ "respuesta":"0"}]}';
		echo $respuesta;
	}