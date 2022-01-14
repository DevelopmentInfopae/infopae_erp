<?php
include '../../config.php';
require_once '../../db/conexion.php';

$idTipoCaso = (isset($_GET['idTipoCaso']) && $_GET['idTipoCaso'] != '') ? mysqli_real_escape_string($Link, $_GET['idTipoCaso']) : '';

$sentenciaBuscar = "SELECT * FROM tipo_casosfqrs WHERE id = '$idTipoCaso';";
$respuestaSentencia = $Link->query($sentenciaBuscar) or die('Error al consultar el tipo de caso fqrs'. mysqli_error($Link));

if($respuestaSentencia->num_rows > 0)
  {
    $dataTipoCasoFqrs = $respuestaSentencia->fetch_assoc();
    $id = $dataTipoCasoFqrs['ID'];
    $descripcion = $dataTipoCasoFqrs['Descripcion'];
    $tipo = $dataTipoCasoFqrs['tipo'];
  }
?>

<div class="modal fade in" id="modal_editar_tipo_caso_fqrs" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formActualizarTipoCasoFqrs">
			<div class="modal-content">
				<div class="modal-header">
		        	<h4 class="modal-title">Edición Tipo Caso FQRS</h4>
		      	</div>
		      	<div class="modal-body">
		      		<div class="row">
		      			<div class="col-sm-4">
		      				<div class="form-group">
		      					<label for="tipo">Tipo *</label>
		      					<select class="form-control" name="tipo" id="tipo" required>
		      						<option value="F" <?= (isset($tipo) && $tipo == "F") ? "selected" : ""; ?>>Felicitaciones</option>
		      						<option value="Q" <?= (isset($tipo) && $tipo == "Q") ? "selected" : ""; ?>>Quejas</option>
		      						<option value="R" <?= (isset($tipo) && $tipo == "R") ? "selected" : ""; ?>>Reclamos</option>
		      						<option value="S" <?= (isset($tipo) && $tipo == "S") ? "selected" : ""; ?>>Solicitudes</option>
		      					</select>
		      				</div> <!-- form-group -->
		      			</div> <!-- col-sm-4 -->
		      			<div class="col-sm-8">
		      				<div class="form-group">
		      					<label for="descripcion">Descripción *</label>
		      					<input class="form-control" type="text" name="descripcion" id="descripcion" required value="<?= $descripcion?>">
		      				</div> <!-- form-group -->	
		      			</div> <!-- col-sm-8 -->
		      		</div><!-- row -->
		      	</div> <!-- modal-body -->
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="actualizar_tipo_caso_fqrs"><i class="fa fa-check"></i> Guardar</button>
		      	</div> <!-- modal-footer -->
			</div> <!-- modal-content -->
			<input type="hidden" name="id" id="id" value="<?= $id?>">
		</form><!-- form -->
	</div> <!-- modal-dialog -->
</div> <!-- modal fade in -->

<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<script type="text/javascript">
    jQuery.extend(jQuery.validator.messages, {step: "Por favor ingresa un número entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>

<script type="text/javascript">
	$(document).ready(function() {	
		$('#modal_editar_tipo_caso_fqrs').modal('show');
	});

</script>

<?php mysqli_close($Link); ?>

