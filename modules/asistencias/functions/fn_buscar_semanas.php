<option value=""> Seleccione una </option>
<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$mes = '';
if(isset($_POST['mes']) && $_POST['mes'] != ''){
		$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
// $opciones = "<option value='' selected >Seleccione una</option>";
// // $opciones = '';
$consulta = " select distinct(SEMANA) as semana from planilla_semanas where mes = \"$mes\" order by semana asc ";
// echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar las semanas. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){ ?>
		<option value="<?= $row['semana'] ?>"> SEMANA <?= $row['semana'] ?> </option>
<?php }
}

