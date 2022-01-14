<?php

  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $grupoEtario = $_POST['grupoEtario'];
  $subtipoProducto = $_POST['subtipoProducto'];
  $variacionMenu = $_POST['variacionMenu'];
  

  $consulta = " select Orden_Ciclo from productos".$_SESSION['periodoActual']." where Cod_Tipo_Complemento = '".$subtipoProducto."' AND Cod_Grupo_Etario = ".$grupoEtario." AND cod_variacion_menu = ".$variacionMenu." order by Orden_Ciclo desc limit 1";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    if($row = $resultado->fetch_assoc()) { 
        echo $row['Orden_Ciclo']+1;
     }// Termina el while
  } else {
    echo 1;
  }//Termina el if que valida que si existan resultados
