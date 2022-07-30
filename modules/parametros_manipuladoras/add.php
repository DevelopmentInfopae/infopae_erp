<?php
	include '../../config.php';
	require_once '../../db/conexion.php';
?>

<div class="modal fade in" id="modal_crear_parametros_manipuladoras" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formCrearParametrosManipuladoras">
			<div class="modal-content">
				<div class="modal-header">
		        	<h4 class="modal-title">Creación Parámetros Manipuladoras</h4>
		      	</div>
		      	<div class="modal-body">
		      		<div class="row">
		      			<div class="col-sm-4">
		      				<div class="form-group">
		      					<label for="tipoComplemento">Tipo de complemento</label>
	                  			<select class="form-control" name="complemento" id="complemento" required>
	                  				<option value="">Seleccione una</option>
	                  				<?php
	                  					$consultaComplementos = "SELECT CODIGO FROM tipo_complemento";
                      					$res_complementos = $Link->query($consultaComplementos) or die('Error al consultar complementos: '. mysqli_error($Link));
                      					if($res_complementos->num_rows > 0) {
                        					while($dataComplementos = $res_complementos->fetch_assoc()) { 
	                  				?>
	                  				<option value="<?= $dataComplementos["CODIGO"]; ?>"><?= $dataComplementos["CODIGO"]; ?></option>
	                  				<?php
					                    	}
					                    }
					                ?>
					            </select>  
		      				</div> <!-- form-group -->
		      			</div> <!-- col-sm-4 -->
		      			<div class="col-sm-4">
		      				<div class="form-group">
		      					<label for="cantidadManipuladoras">Cantidad Manipuladoras</label>
		      					<input class="form-control" type="number" name="cantidad" id="cantidad" min="1" step="1" required>
		      				</div> <!-- form-group -->
		      			</div> <!-- col-sm-4 -->
		      			<div class="col-sm-4">
		      				<div class="form-group">
		      					<label for="limiteInferior">Límite Inferior</label>
		      					<input class="form-control" type="number" name="limiteInferior" id="limiteInferior" min="1" step="1" required>
		      				</div> <!-- form-group -->
		      			</div> <!-- col-sm-4 -->
		      		</div> <!-- row -->
		      		<div class="row">
		      			<div class="col-sm-4">
		      				<div class="form-group">
		      					<label for="limiteSuperior">Límite Superior</label>
		      					<input class="form-control" type="number" name="limiteSuperior" id="limiteSuperior" min="1" step="1" required>
		      				</div> <!-- form-group -->
		      			</div> <!-- col-sm-4 -->
		      		</div><!-- row -->
		      	</div> <!-- modal-body -->
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="guardar_parametros_manipuladoras"><i class="fa fa-check"></i> Guardar</button>
		      	</div>
			</div> <!-- modal-content -->
		</form> <!-- form -->
	</div> <!-- modal-dialog -->
</div> <!-- modal fade in -->

<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script type="text/javascript">
    jQuery.extend(jQuery.validator.messages, {step: "Por favor ingresa un número entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#modal_crear_parametros_manipuladoras').modal('show');
	});

</script>

<?php mysqli_close($Link); ?>