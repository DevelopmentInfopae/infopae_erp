<?php
	require_once '../../../db/conexion.php';
  	require_once '../../../config.php';

    $solucion = (isset($_POST['solucion']) && $_POST['solucion'] != '') ? mysqli_real_escape_string($Link, $_POST['solucion']) : '';
    $id_caso = (isset($_POST['id_caso']) && $_POST['id_caso'] != '') ? mysqli_real_escape_string($Link, $_POST['id_caso']) : '';

    $consulta = "UPDATE fqrs
				SET
				id_responsable = '". $_SESSION["idUsuario"] ."',
				solucion = '". $solucion ."',
				estado = '1',
				fecha_solucion = '". date("Y-m-d H:i:s") ."'
				WHERE ID = '". $id_caso ."';";
	$resultado = $Link->query($consulta) or die("Error al editar el caso ". $Link->error);

	if ($resultado == TRUE) {
		$respuestaAJAX = [
	      	'estado' => 1,
	      	'mensaje' => 'Se ha actualizado el caso exitosamente'
	    ];
	} else {
		$respuestaAJAX = [
	      	'estado' => 0,
	      	'mensaje' => 'Hubo un problema al editar caso.'
	    ];
	}

	echo json_encode($respuestaAJAX);