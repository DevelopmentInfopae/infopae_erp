<?php 
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$tipoNovedad = (isset($_POST['tipoNovedad']) && ! empty($_POST['tipoNovedad'])) ? $Link->real_escape_string($_POST["tipoNovedad"]) : "";
$semana = (isset($_POST['semana']) && ! empty($_POST['semana'])) ? $Link->real_escape_string($_POST["semana"]) : "";
$mes = (isset($_POST['mes']) && ! empty($_POST['mes'])) ? $Link->real_escape_string($_POST["mes"]) : "";
?>

<?php if ($tipoNovedad == 1): ?>
	<th>Documento</th>
	<th>Numero</th>
	<th>Nombre titular de derecho</th>
	<th>Complemento</th>
	<th>Grado</th>
	<th>Grupo</th>
	<?php 
		$consulta_dias_semana = "SELECT IF(LENGTH(DIA) > 1, DIA, CONCAT('0', DIA)) AS dia FROM planilla_semanas WHERE semana = '$semana';";
		$respuesta_dias_semana = $Link->query($consulta_dias_semana) or die('Error al consultar dias_semana:'. $Link->error);
		if ($respuesta_dias_semana->num_rows > 0) {
			$columna = 1;
			while($registro_dias_semana = $respuesta_dias_semana->fetch_object()) {
				$dia = $registro_dias_semana->dia;
				echo '<th>
							<div>'. $dia .'</div>
							<div class="checkbox checkbox-success" style="padding-left: 7px;">
								<input type="checkbox" class="checkbox-header" name="checkbox-header_'.$columna.'" id="checkbox-header_'.$columna.'" data-columna="'. $columna .'" checked/>
								<label for="checkbox-header_'.$columna.'"</label>
							</div>
						</th>';
				$columna++;
			}
			if ($columna < 6) {
				for ($i=$columna; $i < 6 ; $i++) {
					echo '<th></th>';
				}
			}
		}
	?>
<?php endif ?>

<?php if ($tipoNovedad == 0): ?>
	<th>Ciudad</th>
	<th>Codigo Institución</th>
	<th>Nombre Institución</th>
	<th>Codigo Sede</th>
	<th>Nombre Sede</th>
	<th>Complemento</th>
	<?php 
		$consulta_dias_semana = "SELECT IF(LENGTH(DIA) > 1, DIA, CONCAT('0', DIA)) AS dia FROM planilla_semanas WHERE semana = '$semana';";
		$respuesta_dias_semana = $Link->query($consulta_dias_semana) or die('Error al consultar dias_semana:'. $Link->error);
		if ($respuesta_dias_semana->num_rows > 0) {
			$columna = 1;
			while($registro_dias_semana = $respuesta_dias_semana->fetch_object()) {
				$dia = $registro_dias_semana->dia;
				echo '<th>
							<div>'. $dia .'</div>
							<div class="checkbox checkbox-success" style="padding-left: 7px;">
								<input type="checkbox" class="checkbox-header" name="checkbox-header_'.$columna.'" id="checkbox-header_'.$columna.'" data-columna="'. $columna .'" checked/>
								<label for="checkbox-header_'.$columna.'"</label>
							</div>
						</th>';
				$columna++;
			}
			if ($columna < 6) {
				for ($i=$columna; $i < 6 ; $i++) {
					echo '<th></th>';
				}
			}
		}
	?>
<?php endif ?>

