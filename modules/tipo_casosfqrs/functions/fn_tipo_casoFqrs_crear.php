<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';

$descripcionSinEspacios = trim($descripcion);
$caracteres = strlen($descripcionSinEspacios);

if ($caracteres == 0) {
  $respuestaAJAX = [
    'estado' => 0,
    'mensaje' => 'No se puede crear una descripción vacía.'
  ];
  exit(json_encode($respuestaAJAX));
}

// validacion mismo tipo y nombre
$validacion1 = "SELECT Descripcion, tipo FROM tipo_casosfqrs WHERE tipo = '".$tipo."' AND Descripcion = '".$descripcionSinEspacios."';";
$respuesta1 = $Link->query($validacion1) or die('Error al consultar el tipo y descripcion:' . mysqli_error($Link));
if ($respuesta1->num_rows > 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'Ya existe un registro con ese tipo y descripción.'
	];
	exit(json_encode($respuestaAJAX));
}

$sentenciaInsert = "INSERT INTO  tipo_casosfqrs (Descripcion, tipo) VALUES ('$descripcion','$tipo');";
$respuestaInsert = $Link->query($sentenciaInsert) or die('Error al insertar el tipo de caso FQRS'. mysqli_error($Link));
  if($respuestaInsert) {
  	$tipoString = '';
		if ($tipo == 'F') {
			$tipoString = 'Felicitaciones';
		}elseif ($tipo == 'Q') {
			$tipoString = 'Quejas';
		}elseif ($tipo == 'R') {
			$tipoString = 'Reclamos';
		}elseif ($tipo == 'S') {
			$tipoString = 'Solicitudes';
		}
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '74', 'Se creó el tipo de caso FQRS: <strong>".$descripcion."</strong> de tipo ".$tipoString."')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

      $respuestaAJAX = [
            'estado' => 1,
            'mensaje' => 'El tipo caso FQRS se creó exitosamente.'
      ];
  }
  else
  {
      $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El tipo caso FQRS NO se creó exitosamente.'
      ];
  }

  echo json_encode($respuestaAJAX);