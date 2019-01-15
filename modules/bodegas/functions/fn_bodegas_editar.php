<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? mysqli_real_escape_string($Link, $_POST['codigo']) : '';
  $nombre = (isset($_POST['nombre']) && $_POST['nombre'] != '') ? mysqli_real_escape_string($Link, $_POST['nombre']) : '';
  $ciudad = (isset($_POST['ciudad']) && $_POST['ciudad'] != '') ? mysqli_real_escape_string($Link, $_POST['ciudad']) : '';
  $telefono = (isset($_POST['telefono']) && $_POST['telefono'] != '') ? mysqli_real_escape_string($Link, $_POST['telefono']) : '';
  $direccion = (isset($_POST['direccion']) && $_POST['direccion'] != '') ? mysqli_real_escape_string($Link, $_POST['direccion']) : '';
  $responsable = (isset($_POST['responsable']) && $_POST['responsable'] != '') ? mysqli_real_escape_string($Link, $_POST['responsable']) : '';

  // Actualizar la bodega.
	$consulta1 = "UPDATE bodegas SET NOMBRE = '$nombre', DIRECCION = '$direccion', TELEFONO = '$telefono', CIUDAD = '$ciudad', RESPONSABLE = '$responsable' WHERE ID = '$codigo';";
	$resultado1 = $Link->query($consulta1) or die(mysqli_error($Link));
	if ($resultado1)
	{
		// Registro de la bitácora
		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '60', 'Actualizó la bodega <strong>$nombre</strong>')";
		$Link->query($consultaBitacora) or die (mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'La bodega se ha actualizado con éxito'
  	];
	}

  echo json_encode($respuestaAJAX);