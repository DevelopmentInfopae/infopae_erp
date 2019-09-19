<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Variables.
  $condicionConsulta = '';
  $usuario = (isset($_POST['usuario']) && $_POST['usuario'] != '') ? mysqli_real_escape_string($Link, $_POST['usuario']) : '';
  $municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST['municipio']) : '';
  $bodegaSalida = (isset($_POST['bodegaSalida']) && $_POST['bodegaSalida'] != '') ? mysqli_real_escape_string($Link, $_POST['bodegaSalida']) : '';
  $bodegaEntrada = (isset($_POST['bodegaEntrada']) && $_POST['bodegaEntrada'] != '') ? mysqli_real_escape_string($Link, $_POST['bodegaEntrada']) : '';

  // Consulta que retorna el codigo de la bodega que no sea la misma de salida.
  $consulta = "SELECT ID AS codigoBodega FROM bodegas WHERE 1";
  $consulta .= ($bodegaEntrada != "") ? " AND ID = '$bodegaEntrada'" : "";
  $consulta .= ($bodegaSalida != "") ? " AND ID != '$bodegaSalida'" : "";
  $consulta .= ($municipio != "") ? " AND CIUDAD = '$municipio'" : "";

  $resultado = $Link->query($consulta) or die('Error consulta bodegas: '. mysqli_error($Link));
  if ($resultado->num_rows > 0)
  {
    $valoresCrearUsuarioBodegas = '';
    while ($registros = $resultado->fetch_assoc())
    {
      $valoresCrearUsuarioBodegas .= "('$usuario','$bodegaSalida','". $registros['codigoBodega'] ."'), ";
    }

    $consulta1 = "INSERT IGNORE INTO usuarios_bodegas (USUARIO, COD_BODEGA_SALIDA, COD_BODEGA_ENTRADA) VALUES " . trim($valoresCrearUsuarioBodegas, ', ');
    $respuesta1 = $Link->query($consulta1) or die('Error consulta usuarios_bodegas: '. mysqli_error($Link));
    if ($respuesta1)
    {
      $respuestaAJAX = [
        'estado' => 1,
        'mensaje' => 'Las bodegas fueron asignadas exitosamente.'
      ];
    }
  }
  else
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No existe bodegas para los filtros seleccionados.'
    ];
  }

  echo json_encode($respuestaAJAX);