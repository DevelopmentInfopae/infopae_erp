<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $data = [];
  $periodoActual = $_SESSION['periodoActual'];
  // $municipio   = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
  // $institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";

  $consultaNovedad = " SELECT np.id, u.Ciudad as municipio, s.nom_inst, s.nom_sede, td.Abreviatura,np.num_doc_titular , np.tipo_complem, np.semana, np.d1, np.d2, np.d3, np.d4, np.d5, np.observaciones FROM novedades_focalizacion np
	LEFT JOIN sedes$periodoActual s ON np.cod_sede = s.cod_sede
	LEFT JOIN tipodocumento td ON np.tipo_doc_titular = td.id
LEFT JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede
order by np.id desc

	";


	//var_dump($consultaNovedad);
  $resultadoNovedades = $Link->query($consultaNovedad);


  if($resultadoNovedades->num_rows > 0){
    while($registrosSedes = $resultadoNovedades->fetch_assoc()) {
			$semana = $registrosSedes['semana'];
			$numDoc = $registrosSedes['num_doc_titular'];
			$consulta2 = "  SELECT CONCAT(nom1, ' ', nom2, ' ', ape1, ' ', ape2) AS nombre FROM focalizacion$semana WHERE num_doc = '$numDoc' ";
			$resultado2 = $Link->query($consulta2);
			if($resultado2->num_rows > 0){
				$row2 = $resultado2->fetch_assoc();
				$registrosSedes['nombre'] = $row2['nombre'];
			}
			// $aux = $registrosSedes['fecha_hora'];
			// $aux = date("d/m/Y h:i:s a", strtotime($aux));
			// $registrosSedes['fecha_hora'] = $aux;
      $data[] = $registrosSedes;
    }
  }

  $output = [
    'sEcho' => 1,
    'iTotalRecords' => count($data),
    'iTotalDisplayRecords' => count($data),
    'aaData' => $data
  ];

  echo json_encode($output);
