<option value="">seleccione</option>
<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$municipio = $Link->real_escape_string($_POST['codigo_municipio']);
	$condicionRector = '';

	if ($_SESSION['perfil'] == '6' && $_SESSION['num_doc'] != '') {
		$consultaInstitucion = " SELECT codigo_inst FROM instituciones WHERE cc_rector = " .$_SESSION['num_doc'] . ";"; 
		$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institución ' . mysqli_error($Link));
		if ($respuestaInstitucion->num_rows > 0) {
			$dataInstitucion = $respuestaInstitucion->fetch_assoc();
			$codigoInstitucion = $dataInstitucion['codigo_inst'];
		}
		$condicionRector = "AND codigo_inst = $codigoInstitucion ";
	}

	$consulta = "SELECT * FROM instituciones WHERE cod_mun = '$municipio' $condicionRector ORDER BY nom_inst ASC;";
	// exit(var_dump($consulta));
	$resultado = $Link->query($consulta);
	if ($resultado->num_rows > 0) {
		while ($mun = $resultado->fetch_assoc()) { ?>
			<option value="<?php echo $mun['codigo_inst']; ?>"><?php echo $mun['nom_inst']; ?></option>
	<?php
		}
	}