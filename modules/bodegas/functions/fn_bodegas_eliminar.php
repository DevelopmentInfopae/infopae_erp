<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';


  $codigo = (isset($_POST['codigo']) && $_POST['codigo'] != '') ? mysqli_real_escape_string($Link, $_POST['codigo']) : '';
  $nombreMes = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

  // Validar que el código de la bodega no esté asociado a ninguna ruta.
  $consulta = "SELECT rut.Nombre AS nombreRuta  FROM rutasedes ruts INNER JOIN rutas rut ON rut.ID = ruts.IDRUTA WHERE ruts.cod_Sede = '$codigo';";
  $resultado = $Link->query($consulta) or die ('Error consulta rutassedes: '. mysqli_error($Link));
  if ($resultado->num_rows != '')
  {
    $registros = $resultado->fetch_assoc();
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'No es posible eliminar la bodega debido a que se encuentra asociado a la ruta: '. $registros['nombreRuta'],
      'consulta' => $consulta
  	];
    echo json_encode($respuestaAJAX);
    exit;
  }


  // Consultar cantidad las tablas de insumos.
	$consulta1 = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'insumosmov%' AND table_name NOT LIKE 'insumosmovdet%' AND table_schema = DATABASE();";
	$resultado1 = $Link->query($consulta1) or die('Error consulta insumos: '. mysqli_error($Link));
	if ($resultado1->num_rows > 0)
	{
    while ($registros1 = $resultado1->fetch_assoc())
    {
      $consulta2 = "SELECT Numero AS numeroDespacho FROM ". $registros1['table_name'] ." WHERE BodegaOrigen = '$codigo' OR BodegaDestino = '$codigo';";
      $resultado2 = $Link->query($consulta2) or die ('Error consulta '. $registros1['table_name'] .': ' . mysqli_error($Link));
      if ($resultado2->num_rows > 0)
      {
        $mesInsumos = str_replace($_SESSION['periodoActual'], '', str_replace('insumosmov', '', $registros1['table_name']));
        $registros2 = $resultado2->fetch_assoc();
        $respuestaAJAX = [
          'estado' => 0,
          'mensaje' => 'No es posible eliminar la bodega debido a que se encuentra asociado al despacho de insumos Número: '. $registros2['numeroDespacho'] .' para el mes: '. $nombreMes[$mesInsumos]
        ];
        exit (json_encode($respuestaAJAX));
      }
    }
  }

  // Consultar cantidad las tablas de productos.
  $consulta4 = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'productosmov%' AND table_name NOT LIKE 'productosmovdet%' AND table_schema = DATABASE();";
  $resultado4 = $Link->query($consulta4) or die('Error consulta productos: '. mysqli_error($Link));
  if ($resultado4->num_rows > 0)
  {
    while ($registros4 = $resultado4->fetch_assoc())
    {
      $consulta5 = "SELECT Numero AS numeroProducto FROM ". $registros4['table_name'] ." WHERE BodegaOrigen = '$codigo' OR BodegaDestino = '$codigo';";
      $resultado5 = $Link->query($consulta5) or die ('Error consulta '. $registros4['table_name'] .': ' . mysqli_error($Link));
      if ($resultado5->num_rows > 0)
      {
        $mesProductos = str_replace($_SESSION['periodoActual'], '', str_replace('productosmov', '', $registros4['table_name']));
        $registros5 = $resultado5->fetch_assoc();
        $respuestaAJAX = [
          'estado' => 0,
          'mensaje' => 'No es posible eliminar la bodega debido a que se encuentra asociado al despacho de alimentos Número: '. $registros5['numeroProducto'] .' para el mes: '.  $nombreMes[$mesProductos]
        ];
        exit (json_encode($respuestaAJAX));
      }
    }
  }

  // Eliminar bodega.
  $consulta3 = "DELETE FROM bodegas WHERE ID = '$codigo'";
  $resultado3 = $Link->query($consulta3) or die ('Error consulta bodegas: '. mysqli_error($Link));
  if($resultado3)
  {
		// Registro de la bitácora
		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '61', 'Eliminó la bodega número: <strong>$codigo</strong>')";
		$Link->query($consultaBitacora) or die (mysqli_error($Link));

    $respuestaAJAX = [
      'estado' => 1,
      'mensaje' => 'La bodega se ha eliminado con éxito'
    ];
  }

  echo json_encode($respuestaAJAX);