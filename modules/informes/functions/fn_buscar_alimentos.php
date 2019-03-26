<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$periodo_actual = $_SESSION["periodoActual"];
$mes = (isset($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : "";
$sede = (isset($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : "";
// $ruta = (isset($_POST['ruta'])) ? $Link->real_escape_string($_POST['ruta']) : "";
$municipio = (isset($_POST['municipio'])) ? $Link->real_escape_string($_POST['municipio']) : "";
$institucion = (isset($_POST['institucion'])) ? $Link->real_escape_string($_POST['institucion']) : "";
$semana_final = (isset($_POST['semana_final'])) ? $Link->real_escape_string($_POST['semana_final']) : "";
$semana_inicial = (isset($_POST['semana_inicial'])) ? $Link->real_escape_string($_POST['semana_inicial']) : "";
$tipo_complemento = (isset($_POST['tipo_complemento'])) ? $Link->real_escape_string($_POST['tipo_complemento']) : "";

$condicion_tipo_complemento = ($tipo_complemento != "") ? "AND d.Tipo_Complem = '$tipo_complemento'" : "";
$condicion_municipio = ($municipio != "") ? "AND sed.cod_mun_sede = '$municipio'" : "";
$condicion_institucion = ($institucion != "") ? "AND sed.cod_inst = '168013000016'" : "";
$condicion_sede = ($sede != "") ? "AND sed.cod_sede = '268013000053'" : "";
$consulta = "SELECT
  pm.codigoproducto AS codigo_producto,
  pm.descripcion AS descripcion,
  SUM(pm.CantUmedida) AS cantidad_requerida,
  SUM(pm.cantotalpresentacion) AS cantidad_presentacion,
  p.nombreunidad1 AS nombre_unidad_1,
  SUM(pm.cantU2) AS cantidad_unidad_2,
  p.nombreunidad2 AS nombre_unidad_2,
  SUM(pm.cantU3) AS cantidad_unidad_3,
  p.nombreunidad3 AS nombre_unidad_3,
  SUM(pm.cantU4) AS cantidad_unidad_4,
  p.nombreunidad4 AS nombre_unidad_4,
  SUM(pm.cantU5) AS cantidad_unidad_5,
  p.nombreunidad5 AS nombre_cantidad_5
FROM productosmovdet$mes$periodo_actual pm
STRAIGHT_JOIN productosmov$mes$periodo_actual prm ON pm.Numero = prm.Numero
INNER JOIN productos$mes p ON (pm.codigoProducto=p.codigo)
INNER JOIN sedes19 sed ON sed.cod_sede = pm.BodegaDestino
WHERE pm.numero IN (SELECT num_doc FROM despachos_enc$mes$periodo_actual d WHERE d.estado<> 0 AND semana BETWEEN '$semana_inicial' AND '$semana_final' $condicion_tipo_complemento)
GROUP BY pm.codigoproducto";
// AND sed.cod_mun_sede = '68013'
// AND sed.cod_inst = '168013000016'
// AND sed.cod_sede = '268013000053'

$data = [];

$respuesta = $Link->query($consulta);
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