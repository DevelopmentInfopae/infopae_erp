<?php
	include '../../config.php'; 
	require_once '../../db/conexion.php';
	$dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
    if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }

    $codigoInstitucion = $_GET["codigo"];
	$consulta1 = "SELECT * FROM instituciones WHERE codigo_inst = '$codigoInstitucion' LIMIT 1";
	$resultado1 = $Link->query($consulta1);
	if($resultado1){
		$registros1 = $resultado1->fetch_assoc();
	}
?>
<style type="text/css">
	.select2-container--open {
      z-index: 9999999
  }
</style>

<div class="modal fade in" id="modal_editar_institucion" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formEditarInstitucion">
			<div class="modal-content">
				<div class="modal-header">
		        	<h4 class="modal-title">Edición Institución</h4>
		      	</div> <!-- modal-header -->
		      	<div class="modal-body">
		      		<div class="row">
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="codigo">Código *</label>
		                		<input type="text" class="form-control" name="codigo" id="codigo" value="<?php echo $registros1["codigo_inst"]; ?>" disabled required>
			                	<input type="hidden" name="id" id="id" value="<?php echo $registros1["id"]; ?>">
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="nombre">Nombre Institución *</label>
		                		<input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $registros1["nom_inst"]; ?>" required>
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="telefono">Teléfono</label>
		                		<input type="tel" class="form-control" name="telefono" value="<?php echo $registros1["tel_int"]; ?>" id="telefono">
		      				</div><!--  form-group -->
		      			</div> <!-- col -->
		      		</div> <!-- row -->

		      		<div class="row">
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="email">Email</label>
		                		<input type="email" class="form-control" name="email" value="<?php echo $registros1["email_inst"]; ?>" id="email">
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
		      					<label for="municipio">Municipio *</label>
				                <select class="form-control select2" name="municipio" id="municipio" required style="width : 100%;">
				                	<option value="">Seleccione uno</option>
				                	<?php
					                $condicionMunicipios = "";
									if ($_SESSION['p_Municipio'] == 0) {
										$condicionMunicipios = "CodigoDANE like '" .$_SESSION['p_CodDepartamento']. "%'";
									}else if ($_SESSION['p_Municipio'] != 0) {
										$condicionMunicipios = "CodigoDANE = '" .$_SESSION['p_Municipio']. "'";
									}
				                    $consulta2= " SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion where $condicionMunicipios order by ciudad asc; ";
				                    echo $consulta2;
				                    $resultado2 = $Link->query($consulta2);
				                    if($resultado2){
				                      while($registros2 = $resultado2->fetch_assoc()){
				                  ?>
				                        <option value="<?php echo $registros2['CodigoDANE']; ?>" <?php if(isset($registros1['cod_mun']) && $registros1['cod_mun'] == $registros2['CodigoDANE']){ echo ' selected '; } ?>>
				                          <?php echo $registros2['Ciudad']; ?>
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
				                <select class="form-control select2" name="rector" id="rector" style="width: 100%;" required>
				                	<option value="">Seleccione uno</option>
				                	<?php
				                		$condicionMunicipio = (isset($_SESSION['p_Municipio']) && $_SESSION['p_Municipio'] != 0) ? "AND cod_mun LIKE '". $_SESSION['p_Municipio'] ."%'" : "";
				                    $consulta3= "SELECT num_doc, nombre FROM usuarios WHERE id_perfil = '6' $condicionMunicipio ORDER BY nombre ASC;";
				                    $resultado3 = $Link->query($consulta3);
				                    if($resultado3){
				                      while($registros3 = $resultado3->fetch_assoc()){
				                  ?>
				                        <option value="<?php echo $registros3['num_doc']; ?>" <?php if(isset($registros1['cc_rector']) && $registros1['cc_rector'] == $registros3['num_doc']){ echo ' selected '; } ?> >
				                          <?php echo $registros3['nombre']; ?>
				                        </option>
				                  <?php
				                      }
				                    }
				                  ?>
				                </select>
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      		</div> <!-- row -->
		      		<div class="row">
		      			<div class="col-lg-4 col-md-6 col-sm-8">
		      				<div class="form-group">
			                	<label for="estado">Estado</label>
			                		<div class="radio">
										<!-- <label> -->
										<input type="radio" name="estado" id="estado1" value="1" <?php echo ($registros1["estado"] == "1") ? "checked" : ""; ?> required>
											    Activar
										<!-- </label> -->
										<!-- <label> -->
										<input type="radio" name="estado" id="estado2" value="0" <?php echo ($registros1["estado"] == "0") ? "checked" : ""; ?>  required>
											    Inactivar
										<!-- </label> -->
									</div>
		      				</div> <!-- form-group -->
		      			</div> <!-- col -->
		      		</div> <!-- row -->
		      	</div> <!-- modal-body -->
		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="editar_institucion"><i class="fa fa-check"></i> Guardar</button>
		      	</div> <!-- modal-footer -->
			</div> <!-- modal-content -->
		</form> <!-- form -->
	</div> <!-- modal-dialog -->
</div> <!-- modal fade in -->


<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#modal_editar_institucion').modal('show');
		$('.select2').select2();
	});

	$('#telefono').on('keyup', function(){
  	this.value = this.value.replace(/[^0-9 -]/g, '');
	});
	
	$('input').iCheck({
	  labelHover: false,
	  cursor: true,
	  radioClass: "iradio_square-green"
	});

</script>

<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { step:"Por favor, escribe un número entero válido.", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>

<?php mysqli_close($Link); ?>














































