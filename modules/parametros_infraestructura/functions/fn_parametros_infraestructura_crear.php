<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$descripcion = (isset($_POST['descripcion']) && $_POST['descripcion'] != '') ? mysqli_real_escape_string($Link, $_POST['descripcion']) : '';
$descripcionSinEspacios = trim($descripcion);
$caracteres = strlen($descripcionSinEspacios);

if ($caracteres == 0) {
  $respuestaAJAX = [
    'estado' => 0,
    'mensaje' => 'No se puede crear una descripción vacía'
  ];
  exit(json_encode($respuestaAJAX));
}

// validacion para que no tenga mas de un registro la misma cantidad de horas 
$validacionNombre = "SELECT descripcion FROM parametros_infraestructura WHERE descripcion = '$descripcionSinEspacios';";
$resValidacionNombre = $Link->query($validacionNombre) or die('Error al consultar la descripcion '. mysqli_error($Link));
if ($resValidacionNombre->num_rows > 0) {
	$respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'Ya existe un registro para esa descripción'
      ];
      exit (json_encode($respuestaAJAX));
}

$sentenciaInsert = "INSERT INTO  parametros_infraestructura (descripcion) VALUES ('$descripcion');";
$respuestaInsert = $Link->query($sentenciaInsert) or die('Error al insertar el parámetro infraestructura'. mysqli_error($Link));
  if($respuestaInsert) {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '69', 'Se creó el parámetro infraestructura: <strong>".$descripcion."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

      $respuestaAJAX = [
            'estado' => 1,
            'mensaje' => 'El Parámetro Infraestructura se creó exitosamente.'
      ];
  }
  else
  {
      $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El Parámetro Infraestructura NO se creó exitosamente.'
      ];
  }

  echo json_encode($respuestaAJAX);