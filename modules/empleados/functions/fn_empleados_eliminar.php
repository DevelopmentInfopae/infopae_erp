<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $empleado = (isset($_POST['empleado']) && $_POST['empleado'] != '') ? mysqli_real_escape_string($Link, $_POST['empleado']) : '';

  $consulta = "DELETE FROM empleados WHERE ID = '$empleado'";
  $resultado = $Link->query($consulta) or die ('Error al eliminar empleado: '. mysqli_error($Link));
  if ($resultado)
  {
    // Registro de la bitácora
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '64', 'Eliminó el empleado <strong>$nombreCompleto</strong>')";
    $Link->query($consultaBitacora) or die (mysqli_error($Link));

    $respuestaAJAX = [
      'estado' => 1,
      'mensaje' => 'El empleado ha sido eliminado exitosamente'
    ];
  }
  else
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'El empleado NO ha eliminado creado.'
    ];
  }

  echo json_encode($respuestaAJAX);