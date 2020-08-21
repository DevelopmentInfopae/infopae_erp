<?php
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';

  $periodo_actual = $_SESSION['periodoActual'];

  $data = [];

  $mes = (isset($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : "";
  $semana = (isset($_POST['semana'])) ? $Link->real_escape_string($_POST['semana']) : "";
  $municipio = (isset($_POST['municipio'])) ? $Link->real_escape_string($_POST['municipio']) : "";
  $institucion = (isset($_POST['institucion'])) ? $Link->real_escape_string($_POST['institucion']) : "";
  $sede = (isset($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : "";
  $tipo_complemento = (isset($_POST['tipo_complemento'])) ? $Link->real_escape_string($_POST['tipo_complemento']) : "";
  $tipo_alimento = (isset($_POST['tipo_alimento'])) ? $Link->real_escape_string($_POST['tipo_alimento']) : "";
  $proveedor = (isset($_POST['proveedor'])) ? $Link->real_escape_string($_POST['proveedor']) : "";
  $producto = (isset($_POST['producto'])) ? $Link->real_escape_string($_POST['producto']) : "";
  $ruta = (isset($_POST['ruta'])) ? $Link->real_escape_string($_POST['ruta']) : "";
  $por_totales = (isset($_POST['por_totales'])) ? $Link->real_escape_string($_POST['por_totales']) : "";
  $join_rutas = $condicion_ruta = $condicion_municipio = $condicion_institucion = $condicion_sede = $condicion_semana = $condicion_tipo_complemento = $condicion_tipo_alimento = $condicion_proveedor = $condicion_producto = $seleccion_ubicacion = "";

  if (!empty($ruta)) {
    $join_rutas = "INNER JOIN rutasedes rs ON rs.cod_Sede = oce.cod_Sede";
    $condicion_ruta = "AND rs.IDRUTA = '$ruta'";
  } else {
    if (!empty($municipio)) {
      $condicion_municipio = "AND s.cod_mun_sede = '$municipio'";
    }

    if (!empty($institucion)) {
      $condicion_institucion = "AND s.cod_inst = '$institucion'";
    }

    if (!empty($sede)) {
      $condicion_sede = "AND s.cod_sede = '$sede'";
    }
  }

  if (!empty($semana)) {
    $condicion_semana = "AND Semana = '$semana'";
  }

  if (!empty($tipo_alimento)) {
    $condicion_tipo_alimento = "AND p.TipoDespacho = '$tipo_alimento'";
  }

  if (!empty($proveedor)) {
    $condicion_proveedor = "AND oce.proveedor = '$proveedor'";
  }

  if (!empty($producto)) {
    $condicion_producto = "AND p.Id = '$producto'";
  }

  if (!empty($por_totales)) {
    $agrupar = "ocd.cod_Alimento";
  } else {
    $agrupar = "s.cod_mun_sede, s.cod_inst, s.nom_sede";
  }

  $consulta = "SELECT
                  oce.Tipo_Doc AS tipo_documento,
                  oce.Num_Doc AS numero_documento,
                  oce.FechaHora_Elab AS fecha,
                  oce.proveedor AS proveedor,
                  pv.Nombrecomercial AS nombre_proveedor,
                  p.Descripcion AS nombre_producto,
                  p.NombreUnidad2 AS unidad_medida_producto,
                  SUM(ocd.Cantidad) AS cantidad_producto,
                  u.Ciudad AS municipio,
                  s.nom_sede AS sede
              FROM
                  orden_compra_enc$mes$periodo_actual oce
                    INNER JOIN
                  orden_compra_det$mes$periodo_actual ocd ON ocd.Num_Doc = oce.Num_Doc
                    INNER JOIN
                  productos20 p ON p.Codigo = ocd.cod_Alimento
                    INNER JOIN
                  proveedores pv ON pv.Nitcc = oce.proveedor
                    INNER JOIN
                  sedes20 s ON s.cod_sede = oce.cod_Sede
                    INNER JOIN
                  ubicacion u ON u.CodigoDANE = s.cod_mun_sede
                    $join_rutas
              WHERE
                  oce.Tipo_Complem = '$tipo_complemento'
                  $condicion_semana
                  $condicion_municipio
                  $condicion_institucion
                  $condicion_sede
                  $condicion_tipo_complemento
                  $condicion_tipo_alimento
                  $condicion_proveedor
                  $condicion_producto
                  $condicion_ruta
              GROUP BY
                  s.cod_mun_sede, s.cod_inst, s.nom_sede, ocd.cod_Alimento;";
  // echo $consulta;
  // exit();

  $respuesta = $Link->query($consulta) or die('Error realizar la consulta: '. $Link->error);
  if ($respuesta->num_rows > 0) {
  	while ($datos = $respuesta->fetch_assoc()) {
      if ($datos['unidad_medida_producto'] == 'u') {
        $datos['cantidad_producto'] = number_format((float) $datos['cantidad_producto'], 0, ',', '.');
      } else {
        $datos['cantidad_producto'] = number_format((float) $datos['cantidad_producto'], 2, ',', '.');
      }

  		$data[] = $datos;
  	}
  }

   $output = [
      'sEcho' => 1,
      'iTotalRecords' => count($data),
      'iTotalDisplayRecords' => count($data),
      'aaData' => $data
    ];

  echo json_encode($output);