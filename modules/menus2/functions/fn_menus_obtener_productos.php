<?php

require_once '../../../db/conexion.php';
require_once '../../../config.php';

$respuesta = $_POST['respuesta'];

if ($respuesta == 1) { ?>

  <option value="">Seleccione...</option>
  <?php

  if (isset($_POST['tipoProducto'])) {
    $tipoProducto = $_POST['tipoProducto'];
  } else {
    $tipoProducto = 0;
  }

  if (isset($_POST['grupoEtario'])) {
    $grupoEtario = $_POST['grupoEtario'];
  } else {
    $grupoEtario = 0;
  }

  if (isset($_POST['variacionMenu'])) {
    $variacionMenu = $_POST['variacionMenu'];
  } else {
    $variacionMenu = 0;
  }

  if (isset($_POST['tipoComplemento'])) {
    $tipoComplemento = $_POST['tipoComplemento'];
  } else {
    $tipoComplemento = 0;
  }

  if ($variacionMenu == '1') {

    $tipoPreparacion = " Codigo like '0203%' ";

  } else {

    if ($tipoComplemento == "CAJMPS" || $tipoComplemento == "CAJMRI" || $tipoComplemento == "APS") {

      if ($tipoComplemento == "CAJMPS") {
        $tipoPreparacion = " (Codigo like '0202%' or Codigo like '0201%')";
      } else if ($tipoComplemento == "CAJMRI") {
        $tipoPreparacion = " Codigo like '0201%' ";
      } else {
        $tipoPreparacion = " Codigo like '0202%' ";
      }

    }

  }


  if ($tipoProducto == "01") {
   $consulta = "select * from productos".$_SESSION['periodoActual']." where ((".$tipoPreparacion." AND Cod_Grupo_Etario = ".$grupoEtario.") or (Codigo like '04%')) and Nivel = 3 ORDER BY Codigo ASC";
    //$consulta = "select * from productos".$_SESSION['periodoActual']." where (Codigo like '02%' or Codigo like '04%') and Nivel = 3 and Inactivo = 0 Order by Codigo asc";
  } else if ($tipoProducto == "02") {
    $consulta = "select * from productos".$_SESSION['periodoActual']." where Codigo like '03%' and Nivel = 3 and Inactivo = 0 Order by Descripcion asc";
  }
  $cod1 = "";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) {
      if ($cod1 == "" || ($cod1 != substr($row['Codigo'], 0, 2) && $cod1 != "") ) {
        $cod1 = substr($row['Codigo'], 0, 2);
        if (substr($row['Codigo'], 0, 2) == "02") {
          echo "<optgroup label = 'Preparaciones'>";
        } else if (substr($row['Codigo'], 0, 2) == "04") {
          echo "<optgroup label = 'Industrializados'>";
        }
      }
      ?>
        <option value="<?php echo $row['Codigo']; ?>"><?php echo $row['Descripcion']; ?></option>
     <?php
      if ($cod1 != substr($row['Codigo'], 0, 2) ) {
        $cod1 = substr($row['Codigo'], 0, 2) ;
          echo "</optgroup>";
      }
     }// Termina el while
  } else {
    echo 0;
  }//Termina el if que valida que si existan resultados

} else if ($respuesta == 2) {

  $producto = $_POST['producto'];
  $consulta = "select NombreUnidad1 from productos".$_SESSION['periodoActual']." where Codigo = ".$producto;
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    if($row = $resultado->fetch_assoc()) {
      $unidadMedida = $row['NombreUnidad1'];
      echo $unidadMedida;
     }// Termina el while
  } else {
    echo 0;
  }//Termina el if que valida que si existan resultados
}
 ?>
