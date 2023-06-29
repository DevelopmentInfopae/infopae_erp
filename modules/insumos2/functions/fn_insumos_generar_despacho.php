<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once 'fn_insumos_generar_presentaciones.php';
$periodoActual = $_SESSION['periodoActual'];

function obtenerNumDoc($tabla, $Link) {
	$consulta = " SELECT Numero FROM ".$tabla. " ORDER BY Numero DESC LIMIT 1 ";
	$respuesta = $Link->query($consulta) or die ('Error al consultar el consecutivo ' .mysqli_error($Link));
	if ($respuesta->num_rows > 0) {
		if ($num = $respuesta->fetch_assoc()) {
			$numDoc = $num['Numero'];
			$numDoc = $numDoc + 1;
		}
		return $numDoc;
	} else {
		return "1";
	}	
}

function obtenerCoberturas ($sede, $Link, $mes, $complemento) {
	$cupos = [];
	$coberturas = [];
	$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
	$consultaComplementos = " SELECT CODIGO FROM tipo_complemento ";
	$respuestaComplementos = $Link->query($consultaComplementos);
	if ($respuestaComplementos->num_rows > 0) {
		while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
			$complementos[] = $dataComplementos['CODIGO'];
		}
	}

	if ($complemento !== 'ALL') {
		$concatEtarios = '';
		$concatCobertura = '';
		for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
			$concatEtarios .= " Etario".$i."_".$complemento." AS Cobertura_G".$i.",";
		}

		foreach ($complementos as $key => $value) {
			$concatCobertura .= '(';
			for ($i=1; $i<=$cantGruposEtarios ; $i++) { 
				$concatCobertura .= " Etario".$i."_".$value." + ";
			}
			$concatCobertura = trim($concatCobertura, " + ");
			$concatCobertura .= " ) AS Cobertura_".$value.", ";
		}

		$consultaCupos = " SELECT 	cant_Estudiantes AS Cupos,
											$concatEtarios
											$concatCobertura
											mes
									FROM sedes_cobertura WHERE cod_sede = '$sede' AND $complemento != 0 
									GROUP BY mes 
									ORDER BY SEMANA DESC ";

	}else if ($complemento == 'ALL') {
		$concatEtarios = '';
		$concatCobertura = '';

		for ($i=1; $i<=$cantGruposEtarios ; $i++) { 
			$concatEtarios .= '(';
			foreach ($complementos as $key => $value) {
				$concatEtarios .= " Etario".$i."_".$value." + ";
			}
			$concatEtarios = trim($concatEtarios, " + ");
			$concatEtarios .= " ) AS Cobertura_G".$i.", ";
		}

		foreach ($complementos as $key => $value) {
			$concatCobertura .= '(';
			for ($i=1; $i<=$cantGruposEtarios ; $i++) { 
				$concatCobertura .= " Etario".$i."_".$value." + ";
			}
			$concatCobertura = trim($concatCobertura, " + ");
			$concatCobertura .= " ) AS Cobertura_".$value.", ";
		}

		$consultaCupos = "SELECT 	cant_Estudiantes AS Cupos,
											$concatEtarios
											$concatCobertura	
											mes
										FROM sedes_cobertura 
										WHERE cod_sede = '".$sede."' GROUP BY mes ORDER BY SEMANA DESC";
	}
	// exit(var_dump($consultaCupos));
	$resultadoCupos = $Link->query($consultaCupos); 
	if ($resultadoCupos->num_rows > 0) {
		while ($cps = $resultadoCupos->fetch_assoc()) {
			$cupos[$cps['mes']]['Cobertura'] = $cps['Cupos'];
			for ($i=1; $i<=$cantGruposEtarios ; $i++) { 
				$cupos[$cps['mes']]['Cobertura_G'.$i] = $cps['Cobertura_G'.$i];
			}
			foreach ($complementos as $key => $value) {
				$cupos[$cps['mes']]['Cobertura_'.$value] = $cps['Cobertura_'.$value];
			}
		}
	}

	if (isset($cupos[$mes])) {
		// if ($complemento == 'APS') {
		// 	$coberturas['Cobertura'] = $cupos[$mes]['Cobertura_APS'];
		// } else if ($complemento == 'CAJMRI') {
		// 	$coberturas['Cobertura'] = $cupos[$mes]['Cobertura_CAJMRI'];
		// } else if ($complemento == 'CAJTRI') {
		// 	$coberturas['Cobertura'] = $cupos[$mes]['Cobertura_CAJTRI'];
		// } else if ($complemento == 'CAJMPS') {
		// 	$coberturas['Cobertura'] = $cupos[$mes]['Cobertura_CAJMPS'];
		// } else if ($complemento == 'CAJTPS') {
		// 	$coberturas['Cobertura'] = $cupos[$mes]['Cobertura_CAJTPS'];
		// } else if ($complemento == 'RPC') {
		// 	$coberturas['Cobertura'] = $cupos[$mes]['Cobertura_RPC'];
		// } else if ($complemento == 'ALL') {
		// 	$coberturas['Cobertura'] = $cupos[$mes]['Cobertura'];
		// }

		foreach ($complementos as $key => $value) {
			if ($complemento == $value) {
				$coberturas['Cobertura'] = $cupos[$mes]['Cobertura_'.$value];
			}
			if ($complemento == 'ALL') {
				$coberturas['Cobertura'] = $cupos[$mes]['Cobertura'];
			}
		}

		for ($i=1; $i<=$cantGruposEtarios; $i++) { 
			$coberturas['Cobertura_G'.$i] =  $cupos[$mes]['Cobertura_G'.$i];
		}
		foreach ($complementos as $key => $value) {
			$coberturas['Cobertura_'.$value] = $cupos[$mes]['Cobertura_'.$value];
		}
	} else {
		if (count($cupos) > 0) {
			$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
			$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
			if ($respuestaMes->num_rows > 0) {
				$dataMes = $respuestaMes->fetch_assoc();
				$mesA = $dataMes['mes'];
			}else {
				$mes = $mes-1;
				if ($mes < 10) {
					$mes = "0".$mes;
				}
				$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
				$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
				if ($respuestaMes->num_rows > 0) {
					$dataMes = $respuestaMes->fetch_assoc();
					$mesA = $dataMes['mes'];
				}else{
					$mes = $mes-1;
					if ($mes < 10) {
						$mes = "0".$mes;
					}
					$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
					$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
					if ($respuestaMes->num_rows > 0) {
						$dataMes = $respuestaMes->fetch_assoc();
						$mesA = $dataMes['mes'];
					}else {
						$mes = $mes-1;
						if ($mes < 10) {
							$mes = "0".$mes;
						}
						$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
						$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
						if ($respuestaMes->num_rows > 0) {
							$dataMes = $respuestaMes->fetch_assoc();
							$mesA = $dataMes['mes'];
						}else {
							$mes = $mes-1;
							if ($mes < 10) {
								$mes = "0".$mes;
							}
							$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
							$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
							if ($respuestaMes->num_rows > 0) {
								$dataMes = $respuestaMes->fetch_assoc();
								$mesA = $dataMes['mes'];
							}else {
								$mes = $mes-1;
								if ($mes < 10) {
									$mes = "0".$mes;
								}
								$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
								$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
								if ($respuestaMes->num_rows > 0) {
									$dataMes = $respuestaMes->fetch_assoc();
									$mesA = $dataMes['mes'];
								}else{
									$mes = $mes-1;
									if ($mes < 10) {
										$mes = "0".$mes;
									}
									$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
									$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
									if ($respuestaMes->num_rows > 0) {
										$dataMes = $respuestaMes->fetch_assoc();
										$mesA = $dataMes['mes'];
									}else {
										$mes = $mes-1;
										if ($mes < 10) {
											$mes = "0".$mes;
										}
										$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
										$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
										if ($respuestaMes->num_rows > 0) {
											$dataMes = $respuestaMes->fetch_assoc();
											$mesA = $dataMes['mes'];
										}else {
											$mes = $mes-1;
											if ($mes < 10) {
												$mes = "0".$mes;
											}
											$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
											$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
											if ($respuestaMes->num_rows > 0) {
												$dataMes = $respuestaMes->fetch_assoc();
												$mesA = $dataMes['mes'];
											}else{
												$mes = $mes-1;
												if ($mes < 10) {
													$mes = "0".$mes;
												}
												$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
												$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
												if ($respuestaMes->num_rows > 0) {
													$dataMes = $respuestaMes->fetch_assoc();
													$mesA = $dataMes['mes'];
												}else {
													$mes = $mes-1;
													if ($mes < 10) {
														$mes = "0".$mes;
													}
													$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
													$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
													if ($respuestaMes->num_rows > 0) {
														$dataMes = $respuestaMes->fetch_assoc();
														$mesA = $dataMes['mes'];
													}else {
														$mes = $mes-1;
														if ($mes < 10) {
															$mes = "0".$mes;
														}
														$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
														$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
														if ($respuestaMes->num_rows > 0) {
															$dataMes = $respuestaMes->fetch_assoc();
															$mesA = $dataMes['mes'];
														}else {
															$mes = $mes-1;
															if ($mes < 10) {
																$mes = "0".$mes;
															}
															$consultaMes = " SELECT mes FROM sedes_cobertura WHERE mes = '$mes'";
															$respuestaMes = $Link->query($consultaMes) or die('Error al consular los meses' . mysqli_error($Link));
															if ($respuestaMes->num_rows > 0) {
																$dataMes = $respuestaMes->fetch_assoc();
																$mesA = $dataMes['mes'];
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			$cupos_duplicar = ($cupos[$mesA]); 
			$coberturas['Cobertura'] = $cupos_duplicar['Cobertura'];
			for ($i=1; $i<=$cantGruposEtarios ; $i++) { 
				$coberturas['Cobertura_G'.$i] = $cupos_duplicar['Cobertura_G'.$i];
			}
			foreach ($complementos as $key => $value) {
				$coberturas['Cobertura_'.$value] = $cupos_duplicar['Cobertura_'.$value];	
			}
		}
	}
	return $coberturas;					
}

function calcularCantidad ($cins, $sede, $Link, $mes, $complemento) {
	$consultaParametro = "SELECT CantidadCupos FROM parametros";
	$resultadoParametro = $Link->query($consultaParametro);
	if ($resultadoParametro->num_rows > 0) {
		if ($parametro = $resultadoParametro->fetch_assoc()) {
			$cantCuposCalcular = $parametro['CantidadCupos'];
		}
	} else {
		$cantCuposCalcular = 100;
	}
	$datos = datosProducto($cins, $sede, $Link);
	$cantxMes = $datos['cantxMes'] > 0 ? $datos['cantxMes'] : 1;
	// exit(var_dump($cantCuposCalcular));
	if (strpos($datos['uMedida2'], " kg") || strpos($datos['uMedida2'], " lt")) {
		$cantxMes = $cantxMes * 1000;
	} else if (strpos($datos['uMedida2'], " lb")) {
		$cantxMes = $cantxMes * 1000;
	}
	// exit(var_dump($cantxMes));
	$conteoIns = substr($cins, 2, 2);
	if ($conteoIns == '01') { 
		$coberturas = obtenerCoberturas($sede, $Link, $mes, $complemento);
		$cantidad = ceil($coberturas['Cobertura'] / $cantCuposCalcular) * $cantxMes;
		// exit(var_dump($cantidad));
	} else if ($conteoIns == "02") {

		$sentComp = $Link->query(" SELECT CODIGO FROM tipo_complemento ORDER BY CODIGO ");
		if ($sentComp->num_rows > 0) {
			while ($dataComplementos2 = $sentComp->fetch_object()) {
				if ($complemento == $dataComplementos2->CODIGO) {
					$select = " Manipuladora_$dataComplementos2->CODIGO ";
				}
				if ($complemento == 'ALL') {
					$select = 'cantidad_Manipuladora';
				}
			}
		}

		// if ($complemento == 'APS') {
		// 	$select = 'Manipuladora_APS';
		// } else if ($complemento == 'CAJMRI') {
		// 		$select = 'Manipuladora_CAJMRI';
		// } else if ($complemento == 'CAJTRI') {
		// 		$select = 'Manipuladora_CAJTRI';
		// } else if ($complemento == 'CAJMPS') {
		// 		$select = 'Manipuladora_CAJMPS';
		// }else if ($complemento == 'CAJTPS') {
		// 		$select = 'Manipuladora_CAJTPS';
		// }else if ($complemento == 'RPC') {
		// 		$select = 'Manipuladora_RPC';
		// }else if ($complemento == 'ALL') {
		// 		$select = 'cantidad_Manipuladora';
		// }
		$consultaManipuladores = "SELECT ".$select." AS manipuladores FROM sedes".$_SESSION['periodoActual']." WHERE cod_sede = '".$sede."'";
		$resultadoManipuladores = $Link->query($consultaManipuladores);
		if ($resultadoManipuladores->num_rows > 0) {
			if ($manipInf = $resultadoManipuladores->fetch_assoc()) {
				$manipuladores = $manipInf['manipuladores'];
			}
			$cantidad = $cantxMes * $manipuladores;
		}

	}else if ($conteoIns == "03" || $conteoIns == "04") {//individual
		$cantidad = $cantxMes;
	}

	$presentaciones = calcularPresentaciones($cantidad, $cins, $Link);
	$presentaciones[6] = $cantidad;
	return $presentaciones;
}

function datosProducto($cins, $sede, $Link){
	$datosP = [];
	$consProducto = "SELECT FORMAT(CantidadUnd1, 0) AS CantidadUnd1, NombreUnidad1, NombreUnidad2, CantidadUnd2 FROM productos".$_SESSION['periodoActual']." WHERE Codigo = '".$cins."'";
	$resultado = $Link->query($consProducto);
	if ($resultado->num_rows > 0) {
		if ($producto = $resultado->fetch_assoc()) {
			$datosP['cantxMes'] = str_replace(",", "", $producto['CantidadUnd1']);
			$datosP['uMedida'] = $producto['NombreUnidad1'];
			$datosP['uMedida2'] = $producto['NombreUnidad2'];
			$datosP['cantUMedida'] = $producto['CantidadUnd2'];
		}
	}
	return $datosP;
}

function obtenerManipuladoras($sede, $complemento, $Link){
	$manipuladora ; 
	$columna = (($complemento == 'ALL') ? 'cantidad_Manipuladora' : 'manipuladora_'.$complemento);
	$consultaManipuladora = " SELECT $columna AS numManipuladora FROM sedes".$_SESSION['periodoActual'] ." WHERE cod_sede = $sede ";
	$respuestaManipuladora = $Link->query($consultaManipuladora);
	if ($respuestaManipuladora->num_rows > 0) {
		$dataManipuladora = $respuestaManipuladora->fetch_assoc();
		$manipuladora = $dataManipuladora['numManipuladora'];
	}
	return $manipuladora;
}

function obtenerNomTipoMov($tipoMov, $Link){
	$nomTipoMov = "";
	$consultaNomTipoMov = " SELECT Movimiento FROM tipomovimiento WHERE Id = $tipoMov ";
	$respuestaNomTipoMov = $Link->query($consultaNomTipoMov);
	if ($respuestaNomTipoMov->num_rows > 0) {
		$dataNomTipoMov = $respuestaNomTipoMov->fetch_assoc();
		$nomTipoMov = $dataNomTipoMov['Movimiento'];
	}
	return $nomTipoMov;
}

function obterNomProveedor($tipo_despacho, $proveedor, $Link){
	$nomProveedor = "";
	if ($tipo_despacho == "1") {
		$consultaNomProveedor = " SELECT Nombrecomercial AS nombre FROM proveedores WHERE Nitcc = $proveedor ";
	}else if ($tipo_despacho == "2") {
		$consultaNomProveedor = " SELECT Nombre AS nombre FROM empleados WHERE Nitcc = $proveedor ";
	}
	$respuestaNomProveedor = $Link->query($consultaNomProveedor);
	if ($respuestaNomProveedor->num_rows > 0) {
		$dataNomProveedor = $respuestaNomProveedor->fetch_assoc();
		$nomProveedor = $dataNomProveedor['nombre'];
	}
	return $nomProveedor;
}

function obtenerCantDias ($cobertura, $Link, $mes){
	$consultaCantDias = "SELECT COUNT(DIA) as numero FROM planilla_semanas WHERE MES = '$mes'";
	$respuestaCantDias = $Link->query($consultaCantDias) or die ('Error al consultar el numero de dias' . mysqli_error($Link));
	if ($respuestaCantDias->num_rows > 0) {
		$dataCantDias = $respuestaCantDias->fetch_assoc();
		$cantDias = $dataCantDias['numero'];
	}
	return $cantDias;
}

if (isset($_POST['tipo_despacho'])) {
	$tipo_despacho = $_POST['tipo_despacho'];
} else {
	$tipo_despacho = "";
}

if (isset($_POST['proveedor'])) {
	$proveedor = $_POST['proveedor'];
}else{
	$proveedor = "";
}

if (isset($_POST['mes'])) {
	$mes = $_POST['mes'];
}else{
	$mes = "";
}

if (isset($_POST['complemento'])) {
	$complemento = $_POST['complemento'];
}else {
	$complemento = "";
}

if (isset($_POST['municipio'])) {
	$municipio = $_POST['municipio'];
}else {
	$municipio = "";
}

if (isset($_POST['institucion_desp'])) {
	$instituciones = $_POST['institucion_desp'];
}else {
	$instituciones = "";
}

if (isset($_POST['sede'])) {
	$sedes = $_POST['sede'];
}else {
	$sedes = "";
}

if (isset($_POST['productoDespacho'])) {
	$productoDespacho = $_POST['productoDespacho'];
}else {
	$productoDespacho = "";
}

if (isset($_POST['DescInsumo'])) {
	$DescInsumo = $_POST['DescInsumo'];
} else {
	$DescInsumo = "";
}

if (isset($_POST['bodegaOrigen'])) {
	$bodegaOrigen = $_POST['bodegaOrigen'];
}else {
	$bodegaOrigen = "";
}

if (isset($_POST['tipoTransporte'])) {
	$tipoTransporte = $_POST['tipoTransporte'];
}else {
	$tipoTransporte = "";
}

if (isset($_POST['placa'])) {
	$placa = $_POST['placa'];	
}else {
	$placa = "";
}

if (isset($_POST['conductor'])) {
	$conductor = $_POST['conductor'];
}else{
	$conductor = "";
}

$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
$consultaComplementos = " SELECT CODIGO FROM tipo_complemento ";
$respuestaComplementos = $Link->query($consultaComplementos);
if ($respuestaComplementos->num_rows > 0) {
	while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
		$complementos[] = $dataComplementos['CODIGO'];
	}
}

$productos_por_despacho = [];

$consultarResponsable = "SELECT * FROM usuarios WHERE id = ".$_SESSION['idUsuario'];
$resultadoResponsable = $Link->query($consultarResponsable);
if ($resultadoResponsable->num_rows > 0) {
	if ($responsable = $resultadoResponsable->fetch_assoc()) {
		$nombreResp = $responsable['nombre'];
		$loginResp = $responsable['email'];
	}
}

$error = 0;
$descError = "";
$validaTablas = 0;
$validaProductos = 0;
$insumosmov = "insumosmov".$mes.$periodoActual;

$Coberturas = '';
$coberturasInsert = '';
for ($i=1; $i<=$cantGruposEtarios ; $i++) { 
	$Coberturas .=  " Cobertura_G$i int(10) DEFAULT 0, ";
	$coberturasInsert .= "Cobertura_G".$i.", ";
}

$sedesNumDoc = [];
$queryinsumosmov = "CREATE TABLE IF NOT EXISTS `$insumosmov` (
								  `Documento` varchar(10) DEFAULT '',
								  `Numero` int(10) UNSIGNED DEFAULT '0',
								  `Tipo` varchar(100) DEFAULT '',
								  `FechaDoc` varchar(45) DEFAULT '',
								  `BodegaOrigen` bigint(20) UNSIGNED DEFAULT '0',
								  `BodegaDestino` bigint(20) UNSIGNED DEFAULT '0',
								  `Nombre` varchar(200) DEFAULT '',
								  `Nitcc` varchar(20) DEFAULT '',
								  `Aprobado` tinyint(1) DEFAULT '0',
								  `NombreResponsable` varchar(60) DEFAULT '',
								  `LoginResponsable` varchar(30) DEFAULT '',
								  `DocOrigen` varchar(10) DEFAULT '',
								  `NumDocOrigen` int(10) UNSIGNED DEFAULT '0',
								  `Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
								  `FechaMYSQL` datetime DEFAULT NULL,
								  `Anulado` tinyint(1) DEFAULT '0',
								  `TipoTransporte` varchar(50) NOT NULL DEFAULT '',
								  `Placa` varchar(10) NOT NULL DEFAULT '',
								  `ResponsableRecibe` varchar(45) NOT NULL DEFAULT '',
								  `Cobertura` int(10) DEFAULT 0,
								  $Coberturas
								  `Complemento` varchar(20) DEFAULT '',
								  `CantDias` tinyint(4) DEFAULT 1,
								  `NumManipuladoras` varchar(20) DEFAULT 1,
								  PRIMARY KEY (`Id`)
								) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";

if ($Link->query($queryinsumosmov)===true) {
	$validaTablas++;
} else {
	exit(" Error ".$queryinsumosmov);
}

$numerosDespachos = "";

// validaciones con sedes que ya tengan creadas insumos 
foreach ($sedes as $key => $sede) {
	$consultaValidacion = " SELECT s.nom_sede as sede, i.Complemento FROM  $insumosmov i INNER JOIN sedes$periodoActual s ON s.cod_sede = i.BodegaDestino WHERE BodegaDestino = $sede AND Complemento = '$complemento' ";
	$respuestaValidacion = $Link->query($consultaValidacion) or die ('Error al validar ' . $consultaValidacion);
	if ($respuestaValidacion->num_rows > 0) {
		$dataValidacion = $respuestaValidacion->fetch_assoc();
		$respuestaAjax = [
			'sede' => $dataValidacion['sede'],
			'complemento' => $complemento
		];
		echo json_encode($respuestaAjax);
		exit();
	}
}

$insertinsumosmov = "INSERT INTO $insumosmov (	Documento, 
																Numero, 
																Tipo, 
																BodegaOrigen, 
																BodegaDestino, 
																Nombre, 
																Nitcc, 
																Aprobado, 
																NombreResponsable, 
																LoginResponsable, 
																FechaMYSQL, 
																Anulado, 
																TipoTransporte, 
																Placa, 
																ResponsableRecibe, 
																Cobertura, 
																$coberturasInsert 
																Complemento, 
																CantDias, 
																NumManipuladoras) 
														VALUES ";
	
$numDoc = obtenerNumDoc($insumosmov, $Link);
foreach ($sedes as $key => $sede) {
	$manipuladoras = obtenerManipuladoras($sede, $complemento, $Link);
	$coberturas_sedes = obtenerCoberturas($sede, $Link, $mes, $complemento); 
	$nomTipoMov = obtenerNomTipoMov($tipo_despacho, $Link);
	$nombre_proveedor = obterNomProveedor($tipo_despacho, $proveedor, $Link);
	$sedesNumDoc[$sede] = $numDoc;
	$numerosDespachos.=$numDoc.", ";
	$cantDias = obtenerCantDias($coberturas_sedes['Cobertura'], $Link, $mes);
	$insertinsumosmov.="('DESI', 
								'".$sedesNumDoc[$sede]."', 
								'".$nomTipoMov."', 
								'".$bodegaOrigen."', 
								'".$sede."', 
								'".$nombre_proveedor."', 
								'".$proveedor."', 
								'0', 
								'".$nombreResp."', 
								'".$loginResp."', 
								'".date('Y-m-d H:i:s')."', 
								'0', 
								'".$tipoTransporte."', 
								'".$placa."', 
								'".$conductor."', 
								'".$coberturas_sedes['Cobertura']."', ";
								for ($i=1; $i<=$cantGruposEtarios ; $i++) { 
									$insertinsumosmov .= "'".$coberturas_sedes['Cobertura_G'.$i]."',"; 
								}
								$insertinsumosmov .= "'".($complemento == "ALL" ? "Total cobertura" : $complemento)."', 
								'".$cantDias."',
								'".$manipuladoras."'), ";
	$numDoc++;
}

$insertinsumosmov = trim($insertinsumosmov, ", ");
if ($Link->query($insertinsumosmov)===true) {
	$validaProductos++;
} else {
	exit(" Error ".$insertinsumosmov);
}

$insumosmovdet = "insumosmovdet".$mes.$_SESSION['periodoActual'];
$queryinsumosmovdet = "CREATE TABLE IF NOT EXISTS `$insumosmovdet` (
										`Documento` varchar(10) DEFAULT '',
										`Numero` int(10) DEFAULT '0',
										`Item` int(10) UNSIGNED DEFAULT '0',
										`CodigoProducto` varchar(20) DEFAULT '',
										`Descripcion` text NOT NULL,
										`Cantidad` decimal(28,8) DEFAULT '0.00000000',
										`BodegaOrigen` bigint(20) UNSIGNED DEFAULT '0',
										`BodegaDestino` bigint(20) UNSIGNED DEFAULT '0',
										`Id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
										`Umedida` varchar(255) DEFAULT '',
										`CantUmedida` decimal(20,4) DEFAULT '0.0000',
										`Factor` decimal(28,8) DEFAULT '0.00000000',
										`Id_Usuario` int(10) UNSIGNED DEFAULT '0',
										`Lote` varchar(45) NOT NULL DEFAULT '',
										`FechaVencimiento` date DEFAULT NULL,
										`CantU1` decimal(28,8) DEFAULT '0.00000000',
										`CantU2` decimal(28,8) DEFAULT '0.00000000',
										`CantU3` decimal(28,8) DEFAULT '0.00000000',
										`CantU4` decimal(28,8) DEFAULT '0.00000000',
										`CantU5` decimal(28,8) DEFAULT '0.00000000',
										`CanTotalPresentacion` decimal(28,8) DEFAULT '0.00000000',
										`Complemento` varchar(20) DEFAULT '',
										PRIMARY KEY (`Id`)
										) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
										";
if ($Link->query($queryinsumosmovdet)===true) {
	$validaTablas++;
} else {
	exit(" Error ".$queryinsumosmovdet);
}

$insertinsumosmovdet = "INSERT INTO $insumosmovdet (	Documento, 
																		Numero, 
																		Item,
																		CodigoProducto, 
																		Descripcion, 
																		Cantidad, 
																		BodegaOrigen, 
																		BodegaDestino, 
																		Umedida, 
																		CantUmedida, 
																		Factor, 
																		Id_Usuario, 
																		CantU2, 
																		CantU3, 
																		CantU4, 
																		CantU5, 
																		CanTotalPresentacion, 
																		Complemento) 
																VALUES ";
foreach ($sedes as $key => $sede) {
	$numItem = 0;
	foreach ($productoDespacho as $keyIns => $producto) {
		$datos = datosProducto($producto, $sede, $Link);
		if (substr($producto, 0, 4) == '0504') {
			if (!isset($productos_por_despacho[$sede][$producto])) {
				$productos_por_despacho[$sede][$producto] = 1;
			} else {
				continue;
			}
		}
		$presentaciones = calcularCantidad($producto, $sede, $Link, $mes, $complemento);
		if ($presentaciones[6] < 1) {
			continue;
		}
		$numItem++;
		$insertinsumosmovdet.="(	'DESI', 
											'".$sedesNumDoc[$sede]."', 
											'".$numItem."', 
											'".$producto."', 
											'".$DescInsumo[$keyIns]."', 
											'".$presentaciones[6]."', 
											'".$bodegaOrigen."', 
											'".$sede."', 
											'".$datos['uMedida2']."', 
											'".$datos['cantUMedida']."', 
											'1', 
											'".$_SESSION['idUsuario']."', 
											'".$presentaciones[1]."', 
											'".$presentaciones[2]."', 
											'".$presentaciones[3]."', 
											'".$presentaciones[4]."', 
											'".$presentaciones[5]."', 
											'".($complemento == "ALL" ? "Total cobertura" : $complemento)."'), ";
	}
}

$insertinsumosmovdet = trim($insertinsumosmovdet, ", ");
if ($Link->query($insertinsumosmovdet)===true) {
	$validaProductos++;
} else {
	exit(" Error ".$insertinsumosmovdet);
}

if ($validaProductos > 0) {
	echo "1";
} else {
	echo "0";
}