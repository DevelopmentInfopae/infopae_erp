<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? mysqli_real_escape_string($Link, $_POST['codigo']) : '';
  $jornada = (isset($_POST['jornada']) && $_POST['jornada'] != '') ? mysqli_real_escape_string($Link, $_POST['jornada']) : '';
  $valorRacion = (isset($_POST['valorRacion']) && $_POST['valorRacion'] != '') ? mysqli_real_escape_string($Link, $_POST['valorRacion']) : '';
  $descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';

  $con_codigo = "SELECT * FROM tipo_complemento WHERE CODIGO = '".$codigo."';";
  $res_codigo = $Link->query($con_codigo) or die('Error al consultar edades de grupo etarios: '. mysqli_error($Link));
  if($res_codigo->num_rows > 0) {
    $respuestaAJAX = [
     'estado' => 0,
     'mensaje' => 'El código de Complemento ya se encuentra registrado.'
    ];
    exit (json_encode($respuestaAJAX));
  }

  $con_crear = "INSERT INTO tipo_complemento (CODIGO, DESCRIPCION, Jornada, ValorRacion) VALUES ('$codigo', '$descripcion', '$jornada', '$valorRacion');";
  $res_crear = $Link->query($con_crear) or die('Error al crear Grupo etario: '. mysqli_error($Link));
  if($res_crear) {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '54', 'Se creó el Tipo de complemento con el código: <strong>".$codigo."</strong>')";
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