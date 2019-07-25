<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$preparacionOriginal = "";

$mes = '';
$semana = '';
$tipoComplemento = '';
$grupoEtario = '';

if(isset($_POST['mes']) && $_POST['mes'] != ''){
	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}

if(isset($_POST['semana']) && $_POST['semana'] != ''){
	$semana = mysqli_real_escape_string($Link, $_POST['semana']);
}

if(isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != ''){
	$tipoComplemento = mysqli_real_escape_string($Link, $_POST['tipoComplemento']);
}

if(isset($_POST['grupoEtario']) && $_POST['grupoEtario'] != ''){
	$grupoEtario = mysqli_real_escape_string($Link, $_POST['grupoEtario']);
}

//var_dump($_POST);


$consulta = " SELECT ps.DIA,  p.Id, p.Codigo, p.Descripcion FROM planilla_semanas ps LEFT JOIN productos19 p ON ps.MENU = p.Orden_Ciclo WHERE ps.MES = \"$mes\" AND ps.SEMANA = \"$semana\" AND p.Cod_Tipo_complemento = \"$tipoComplemento\" AND p.Cod_Grupo_Etario = \"$grupoEtario\" AND p.Codigo LIKE \"01%\" AND p.Nivel = 3 ORDER BY DIA asc ";

//echo $consulta;

$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	$cntFTD = 0;  
	?>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-sm-12">
				<div class="ibox">
					<div class="ibox-title">
						<h5>Programación de menús para la semana <?= $semana ?> Original</h5> 
					</div>
					<div class="ibox-content">
						<table class="table">
							<tbody>
								<?php while ($menuDia = $resultado->fetch_assoc()) { 
									$cntFTD++;
									?>
									<tr>
										<th>Día <?= $menuDia['DIA'] ?></th>
									</tr>
									<tr>
										<td>					
											<input type="text" class="form-control" name="" id=""  value="<?= $menuDia['Descripcion'] ?>" readonly>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php
$consulta = " SELECT ps.DIA, ps.MENU,  p.Id, p.Codigo, p.Descripcion FROM planilla_semanas ps LEFT JOIN productos19 p ON ps.MENU = p.Orden_Ciclo WHERE ps.MES = \"$mes\" AND ps.SEMANA = \"$semana\" AND p.Cod_Tipo_complemento = \"$tipoComplemento\" AND p.Cod_Grupo_Etario = \"$grupoEtario\" AND p.Codigo LIKE \"01%\" AND p.Nivel = 3 ORDER BY DIA asc ";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	$cntFTD = 0;  
	?>
	<div class="wrapper wrapper-content  animated fadeInRight">
		<div class="row">
			<div class="col-sm-12">
				<div class="ibox">
					<div class="ibox-title">
						<h5>Nueva Programación de menús para la semana <?= $semana ?></h5> 
					</div>
					<div class="ibox-content">
						<table class="table">
							<tbody>
								<?php while ($menuDia = $resultado->fetch_assoc()) { 
									$cntFTD++;
									?>
									<tr>
										<th>Día <?= $menuDia['DIA'] ?></th>
									</tr>
									<tr>
										<td>
											<select class="form-control menuDia" name="menuDia[<?php echo $cntFTD ?>]" id="menuDia<?php echo $cntFTD ?>" dia="<?= $menuDia['DIA'] ?>" menu="<?= $menuDia['MENU'] ?>" required>
												
												<!-- <option value="<?php //= $menuDia['Codigo'] ?>"><?php //= $menuDia['Descripcion'] ?></option> -->

										  	</select>
										</td>
									</tr>
								<?php } ?>
							</tbody>
						</table>




					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row">
				<div class="col-sm-12">
					<div class="ibox">
						<div class="ibox-content">
							<div class="row">
								<div class="col-sm-12">				
									<div class="row">


										<div class="col-sm-6 form-group">
											<label for="departamento">Fecha de vencimiento</label>
											<div class="input-group date">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input name="fechaVencimiento>" id="fechaVencimiento" type="text" class="form-control inputFechaVencimiento datepick" value="" required>
											</div>
										</div>


										<div class="col-sm-6 form-group">
											<label for="departamento">Archivo</label>
											<div class="fileinput fileinput-new input-group" data-provides="fileinput"> 
												<div class="form-control" data-trigger="fileinput">
													<i class="glyphicon glyphicon-file fileinput-exists"></i> 
													<span class="fileinput-filename"></span>
												</div> 
												<span class="input-group-addon btn btn-default btn-file">
													<span class="fileinput-new">Elegir archivo</span>
													<span class="fileinput-exists">Change</span>
													<input type="file" name="foto" id="foto" accept="image/jpeg,image/gif,image/png,application/pdf">
												</span> 
												<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a> 
											</div>
										</div>

										<div class="col-sm-12 form-group">
											<label for="observaciones">Observaciones</label>
											<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80"></textarea>
										</div>

									</div>
									<div class="hr-line-dashed"></div>
									<div class="form-group row">
										<div class="col-sm-12">
											<button class="btn btn-primary btnGuardar" type="button">Guardar</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>












<?php } ?>