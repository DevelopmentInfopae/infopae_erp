<?php
	include '../../header.php';
	$titulo = 'Nuevo Empleado';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/empleados'; ?>">Empleados</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="guardarEmpleado"><i class="fa fa-check "></i> Guardar </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formCrearEmpleado" action="function/fn_empleados_crear.php" method="post">
          	<div class="row">
        			<div class="col-sm-12">
        				<div class="row">

									<div class="form-group col-sm-6 col-md-3">
		                <label for="tipoDocumento">Tipo documento</label>
		                <select class="form-control" name="tipoDocumento" id="tipoDocumento" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $consulta = "SELECT id AS idTipoDocumento, nombre AS nombreTipoDocumento FROM tipodocumento;";
		                    $resultado = $Link->query($consulta) or die ('Error al listar los usuarios: . '. mysqli_error($Link));
		                    if($resultado){
		                      while($registros = $resultado->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $registros['idTipoDocumento']; ?>">
		                          <?php echo $registros['nombreTipoDocumento']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

	        				<div class="form-group col-sm-6 col-md-3">
		                <label for="numeroDocumento">Número documento</label>
		                <select class="form-control" name="numeroDocumento" id="numeroDocumento" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $consulta1= "SELECT usu.num_doc AS cedulaEmpleado FROM usuarios usu WHERE usu.id_perfil = '2' AND usu.Tipo_Usuario = 'Empleado';";
		                    $resultado1 = $Link->query($consulta1) or die ('Error al listar los usuarios: . '. mysqli_error($Link));
		                    if($resultado1){
		                      while($registros1 = $resultado1->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $registros1['cedulaEmpleado']; ?>">
		                          <?php echo $registros1['cedulaEmpleado']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>


									<div class="form-group col-sm-6 col-md-3">
		                <label for="libretaMilitar">Libreta militar</label>
		                <input type="number" class="form-control" name="libretaMilitar" id="libretaMilitar">
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="estadoCivil">Estado civil</label>
		                <select class="form-control" name="estadoCivil" id="estadoCivil">
		                	<option value="">Seleccione uno</option>
		                	<option value="Soltero/a">Soltero/a</option>
		                	<option value="Comprometido/a">Comprometido/a</option>
		                	<option value="Casado/a">Casado/a</option>
		                	<option value="Unión libre">Unión libre</option>
		                	<option value="Divorciado/a">Divorciado/a</option>
		                	<option value="Viudo/a">Viudo/a</option>
		                </select>
		              </div>

        				</div>

        				<div class="row">

        					<div class="form-group col-sm-6 col-md-3">
		                <label for="primerNombre">Primer nombre</label>
		                <input type="text" class="form-control" name="primerNombre" id="primerNombre" required>
		              </div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="segundoNombre">Segundo nombre</label>
		                <input type="text" class="form-control" name="segundoNombre" id="segundoNombre">
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="primerApellido">Primer apellido</label>
		                <input type="text" class="form-control" name="primerApellido" id="primerApellido" required>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="segundoApellido">Segundo apellido</label>
		                <input type="text" class="form-control" name="segundoApellido" id="segundoApellido">
		              </div>

        				</div>

								<div class="row">

									<div class="form-group col-sm-6 col-md-3">
		                <label for="email">Email</label>
		                <input type="email" class="form-control" name="email" id="email" readonly required>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="direccion">Dirección</label>
		                <input type="text" class="form-control" name="direccion" id="direccion" required>
		              </div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="telefono">Teléfono 1</label>
		                <input type="tel" class="form-control" name="telefono" id="telefono" required>
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="telefono2">Teléfono 2</label>
		                <input type="tel" class="form-control" name="telefono2" id="telefono2">
									</div>

								</div>

								<div class="row">

									<div class="form-group col-sm-6 col-md-3">
		                <label for="departamentoResidencia">Departamento de residencia</label>
		                <select class="form-control" name="departamentoResidencia" id="departamentoResidencia" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $consulta3= "SELECT id AS codigoDepartamento, nombre AS nombreDepartamento FROM departamentos WHERE id <> 0;";
		                    $resultado3 = $Link->query($consulta3) or die ('Error al listar los departamentos: . '. mysqli_error($Link));
		                    if($resultado3){
		                      while($registros3 = $resultado3->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $registros3['codigoDepartamento']; ?>">
		                          <?php echo $registros3['nombreDepartamento']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="municipioResidencia">Municipio de residencia</label>
		                <select class="form-control" name="municipioResidencia" id="municipioResidencia" required>
		                	<option value="">Seleccione uno</option>
		                </select>
		              </div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="barrio">Barrio</label>
		                <input type="text" class="form-control" name="barrio" id="barrio">
									</div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="tipoSangre">Tipo de sangre</label>
		                <select class="form-control" name="tipoSangre" id="tipoSangre">
		                	<option value="">Seleccione uno</option>
		                	<option value="O+">O positivo</option>
		                	<option value="O-">O negativo</option>
		                	<option value="A+">A positivo</option>
		                	<option value="A-">A negativo</option>
		                	<option value="B+">B positivo</option>
		                	<option value="B-">B negativo</option>
		                	<option value="AB+">AB positivo</option>
		                	<option value="AB-">AB negativo</option>
		                </select>
		              </div>

								</div>

								<div class="row">

									<div class="form-group col-sm-6 col-md-3">
		                <label for="departamentoNacimiento">Departamento de nacimiento</label>
		                <select class="form-control" name="departamentoNacimiento" id="departamentoNacimiento" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $consulta3= "SELECT id AS codigoDepartamento, nombre AS nombreDepartamento FROM departamentos WHERE id <> 0;";
		                    $resultado3 = $Link->query($consulta3) or die ('Error al listar los departamentos: . '. mysqli_error($Link));
		                    if($resultado3){
		                      while($registros3 = $resultado3->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $registros3['codigoDepartamento']; ?>">
		                          <?php echo $registros3['nombreDepartamento']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="municipioNacimiento">Municipio de nacimiento</label>
		                <select class="form-control" name="municipioNacimiento" id="municipioNacimiento" required>
		                	<option value="">Seleccione uno</option>
		                </select>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="fechaNacimiento">Fecha de nacimiento</label>
		                <input type="text" class="form-control datepicker" name="fechaNacimiento" id="fechaNacimiento" value=""  required>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="sexo">Sexo</label>
		                <div class="radio">
										  <label>
										    <input type="radio" name="sexo" id="sexo1" value="M" required> Masculino
										  </label>
										  <label>
										    <input type="radio" name="sexo" id="sexo2" value="F" required> Femenino
										  </label>
										</div>
										<label for="sexo" class="error" style="display: none;"></label>
		              </div>

								</div>

								<div class="row">

									<div class="form-group col-sm-6 col-md-3">
		                <label for="profesion">Profesión</label>
		                <input type="text" class="form-control" name="profesion" id="profesion" value="">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="cargo">Cargo</label>
		                <input type="text" class="form-control" name="cargo" id="cargo" value="">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="nivelEstudio">Nivel de estudio</label>
		                <input type="text" class="form-control" name="nivelEstudio" id="nivelEstudio" value="">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="numeroContrato">Número contrato</label>
		                <input type="text" class="form-control" name="numeroContrato" id="numeroContrato" value="">
									</div>

								</div>

								<div class="row">
									<div class="form-group col-sm-6 col-md-3">
		                <label for="tallaPantalon">Talla pantalón</label>
		                <input type="text" class="form-control" name="tallaPantalon" id="tallaPantalon" value="">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="tallaCamisa">Talla camisa</label>
		                <input type="text" class="form-control" name="tallaCamisa" id="tallaCamisa" value="">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="numeroCalzado">Número calzado</label>
		                <input type="text" class="form-control" name="numeroCalzado" id="numeroCalzado" value="">
									</div>

								</div>

        			</div>
          	</div>
          	<div class="row">
          		<div class="col-sm12">
          			<div class="row-">
          				<div class="col-sm-3 col-lg-2 text-center">
      							<a href="#" class="btn btn-primary" id="guardarEmpleadoContinuar"><i class="fa fa-check "></i> Guardar y Continuar </a>
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
<script src="<?php echo $baseUrl; ?>/modules/empleados/js/empleados.js"></script>
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