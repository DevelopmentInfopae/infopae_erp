<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$periodoActual = $_SESSION['periodoActual'];
// exit(var_dump($_POST));
$mes = ((isset($_POST['mes']) && $_POST['mes'] != "") ? $_POST['mes'] : "");
$complemento = ((isset($_POST['complemento']) && $_POST['complemento'] != "") ? $_POST['complemento'] : "");
$manipuladoras = ((isset($_POST['manipuladoras']) && $_POST['manipuladoras'] != "" ) ? $_POST['manipuladoras'] : "");
$municipio = ((isset($_POST['municipio']) && $_POST['municipio'] != "") ? $_POST['municipio'] : "");
$institucion = ((isset($_POST['institucion']) && $_POST['institucion'] != "") ? $_POST['institucion'] : "");
$sede = ((isset($_POST['sede'])&& $_POST['sede'] != "") ? $_POST['sede'] : "");
$ruta = ((isset($_POST['ruta']) && $_POST['ruta'] != "") ? $_POST['ruta'] : "");

$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
if ($respuestaSemanaPriorizacion->num_rows > 0) {
	$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
	$semana = $dataSemanaPriorizacion['semana'];
}else{
	$mes = $mes-1;
	if ($mes < 10) {
		$mes = "0".$mes;
	}
	$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
	$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
	if ($respuestaSemanaPriorizacion->num_rows > 0) {
		$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
		$semana = $dataSemanaPriorizacion['semana'];
	}else{
		$mes = $mes-1;
		if ($mes < 10) {
			$mes = "0".$mes;
		}
		$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
		$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
		if ($respuestaSemanaPriorizacion->num_rows > 0) {
			$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
			$semana = $dataSemanaPriorizacion['semana'];
		}else {
			$mes = $mes-1;
			if ($mes < 10) {
				$mes = "0".$mes;
			}
			$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
			$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
			if ($respuestaSemanaPriorizacion->num_rows > 0) {
				$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
				$semana = $dataSemanaPriorizacion['semana'];
			}else {
				$mes = $mes-1;
				if ($mes < 10) {
					$mes = "0".$mes;
				}
				$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
				$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
				if ($respuestaSemanaPriorizacion->num_rows > 0) {
					$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
					$semana = $dataSemanaPriorizacion['semana'];
				}else {
					$mes = $mes-1;
					if ($mes < 10) {
						$mes = "0".$mes;
					}
					$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
					$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
					if ($respuestaSemanaPriorizacion->num_rows > 0) {
						$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
						$semana = $dataSemanaPriorizacion['semana'];
					}else {
						$mes = $mes-1;
						if ($mes < 10) {
							$mes = "0".$mes;
						}
						$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
						$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
						if ($respuestaSemanaPriorizacion->num_rows > 0) {
							$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
							$semana = $dataSemanaPriorizacion['semana'];
						}else {
							$mes = $mes-1;
							if ($mes < 10) {
								$mes = "0".$mes;
							}
							$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
							$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
							if ($respuestaSemanaPriorizacion->num_rows > 0) {
								$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
								$semana = $dataSemanaPriorizacion['semana'];
							}else {
								$mes = $mes-1;
								if ($mes < 10) {
									$mes = "0".$mes;
								}
								$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
								$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
								if ($respuestaSemanaPriorizacion->num_rows > 0) {
									$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
									$semana = $dataSemanaPriorizacion['semana'];
								}else{
									$mes = $mes-1;
									if ($mes < 10) {
										$mes = "0".$mes;
									}
									$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
									$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
									if ($respuestaSemanaPriorizacion->num_rows > 0) {
										$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
										$semana = $dataSemanaPriorizacion['semana'];
									}else {
										$mes = $mes-1;
										if ($mes < 10) {
											$mes = "0".$mes;
										}
										$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
										$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
										if ($respuestaSemanaPriorizacion->num_rows > 0) {
											$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
											$semana = $dataSemanaPriorizacion['semana'];
										}else {
											$mes = $mes-1;
											if ($mes < 10) {
												$mes = "0".$mes;
											}
											$consultaSemanaPriorizacion = " SELECT semana FROM sedes_cobertura WHERE mes = '$mes' ORDER BY semana DESC LIMIT 1 ";
											$respuestaSemanaPriorizacion = $Link->query($consultaSemanaPriorizacion) or die ('Error al consultar la priorizacion ' .mysqli_error($Link));
											if ($respuestaSemanaPriorizacion->num_rows > 0) {
												$dataSemanaPriorizacion = $respuestaSemanaPriorizacion->fetch_assoc();
												$semana = $dataSemanaPriorizacion['semana'];
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

$condicionManipuladoras = "";
if ($manipuladoras  == 'si') {
	if ($complemento == 'ALL') {
		$condicionManipuladoras .= " AND s.cantidad_Manipuladora != 0 ";
	}else{
		$condicionManipuladoras .= " AND s.Manipuladora_".$complemento. " != 0 ";
	}
}else if ($manipuladoras == 'no'){
	if ($complemento == 'ALL') {
		$condicionManipuladoras .= " AND s.cantidad_Manipuladora = 0 ";
	}else {
		$condicionManipuladoras .= " AND s.Manipuladora_".$complemento. " = 0 ";
	}	
}

$consultaTabla = "";
if ($ruta != "") {
	$consultaTabla = "   SELECT DISTINCT(s.cod_sede) AS codigo, 
								u.Ciudad AS ciudad, 
								s.nom_inst AS institucion, 
								s.nom_sede AS sede 
						    FROM sedes$periodoActual s
						    INNER JOIN priorizacion$semana p ON s.cod_sede = p.cod_sede
						    INNER JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede
						    INNER JOIN rutasedes r ON r.cod_Sede = s.cod_sede
						    INNER JOIN rutas ru ON r.IDRUTA = ru.ID
						    WHERE ru.ID = $ruta AND " .(($complemento == 'ALL') ? 'p.cant_Estudiantes' : "p.$complemento"). " != 0 $condicionManipuladoras
						    ORDER BY u.Ciudad, s.cod_inst, s.cod_sede 
						    ";
}
else if ($ruta == "") {
	$condicionInstitucion = "";
	$condicionSede = "";
	$condicionMunicipio = "";
	if ($institucion != "") {
		$condicionInstitucion = " AND s.cod_inst = $institucion ";
		if ($sede != "") {
			$condicionSede = " AND s.cod_sede = $sede ";
		}
	}
	if ($municipio != "ALL") {
		$condicionMunicipio = " AND u.CodigoDANE = $municipio " ; 
	}
	$consultaTabla = "  SELECT DISTINCT(s.cod_sede) AS codigo,
								u.Ciudad AS ciudad,
								s.nom_inst AS institucion,
								s.nom_sede AS sede
							FROM sedes$periodoActual s
							INNER JOIN priorizacion$semana p ON s.cod_sede = p.cod_sede
							INNER JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede
							WHERE 1 = 1 $condicionMunicipio AND " .(($complemento == 'ALL') ? 'p.cant_Estudiantes' : "p.$complemento"). " != 0
							          $condicionManipuladoras $condicionInstitucion $condicionSede
							ORDER BY u.Ciudad, s.cod_inst, s.cod_sede 	
							";
}
// exit(var_dump($consultaTabla));
$respuestaTabla = $Link->query($consultaTabla) or die ('Error al consultar las sedes priorizadas ' . mysqli_error($Link));
if ($respuestaTabla->num_rows > 0) {
	while ($dataTabla = $respuestaTabla->fetch_assoc()) {
		$sedesPriorizadas[] = $dataTabla; 
	}
}
?>

<?php if (isset($sedesPriorizadas)): ?>
	<?php foreach ($sedesPriorizadas as $key => $value): ?>
		<tr>
			<td><input type="checkbox" name="sede[]" class="checkInst " value="<?= $value['codigo'] ?>"></td>
			<td><?= $value['ciudad']; ?></td>
			<td><?= $value['institucion']; ?></td>
			<td><?= $value['sede']; ?></td>
		</tr>
	<?php endforeach ?>
<?php endif ?>
