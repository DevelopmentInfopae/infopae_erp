<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? mysqli_real_escape_string($Link, $_POST['codigo']) : '';
  $consulta = "SELECT DESCRIPCION AS descripcion, EDADINICIAL AS edadInicial, EDADFINAL AS edadFinal FROM grupo_etario WHERE ID = '$codigo';";
  $resultado = $Link->query($consulta) or die('Error al consultar el grupo estario: '. mysqli_error($Link));
  if($resultado->num_rows > 0)
  {
    $registros = $resultado->fetch_assoc();
    $descripcion = $registros['descripcion'];
    $edadInicial = $registros['edadInicial'];
    $edadFinal = $registros['edadFinal'];
  }

  $consulta1 = "DELETE FROM grupo_etario WHERE ID = '$codigo';";
  $resultado1 = $Link->query($consulta1) or die('Error al eliminar el Grupo etario: '. mysqli_error($Link));
  if($resultado1)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '28', 'Eliminó el grupo etario con el rango: <strong>Edad inicial: $edadInicial</strong> a <strong>Edad final: $edadFinal</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Grupo etario se eliminó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Grupo etario NO se eliminó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);