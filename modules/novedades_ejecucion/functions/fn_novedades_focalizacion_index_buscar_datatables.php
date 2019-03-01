<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $data = [];
  $periodoActual = $_SESSION['periodoActual'];

  $consultaNovedades = "SELECT np.id, u.Ciudad as municipio, s.nom_inst, s.nom_sede, td.Abreviatura,np.num_doc_titular , np.tipo_complem, np.semana, np.d1, np.d2, np.d3, np.d4, np.d5, np.observaciones,
                          IF((SELECT CONCAT(nom1, ' ', nom2, ' ', ape1, ' ', ape2) AS nombre FROM focalizacion01 WHERE num_doc = np.num_doc_titular) IS NOT NULL,
                              (SELECT CONCAT(nom1, ' ', nom2, ' ', ape1, ' ', ape2) AS nombre FROM focalizacion01 WHERE num_doc = np.num_doc_titular),
                              (SELECT CONCAT(nom1, ' ', nom2, ' ', ape1, ' ', ape2) AS nombre FROM suplentes WHERE num_doc = np.num_doc_titular)
                          ) AS nombre
                        FROM novedades_focalizacion np
                          LEFT JOIN sedes$periodoActual s ON np.cod_sede = s.cod_sede
                          LEFT JOIN tipodocumento td ON np.tipo_doc_titular = td.id
                          LEFT JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede
                        ORDER BY np.id DESC";
  $resultadoNovedades = $Link->query($consultaNovedades) or die("Error al consultar novedades_focalizacion: ". $Link->error);
  if($resultadoNovedades->num_rows > 0){
    while($registrosSedes = $resultadoNovedades->fetch_assoc()) {
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
