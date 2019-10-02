<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $departamento = (isset($_POST['departamento']) && $_POST['departamento'] != '') ? mysqli_real_escape_string($Link, $_POST['departamento']) : '';
  $departamento = (strlen($departamento) == 1) ? '0'. $departamento : $departamento;

  $consulta = "SELECT ubi.CodigoDANE AS codigoMunicipio, ubi.Ciudad AS nombreMunicipio FROM ubicacion ubi WHERE ubi.CodigoDANE LIKE '$departamento%';";
  $resultado = $Link->query($consulta) or die ('Error al consultar ubicaciones: '. mysqli_error($Link));
?>
<option value="">Seleccione uno</option>
<?php
  if ($resultado->num_rows > 0)
  {
  	while ($registros = $resultado->fetch_assoc())
  	{
?>
<option value="<?php echo $registros['codigoMunicipio']; ?>"><?php echo $registros['nombreMunicipio']; ?></option>
<?php
  	}
  }