<?php
/**
 * Filtro.
 * Funcionalidades para el filtro de las entregas.
 * Rutina desarrollada originalmente para el modulo de asistencias
 * @author Ricardo Farfán <ricardo@xlogam.com>
 */
?>
<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox">

				<div class="ibox-title">
					<h5>Busqueda</h5>
					<div class="ibox-tools">
						<div class="collapse-link"> <i class="fa fa-chevron-down"></i> </div>
					</div>
				</div>

				<div class="ibox-content">

					<div class="alert alert-success"> Seleccione los datos correspondientes a al grupo de estudiantes y dispositivo para los que se desea hacer el registro. </div>
					
					<div class="row">
						<div class="col-sm-12">

							<form action="" id="form_asistencia" name="form_asistencia" method="post">
								<div class="row">




								<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0 || $_SESSION["perfil"] == 5 || $_SESSION["perfil"] == 6 || $_SESSION["perfil"] == 3) { ?>
										
										<div class="col-sm-4 form-group">
											<label for="mes">Mes</label>
											<select class="form-control" name="mes" id="mes" required>
												<option value="">Seleccione uno</option>
												<option value="01">Enero</option>									
												<option value="02">Febrero</option>									
												<option value="03">Marzo</option>									
												<option value="04">Abril</option>									
												<option value="05">Mayo</option>									
												<option value="06">Junio</option>									
												<option value="07">Julio</option>									
												<option value="08">Agosto</option>									
												<option value="09">Septiembre</option>									
												<option value="10">Octubre</option>									
												<option value="11">Noviembre</option>									
												<option value="12">Diciembre</option>									
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="semana">Semana</label>
											<select class="form-control" name="semana" id="semana" required>
												<option value="">Seleccione uno</option>									
											</select>
										</div>

										<!-- <div class="col-sm-4 form-group">
											<label for="dia">Día</label>
									<select class="form-control" name="dia" id="dia" <?php if(!isset($diaNoObligatorio) || $diaNoObligatorio != 1){ ?>required<?php } ?>>
												<option value="">Seleccione uno</option>									
											</select>
										</div> -->

									<?php } ?>







									<div class="col-sm-4 form-group">
										<label for="municipio">Municipio</label>
										<select class="form-control" name="municipio" id="municipio" required>
											<option value="">Seleccione uno</option>									
										</select>
									</div>

									<div class="col-sm-4 form-group">
										<label for="institucion">Institución</label>
										<select class="form-control" name="institucion" id="institucion" required>
											<option value="">Seleccione una</option>									
										</select>
									</div>

									<div class="col-sm-4 form-group">
										<label for="sede">Sede</label>
										<select class="form-control" name="sede" id="sede" required>
											<option value="">Seleccione una</option>
										</select>
									</div>  


									<?php if(!isset($ventanaRepitentes) || $ventanaRepitentes == 0){ ?>
									<?php } ?>



									<?php if($_SESSION["perfil"] != 5 && $_SESSION["perfil"] != 6){ ?>

										<div class="col-sm-4 form-group">
											<label for="nivel">Nivel</label>
											<select class="form-control" name="nivel" id="nivel" required>
												<option value="">Seleccione uno</option>
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="grado">Grado</label>
											<select class="form-control" name="grado" id="grado">
												<option value="">Todas</option>
											</select>
										</div>

										<div class="col-sm-4 form-group">
											<label for="grupo">Grupo</label>
											<select class="form-control" name="grupo" id="grupo">
												<option value="">Todas</option>
											</select>
										</div>
										
										<div class="col-sm-4 form-group">
											<label for="dispositivo">Dispositivo</label>
											<select class="form-control" name="dispositivo" id="dispositivo" required>
												<option value="">Seleccione uno</option>
											</select>
										</div>

									<?php } ?>


										


								</div>

								<div class="hr-line-dashed"></div>

								<div class="form-group row">
									<div class="col-sm-12">
										<button class="btn btn-primary" type="button" id="btnBuscar"> <i class="fa fa-search"></i> Buscar</button>
									</div>
								</div>

							</form>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>