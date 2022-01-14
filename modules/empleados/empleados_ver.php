<?php
	include '../../header.php';

	if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    	?><script type="text/javascript">
        	window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	<?php exit(); }

	$titulo = 'Ver empleado';
$periodoActual = $_SESSION["periodoActual"];

$idEmpleado = (isset($_POST['codigoEmpleado']) && $_POST['codigoEmpleado'] != '') ? mysqli_real_escape_string($Link, $_POST['codigoEmpleado']) : '';
$consulta0 = "SELECT * FROM empleados WHERE ID = '$idEmpleado'";
$resultado0 = $Link->query($consulta0) or die ("Error al consultar datos del empleado: ". mysqli_error($Link));
if ($resultado0->num_rows > 0)
{
	$registros0 = $resultado0->fetch_assoc();
	$tipoDocumento = $registros0['TipoDoc'];
	$foto = $registros0['Foto'];
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
	$datos_man = false;
	$datos_sede_man = false;
	if ($registros0['tipo'] == 2) {
		$consulta_man = "SELECT * FROM manipuladoras_sedes WHERE documento = ".$cedulaEmpleado;
		$resultado_man = $Link->query($consulta_man);
		if ($resultado_man->num_rows > 0) {
			while ($datman = $resultado_man->fetch_assoc()) {
				$datos_man[$datman['ID']] = $datman;
				$consulta_sede_man = "SELECT instituciones.cod_mun as cod_mun_inst, sedes.* FROM sedes$periodoActual as sedes 
										INNER JOIN instituciones ON sedes.cod_inst = instituciones.codigo_inst
									WHERE sedes.cod_sede = ".$datos_man[$datman['ID']]['cod_sede'];
				$resultados_sede_man = $Link->query($consulta_sede_man);
				if ($resultados_sede_man->num_rows > 0) {
					$datos_sede_man[$datman['ID']] = $resultados_sede_man->fetch_assoc();
				}
			}
		}
	}
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
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          	<div class="row">
    			<div class="col-sm-12">
    				<div class="row">
    					<div class="col-sm-3 col-md-3 text-center">
	        				<div class="form-group">
								<div class="fileinput fileinput-new" data-provides="fileinput">
								  <div class="fileinput-preview thumbnail img-circle" data-trigger="fileinput" style="width: 150px; height: 150px; padding: 0px;">
								  	<img class="img-responsive" <?php if ($foto != "") { ?> src="<?php echo $foto; ?>" <?php } ?> alt="">
								  </div>
								</div>
								<input type="hidden" nombre="fotoCargada" id="fotoCargada" value=" <?php echo $foto; ?> ">
							</div>
	        			</div>

	        			<div class="col-md-9">
	        				<div class="row">
	        					<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
									    <label for="tipoDocumento">Tipo documento</label>
									    <select class="form-control form-data disabled" name="tipoDocumento" id="tipoDocumento"  required>
									    	<option value="">Seleccione uno</option>
									    	<?php
									        $consulta = "SELECT id AS idTipoDocumento, nombre AS nombreTipoDocumento, Abreviatura FROM tipodocumento;";
									        $resultado = $Link->query($consulta) or die ('Error al listar los usuarios: . '. mysqli_error($Link));
									        if($resultado){
									          while($registros = $resultado->fetch_assoc()){
									      ?>
									            <option value="<?php echo $registros['idTipoDocumento']; ?>" <?php if(isset($tipoDocumento) && $tipoDocumento == $registros['Abreviatura']) { echo 'selected'; } ?>>
									              <?php echo $registros['nombreTipoDocumento']; ?>
									            </option>
									      <?php
									          }
									        }
									      ?>
									    </select>
									</fieldset>
									<input type="hidden" name="idEmpleado" id="idEmpleado" value="<?php echo $idEmpleado; ?>" class="form-data">
								</div>

								<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
									    <label for="numeroDocumento">Número documento</label>
									    <input type="text" name="numeroDocumento" id="numeroDocumento" class="form-control form-data" value="<?= $cedulaEmpleado ?>">
									</fieldset>
								</div>

								<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
										<label for="libretaMilitar">Libreta militar</label>
										<input type="number" class="form-control form-data" name="libretaMilitar" id="libretaMilitar" value="<?php echo $libretaMilitar; ?>">
									</fieldset>
								</div>
	        				</div>
	        				<div class="row">
	        					<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
										<label for="estadoCivil">Estado civil</label>
										<select class="form-control form-data" name="estadoCivil" id="estadoCivil">
											<option value="">Seleccione uno</option>
											<option value="Soltero/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Soltero/a'){ echo 'selected'; } ?>>Soltero/a</option>
											<option value="Comprometido/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Comprometido/a'){ echo 'selected'; } ?>>Comprometido/a</option>
											<option value="Casado/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Casado/a'){ echo 'selected'; } ?>>Casado/a</option>
											<option value="Unión libre" <?php if(isset($estadoCivil) && $estadoCivil == 'Unión libre'){ echo 'selected'; } ?>>Unión libre</option>
											<option value="Divorciado/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Divorciado/a'){ echo 'selected'; } ?>>Divorciado/a</option>
											<option value="Viudo/a" <?php if(isset($estadoCivil) && $estadoCivil == 'Viudo/a'){ echo 'selected'; } ?>>Viudo/a</option>
										</select>
									</fieldset>
								</div>

								<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
										<label for="primerNombre">Primer nombre</label>
										<input type="text" class="form-control form-data" name="primerNombre" id="primerNombre" value="<?php echo $primerNombre; ?>" required>
									</fieldset>
								</div>

								<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
										<label for="segundoNombre">Segundo nombre</label>
										<input type="text" class="form-control form-data" name="segundoNombre" id="segundoNombre" value="<?php echo $segundoNombre; ?>">
									</fieldset>
								</div>
	        				</div>
	        				<div class="row">
	        					<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
									<label for="primerApellido">Primer apellido</label>
									<input type="text" class="form-control form-data" name="primerApellido" id="primerApellido" value="<?php echo $primerApellido; ?>" required>
									</fieldset>
								</div>

								<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
									<label for="segundoApellido">Segundo apellido</label>
									<input type="text" class="form-control form-data" name="segundoApellido" id="segundoApellido" value="<?php echo $segundoApellido; ?>">
									</fieldset>
								</div>

								<div class="form-group col-sm-6 col-md-4">
									<fieldset disabled>
									<label for="email">Email</label>
									<input type="email" class="form-control form-data" name="email" id="email" value="<?php echo $email; ?>" readOnly required>
									</fieldset>
								</div>
	        				</div>
	        			</div>

					</div>

					<div class="row">

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="departamentoResidencia">Departamento de residencia</label>
							<select class="form-control form-data" name="departamentoResidencia" id="departamentoResidencia" required>
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
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="municipioResidencia">Municipio de residencia</label>
							<select class="form-control form-data" name="municipioResidencia" id="municipioResidencia" required>
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
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="direccion">Dirección</label>
							<input type="text" class="form-control form-data" name="direccion" id="direccion" value="<?php echo $direccion; ?>" required>
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="barrio">Barrio</label>
							<input type="text" class="form-control form-data" name="barrio" id="barrio" value="<?php echo $barrio; ?>">
									</fieldset>
						</div>

					</div>
					<div class="row">

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="telefono">Teléfono Fijo</label>
							<input type="tel" class="form-control form-data" name="telefono" id="telefono" value="<?php echo $telefono; ?>" required>
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="telefono2">Teléfono Móvil</label>
							<input type="tel" class="form-control form-data" name="telefono2" id="telefono2" value="<?php echo $telefono2; ?>">
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="departamentoNacimiento">Departamento de nacimiento</label>
							<select class="form-control form-data" name="departamentoNacimiento" id="departamentoNacimiento" required>
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
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="municipioNacimiento">Municipio de nacimiento</label>
							<select class="form-control form-data" name="municipioNacimiento" id="municipioNacimiento" required>
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
									</fieldset>
						</div>

					</div>

					<div class="row">

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="fechaNacimiento">Fecha de nacimiento</label>
							<input type="date" class="form-control form-data" name="fechaNacimiento" id="fechaNacimiento" value="<?php echo $fechaNacimiento; ?>"  required>
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="tipoSangre">Tipo de sangre</label>
							<select class="form-control form-data" name="tipoSangre" id="tipoSangre">
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
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label for="sexo">Género</label> <br>
							  <label>
							    <input type="radio" name="sexo" id="sexo1" class="form-data" disabled value="M" <?php if(isset($sexo) && $sexo == 'M'){ echo 'checked'; } ?> required> Masculino
							  </label>
							  <label>
							    <input type="radio" name="sexo" id="sexo2" class="form-data" disabled value="F" <?php if(isset($sexo) && $sexo == 'F'){ echo 'checked'; } ?> required> Femenino
							  </label>
							<label for="sexo" class="error" style="display: none;"></label>
									</fieldset>
						</div>

					</div>
					<hr>
					<div class="row">

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
						    <label for="profesion">Profesión</label>
						    <input type="text" class="form-control form-data" name="profesion" id="profesion" value="<?php echo $profesion; ?>">
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
						    <label for="cargo">Cargo</label>
						    <input type="text" class="form-control form-data" name="cargo" id="cargo" value="<?php echo $cargo; ?>">
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
						    <label for="nivelEstudio">Nivel de estudio</label>
						    <input type="text" class="form-control form-data" name="nivelEstudio" id="nivelEstudio" value="<?php echo $nivelEstudio; ?>">
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
			                <label for="numeroContrato">Número contrato</label>
			                <input type="text" class="form-control form-data" name="numeroContrato" id="numeroContrato" value="<?php echo $numeroContrato; ?>">
									</fieldset>
						</div>

					</div>

					<div class="row">
						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
			                <label for="tallaPantalon">Talla pantalón</label>
			                <input type="text" class="form-control form-data" name="tallaPantalon" id="tallaPantalon" value="<?php echo $tallaPantalon; ?>">
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
			                <label for="tallaCamisa">Talla camisa</label>
			                <input type="text" class="form-control form-data" name="tallaCamisa" id="tallaCamisa" value="<?php echo $tallaCamisa; ?>">
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
			                <label for="numeroCalzado">Número calzado</label>
			                <input type="text" class="form-control form-data" name="numeroCalzado" id="numeroCalzado" value="<?php echo $numeroCalzado; ?>">
									</fieldset>
						</div>

						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label>Tipo Empleado</label>
							<select name="tipo" id="tipo" class="form-control form-data form-data" data-edit="1">
								<option value="">Seleccione...</option>
								<option value="1" <?= $registros0['tipo'] == '1' ? 'selected' : '' ?>>Empleado</option>
								<option value="2" <?= $registros0['tipo'] == '2' ? 'selected' : '' ?>>Manipulador(a)</option>
								<option value="3" <?= $registros0['tipo'] == '3' ? 'selected' : '' ?>>Contratista</option>
								<option value="4" <?= $registros0['tipo'] == '4' ? 'selected' : '' ?>>Transportador</option>
							</select>
									</fieldset>
						</div>

					</div>

					<div class="row">
						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label>Tipo contrato</label>
							<select name="TipoContrato" id="TipoContrato" class="form-control form-data" required>
								<option value="1" <?= $registros0['TipoContrato'] == '1' ? 'selected' : '' ?>>OPS</option>
								<option value="2" <?= $registros0['TipoContrato'] == '2' ? 'selected' : '' ?>>Nómina</option>
								<option value="3" <?= $registros0['TipoContrato'] == '3' ? 'selected' : '' ?>>Obra labor</option>
								<option value="4" <?= $registros0['TipoContrato'] == '4' ? 'selected' : '' ?>>Servicios</option>
							</select>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3 div_base_mes" style="display: none;">
									<fieldset disabled>
							<label>Valor Base Mes</label>
							<input type="text" name="ValorBaseMes" id="ValorBaseMes" value="<?= $registros0['ValorBaseMes'] ?>" class="form-control form-data only_number">
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label>Fecha inicio contrato</label>
							<input type="date" name="FechaInicalContrato" id="FechaInicalContrato" value="<?= $registros0['FechaInicalContrato'] ?>" class="form-control form-data">
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">
									<fieldset disabled>
							<label>Fecha fin contrato</label>
							<input type="date" name="FechaFinalContrato" id="FechaFinalContrato" value="<?= $registros0['FechaFinalContrato'] ?>" class="form-control form-data">
									</fieldset>
						</div>
					</div>

										<!--  -->
					<div class="row">	
						<div class="form-group col-sm-6 col-md-3">	
							<label>Tipo servicio</label>
									<fieldset disabled>
							<select name="TipoServicio" id="TipoServicio" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<option value="0" <?= $registros0['TipoServicio'] == 0 ? 'selected' : '' ?> >No aplica</option>
								<option value="1" <?= $registros0['TipoServicio'] == 1 ? 'selected' : '' ?> >Servicios</option>
								<option value="2" <?= $registros0['TipoServicio'] == 2 ? 'selected' : '' ?> >Honorarios</option>
									</fieldset>
							</select>
						</div>	
						<div class="form-group col-sm-6 col-md-3">	
							<label>Salario Integral</label>
									<fieldset disabled>
							<select name="SalarioIntegral" id="SalarioIntegral" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<option value="0" <?= $registros0['SalarioIntegral'] == 0 ? 'selected' : '' ?> >No</option>
								<option value="1" <?= $registros0['SalarioIntegral'] == 1 ? 'selected' : '' ?> >Si</option>
							</select>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>Duración días</label>
									<fieldset disabled>
							<input type="text" name="DuracionDias" id="DuracionDias" class="form-control form-data only_number" value="<?= $registros0['DuracionDias'] ?>" required>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>Aux. transporte</label>
									<fieldset disabled>
							<select name="auxilio_transporte" id="auxilio_transporte" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<option value="1" <?= $registros0['auxilio_transporte'] == 1 ? 'selected' : '' ?> >Si</option>
								<option value="0" <?= $registros0['auxilio_transporte'] == 0 ? 'selected' : '' ?> >No</option>
							</select>
									</fieldset>
						</div>	
					</div>
					<div class="row">
						<div class="form-group col-sm-6 col-md-3">
							<label>Aux. extra</label>
									<fieldset disabled>
							<input type="text" class="form-control form-data only_number" name="auxilio_extra" id="auxilio_extra" value="<?= $registros0['auxilio_extra'] ?>">
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>AFP Entidad</label>
									<fieldset disabled>
							<select name="afp_entidad" id="afp_entidad" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<?php 
								$consulta_afp = "SELECT * FROM nomina_entidad WHERE tipo = 2";
								$resultado_afp = $Link->query($consulta_afp);
								if ($resultado_afp->num_rows > 0) {
									while ($afp = $resultado_afp->fetch_assoc()) { ?>
										<option value="<?= $afp['ID'] ?>" <?= $registros0['afp_entidad'] == $afp['ID'] ? 'selected' : '' ?> ><?= $afp['Entidad'] ?></option>
									<?php }
								}
								 ?>
							</select>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>EPS Entidad</label>
									<fieldset disabled>
							<select name="eps_entidad" id="eps_entidad" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<?php 
								$consulta_eps = "SELECT * FROM nomina_entidad WHERE tipo = 1";
								$resultado_eps = $Link->query($consulta_eps);
								if ($resultado_eps->num_rows > 0) {
									while ($eps = $resultado_eps->fetch_assoc()) { ?>
										<option value="<?= $eps['ID'] ?>" <?= $registros0['eps_entidad'] == $eps['ID'] ? 'selected' : '' ?> ><?= $eps['Entidad'] ?></option>
									<?php }
								}
								 ?>
							</select>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>ARL Riesgo</label>
									<fieldset disabled>
							<select name="arl_riesgo" id="arl_riesgo" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<?php 
								$consulta_arl = "SELECT * FROM nomina_riesgos";
								$resultado_arl = $Link->query($consulta_arl);
								if ($resultado_arl->num_rows > 0) {
									while ($arl = $resultado_arl->fetch_assoc()) { ?>
										<option value="<?= $arl['ID'] ?>" <?= $registros0['arl_riesgo'] == $arl['ID'] ? 'selected' : '' ?> ><?= $arl['Tipo']." (".$arl['Porcentaje'].")" ?></option>
									<?php }
								}
								 ?>
							</select>
									</fieldset>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6 col-md-3">	
							<label>Aporta CCF</label>
									<fieldset disabled>
							<select name="caja" id="caja" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<option value="1" <?= $registros0['caja'] == 1 ? 'selected' : '' ?> >Si</option>
								<option value="0" <?= $registros0['caja'] == 0 ? 'selected' : '' ?> >No</option>
							</select>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>Aporta SENA</label>
									<fieldset disabled>
							<select name="sena" id="sena" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<option value="1" <?= $registros0['sena'] == 1 ? 'selected' : '' ?> >Si</option>
								<option value="0" <?= $registros0['sena'] == 0 ? 'selected' : '' ?> >No</option>
							</select>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>Aporta ICBF</label>
									<fieldset disabled>
							<select name="icbf" id="icbf" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<option value="1" <?= $registros0['icbf'] == 1 ? 'selected' : '' ?> >Si</option>
								<option value="0" <?= $registros0['icbf'] == 0 ? 'selected' : '' ?> >No</option>
									</fieldset>
							</select>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>Forma de pago</label>
									<fieldset disabled>
							<select name="Forma_pago" id="Forma_pago" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<option value="1" <?= $registros0['Forma_pago'] == 1 ? 'selected' : '' ?> >Efectivo</option>
								<option value="2" <?= $registros0['Forma_pago'] == 2 ? 'selected' : '' ?> >Cheque</option>
								<option value="3" <?= $registros0['Forma_pago'] == 3 ? 'selected' : '' ?> >Transferencia</option>
							</select>
									</fieldset>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6 col-md-3">	
							<label>Banco</label>
									<fieldset disabled>
							<select name="Banco" id="Banco" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<?php 
								$consulta_banco = "SELECT * FROM bancos";
								$resultado_banco = $Link->query($consulta_banco);
								if ($resultado_banco->num_rows > 0) {
									while ($banco = $resultado_banco->fetch_assoc()) { ?>
										<option value="<?= $banco['ID'] ?>" <?= $registros0['Banco'] == $banco['ID'] ? 'selected' : '' ?> ><?= $banco['Descripcion'] ?></option>
									<?php }
								}
								 ?>
							</select>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
							<label>Tipo de cuenta</label>
									<fieldset disabled>
							<select name="Tipo_cuenta" id="Tipo_cuenta" class="form-control form-data" required>	
								<option value="">Seleccione...</option>
								<option value="1" <?= $registros0['Tipo_cuenta'] == 1 ? 'selected' : '' ?> >Ahorros</option>
								<option value="2" <?= $registros0['Tipo_cuenta'] == 2 ? 'selected' : '' ?> >Corriente</option>
							</select>
									</fieldset>
						</div>
						<div class="form-group col-sm-6 col-md-3">	
									<fieldset disabled>
							<label>Número de cuenta</label>
							<input type="text" name="Numero_Cuenta" id="Numero_Cuenta" class="form-control form-data" value="<?= $registros0['Numero_Cuenta'] ?>" required>
						</div>
									</fieldset>
					</div>

					<!--  -->

					<div class="row div_manipulador" style="display: none;">
						<div class="col-sm-12 col-md-12">
							<table class="table">
								<thead>
									<tr>
										<th>Tipo complemento</th>
										<th>Municipio</th>
										<th>Institución</th>
										<th>Sede</th>
										<th>Estado</th>
										<th>
										</th>
									</tr>
								</thead>
								<tbody id="manipulador_tbody">
									<?php foreach ($datos_man as $ID => $dato_manipulador): ?>
										<tr>
											<td>
												<input type="hidden" name="id_manipulador[]" value="<?= $ID ?>">
									<fieldset disabled>
												<select name="manipulador_tipo_complemento[]" class="form-control manipulador_tipo_complemento">
													<?php 
													$consulta = "SELECT * FROM tipo_complemento";
													$result = $Link->query($consulta);
													if ($result->num_rows > 0) {
														while($tcom = $result->fetch_assoc()){ ?>

															<option value="<?= $tcom['CODIGO'] ?>" <?= $dato_manipulador['tipo_complem'] == $tcom['CODIGO'] ? 'selected' : '' ?>><?= $tcom['CODIGO'] ?></option>

														<?php }
													}
													 ?>
												</select>
									</fieldset>
											</td>
											<td>
									<fieldset disabled>
												<select class="form-control manipulador_municipio" name="manipulador_municipio[]">
													<?php if ($datos_sede_man && isset($datos_sede_man[$ID])): ?>
													<?php
												    $consulta4= "SELECT ubi.CodigoDANE AS codigoMunicipio, ubi.Ciudad AS nombreMunicipio FROM ubicacion ubi WHERE ubi.CodigoDANE LIKE '".$departamentoResidencia."%';";
												    $resultado4 = $Link->query($consulta4) or die ('Error al listar los departamentos: . '. mysqli_error($Link));
												    if($resultado4){
												      while($registros4 = $resultado4->fetch_assoc()){
												  	?>
												        <option value="<?php echo $registros4['codigoMunicipio']; ?>" <?php if($datos_sede_man[$ID]['cod_mun_inst'] == $registros4['codigoMunicipio']){ echo 'selected'; } ?>>
												          <?php echo $registros4['nombreMunicipio']; ?>
												        </option>
												  <?php
												      }
												    }
												  ?>
												<?php endif ?>
												</select>
									</fieldset>
											</td>
											<td>
									<fieldset disabled>
												<select class="form-control manipulador_institucion" name="manipulador_institucion[]">
													<?php if ($datos_sede_man && isset($datos_sede_man[$ID])): ?>
														<?php 
														$consulta_inst = "SELECT * FROM instituciones WHERE cod_mun = ".$datos_sede_man[$ID]['cod_mun_inst'];
														$result_inst = $Link->query($consulta_inst);
														if ($result_inst->num_rows > 0) {
															while ($inst = $result_inst->fetch_assoc()) { ?>
																<option value="<?= $inst['codigo_inst'] ?>" <?= $inst['codigo_inst'] ==  $datos_sede_man[$ID]['cod_inst'] ? "selected" : "" ?>><?= $inst['nom_inst'] ?></option>
															<?php
															}
														}
														 ?>
													<?php endif ?>
												</select>
									</fieldset>
											</td>
											<td>
									<fieldset disabled>
												<select class="form-control manipulador_sede" name="manipulador_sede[]">
													<?php if ($datos_sede_man && isset($datos_sede_man[$ID])): ?>
														<?php 
														$consulta_sedes = "SELECT * FROM sedes$periodoActual as sedes WHERE cod_inst = ".$datos_sede_man[$ID]['cod_inst'];
														$result_sedes = $Link->query($consulta_sedes);
														if ($result_sedes->num_rows > 0) {
															while ($sede = $result_sedes->fetch_assoc()) { ?>
																<option value="<?= $sede['cod_sede'] ?>"  <?= $sede['cod_sede'] == $dato_manipulador['cod_sede'] ? 'selected' : '' ?>><?= $sede['nom_sede'] ?></option>
															<?php }
														}
														 ?>
													<?php endif ?>
												</select>
									</fieldset>
											</td>
											<td>
									<fieldset disabled>
												<select name="estado_manipulador[]" class="form-control form-data">
													<option value="1" <?= $dato_manipulador['estado'] == 1 ? 'selected' : '' ?>>Activo</option>
													<option value="0" <?= $dato_manipulador['estado'] == 0 ? 'selected' : '' ?>>Inactivo</option>
												</select>
									</fieldset>
											</td>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-6 col-md-3">
							<label>Estado</label><br>
			                <!-- <div class="radio"> -->
							  <label>
							    <input type="radio" name="estado" id="estado1" value="1" disabled class="form-data"  <?php if($registros0['estado'] == 1){ echo 'checked'; } ?>  required> Activo
							  </label>
							  <label>
							    <input type="radio" name="estado" id="estado2" value="0" disabled class="form-data" <?php if($registros0['estado'] == 0){ echo 'checked'; } ?> required> Inactivo
							  </label>
							<!-- </div> -->
							<label for="estado" class="error" style="display: none;"></label>
						</div>
					</div>
    			</div>
          	</div>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

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
	$(document).ready(function(){
		$('#tipo').trigger('change');
		$('#TipoContrato').trigger('change');
		setTimeout(function() {
			$('select.form-control').select2({width : "100%", 'disabled' : true});
		}, 800);
	});

	
</script>
<?php mysqli_close($Link); ?>

</body>
</html>