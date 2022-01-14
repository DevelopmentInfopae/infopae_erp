<?php 

require_once '../../../db/conexion.php';
require_once '../../../config.php';

function consultarGrupoEtario($Cod_Grupo_Etario){
  global $Link;
  $consultaGrupoEtario = "select * from grupo_etario where id = '".$Cod_Grupo_Etario."'";
  $resultadoGrupoEtario = $Link->query($consultaGrupoEtario) or die('Unable to execute query. '. mysqli_error($Link).$consultaGrupoEtario);
  if ($resultadoGrupoEtario->num_rows > 0) {
    while ($row = $resultadoGrupoEtario->fetch_assoc()) {
      $grupoEtario = str_replace("Grupo", " ", $row['DESCRIPCION']);
    }
    return $grupoEtario;
  }   
}

function consultarVariacionMenu($variacionMenu){
  global $Link;
  $consultaVariacionMenu = "select * from variacion_menu where id = '".$variacionMenu."'";
  $resultadoVariacionMenu = $Link->query($consultaVariacionMenu) or die('Unable to execute query. '. mysqli_error($Link).$consultaVariacionMenu);
  if ($resultadoVariacionMenu->num_rows > 0) {
    while ($row = $resultadoVariacionMenu->fetch_assoc()) {
      $variacionMenuDesc = $row['descripcion'];
    }
    return $variacionMenuDesc;
  }
}

function obtenerUltimoCodigo($codigoPrefijo){
  global $Link;
  $consultUltimoCodigo = "select Codigo from productos".$_SESSION['periodoActual']." where Codigo like '".$codigoPrefijo."%' AND Nivel = 3 order by Codigo desc limit 1";
  $resultadoUltimoCodigo = $Link->query($consultUltimoCodigo) or die('Unable to execute query. '. mysqli_error($Link).$consultUltimoCodigo);
  if ($resultadoUltimoCodigo->num_rows > 0) {
    while ($row = $resultadoUltimoCodigo->fetch_assoc()) {
      $nuevoCodigo = $row['Codigo']+1;
      $nuevoCodigo = "0".$nuevoCodigo;
    }
  } else {
    $nuevoCodigo = $codigoPrefijo."001";
  }
  return $nuevoCodigo;
}


 ?>