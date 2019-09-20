<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';

  $consulta = "SELECT (MAX(Id) + 1) AS idTipoDespacho FROM tipo_despacho WHERE Id <> '99'";
  $resultado = $Link->query($consulta);
  if($resultado->num_rows > 0)
  {
    $registro = $resultado->fetch_assoc();
    $id = $registro['idTipoDespacho'];
  }

  $consulta1 = "INSERT INTO tipo_despacho (Id, Descripcion) VALUES ('$id', '$descripcion');";
  $resultado1 = $Link->query($consulta1) or die('Error al crear Tipo vehiculo: '. mysqli_error($Link));
  if($resultado1)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '27', 'Se creó el Tipo de descpacho: <strong>$descripcion</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Tipo de despacho se creo exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Tipo de despacho NO se creo exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);