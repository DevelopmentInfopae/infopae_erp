<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $data = [];
  $periodoActual = $_SESSION['periodoActual'];
  $institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != "") ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
  $municipio   = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? ($_POST["municipio"] == "0") ? "" : mysqli_real_escape_string($Link, $_POST["municipio"]) : "";


  $consultaSedes = "SELECT
                      sed.cod_sede AS codigoSede,
                      sed.nom_sede AS nombreSede,
                      sed.cod_inst AS codigoInstitucion,
                      sed.nom_inst AS nombreInstitucion,
                      usu.nombre AS nombreCoordinador,
                      jor.nombre AS nombreJornada,
                      sed.tipo_validacion AS tipoValidacion,
                      sed.estado AS estadoSede
                    FROM sedes$periodoActual sed
                    LEFT JOIN usuarios usu ON usu.id = sed.id_coordinador
                    LEFT JOIN jornada jor ON jor.id = sed.jornada
                    WHERE 1=1 ";
  if($municipio  != ""){ $consultaSedes .= " AND cod_mun_sede = '" . $_POST['municipio'] . "' "; }
  if($institucion != ""){ $consultaSedes .= " AND cod_inst = '" . $_POST['institucion'] . "' "; }
  $consultaSedes .= "ORDER BY nom_sede ASC";

  $resultadoSedes = $Link->query($consultaSedes);
  if($resultadoSedes->num_rows > 0){
    while($registrosSedes = $resultadoSedes->fetch_assoc()) {
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