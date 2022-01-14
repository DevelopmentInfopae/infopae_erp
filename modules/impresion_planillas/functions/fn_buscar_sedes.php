<option value="0">Seleccione...</option>
<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
$codigoInstitucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";

$consultaSedes = "SELECT cod_sede, nom_sede FROM sedes".$_SESSION['periodoActual']." WHERE cod_inst = " .$codigoInstitucion. ";";
$respuestaSedes = $Link->query($consultaSedes) or die('Error al consultar las sedes educativas' . mysqli_error($Link));
if ($respuestaSedes->num_rows > 0) {
	while ($dataSedes = $respuestaSedes->fetch_assoc()) { ?>
		<option value="<?php echo $dataSedes['cod_sede']?>"> <?php echo $dataSedes['nom_sede']; ?></option>
	<?php }
}else {
	echo "<option>No se encontraron Sedes Educativas </option>";
}

