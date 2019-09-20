<option value="">Seleccione</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';

$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? $Link->real_escape_string($_POST["institucion"]) : "";

$consulta_sedes = "SELECT * FROM sedes". $_SESSION["periodoActual"] ." WHERE cod_inst = '$institucion' ORDER BY nom_sede";
$respuesta_sedes = $Link->query($consulta_sedes) or die ("Error al consultar sedes". $_SESSION["periodoActual"] .": ". $Link->error);
if($respuesta_sedes->num_rows > 0){
  while($sede = $respuesta_sedes->fetch_assoc()) {
?>
<option value="<?= $sede['cod_sede']; ?>"><?= $sede['nom_sede']; ?></option>
<?php
  }
}
