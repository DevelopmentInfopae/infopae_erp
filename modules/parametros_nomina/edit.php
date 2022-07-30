<?php
include '../../config.php';
require_once '../../db/conexion.php';

$horaParametroNomina = (isset($_GET['horParametroNomina']) && $_GET['horParametroNomina'] != '') ? mysqli_real_escape_string($Link, $_GET['horParametroNomina']) : '';

$sentenciaBuscar = "SELECT * FROM parametros_nomina WHERE hora_mes = '$horaParametroNomina';";
$respuestaSentencia = $Link->query($sentenciaBuscar) or die('Error al consultar el parámetro nómina'. mysqli_error($Link));

if($respuestaSentencia->num_rows > 0)
  {
    $dataParametrosNomina = $respuestaSentencia->fetch_assoc();
    $horaMes = $dataParametrosNomina['hora_mes'];
    $salarioMinimo = $dataParametrosNomina['salario_minimo'];
    $auxilioTransporte = $dataParametrosNomina['auxilio_trans'];
    $descuentoEps = $dataParametrosNomina['desc_eps'];
    $descuentoAfp = $dataParametrosNomina['desc_afp'];
    $entidadArl = $dataParametrosNomina['arl_entidad'];
    $entidadCaja = $dataParametrosNomina['caja_entidad'];
    $porcentajeCaja = $dataParametrosNomina['caja_porc'];
    $porcentajeIcbf = $dataParametrosNomina['icbf_porc'];
    $porcentajeSena = $dataParametrosNomina['sena_porc'];
    $retefuenteServicios = $dataParametrosNomina['retefuente_servicios'];
    $retefuenteHonorarios = $dataParametrosNomina['retefuente_honorarios'];
    $reteica = $dataParametrosNomina['reteica'];
  }
?>

<style type="text/css">
    .select2-container--open {
      z-index: 9999999
  }
</style>

<div class="modal fade in" id="modal_actualizar_parametros_nomina" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formActualizarParametrosNomina">
			<div class="modal-content">
				<div class="modal-header">
          			<h4 class="modal-title">Edición Parámetros Nómina</h4>
        		</div>
        		<div class="modal-body">
        			<div class="row">
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="horasMes">Horas Mes</label>
        						<input class="form-control" type="number" name="horasMes" id="horasMes" min="1" step="1" value="<?= $horaMes?>" required readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="salarioMinimo">Salario Mínimo</label>
        						<input class="form-control" type="number" name="salarioMinimo" id="salarioMinimo" min="1" value="<?= $salarioMinimo?>" required>
        					</div><!--  form-group -->
        				</div><!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="auxilioTransporte">Auxilio Transporte</label>
        						<input class="form-control" type="number" name="auxilioTransporte" id="auxilioTransporte" min="0" value="<?= $auxilioTransporte?>" required>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        			</div> <!-- row -->
        			<div class="row">
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="descuentoEps">Descuento EPS</label>
        						<input class="form-control" type="number" name="descuentoEps" id="descuentoEps" value="<?= $descuentoEps?>" readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="descuentoAfp">Descuento AFP</label>
        						<input class="form-control" type="number" name="descuentoAfp" id="descuentoAfp" value="<?= $descuentoAfp?>" readonly>
        					</div><!-- form-group -->
        				</div><!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="entidadArl">Entidad ARL</label>
        						<select class="form-control select2" style="width: 100%;" name="entidadArl" id="entidadArl" required>
        						<?php
	                  				$consultaEntidadArl = "SELECT ID, Entidad FROM nomina_entidad WHERE Tipo = '3'";
                      				$respuestaConsultaEntidadArl = $Link->query($consultaEntidadArl) or die('Error al consultar entidades: '. mysqli_error($Link));
                      				if($respuestaConsultaEntidadArl->num_rows > 0) {
                        				while($dataEntidadArl = $respuestaConsultaEntidadArl->fetch_assoc()) { 
	                  			?>
	                  			<option value="<?= $dataEntidadArl["ID"]; ?>" <?= (isset($entidadArl) && $entidadArl == $dataEntidadArl["ID"]) ? "selected" : ""; ?>><?= $dataEntidadArl['Entidad']; ?></option>
	                  			<?php
					                    	}
					                }
					            ?>
					        	</select>
        					</div> <!-- form-group -->
        				</div><!-- col-sm-4 -->
        			</div><!-- row -->
        			<div class="row">
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="entidadCaja">Entidad CAJA</label>
        						<select class="form-control select2" style="width: 100%;" name="entidadCaja" id="entidadCaja" required>
        						<?php
	                  				$consultaEntidadCaja = "SELECT ID, Entidad FROM nomina_entidad WHERE Tipo = '4'";
                      				$respuestaConsultaEntidadCaja = $Link->query($consultaEntidadCaja) or die('Error al consultar entidades: '. mysqli_error($Link));
                      				if($respuestaConsultaEntidadCaja->num_rows > 0) {
                        				while($dataEntidadCaja = $respuestaConsultaEntidadCaja->fetch_assoc()) { 
	                  			?>
	                  			<option value="<?= $dataEntidadCaja["ID"]; ?>" <?= (isset($entidadCaja) && $entidadCaja == $dataEntidadCaja["ID"]) ? "selected" : ""; ?>><?= $dataEntidadCaja['Entidad']; ?></option>
	                  			<?php
					                    	}
					                }
					            ?>
					            </select>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="porcentajeCaja">Porcentaje CAJA</label>
        						<input class="form-control" type="number" name="porcentajeCaja" id="porcentajeCaja" value="<?= $porcentajeCaja?>" readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="porcentajeIcbf">Porcentaje ICBF</label>
        						<input class="form-control" type="number" name="porcentajeIcbf" id="porcentajeIcbf" value="<?= $porcentajeIcbf?>" readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        			</div> <!-- row -->
        			<div class="row">
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="porcentajeSena">Porcentaje SENA</label>
        						<input class="form-control" type="number" name="porcentajeSena" id="porcentajeSena" value="<?= $porcentajeSena?>" readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="retefuenteServicios">Retefuente Servicios</label>
        						<input class="form-control" type="number" name="retefuenteServicios" id="retefuenteServicios" value="<?= $retefuenteServicios?>" readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="retefuenteHonorarios">Retefuente Honorarios</label>
        						<input class="form-control" type="number" name="retefuenteHonorarios" id="retefuenteHonorarios" value="<?= $retefuenteHonorarios?>" readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        			</div> <!-- row -->
        			<div class="row">
        				<div class="col-sm-4">
        					<div class="form-group">
        						<label for="reteica">Reteica</label>
        						<input class="form-control" type="number" name="reteica" id="reteica" value="<?= $reteica?>" readonly>
        					</div> <!-- form-group -->
        				</div> <!-- col-sm-4 -->
        			</div> <!-- row -->
        		</div> <!-- modal-body -->
        		<div class="modal-footer">
		        	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="editar_parametros_nomina"><i class="fa fa-check"></i> Guardar</button>
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
		$('#modal_actualizar_parametros_nomina').modal('show');
        $('.select2').select2();
	});

</script>

<?php mysqli_close($Link); ?>