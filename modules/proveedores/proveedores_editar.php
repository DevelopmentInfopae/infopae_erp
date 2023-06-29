<?php
	include '../../header.php';

	if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    	?><script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	<?php exit(); }
	  	  else {
			?><script type="text/javascript">
			  const list = document.querySelector(".li_configuracion");
			  list.className += " active ";
			  const list2 = document.querySelector(".li_proveedores");
			  list2.className += " active ";
			</script>
			<?php
			}

	$titulo = 'Actualizar proveedor';

	$codigo_municipio = $_SESSION['p_Municipio'];
  	$codigo_departamento = $_SESSION['p_CodDepartamento'];

  	$idProveedor = (isset($_POST['idProveedor']) && $_POST['idProveedor'] != '') ? mysqli_real_escape_string($Link, $_POST['idProveedor']) : '';
	$consulta0 = "SELECT * FROM proveedores WHERE ID = '$idProveedor'";
	$resultado0 = $Link->query($consulta0) or die ("Error al consultar datos del empleado: ". mysqli_error($Link));
	if ($resultado0->num_rows > 0) {
		$registros0 = $resultado0->fetch_assoc();

		$tipoJuridico = $registros0['TipoJuridico'];
		$tipoDocumento = $registros0['TipoDocumento'];
		$digitoVerificacion = $registros0['DigitoVerificacion'];
		$razonSocial = $registros0["RazonSocial"];
		$nombreComercial = $registros0['Nombrecomercial'];
		$primerNombre = $registros0['PrimerNombre'];
		$segundoNombre = $registros0['SegundoNombre'];
		$primerApellido = $registros0['PrimerApellido'];
		$segundoApellido = $registros0['SegundoApellido'];
		$email = $registros0['Email'];
		$telefonofijo = $registros0['Telefono1'];
		$telefonomovil = $registros0['Telefono2'];
		$numeroDocumento = $registros0['Nitcc'];
		$direccion = $registros0['Direccion'];
		$municipio = $registros0['cod_municipio'];
		$tipoalimentos = explode(",", $registros0['TipoAlimento']);
		$compraslocales = $registros0['compraslocales'];
		$estado = $registros0["estado"];
	}
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?= $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?= $baseUrl; ?>">Home</a>
      </li>
      <li>
      	<a href="<?= $baseUrl . '/modules/proveedores'; ?>">Proveedores</a>
      </li>
      <li class="active">
        <strong><?= $titulo; ?></strong>
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
        	<form id="formEditarProveedor" method="post">
        		<input type="hidden" name="idProveedor" id="idProveedor" value="<?= $idProveedor; ?>">
        		<input type="hidden" name="numeroDocumentohidden" id="numeroDocumentohidden" value="<?= $numeroDocumento; ?>">
          				<div class="row">
    						<div class="col-sm-12">
        						<div class="row">
									<div class="form-group col-sm-6 col-md-3">
						                <label for="tipoJuridico">Tipo jurídico</label>
						                <select class="form-control" name="tipoJuridico" id="tipoJuridico" required readonly>
						                	<option value="">Seleccione</option>
						                	<option value="1" <?= ($tipoJuridico == 1) ? "selected": ""; ?>>Persona Jurídica</option>
						                	<option value="2" <?= ($tipoJuridico == 2) ? "selected": ""; ?>>Persona Natural</option>
						                </select>
					              	</div>

					              	<div class="form-group col-sm-6 col-md-3">
						                <label for="tipoRegimen">Tipo régimen</label>
						                <select class="form-control" name="tipoRegimen" id="tipoRegimen" required readonly>
						                	<option value="">Seleccione</option>
						                	<option value="1">Común</option>
						                	<option value="2">Simplificado</option>
						                </select>
					              	</div>

									<div class="form-group col-sm-6 col-md-3">
		                				<label for="tipoDocumento">Tipo documento</label>
		                				<select class="form-control" name="tipoDocumento" id="tipoDocumento" required readonly>
		                					<option value="">Seleccione</option>
					                	</select>
		              				</div>

			        				<div class="form-group col-sm-6 col-md-3">
				                		<label for="numeroDocumento">Número documento</label>
				                		<input class="form-control" type="text" name="numeroDocumento" id="numeroDocumento" value="<?= $numeroDocumento; ?>" required readonly>
				              		</div>
        						</div>

        						<div class="row">
									<div class="form-group col-sm-6 col-md-3">
				                		<label for="digitoVerificacion">Dígito de verificación</label>
				                		<input class="form-control" type="number" name="digitoVerificacion" id="digitoVerificacion" value="<?= $digitoVerificacion; ?>" required readonly>
				              		</div>

									<div class="form-group col-sm-6 col-md-3">
						                <label for="razonSocial">Razón Social</label>
						                <input type="text" class="form-control" name="razonSocial" id="razonSocial" value="<?= $razonSocial; ?>" required>
						            </div>

									<div class="form-group col-sm-6 col-md-3">
						                <label for="nombreComercial">Nombre comercial</label>
						                <input type="text" class="form-control" name="nombreComercial" id="nombreComercial" value="<?= $nombreComercial; ?>" required>
						            </div>
						        </div>


								<div class="row">
        							<div class="form-group col-sm-6 col-md-3">
						                <label for="primerNombre">Primer nombre</label>
						                <input type="text" class="form-control" name="primerNombre" id="primerNombre" value="<?= $primerNombre; ?>" required>
						            </div>

									<div class="form-group col-sm-6 col-md-3">
					                	<label for="segundoNombre">Segundo nombre</label>
					                	<input type="text" class="form-control" name="segundoNombre" id="segundoNombre" value="<?= $segundoNombre; ?>">
					              	</div>

					              	<div class="form-group col-sm-6 col-md-3">
						                <label for="primerApellido">Primer apellido</label>
						                <input type="text" class="form-control" name="primerApellido" id="primerApellido" value="<?= $primerApellido; ?>" required>
					              	</div>

									<div class="form-group col-sm-6 col-md-3">
										<label for="segundoApellido">Segundo apellido</label>
										<input type="text" class="form-control" name="segundoApellido" id="segundoApellido" value="<?= $segundoApellido; ?>">
									</div>
        						</div>

								<div class="row">
									<div class="form-group col-sm-6 col-md-3">
						                <label for="email">Email</label>
						                <input type="email" class="form-control" name="email" id="email" value="<?= $email; ?>" required readonly>
					              	</div>

									<div class="form-group col-sm-6 col-md-3">
		                				<label for="telefonofijo">Teléfono fijo</label>
		                				<input type="tel" class="form-control" name="telefonofijo" id="telefonofijo" value="<?= $telefonofijo; ?>" required>
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                				<label for="telefonomovil">Teléfono movil</label>
		                				<input type="tel" class="form-control" name="telefonomovil" id="telefonomovil" value="<?= $telefonomovil; ?>">
									</div>

					              	<div class="form-group col-sm-6 col-md-3">
						                <label for="direccion">Dirección</label>
						                <input type="text" class="form-control" name="direccion" id="direccion" value="<?= $direccion; ?>" required>
					              	</div>
								</div>

								<div class="row">
									<div class="form-group col-sm-6 col-md-3">
						                <label for="municipio">Municipio</label>
						                <select class="form-control select2" name="municipio" id="municipio" required>
						                	<?php
						                      	$parametro_municipio = (! empty($codigo_municipio)) ? "AND CodigoDANE = '$codigo_municipio'" : "";
						                      	$consulta_municipios = "SELECT CodigoDANE AS codigo, Ciudad AS nombre, Departamento AS departamento FROM ubicacion ORDER BY Ciudad ASC;";
						                      	$respuesta_consulta_municipios = $Link->query($consulta_municipios) or die('Error al consultar municipios: '. $Link->error);
						                      	if (! empty($respuesta_consulta_municipios->num_rows)) {
						                        	while($municipio = $respuesta_consulta_municipios->fetch_object()) {
						                          		$seleccion = ($municipio->codigo == $codigo_municipio) ? 'selected' : ($municipio == $municipio->codigo) ? "selected" : "";
					                          			echo '<option value="'. $municipio->codigo .'" '. $seleccion .'>'. $municipio->nombre .' ('. $municipio->departamento .') </option>';
						                        	}
						                      	}
						                    ?>
						                </select>
						                <label id="municipio-error" class="error" for="municipio"></label>
					              	</div>

					              	<div class="form-group col-sm-6 col-md-3">
						                <label for="tipoalimento">Tipo alimento</label>
						                <select class="form-control select2" name="tipoalimento[]" id="tipoalimento" multiple="multiple" required>
						                	<?php
						                		$consulta_tipo_alimento = "SELECT * FROM tipo_despacho";
					                			$respuesta_tipo_alimento = $Link->query($consulta_tipo_alimento) or die('Error al consultar tipo documentos: '. $Link->error);

					                			if (! empty($respuesta_tipo_alimento->num_rows)) {
						                        	while($tipoalimento = $respuesta_tipo_alimento->fetch_object()) {
					                        			$selected = "";

						                        		foreach ($tipoalimentos as $alimento) {
						                        			if ($alimento == $tipoalimento->Id) {
					                        					$selected = "selected";
					                        					break;
						                        			}
						                        		}

			                          					echo '<option value="'. $tipoalimento->Id .'" '. $selected .'>'. $tipoalimento->Descripcion .'</option>';
						                        	}
						                      	}
						                	?>
						                </select>
					              	</div>

					              	<div class="form-group col-sm-6 col-md-3">
						                <label for="compraslocales">Compras locales</label>
						                <select class="form-control" name="compraslocales" id="compraslocales" required>
						                	<option value="1" <?= ($compraslocales == 1) ? "selected" : ""; ?>>SI</option>
						                	<option value="0" <?= ($compraslocales == 0) ? "selected" : ""; ?>>NO</option>
						                </select>
					              	</div>

					              	<div class="form-group col-sm-6 col-md-3">
						                <label for="estado">Estado</label>
						                <select class="form-control" name="estado" id="estado" required>
						                	<option value="1" <?= ($estado == 1) ? "selected" : ""; ?>>Activo</option>
						                	<option value="0" <?= ($estado == 0) ? "selected" : ""; ?>>Inactivo</option>
						                </select>
					              	</div>
								</div>
        					</div>
          				</div>

			          	<div class="row">
	          				<div class="col-sm-3 col-lg-2">
	      						<a href="#" class="btn btn-primary" id="actualizarProveedorContinuar"><i class="fa fa-check "></i> Guardar </a>
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
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<!-- Section Scripts -->
<script src="<?= $baseUrl; ?>/modules/proveedores/js/proveedores.js"></script>
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

	$(document).ready(function() {
		$('#tipoJuridico').trigger('change');
	});
</script>
<?php mysqli_close($Link); ?>

</body>
</html>