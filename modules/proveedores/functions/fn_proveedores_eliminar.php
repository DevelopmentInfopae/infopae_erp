<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $proveedor = (isset($_POST['proveedor']) && $_POST['proveedor'] != '') ? mysqli_real_escape_string($Link, $_POST['proveedor']) : '';
  $razonSocial = (isset($_POST['razonSocial']) && $_POST['razonSocial'] != '') ? mysqli_real_escape_string($Link, $_POST['razonSocial']) : '';

  $consulta = "DELETE FROM proveedores WHERE ID = '$proveedor'";
  $resultado = $Link->query($consulta) or die ('Error al eliminar proveedor: '. mysqli_error($Link));

  if ($resultado) {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '67', 'Eliminó el proveedor : <strong>$razonSocial</strong>')";
    $Link->query($consultaBitacora) or die (mysqli_error($Link));

    $respuestaAJAX = [
      'estado' => 1,
      'mensaje' => 'El proveedor ha sido eliminado exitosamente'
    ];
  } else {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'El proveedor NO ha eliminado creado.'
    ];
  }

  echo json_encode($respuestaAJAX);