<th>Documento</th>
<th>Numero</th>
<th>Nombre suplente</th>
<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$mes = (isset($_POST['mes']) && ! empty($_POST['mes'])) ? $Link->real_escape_string($_POST["mes"]) : "";
	$semana = (isset($_POST['semana']) && ! empty($_POST['semana'])) ? $Link->real_escape_string($_POST["semana"]) : "";

	$consulta_dias_semana = "SELECT IF(LENGTH(DIA) > 1, DIA, CONCAT('0', DIA)) AS dia FROM planilla_semanas WHERE MES='$mes' AND semana = '$semana';";
	$respuesta_dias_semana = $Link->query($consulta_dias_semana) or die('Error al consultar dias_semana:'. $Link->error);
	if ($respuesta_dias_semana->num_rows > 0)
	{
		$columna = 1;
		while($registro_dias_semana = $respuesta_dias_semana->fetch_object())
		{
			$dia = $registro_dias_semana->dia;
			echo '<th class="text-center">
							<div>'. $dia .'</div>
							<div class="checkbox checkbox-success" style="padding-left: 7px;">
								<input type="checkbox" class="checkbox-header" name="checkbox-header_'.$columna.'" id="checkbox-header_'.$columna.'" data-columna="'. $columna .'" checked/>
								<label for="checkbox-header_'.$columna.'"></label>
							</div>
						</th>';
			$columna++;
		}

		if ($columna < 6)
		{
			for ($i=$columna; $i < 6 ; $i++)
			{
				echo '<th></th>';
			}
		}
	}

