<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
$sentencia1 = "SELECT Entidad FROM nomina_entidad WHERE Id = '$id';";
$resultado = $Link->query($sentencia1) or die('Error al consultar la nómina Entidad'. mysqli_error($Link));
if($resultado->num_rows > 0)
  {
    $registros = $resultado->fetch_assoc();
    $nombre = $registros['Entidad'];
  }

$sentencia2 = "DELETE FROM nomina_entidad WHERE Id = '$id';";
$resultado2 = $Link->query($sentencia2) or die('Error al eliminar la nómina entidad '. mysqli_error($Link));
if($resultado2)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '59', 'Eliminó la nómina entidad: $nombre')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'La Nómina Entidad se eliminó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'La Nómina Entidad NO se eliminó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);