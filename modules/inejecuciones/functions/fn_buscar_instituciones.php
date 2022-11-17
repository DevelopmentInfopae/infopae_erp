<option value="">Seleccione</option>
<?php 
include '../../../config.php';
require_once '../../../db/conexion.php';

$municipio = (isset($_POST["municipio"]) && $_POST["municipio"]) ? $Link->real_escape_string($_POST["municipio"]) : "";

$consulta = "SELECT codigo_inst AS cod_inst, nom_inst FROM instituciones WHERE cod_mun = '$municipio' ";
$respuesta = $Link->query($consulta) or die("Error al consultar las instituciones: ". $Link->error);

if ($respuesta->num_rows > 0) {
	while ($instituciones = $respuesta->fetch_assoc()) {
?>
	<option value="<?= $instituciones["cod_inst"] ?>"><?= $instituciones['nom_inst'] ?> </option>
<?php
	}
}
