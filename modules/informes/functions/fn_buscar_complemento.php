<option value="">Seleccione</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';

$periodo_actual = $_SESSION["periodoActual"];
$mes = (isset($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : "";
$sede = (isset($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : "";
$ruta = (isset($_POST['ruta'])) ? $Link->real_escape_string($_POST['ruta']) : "";
$municipio = (isset($_POST['municipio'])) ? $Link->real_escape_string($_POST['municipio']) : "";
$institucion = (isset($_POST['institucion'])) ? $Link->real_escape_string($_POST['institucion']) : "";
$semana_final = (isset($_POST['semana_final'])) ? $Link->real_escape_string($_POST['semana_final']) : "";
$semana_inicial = (isset($_POST['semana_inicial'])) ? $Link->real_escape_string($_POST['semana_inicial']) : "";

if (!empty($ruta)) {
	$consulta_tipo_complemento = "SELECT
								    DISTINCT de.Tipo_Complem AS tipo_complemento
								FROM
									despachos_enc$mes$periodo_actual de
								    INNER JOIN sedes$periodo_actual s ON s.cod_sede = de.cod_Sede
								    INNER JOIN rutasedes rs ON rs.cod_Sede = s.cod_sede
								    INNER JOIN rutas r ON r.ID = rs.IDRUTA
								WHERE
									r.ID = '$ruta';";
} else {
	$consulta_tipo_complemento = "SELECT DISTINCT dee.Tipo_Complem AS tipo_complemento
								FROM
									despachos_enc$mes$periodo_actual dee
								INNER JOIN sedes$periodo_actual sed ON sed.cod_sede = dee.cod_Sede
								WHERE 1 = 1";

	$consulta_tipo_complemento .= ($semana_inicial != "" && $semana_final != "") ? " AND Semana BETWEEN '$semana_inicial' AND '$semana_final'" : "";
	$consulta_tipo_complemento .= ($semana_inicial != "" && $semana_final == "") ? " AND Semana >= '$semana_inicial'" : "";
	$consulta_tipo_complemento .= ($municipio != "") ? " AND sed.cod_mun_sede = '$municipio'" : "";
	$consulta_tipo_complemento .= ($institucion != "") ? " AND sed.cod_inst = '$institucion'" : "";
	$consulta_tipo_complemento .= ($sede != "") ? " AND dee.cod_Sede = '$sede'" : "";
}

$respuesta_tipo_complemento = $Link->query($consulta_tipo_complemento) or die ('Unable to execute query. '. mysqli_error($Link));
if($respuesta_tipo_complemento->num_rows > 0){
  while($tipo_complemento = $respuesta_tipo_complemento->fetch_assoc()) {
?>
<option value="<?= $tipo_complemento['tipo_complemento']; ?>"><?= $tipo_complemento['tipo_complemento']; ?></option>
<?php
  }
}
