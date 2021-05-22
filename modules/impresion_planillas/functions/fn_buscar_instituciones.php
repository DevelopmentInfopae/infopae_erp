
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
$codigoMunicipio = (isset($_POST['municipio']) && $_POST['municipio'] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : "";

$consultaInstituciones = "SELECT codigo_inst, nom_inst FROM instituciones WHERE cod_mun = " .$codigoMunicipio. ";";
$respuestaInstituciones = $Link->query($consultaInstituciones) or die('Error al consultar las instituciones' . mysqli_error($Link));
if ($respuestaInstituciones->num_rows > 0) { ?>
	<option value="0">Seleccione...</option>
	<?php 
	while ($dataInstituciones = $respuestaInstituciones->fetch_assoc()) { ?>
		<option value="<?php echo $dataInstituciones['codigo_inst']?>"> <?php echo $dataInstituciones['nom_inst']; ?></option>
	<?php }
}else {
	echo "<option>No se encontraron Instituciones </option>";
}

