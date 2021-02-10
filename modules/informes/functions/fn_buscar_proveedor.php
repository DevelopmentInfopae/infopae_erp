<option value="">Seleccione</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';

$periodo_actual = $_SESSION["periodoActual"];
$tipo_alimento = (isset($_POST['tipo_alimento'])) ? $Link->real_escape_string($_POST['tipo_alimento']) : "";

$consulta_proveedores = "SELECT * FROM proveedores where find_in_set('$tipo_alimento', TipoAlimento) OR TipoAlimento = 99;";

$respuesta_proveedores = $Link->query($consulta_proveedores) or die ('Error al consultar alimentos por tipo. '. mysqli_error($Link));
if($respuesta_proveedores->num_rows > 0){
  while($proveedor = $respuesta_proveedores->fetch_object()) {
?>
	<option value="<?= $proveedor->Id; ?>"><?= $proveedor->Nombrecomercial; ?></option>
<?php
  }
}