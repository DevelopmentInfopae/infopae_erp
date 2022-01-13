<?php
	include '../../config.php'; 
	require_once '../../db/conexion.php';
	$dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
    if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }
?>
<style type="text/css">
	.select2-container--open {
      z-index: 9999999
  }
</style>

<div class="modal fade in" id="modal_crear_institucion" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formCrearInstitucion"> 
			<div class="modal-content">
				<div class="modal-header">
		        	<h4 class="modal-title">Creación Institución</h4>
		      	</div> <!-- modal-header -->
		      	<div class="modal-body">
		      		<div class="row">
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="codigo">Código *</label>
		                		<input type="number" class="form-control" name="codigo" id="codigo" required min="0" step="1">
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="nombre">Nombre Institución *</label>
		                		<input type="text" class="form-control" name="nombre" id="nombre" required>
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="telefono">Teléfono</label>
		                		<input type="tel" class="form-control" name="telefono" id="telefono">
		      				</div><!--  form-group -->
		      			</div> <!-- col -->
		      		</div> <!-- row -->
		      		<div class="row">
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="email">Email</label>
		                		<input type="email" class="form-control" name="email" id="email">
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="municipio">Municipio *</label>
		                		<select class="form-control select2" name="municipio" id="municipio" required style="width: 100%">
		                			<option value="">Seleccione uno</option>
		                			<?php
		                			$condicionMunicipios = "";
									if ($_SESSION['p_Municipio'] == 0) {
										$condicionMunicipios = "CodigoDANE like '" .$_SESSION['p_CodDepartamento']. "%'";
									}else if ($_SESSION['p_Municipio'] != 0) {
										$condicionMunicipios = "CodigoDANE = '" .$_SESSION['p_Municipio']. "'";
									}
									$consultaMunicipios =  "SELECT CodigoDANE, Ciudad FROM ubicacion WHERE " .$condicionMunicipios.";";
									$respuestaMunicipios = $Link->query($consultaMunicipios) or die ('Error al consultar los municipios ' .mysqli_error($Link));
		                    		if($respuestaMunicipios){
		                      			while($row1 = $respuestaMunicipios->fetch_assoc()){
		                  			?>
		                        	<option value="<?php echo $row1['CodigoDANE']; ?>" <?php if(isset($row['cod_mun']) && $row['cod_mun'] == $row1['CodigoDANE'] || $municipio_defecto["CodMunicipio"] == $row1['CodigoDANE']){ echo ' selected '; } ?>>
		                          	<?php echo $row1['Ciudad']; ?>
		                        	</option>
		                  			<?php
		                      			}
		                   	 		}
		                  			?>
		               			 </select>
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="rector">Rector *</label>
		                		<select class="form-control select2" name="rector" id="rector" required style="width: 100%">
		                			<option value="">Seleccione uno</option>
		                			<?php
		                    		$codigoCiudad = $_SESSION['codCiudad'];
		                    		$consulta1= " SELECT num_doc, nombre FROM usuarios WHERE id_perfil = '6' AND cod_mun LIKE '$codigoCiudad%' ORDER BY nombre ASC;";
		                    		$result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
		                    		if($result1){
		                      			while($row1 = $result1->fetch_assoc()){
		                  			?>
		                        	<option value="<?php echo $row1['num_doc']; ?>">
		                          	<?php echo $row1['nombre']; ?>
		                        	</option>
		                  			<?php
		                      			}
		                    		}
		                  			?>
		                		</select>
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      		</div> <!-- row -->
		      	</div> <!-- modal-body -->
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="guardar_institucion"><i class="fa fa-check"></i> Guardar</button>
		      	</div> <!-- modal-footer -->
			</div> <!-- modal-content -->
		</form> <!-- form -->
	</div> <!-- modal-dialog -->
</div> <!-- modal fade in -->

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#modal_crear_institucion').modal('show');
		$('.select2').select2();
	});

	$('#telefono').on('keyup', function(){
  	this.value = this.value.replace(/[^0-9 -]/g, '');
	});
</script>

<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { step:"Por favor, escribe un número entero válido.", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>

<?php mysqli_close($Link); ?>