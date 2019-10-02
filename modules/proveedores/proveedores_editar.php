<?php
	include '../../header.php';
	$titulo = 'Actualizar proveedor';

  $idProveedor = (isset($_POST['idProveedor']) && $_POST['idProveedor'] != '') ? mysqli_real_escape_string($Link, $_POST['idProveedor']) : '';
	$consulta0 = "SELECT * FROM proveedores WHERE ID = '$idProveedor'";
	$resultado0 = $Link->query($consulta0) or die ("Error al consultar datos del empleado: ". mysqli_error($Link));
	if ($resultado0->num_rows > 0)
	{
		$registros0 = $resultado0->fetch_assoc();
		$email = $registros0['Email'];
		$telefono = $registros0['Telefono1'];
		$telefono2 = $registros0['Telefono2'];
		$direccion = $registros0['Direccion'];
		$numeroDocumento = $registros0['Nitcc'];
		$primerNombre = $registros0['PrimerNombre'];
		$tipoJuridico = $registros0['TipoJuridico'];
		$tipoDocumento = $registros0['TipoDocumento'];
		$segundoNombre = $registros0['SegundoNombre'];
		$primerApellido = $registros0['PrimerApellido'];
		$segundoApellido = $registros0['SegundoApellido'];
		$nombreComercial = $registros0['Nombrecomercial'];
		$digitoVerificacion = $registros0['DigitoVerificacion'];
	}
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/proveedores'; ?>">Proveedores</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="actualizarProveedor"><i class="fa fa-check "></i> Guardar </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formEditarProveedor" action="function/fn_proveedor_editar.php" method="post">
          	<div class="row">
        			<div class="col-sm-12">
        				<div class="row">

									<div class="form-group col-sm-6 col-md-3">
										<fieldset disabled>
			                <label for="tipoDocumento">Tipo documento</label>
			                <select class="form-control disabled" name="tipoDocumento" id="tipoDocumento"  required>
			                	<option value="">Seleccione uno</option>
			                	<?php
			                    $consulta = "SELECT id AS idTipoDocumento, nombre AS nombreTipoDocumento FROM tipodocumento;";
			                    $resultado = $Link->query($consulta) or die ('Error al listar los usuarios: . '. mysqli_error($Link));
			                    if($resultado){
			                      while($registros = $resultado->fetch_assoc()){
			                  ?>
			                        <option value="<?php echo $registros['idTipoDocumento']; ?>" <?php if(isset($tipoDocumento) && $tipoDocumento == $registros['idTipoDocumento']) { echo 'selected'; } ?>>
			                          <?php echo $registros['nombreTipoDocumento']; ?>
			                        </option>
			                  <?php
			                      }
			                    }
			                  ?>
			                </select>
										</fieldset>
										<input type="hidden" name="idProveedor" id="idProveedor" value="<?php echo $idProveedor; ?>">
		              </div>

	        				<div class="form-group col-sm-6 col-md-3">
	        					<fieldset disabled>
			                <label for="numeroDocumento">Número documento</label>
			                <select class="form-control" name="numeroDocumento" id="numeroDocumento" required>
			                	<option value="">Seleccione uno</option>
			                	<?php
			                    $consulta1= "SELECT usu.num_doc AS cedulaEmpleado FROM usuarios usu WHERE usu.id_perfil = '2' AND usu.Tipo_Usuario = 'Proveedor';";
			                    $resultado1 = $Link->query($consulta1) or die ('Error al listar los usuarios: . '. mysqli_error($Link));
			                    if($resultado1){
			                      while($registros1 = $resultado1->fetch_assoc()){
			                  ?>
			                        <option value="<?php echo $registros1['cedulaEmpleado']; ?>" <?php if(isset($numeroDocumento) && $numeroDocumento == $registros1['cedulaEmpleado']){ echo 'selected'; } ?>>
			                          <?php echo $registros1['cedulaEmpleado']; ?>
			                        </option>
			                  <?php
			                      }
			                    }
			                  ?>
			                </select>
		              	</fieldset>
		                <input type="hidden" name="numeroDocumentohidden" id="numeroDocumentohidden" value="<?php echo $numeroDocumento; ?>">
		              </div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="nombreComercial">Nombre comercial</label>
		                <input type="text" class="form-control" name="nombreComercial" id="nombreComercial" value="<?php echo $nombreComercial; ?>" required>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="tipoJuridico">Tipo jurídico</label>
		                <select class="form-control" name="tipoJuridico" id="tipoJuridico" required>
		                	<option value="">Seleccione uno</option>
		                	<option value="Régimen común" <?php if(isset($tipoJuridico) && $tipoJuridico == 'Régimen común'){ echo 'selected'; } ?>>Régimen común</option>
		                	<option value="Régimen simplificado" <?php if(isset($tipoJuridico) && $tipoJuridico == 'Régimen simplificado'){ echo 'selected'; } ?>>Régimen simplificado</option>
		                </select>
		              </div>

        				</div>

        				<div class="row">

        					<div class="form-group col-sm-6 col-md-3">
		                <label for="primerNombre">Primer nombre</label>
		                <input type="text" class="form-control" name="primerNombre" id="primerNombre" value="<?php echo $primerNombre; ?>" required>
		              </div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="segundoNombre">Segundo nombre</label>
		                <input type="text" class="form-control" name="segundoNombre" id="segundoNombre" value="<?php echo $segundoNombre; ?>">
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="primerApellido">Primer apellido</label>
		                <input type="text" class="form-control" name="primerApellido" id="primerApellido" value="<?php echo $primerApellido; ?>" required>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="segundoApellido">Segundo apellido</label>
		                <input type="text" class="form-control" name="segundoApellido" id="segundoApellido" value="<?php echo $segundoApellido; ?>">
		              </div>

        				</div>

								<div class="row">

									<div class="form-group col-sm-6 col-md-3">
		                <label for="email">Email</label>
		                <input type="email" class="form-control" name="email" id="email" value="<?php echo $email; ?>" readOnly required>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="direccion">Dirección</label>
		                <input type="text" class="form-control" name="direccion" id="direccion" value="<?php echo $direccion; ?>" required>
		              </div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="telefono">Teléfono 1</label>
		                <input type="text" class="form-control" name="telefono" id="telefono" value="<?php echo $telefono; ?>" required>
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="telefono2">Teléfono 2</label>
		                <input type="text" class="form-control" name="telefono2" id="telefono2" value="<?php echo $telefono2; ?>">
									</div>

								</div>

								<div class="row">

									<div class="form-group col-sm-6 col-md-3">
		                <label for="digitoVerificacion">Dígito verificación</label>
		                <input type="text" class="form-control" name="digitoVerificacion" id="digitoVerificacion" value="<?php echo $digitoVerificacion; ?>">
									</div>

								</div>

        			</div>
          	</div>
          	<div class="row">
          		<div class="col-sm12">
          			<div class="row-">
          				<div class="col-sm-3 col-lg-2 text-center">
      							<a href="#" class="btn btn-primary" id="actualizarProveedorContinuar"><i class="fa fa-check "></i> Guardar y Continuar </a>
          				</div>
          			</div>
          		</div>
          	</div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/proveedores/js/proveedores.js"></script>
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	// Configuración del plugin datepicker
	$.fn.datepicker.dates['en'] = {
	  days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
	  daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab", "Dom"],
	  daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
	  months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	  monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
	};
	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd'
	});
</script>
<?php mysqli_close($Link); ?>

</body>
</html>