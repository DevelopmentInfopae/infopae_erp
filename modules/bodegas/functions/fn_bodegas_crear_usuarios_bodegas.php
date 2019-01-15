<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $condicionConsulta = '';
  $usuario = (isset($_POST['usuario']) && $_POST['usuario'] != '') ? mysqli_real_escape_string($Link, $_POST['usuario']) : '';
  $municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST['municipio']) : '';
  $bodegaSalida = (isset($_POST['bodegaSalida']) && $_POST['bodegaSalida'] != '') ? mysqli_real_escape_string($Link, $_POST['bodegaSalida']) : '';
  $bodegaEntrada = (isset($_POST['bodegaEntrada']) && $_POST['bodegaEntrada'] != '') ? mysqli_real_escape_string($Link, $_POST['bodegaEntrada']) : '';

  // Validar si existen la bodega de entrada seleccionada.
  $condicionBodegaEntrada = ($bodegaEntrada != '') ? " AND ID = '$bodegaEntrada'" : '';
  // validar si existe el mucipio seleccionado.
  $condicionMunicipio = ($municipio != '') ? " AND CIUDAD = '$municipio'" : '';

  $consulta = "SELECT ID AS codigoBodega FROM bodegas WHERE ID != '$bodegaSalida'". $condicionMunicipio . $condicionBodegaEntrada;
  $resultado = $Link->query($consulta) or die('Error consulta bodegas: '. mysqli_error($Link));
  if ($resultado->num_rows > 0)
  {
    $valoresCrearUsuarioBodegas = '';
    while ($registros = $resultado->fetch_assoc())
    {
      $valoresCrearUsuarioBodegas .= "('$usuario','$bodegaSalida','". $registros['codigoBodega'] ."'), ";
    }

    $consulta1 = "INSERT IGNORE INTO usuarios_bodegas (USUARIO, COD_BODEGA_ENTRADA, COD_BODEGA_SALIDA) VALUES " . trim($valoresCrearUsuarioBodegas, ', ');
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