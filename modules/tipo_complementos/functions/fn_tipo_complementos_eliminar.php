<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
  $nombreMes = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

  $consulta = "SELECT * FROM tipo_complemento WHERE ID = '$id';";
  $resultado = $Link->query($consulta) or die('Error al consultar el grupo estario: '. mysqli_error($Link));
  if($resultado->num_rows > 0)
  {
    $registros = $resultado->fetch_assoc();
    $codigo = $registros['CODIGO'];
    $descripcion = $registros['DESCRIPCION'];
  }

  // Consultar cantidad las tablas de despachos.
  $consulta1 = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'despachos_det%' AND table_schema = DATABASE();";
  $resultado1 = $Link->query($consulta1) or die('Error al consultar tablas despachos_det: '. mysqli_error($Link));
  if ($resultado1->num_rows > 0)
  {
    while ($registros1 = $resultado1->fetch_assoc())
    {
      $consulta2 = "SELECT Num_Doc AS numeroDespacho FROM ". $registros1['table_name'] ." WHERE Id_GrupoEtario = '$id' GROUP BY Num_Doc LIMIT 1;";
      $resultado2 = $Link->query($consulta2) or die ('Error consulta '. $registros1['table_name'] .': ' . mysqli_error($Link));
      if ($resultado2->num_rows > 0)
      {
        $mesInsumos = str_replace($_SESSION['periodoActual'], '', str_replace('despachos_det', '', $registros1['table_name']));
        $registros2 = $resultado2->fetch_assoc();
        $respuestaAJAX = [
          'estado' => 0,
          'mensaje' => 'No es posible eliminar el tipo de complemento debido a que se encuentra asociado al despacho Número: '. $registros2['numeroDespacho'] .' para el mes: '. $nombreMes[$mesInsumos]
        ];
        exit (json_encode($respuestaAJAX));
      }
    }
  }

  // Consultar cantidad las tablas de entregas_res.
  $consulta1 = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'entregas_res_%' AND table_schema = DATABASE();";
  $resultado1 = $Link->query($consulta1) or die('Error al consultar tablas entregas_res: '. mysqli_error($Link));
  if ($resultado1->num_rows > 0)
  {
    while ($registros1 = $resultado1->fetch_assoc())
    {
      $consulta2 = "SELECT id FROM ". $registros1['table_name'] ." WHERE (tipo_complem1 = '". $codigo ."' OR tipo_complem2 = '". $codigo ."' OR tipo_complem3 = '". $codigo ."' OR tipo_complem4 = '". $codigo ."' OR tipo_complem1 = '". $codigo ."') LIMIT 1;";
      $resultado2 = $Link->query($consulta2) or die ('Error consulta '. $registros1['table_name'] .': ' . mysqli_error($Link));
      if ($resultado2->num_rows > 0)
      {
        $mesInsumos = str_replace($_SESSION['periodoActual'], '', str_replace('entregas_res_', '', $registros1['table_name']));
        $registros2 = $resultado2->fetch_assoc();
        $respuestaAJAX = [
          'estado' => 0,
          'mensaje' => 'No es posible eliminar el tipo de complemento debido a que se encuentra asociado a las entregas del mes: '. $nombreMes[$mesInsumos]
        ];
        exit (json_encode($respuestaAJAX));
      }
    }
  }

  $consulta1 = "DELETE FROM tipo_complemento WHERE ID = '$id';";
  $resultado1 = $Link->query($consulta1) or die('Error al eliminar el Tipo de complemento: '. mysqli_error($Link));
  if($resultado1)
  {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '28', 'Eliminó el tipo de complemento: <strong>$codigo</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El Tipo de complemento se eliminó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El Tipo de complemento NO se eliminó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);