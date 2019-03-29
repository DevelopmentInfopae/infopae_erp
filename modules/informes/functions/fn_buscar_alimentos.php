<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$data = [];
$periodo_actual = $_SESSION["periodoActual"];
$mes = (isset($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : "";
$sede = (isset($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : "";
// $ruta = (isset($_POST['ruta'])) ? $Link->real_escape_string($_POST['ruta']) : "";
$municipio = (isset($_POST['municipio'])) ? $Link->real_escape_string($_POST['municipio']) : "";
$institucion = (isset($_POST['institucion'])) ? $Link->real_escape_string($_POST['institucion']) : "";
$semana_final = (isset($_POST['semana_final'])) ? $Link->real_escape_string($_POST['semana_final']) : "";
$semana_inicial = (isset($_POST['semana_inicial'])) ? $Link->real_escape_string($_POST['semana_inicial']) : "";
$tipo_complemento = (isset($_POST['tipo_complemento'])) ? $Link->real_escape_string($_POST['tipo_complemento']) : "";

$condicion_sede = ($sede != "") ? "AND sed.cod_sede = '268013000053'" : "";
$condicion_municipio = ($municipio != "") ? "AND sed.cod_mun_sede = '$municipio'" : "";
$condicion_institucion = ($institucion != "") ? "AND sed.cod_inst = '168013000016'" : "";
$condicion_tipo_complemento = ($tipo_complemento != "") ? "AND d.Tipo_Complem = '$tipo_complemento'" : "";

$consulta = "SELECT
  pm.CodigoProducto AS codigo_producto,
  pm.Descripcion AS descripcion,
  SUM(pm.CantUmedida) AS cantidad_requerida,
  CAST(SUM(pm.CanTotalPresentacion) AS DECIMAL(12,2)) AS cantidad_presentacion,
  CAST(SUM(pm.CantU1) AS DECIMAL(12,2)) AS cantidad_unidad_1,
  p.NombreUnidad1 AS nombre_unidad_1,
  CAST(SUM(pm.CantU2) AS DECIMAL(12,2)) AS cantidad_unidad_2,
  p.NombreUnidad2 AS nombre_unidad_2,
  CAST(SUM(pm.CantU3) AS DECIMAL(12,2)) AS cantidad_unidad_3,
  p.NombreUnidad3 AS nombre_unidad_3,
  CAST(SUM(pm.CantU4) AS DECIMAL(12,2)) AS cantidad_unidad_4,
  p.NombreUnidad4 AS nombre_unidad_4
FROM productosmovdet$mes$periodo_actual pm
STRAIGHT_JOIN productosmov$mes$periodo_actual prm ON pm.Numero = prm.Numero
INNER JOIN productos$periodo_actual p ON pm.CodigoProducto = p.codigo
INNER JOIN sedes19 sed ON sed.cod_sede = pm.BodegaDestino
WHERE pm.Numero IN (SELECT num_doc FROM despachos_enc$mes$periodo_actual d WHERE d.estado<> 0 AND semana BETWEEN '$semana_inicial' AND '$semana_final' $condicion_tipo_complemento)
GROUP BY pm.CodigoProducto";

$respuesta = $Link->query($consulta) or die("Error al consultar alimentos: ". $Link->error);
if ($respuesta->num_rows > 0) {
	while ($datos = $respuesta->fetch_assoc()) {
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