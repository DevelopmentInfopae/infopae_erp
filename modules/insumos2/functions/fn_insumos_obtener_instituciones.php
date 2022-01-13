<option value="">Seleccione...</option>
<?php 
require_once '../../../db/conexion.php';

$municipio = (isset($_POST['municipio']) && $_POST['municipio'] != "") ? $_POST['municipio'] : "";
$institucionPost = (isset($_POST['institucion']) && $_POST['institucion'] != "") ? $_POST['institucion'] : "";
// exit(var_dump($_POST));
$consultaInstitucion = " SELECT codigo_inst, nom_inst FROM instituciones WHERE cod_mun = $municipio ";
$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar las instituciones ' . mysqli_error($Link));
if ($respuestaInstitucion->num_rows > 0) {
	while ($dataInstitucion = $respuestaInstitucion->fetch_assoc()) {
		$instituciones[$dataInstitucion['codigo_inst']] = $dataInstitucion['nom_inst'];
	}
}
foreach ($instituciones as $codigo => $nombre) { ?>
	<option value="<?= $codigo; ?>" <?= (isset($institucionPost) && $institucionPost == $codigo) ? "selected" : "" ?> ><?= $nombre; ?></option>
<?php } ?>


