<option value="">Seleccione...</option>
<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$municipio = $_POST['municipio'];
$bodegaCache = (isset($_POST['bodegaCache']) && $_POST['bodegaCache'] != '') ? mysqli_real_escape_string($Link, $_POST['bodegaCache']) : '';
$condicionMunicipio = '';
if ($municipio != "") {
	$condicionMunicipio .= " WHERE CIUDAD = $municipio ";
}

$consulta = "SELECT * FROM bodegas $condicionMunicipio ORDER BY Nombre ASC ";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
 while ($bodega = $resultado->fetch_assoc()) { ?>
   <option value="<?php echo $bodega['ID'] ?>" <?= (isset($bodegaCache) && $bodega['ID'] == $bodegaCache) ? 'selected' : '' ?> ><?php echo $bodega['NOMBRE'] ?></option>
 <?php }
}
 ?>