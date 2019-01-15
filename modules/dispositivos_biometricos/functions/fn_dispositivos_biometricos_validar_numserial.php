<?php

  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $num_serial = $_POST['num_serial'];

  $consulta = " select num_serial from dispositivos where num_serial = ".$num_serial;

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows > 0){
    echo "1";
  } else {
    echo "0";
  }
