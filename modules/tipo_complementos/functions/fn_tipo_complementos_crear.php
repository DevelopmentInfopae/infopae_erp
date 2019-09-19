<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $jornada = (isset($_POST['jornada']) && $_POST['jornada'] != '') ? mysqli_real_escape_string($Link, $_POST['jornada']) : '';
  $codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? strtoupper(mysqli_real_escape_string($Link, $_POST['codigo'])) : '';
  $valorRacion = (isset($_POST['valorRacion']) && $_POST['valorRacion'] != '') ? mysqli_real_escape_string($Link, $_POST['valorRacion']) : '';
  $descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';

  $consulta = "SELECT * FROM tipo_complemento WHERE CODIGO = '$codigo';";
  $resultado = $Link->query($consulta) or die('Error al consultar tipo de complemento: '. mysqli_error($consulta));
  if($resultado->num_rows > 0)
  {
  	$respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No es posible el crear el tipo complemento debido a que el código ya se encuentra registrado.'
    ];
    exit(json_encode($respuestaAJAX));
  }

  $consulta1 = "INSERT INTO tipo_complemento (CODIGO, DESCRIPCION, jornada, ValorRacion) VALUES ('$codigo', '$descripcion', '$jornada', '$valorRacion');";
  $resultado1 = $Link->query($consulta1) or die('Error al crear tipo de complemento: '. mysqli_error($Link));
  if($resultado1)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '27', 'Se creó el tipo de complemento: <strong>$codigo</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Tipo de complemento se creo exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Tipo de complemento NO se creo exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);