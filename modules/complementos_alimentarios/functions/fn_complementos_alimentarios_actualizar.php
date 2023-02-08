<?php
  	require_once '../../../db/conexion.php';
  	require_once '../../../config.php';
	// exit(var_dump($_POST));
  	$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
  	$codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? mysqli_real_escape_string($Link, $_POST['codigo']) : '';
  	$jornada = (isset($_POST['jornada']) && $_POST['jornada'] != '') ? mysqli_real_escape_string($Link, $_POST['jornada']) : '';
  	$valorRacion = (isset($_POST['valorRacion']) && $_POST['valorRacion'] != '') ? mysqli_real_escape_string($Link, $_POST['valorRacion']) : '';
  	$descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';
  	$numeroRaciones = (isset($_POST['numeroRaciones']) && $_POST['numeroRaciones'] != '') ? mysqli_real_escape_string($Link, $_POST['numeroRaciones']) : '';
  	$jornadaUnica = (isset($_POST['jornadaUnica']) && $_POST['jornadaUnica'] != '') ? mysqli_real_escape_string($Link, $_POST['jornadaUnica']) : '';

  	$con_editar = "UPDATE tipo_complemento SET 
												DESCRIPCION = '$descripcion', 
												valorRacion = '$valorRacion', 
												jornada = '$jornada',
												numero_raciones_contratadas = '$numeroRaciones',
												jornadaUnica = '$jornadaUnica'
											WHERE ID = '".$id."';"; 
											// exit(var_dump($con_editar));
  	$res_editar = $Link->query($con_editar) or die('Error al actualizar el Grupo etario: '. mysqli_error($Link));
  	if($res_editar){
    	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '55', 'Se actualizó el Tipo de complemento: <strong>".$codigo."</strong>')";
    	$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  		$respuestaAJAX = [
  			'estado' => 1,
  			'mensaje' => 'El Complemento alimentario se actualizó exitosamente.'
  		];
  	}
  	else {
  		$respuestaAJAX = [
  			'estado' => 0,
  			'mensaje' => 'El Complemento alimentario NO se actualizó exitosamente.'
  		];
  	}

  	echo json_encode($respuestaAJAX);