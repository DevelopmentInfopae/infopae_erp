<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';

// sentencia buscar para el llenar la bitacora 
$sentenciaBuscar = "SELECT Descripcion, tipo FROM tipo_casosfqrs WHERE id = '$id';";
$resSentenciaBuscar = $Link->query($sentenciaBuscar) or die('Error al consultar el tipo de caso FQRS '. mysqli_error($Link));
if ($resSentenciaBuscar->num_rows > 0) {
    $DataSentenciaBuscar = $resSentenciaBuscar->fetch_assoc();
    $descripcion = $DataSentenciaBuscar['Descripcion'];
    $tipo = $DataSentenciaBuscar['tipo'];
}

$sentenciaEliminar = "DELETE FROM tipo_casosfqrs WHERE id = '" .$id. "';";
$respuestaEliminar = $Link->query($sentenciaEliminar) or die('Error al eliminar el tipo de caso FQRS' . mysqli_error($Link));
if ($respuestaEliminar) {
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
	$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '76', 'Se eliminó el tipo de caso FQRS: <strong>".$descripcion."</strong> de tipo ".$tipoString."')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

      $respuestaAJAX = [
            'estado' => 1,
            'mensaje' => 'El tipo caso FQRS se eliminó exitosamente.'
      ];
}
else
	{
      $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El tipo caso FQRS NO se eliminó exitosamente.'
      ];
  	}
 echo json_encode($respuestaAJAX);  	