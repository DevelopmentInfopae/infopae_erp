<?php
include '../../config.php';
require_once '../../db/conexion.php';

$idParametrosManipuladora = (isset($_GET['idParametroManipuladora']) && $_GET['idParametroManipuladora'] != '') ? mysqli_real_escape_string($Link, $_GET['idParametroManipuladora']) : '';

$sentenciaBuscar = "SELECT ID, tipo_complem, cant_manipuladora, limite_inferior, limite_superior FROM parametros_manipuladoras WHERE ID = '$idParametrosManipuladora';";
$respuestaSentencia = $Link->query($sentenciaBuscar) or die('Error al consultar el parámetro manipuladora'. mysqli_error($Link));

if($respuestaSentencia->num_rows > 0)
  {
    $dataValoresManipuladora = $respuestaSentencia->fetch_assoc();
    $id = $dataValoresManipuladora['ID'];
    $tipoComplemento = $dataValoresManipuladora['tipo_complem'];
    $cantidad = $dataValoresManipuladora['cant_manipuladora'];
    $limiteInferior = $dataValoresManipuladora['limite_inferior'];
    $limiteSuperior = $dataValoresManipuladora['limite_superior'];
  }
?>

<div class="modal fade in" id="modal_actualizar_parametros_manipuladoras" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formActualizarParametrosManipuladora">
			<div class="modal-content">
				<div class="modal-header">
          			<h4 class="modal-title">Edición Parámetros Manipuladora</h4>
        		</div>
        		<div class="modal-body">
        			<div class="row">
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="tipoComplemento">Tipo de complemento</label>
                				<input type="text" class="form-control" name="tipoComplemento" id="tipoComplemento"  value="<?= $tipoComplemento; ?>" required readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="cantidadManipuladoras">Cantidad Manipuladoras</label>
        						<input type="number" class="form-control" name="cantidad" id="cantidad" value="<?= $cantidad; ?>" step="1" required>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="limiteInferior">Límite Inferior</label>
        						<input type="number" class="form-control" name="limiteInferior" id="limiteInferior" value="<?= $limiteInferior; ?>" step = "1" required>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        			</div> <!-- row -->
        			<div class="row">
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="limiteSuperior">Límite Superior</label>
        						<input type="number" class="form-control" name="limiteSuperior" id="limiteSuperior" value="<?= $limiteSuperior; ?>" step= "1" required>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        			</div>
        		</div> <!-- modal-body -->
        		<div class="modal-footer">
		        	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="editar_parametros_manipuladoras"><i class="fa fa-check"></i> Guardar</button>
		      	</div>
			</div> <!-- modal-content -->
            <input type="hidden" name="id" id="id" value="<?= $id; ?> ">
		</form> <!-- form -->
	</div> <!-- modal-dialog -->
</div><!--  modal fade in -->

<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script type="text/javascript">
    jQuery.extend(jQuery.validator.messages, {step: "Por favor ingresa un numero entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#modal_actualizar_parametros_manipuladoras').modal('show');
	});

</script>

<?php mysqli_close($Link); ?>
