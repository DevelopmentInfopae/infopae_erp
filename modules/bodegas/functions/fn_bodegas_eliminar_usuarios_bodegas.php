<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $valoresConsulta = '';
  $usuarioBodega = (isset($_POST['usuarioBodega']) && $_POST['usuarioBodega'] != '') ? $_POST['usuarioBodega'] : '';

  if ($usuarioBodega != '')
  {
    foreach ($usuarioBodega as $i => $bodega)
    {
      $valoresConsulta .= "(". $bodega ."), ";
    }

    $consulta = "DELETE FROM usuarios_bodegas WHERE (ID) IN (". trim($valoresConsulta, ', ') .");";
    $resultado = $Link->query($consulta) or die('Error consulta usuarios_bodegas: '. mysqli_query($Link));
    if ($resultado)
    {
      $respuestaAJAX = [
        'estado' => 1,
        'mensaje' => 'Las bodegas fueron eliminadas con exito.'
      ];
    }
  }
  else
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No se ha seleccionado ninguna bodega para continuar con la eliminación.'
    ];
  }

  echo json_encode($respuestaAJAX);