<?php
	include '../../header.php';

	if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    	?><script type="text/javascript">
        	window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	<?php exit(); }

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

						<div class="col-sm-3 col-md-3 text-center">
							<div class="form-group">
								<div class="fileinput fileinput-new" data-provides="fileinput">
									<div class="fileinput-preview thumbnail img-circle" data-trigger="fileinput" style="width: 150px; height: 150px; padding: 0px;">
										<img class="img-responsive" alt="">
									</div>
									<div class="text-center">
										<span class="btn btn-default btn-file"><span class="fileinput-new">seleccionar</span><span class="fileinput-exists">Cambiar</span><input type="file" name="foto" id="foto" accept="image/jpg, image/jpeg, image/png"></span>
										<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-9">
							<div class="row">
								<div class="form-group col-sm-4 col-md-4">
					                <label for="tipoDocumento">Tipo documento</label>
					                <select class="form-control form-data" name="tipoDocumento" id="tipoDocumento" required>
					                	<option value="">Seleccione uno</option>
					                	<?php
					                    $consulta = "SELECT id AS idTipoDocumento, nombre AS nombreTipoDocumento, Abreviatura FROM tipodocumento;";
					                    $resultado = $Link->query($consulta) or die ('Error al listar los usuarios: . '. mysqli_error($Link));
					                    if($resultado){
					                      while($registros = $resultado->fetch_assoc()){
					                  ?>
					                        <option value="<?php echo $registros['Abreviatura']; ?>">
					                          <?php echo $registros['nombreTipoDocumento']; ?>
					                        </option>
					                  <?php
					                      }
					                    }
					                  ?>
					                </select>
				                </div>

			        			<div class="form-group col-sm-4 col-md-4">
									<label for="numeroDocumento">Número documento</label>
									<input type="text" name="numeroDocumento" id="numeroDocumento" class="form-control form-data only_number" required>
					            </div>

								<div class="form-group col-sm-4 col-md-4">
									<label for="libretaMilitar">Libreta militar</label>
									<input type="text" class="form-control form-data only_number" name="libretaMilitar" id="libretaMilitar">
								</div>
								
							</div>
							<div class="row">
								<div class="form-group col-sm-4 col-md-4">
					                <label for="estadoCivil">Estado civil</label>
					                <select class="form-control form-data" name="estadoCivil" id="estadoCivil">
					                	<option value="">Seleccione uno</option>
					                	<option value="Soltero/a">Soltero/a</option>
					                	<option value="Comprometido/a">Comprometido/a</option>
					                	<option value="Casado/a">Casado/a</option>
					                	<option value="Unión libre">Unión libre</option>
					                	<option value="Divorciado/a">Divorciado/a</option>
					                	<option value="Viudo/a">Viudo/a</option>
					                </select>
					            </div>

			        			<div class="form-group col-sm-4 col-md-4">
					                <label for="primerNombre">Primer nombre</label>
					                <input type="text" class="form-control form-data" name="primerNombre" id="primerNombre" required>
					            </div>

								<div class="form-group col-sm-4 col-md-4">
					                <label for="segundoNombre">Segundo nombre</label>
					                <input type="text" class="form-control form-data" name="segundoNombre" id="segundoNombre">
					            </div>
							</div>
							<div class="row">
								<div class="form-group col-sm-4 col-md-4">
									<label for="primerApellido">Primer apellido</label>
									<input type="text" class="form-control form-data" name="primerApellido" id="primerApellido" required>
								</div>

								<div class="form-group col-sm-6 col-md-4">
									<label for="segundoApellido">Segundo apellido</label>
									<input type="text" class="form-control form-data" name="segundoApellido" id="segundoApellido">
								</div>

								<div class="form-group col-sm-6 col-md-4">
									<label for="email">Email</label>
									<input type="email" class="form-control form-data" name="email" id="email" required>
								</div>
							</div>
						</div>

				</div>

				<div class="row">

					<div class="form-group col-sm-6 col-md-3">
		                <label for="departamentoResidencia">Departamento de residencia</label>
		                <select class="form-control form-data" name="departamentoResidencia" id="departamentoResidencia" required>
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
		                <select class="form-control form-data" name="municipioResidencia" id="municipioResidencia" required>
		                	<option value="">Seleccione uno</option>
		                </select>
		              </div>

		            <div class="form-group col-sm-6 col-md-3">
		                <label for="direccion">Dirección</label>
		                <input type="text" class="form-control form-data" name="direccion" id="direccion" required>
		            </div>

					<div class="form-group col-sm-6 col-md-3">
		                <label for="barrio">Barrio</label>
		                <input type="text" class="form-control form-data" name="barrio" id="barrio">
					</div>

				</div>

				<div class="row">

					<div class="form-group col-sm-6 col-md-3">
		                <label for="telefono">Teléfono Fijo</label>
		                <input type="tel" class="form-control form-data" name="telefono" id="telefono" required>
					</div>

					<div class="form-group col-sm-6 col-md-3">
		                <label for="telefono2">Teléfono Móvil</label>
		                <input type="tel" class="form-control form-data" name="telefono2" id="telefono2">
					</div>

					<div class="form-group col-sm-6 col-md-3">
		                <label for="departamentoNacimiento">Departamento de nacimiento</label>
		                <select class="form-control form-data" name="departamentoNacimiento" id="departamentoNacimiento" required>
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
		                <select class="form-control form-data" name="municipioNacimiento" id="municipioNacimiento" required>
		                	<option value="">Seleccione uno</option>
		                </select>
		              </div>

				</div>

				<div class="row">

		              <div class="form-group col-sm-6 col-md-3">
		                <label for="fechaNacimiento">Fecha de nacimiento</label>
		                <input type="date" class="form-control form-data" name="fechaNacimiento" id="fechaNacimiento" value=""  required>
		              </div>

		            <div class="form-group col-sm-6 col-md-3">
						<label for="tipoSangre">Tipo de sangre</label>
						<select class="form-control form-data" name="tipoSangre" id="tipoSangre">
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


		              <div class="form-group col-sm-6 col-md-3">
		                <label>Género</label><br>
		                <!-- <div class="radio"> -->
						  <label>
						    <input type="radio" name="sexo" id="sexo1" class="form-data" value="M" required> Masculino
						  </label>
						  <label>
						    <input type="radio" name="sexo" id="sexo2" class="form-data" value="F" required> Femenino
						  </label>
						<!-- </div> -->
						<label for="sexo" class="error" style="display: none;"></label>
		              </div>

				</div>

				<hr>

				<div class="row">

					<div class="form-group col-sm-6 col-md-3">
		                <label for="profesion">Profesión</label>
		                <input type="text" class="form-control form-data" name="profesion" id="profesion" value="">
					</div>

					<div class="form-group col-sm-6 col-md-3">
		                <label for="cargo">Cargo</label>
		                <input type="text" class="form-control form-data" name="cargo" id="cargo" value="">
					</div>

					<div class="form-group col-sm-6 col-md-3">
		                <label for="nivelEstudio">Nivel de estudio</label>
		                <select name="nivelEstudio" id="nivelEstudio" class="form-control form-data">
		                	<option value="">Seleccione...</option>
		                	<option value="Ninguno">Ninguno</option>
		                	<option value="Primaria">Primaria</option>
		                	<option value="Secundaria">Secundaria</option>
		                	<option value="Pregrado">Pregrado</option>
		                	<option value="Especialización">Especialización</option>
		                	<option value="Maestría">Maestría</option>
		                </select>
					</div>

					<div class="form-group col-sm-6 col-md-3">
		                <label for="numeroContrato">Número contrato</label>
		                <input type="text" class="form-control form-data" name="numeroContrato" id="numeroContrato" value="">
					</div>

				</div>
				<div class="row">

					<div class="form-group col-sm-6 col-md-3">
		                <label for="tallaPantalon">Talla pantalón</label>
		                <input type="text" class="form-control form-data" name="tallaPantalon" id="tallaPantalon" value="">
					</div>

					<div class="form-group col-sm-6 col-md-3">
		                <label for="tallaCamisa">Talla camisa</label>
		                <input type="text" class="form-control form-data" name="tallaCamisa" id="tallaCamisa" value="">
					</div>

					<div class="form-group col-sm-6 col-md-3">
		                <label for="numeroCalzado">Número calzado</label>
		                <input type="text" class="form-control form-data" name="numeroCalzado" id="numeroCalzado" value="">
					</div>

					<div class="form-group col-sm-6 col-md-3">
						<label>Tipo Empleado</label>
						<select name="tipo" id="tipo" class="form-control form-data" data-edit="0" required>
							<option value="">Seleccione...</option>
							<option value="1">Empleado</option>
							<option value="2">Manipulador(a)</option>
							<option value="3">Contratista</option>
							<option value="4">Transportador</option>
						</select>
					</div>
				</div>

				<div class="row">
					<div class="form-group col-sm-6 col-md-3">
						<label>Tipo contrato</label>
						<select name="TipoContrato" id="TipoContrato" class="form-control form-data" required>
							<option value="">Seleccione...</option>
							<option value="1">Ops</option>
							<option value="2">Nómina</option>
							<option value="3">Obra labor</option>
							<option value="4">Servicios</option>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3">
						<label>Fecha inicio contrato</label>
						<input type="date" name="FechaInicalContrato" id="FechaInicalContrato" class="form-control form-data">
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>Duración días</label>
						<input type="text" name="DuracionDias" id="DuracionDias" class="form-control form-data only_number" required>
					</div>
					<div class="form-group col-sm-6 col-md-3">
						<label>Fecha fin contrato</label>
						<fieldset disabled>
							<input type="date" name="FechaFinalContrato" id="FechaFinalContrato" class="form-control form-data">
						</fieldset>
					</div>
				</div>

				<div class="row">	
					<div class="form-group col-sm-6 col-md-3">	
						<label>Salario Integral</label>
						<select name="SalarioIntegral" id="SalarioIntegral" class="form-control form-data" required>	
							<option value="">Seleccione...</option>
							<option value="0">No</option>
							<option value="1">Si</option>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3 div_base_mes" style="display: none;">
						<label>Valor Base Mes</label>
						<input type="text" name="ValorBaseMes" id="ValorBaseMes" class="form-control form-data only_number">
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>Tipo servicio</label>
						<select name="TipoServicio" id="TipoServicio" class="form-control form-data" required>	
							<option value="">Seleccione...</option>
							<option value="0">No aplica</option>
							<option value="1">Servicios</option>
							<option value="2">Honorarios</option>
						</select>
					</div>	
					<div class="form-group col-sm-6 col-md-3">	
						<label>Aux. transporte</label>
						<select name="auxilio_transporte" id="auxilio_transporte" class="form-control form-data" required>	
							<option value="">Seleccione...</option>
							<option value="1">Si</option>
							<option value="0">No</option>
						</select>
					</div>	
				</div>
				<div class="row">
					<div class="form-group col-sm-6 col-md-3">
						<label>Aux. extra</label>
						<input type="text" class="form-control form-data only_number" name="auxilio_extra" id="auxilio_extra">
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>AFP Entidad</label>
						<select name="afp_entidad" id="afp_entidad" class="form-control form-data" required>	
							<?php 
							$consulta_afp = "SELECT * FROM nomina_entidad WHERE Entidad = 'NINGUNA' AND tipo = 2";
							$resultado_afp = $Link->query($consulta_afp);
							if ($resultado_afp->num_rows > 0) {
								while ($afp = $resultado_afp->fetch_assoc()) { 
									?>
									<option value="<?= $afp['ID'] ?>" data-default="1"><?= $afp['Entidad'] ?></option>
								<?php }
							}
							 ?>
							<?php 
							$consulta_afp = "SELECT * FROM nomina_entidad WHERE tipo = 2";
							$resultado_afp = $Link->query($consulta_afp);
							if ($resultado_afp->num_rows > 0) {
								while ($afp = $resultado_afp->fetch_assoc()) { 
									if ($afp['Entidad'] == 'NINGUNA') {
										continue;
									}

									?>
									<option value="<?= $afp['ID'] ?>"><?= $afp['Entidad'] ?></option>
								<?php }
							}
							 ?>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>EPS Entidad</label>
						<select name="eps_entidad" id="eps_entidad" class="form-control form-data" required>	
							<?php 
							$consulta_eps = "SELECT * FROM nomina_entidad WHERE Entidad = 'NINGUNA' AND tipo = 1";
							$resultado_eps = $Link->query($consulta_eps);
							if ($resultado_eps->num_rows > 0) {
								while ($eps = $resultado_eps->fetch_assoc()) { 
									?>
									<option value="<?= $eps['ID'] ?>" data-default="1"><?= $eps['Entidad'] ?></option>
								<?php }
							}
							 ?>
							<?php 
							$consulta_eps = "SELECT * FROM nomina_entidad WHERE tipo = 1";
							$resultado_eps = $Link->query($consulta_eps);
							if ($resultado_eps->num_rows > 0) {
								while ($eps = $resultado_eps->fetch_assoc()) { 
									if ($eps['Entidad'] == 'NINGUNA') {
										continue;
									}

									?>
									<option value="<?= $eps['ID'] ?>"><?= $eps['Entidad'] ?></option>
								<?php }
							}
							 ?>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>ARL Riesgo</label>
						<select name="arl_riesgo" id="arl_riesgo" class="form-control form-data" required>	
							<?php 
							$consulta_arl = "SELECT * FROM nomina_riesgos WHERE Porcentaje = 0";
							$resultado_arl = $Link->query($consulta_arl);
							if ($resultado_arl->num_rows > 0) {
								while ($arl = $resultado_arl->fetch_assoc()) { ?>
									<option value="<?= $arl['ID'] ?>" data-default="1"><?= $arl['Tipo']." (".$arl['Porcentaje'].")" ?></option>
								<?php }
							}
							 ?>
							<?php 
							$consulta_arl = "SELECT * FROM nomina_riesgos WHERE Porcentaje > 0";
							$resultado_arl = $Link->query($consulta_arl);
							if ($resultado_arl->num_rows > 0) {
								while ($arl = $resultado_arl->fetch_assoc()) { ?>
									<option value="<?= $arl['ID'] ?>"><?= $arl['Tipo']." (".$arl['Porcentaje'].")" ?></option>
								<?php }
							}
							 ?>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-6 col-md-3">	
						<label>Aporta CCF</label>
						<select name="caja" id="caja" class="form-control form-data" required>	
							<option value="">Seleccione...</option>
							<option value="1">Si</option>
							<option value="0">No</option>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>Aporta SENA</label>
						<select name="sena" id="sena" class="form-control form-data" required>	
							<option value="">Seleccione...</option>
							<option value="1">Si</option>
							<option value="0">No</option>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>Aporta ICBF</label>
						<select name="icbf" id="icbf" class="form-control form-data" required>	
							<option value="">Seleccione...</option>
							<option value="1">Si</option>
							<option value="0">No</option>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>Forma de pago</label>
						<select name="Forma_pago" id="Forma_pago" class="form-control form-data" required>	
							<option value="">Seleccione...</option>
							<option value="1">Efectivo</option>
							<option value="2">Cheque</option>
							<option value="3">Transferencia</option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-6 col-md-3">	
						<label>Banco</label>
						<select name="Banco" id="Banco" class="form-control form-data" required>	
							<?php 
							$consulta_banco = "SELECT * FROM bancos WHERE Descripcion = 'No Aplica'";
							$resultado_banco = $Link->query($consulta_banco);
							if ($resultado_banco->num_rows > 0) {
								while ($banco = $resultado_banco->fetch_assoc()) { ?>
									<option value="<?= $banco['ID'] ?>"><?= $banco['Descripcion'] ?></option>
								<?php }
							}
							 ?>
							<?php 
							$consulta_banco = "SELECT * FROM bancos WHERE Descripcion != 'No Aplica'";
							$resultado_banco = $Link->query($consulta_banco);
							if ($resultado_banco->num_rows > 0) {
								while ($banco = $resultado_banco->fetch_assoc()) { ?>
									<option value="<?= $banco['ID'] ?>"><?= $banco['Descripcion'] ?></option>
								<?php }
							}
							 ?>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>Tipo de cuenta</label>
						<select name="Tipo_cuenta" id="Tipo_cuenta" class="form-control form-data" required>	
							<option value="">No Aplica</option>
							<option value="1">Ahorros</option>
							<option value="2">Corriente</option>
						</select>
					</div>
					<div class="form-group col-sm-6 col-md-3">	
						<label>Número de cuenta</label>
						<input type="text" name="Numero_Cuenta" id="Numero_Cuenta" class="form-control form-data" required>
					</div>
				</div>

				<div class="row div_manipulador" style="display: none;">
					<div class="col-sm-12 col-md-12">
						<table class="table">
							<thead>
								<tr>
									<th>Tipo complemento</th>
									<th>Municipio</th>
									<th>Institución</th>
									<th>Sede</th>
									<th>
										<button type="button" class="btn-sm btn-default add_fila_manipuladora"><span class="fa fa-plus"></span></button>
									</th>
								</tr>
							</thead>
							<tbody id="manipulador_tbody">
								<tr>
									<td>
										<select name="manipulador_tipo_complemento[]" id="manipulador_tipo_complemento" class="form-control form-data">
											<option value="">Seleccione...</option>
											<?php 
											$consulta = "SELECT * FROM tipo_complemento";
											$result = $Link->query($consulta);
											if ($result->num_rows > 0) {
												while($tcom = $result->fetch_assoc()){ ?>

													<option value="<?= $tcom['CODIGO'] ?>"><?= $tcom['CODIGO'] ?></option>

												<?php }
											}
											 ?>
										</select>
									</td>
									<td>
										<select class="form-control form-data" name="manipulador_municipio[]" id="manipulador_municipio">
											<option value="">Seleccione uno</option>
										</select>
									</td>
									<td>
										<select class="form-control form-data" name="manipulador_institucion[]" id="manipulador_institucion">
											<option value="">Seleccione uno</option>
										</select>
									</td>
									<td>
										<select class="form-control form-data" name="manipulador_sede[]" id="manipulador_sede">
											<option value="">Seleccione uno</option>
										</select>
									</td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-6 col-md-3">
						<label>Estado</label><br>
		                <!-- <div class="radio"> -->
						  <label>
						    <input type="radio" name="estado" id="estado1" value="1" class="form-data" required> Activo
						  </label>
						  <label>
						    <input type="radio" name="estado" id="estado2" value="0" class="form-data" required> Inactivo
						  </label>
						<!-- </div> -->
						<label for="estado" class="error" style="display: none;"></label>
					</div>
					<div class="form-group col-sm-6 col-md-3">
						<label>
							<input type="checkbox" name="crear_usuario" id="crear_usuario" class="form-data">
							Crear usuario
						</label>
					</div>
				</div>

			</div>
          	</div>
          	<div class="row">
          		<div class="col-sm-12">
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
</script>
<?php mysqli_close($Link); ?>

</body>
</html>