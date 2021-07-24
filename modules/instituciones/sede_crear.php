<?php
	require_once '../../header.php';
	$titulo = "Nueva sede";

	if ($permisos['instituciones'] == "0" || $permisos['instituciones'] == "1") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  	<?php exit(); }

	$codigoInstitucion = (isset($_POST["codigoInstitucion"]) && $_POST["codigoInstitucion"] != "") ? mysqli_real_escape_string($Link, $_POST["codigoInstitucion"]) : "";
	if($codigoInstitucion != ""){
		$consultaInstitucion = "SELECT * FROM instituciones WHERE codigo_inst = '$codigoInstitucion' AND cod_mun = '". $municipio_defecto["CodMunicipio"] ."'";
		$resultadoInstitucion = $Link->query($consultaInstitucion);
		if ($resultadoInstitucion->num_rows > 0){
			$registrosIntitucion = $resultadoInstitucion->fetch_assoc();
		}
	}
	// exit(var_dump($_POST));
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
      	<a href="<?php echo $baseUrl . '/modules/instituciones/sedes.php'; ?>">Sedes</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <a class="btn btn-primary" onclick="guardarSede(false);"><i class="fa fa-check "></i> Guardar </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  	<div class="row">
    	<div class="col-lg-12">
      		<div class="ibox float-e-margins">
        		<div class="ibox-content contentBackground">
          			<form id="formCrearSede" action="" method="post">
          				<div class="row">
			          		<div class=" col-lg-2 col-md-3 col-sm-8 text-center">
			        			<div class="form-group">
									<div class="fileinput fileinput-new" data-provides="fileinput">
									  <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px; padding: 0px;">
									  	<img class="img-responsive" alt="">
									  </div>
									  <div class="text-center">
									    <span class="btn btn-default btn-file"><span class="fileinput-new">seleccionar</span>
									    <span class="fileinput-exists">Cambiar</span>
									    <input type="file" name="imagen" id="imagen" accept="image/jpg, image/jpeg, image/png"></span>
									    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
									  </div>
									</div>
								</div>
        					</div>
        					<div class="col-lg-10 col-md-9 col-sm-8">
        						<div class="row">
        							<?php if ($_SESSION['p_Municipio'] == "0"): ?>
        								<div class="form-group col-lg-3 col-md-4 col-sm-6">
											<label for="zonaPae"> Zona Pae </label>
											<input class="form-control" type="text" name="zonaPae" id="zonaPae" required="">
										</div>
        							<?php endif ?>

        							<div class="form-group col-lg-3 col-md-4 col-sm-4">
                    					<label for="municipio">Municipio</label>
                    					<select class="form-control" name="municipio" id="municipio" required>
					                      <option value="">Seleccione uno</option>
					                      <?php
					                        $consulta = " SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE 1=1 ";
					                        $municipio = $_SESSION['p_Municipio'];
					                        $DepartamentoOperador = $_SESSION['p_CodDepartamento'];
					                        if ($municipio != 0) {
					                        	$condicion = "AND CodigoDANE = " .$municipio. "";
					                        }
					                        else {
					                        	$condicion = "AND CodigoDANE LIKE '$DepartamentoOperador%' "; 
					                        }
				                        	$consulta .= $condicion ." ORDER BY ciudad ASC ";
					                        $resultado = $Link->query($consulta);
					                        if($resultado->num_rows > 0){
					                          while($row = $resultado->fetch_assoc()) {
					                            $selected = (isset($registrosIntitucion["cod_mun"]) && $registrosIntitucion["cod_mun"] == $row["codigoDANE"] || $municipio_defecto["CodMunicipio"] == $row["codigoDANE"]) ? " selected " : "";
					                            echo '<option value="' . $row["codigoDANE"] . '" ' . $selected . '>
					                                    ' . $row["ciudad"] .
					                                  '</option>';
					                          }
					                        }
					                      ?>
					                    </select>
                  					</div>

                  					<div class="form-group col-lg-3 col-md-4 col-sm-4">
					                    <label for="institucion">Institución</label>
					                    <select class="form-control" name="institucion" id="institucion" required="">
					                      	<option value="">Seleccione uno</option>
					                      	
					                    </select>
                  					</div>

					                <div class="form-group col-lg-3 col-md-4 col-sm-4">
							            <label for="codigo">Código Sede</label>
							            <input type="number" class="form-control" name="codigo" id="codigo" min="0" required step="1">
							        </div>

						            <div class="form-group col-lg-3 col-md-4 col-sm-4">
						                <label for="nombre">Nombre Sede</label>
						                <input type="text" class="form-control" name="nombre" id="nombre" required>
						            </div>

						            <div class="form-group col-lg-3 col-md-4 col-sm-4">
		                				<label for="direccion">Dirección</label>
		                				<input type="text" class="form-control" name="direccion" id="direccion">
		              				</div>

		              				<div class="form-group col-lg-3 col-md-4 col-sm-4">
						                <label for="telefono">Teléfono</label>
						                <input type="tel" class="form-control" name="telefono" id="telefono">
						            </div>

						            <div class="form-group col-lg-3 col-md-4 col-sm-4">
						                <label for="email">Email</label>
						                <input type="email" class="form-control" name="email" id="email" required>
						            </div>

 									<div class="form-group col-lg-3 col-md-4 col-sm-4">
						                <label for="coordinador">Coordinador </label>
						                <select class="form-control" name="coordinador" id="coordinador" required>
						                	<option value="">Seleccione uno</option>
						                	<?php
						                    $codigoCiudad = $_SESSION['codCiudad'];
						                    $consulta1= " SELECT num_doc AS numeroDocumento, nombre AS nombreCoordinador FROM usuarios WHERE id_perfil = '7' AND cod_mun LIKE '$codigoCiudad%' ORDER BY nombre ASC;";
						                    $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
						                    if($result1){
						                      while($row1 = $result1->fetch_assoc()){
						                  ?>
						                        <option value="<?php echo $row1['numeroDocumento']; ?>">
						                          <?php echo $row1['nombreCoordinador']; ?>
						                        </option>
						                  <?php
						                      }
						                    }
						                  ?>
						                </select>
		              				</div>

		              				<div class="form-group col-lg-3 col-md-4 col-sm-4">
						                <label for="jornada">Jornada </label>
						                <select class="form-control" name="jornada" id="jornada">
						                	<option value="">Seleccione uno</option>
						                	<?php
						                    $consulta1= "SELECT id AS idJornada, nombre AS nombreJornada FROM jornada";
						                    $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
						                    if($result1){
						                      while($row1 = $result1->fetch_assoc()){
						                  ?>
						                        <option value="<?php echo $row1['idJornada']; ?>">
						                          <?php echo $row1['nombreJornada']; ?>
						                        </option>
						                  <?php
						                      }
						                    }
						                  ?>
						                </select>
		              				</div>

		              			<div class="form-group col-lg-3 col-md-4 col-sm-4">
					                <label for="complemento">Tipo complemento </label>
					                <select class="form-control" name="complemento" id="complemento">
					                	<option value="">Seleccione uno</option>
					                </select>
					           	</div>

        						<div class="form-group col-sm-3">
		                			<label for="variacion">Variación </label>
		                			<select class="form-control" name="variacion" id="variacion" required>
		                				<option value="">Seleccione uno</option>
					                	<?php
					                    $consulta1= "SELECT id AS idVariacion, descripcion AS descripcionVariacion FROM variacion_menu;";
					                    $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
					                    if($result1){
					                      while($row1 = $result1->fetch_assoc()){
					                  ?>
					                        <option value="<?php echo $row1['idVariacion']; ?>">
					                          <?php echo $row1['descripcionVariacion']; ?>
					                        </option>
					                  <?php
					                      }
					                    }
					                  ?>
		                			</select>
		              			</div>
		        				<div class="form-group col-sm-3">
		        					<label for="manipuladora">Manipuladoras</label>
		        					<input type="number" class="form-control" name="manipuladora" id="manipuladora" min="0" max="7">
		        				</div>

		        				<div class="form-group col-lg-3 col-md-4 col-sm-4">
					                <label for="sector">Sector</label>
					                <div class="radio">
										<div>			  <!-- <label> -->
										<input type="radio" name="sector" id="sector1" value="1" checked required>
											Rural
											<br>
										</div>			  <!-- </label> -->
										<div>			  <!-- <label> -->
										<input type="radio" name="sector" id="sector2" value="2" required>
											Urbano
										</div>			  <!-- </label> -->
									</div>
									<label for="sector" class="error" style="display: none;"></label>
		        				</div>

		        				<div class="form-group col-lg-3 col-md-4 col-sm-4">
			                		<label for="validacion">Tipo validación</label>
			                		<div class="radio">
										<div>
											<input type="radio" name="validacion" id="validacion1" value="Planilla" checked required> 
											Planilla
											<br>
										</div>
										<div>
	  	
											<input type="radio" name="validacion" id="validacion2" value="Lector Biométrico" required> 
											Lector Biométrico
										</div>
									</div>
									<label for="validacion" class="error" style="display: none;"></label>
	        					</div>
        					</div>
        				</div>
          			</div>
          			<div class="row">
		          		<div class="col-sm-3 col-lg-2 text-center">
		          			<a class="btn btn-primary" onclick="guardarSede(true);"><i class="fa fa-check "></i> Guardar y Continuar </a>
		          		</div>
          			</div>
          		</form>
        	</div>
      	</div>
    </div>
  </div>
</div>

<div class="modal inmodal fade" id="ventanaInformar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-check-square fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Aceptar</button>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/sede.js"></script>
<?php mysqli_close($Link); ?>

</body>
</html>