<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$consultaMunicipioDefecto = "SELECT CodMunicipio AS municipioDefecto FROM parametros";
	$resultadoMunicipioDefecto = $Link->query($consultaMunicipioDefecto) or die("Error al consultar parametros: ". $Link->error);
	if ($resultadoMunicipioDefecto->num_rows > 0)
	{
		$municipio = $resultadoMunicipioDefecto->fetch_assoc();
	}

	$codDepartamento = mysqli_real_escape_string($Link, $_SESSION["p_CodDepartamento"]);
	$opciones = '<option value="">Seleccione uno</option>';

	$consulta = "SELECT u.Ciudad, u.CodigoDANE FROM ubicacion u WHERE u.ETC = 0 AND CodigoDANE LIKE '$codDepartamento%' ORDER BY u.Ciudad asc";
	$resultado = $Link->query($consulta);
	if($resultado->num_rows > 0)
	{
		while($row = $resultado->fetch_assoc())
		{
			$selected = (isset($municipio["municipioDefecto"]) && $municipio["municipioDefecto"] == $row['CodigoDANE']) ? "selected" : "";
			$opciones .= '<option value="'. $row['CodigoDANE'] .'" '. $selected  .'>'. $row['Ciudad'] .'</option>';
		}

		$respuestaAJAX = [
			"estado" => 1,
			"opciones" => $opciones,
			"mensaje" => "Municipios cargados correctamente."
		];
	}

	echo json_encode($respuestaAJAX);
