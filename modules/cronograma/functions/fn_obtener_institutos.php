<?php
	require_once '../../../db/conexion.php';
    require_once '../../../config.php';

    $municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST['municipio']) : '';

    $consulta = "SELECT DISTINCT cod_inst AS codigo, nom_inst AS nombre FROM sedes".$_SESSION["periodoActual"]." WHERE cod_mun_sede = '".$municipio."' ORDER BY nom_inst;";
    $respuesta = $Link->query($consulta) or die("Error al consultar instituciones ". $Link->error);

    $options = '<option value="">Todas</option>';
    if ($respuesta->num_rows > 0) {
    	while($instituto = $respuesta->fetch_object()) {
    		$options .= '<option value="'. $instituto->codigo .'">'. $instituto->nombre .'</option>';
    	}
    }

    echo $options;