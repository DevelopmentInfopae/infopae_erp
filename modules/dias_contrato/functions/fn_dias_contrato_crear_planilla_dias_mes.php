<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	// Variables post
	$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST['mes']) : ''; //FORMATO : 9
	$mesCompleto = (strlen($mes) == 1) ? '0' . $mes : $mes;

	// Consultar si existen registros en planilla semana del mes seleccionado.
	$consulta0 = "SELECT * FROM planilla_semanas WHERE MES = '$mesCompleto';";
	$resultado0 = $Link->query($consulta0);
	if ($resultado0->num_rows == 0)
	{
		$respuestaAjax = [
			'estado' => 0,
			'mensaje'=> 'No existe ningun registro para almacenar en este mes. Por favor asigne los menus correpondientes.'
		];
	}
	else
	{
		// Consultar si existen registros en planilla semana del mes seleccionado.
		$consulta1 = "SELECT * FROM planilla_dias WHERE mes = '$mesCompleto';";
		$resultado1 = $Link->query($consulta1);
		if ($resultado1->num_rows > 0)
		{
			$respuestaAjax = [
				'estado' => 0,
				'mensaje'=> 'Ya se existen registrados asociados a este mes. Por favor proceda con el siguiente o el anterior mes.'
			];
		}
		else
		{
			$columna = 1;
			$camposConsulta = '';
			$valoresConsulta = '';
			while($registros0 = $resultado0->fetch_assoc())
			{
				$camposConsulta .= 'D'. $columna .', ';
				$valoresConsulta .= "'". $registros0 ['DIA']."', ";
				$columna++;
			}

			$consulta2 = "INSERT INTO planilla_dias (ano, mes, ". trim($camposConsulta, ', ') .") VALUES ('". $_SESSION['periodoActualCompleto'] ."', '$mesCompleto', ". trim($valoresConsulta, ', ') .")";
			$resultado2 = $Link->query($consulta2) or die(mysqli_error($Link));
			if($resultado2)
			{
				$respuestaAjax = [
					'estado' => 1,
					'mensaje'=> 'Los registros han sido guardados con éxito..'
				];

				// Registro de la Bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '27', 'Se agregaron los días contratados para el mes: <strong>". $mesCompleto ."</strong>')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

			}
		}
	}

	echo json_encode($respuestaAjax);