<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $data = [];
  $periodoActual = $_SESSION['periodoActual'];
  $sede = (isset($_POST['sede']) && ! empty($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : '';
  $semana = (isset($_POST['semana']) && ! empty($_POST['semana'])) ? $Link->real_escape_string($_POST['semana']) : '';
  $codSede   = (isset($_POST['codSede']) && $_POST['codSede'] != "") ? mysqli_real_escape_string($Link, $_POST["codSede"]) : "";
  $institucion = (isset($_POST['institucion']) && ! empty($_POST['institucion'])) ? $Link->real_escape_string($_POST['institucion']) : '';

  $parametro_sede = (! empty($sede)) ? "AND nf.cod_sede = $codSede" : "";
  $consultaNovedades = "SELECT nf.id, u.Ciudad as municipio, s.nom_inst, s.nom_sede, td.Abreviatura,nf.num_doc_titular , nf.tipo_complem, nf.semana, nf.d1, nf.d2, nf.d3, nf.d4, nf.d5, nf.observaciones,
                          IF((SELECT num_doc FROM focalizacion$semana WHERE num_doc = nf.num_doc_titular) IS NOT NULL,
                              (SELECT CONCAT(nom1, ' ', nom2, ' ', ape1, ' ', ape2) AS nombre FROM focalizacion$semana WHERE num_doc = nf.num_doc_titular),
                              (SELECT CONCAT(nom1, ' ', nom2, ' ', ape1, ' ', ape2) AS nombre FROM suplentes$semana WHERE num_doc = nf.num_doc_titular)
                          ) AS nombre
                        FROM novedades_focalizacion nf
                          LEFT JOIN sedes$periodoActual s ON nf.cod_sede = s.cod_sede
                          LEFT JOIN tipodocumento td ON nf.tipo_doc_titular = td.id
                          LEFT JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede
                        WHERE 1 = 1
                          $condicion
                        ORDER BY nf.id DESC";
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
