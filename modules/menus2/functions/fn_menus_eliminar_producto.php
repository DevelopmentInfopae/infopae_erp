<?php 

require_once '../../../db/conexion.php';
require_once '../../../config.php';

$usuario = $_SESSION['idUsuario'];

function tablaPorMeses($tabla){
	global $Link;
	$tablaMeses = array();
	for ($i=date('m'); $i >= 1 ; $i--) { 
	  if (strlen($i) == 1) {
	    $mes = "0".$i;
	  } else {
	    $mes = $i;
	  }
	   $tablaDespMes = "SHOW TABLES LIKE '".$tabla.$mes.$_SESSION['periodoActual']."'";
	   $resultado = $Link->query($tablaDespMes);
	   if ($resultado->num_rows > 0) {
	     $tablaMeses[] = $tabla.$mes.$_SESSION['periodoActual'];
	   }
	}
	return $tablaMeses;
}

function verificarExisteDespacho($codigoProducto){
	global $Link;
	$mesesDespachos = tablaPorMeses('despachos_det');
	$sql = "SELECT ";
	for ($i=0; $i < sizeof($mesesDespachos); $i++) { 
		if ($i == 0) {
		  $sql.= "IFNULL((SELECT DISTINCT cod_Alimento FROM ".$mesesDespachos[$i]." WHERE cod_Alimento = ".$codigoProducto."), ";
		} else if ($i > 0 && $i < sizeof($mesesDespachos)-1 && $i != sizeof($mesesDespachos)-1) {
		  $sql.= "(IFNULL(SELECT DISTINCT cod_Alimento FROM ".$mesesDespachos[$i]." WHERE cod_Alimento = ".$codigoProducto."), ";
		} else if ($i == sizeof($mesesDespachos)-1) {
		 $sql.= "(SELECT DISTINCT cod_Alimento FROM ".$mesesDespachos[$i]." WHERE cod_Alimento = ".$codigoProducto.")) AS EXISTE";
		}
	}
	$result = $Link->query($sql);
	if ($result->num_rows > 0) {
		if ($row = $result->fetch_assoc()) {
			if ($row['EXISTE'] == $codigoProducto) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
}

function verificarDespachoMenu($tipoComplemento, $ordenCiclo){
	global $Link;
	$mesesDespachos = tablaPorMeses('despachos_enc');
	$valida = 0;
	for ($i=0; $i < sizeof($mesesDespachos); $i++) { 
		unset($resultado);
		$sql = "SELECT Tipo_Complem, Menus FROM ".$mesesDespachos[$i]." WHERE Tipo_Complem = '".$tipoComplemento."' AND Menus like '%".$ordenCiclo."%'";
		$resultado = $Link->query($sql);
		if ($resultado->num_rows > 0) {
			$valida++;
		}
	}
	if ($valida > 0) {
		return true;
	} else if ($valida == 0){
		return false;
	}
}

if (isset($_POST['codigoProducto'])) {
	$codigoProducto = $_POST['codigoProducto'];
} else {
	$codigoProducto = "";
}

if (isset($_POST['tipoComplemento'])) {
	$tipoComplemento = $_POST['tipoComplemento'];
} else {
	$tipoComplemento = "";
}

if (isset($_POST['ordenCiclo'])) {
	$ordenCiclo = $_POST['ordenCiclo'];
} else {
	$ordenCiclo = "";
}

$consultarDescProducto = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoProducto;
$resultadoDescProducto = $Link->query($consultarDescProducto);
if ($resultadoDescProducto->num_rows > 0) {
	if ($Producto = $resultadoDescProducto->fetch_assoc()) {
		$descripcionProducto = $Producto['Descripcion'];
	}
}


$valida = 0;

if (substr($codigoProducto, 0, 2) == "01") { //Si el producto a eliminar es menu
	if (verificarDespachoMenu($tipoComplemento, $ordenCiclo)) {
		$desactivarMenu = "UPDATE productos".$_SESSION['periodoActual']." SET Inactivo = 1 WHERE Codigo = ".$codigoProducto;
		if ($Link->query($desactivarMenu)===true) {
			$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '12', 'Desactivó el menú <strong>".$descripcionProducto."</strong> con código <strong>".$codigoProducto."</strong>') ";
		  	if ($Link->query($sqlBitacora)===true) {
				echo '{"respuesta" : [{"exitoso" : "1", "Accion" : "desactivado", "TipoProducto" : "01" }]}';
		  	} else {
		  		echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Bitacora", "Nota" : "Error al crear bitácora"}]}';
		  	}
		} else {
			echo "Error al desactivar menú : ".$desactivarMenu;
		}
	} else {
		$consultarFichaTecnica = "SELECT * FROM fichatecnica WHERE codigo = ".$codigoProducto;
		$resultadoFichaTecnica = $Link->query($consultarFichaTecnica);
		if ($resultadoFichaTecnica->num_rows > 0) {
			if ($fichaTecnica = $resultadoFichaTecnica->fetch_assoc()) {
				$IdFT = $fichaTecnica['Id'];
				$eliminarFichaTecnica="DELETE FROM fichatecnica WHERE Id = ".$IdFT;
				if ($Link->query($eliminarFichaTecnica)===true) {
					$eliminarFichaTecnicaDet = "DELETE FROM fichatecnicadet WHERE IdFT = ".$IdFT;
					if ($Link->query($eliminarFichaTecnicaDet)===true) {
						$eliminarMenu = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoProducto;
						if ($Link->query($eliminarMenu)===true) {
							$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '33', 'Eliminó el menú <strong>".$descripcionProducto."</strong>') ";
						  	if ($Link->query($sqlBitacora)===true) {
						  		echo '{"respuesta" : [{"exitoso" : "1", "Accion" : "eliminado", "Nota" : "El menú no está en despachos", "TipoProducto" : "01"}]}';
						  	} else {
						  		echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Bitacora", "Nota" : "Error al crear bitácora"}]}';
						  	}
						} else {
							echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar menu : '.$eliminarMenu.'"}]}';
						}
					} else {
						echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar fichatecnicadet : '.$eliminarFichaTecnicaDet.'"}]}';
					}
				} else {
					echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar fichatecnica : '.$eliminarFichaTecnica.'"}]}';
				}
			}
		}
	}
} else if (substr($codigoProducto, 0, 2) == "02" || substr($codigoProducto, 0, 2) == "04") {
	$consultarFichaTecnicaDetMenu = "SELECT * FROM fichatecnicadet WHERE codigo = ".$codigoProducto." GROUP BY IdFT";
	$resultadoFichaTecnicaDetMenu = $Link->query($consultarFichaTecnicaDetMenu);
	
	if ($resultadoFichaTecnicaDetMenu->num_rows > 0) { //si el preparado está relacionado
		if ($fichaTecnicaDet = $resultadoFichaTecnicaDetMenu->fetch_assoc()) {
			$idFTMenu = $fichaTecnicaDet['IdFT'];
			$consultarFichaTecnicaMenu = "SELECT * FROM fichatecnica WHERE Id = ".$idFTMenu;
			$resultadoFichaTecnicaMenu = $Link->query($consultarFichaTecnicaMenu);
			if ($resultadoFichaTecnicaMenu->num_rows > 0) {
				if ($fichaTecnicaMenu = $resultadoFichaTecnicaMenu->fetch_assoc()) {
					$codigoMenu = $fichaTecnicaMenu['Codigo'];
					$consultarMenu = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoMenu;
					$resultadoMenu = $Link->query($consultarMenu);
					if ($resultadoMenu->num_rows > 0) {
						if ($Menu = $resultadoMenu->fetch_assoc()) {
							$tipoComplemento = $Menu['Cod_Tipo_complemento'];
							$ordenCiclo = $Menu['Orden_Ciclo'];
						}
						if (verificarDespachoMenu($tipoComplemento, $ordenCiclo)) {
							$desactivarPreparado = "UPDATE productos".$_SESSION['periodoActual']." SET Inactivo = 1 WHERE Codigo = ".$codigoProducto;
							if ($Link->query($desactivarPreparado)===true) {
								$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '29', 'Desactivó el preparado <strong>".$descripcionProducto."</strong> con código <strong>".$codigoProducto."</strong>') ";
							  	if ($Link->query($sqlBitacora)===true) {
							  		echo '{"respuesta" : [{"exitoso" : "1", "Accion" : "desactivado", "TipoProducto" : "'.substr($codigoProducto, 0, 2).'" }]}';
							  	} else {
							  		echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Bitacora", "Nota" : "Error al crear bitácora"}]}';
							  	}
							} else {
								echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "desactivado", "Nota" : "Error al desactivar preparado : '.$desactivarPreparado.'"}]}';
							}
						} else {

							$consultarFichaTecnicaPrep = "SELECT * FROM fichatecnica WHERE Codigo = ".$codigoProducto;
							$resultadoFichaTecnicaPrep = $Link->query($consultarFichaTecnicaPrep);
							if ($resultadoFichaTecnicaPrep->num_rows > 0) {
								if ($Preparado = $resultadoFichaTecnicaPrep->fetch_assoc()) {
									$IdFTPreparado = $Preparado['Id'];
								}
							}


							$valida = 0;
							$error = "";
							if (substr($codigoProducto, 0, 2) == "04") {
								$consultarAlimentoIndus = "SELECT productos18.id, fichatecnicadet.codigo FROM fichatecnicadet, productos".$_SESSION['periodoActual']." WHERE fichatecnicadet.IdFT = ".$IdFTPreparado." AND productos18.codigo = fichatecnicadet.codigo";
								$resultadoAlimentoIndus = $Link->query($consultarAlimentoIndus);
								if ($resultadoAlimentoIndus->num_rows > 0) {
									while ($alimIndus = $resultadoAlimentoIndus->fetch_assoc()) {
										$borrarAlimIndus = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Id = ".$alimIndus['id'];
										if ($Link->query($borrarAlimIndus)===true) {
											$borrarCalyNut = "DELETE FROM menu_aportes_calynut WHERE cod_prod = ".$alimIndus['codigo'];
											if ($Link->query($borrarCalyNut) === true) {
												# code...
											} else {
												$valida++;
												$error.=$borrarCalyNut;
											}
										} else {
											$valida++;
											$error.=$borrarAlimIndus;
										}
									}
								} else {
									$valida++;
									$error.=$consultarAlimentoIndus;
								}
							}
							if ($valida==0) {
								$eliminarFichaTecnicaDet = "DELETE FROM fichatecnicadet WHERE IdFT = ".$IdFTPreparado;
								if ($Link->query($eliminarFichaTecnicaDet)===true) {
									$eliminarFichaTecnica = "DELETE FROM fichatecnica WHERE Id = ".$IdFTPreparado;
									if ($Link->query($eliminarFichaTecnica)===true) {
										$eliminarPreparado = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoProducto;
										if ($Link->query($eliminarPreparado)===true) {
											$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '34', 'Eliminó el preparado <strong>".$descripcionProducto."</strong>') ";
										  	if ($Link->query($sqlBitacora)===true) {
										  		echo '{"respuesta" : [{"exitoso" : "1", "Accion" : "eliminado", "Nota" : "El preparado no está en despachos", "TipoProducto" : "'.substr($codigoProducto, 0, 2).'"  }]}';
										  	} else {
										  		echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Bitacora", "Nota" : "Error al crear bitácora"}]}';
										  	}
										} else {
											echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar preparado : '.$eliminarPreparado.'"}]}';
										}
									} else {
										echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar ficha tecnica  : '.$eliminarFichaTecnica.'"}]}';
									}
								} else {
									echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar fichatecnicadet  : '.$eliminarFichaTecnicaDet.'"}]}';
								}
							} else  {
								echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar industrializado  : '.$error.'"}]}';
							}
						}
					} else {
						echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Consultar", "Nota" : "Error al obtener menú  : '.$consultarMenu.'"}]}';
					}
				}
			} else {
				echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Consultar", "Nota" : "Error al obtener menú  : '.$consultarMenu.'"}]}';
			}
		}
	} else { //si el preparado no está relacionado
		$consultarFichaTecnicaPrep = "SELECT * FROM fichatecnica WHERE Codigo = ".$codigoProducto;
		$resultadoFichaTecnicaPrep = $Link->query($consultarFichaTecnicaPrep);
		if ($resultadoFichaTecnicaPrep->num_rows > 0) {
			if ($Preparado = $resultadoFichaTecnicaPrep->fetch_assoc()) {
				$IdFTPreparado = $Preparado['Id'];
			}
		}
		$valida = 0;
		$error = "";
		if (substr($codigoProducto, 0, 2) == "04") {
			$consultarAlimentoIndus = "SELECT productos18.id, fichatecnicadet.codigo FROM fichatecnicadet, productos".$_SESSION['periodoActual']." WHERE fichatecnicadet.IdFT = ".$IdFTPreparado." AND productos18.codigo = fichatecnicadet.codigo";
			$resultadoAlimentoIndus = $Link->query($consultarAlimentoIndus);
			if ($resultadoAlimentoIndus->num_rows > 0) {
				while ($alimIndus = $resultadoAlimentoIndus->fetch_assoc()) {
					$borrarAlimIndus = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Id = ".$alimIndus['id'];
					if ($Link->query($borrarAlimIndus)===true) {
						$borrarCalyNut = "DELETE FROM menu_aportes_calynut WHERE cod_prod = ".$alimIndus['codigo'];
						if ($Link->query($borrarCalyNut) === true) {
							# code...
						} else {
							$valida++;
							$error.=$borrarCalyNut;
						}
					} else {
						$valida++;
						$error.=$borrarAlimIndus;
					}
				}
			} else {
				$valida++;
				$error.=$consultarAlimentoIndus;
			}
		}
		$eliminarPreparado = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoProducto;
		if ($valida == 0) {
			if ($Link->query($eliminarPreparado)===true) {
				$eliminarFichaTecnica = "DELETE FROM fichatecnica WHERE Id = ".$IdFTPreparado;
				if ($Link->query($eliminarFichaTecnica)===true) {
					$eliminarFichaTecnicaDet = "DELETE FROM fichatecnicadet WHERE IdFT = ".$IdFTPreparado;
					if ($Link->query($eliminarFichaTecnicaDet)) {
						$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '34', 'Eliminó el preparado <strong>".$descripcionProducto."</strong>') ";
					  	if ($Link->query($sqlBitacora)===true) {
					  		echo '{"respuesta" : [{"exitoso" : "1", "Accion" : "eliminado", "Nota" : "Preparado no relacionado", "TipoProducto" : "'.substr($codigoProducto, 0, 2).'" }]}';
					  	} else {
					  		echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Bitacora", "Nota" : "Error al crear bitácora"}]}';
					  	}
					} else {
						echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar fichatecnicadet  : '.$eliminarFichaTecnicaDet.'"}]}';
					}
				} else {
					echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar fichatecnica  : '.$eliminarFichaTecnica.'"}]}';
				}
			} else {
				echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar preparado  : '.$eliminarPreparado.'"}]}';
			}
		} else {
			echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar industrializado  : '.$error.'"}]}';
		}
	}
} else if (substr($codigoProducto, 0, 2) == "03"){

	$buscarFichaTecnicaDet = "SELECT * FROM fichatecnicadet WHERE Codigo = ".$codigoProducto;
	$resultadoFichaTecnicaDet = $Link->query($buscarFichaTecnicaDet);
	if ($resultadoFichaTecnicaDet->num_rows > 0) {
		$inactivarProducto = "UPDATE productos".$_SESSION['periodoActual']." SET Inactivo = 1 WHERE Codigo = ".$codigoProducto;
		if ($Link->query($inactivarProducto)===true) {
			$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '13', 'Desactivó el alimento <strong>".$descripcionProducto."</strong> con código <strong>".$codigoProducto."</strong>') ";
		  	if ($Link->query($sqlBitacora)===true) {
		  		echo '{"respuesta" : [{"exitoso" : "1", "Accion" : "desactivado", "TipoProducto" : "03"}]}';
		  	} else {
		  		echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Bitacora", "Nota" : "Error al crear bitácora"}]}';
		  	}
		} else {
			echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "desactivado", "Nota" : "Error al eliminar alimento  : '.$inactivarProducto.'"}]}';
		}
	} else {
		if (verificarExisteDespacho($codigoProducto)) {
			$inactivarProducto = "UPDATE productos".$_SESSION['periodoActual']." SET Inactivo = 1 WHERE Codigo = ".$codigoProducto;
			if ($Link->query($inactivarProducto)===true) {
				$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '13', 'Desactivó el alimento <strong>".$descripcionProducto."</strong> con código <strong>".$codigoProducto."</strong>') ";
			  	if ($Link->query($sqlBitacora)===true) {
					echo '{"respuesta" : [{"exitoso" : "1", "Accion" : "desactivado", "TipoProducto" : "03"}]}';
			  	} else {
			  		echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Bitacora", "Nota" : "Error al crear bitácora"}]}';
			  	}
			} else {
				echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "desactivado", "Nota" : "Error al desactivar alimento  : '.$inactivarProducto.'"}]}';
			}
		} else {
			$borrarProducto = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoProducto;
			if ($Link->query($borrarProducto)===true) {
				$borrarCalyNut = "DELETE FROM menu_aportes_calynut WHERE cod_prod = ".$codigoProducto;
				if ($Link->query($borrarCalyNut)===true) {
					$sqlBitacora = "insert into bitacora (id, fecha, usuario, tipo_accion, observacion) values ('', '".date('Y-m-d h:i:s')."', '".$usuario."', '35', 'Eliminó el alimento <strong>".$descripcionProducto."</strong>') ";
				  	if ($Link->query($sqlBitacora)===true) {
						echo '{"respuesta" : [{"exitoso" : "1", "Accion" : "eliminado", "Nota" : "El alimento no está en despachos", "TipoProducto" : "03"  }]}';
				  	} else {
				  		echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "Bitacora", "Nota" : "Error al crear bitácora"}]}';
				  	}
				} else {
					echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar calynut  : '.$borrarCalyNut.'"}]}';
				}
			} else {
				echo '{"respuesta" : [{"exitoso" : "0", "Accion" : "eliminado", "Nota" : "Error al eliminar alimento  : '.$borrarProducto.'"}]}';
			}
		}
	}
}


/*
$consultaFichaTecnica = "SELECT * FROM fichatecnica WHERE Codigo = ".$codigoProducto;
	$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
	if ($resultadoFichaTecnica->num_rows > 0) {
		if ($row = $resultadoFichaTecnica->fetch_assoc()) {
			$IdFT = $row['Id'];
			$borrarFichaTecnica = "DELETE FROM fichatecnica WHERE Id = ".$IdFT;
			if ($Link->query($borrarFichaTecnica)===true) {
				$borrarFichaTecnicaDet "DELETE FROM fichatecnicadet WHERE IdFT = ".$IdFT;
				if ($Link->query($borrarFichaTecnicaDet)===true) {
					$sqlBorrar = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoProducto;
				}
			}

		}
	}
	
	$consultaCalyNut = "SELECT * FROM menu_aportes_calynut WHERE cod_prod = ".$codigoProducto;
	$resultadoCalyNut = $Link->query($consultaCalyNut);
	if ($resultadoCalyNut->num_rows > 0) {
		if ($row = $resultado->fetch_assoc()) {
			$idCalyNut = $row['id'];
			$borrarCalyNut= "DELETE FROM menu_aportes_calynut WHERE id = ".$idCalyNut;
			if ($Link->query($borrarCalyNut)===true) {
				$sqlBorrar = "DELETE FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$codigoProducto;
			}
		}
	} */
?>