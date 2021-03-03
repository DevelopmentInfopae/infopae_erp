<?php
    require_once '../../../db/conexion.php';
    require_once '../../../config.php';

    $institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST['institucion']) : '';

    $consulta = "SELECT DISTINCT cod_sede AS codigo, nom_sede AS nombre FROM sedes".$_SESSION["periodoActual"]." WHERE cod_inst = '".$institucion."';";
    $respuesta = $Link->query($consulta) or die("Error al consultar municipios ". $link->error);

    $options = '<option value="">Todas</option>';
    if ($respuesta->num_rows > 0) {
    	while($sede = $respuesta->fetch_object()) {
    		$options .= '<option value="'. $sede->codigo .'">'. $sede->nombre .'</option>';
    	}
    }

    echo $options;