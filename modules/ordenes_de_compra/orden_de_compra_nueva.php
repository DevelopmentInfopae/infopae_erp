<?php
	include '../../header.php';

	if ($permisos['orden_compra'] == "0") {
    	?><script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	<?php exit(); }

	include '../../db/conexion.php';
	set_time_limit (0);
	ini_set('memory_limit','6000M');
	$periodoActual = $_SESSION['periodoActual'];

	$nomMeses = [
      "01" => "Enero",
      "02" => "Febrero",
      "03" => "Marzo",
      "04" => "Abril",
      "05" => "Mayo",
      "06" => "Junio",
      "07" => "Julio",
      "08" => "Agosto",
      "09" => "Septiembre",
      "10" => "Octobre",
      "11" => "Novienmbre",
      "12" => "Diciembre"
	]
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2>Nueva Orden de Compra</h2>
		<ol class="breadcrumb">
			<li> <a href="<?php echo $baseUrl; ?>">Inicio</a> </li>
			<li class="active"> <strong>Nueva Orden de Compra</strong> </li>
		</ol>
	</div>
	<div class="col-lg-4">
		<div class="title-action">
			<a href="#" onclick="generarDespacho()" target="_self" class="btn btn-primary"><i class="fa fa-truck"></i> Generar orden de compra </a>
		</div>
	</div>	
</div>
<?php if ($_SESSION['perfil'] == "0" || $permisos['orden_compra'] == "2"): ?>
	
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<form class="col-lg-12" action="despachos.php" name="formDespachos" id="formDespachos" method="post">
						<div class="row">

							<div class="col-sm-6 col-md-3 form-group">
								<label for="mes">Mes</label>
								<select class="form-control" name="mes" id="mes">
									<option value="">Seleccione una</option>
									<?php
										$consulta = " SELECT DISTINCT MES from planilla_semanas ";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
											while($row = $resultado->fetch_assoc()) { ?>
												<option value="<?php echo $row["MES"]; ?>" <?php  if (isset($_POST['mes']) && ($_POST['mes'] == $row["MES"]) ) { echo ' selected '; } ?>   ><?php echo $nomMeses[$row['MES']] ?></option>
									<?php
											}
										}
									?>
								</select>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="semana">Semana</label>
								<select class="form-control" name="semana" id="semana">
									<option value="">Seleccione una</option>
									<?php
									$consulta = " SELECT DISTINCT SEMANA FROM planilla_semanas ";
									$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
									if($resultado->num_rows >= 1){
										while($row = $resultado->fetch_assoc()) { ?>
											<option value="<?php echo $row["SEMANA"]; ?>" <?php  if (isset($_POST['semana']) && ($_POST['semana'] == $row["SEMANA"]) ) { echo ' selected '; } ?>   ><?php echo $row["SEMANA"]; ?></option>
											<?php
										}
									}
									?>
								</select>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="dias">Días</label>
								<!-- Planilla semanas -->
								<div id="dias">
								</div>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="tipoRacion">Tipo Ración</label>
								<select class="form-control" name="tipoRacion" id="tipoRacion">
									<option value="">Seleccione una</option>
									<?php
									$consulta = " SELECT DISTINCT CODIGO FROM tipo_complemento ";
									$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
									if($resultado->num_rows >= 1){
										while($row = $resultado->fetch_assoc()) { ?>
											<option value="<?php echo $row["CODIGO"]; ?>" <?php  if (isset($_POST['tipoRacion']) && ($_POST['tipoRacion'] == $row["CODIGO"]) ) { echo ' selected '; } ?>   ><?php echo $row["CODIGO"]; ?></option>
											<?php
										}// Termina el while
									}//Termina el if que valida que si existan resultados
									?>
								</select>
							</div>

						</div> <!-- row -->	
						<div class="row">

							<div class="col-sm-6 col-md-3 form-group">
								<label for="tipoDespacho">Tipo de Alimento</label>
								<select class="form-control" name="tipoDespacho" id="tipoDespacho">
									<option value="">Seleccione una</option>
									<?php
										$consulta = " SELECT * FROM tipo_despacho WHERE 1=1 ORDER BY Descripcion ASC ";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
											while($row = $resultado->fetch_assoc()) { ?>
												<option value="<?php echo $row["Id"]; ?>" ><?php echo $row["Descripcion"]; ?></option>
									<?php
											}// Termina el while
										}//Termina el if que valida que si existan resultados
									?>
								</select>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="subtipo">Proveedor</label>
								<select class="form-control" name="proveedorEmpleado" id="proveedorEmpleado" required>
									<option value="">Seleccione uno</option>
								</select>
								<input type="hidden" id="proveedorEmpleadoNm" name="proveedorEmpleadoNm" value="">
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="ruta">Buscar Sedes x Ruta</label>
								<select class="form-control select2" name="ruta" id="ruta">
									<option value="">Seleccione una</option>
									<?php
										$consulta = " SELECT * FROM rutas ORDER BY nombre ASC ";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
											while($row = $resultado->fetch_assoc()) { ?>
												<option value="<?php echo $row["ID"]; ?>" ><?php echo $row["Nombre"]; ?></option>
									<?php
											}// Termina el while
										}//Termina el if que valida que si existan resultados
									?>
								</select>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="municipio">Municipio</label>
								<select class="form-control" name="municipio" id="municipio">
									<option value="">Seleccione uno</option>
								</select>
							</div>

						</div> <!-- row -->	
						<div class="row">

							<div class="col-sm-6 col-md-3 form-group">
								<label for="institucion">Institución</label>
								<!-- Tipo Complemento - Codigo -->
								<select class="form-control select2" name="institucion" id="institucion">
									<option value="">Todos</option>
								</select>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="sede">Sede</label>
								<!-- Tipo Complemento - Codigo -->
								<select class="form-control select2" name="sede" id="sede">
									<option value="">Todos</option>
								</select>
							</div>
						</div><!-- -/.row -->

						<div class="row">
							<div class="col-sm-3 form-group">
								<button type="button" id="btnAgregar" class="botonParametro btn btn-primary">+</button>
								<button type="button" id="btnQuitar" class="botonParametro btn btn-primary">-</button>
							</div><!-- /.col -->
						</div><!-- -/.row -->

						<hr>
						<div class="row">
							<div class="col-sm-12">
								<div class="table-responsive">
									<table width="100%" id="box-table-a" class="table table-striped table-bordered table-hover selectableRows" >
										<thead>
											<tr>
												<th class="col-sm-1 text-center">
													<input type="checkbox" class="i-checks" name="selectVarios" id="selectVarios" value="">
												</th>
												<th>Municipio</th>
												<th>Institución</th>
												<th>Sede</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
											</tr>
										</tbody>
									</table>
								</div><!-- /.table-responsive -->
							</div>
						</div>
						<hr>
					</form>
					<div class="listadoFondo">
						<div class="listadoContenedor">
							<div class="listadoCuerpo">
							</div><!-- /.listadoCuerpo -->
						</div><!-- /.listadoContenedor -->
					</div><!-- /.listadoFondo -->
				</div><!-- /.ibox-content -->
			</div><!-- /.ibox float-e-margins -->
		</div><!-- /.col-lg-12 -->
	</div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<?php else: ?>
	<script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php endif ?>

<?php include '../../footer.php'; ?>
<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/ordenes_de_compra/js/orden_de_compra_nueva.js"></script>

<?php mysqli_close($Link); ?>

</body>
</html>