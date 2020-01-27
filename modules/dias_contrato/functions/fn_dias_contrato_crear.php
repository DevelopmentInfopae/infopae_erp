<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	// Variables post
	$nombreDias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
	$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST['mes']) : '';
	$dia = (isset($_POST['dia']) && $_POST['dia'] != '') ? mysqli_real_escape_string($Link, $_POST['dia']) : '';
	$menu = (isset($_POST['menu']) && $_POST['menu'] != '') ? mysqli_real_escape_string($Link, $_POST['menu']) : '';
	$ciclo = (isset($_POST['ciclo']) && $_POST['ciclo'] != '') ? mysqli_real_escape_string($Link, $_POST['ciclo']) : '';
	$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST['semana']) : '';
	$diaSemana = (isset($_POST['diaSemana']) && $_POST['diaSemana'] != '') ? mysqli_real_escape_string($Link, $_POST['diaSemana']) : '';
	$semanaCompleta = (isset($_POST['semanaCompleta']) && $_POST['semanaCompleta'] != '') ? mysqli_real_escape_string($Link, $_POST['semanaCompleta']) : '';

	$consulta0 = "SELECT * FROM planilla_semanas WHERE SEMANA LIKE '$semanaCompleta%' AND MENU = '$menu';";
	$resultado0 = $Link->query($consulta0);
	if ($resultado0->num_rows > 0)
	{
		$respuestaAJAX = [
			'estado' => 0,
			'mensaje' => 'No es posible asignar el menú: '. $menu .' debido a que ya se almacenó para la semana seleccionada.'
		];
	}
	else
	{
		$mesCompleto = (strlen($mes) == 1) ? '0' . $mes : $mes;
		$consulta1 = "INSERT INTO planilla_semanas (ANO, MES, SEMANA, DIA, MENU, CICLO, NOMDIAS) VALUES ('". $_SESSION['periodoActualCompleto'] ."', '$mesCompleto', '$semanaCompleta', '$dia', '$menu', '$ciclo', '". $nombreDias[$diaSemana] ."');";
		$resultado1 = $Link->query($consulta1);
		if ($resultado1)
		{
			$respuestaAJAX = [
				'estado' => 1,
				'mensaje' => 'El registro se guardó exitosamente'
			];

			// Registro de la Bitácora
			$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date("Y-m-d H-i-s") ."', '". $_SESSION["idUsuario"] ."', '27', 'Se agregó el menú: ". $menu ." del ciclo: ". $ciclo ." al día: ". $dia ." de la semana: ". $semanaCompleta ."')";
			$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
		}
	}

	echo json_encode($respuestaAJAX);
