<option value="">Seleccione</option>
<?php 
include '../../../config.php';
require_once '../../../db/conexion.php';

$institucion = (isset($_POST["institucion"]) && $_POST["institucion"]) ? $Link->real_escape_string($_POST["institucion"]) : "";

$consulta = " SELECT cod_sede, nom_sede FROM sedes" .$_SESSION["periodoActual"]. " WHERE cod_inst = '$institucion' ";
$respuesta = $Link->query($consulta) or die("Error al consultar las sedes: ". $Link->error);

if ($respuesta->num_rows > 0) {
	while ($sedes = $respuesta->fetch_assoc()) {
?>
	<option value="<?= $sedes["cod_sede"] ?>"><?= $sedes['nom_sede'] ?> </option>
<?php
	}
}
