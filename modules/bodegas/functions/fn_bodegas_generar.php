<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';


  // Obtener el listado de sedes.
  $consulta1 = "SELECT cod_sede AS codigoSede, nom_sede AS nombreSede, direccion AS direccionSede, telefonos AS telefonoSede, cod_mun_sede AS ciudadSede FROM sedes". $_SESSION['periodoActual'] .";";
  $resultado1 = $Link->query($consulta1) or die (mysqli_error($Link));
  if ($resultado1->num_rows > 0)
  {
    // Obtener listado de código de bodegas.
    $arrayBodegas = [];
    $consulta = "SELECT ID AS codigoBodega FROM bodegas;";
    $resultado = $Link->query($consulta) or die ('Error en consulta Bodegas: ' . mysqli_error($Link));
    if ($resultado->num_rows > 0)
    {
      while($registros = $resultado->fetch_assoc())
      {
        $arrayBodegas[] = $registros['codigoBodega'];
      }
    }

    // Validar que el código de sede no esté creado como bodega.
    $valoresConsulta = '';
    while($registros1 = $resultado1->fetch_assoc())
    {
      if (!in_array($registros1['codigoSede'], $arrayBodegas))
      {
        $valoresConsulta .= "('". $registros1['codigoSede'] ."', '". $registros1['nombreSede'] ."', '". $registros1['direccionSede'] ."', '". $registros1['telefonoSede'] ."', '". $registros1['ciudadSede'] ."', ''), ";
      }
    }

    if ($valoresConsulta != '')
    {
      $consulta2 = "INSERT INTO bodegas (ID, NOMBRE, DIRECCION, TELEFONO, CIUDAD, RESPONSABLE) VALUES ". trim($valoresConsulta, ", ");
      $resultado2 = $Link->query($consulta2) or die ('Error en consulta Sedes: ' . mysqli_error($Link));
      if ($resultado2)
      {
        // Registro de la bitácora
        $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '59', 'Generó bodegas de acuerdo a las sedes registradas.')";
        $Link->query($consultaBitacora) or die ('Error en consulta Bitácora: ' . mysqli_error($Link));

        $respuestaAJAX = [
          'estado' => 1,
          'mensaje' => 'Las bodegas se generaron exitosamente.'
        ];
      }
    }
    else
    {
      $respuestaAJAX = [
        'estado' => 0,
        'mensaje' => 'Todas las sedes ya han sido generadas como bodegas.'
      ];
    }
  }
  else
  {
    $respuestaAJAX = [
      'estado' => 0,
      'mensaje' => 'No existen sedes. Por favor registrarlos para continuar el proceso.'
    ];
  }
  echo json_encode($respuestaAJAX);