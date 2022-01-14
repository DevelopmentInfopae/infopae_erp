<option value="">Seleccione...</option>
<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$institucion = $_POST['institucion'];
$periodoActual = $_SESSION['periodoActual'];

$consultaSedes = " SELECT cod_sede, nom_sede FROM sedes$periodoActual WHERE cod_inst = $institucion ";
$respuestaSedes = $Link->query($consultaSedes) or die ('Error al consultar las sedes ' . mysqli_error($Link));
if ($respuestaSedes->num_rows > 0) {
 	while ($dataSedes = $respuestaSedes->fetch_assoc()) {
 		$sedes[$dataSedes['cod_sede']] = $dataSedes['nom_sede'];
 	}
}

foreach ($sedes as $codigo => $nombre) { ?>
 	<option value="<?= $codigo; ?>"><?= $nombre; ?></option>
<?php } ?>  