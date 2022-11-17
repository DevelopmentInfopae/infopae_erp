<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';
	require_once '../../../php/funciones.php';

	$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
	$opciones = '<option value="">seleccione</option>';
  	$consulta = "SELECT DISTINCT sc.mes FROM sedes_cobertura sc WHERE sc.cod_inst = '$institucion' ORDER BY sc.mes ASC";
  	$resultado = $Link->query($consulta);
  	if($resultado->num_rows > 0){
	  	while($row = $resultado->fetch_assoc()) {
		  	$opciones .= '<option value="'. $row['mes'] .'">'. mesEnLetras($row['mes']) .'</option>';
	  	}

	  $respuestaAJAX = [
		  "estado" => 1,
		  "opciones" => $opciones,
		  "mensaje" => "Meses cargados correctamente."
	  ];
  	} 
  	else {
  		if ($institucion == "") {
	  		$respuestaAJAX = [
			  	"estado" => 1,
			  	"opciones" => $opciones,
			  	"mensaje" => ""
		  	];
  		} else {
  			$respuestaAJAX = [
			  	"estado" => 1,
			  	"opciones" => $opciones,
			  	"mensaje" => "No existen meses para la sede seleccionada."
		  	];
  		}
  	}

  echo json_encode($respuestaAJAX);