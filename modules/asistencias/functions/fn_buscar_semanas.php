<option value=""> Seleccione una </option>
<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? $semana = $_POST['semana'] : '';
$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? $mes = $_POST['mes'] : '';

$consulta = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE mes = \"$mes\" ORDER BY semana ASC ";

$resultado = $Link->query($consulta) or die ('No se pudieron cargar las semanas. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){ ?>
		<option value="<?= $row['semana'] ?>" <?= ($row['semana'] == $semana) ? 'selected' : '' ?> > SEMANA <?= $row['semana'] ?> </option>
<?php }
}

