<?php

  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $idbioest = $_POST['idbioest'];
  $iddispositivo = $_POST['iddispositivo'];

  $consulta = "SELECT * FROM biometria WHERE id_dispositivo = ".$iddispositivo." AND id_bioest = ".$idbioest;
  if ($idbioest != "") {
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows > 0){
      echo "1";
    } else {
      echo "0";
    }
  } else {
    echo "0";
  }
