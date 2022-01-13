<option value="">Seleccione uno</option>
<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$mes = '';
$semana = '';
if(isset($_POST['mes']) && $_POST['mes'] != ''){
		$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
if(isset($_POST['semana']) && $_POST['semana'] != ''){
		$semana = mysqli_real_escape_string($Link, $_POST['semana']);
}

$consulta = " select distinct(dia) as dia from planilla_semanas where mes = \"$mes\" and semana = \"$semana\" order by dia+0 asc ";
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = 1;
	while($row = $resultado->fetch_assoc()){ ?>			
		<option value="<?= $row['dia'] ?>"> <?= $row['dia'] ?></option>
	<?php  }
}