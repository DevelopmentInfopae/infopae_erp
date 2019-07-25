<?php
require_once 'fn_menus_head_funciones.php';

$usuario = $_SESSION['idUsuario'];

if (isset($_POST['descripcion'])) {
  $descripcion = mysqli_real_escape_string($Link, $_POST['descripcion']);
  $descripcion = strtoupper($descripcion);
} else {
  $descripcion = "";
}

if (isset($_POST['tipoProducto'])) {
  $tipoProducto = mysqli_real_escape_string($Link, $_POST['tipoProducto']);
} else {
  $tipoProducto = "";
}

if (isset($_POST['subtipoProducto'])) {
  $subtipoProducto = mysqli_real_escape_string($Link, $_POST['subtipoProducto']);
} else {
  $subtipoProducto = "";
}

if (isset($_POST['tipoComplemento'])) {
  $tipoComplemento = mysqli_real_escape_string($Link, $_POST['tipoComplemento']);
} else {
  $tipoComplemento = "";
}

if (isset($_POST['tipo_despacho'])) {
  $TipoDespacho = mysqli_real_escape_string($Link, $_POST['tipo_despacho']);
} else {
  $TipoDespacho = "";
}

if (isset($_POST['Cod_Grupo_Etario'])) {
  $Cod_Grupo_Etario = mysqli_real_escape_string($Link, $_POST['Cod_Grupo_Etario']);
} else {
  $Cod_Grupo_Etario = "";
}

if (isset($_POST['ordenCiclo'])) {
  $ordenCiclo = mysqli_real_escape_string($Link, $_POST['ordenCiclo']);
} else {
  $ordenCiclo = "";
}

if (isset($_POST['unidadMedida'])) {
  $unidadMedida = mysqli_real_escape_string($Link, $_POST['unidadMedida']);
} else {
  $unidadMedida = "";
}

if (isset($_POST['unidadMedidaPresentacion'])) {
  $unidadMedidaPresentacion = $_POST['unidadMedidaPresentacion'];
} else {
  $unidadMedidaPresentacion = "";
}

if (isset($_POST['cantPresentacion'])) {
  $cantPresentacion = $_POST['cantPresentacion'];
} else {
  $cantPresentacion = "";
}

if (isset($_POST['variacionMenu'])) {
  $variacionMenu = $_POST['variacionMenu'];
} else {
  $variacionMenu = "";
}

$consultaTipoProducto = "select Descripcion from productos".$_SESSION['periodoActual']." where Codigo like '".$tipoProducto."%' AND nivel = 1";
$resultadoTipoProducto = $Link->query($consultaTipoProducto) or die('Unable to execute query. '. mysqli_error($Link).$consultaTipoProducto);
if ($resultadoTipoProducto->num_rows > 0) {
  while ($row = $resultadoTipoProducto->fetch_assoc()) {
    $tipoProducto2 = $row['Descripcion'];
  }
}

if ($tipoProducto == "01") {
  $consultaTipoComplemento = "select * from tipo_complemento where CODIGO = '".$tipoComplemento."'";
  $resultadoTipoComplemento = $Link->query($consultaTipoComplemento) or die('Unable to execute query. '. mysqli_error($Link).$consultaTipoComplemento.$consultaTipoComplemento);
  if ($resultadoTipoComplemento->num_rows > 0) {
    if ($row = $resultadoTipoComplemento->fetch_assoc()) {
      $IDComplemento = $row['ID'];
    }
  }
  if (strlen($IDComplemento) == 1) {
    $codigoPrefijo = "010".$IDComplemento;
  } else if (strlen($IDComplemento) > 1) {
    $codigoPrefijo = "01".$IDComplemento;
  }
  $nuevoCodigo = obtenerUltimoCodigo($codigoPrefijo);
  $tipo_complemento = $tipoComplemento;
} else {
  $nuevoCodigo = obtenerUltimoCodigo($subtipoProducto);
  $tipo_complemento = "";
}

$NombreUnidad = array();
$CantidadUnd = array();

if ($unidadMedida == "g" || $unidadMedida == "cc") { //Si tipo de producto es 03 (Alimento) o 04(Alimento Industrializado), siempre entra aquí.
  $NombreUnidad[1] = $unidadMedida;
  if ( $unidadMedidaPresentacion[1] == "u" || $unidadMedidaPresentacion[1] == "g" || $unidadMedidaPresentacion[1] == "cc") {
    $CantidadUnd[1] = 1/$cantPresentacion[1];


    if (strpos($descripcion, strtoupper("x ".$cantPresentacion[1]." ".$unidadMedida)) || strpos($descripcion, strtoupper("x".$cantPresentacion[1].$unidadMedida))) {
        $descripcion = str_replace(strtoupper("x ".$cantPresentacion[1]." ".$unidadMedida), "", $descripcion);
        $descripcion = str_replace(strtoupper("x".$cantPresentacion[1].$unidadMedida), "", $descripcion);
    }

    $descripcion = $descripcion." x ".$cantPresentacion[1]." ".$unidadMedida;

  } else if ($unidadMedidaPresentacion[1] == "lb"){
    $CantidadUnd[1] = 1/500;
  } else if ($unidadMedidaPresentacion[1] == "kg" || $unidadMedidaPresentacion[1] == "lt"){
    $CantidadUnd[1] = 1/1000;
  }
  for ($i=1; $i <= sizeof($unidadMedidaPresentacion); $i++) {
    if ($unidadMedidaPresentacion[$i] == "u" || $unidadMedidaPresentacion[$i] == "lb" || $unidadMedidaPresentacion[$i] == "kg" || $unidadMedidaPresentacion[$i] == "lt") {
      $NombreUnidad[$i+1] =  $unidadMedidaPresentacion[$i];
      $CantidadUnd[$i+1] = 1;
    } else if (($unidadMedidaPresentacion[$i] == "g" || $unidadMedidaPresentacion[$i] == "cc") && (sizeof($unidadMedidaPresentacion) == 1)) {
      $NombreUnidad[$i+1] = "x ".$cantPresentacion[$i]." ".$unidadMedida;
      $CantidadUnd[$i+1] = 1;
    } else if (($unidadMedidaPresentacion[$i] == "g" || $unidadMedidaPresentacion[$i] == "cc") && (sizeof($unidadMedidaPresentacion) > 1)) {
      $NombreUnidad[$i+1] = "x ".$cantPresentacion[$i]." ".$unidadMedida;
      $CantidadUnd[$i+1] = $cantPresentacion[$i]/1000;
    }
  }
} else if ($unidadMedida == "u") { //Si tipo de producto es 01 (Menú) o 02(Preparado), siempre entra aquí.
  $NombreUnidad[1] = $unidadMedida;
  $CantidadUnd[1] = 1;
  if ($tipoProducto == "01") {
  $TipoDespacho = "99";
  $grupoEtario = consultarGrupoEtario($Cod_Grupo_Etario);

  if ($variacionMenu != 0) {
    $variacionMenuDesc = consultarVariacionMenu($variacionMenu);
  } else {
    $variacionMenuDesc = "";
  }

  $descripcion = $descripcion." No.".$ordenCiclo." Grupo Etario ".$grupoEtario." ".$variacionMenuDesc;

  } else if ($tipoProducto == "02") {
  $TipoDespacho = "0";
  $grupoEtario = consultarGrupoEtario($Cod_Grupo_Etario);

  if ($variacionMenu != 0) {
    $variacionMenuDesc = consultarVariacionMenu($variacionMenu);
  } else {
    $variacionMenuDesc = "";
  }
  $descripcion = $descripcion." ".$grupoEtario." ".$variacionMenuDesc;

  }
}

/*print_r($NombreUnidad);
print_r($CantidadUnd);
echo "<br>".sizeof($unidadMedidaPresentacion);*/
for ($i=sizeof($NombreUnidad)+1; $i <=5 ; $i++) {
  $NombreUnidad[$i] = "";
  $CantidadUnd[$i] = "";
}

if ($tipoProducto != "04") {
  $sqlProducto = "insert into productos".$_SESSION['periodoActual']." (Id, Codigo, Descripcion, Nivel, Tipo, Inactivo, NombreUnidad1, NombreUnidad2, NombreUnidad3, NombreUnidad4, NombreUnidad5, CantidadUnd1, CantidadUnd2, CantidadUnd3, CantidadUnd4, CantidadUnd5, TipodeProducto, FecExpDesc, Cod_Tipo_complemento, Cod_Grupo_Etario, Orden_Ciclo, TipoDespacho, cod_variacion_menu) values ('', '".$nuevoCodigo."', '".$descripcion."', '3', 'P', '0','".$NombreUnidad[1]."', '".$NombreUnidad[2]."', '".$NombreUnidad[3]."', '".$NombreUnidad[4]."', '".$NombreUnidad[5]."', '".$CantidadUnd[1]."', '".$CantidadUnd[2]."', '".$CantidadUnd[3]."', '".$CantidadUnd[4]."', '".$CantidadUnd[5]."', '".$tipoProducto2."', '".date('d/m/Y')."', '".$tipo_complemento."', '".$Cod_Grupo_Etario."', '".$ordenCiclo."', '".$TipoDespacho."', '".$variacionMenu."')";
  //echo $sqlProducto;

  if ($Link->query($sqlProducto) === true) {
    $idProducto = $Link->insert_id;

    if ($tipoProducto != "03") {
      $sqlFichaTecnica = "insert into fichatecnica (Id, Nombre, Codigo, NumeroUnidades, Instrucciones, CostoIngredientes, MargenSeguridad, CostoXUnidades, CostoUnidad, UltimoCosto, FactorVenta, FechaCosto, IdFT, UM) values ('', '".$descripcion."', '".$nuevoCodigo."', '1', '', '', '', '', '', '', '', '".date('Y/m/d')."', '', '')";

      if ($Link->query($sqlFichaTecnica) === true) {
        $IdFT = $Link->insert_id;

        if ($tipoProducto == "01") {
          $sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '12', 'Registró el menú <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigo."</strong>') ";
        } else if ($tipoProducto == "02") {
          $sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '29', 'Registró la preparación <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigo."</strong>') ";
        }
        if ($Link->query($sqlBitacora)===true) {
          echo '{"respuesta" : [{"exitoso" : "1", "idProducto" : "'.$idProducto.'", "IdFT" : "'.$IdFT.'", "nuevoCodigo" : "'.$nuevoCodigo.'"}]}';
        } else {
          echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlBitacora.'"}]}';
        }
      } else {
        echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlFichaTecnica.'"}]}';
      }
    } else {
      $sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '13', 'Registró el alimento <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigo."</strong>') ";
      if ($Link->query($sqlBitacora)===true) {
        echo '{"respuesta" : [{"exitoso" : "1", "idProducto" : "'.$idProducto.'", "IdFT" : "0", "nuevoCodigo" : "'.$nuevoCodigo.'"}]}';
      } else {
        echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlBitacora.'"}]}';
      }
    }
  } else {
    echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlProducto.'"}]}';
  }
} else if ($tipoProducto == "04") { //SI ES INDUSTRIALIZADO, CREA PRODUCTO tipo Alimento, luego PRODUCTO tipo Industrializado, al último se le crea ficha técnica y fichatecnicadet relacionando el primer producto creado como Alimento.

  if ($subtipoProducto == "0401") {
    $sufijo = "01";
  } else if ($subtipoProducto == "0402") {
    $sufijo = "06";
  } else if ($subtipoProducto == "0403") {
    $sufijo = "05";
  } else if ($subtipoProducto == "0404") {
    $sufijo = "04";
  }

  $nuevoCodigoIndus = obtenerUltimoCodigo("03".$sufijo);

  $sqlProducto = "insert into productos".$_SESSION['periodoActual']." (Id, Codigo, Descripcion, Nivel, Tipo, Inactivo, NombreUnidad1, NombreUnidad2, NombreUnidad3, NombreUnidad4, NombreUnidad5, CantidadUnd1, CantidadUnd2, CantidadUnd3, CantidadUnd4, CantidadUnd5, TipodeProducto, FecExpDesc, Cod_Tipo_complemento, Cod_Grupo_Etario, Orden_Ciclo, TipoDespacho, cod_variacion_menu) values ('', '".$nuevoCodigoIndus."', '".$descripcion."', '3', 'P', '0','".$NombreUnidad[1]."', '".$NombreUnidad[2]."', '".$NombreUnidad[3]."', '".$NombreUnidad[4]."', '".$NombreUnidad[5]."', '".$CantidadUnd[1]."', '".$CantidadUnd[2]."', '".$CantidadUnd[3]."', '".$CantidadUnd[4]."', '".$CantidadUnd[5]."', 'Alimento', '".date('d/m/Y')."', '".$tipo_complemento."', '".$Cod_Grupo_Etario."', '".$ordenCiclo."', '".$TipoDespacho."', '".$variacionMenu."')";
    $undMed = 1/$CantidadUnd[1];
    $descRI = $descripcion." x ".$undMed." ".$NombreUnidad[1]." RI";
    if ($Link->query($sqlProducto)===true) {
          $idProducto= $Link->insert_id;
        $sqlProducto2 = "insert into productos".$_SESSION['periodoActual']." (Id, Codigo, Descripcion, Nivel, Tipo, Inactivo, NombreUnidad1, NombreUnidad2, NombreUnidad3, NombreUnidad4, NombreUnidad5, CantidadUnd1, CantidadUnd2, CantidadUnd3, CantidadUnd4, CantidadUnd5, TipodeProducto, FecExpDesc, Cod_Tipo_complemento, Cod_Grupo_Etario, Orden_Ciclo, TipoDespacho, cod_variacion_menu) values ('', '".$nuevoCodigo."', '".$descRI."', '3', 'P', '0','u', '', '', '', '', '1', '', '', '', '', '".$tipoProducto2."', '".date('d/m/Y')."', '".$tipo_complemento."', '".$Cod_Grupo_Etario."', '".$ordenCiclo."', '0', '".$variacionMenu."')";
          if ($Link->query($sqlProducto2)===true) {
              $sqlFichaTecnica = "insert into fichatecnica (Id, Nombre, Codigo, NumeroUnidades, Instrucciones, CostoIngredientes, MargenSeguridad, CostoXUnidades, CostoUnidad, UltimoCosto, FactorVenta, FechaCosto, IdFT, UM) values ('', '".$descripcion."', '".$nuevoCodigo."', '1', '', '', '', '', '', '', '', '".date('Y/m/d')."', '', '')";
              if ($Link->query($sqlFichaTecnica)===true) {
                  $IdFT= $Link->insert_id;
                  $sqlFichaTecnicaDet = "insert into fichatecnicadet (Id, codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) values ('', '".$nuevoCodigoIndus."', '".$descripcion."', '0', 'u', '0', '".$IdFT."', '0', '', '0', 'Industrializados', 'Industrializados', '0', '0')";
                  if ($Link->query($sqlFichaTecnicaDet)===true) {
                    $sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '13', 'Registró el alimento industrializado <strong>".$descRI." </strong> con código <strong>".$nuevoCodigo."</strong>, sobre el cual se le creó el alimento <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigoIndus."</strong>') ";
                    if ($Link->query($sqlBitacora)===true) {
                      echo '{"respuesta" : [{"exitoso" : "1", "idProducto" : "'.$idProducto.'", "IdFT" : "'.$IdFT.'", "nuevoCodigo" : "'.$nuevoCodigoIndus.'"}]}';
                    } else {
                      echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlBitacora.'"}]}';
                    }
                  } else {
                    echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlFichaTecnicaDet.'"}]}';
                  }
              } else {
                echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlFichaTecnica.'"}]}';
              }
          } else {
            echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlProducto2.'"}]}';
          }
    } else {
      echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error : '.$sqlProducto.'"}]}';
    }
}