<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? mysqli_real_escape_string($Link, $_POST['codigo']) : '';
  $nombre = (isset($_POST['nombre']) && $_POST['nombre'] != '') ? mysqli_real_escape_string($Link, $_POST['nombre']) : '';
  $ciudad = (isset($_POST['ciudad']) && $_POST['ciudad'] != '') ? mysqli_real_escape_string($Link, $_POST['ciudad']) : '';
  $telefono = (isset($_POST['telefono']) && $_POST['telefono'] != '') ? mysqli_real_escape_string($Link, $_POST['telefono']) : '';
  $direccion = (isset($_POST['direccion']) && $_POST['direccion'] != '') ? mysqli_real_escape_string($Link, $_POST['direccion']) : '';
  $responsable = (isset($_POST['responsable']) && $_POST['responsable'] != '') ? mysqli_real_escape_string($Link, $_POST['responsable']) : '';

  // Validar que el código ingresado no exista en la BD.
  $consulta = "SELECT * FROM bodegas WHERE ID = '$codigo';";
  $resultado = $Link->query($consulta);
  if ($resultado->num_rows > 0)
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El código de bodega ingresado ya se encuentra registrado.'
  	];
  }
  else
  {
  	$consulta1 = "INSERT INTO bodegas (ID, NOMBRE, DIRECCION, TELEFONO, CIUDAD, RESPONSABLE) VALUES ('$codigo', '$nombre', '$direccion', '$telefono', '$ciudad', '$responsable');";
  	$resultado1 = $Link->query($consulta1) or die(mysqli_error($Link));
  	if ($resultado1)
  	{
  		// Registro de la bitácora
			$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '59', 'Creó la bodega <strong>$nombre</strong>')";
			$Link->query($consultaBitacora) or die (mysqli_error($Link));

	  	$respuestaAJAX = [
	  		'estado' => 1,
	  		'mensaje' => 'La bodega se ha creado con éxito'
	  	];
  	}
  }

  echo json_encode($respuestaAJAX);