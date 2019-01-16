<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? mysqli_real_escape_string($Link, $_POST['codigo']) : '';
  $edadFinal = (isset($_POST['edadFinal']) && $_POST['edadFinal'] != '') ? mysqli_real_escape_string($Link, $_POST['edadFinal']) : '';
  $edadInicial = (isset($_POST['edadInicial']) && $_POST['edadInicial'] != '') ? mysqli_real_escape_string($Link, $_POST['edadInicial']) : '';
  $descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';

  if ($edadInicial > $edadFinal)
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'La <strong>edad Inicial</strong> no puede ser mayor a la <strong>edad Final</strong>. Por favor modifique las edades.'
  	];
  	exit (json_encode($respuestaAJAX));
  }
  elseif ($edadInicial == $edadFinal)
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'La <strong>edad Inicial</strong> no puede ser igual a la <strong>edad Final</strong>. Por favor modifique las edades.'
  	];
  	exit (json_encode($respuestaAJAX));
  }

  $consulta = "SELECT EDADINICIAL AS edadInicial, EDADFINAL AS edadFinal FROM grupo_etario WHERE ID <> $codigo;";
  $resultado = $Link->query($consulta) or die('Error al consultar edades de grupo etarios: '. mysqli_error($consulta));
  if($resultado->num_rows > 0)
  {
  	while($registro = $resultado->fetch_assoc())
  	{
  		// Se valida que el rango de fechas ingresadas no se encuentre asignada.
  		if(($edadInicial >= $registro['edadInicial'] && $edadInicial <= $registro['edadFinal'])  )
  		{
  			$respuestaAJAX = [
		  		'estado' => 0,
		  		'mensaje' => 'No es posible agregar la <strong>edad inicial</strong> debido a que ya existe dentro de un rango de grupo etario.'
		  	];
		  	exit (json_encode($respuestaAJAX));
  		}
  		elseif(($edadFinal >= $registro['edadInicial'] && $edadFinal <= $registro['edadFinal'])  )
  		{
  			$respuestaAJAX = [
		  		'estado' => 0,
		  		'mensaje' => 'No es posible agregar la <strong>edad final</strong> debido a que ya existe dentro de un rango de grupo etario.'
		  	];
		  	exit (json_encode($respuestaAJAX));
  		}
  	}
  }

  $consulta1 = "UPDATE grupo_etario SET DESCRIPCION = '$descripcion', EDADINICIAL = '$edadInicial', EDADFINAL = '$edadFinal' WHERE ID = '$codigo';";
  $resultado1 = $Link->query($consulta1) or die('Error al actualizar el Grupo etario: '. mysqli_error($Link));
  if($resultado1)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Se actualizó el grupo etario con el rango: <strong>Edad inicial: $edadInicial</strong> a <strong>Edad final: $edadFinal</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Grupo etario se actualizó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Grupo etario NO se actualizó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);