<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$municipio = isset($_POST["municipio"]) ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
	$opciones = '<option value="">seleccione</option>';

	$consulta = "SELECT i.codigo_inst, i.nom_inst FROM instituciones i WHERE cod_mun = '$municipio' ORDER BY i.nom_inst";
	$resultado = $Link->query($consulta) or die ($Link->error);
	if($resultado->num_rows > 0){
		while($row = $resultado->fetch_assoc()) {
			$opciones .= '<option value="'. $row['codigo_inst'] .'">'. $row['nom_inst'] .'</option>';
		}

		$respuestaAJAX = [
			"estado" => 1,
			"opciones" => $opciones,
			"mensaje" => "Instituciones cargados correctamente."
		];
	} else {
		$respuestaAJAX = [
			"estado" => 0,
			"opciones" => $opciones,
			"mensaje" => "No se encuentran instituciones para el mucipio seleccionado."
		];
	}

	echo json_encode($respuestaAJAX);