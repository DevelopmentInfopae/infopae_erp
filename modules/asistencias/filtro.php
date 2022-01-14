<?php 
$consultaMes = " SELECT DISTINCT(MES) AS mes FROM planilla_semanas ";
$respuestaMes = $Link->query($consultaMes) or die ('Error al consultar los meses ' . mysqli_error($Link));
if ($respuestaMes->num_rows > 0) {
	while ($dataMes = $respuestaMes->fetch_assoc()) {
		$meses[] = $dataMes['mes'];
	}
}
$nomMeses = [ "01" => "ENERO", "02" => "FEBRERO", "03" => "MARZO", "04" => "ABRIL", "05" => "MAYO", "06" => "JUNIO", "07" => "JULIO", "08" => "AGOSTO", "09" => "SEPTIEMBRE", "10" => "OCTUBRE", "11" => "NOVIEMBRE", "12" => "DICIEMBRE" ];
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
					<div class="row">
						<div class="col-sm-12">
							<form action="" id="form_asistencia" name="form_asistencia" method="post">
								<div class="row">
									<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0 || $_SESSION["perfil"] == 5 || $_SESSION["perfil"] == 6 || $_SESSION["perfil"] == 3 || $_SESSION['perfil'] == 7) { ?>
										<div class="col-sm-4 form-group">
											<label for="mes">Mes</label>
											<select class="form-control" name="mes" id="mes" required>
												<option value="">Seleccione uno</option>
												<?php foreach ($meses as $key => $value): ?>
													<option value="<?= $value; ?>"> <?= $nomMeses[$value]; ?> </option>									
												<?php endforeach ?>								
											</select>
										</div>
										<div class="col-sm-4 form-group">
											<label for="semana">Semana</label>
											<select class="form-control" name="semana" id="semana" required>
												<option value="">Seleccione una</option>	   								
											</select>
										</div>
										<div class="col-sm-4 form-group">
											<label for="dia">Día</label>
											<select class="form-control" name="dia" id="dia" <?php if(!isset($diaNoObligatorio) || $diaNoObligatorio != 1){ ?>required<?php } ?>>
												<option value="">Seleccione uno</option>									
											</select>
										</div>
									<?php } ?>
									<div class="col-sm-4 form-group">
										<label for="municipio">Municipio</label>
										<select class="form-control" name="municipio" id="municipio" required>
											<option value="">Seleccione uno</option>									
										</select>
									</div>
									<div class="col-sm-4 form-group">
										<label for="institucion">Institución</label>
										<select class="form-control select2" name="institucion" id="institucion" required>
											<option value="">Seleccione una</option>									
										</select>
									</div>
									<div class="col-sm-4 form-group">
										<label for="sede">Sede</label>
										<select class="form-control select2" name="sede" id="sede" required>
											<option value="">Seleccione una</option>
										</select>
									</div>  
									<?php if($_SESSION["perfil"] != 5 && $_SESSION["perfil"] != 6 && $_SESSION['perfil'] != 7){ ?>
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
									<?php } ?>
									<div class="col-sm-4 form-group">
										<label for="complemento">Complemento</label>
										<select class="form-control" name="complemento" id="complemento" required>
											<option value="">Seleccionar</option>
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