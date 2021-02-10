<?php
  	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

  	$tipoJuridico = (isset($_POST['tipoJuridico']) && $_POST['tipoJuridico'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoJuridico']) : '';

  	if ($tipoJuridico == 1) {
  		$consulta = "SELECT * FROM tipodocumento WHERE tipojuridico = '$tipoJuridico'";
  	} else {
  		$consulta = "SELECT * FROM tipodocumento WHERE tipojuridico <> ''";
  	}

	$resultado = $Link->query($consulta) or die('Error al consultar el tipo de documento: '. mysqli_error($Link));

	if ($resultado->num_rows > 0) {
		$string = '';

		while ($tipoDocumento = $resultado->fetch_object()) {
			$string.='<option value="'.$tipoDocumento->id.'">'.$tipoDocumento->Abreviatura.'</option>';
		}
	}

	echo $string;