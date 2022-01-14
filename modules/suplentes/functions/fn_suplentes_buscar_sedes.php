<option value="">seleccione</option>
<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$periodoActual = $_SESSION['periodoActual'];
	$codigo_institucion = $Link->real_escape_string($_POST['institucion']);
	$condicionCoordinador = '';

	if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != "") {
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

	$consulta_sedes = "SELECT DISTINCT cod_sede AS codigo, nom_sede AS nombre FROM sedes$periodoActual WHERE cod_inst = '$codigo_institucion' $condicionCoordinador ORDER BY nom_sede ASC";
	$respuesta_sedes = $Link->query($consulta_sedes) or die('Error al consultar las sedes: '. $Link->error);
	if ($respuesta_sedes->num_rows > 0)
	{
		while ($sede = $respuesta_sedes->fetch_assoc())
		{
?>
	  	<option value="<?= $sede['codigo'] ?>"><?= $sede['nombre'] ?></option>
<?php
		}
	}
