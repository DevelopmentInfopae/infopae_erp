<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $documento = (isset($_POST['documento']) && $_POST['documento'] != '') ? mysqli_real_escape_string($Link, $_POST['documento']) : '';

  $consulta = "SELECT * FROM usuarios usu WHERE usu.num_doc = '$documento';";
  $resultado = $Link->query($consulta) or die('Error al consultar datos de usuario: '. mysqli_error($link));
  if ($resultado->num_rows > 0)
  {
  	$registros = $resultado->fetch_assoc();
  }
// print_r($registros);
  echo json_encode($registros);