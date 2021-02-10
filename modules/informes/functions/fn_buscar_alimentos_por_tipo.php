<option value="">Seleccione</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';

$periodo_actual = $_SESSION["periodoActual"];
$tipo_alimento = (isset($_POST['tipo_alimento'])) ? $Link->real_escape_string($_POST['tipo_alimento']) : "";

$consulta_alimentos = "SELECT * FROM productos$periodo_actual WHERE tipoDespacho = '$tipo_alimento' ORDER BY Descripcion ASC;";

$respuesta_alimentos = $Link->query($consulta_alimentos) or die ('Error al consultar alimentos por tipo. '. mysqli_error($Link));
if($respuesta_alimentos->num_rows > 0){
  while($alimento = $respuesta_alimentos->fetch_object()) {
?>
	<option value="<?= $alimento->Id; ?>"><?= $alimento->Descripcion; ?></option>
<?php
  }
}