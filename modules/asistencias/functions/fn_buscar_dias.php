<option value="">Seleccione uno</option>
<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$mes = '';
$semana = '';
$dia = '';
if(isset($_POST['mes']) && $_POST['mes'] != ''){
		$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
if(isset($_POST['semana']) && $_POST['semana'] != ''){
		$semana = mysqli_real_escape_string($Link, $_POST['semana']);
}
if(isset($_POST['dia']) && $_POST['dia'] != ''){
	$dia = mysqli_real_escape_string($Link, $_POST['dia']);
}

$consulta = " SELECT DISTINCT(dia) AS dia FROM planilla_semanas WHERE mes = \"$mes\" AND semana = \"$semana\" ORDER BY dia+0 ASC ";
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = 1;
	while($row = $resultado->fetch_assoc()){ ?>			
		<option value="<?= $row['dia'] ?>" <?= ($dia == $row['dia']) ? 'selected' : '' ?> > <?= $row['dia'] ?></option>
	<?php  }
}