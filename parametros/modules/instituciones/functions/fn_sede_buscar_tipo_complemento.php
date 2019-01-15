<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

if(isset($_POST["jornada"]) && $_POST["jornada"] != "" ){
  $jornada = mysqli_real_escape_string($Link, $_POST["jornada"]);
  
	$consulta = "SELECT CODIGO AS codigoTipoComplemento, ID AS idTipoComplemento, DESCRIPCION AS descripcionTipoComplemento FROM tipo_complemento WHERE jornada = '$jornada';"; 
	$result = $Link->query($consulta);
	$Link->close();
?>

	<option value="">Seleccione uno</option>
	<?php
		while($row = $result->fetch_assoc()) {  ?>
		<option value="<?php echo $row["codigoTipoComplemento"]; ?>"><?php echo $row["descripcionTipoComplemento"]; ?></option>
<?php 
		}
}
else{
?>
		<option value="">Seleccione uno</option>
<?php
}