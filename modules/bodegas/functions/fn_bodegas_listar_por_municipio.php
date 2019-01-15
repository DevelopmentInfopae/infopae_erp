<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $municipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST['municipio']) : '';
?>
<option value="">Seleccionar todo</option>
<?php
  $consulta = "SELECT ID AS codigoBodega, NOMBRE AS nombreBodega FROM bodegas WHERE CIUDAD = '$municipio'";
  $resultado = $Link->query($consulta);
  if ($resultado->num_rows > 0)
  {
  	while($registros = $resultado->fetch_assoc())
  	{
?>
<option value="<?php echo $registros['codigoBodega']; ?>"><?php echo $registros['nombreBodega']; ?></option>
<?php
  	}
  }