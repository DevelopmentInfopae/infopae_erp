<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $data = [];
  $periodo_actual = $_SESSION['periodoActual'];
  $mes = (isset($_POST['mes']) && ! empty($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : '';
  $sede = (isset($_POST['sede']) && ! empty($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : '';
  $semana = (isset($_POST['semana']) && ! empty($_POST['semana'])) ? $Link->real_escape_string($_POST['semana']) : '';
  $institucion = (isset($_POST['institucion']) && ! empty($_POST['institucion'])) ? $Link->real_escape_string($_POST['institucion']) : '';
  $tipo_complemento = (isset($_POST['tipo_complemento']) && !empty($_POST['tipo_complemento'])) ? $Link->real_escape_string($_POST['tipo_complemento']) : '';

  $parametro_sede = (! empty($sede)) ? " AND nf.cod_sede = '$sede'" : "";
  $consulta_novedades = "SELECT nf.id, u.Ciudad as municipio, s.nom_inst, s.nom_sede, td.Abreviatura, nf.num_doc_titular, nf.tipo_complem, nf.semana, nf.d1, nf.d2, nf.d3, nf.d4, nf.d5, nf.observaciones,
                          (SELECT CONCAT(nom1, ' ', nom2, ' ', ape1, ' ', ape2) FROM entregas_res_$mes$periodo_actual WHERE num_doc = nf.num_doc_titular AND tipo = 'F') AS nombre
                        FROM novedades_focalizacion nf
                          LEFT JOIN sedes$periodo_actual s ON nf.cod_sede = s.cod_sede
                          LEFT JOIN tipodocumento td ON nf.tipo_doc_titular = td.id
                          LEFT JOIN ubicacion u ON u.CodigoDANE = s.cod_mun_sede
                        WHERE
                          nf.semana = '$semana'
                          AND tipo_complem = '$tipo_complemento'
                          AND s.cod_inst = '$institucion'
                          $parametro_sede
                        ORDER BY nf.id DESC";

  $resultado_novedades = $Link->query($consulta_novedades) or die("Error al consultar novedades_focalizacion: ". $Link->error);
  if($resultado_novedades->num_rows > 0)
  {
    while($registros_sedes = $resultado_novedades->fetch_assoc())
    {
      if (isset($registros_sedes['d1']))
      {
        $registros_sedes['d1'] = ($registros_sedes['d1'] == 1) ? '<i class="fa fa-check text-navy"></i>' : '';
      }

      if (isset($registros_sedes['d2']))
      {
        $registros_sedes['d2'] = ($registros_sedes['d2'] == 1) ? '<i class="fa fa-check text-navy"></i>' : '';
      }

      if (isset($registros_sedes['d3']))
      {
        $registros_sedes['d3'] = ($registros_sedes['d3'] == 1) ? '<i class="fa fa-check text-navy"></i>' : '';
      }

      if (isset($registros_sedes['d4']))
      {
        $registros_sedes['d4'] = ($registros_sedes['d4'] == 1) ? '<i class="fa fa-check text-navy"></i>' : '';
      }

      if (isset($registros_sedes['d5']))
      {
        $registros_sedes['d5'] = ($registros_sedes['d5'] == 1) ? '<i class="fa fa-check text-navy"></i>' : '';
      }

      $data[] = $registros_sedes;
    }
  }

  $output = [
    'sEcho' => 1,
    'iTotalRecords' => count($data),
    'iTotalDisplayRecords' => count($data),
    'aaData' => $data
  ];

  echo json_encode($output);
