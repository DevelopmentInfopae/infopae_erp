<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Busqueda por fecha</h5>
					<div class="ibox-tools">
						<div class="collapse-link"> <i class="fa fa-chevron-down"></i> </div>
					</div>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<form action="" id="form_control_asistencia" name="form_control_asistencia" method="get">
								<input type="hidden" name="validacion" id="validacion" value="<?= $validacion; ?>">
								<div class="row">
									<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0  || $_SESSION["perfil"] == 3) { ?>
										<div class="col-sm-4 form-group">
											<label for="mes">Mes *</label>
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
											<label for="semana">Semana *</label>
											<select class="form-control" name="semana" id="semana" required>
												<option value="">Seleccione uno</option>									
											</select>
										</div>
										<div class="col-sm-4 form-group">
											<label for="dia">Día *</label>
											<select class="form-control" name="dia" id="dia" required>
												<option value="">Seleccione uno</option>									
											</select>
										</div>
									<?php } ?>
									<div class="col-sm-4 form-group">
										<label for="municipio">Municipio *</label>
										<select class="form-control" name="municipio" id="municipio" required>
											<option value="">Seleccione uno</option>									
										</select>
									</div>
									<div class="col-sm-4 form-group">
										<label for="institucion">Institución</label>
										<select class="form-control" name="institucion" id="institucion" >
											<option value="">Seleccione una</option>									
										</select>
									</div>
									<div class="col-sm-4 form-group">
										<label for="sede">Sede</label>
										<select class="form-control" name="sede" id="sede" >
											<option value="">Seleccione una</option>
										</select>
									</div>  
									<?php if(!isset($ventanaRepitentes) || $ventanaRepitentes == 0){ ?>
									<?php } ?>
								</div>
								<div class="hr-line-dashed"></div>
								<div class="form-group row">
									<div class="col-sm-12">
										<button class="btn btn-primary" type="button" id="btnBuscarControl"> <i class="fa fa-search"></i> Buscar</button>
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