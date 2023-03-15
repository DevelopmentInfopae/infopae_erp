<option value="">seleccione</option>
<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$mes = (isset($_POST['mes']) && ! empty($_POST['mes'])) ? $Link->real_escape_string($_POST["mes"]) : "";

	$consulta_semanas = "SELECT DISTINCT ps.semana AS codigo, 
								(SELECT count(*) FROM planilla_semanas WHERE semana = ps.semana) AS cantidad_dias  
							FROM planilla_semanas ps WHERE MES_ENTREGAS = '$mes' ORDER BY semana ASC";
	$respuesta_consulta_semanas = $Link->query($consulta_semanas) or die('Error al consulta las semanas: '. $Link->error);
	if(! empty($respuesta_consulta_semanas->num_rows))
	{
		while($semana = $respuesta_consulta_semanas->fetch_object())
		{
			$codigo = $semana->codigo;
			$cantidad_dias = $semana->cantidad_dias;
			echo '<option value="' . $codigo .'" data-cantidad_dias="'. $cantidad_dias .'"> Semana '. $codigo .'</option>';
		}
	}
