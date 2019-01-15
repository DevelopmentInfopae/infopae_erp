<option value="">Seleccione...</option>
<?php 
  require_once '../../../config.php';
  require_once '../../../db/conexion.php';
$consulta = "SELECT *  FROM grupo_etario";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
while ($grupoEtario = $resultado->fetch_assoc()) { ?>
<option value="<?php echo $grupoEtario['ID'] ?>"><?php echo $grupoEtario['DESCRIPCION'] ?></option>
<?php }
}
?>