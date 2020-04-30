<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	// Variables post
	$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST['mes']) : '';
	$dia = (isset($_POST['dia']) && $_POST['dia'] != '') ? mysqli_real_escape_string($Link, $_POST['dia']) : '';
	$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST['semana']) : '';
	$diaSemana = (isset($_POST['diaSemana']) && $_POST['diaSemana'] != '') ? mysqli_real_escape_string($Link, $_POST['diaSemana']) : '';

	// Consultar primera semana de contrato.
	$conPriSemCon="SELECT * FROM planilla_semanas ORDER BY ID ASC LIMIT 1;";
	$resPriSemCon=$Link->query($conPriSemCon);
	if($resPriSemCon->num_rows > 0){
		$regPriSemCon = $resPriSemCon->fetch_assoc();
		$primeraSemanaContrato = (int) $regPriSemCon['SEMANA'];
		$primerMesContrato = (int) $regPriSemCon['MES'];
		$primeraSemanaContratoAnio = date('W', mktime(0,0,0,$regPriSemCon['MES'],$regPriSemCon['DIA'],$regPriSemCon['ANO']));
	}
	else
	{
		$primeraSemanaContrato = 1;
		$primerMesContrato = $mes;
		$primeraSemanaContratoAnio = $semana;
	}

	// Validar si ya existe un registro en la fecha seleccionada.
	$mesCadena = (strlen($mes) == 1) ? '0'.$mes : $mes;
	$diaCadena = (strlen($dia) == 1) ? '0'.$dia : $dia;
	$conReg = "SELECT * FROM planilla_semanas WHERE MES = '$mesCadena' AND DIA = '$diaCadena';";
	$resReg = $Link->query($conReg);
	if($resReg->num_rows > 0)
	{
		$respuestaAJAX = [
			'estado' => 0,
			'mensaje' => 'No es posible asignar o modificar el menú para este día.'
		];
	}
	else
	{
		if ($mes < $primerMesContrato)
		{
			$respuestaAJAX = [
				'estado' => 0,
				'mensaje' => 'No es posible asignar o modificar el menú para este día.'
			];
		}
		else
		{
			// Calcula la diferencia de semanas en la primera semana de contrato y la semana donde se agregará el menú.
			$semanaContrato = $semana - $primeraSemanaContratoAnio;

			// Se suma la diferencia a la primera semana de contrato para saber la semana consecutiva del contrato.
			$semanaConsecutiva = $primeraSemanaContrato + $semanaContrato;
			$semanaConsecutivaCadena = (strlen($semanaConsecutiva) == 1) ? '0'.$semanaConsecutiva : $semanaConsecutiva;
			$semanaConsecutivaCompleta = $semanaConsecutivaCadena;
			if ($dia == 1 | $dia == 2 | $dia  == 3 | $dia == 4)
			{
				if ($dia == 1)
				{
					if ($diaSemana > 1)
					{
						$semanaConsecutivaCompleta = $semanaConsecutivaCadena . "b";
					}
				}
				if ($dia == 2)
				{
					if ($diaSemana > 2)
					{
						$semanaConsecutivaCompleta = $semanaConsecutivaCadena . "b";
					}
				}
				if ($dia == 3)
				{
					if ($diaSemana > 3)
					{
						$semanaConsecutivaCompleta = $semanaConsecutivaCadena . "b";
					}
				}
				if ($dia == 4)
				{
					if ($diaSemana > 4)
					{
						$semanaConsecutivaCompleta = $semanaConsecutivaCadena . "b";
					}
				}
			}

			// Consultar ciclo y menú de la semana consecutiva obtenida.
			$regCicloMenuSemana = '';
			$conCicloMenuSemana = "SELECT DISTINCT CICLO FROM planilla_semanas WHERE SEMANA LIKE '$semanaConsecutivaCadena%';";
			$resCicloMenuSemana=$Link->query($conCicloMenuSemana);
			if($resCicloMenuSemana->num_rows > 0){
				$regCicloMenuSemana = $resCicloMenuSemana->fetch_assoc();
				$regCicloMenuSemana = $regCicloMenuSemana['CICLO'];
			}

			$respuestaAJAX = [
				'estado' => 1,
				'datos' =>
				[
					'dia' => $dia,
					'mes' => $mes,
					'semana' => $semanaConsecutiva,
					'ciclo' => $regCicloMenuSemana,
					'semanaCompleta' => $semanaConsecutivaCompleta, 'd'=> $conCicloMenuSemana
				]
			];
		}

	}

	echo json_encode($respuestaAJAX);
