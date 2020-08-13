<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $data = [];
  $periodoActual = $_SESSION['periodoActual'];
  // $municipio   = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";
  // $institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";

  $consultaNovedad = " SELECT
                        np.id,
                        s.nom_inst,
                        s.nom_sede,
                        np.fecha_hora,
                        np.APS,
                        np.CAJMRI,
                        np.CAJMPS,
                        np.Semana,
                        np.observaciones
                      FROM
                        novedades_priorizacion np LEFT JOIN sedes$periodoActual s ON np.cod_sede = s.cod_sede
                      ORDER BY np.id DESC";
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
