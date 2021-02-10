<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

if (isset($_POST['cod_municipio'])) {
	$cod_municipio = $_POST['cod_municipio'];
} else {
	$cod_municipio = "";
}

if (isset($_POST['cod_inst'])) {
	$cod_inst = $_POST['cod_inst'];
} else {
	$cod_inst = "";
}

if (isset($_POST['cod_sede'])) {
	$cod_sede = $_POST['cod_sede'];
} else {
	$cod_sede = "";
}

if (isset($_POST['nom_sede'])) {
	$nom_sede = $_POST['nom_sede'];
} else {
	$nom_sede = "";
}

if (isset($_POST['id_Complem_JMJT'])) {
	$id_Complem_JMJT = $_POST['id_Complem_JMJT'];
} else {
	$id_Complem_JMJT = "";
}

if (isset($_POST['id_Almuerzo'])) {
	$id_Almuerzo = $_POST['id_Almuerzo'];
} else {
	$id_Almuerzo = "";
}

if (isset($_POST['Concepto_Sanitario'])) {
	$Concepto_Sanitario = $_POST['Concepto_Sanitario'];
} else {
	$Concepto_Sanitario = "";
}

if (isset($_POST['fecha_expedicion'])) {
	$fecha_expedicion = $_POST['fecha_expedicion'];
} else {
	$fecha_expedicion = "";
}

if (isset($_POST['Atencion_MayoritariaI'])) {
	$Atencion_MayoritariaI = $_POST['Atencion_MayoritariaI'];
} else {
	$Atencion_MayoritariaI = "";
}

if (isset($_POST['Comedor_Escolar'])) {
	$Comedor_Escolar = $_POST['Comedor_Escolar'];
} else {
	$Comedor_Escolar = "";
}

if (isset($_POST['observaciones'])) {
	$observaciones = $_POST['observaciones'];
} else {
	$observaciones = "";
}

$sqlInfraestructura = "INSERT INTO infraestructura (cod_inst, cod_sede, Atencion_MayoritariaI, id_Complem_JMJT, id_Almuerzo, Comedor_Escolar, Concepto_Sanitario, Fecha_Expe, observaciones) VALUES ('".$cod_inst."', '".$cod_sede."', '".$Atencion_MayoritariaI."', '".$id_Complem_JMJT."', '".$id_Almuerzo."', '".$Comedor_Escolar."', '".$Concepto_Sanitario."', '".$fecha_expedicion."', '".$observaciones."')";

if ($Link->query($sqlInfraestructura) === true) {
	$idInfraestructura = $Link->insert_id;
	//---- Parametros ---
	if (isset($_POST['id_parametro'])) {
		$id_parametro = $_POST['id_parametro'];
	} else {
		$id_parametro = "";
	}

	// --- Campos por parametro ---
	// ----1 a 3----
	if (isset($_POST['material_piso'])) {
		$material_piso = $_POST['material_piso'];
	} else {
		$material_piso = "";
	}

	if (isset($_POST['material_paredes'])) {
		$material_paredes = $_POST['material_paredes'];
	} else {
		$material_paredes = "";
	}

	if (isset($_POST['material_techo'])) {
		$material_techo = $_POST['material_techo'];
	} else {
		$material_techo = "";
	}

	if (isset($_POST['material_mesones'])) {
		$material_mesones = $_POST['material_mesones'];
	} else {
		$material_mesones = "";
	}
	// ---- 2 ----

	if (isset($_POST['utensilios_suficientes'])) {
		$utensilios_suficientes = $_POST['utensilios_suficientes'];
	} else {
		$utensilios_suficientes = "";
	}
	//---- 3 -----
	if (isset($_POST['mesas_sillas_suficientes'])) {
		$mesas_sillas_suficientes = $_POST['mesas_sillas_suficientes'];
	} else {
		$mesas_sillas_suficientes = "";
	}

	//---- 4 ----
	if (isset($_POST['energia'])) {
		$energia = $_POST['energia'];
	} else {
		$energia = "";
	}

	if (isset($_POST['agua'])) {
		$agua = $_POST['agua'];
	} else {
		$agua = "";
	}

	if (isset($_POST['acueducto'])) {
		$acueducto = $_POST['acueducto'];
	} else {
		$acueducto = "";
	}

	if (isset($_POST['alcantarillado'])) {
		$alcantarillado = $_POST['alcantarillado'];
	} else {
		$alcantarillado = "";
	}

	if (isset($_POST['gas'])) {
		$gas = $_POST['gas'];
	} else {
		$gas = "";
	}

	if (isset($_POST['almacenamiento_agua'])) {
		$almacenamiento_agua = $_POST['almacenamiento_agua'];
	} else {
		$almacenamiento_agua = "";
	}

	//---- 5 ----
	if (isset($_POST['area_alm'])) {
		$area_alm = $_POST['area_alm'];
	} else {
		$area_alm = "";
	}

	if (isset($_POST['final_residuos'])) {
		$final_residuos = $_POST['final_residuos'];
	} else {
		$final_residuos = "";
	}

	//---- 6 ----
	if (isset($_POST['lavado_manos'])) {
		$lavado_manos = $_POST['lavado_manos'];
	} else {
		$lavado_manos = "";
	}

	if (isset($_POST['estado_lavadomanos'])) {
		$estado_lavadomanos = $_POST['estado_lavadomanos'];
	} else {
		$estado_lavadomanos = "";
	}

	if (isset($_POST['manos_implemento_aseo'])) {
		$manos_implemento_aseo = $_POST['manos_implemento_aseo'];
	} else {
		$manos_implemento_aseo = "";
	}

	if (isset($_POST['bano_manipuladoras'])) {
		$bano_manipuladoras = $_POST['bano_manipuladoras'];
	} else {
		$bano_manipuladoras = "";
	}

	if (isset($_POST['estado_bano'])) {
		$estado_bano = $_POST['estado_bano'];
	} else {
		$estado_bano = "";
	}

	if (isset($_POST['bano_implemento_aseo'])) {
		$bano_implemento_aseo = $_POST['bano_implemento_aseo'];
	} else {
		$bano_implemento_aseo = "";
	}

	for ($k=0; $k < sizeof($id_parametro); $k++) { 
		if (!isset($material_piso[$id_parametro[$k]])) {
			$material_piso[$id_parametro[$k]] = "";
		}
		if (!isset($material_paredes[$id_parametro[$k]])) {
			$material_paredes[$id_parametro[$k]] = "";
		}
		if (!isset($material_techo[$id_parametro[$k]])) {
			$material_techo[$id_parametro[$k]] = "";
		}
		if (!isset($material_mesones[$id_parametro[$k]])) {
			$material_mesones[$id_parametro[$k]] = "";
		}
		if (!isset($utensilios_suficientes[$id_parametro[$k]])) {
			$utensilios_suficientes[$id_parametro[$k]] = 0;
		}
		if (!isset($mesas_sillas_suficientes[$id_parametro[$k]])) {
			$mesas_sillas_suficientes[$id_parametro[$k]] = 0;
		}
		if (!isset($energia[$id_parametro[$k]])) {
			$energia[$id_parametro[$k]] = "";
		}
		if (!isset($agua[$id_parametro[$k]])) {
			$agua[$id_parametro[$k]] = "";
		}
		if (!isset($acueducto[$id_parametro[$k]])) {
			$acueducto[$id_parametro[$k]] = 0;
		}
		if (!isset($gas[$id_parametro[$k]])) {
			$gas[$id_parametro[$k]] = 0;
		}
		if (!isset($alcantarillado[$id_parametro[$k]])) {
			$alcantarillado[$id_parametro[$k]] = 0;
		}
		if (!isset($almacenamiento_agua[$id_parametro[$k]])) {
			$almacenamiento_agua[$id_parametro[$k]] = "";
		}
		if (!isset($area_alm[$id_parametro[$k]])) {
			$area_alm[$id_parametro[$k]] = 0;
		}
		if (!isset($final_residuos[$id_parametro[$k]])) {
			$final_residuos[$id_parametro[$k]] = "";
		}
		if (!isset($lavado_manos[$id_parametro[$k]])) {
			$lavado_manos[$id_parametro[$k]] = 0;
		}
		if (!isset($estado_lavadomanos[$id_parametro[$k]])) {
			$estado_lavadomanos[$id_parametro[$k]] = "";
		}
		if (!isset($manos_implemento_aseo[$id_parametro[$k]])) {
			$manos_implemento_aseo[$id_parametro[$k]] = 0;
		}
		if (!isset($bano_manipuladoras[$id_parametro[$k]])) {
			$bano_manipuladoras[$id_parametro[$k]] = 0;
		}
		if (!isset($estado_bano[$id_parametro[$k]])) {
			$estado_bano[$id_parametro[$k]] = "";
		}
		if (!isset($bano_implemento_aseo[$id_parametro[$k]])) {
			$bano_implemento_aseo[$id_parametro[$k]] = 0;
		}
	}


	$sqlValoresParametro = "INSERT INTO valores_param_infraestructura (cod_infraestructura, cod_parametrosInf, piso, paredes, techo, mesones, utensilios_suf, cant_mesasillas_suf, energia, agua, acueducto, gas, alcantarillado, alm_agua, area_alm, final_residuos, lavado_manos, estado_lavadomanos, manos_implemento_aseo, bano_manipuladoras, estado_bano, bano_implemento_aseo) VALUES ";
	for ($i=0; $i < sizeof($id_parametro); $i++) { 
		$sqlValoresParametro.= "('".$idInfraestructura."', '".$id_parametro[$i]."', '".$material_piso[$id_parametro[$i]]."', '".$material_paredes[$id_parametro[$i]]."', '".$material_techo[$id_parametro[$i]]."', '".$material_mesones[$id_parametro[$i]]."', '".$utensilios_suficientes[$id_parametro[$i]]."', '".$mesas_sillas_suficientes[$id_parametro[$i]]."', '".$energia[$id_parametro[$i]]."', '".$agua[$id_parametro[$i]]."', '".$acueducto[$id_parametro[$i]]."', '".$gas[$id_parametro[$i]]."', '".$alcantarillado[$id_parametro[$i]]."', '".$almacenamiento_agua[$id_parametro[$i]]."', '".$area_alm[$id_parametro[$i]]."', '".$final_residuos[$id_parametro[$i]]."', '".$lavado_manos[$id_parametro[$i]]."', '".$estado_lavadomanos[$id_parametro[$i]]."', '".$manos_implemento_aseo[$id_parametro[$i]]."', '".$bano_manipuladoras[$id_parametro[$i]]."', '".$estado_bano[$id_parametro[$i]]."', '".$bano_implemento_aseo[$id_parametro[$i]]."'), ";
	}

	$sqlValoresParametro = trim($sqlValoresParametro, ", ");

	if ($Link->query($sqlValoresParametro)===true) {

		//--- Dotaciones por parametro ---

		if (isset($_POST['id_dotacion'])) {
			$id_dotacion = $_POST['id_dotacion'];
		} else {
			$id_dotacion = NULL;
		}
		if (isset($_POST['tiene'])) {
			$tiene = $_POST['tiene'];
		} else {
			$tiene = "";
		}
		if (isset($_POST['en_uso'])) {
			$en_uso = $_POST['en_uso'];
		} else {
			$en_uso = "";
		}
		if (isset($_POST['funciona'])) {
			$funciona = $_POST['funciona'];
		} else {
			$funciona = "";
		}
		if (isset($_POST['tipo'])) {
			$tipo = $_POST['tipo'];
		} else {
			$tipo = "";
		}
		if (isset($_POST['capacidad'])) {
			$capacidad = $_POST['capacidad'];
		} else {
			$capacidad = "";
		}

		for ($l=0; $l < sizeof($id_parametro) ; $l++) { 
			if ($id_dotacion) {
				for ($m=0; $m < sizeof($id_dotacion) ; $m++) { 
					if (!isset($tiene[$id_parametro[$l]][$id_dotacion[$m]])) {
						$tiene[$id_parametro[$l]][$id_dotacion[$m]] = 0;
					}
					if (!isset($en_uso[$id_parametro[$l]][$id_dotacion[$m]])) {
						$en_uso[$id_parametro[$l]][$id_dotacion[$m]] = 0;
					}
					if (!isset($funciona[$id_parametro[$l]][$id_dotacion[$m]])) {
						$funciona[$id_parametro[$l]][$id_dotacion[$m]] = 0;
					}
					if (!isset($tipo[$id_parametro[$l]][$id_dotacion[$m]])) {
						$tipo[$id_parametro[$l]][$id_dotacion[$m]] = 0;
					}
					if (!isset($capacidad[$id_parametro[$l]][$id_dotacion[$m]])) {
						$capacidad[$id_parametro[$l]][$id_dotacion[$m]] = 0;
					}
				}
			}
		}

		if ($id_dotacion) {
			$sqlDotacionParamVal = "INSERT INTO dotacion_param_val (cod_infraestructura, cod_dotacion, tiene, enuso, tipo, funciona, capacidad) VALUES ";
			for ($l=0; $l < sizeof($id_parametro) ; $l++) { 
					for ($m=0; $m < sizeof($id_dotacion) ; $m++) { 
						if ($tiene[$id_parametro[$l]][$id_dotacion[$m]] != "" || $en_uso[$id_parametro[$l]][$id_dotacion[$m]] != "" || $tipo[$id_parametro[$l]][$id_dotacion[$m]] != "" || $capacidad[$id_parametro[$l]][$id_dotacion[$m]] || $funciona[$id_parametro[$l]][$id_dotacion[$m]] != "") {

							$sqlDotacionParamVal.="('".$idInfraestructura."', '".$id_dotacion[$m]."', '".$tiene[$id_parametro[$l]][$id_dotacion[$m]]."', '".$en_uso[$id_parametro[$l]][$id_dotacion[$m]]."', '".$tipo[$id_parametro[$l]][$id_dotacion[$m]]."', '".$funciona[$id_parametro[$l]][$id_dotacion[$m]]."', '".$capacidad[$id_parametro[$l]][$id_dotacion[$m]]."'), ";

						}
					}
			}

			$sqlDotacionParamVal = trim($sqlDotacionParamVal, ", ");
			$Link->query($sqlDotacionParamVal);
		}

		// if ($Link->query($sqlDotacionParamVal)===true) {
			$sqlBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '40', 'Creó diagnóstico de infraestructura de la sede <strong>".$nom_sede."</strong>')";
			$Link->query($sqlBitacora);
			echo '{"respuesta" : [{"exitoso" : "1", "respuesta" : "Creado con éxito", "IdInfraestructura" : "'.$idInfraestructura.'"}]}';
		// } else {
		// 	echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error al crear valores de dotacion '.$sqlDotacionParamVal.'"}]}';
		// }
	} else {
		echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error al crear valores de parámetros '.$sqlValoresParametro.'"}]}';
	}
} else {
	echo '{"respuesta" : [{"exitoso" : "0", "respuesta" : "Error al crear infraestructura '.$sqlInfraestructura.'"}]}';
}

 ?>