<option value="">Seleccione...</option>
<?php 
  	require_once '../../../config.php';
  	require_once '../../../db/conexion.php';

  	$cod_inst = $_POST['cod_inst'];
  	$periodoActual = $_SESSION['periodoActual'];
  	$condicionCoordinador = '';

  	if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != '') {
  		$codigoSedes = "";
  		$documentoCoordinador = $_SESSION['num_doc'];
  		$consultaCodigoSedes = "SELECT cod_sede FROM sedes$periodoActual WHERE id_coordinador = $documentoCoordinador;";
		$respuestaCodigoSedes = $Link->query($consultaCodigoSedes) or die('Error al consultar el código de la sede ' . mysqli_error($Link));
		if ($respuestaCodigoSedes->num_rows > 0) {
			$codigoInstitucion = '';
			while ($dataCodigoSedes = $respuestaCodigoSedes->fetch_assoc()) {
				$codigoSedeRow = $dataCodigoSedes['cod_sede'];
				$consultaCodigoInstitucion = "SELECT cod_inst FROM sedes$periodoActual WHERE cod_sede = $codigoSedeRow;";
				$respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
				if ($respuestaCodigoInstitucion->num_rows > 0) {
					$dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
					$codigoInstitucionRow = $dataCodigoInstitucion['cod_inst'];
					if ($codigoInstitucionRow == $codigoInstitucion || $codigoInstitucion == '') {
						$codigoSedes .= "'$codigoSedeRow'".",";
						$codigoInstitucion = $codigoInstitucionRow; 
					}
				}
			}
		}
		$codigoSedes = substr($codigoSedes, 0 , -1);
		$condicionCoordinador = " AND cod_sede IN ($codigoSedes) ";
  	}

	$consultaInstParametros = "SELECT DISTINCT cod_sede, nom_sede FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = '".$cod_inst."' ".$condicionCoordinador. " ORDER BY nom_sede ASC";
	// exit(var_dump($consultaInstParametros));
	$resultado = $Link->query($consultaInstParametros);
	if ($resultado->num_rows > 0) {
		while ($institucion = $resultado->fetch_assoc()) { ?>
		  <option value="<?php echo $institucion['cod_sede'] ?>"><?php echo $institucion['nom_sede'] ?></option>
		<?php }
	} else { ?>
		<option value="">Sin sedes</option>
	<?php }

 ?>