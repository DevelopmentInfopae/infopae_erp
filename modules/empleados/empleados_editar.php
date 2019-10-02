<?php
	include '../../header.php';
	$titulo = 'Actualizar empleado';

  $idEmpleado = (isset($_POST['idEmpleado']) && $_POST['idEmpleado'] != '') ? mysqli_real_escape_string($Link, $_POST['idEmpleado']) : '';
	$consulta0 = "SELECT * FROM empleados WHERE ID = '$idEmpleado'";
	$resultado0 = $Link->query($consulta0) or die ("Error al consultar datos del empleado: ". mysqli_error($Link));
	if ($resultado0->num_rows > 0)
	{
		$registros0 = $resultado0->fetch_assoc();
		$tipoDocumento = $registros0['TipoDoc'];
		$cedulaEmpleado = $registros0['Nitcc'];
		$libretaMilitar = $registros0['LibretaMilitar'];
		$estadoCivil = $registros0['EstadoCivil'];
		$primerNombre = $registros0['PrimerNombre'];
		$segundoNombre = $registros0['SegundoNombre'];
		$primerApellido = $registros0['PrimerApellido'];
		$segundoApellido = $registros0['SegundoApellido'];
		$email = $registros0['Email'];
		$direccion = $registros0['Direccion'];
		$telefono = $registros0['Telefono1'];
		$telefono2 = $registros0['Telefono2'];
		$departamentoResidenciaString = substr($registros0['Ciudad'], 0, 2);
		$departamentoResidencia = (int) substr($registros0['Ciudad'], 0, 2);
		$municipioResidencia = $registros0['Ciudad'];
		$barrio = $registros0['Barrio'];
		$tipoSangre = $registros0['TipoSangre'];
		$departamentoNacimientoString = substr($registros0['LugarNacimiento'], 0, 2);
		$departamentoNacimiento = (int) substr($registros0['LugarNacimiento'], 0, 2);
		$municipioNacimiento = $registros0['LugarNacimiento'];
		$fechaNacimiento = $registros0['FechaNacimiento'];
		$sexo = $registros0['Sexo'];
		$profesion = $registros0['Profesion'];
		$cargo = $registros0['Cargo'];
		$nivelEstudio = $registros0['NivelEstudio'];
		$numeroContrato = $registros0['Contrato'];
		$tallaPantalon = $registros0['TallaPantalon'];
		$tallaCamisa = $registros0['TallaCamisa'];
		$numeroCalzado = $registros0['NumeroCalzado'];
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
      	<a href="<?php echo $baseUrl . '/modules/empleados'; ?>">Empleados</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="actualizarEmpleado"><i class="fa fa-check "></i> Guardar </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formEditarEmpleado" action="function/fn_empleados_crear.php" method="post">
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
										<input type="hidden" name="idEmpleado" id="idEmpleado" value="<?php echo $idEmpleado; ?>">
		              </div>

	        				<div class="form-group col-sm-6 col-md-3">
	        					<fieldset disabled>
			                <label for="numeroDocumento">Número documento</label>
			                <select class="form-control" name="numeroDocumento" id="numeroDocumento" required>
			                	<option value="">Seleccione uno</option>
			                	<?php
			                    $consulta1= "SELECT usu.num_doc AS cedulaEmpleado FROM usuarios usu WHERE usu.id_perfil = '2' AND usu.Tipo_Usuario = 'Empleado';";
			                    $resultado1 = $Link->query($consulta1) or die ('Error al listar los usuarios: . '. mysqli_error($Link));
			                    if($resultado1){
			                      while($registros1 = $resultado1->fetch_assoc()){
			                  ?>
			                        <option value="<?php echo $registros1['cedulaEmpleado']; ?>" <?php if(isset($cedulaEmpleado) && $cedulaEmpleado == $registros1['cedulaEmpleado']){ echo 'selected'; } ?>>
			                          <?php echo $registros1['cedulaEmpleado']; ?>
			                        </option>
			                  <?php
			                      }
			                    }
			                  ?>
			                </select>
		              	</fieldset>
		                <input type="hidden" name="numeroDocumentohidden" id="numeroDocumentohidden" value="<?php echo $cedulaEmpleado; ?>">
		              </div>


									<div class="form-group col-sm-6 col-md-3">
		                <label for="libretaMilitar">Libreta militar</label>
		                <input type="number" class="form-control" name="libretaMilitar" id="libretaMilitar" value="<?php echo $libretaMilitar; ?>">
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="estadoCivil">Estado civil</label>
		                <select class="form-control" name="estadoCivil" id="estadoCivil">
		                	<option value="">Seleccione uno</option>
		                	<option value="Soltero/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Soltero/a'){ echo 'selected'; } ?>>Soltero/a</option>
		                	<option value="Comprometido/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Comprometido/a'){ echo 'selected'; } ?>>Comprometido/a</option>
		                	<option value="Casado/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Casado/a'){ echo 'selected'; } ?>>Casado/a</option>
		                	<option value="Unión libre" <?php if(isset($estadoCivil) && $estadoCivil == 'Unión libre'){ echo 'selected'; } ?>>Unión libre</option>
		                	<option value="Divorciado/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Divorciado/a'){ echo 'selected'; } ?>>Divorciado/a</option>
		                	<option value="Viudo/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Viudo/a'){ echo 'selected'; } ?>>Viudo/a</option>
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
		                <input type="tel" class="form-control" name="telefono" id="telefono" value="<?php echo $telefono; ?>" required>
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="telefono2">Teléfono 2</label>
		                <input type="tel" class="form-control" name="telefono2" id="telefono2" value="<?php echo $telefono2; ?>">
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
		                        <option value="<?php echo $registros3['codigoDepartamento']; ?>" <?php if(isset($departamentoResidencia) && $departamentoResidencia == $registros3['codigoDepartamento']){ echo 'selected'; } ?>>
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
											<?php
		                    $consulta4= "SELECT ubi.CodigoDANE AS codigoMunicipio, ubi.Ciudad AS nombreMunicipio FROM ubicacion ubi WHERE ubi.CodigoDANE LIKE '$departamentoResidenciaString%';";
		                    $resultado4 = $Link->query($consulta4) or die ('Error al listar los departamentos: . '. mysqli_error($Link));
		                    if($resultado4){
		                      while($registros4 = $resultado4->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $registros4['codigoMunicipio']; ?>" <?php if(isset($municipioResidencia) && $municipioResidencia == $registros4['codigoMunicipio']){ echo 'selected'; } ?>>
		                          <?php echo $registros4['nombreMunicipio']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="barrio">Barrio</label>
		                <input type="text" class="form-control" name="barrio" id="barrio" value="<?php echo $barrio; ?>">
									</div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="tipoSangre">Tipo de sangre</label>
		                <select class="form-control" name="tipoSangre" id="tipoSangre">
		                	<option value="">Seleccione uno</option>
		                	<option value="O+" <?php if(isset($tipoSangre) && $tipoSangre == '0+'){ echo 'selected'; } ?>>O positivo</option>
		                	<option value="O-"<?php if(isset($tipoSangre) && $tipoSangre == '0-'){ echo 'selected'; } ?>>O negativo</option>
		                	<option value="A+"<?php if(isset($tipoSangre) && $tipoSangre == 'A+'){ echo 'selected'; } ?>>A positivo</option>
		                	<option value="A-"<?php if(isset($tipoSangre) && $tipoSangre == 'A-'){ echo 'selected'; } ?>>A negativo</option>
		                	<option value="B+"<?php if(isset($tipoSangre) && $tipoSangre == 'B+'){ echo 'selected'; } ?>>B positivo</option>
		                	<option value="B-"<?php if(isset($tipoSangre) && $tipoSangre == 'B-'){ echo 'selected'; } ?>>B negativo</option>
		                	<option value="AB+"<?php if(isset($tipoSangre) && $tipoSangre == 'AB+'){ echo 'selected'; } ?>>AB positivo</option>
		                	<option value="AB-"<?php if(isset($tipoSangre) && $tipoSangre == 'AB-'){ echo 'selected'; } ?>>AB negativo</option>
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
		                        <option value="<?php echo $registros3['codigoDepartamento']; ?>" <?php if(isset($departamentoNacimiento) && $departamentoNacimiento == $registros3['codigoDepartamento']){ echo 'selected'; } ?>>
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
		                	<?php
		                    $consulta4= "SELECT ubi.CodigoDANE AS codigoMunicipio, ubi.Ciudad AS nombreMunicipio FROM ubicacion ubi WHERE ubi.CodigoDANE LIKE '$departamentoNacimientoString%';";
		                    $resultado4 = $Link->query($consulta4) or die ('Error al listar los departamentos: . '. mysqli_error($Link));
		                    if($resultado4){
		                      while($registros4 = $resultado4->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $registros4['codigoMunicipio']; ?>" <?php if(isset($municipioNacimiento) && $municipioNacimiento == $registros4['codigoMunicipio']){ echo 'selected'; } ?>>
		                          <?php echo $registros4['nombreMunicipio']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="fechaNacimiento">Fecha de nacimiento</label>
		                <input type="text" class="form-control datepicker" name="fechaNacimiento" id="fechaNacimiento" value="<?php echo $fechaNacimiento; ?>"  required>
		              </div>

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="sexo">Sexo</label>
		                <div class="radio">
										  <label>
										    <input type="radio" name="sexo" id="sexo1" value="M" <?php if(isset($sexo) && $sexo == 'M'){ echo 'checked'; } ?> required> Masculino
										  </label>
										  <label>
										    <input type="radio" name="sexo" id="sexo2" value="F" <?php if(isset($sexo) && $sexo == 'F'){ echo 'checked'; } ?> required> Femenino
										  </label>
										</div>
										<label for="sexo" class="error" style="display: none;"></label>
		              </div>

								</div>

								<div class="row">

									<div class="form-group col-sm-6 col-md-3">
		                <label for="profesion">Profesión</label>
		                <input type="text" class="form-control" name="profesion" id="profesion" value="<?php echo $profesion; ?>">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="cargo">Cargo</label>
		                <input type="text" class="form-control" name="cargo" id="cargo" value="<?php echo $cargo; ?>">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="nivelEstudio">Nivel de estudio</label>
		                <input type="text" class="form-control" name="nivelEstudio" id="nivelEstudio" value="<?php echo $nivelEstudio; ?>">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="numeroContrato">Número contrato</label>
		                <input type="text" class="form-control" name="numeroContrato" id="numeroContrato" value="<?php echo $numeroContrato; ?>">
									</div>

								</div>

								<div class="row">
									<div class="form-group col-sm-6 col-md-3">
		                <label for="tallaPantalon">Talla pantalón</label>
		                <input type="text" class="form-control" name="tallaPantalon" id="tallaPantalon" value="<?php echo $tallaPantalon; ?>">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="tallaCamisa">Talla camisa</label>
		                <input type="text" class="form-control" name="tallaCamisa" id="tallaCamisa" value="<?php echo $tallaPantalon; ?>">
									</div>

									<div class="form-group col-sm-6 col-md-3">
		                <label for="numeroCalzado">Número calzado</label>
		                <input type="text" class="form-control" name="numeroCalzado" id="numeroCalzado" value="<?php echo $numeroCalzado; ?>">
									</div>

								</div>

        			</div>
          	</div>
          	<div class="row">
          		<div class="col-sm12">
          			<div class="row-">
          				<div class="col-sm-3 col-lg-2 text-center">
      							<a href="#" class="btn btn-primary" id="actualizarEmpleadoContinuar"><i class="fa fa-check "></i> Guardar y Continuar </a>
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