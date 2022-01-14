<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
$descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';

$descripcionSinEspacios = trim($descripcion);
$caracteres = strlen($descripcionSinEspacios);

if ($caracteres == 0) {
  $respuestaAJAX = [
    'estado' => 0,
    'mensaje' => 'No se puede editar con una descripción vacía.'
  ];
  exit(json_encode($respuestaAJAX));
}

$validacion1 = "SELECT Descripcion, tipo FROM tipo_casosfqrs WHERE tipo = '".$tipo."' AND Descripcion = '".$descripcionSinEspacios."'AND id != '".$id."';";
// exit(var_dump($validacion1));
$respuesta1 = $Link->query($validacion1) or die('Error al consultar el tipo y descripcion:' . mysqli_error($Link));
if ($respuesta1->num_rows > 0) {
	$respuestaAJAX = [
		'estado' => 0,
		'mensaje' => 'Ya existe un registro con ese tipo y descripción'
	];
	exit(json_encode($respuestaAJAX));
}

$sentenciaUpdate = "UPDATE tipo_casosfqrs SET Descripcion ='" .$descripcion. "', tipo ='" .$tipo. "' WHERE id = '" .$id. "'; ";
$respuestaUpdate = $Link->query($sentenciaUpdate)or die('Error al actualizar el tipo de caso' . mysqli_error($Link));
if ($respuestaUpdate) {
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
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '75', 'Se actualizó el tipo de caso FQRS: <strong>".$descripcion."</strong> de tipo ".$tipoString."')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));	

    $respuestaAJAX = [
    	'estado' => 1,
    	'mensaje' => 'El tipo caso FQRS se actualizó exitosamente.'
    ];
}else
{
    $respuestaAJAX = [
        'estado' => 0,
        'mensaje' => 'El tipo caso FQRS NO se actualizó exitosamente.'
    ];
}

echo json_encode($respuestaAJAX);