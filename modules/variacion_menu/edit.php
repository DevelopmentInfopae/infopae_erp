<?php
include '../../config.php';
require_once '../../db/conexion.php';

$idvariacion = (isset($_GET['id']) && $_GET['id'] != '') ? mysqli_real_escape_string($Link, $_GET['id']) : '';

$sentenciaBuscar = "SELECT * FROM variacion_menu WHERE id = '$idvariacion';";
$respuestaSentencia = $Link->query($sentenciaBuscar) or die('Error al consultar el tipo de caso fqrs'. mysqli_error($Link));

if ($respuestaSentencia->num_rows > 0) {
	$dataRespuesta = $respuestaSentencia->fetch_assoc();
	$id = $dataRespuesta['id'];
	$descripcion = $dataRespuesta['descripcion'];
}
?>

<div class="modal fade in" id="modal_editar_variacion_menu" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formActualizarVariacionMenu">
			<div class="modal-content">
				<div class="modal-header">
		        	<h4 class="modal-title">Edición Variación Menú</h4>
		      	</div>
		      	<div class="modal-body">
		      		<div class="row">
		      			<div class="col-sm-9">
		      				<div class="form-group">
		      					<label for="descripcion">Descripción *</label>
		      					<input class="form-control" type="text" name="descripcion" id="descripcion" required value="<?= $descripcion; ?>">
		      				</div> <!-- form-group -->
		      			</div> <!-- col-sm-9 -->
		      		</div> <!-- row -->
		      	</div> <!-- modal-body -->
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="actualizar_variacion_menu"><i class="fa fa-check"></i> Guardar</button>
		      	</div> <!-- modal-footer -->
			</div> <!-- modal-content -->
			<input type="hidden" name="id" id="id" value="<?= $id; ?>">
		</form> <!-- form -->
	</div> <!-- modal-dialog -->
</div> <!-- modal fade in -->

<script type="text/javascript">
    jQuery.extend(jQuery.validator.messages, {step: "Por favor ingresa un número entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>

<script type="text/javascript">
	$(document).ready(function() {	
		$('#modal_editar_variacion_menu').modal('show');
	});

</script>

<?php mysqli_close($Link); ?>
