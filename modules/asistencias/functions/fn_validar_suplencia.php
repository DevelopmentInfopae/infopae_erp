<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$mes = $_POST["mes"];
$semana = $_POST["semana"];
$dia = $_POST["dia"];
$municipio = $_POST["municipio"];
$institucion = $_POST["institucion"];
$sede = $_POST["sede"];
$nivel = $_POST["nivel"];
$grado = $_POST["grado"];
$grupo = $_POST["grupo"];
$complemento = $_POST["complemento"];
$periodoActual = $_SESSION['periodoActual'];

$consultaNomSede = " SELECT nom_sede FROM sedes$periodoActual WHERE cod_sede = '$sede' ";
$respuestaNomSede = $Link->query($consultaNomSede) or die ('Error al consultar el nombre de la sede');
if ($respuestaNomSede->num_rows> 0) {
	$dataNomSede = $respuestaNomSede->fetch_assoc();
	$nomSede = $dataNomSede['nom_sede'];
}

$consultaValidacionExistencia = " SHOW TABLES LIKE 'suplentes$semana' ";
$respuestaValidacionExistencia = $Link->query($consultaValidacionExistencia);
if ($respuestaValidacionExistencia->num_rows > 0) {
	$consultaValidacionSede = " SELECT * FROM suplentes$semana WHERE cod_sede = $sede ";
	$respuestaValidacionSede = $Link->query($consultaValidacionSede);
	if ($respuestaValidacionSede->num_rows > 0) {
		$consultaValidacionSellado = " SELECT estado FROM asistencia_enc$mes$periodoActual WHERE dia = '$dia' AND semana = '$semana' AND mes = '$mes' AND cod_sede = '$sede' AND complemento = '$complemento' ";
		$respuestaValidacionSellado = $Link->query($consultaValidacionSellado);
		if ($respuestaValidacionSellado->num_rows > 0) {
			$dataValidacionSellado = $respuestaValidacionSellado->fetch_assoc();
			if ($dataValidacionSellado['estado'] == 2) {
				$consultaValidacionAsistencia = " SELECT det.asistencia FROM asistencia_det$mes$periodoActual det INNER JOIN asistencia_enc$mes$periodoActual enc ON det.dia = enc.dia WHERE enc.cod_sede = '$sede' AND enc.dia = '$dia' AND det.complemento = '$complemento' ";
				$respuestaValidacionAsistencia = $Link->query($consultaValidacionAsistencia);
				if ($respuestaValidacionAsistencia->num_rows > 0) {
					$consultaFocalizacion = " SELECT SUM(det.consumio) AS consumio, SUM(det.repite) AS repite, SUM(det.repitio) AS repitio FROM asistencia_det$mes$periodoActual det INNER JOIN focalizacion$semana foc ON foc.num_doc = det.num_doc WHERE foc.cod_sede = '$sede' AND det.dia = '$dia' AND det.complemento = '$complemento' "; 
					$respuestaConsumio = $Link->query($consultaFocalizacion);
					if ($respuestaConsumio->num_rows > 0) {
						$dataConsumio = $respuestaConsumio->fetch_assoc();
						$consumos = $dataConsumio['consumio'];
						$repite = $dataConsumio['repite'];
						$repitio = $dataConsumio['repitio'];
						if ($repite > 0  ||  $repitio > 0) {
							$resultadoAJAX = array(
								"estado" => 1,
								"mensaje" => " La sede $nomSede ya tiene repitentes consumiendo en el día $dia "
							);
						}else {
							$consultaCobertura = " SELECT $complemento AS cobertura FROM sedes_cobertura WHERE mes = '$mes' AND semana = '$semana' AND cod_sede = '$sede' ";
							$respuestaCobertura = $Link->query($consultaCobertura);
							if ($respuestaCobertura->num_rows > 0) {
								$dataCobertura = $respuestaCobertura->fetch_assoc();
								$cobertura = $dataCobertura['cobertura'];
							}
							if ($cobertura <= $consumos) {
								$resultadoAJAX = array(
									"estado" => 1,
									"mensaje" => "Cobertura Completa No se puede modificar. "
								);
							}else {
								$resultadoAJAX = array(
									"estado" => 0,
									"mensaje" => ""
								);
							}
						}
					}
				}else{
					$resultadoAJAX = array(
						"estado" => 1,
						"mensaje" => "No existe asistencia tomada para la sede $nomSede en el día $dia "
					);
				}
			}
		}else {
			$resultadoAJAX = array(
				"estado" => 1,
				"mensaje" => "No Existe asistencia Sellada de la sede $nomSede el día $dia "
			);
		}
	}else {
		$resultadoAJAX = array(
			"estado" => 1,
			"mensaje" => "No existen suplentes registrados en la sede $nomSede "
		);
	}
}else {
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "No existen suplentes registrados en la semana $semana "
	);
}
echo json_encode($resultadoAJAX);
