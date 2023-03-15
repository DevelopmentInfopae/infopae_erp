<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';
	require_once '../../../php/funciones.php';

	$periodoActual = $_SESSION['periodoActual'];
	$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
	$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
	$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
	
	$condicionMunicipio = $condicionInstitucion = $condicionSede = '';
	if ($municipio != '') {
		$condicionMunicipio = " AND s.cod_mun_sede = '$municipio' ";
	}
	if ($institucion != '') {
		$condicionInstitucion = " AND s.cod_inst = '$institucion' ";
	}
	if ($sede != '') {
		$condicionSede = " AND s.cod_sede = '$sede' ";
	}
	
	$opciones = '<option value="">seleccione</option>';
  	$consulta = "SELECT DISTINCT p.MES_ENTREGAS AS mes
					FROM sedes_cobertura sc 
					JOIN sedes$periodoActual s ON s.cod_sede = sc.cod_sede
					join planilla_semanas p ON p.MES = sc.mes
					WHERE 1=1
					$condicionMunicipio
					$condicionInstitucion
					$condicionSede
					ORDER BY sc.mes ASC";
	// exit(var_dump($consulta));
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