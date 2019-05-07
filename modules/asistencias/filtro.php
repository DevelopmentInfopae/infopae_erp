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
					<div class="row">
						<div class="col-sm-12">

							<form action="" id="form_asistencia" name="form_asistencia" method="post">
								<div class="row">

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