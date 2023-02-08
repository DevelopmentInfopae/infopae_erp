<?php
  	include '../../header.php';
	if ($permisos['despachos'] == "0") {
?>		<script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    	</script>
<?php 
		exit(); 
	}
	else {
		?><script type="text/javascript">
		  const list = document.querySelector(".li_despachos");
		  list.className += " active ";
		</script>
	  <?php
	  }

  	include '../../db/conexion.php';
  	set_time_limit (0);
  	ini_set('memory_limit','6000M');
  	$periodoActual = $_SESSION['periodoActual'];
  	$mesesNombre = [	"01" => "ENERO", 
  							"02" => "FEBRERO", 
							"03" => "MARZO", 
							"04" => "ABRIL", 
							"05" => "MAYO", 
							"06" => "JUNIO", 
							"07" => "JULIO", 
							"08" => "AGOSTO", 
							"09" => "SEPTIEMBRE", 
							"10" => "OCTUBRE", 
							"11" => "NOVIEMBRE", 
							"12" => "DICIEMBRE" ];
  	$consultaMeses = " SELECT DISTINCT(MES) AS mes FROM planilla_semanas ";
  	$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses ' . mysqli_error($Link));
  	if ($respuestaMeses->num_rows > 0) {
      while ($dataMeses = $respuestaMeses->fetch_assoc()) {
        	$mesesNm[$dataMeses['mes']] = $mesesNombre[$dataMeses['mes']];
      }
  	}

	$nameLabel = get_titles('despachos', 'alimentos', $labels);
	$titulo = $nameLabel .' - Nuevo';
?>

<?php if ($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"): ?>
	<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  		<div class="col-lg-8">
      	<h2><?= $titulo ?></h2>
      	<ol class="breadcrumb">
          	<li>
              	<a href="<?php echo $baseUrl; ?>">Inicio</a>
          	</li>
			<li>
              	<a href="<?php echo $baseUrl; ?>/modules/despachos/despachos.php"><?= $nameLabel ?></a>
          	</li>
          	<li class="active">
              	<strong><?= $titulo ?></strong>
          	</li>
      	</ol>
  		</div>
  		<div class="col-lg-4">
      	<div class="title-action">
          	<a href="#" onclick="generarDespacho()" target="_self" class="btn btn-primary"><i class="fa fa-truck"></i> Generar despachos </a>
      	</div>
  		</div>
	</div>

	<div class="wrapper wrapper-content animated fadeInRight">
  		<div class="row">
    		<div class="col-lg-12">
      		<div class="ibox float-e-margins">
        			<div class="ibox-content contentBackground">
          			<form class="col-lg-12" action="despachos.php" name="formDespachos" id="formDespachos" method="post">
            			<div class="row">

              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="subtipo">Tipo de despacho</label>
                				<select class="form-control select2" name="subtipo" id="subtipo">
                  				<option value="">Seleccione uno</option>
                  				<?php
                  					$consulta = " SELECT * FROM tipomovimiento WHERE Documento = 'DES' ";
                  					$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                  					if($resultado->num_rows >= 1){
                    						while($row = $resultado->fetch_assoc()) { ?>
                      						<option value="<?php echo $row["Id"]; ?>" <?php  if (isset($_POST['subtipo']) && ($_POST['subtipo'] == $row["Id"]) ) { echo ' selected '; } ?>   ><?php echo $row["Movimiento"]; ?></option>
                      			<?php
                    						}
                  					}
                  				?>
                				</select>
                				<input type="hidden" id="subtipoNm" name="subtipoNm" value="">
              				</div>

              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="subtipo">Proveedor / Empleado</label>
                				<select class="form-control select2" name="proveedorEmpleado" id="proveedorEmpleado">
                  				<option value="">Seleccione uno</option>
                				</select>
                				<input type="hidden" id="proveedorEmpleadoNm" name="proveedorEmpleadoNm" value="">
              				</div><!-- /.col -->

             	 			<div class="col-sm-4 col-md-3 form-group">
                				<label for="mes">Mes</label>
                				<select class="form-control select2" name="mes" id="mes">
                  				<option value="">Seleccione uno</option>
                  				<?php foreach ($mesesNm as $key => $value): ?>
                    					<option value="<?= $key ?>"><?= $value ?></option>
                  				<?php endforeach ?>
                				</select>
              				</div>  <!-- col -->

              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="subtipo">Semana</label>
                				<select class="form-control select2" name="semana" id="semana">
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
							</div><!-- row -->

							<div class="row">
              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="dias">Días</label>
                				<div id="dias"> </div>
              				</div><!-- /col -->

              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="tipoRacion">Tipo Ración</label>
                				<select class="form-control select2" name="tipoRacion" id="tipoRacion">
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
              				</div><!-- /.col -->

              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="tipoDespacho">Tipo Despacho</label>
                				<select class="form-control select2" name="tipoDespacho" id="tipoDespacho">
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
              				</div><!-- /.col -->

              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="municipio">Municipio</label>
                				<select class="form-control select2" name="municipio" id="municipio">
                					<option value="">Seleccione uno</option>
                				</select>
              				</div><!-- /.col -->
							</div><!--	row -->

							<div class="row">				
              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="institucion">Institución</label>
                				<select class="form-control select2" name="institucion" id="institucion">
                  				<option value="">Todos</option>
                				</select>
              				</div><!-- /.col -->

              				<div class="col-sm-4 col-md-3 form-group">
                				<label for="sede">Sede</label>
                				<select class="form-control select2" name="sede" id="sede">
                  				<option value="">Todos</option>
                				</select>
              				</div><!-- /.col -->

              				<div class="col-sm-4 col-md-3 form-group">
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
              				</div><!-- /.col -->
            			</div><!-- -/.row -->
            
							<div class="row">
              				<div class="col-sm-3 form-group">
                				<button type="button" id="btnAgregar" class="botonParametro btn btn-primary">+</button>
                				<button type="button" id="btnQuitar" class="botonParametro btn btn-primary">-</button>
              				</div><!-- /.col -->
            			</div><!-- -/.row -->
            			<hr>
            			<div class="row" id="rowTable" style="display: none;">
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
											  	<tbody id="bodyTable">

												</tbody>
                  				</table>
                				</div><!-- /.table-responsive -->
									<label id="mostrando" ></label>
              				</div>
            			</div>
            			<hr>
            			<div class="row">
              				<div class="col-sm-3 form-group">
                				<label for="bodegaOrigen">Bodega Origen</label>
                				<select class="form-control" name="bodegaOrigen" id="bodegaOrigen">
                  				<option value="">Seleccione una</option>
                				</select>
              				</div><!-- /.col -->
              				<div class="col-sm-3 form-group">
                				<label for="bodegaDestino">Bodega Destino</label>
                				<input type="text" name="bodegaDestino" id="bodegaDestino" value="Bodega asignada a sede" readonly="readonly" class="form-control">
              				</div><!-- /.col -->
            			</div><!-- -/.row -->

            			<div class="row">
              				<div class="col-sm-3 form-group">
                				<label for="tipoTransporte">Tipo Transporte</label>
                				<select class="form-control" name="tipoTransporte" id="tipoTransporte">
                  				<option value="">Seleccione uno</option>
                  				<?php
                  					$consulta = " SELECT * FROM tipovehiculo ORDER BY nombre ASC ";
                  					$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                  					if($resultado->num_rows >= 1){
                    						while($row = $resultado->fetch_assoc()) { ?>
                      						<option value="<?php echo $row["Id"]; ?>" <?php  if (isset($_POST['tipoTransporte']) && ($_POST['tipoTransporte'] == $row["Id"]) ) { echo ' selected '; } ?>   ><?php echo $row["Nombre"]; ?></option>
                  				<?php
                    						}// Termina el while
                  					}//Termina el if que valida que si existan resultados
                  				?>
                				</select>
              				</div><!-- /.col -->
              
								<div class="col-sm-3 form-group">
                				<label for="placa">Placa</label>
                				<input type="text" name="placa" id="placa" value="" class="form-control">
              				</div><!-- /.col -->
              	
								<div class="col-sm-3 form-group">
                				<label for="conductor">Nombre Conductor</label>
                				<input type="text" name="conductor" id="conductor" value="" class="form-control">
              				</div><!-- /.col -->
            			</div><!-- -/.row -->
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
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/modules/despachos/js/despacho_nuevo.js"></script>

<?php mysqli_close($Link); ?>

</body>
</html>