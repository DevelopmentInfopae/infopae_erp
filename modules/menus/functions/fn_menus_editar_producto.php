<?php

require_once 'fn_menus_head_funciones.php';

$usuario = $_SESSION['idUsuario'];

function validarCambioTipoProducto($IdProducto, $codigoPrefijo){
  global $Link;
  $consultaProducto = "select Codigo from productos".$_SESSION['periodoActual']." where Id = '".$IdProducto."'";
  $resultadoProducto = $Link->query($consultaProducto) or die('Unable to execute query. '. mysqli_error($Link));
  if ($resultadoProducto->num_rows > 0) {
    while ($row = $resultadoProducto->fetch_assoc()) {
      $Codigo = $row['Codigo'];
    }
    if (substr($Codigo,0,4) != $codigoPrefijo) {
    	return true;
    } else {
    	return false;
    }
  }
}

function limpiarGrupoEtarioDeNombre($descripcion, $Cod_Grupo_Etario){
  global $Link;
  $consultaGrupoEtario = "select * from grupo_etario";
  $resultadoGrupoEtario = $Link->query($consultaGrupoEtario) or die('Unable to execute query. '. mysqli_error($Link).$consultaGrupoEtario);
  if ($resultadoGrupoEtario->num_rows > 0) {
    while ($row = $resultadoGrupoEtario->fetch_assoc()) {
    	$GrupoEtario[] = $row;
    	$descGE = str_replace("Grupo ", "", $row['DESCRIPCION']);
      if (strpos($descripcion, $descGE)) {
        $descripcion = str_replace($descGE, "", $descripcion);
      }
    }
    foreach ($GrupoEtario as $GE) {
      if ($GE['ID'] == $Cod_Grupo_Etario) {
        $descripcion = $descripcion." ".$GE['DESCRIPCION'];
      }
    }
    return $descripcion;
  }
}

if (isset($_POST['descripcion'])) {
	$descripcion = strtoupper($_POST['descripcion']);
} else {
	$descripcion = "";
}

if (isset($_POST['Codigo'])) {
  $Codigo = $_POST['Codigo'];
} else {
  $Codigo = "";
}

if (isset($_POST['IdProducto'])) {
  $IdProducto = $_POST['IdProducto'];
} else {
  $IdProducto = "";
}

if (isset($_POST['tipoProducto'])) {
  $tipoProducto = $_POST['tipoProducto'];
} else {
  $tipoProducto = "";
}

if (isset($_POST['subtipoProducto'])) {
  $subtipoProducto = $_POST['subtipoProducto'];
} else {
  $subtipoProducto = "";
}

if (isset($_POST['tipoComplemento'])) {
  $tipoComplemento = $_POST['tipoComplemento'];
} else {
  $tipoComplemento = "";
}

if (isset($_POST['TipoDespacho'])) {
  $TipoDespacho = $_POST['TipoDespacho'];
} else {
  $TipoDespacho = "";
}

if (isset($_POST['Cod_Grupo_Etario'])) {
  $Cod_Grupo_Etario = $_POST['Cod_Grupo_Etario'];
} else {
  $Cod_Grupo_Etario = "";
}

if (isset($_POST['ordenCiclo'])) {
  $ordenCiclo = $_POST['ordenCiclo'];
} else {
  $ordenCiclo = "";
}

if (isset($_POST['unidadMedida'])) {
  $unidadMedida = $_POST['unidadMedida'];
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
$resultadoTipoProducto = $Link->query($consultaTipoProducto) or die('Unable to execute query. '. mysqli_error($Link));
if ($resultadoTipoProducto->num_rows > 0) {
  while ($row = $resultadoTipoProducto->fetch_assoc()) {
    $tipoProducto2 = $row['Descripcion'];
  }
}

if ($tipoProducto == "01") {
  $consultaTipoComplemento = "select * from tipo_complemento where CODIGO = '".$tipoComplemento."'";
  $resultadoTipoComplemento = $Link->query($consultaTipoComplemento) or die('Unable to execute query. '. mysqli_error($Link).$consultaTipoComplemento);
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

  if (validarCambioTipoProducto($IdProducto, $codigoPrefijo)) {
	  $nuevoCodigo = obtenerUltimoCodigo($codigoPrefijo);
  } else {
  	$nuevoCodigo = $Codigo;
  }
  $tipo_complemento = $tipoComplemento;
} else {
  if (validarCambioTipoProducto($IdProducto, $subtipoProducto)) {
	  $nuevoCodigo = obtenerUltimoCodigo($subtipoProducto);
  } else {
  	$nuevoCodigo = $Codigo;
  }
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
  $variacionMenuDesc = consultarVariacionMenu($variacionMenu);
  $descripcion = "Menú No.".$ordenCiclo." Grupo Etario ".$grupoEtario." ".$variacionMenuDesc;

  } else if ($tipoProducto == "02") {
  $TipoDespacho = "0";
  $descripcion = limpiarGrupoEtarioDeNombre($descripcion, $Cod_Grupo_Etario);
  $variacionMenuDesc = consultarVariacionMenu($variacionMenu);
	if (strpos($descripcion, "Vegetariano") || strpos($descripcion, "Normal")) {
		$descripcion = str_replace("Vegetariano", "", $descripcion);
		$descripcion = str_replace("Normal", "", $descripcion);
		$descripcion = str_replace("   ", "", $descripcion);
	}
	$descripcion = $descripcion." ".$variacionMenuDesc;
  }
}

/*print_r($NombreUnidad);
print_r($CantidadUnd);
echo "<br>".sizeof($unidadMedidaPresentacion);*/
for ($i=sizeof($NombreUnidad)+1; $i <=5 ; $i++) {
  $NombreUnidad[$i] = "";
  $CantidadUnd[$i] = "";
}

$sqlProducto = "update productos".$_SESSION['periodoActual']." set Codigo = '".$nuevoCodigo."', Descripcion = '".$descripcion."', Nivel = '3', Tipo = 'P', Inactivo = '0', NombreUnidad1 = '".$NombreUnidad[1]."', NombreUnidad2 = '".$NombreUnidad[2]."', NombreUnidad3 = '".$NombreUnidad[3]."', NombreUnidad4 = '".$NombreUnidad[4]."', NombreUnidad5 = '".$NombreUnidad[5]."', CantidadUnd1 = '".$CantidadUnd[1]."', CantidadUnd2 = '".$CantidadUnd[2]."', CantidadUnd3 = '".$CantidadUnd[3]."', CantidadUnd4 = '".$CantidadUnd[4]."', CantidadUnd5 = '".$CantidadUnd[5]."', TipodeProducto = '".$tipoProducto2."', FecExpDesc = '".date('d/m/Y')."', Cod_Tipo_complemento = '".$tipo_complemento."', Cod_Grupo_Etario = '".$Cod_Grupo_Etario."', Orden_Ciclo = '".$ordenCiclo."', TipoDespacho = '".$TipoDespacho."', cod_variacion_menu ='".$variacionMenu."' where Id = '".$IdProducto."'";

if ($tipoProducto == "01") {
	$tipo_accion = "12";
	$accion = "Actualizó los datos del menú <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigo."</strong>";
} else if ($tipoProducto == "02") {
	$tipo_accion = "29";
	$accion = "Actualizó los datos de la preparación <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigo."</strong>";
} else if ($tipoProducto == "03" || substr($tipoProducto, 0, 2) == "04") {
	$tipo_accion = "13";
	$accion = "Actualizó los datos del alimento <strong>".$descripcion."</strong> con código <strong>".$nuevoCodigo."</strong>";
}

if ($Link->query($sqlProducto) === true) {

	if ($tipoProducto != "01" && $nuevoCodigo != $Codigo) {
		$actualizarFichaTecnicaDet = "UPDATE fichatecnicadet SET codigo = ".$nuevoCodigo." WHERE codigo = ".$Codigo;
		if ($Link->query($actualizarFichaTecnicaDet)===true) {
			# code...
		} else {
			echo "Error al actualizar el codigo del producto en fichatecnicadet";
		}
	}

	if (isset($_POST['IdFT'])) {
		$IdFT = $_POST['IdFT'];

		if (isset($_POST['IdFTDet'])) {
			$IdFTDet = $_POST['IdFTDet'];
		} else {
			$IdFTDet = "";
		}

		if (isset($_POST['productoFichaTecnicaDet'])) {
			$productoFichaTecnicaDet = $_POST['productoFichaTecnicaDet'];
		} else {
			$productoFichaTecnicaDet = "";
		}


		if (isset($_POST['cantidadProducto'])) {
			$cantidadProducto = $_POST['cantidadProducto'];
		} else {
			$cantidadProducto = "";
		}

		if (isset($_POST['unidadMedidaProducto'])) {
			$unidadMedidaProducto = $_POST['unidadMedidaProducto'];
		} else {
			$unidadMedidaProducto = "";
		}

		if (isset($_POST['pesoBrutoProducto'])) {
			$pesoBrutoProducto = $_POST['pesoBrutoProducto'];
		} else {
			$pesoBrutoProducto = "";
		}

		if (isset($_POST['pesoNetoProducto'])) {
			$pesoNetoProducto = $_POST['pesoNetoProducto'];
		} else {
			$pesoNetoProducto = "";
		}

		$sqlFichaTecnica = "update fichatecnica set Nombre = '".$descripcion."', Codigo = '".$nuevoCodigo."', NumeroUnidades = '1', FechaCosto = '".date('Y/m/d')."' WHERE Id = '".$IdFT."'";

		if ($Link->query($sqlFichaTecnica) === true) {
			$validaRegistro = 0;

			if (($tipoProducto == "01" || $tipoProducto == "02" || $tipoProducto == "04") && $IdProducto != 0) {
			  if ($tipoProducto == "01" && $IdFT != 0) {
			    for ($i=1; $i <= sizeof($productoFichaTecnicaDet); $i++) {
			      $consultaDesc = "select Descripcion from productos".$_SESSION['periodoActual']." where Codigo = ".$productoFichaTecnicaDet[$i];
			      $resultadoDesc = $Link->query($consultaDesc) or die('Unable to execute query. '. mysqli_error($Link)." ".$consultaDesc);
			      if ($resultadoDesc->num_rows > 0) {
			        if ($row = $resultadoDesc->fetch_assoc()) {
			          $descProductoFichaTecnicaDet = $row['Descripcion'];
			        }
			      }

			      if (isset($IdFTDet[$i])) {
			      	$sqlFichaTecnicaDet = "update fichatecnicadet set codigo = '".$productoFichaTecnicaDet[$i]."', Componente = '".$descProductoFichaTecnicaDet."', Cantidad = '0', UnidadMedida = 'u', Factor = '0', Estado = '0', Tipo = 'Preparación', TipoProducto = 'Preparación', PesoBruto = '0', PesoNeto = '0' WHERE Id = '".$IdFTDet[$i]."'";
			      } else {
			      	$sqlFichaTecnicaDet = "insert into fichatecnicadet (Id, codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) values ('', '".$productoFichaTecnicaDet[$i]."', '".$descProductoFichaTecnicaDet."', '1', 'u', '0', '".$IdFT."', '0', '0', '0', 'Alimento', 'Alimento', '0', '0')";
			      }
			      if ($Link->query($sqlFichaTecnicaDet) === true) {
			        $validaRegistro++;
			      } else {
			        echo "Error : ".$sqlFichaTecnicaDet;
			      }
			    }
			  } else if (($tipoProducto == "02" || $tipoProducto == "04") && $IdFT != 0) {
			    for ($i=1; $i <= sizeof($productoFichaTecnicaDet); $i++) {
			      $consultaDesc = "select Descripcion from productos".$_SESSION['periodoActual']." where Codigo = ".$productoFichaTecnicaDet[$i];
			      $resultadoDesc = $Link->query($consultaDesc) or die('Unable to execute query. '. mysqli_error($Link)." ".$consultaDesc);
			      if ($resultadoDesc->num_rows > 0) {
			        if ($row = $resultadoDesc->fetch_assoc()) {
			          $descProductoFichaTecnicaDet = $row['Descripcion'];
			        }
			      } else {
			      	$descProductoFichaTecnicaDet = "";
			      }

			      $consultaCantidadUnd2Producto = "select CantidadUnd2 from productos".$_SESSION['periodoActual']." where Codigo = ".$productoFichaTecnicaDet[$i];
			      $resultadoCantidadUnd2Producto = $Link->query($consultaCantidadUnd2Producto) or die('Unable to execute query. '. mysqli_error($Link)." ".$consultaCantidadUnd2Producto);
			      if ($resultadoCantidadUnd2Producto->num_rows > 0) {
			        if ($row = $resultadoCantidadUnd2Producto->fetch_assoc()) {
			          $CantidadUnd2 = $row['CantidadUnd2'];
			        }
			      } else {
			      	$CantidadUnd2 = 0;
			      }

			      if ($cantidadProducto[$i] != 0) {
			        $factorProducto = $CantidadUnd2/$cantidadProducto[$i];
			      } else {
			        $factorProducto = 0;
			      }

			      if (isset($IdFTDet[$i])) {
			      	$sqlFichaTecnicaDet = "update fichatecnicadet set codigo = '".$productoFichaTecnicaDet[$i]."', Componente = '".$descProductoFichaTecnicaDet."', Cantidad = '".$cantidadProducto[$i]."', UnidadMedida = '".$unidadMedidaProducto[$i]."', Factor = '".$factorProducto."', Estado = '0', Tipo = 'Alimento', TipoProducto = 'Alimento', PesoBruto = '".$pesoBrutoProducto[$i]."', PesoNeto = '".$pesoNetoProducto[$i]."'  WHERE Id = '".$IdFTDet[$i]."'";
			      } else {
			      	$sqlFichaTecnicaDet = "insert into fichatecnicadet (Id, codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) values ('', '".$productoFichaTecnicaDet[$i]."', '".$descProductoFichaTecnicaDet."', '".$cantidadProducto[$i]."', '".$unidadMedidaProducto[$i]."', '0', '".$IdFT."', '0', '".$factorProducto."', '0', 'Alimento', 'Alimento', '".$pesoBrutoProducto[$i]."', '".$pesoNetoProducto[$i]."')";
			      }
			     	if ($Link->query($sqlFichaTecnicaDet) === true) {
			        $validaRegistro++;
			      } else {
			        echo "Error : ".$sqlFichaTecnicaDet;
			      }
			    }
			  } else {
			  	echo "Error : tipoProducto = ".$tipoProducto.", IdFT = ".$IdFT;
			  }

			  if ($validaRegistro == sizeof($productoFichaTecnicaDet)) {
			  	$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '".$tipo_accion."', '".$accion."') ";
			  	if ($Link->query($sqlBitacora)===true) {
			  		echo "1";
			  	} else {
			  		echo "Error bitácora : ".$sqlBitacora;
			  	}
			  } else {
			  	echo "Error al completar los registros.";
			  }

			} else {
				 echo "Error en tipo producto : ".$tipoProducto;
			}
		} else {
			echo "Error al actualizar ficha tecnica : ".$sqlFichaTecnica;
		}
	} else if (isset($_POST['IdCalyNut'])) {
		$IdCalyNut = $_POST['IdCalyNut'];
		if (isset($_POST['kcalxg'])) {
			$kcalxg = $_POST['kcalxg'];
		} else {
			$kcalxg = "";
		}

		if (isset($_POST['kcaldgrasa'])) {
			$kcaldgrasa = $_POST['kcaldgrasa'];
		} else {
			$kcaldgrasa = "";
		}

		if (isset($_POST['Grasa_Sat'])) {
			$Grasa_Sat = $_POST['Grasa_Sat'];
		} else {
			$Grasa_Sat = "";
		}

		if (isset($_POST['Grasa_poliins'])) {
			$Grasa_poliins = $_POST['Grasa_poliins'];
		} else {
			$Grasa_poliins = "";
		}

		if (isset($_POST['Grasa_monoins'])) {
			$Grasa_monoins = $_POST['Grasa_monoins'];
		} else {
			$Grasa_monoins = "";
		}

		if (isset($_POST['Grasa_Trans'])) {
			$Grasa_Trans = $_POST['Grasa_Trans'];
		} else {
			$Grasa_Trans = "";
		}

		if (isset($_POST['Fibra_dietaria'])) {
			$Fibra_dietaria = $_POST['Fibra_dietaria'];
		} else {
			$Fibra_dietaria = "";
		}

		if (isset($_POST['Azucares'])) {
			$Azucares = $_POST['Azucares'];
		} else {
			$Azucares = "";
		}

		if (isset($_POST['Proteinas'])) {
			$Proteinas = $_POST['Proteinas'];
		} else {
			$Proteinas = "";
		}

		if (isset($_POST['Colesterol'])) {
			$Colesterol = $_POST['Colesterol'];
		} else {
			$Colesterol = "";
		}

		if (isset($_POST['Sodio'])) {
			$Sodio = $_POST['Sodio'];
		} else {
			$Sodio = "";
		}

		if (isset($_POST['Zinc'])) {
			$Zinc = $_POST['Zinc'];
		} else {
			$Zinc = "";
		}

		if (isset($_POST['Calcio'])) {
			$Calcio = $_POST['Calcio'];
		} else {
			$Calcio = "";
		}

		if (isset($_POST['Hierro'])) {
			$Hierro = $_POST['Hierro'];
		} else {
			$Hierro = "";
		}

		if (isset($_POST['Vit_A'])) {
			$Vit_A = $_POST['Vit_A'];
		} else {
			$Vit_A = "";
		}

		if (isset($_POST['Vit_C'])) {
			$Vit_C = $_POST['Vit_C'];
		} else {
			$Vit_C = "";
		}

		if (isset($_POST['Vit_B1'])) {
			$Vit_B1 = $_POST['Vit_B1'];
		} else {
			$Vit_B1 = "";
		}

		if (isset($_POST['Vit_B2'])) {
			$Vit_B2 = $_POST['Vit_B2'];
		} else {
			$Vit_B2 = "";
		}

		if (isset($_POST['Vit_B3'])) {
			$Vit_B3 = $_POST['Vit_B3'];
		} else {
			$Vit_B3 = "";
		}

		if (isset($_POST['Acido_Fol'])) {
			$Acido_Fol = $_POST['Acido_Fol'];
		} else {
			$Acido_Fol = "";
		}

		if (isset($_POST['Referencia'])) {
			$Referencia = $_POST['Referencia'];
		} else {
			$Referencia = "";
		}

		if (isset($_POST['cod_Referencia'])) {
			$cod_Referencia = $_POST['cod_Referencia'];
		} else {
			$cod_Referencia = "";
		}

		$consultaGrupoAlimento = "select Descripcion from productos".$_SESSION['periodoActual']." where nivel = 2 AND Codigo = ".substr($nuevoCodigo, 0, 4);
		$resultadoGrupoAlimento = $Link->query($consultaGrupoAlimento) or die('Unable to execute query. '. mysqli_error($Link));
		if ($resultadoGrupoAlimento->num_rows > 0) {
		  while ($row = $resultadoGrupoAlimento->fetch_assoc()) {
		    $grupo_alim = $row['Descripcion'];
		  }
		} else {
			$grupo_alim = "Error :".$consultaGrupoAlimento;
		}
		$sqlCalyNut = "update menu_aportes_calynut set cod_prod = '".$nuevoCodigo."', nom_prod = '".$descripcion."', grupo_alim = '".$grupo_alim."', kcalxg = '".$kcalxg."', kcaldgrasa = '".$kcaldgrasa."', Grasa_Sat = '".$Grasa_Sat."', Grasa_poliins = '".$Grasa_poliins."', Grasa_Monoins = '".$Grasa_monoins."', Grasa_Trans = '".$Grasa_Trans."', Fibra_dietaria = '".$Fibra_dietaria."', Azucares = '".$Azucares."', Proteinas = '".$Proteinas."', Colesterol = '".$Colesterol."', Sodio = '".$Sodio."', Zinc = '".$Zinc."', Calcio = '".$Calcio."', Hierro = '".$Hierro."', Vit_A = '".$Vit_A."', Vit_C = '".$Vit_C."', Vit_B1 = '".$Vit_B1."', Vit_B2 = '".$Vit_B2."', Vit_B3 = '".$Vit_B3."', Acido_Fol = '".$Acido_Fol."', Referencia = '".$Referencia."', cod_Referencia = '".$cod_Referencia."' where id = '".$IdCalyNut."'";
		if ($Link->query($sqlCalyNut) === true) {
			$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '".$tipo_accion."', '".$accion."') ";
		  	if ($Link->query($sqlBitacora)===true) {
		  		echo "1";
		  	} else {
		  		echo "Error bitácora : ".$sqlBitacora;
		  	}
		} else {
			echo "Error al actualizar calynut";
		}
	}

} else {
	echo "Error al actualizar producto : ".$sqlProducto;
}

 ?>