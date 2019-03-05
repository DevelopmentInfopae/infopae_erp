<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $data = [];
  $periodoActual = $_SESSION['periodoActual'];
  $codSede   = (isset($_POST['codSede']) && $_POST['codSede'] != "") ? mysqli_real_escape_string($Link, $_POST["codSede"]) : "";
  // $institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";

  $condicion = ($codSede != "") ? "AND np.cod_sede = $codSede" : "";
  $consultaNovedad = " SELECT u.Ciudad as municipio, np.id, s.nom_inst, s.nom_sede, np.fecha_hora, np.APS, np.CAJMRI, np.CAJMPS, np.Semana, np.observaciones FROM novedades_priorizacion np LEFT JOIN sedes$periodoActual s ON np.cod_sede = s.cod_sede LEFT JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede WHERE 1 = 1 $condicion ORDER BY np.id DESC ";

  $resultadoNovedades = $Link->query($consultaNovedad);
  if($resultadoNovedades->num_rows > 0){
    while($registrosSedes = $resultadoNovedades->fetch_assoc()) {
			$aux = $registrosSedes['fecha_hora'];
			$aux = date("d/m/Y h:i:s a", strtotime($aux));
			$registrosSedes['fecha_hora'] = $aux;
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
