<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$perido_actual = $_SESSION["periodoActual"];
$semana = $Link->real_escape_string($_POST["semana"]);
$estado = $Link->real_escape_string($_POST['estado']);
$numero_documento = $Link->real_escape_string($_POST["numero_documento"]);

// Determinar el mes del suplente en cuestión
$consulta_mes = "SELECT DISTINCT (MES) AS mes FROM planilla_semanas WHERE SEMANA = '$semana'";
$respuesta_mes = $Link->query($consulta_mes) or die('Error al consultar planilla_semanas: '. $Link->error);
if ($respuesta_mes->num_rows > 0)
{
	$mes_suplente = $respuesta_mes->fetch_assoc();
	$mes = $mes_suplente["mes"];
}


// Consulta que valida si el suplente ya se encuentra registrado en entrega_res.
$consulta_suplente_entrega = "SELECT * FROM entregas_res_$mes$perido_actual WHERE num_doc = '$numero_documento' AND tipo = 'S'";
$respuesta_suplente_entrega = $Link->query($consulta_suplente_entrega) or die("error al consulta entregas_res: ". $Link->error);
if ($respuesta_suplente_entrega->num_rows > 0)
{
	$respuesta_ajax = [
		"success" => 0,
		"message" => "No es posible cambiar el estado del suplente. Ya se encuentra registrado en entregas como suplente"
	];
	echo json_encode($respuesta_ajax);
	exit();
}

$consulta_cambiar_estado = "UPDATE suplentes$semana SET activo = '$estado' WHERE num_doc = '$numero_documento'";
$respuesta_cambiar_estado = $Link->query($consulta_cambiar_estado) or die("Error al actualizar suplente$semana: ". $Link->error);
if ($respuesta_cambiar_estado == TRUE)
{
	$respuesta_ajax = [
		"success" => 1,
		"message" => "El estado ha sido actualizado exitosamente."
	];
	echo json_encode($respuesta_ajax);
}
else
{
	$respuesta_ajax = [
		"success" => 0,
		"message" => "No fue posible actulizar el estado del suplente."
	];
	echo json_encode($respuesta_ajax);
}

