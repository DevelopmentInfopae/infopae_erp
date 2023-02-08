<?php
	require_once '../../header.php';

	if ($permisos['instituciones'] == "0" || $permisos['instituciones'] == "1") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  	<?php exit(); }
	  else {
    ?><script type="text/javascript">
      const list = document.querySelector(".li_sedes");
      list.className += " active ";
      </script>
    <?php
  }

	
	$periodoActual = $_SESSION["periodoActual"];
	$indicadorDepartamento = $_SESSION['p_CodDepartamento'];
	$codigoSede = (isset($_POST["codigoSede"]) && $_POST["codigoSede"] != "") ? mysqli_real_escape_string($Link, $_POST["codigoSede"]) : "";

	$consultaSede = "SELECT * FROM sedes$periodoActual WHERE	cod_sede = '$codigoSede'";
	$resultadoSede = $Link->query($consultaSede);
	if ($resultadoSede->num_rows > 0){
		$registrosSede = $resultadoSede->fetch_assoc();
	}

	$nameLabel = get_titles('sedes', 'sedes', $labels);
	$titulo = "Editar ".$nameLabel ;
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-8">
    	<h2><?php echo $nameLabel; ?></h2>
    	<ol class="breadcrumb">
      		<li>
        		<a href="<?php echo $baseUrl; ?>">Home</a>
      		</li>
      		<li class="active">
      			<a href="<?php echo $baseUrl . '/modules/instituciones/sedes.php'; ?>"><?= $nameLabel ?></a>
      		</li>
      		<li class="active">
        		<strong><?php echo $titulo; ?></strong>
      		</li>
    	</ol>
  	</div><!-- /.col -->
  	<div class="col-lg-4">
    	<div class="title-action">
      		<a class="btn btn-primary" onclick="actualizarSede(false);"><i class="fa fa-check "></i> Guardar </a>
    	</div><!-- /.title-action -->
  	</div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  	<div class="row">
    	<div class="col-lg-12">
      		<div class="ibox float-e-margins">
        		<div class="ibox-content contentBackground">
          			<form id="formActualizarSede" action="" method="post">
          				<div class="row">
          					<div class="col-sm-3 col-lg-2 text-center">
        						<div class="form-group">
									<div class="fileinput fileinput-new" data-provides="fileinput">
									  	<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px; padding: 0px;">
									  		<img class="img-responsive" alt="">
									  		<img class="img-responsive" <?php if ($registrosSede['url_foto'] != "") { ?> src="<?php echo $registrosSede['url_foto']; ?>" <?php } ?> alt="">
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
        					<div class="col-sm-9 col-lg-10">
        						<div class="row">

        							<?php if ($_SESSION['p_Municipio'] == '0'): ?>
        								<div class="form-group col-sm-3">
        									<label for="zonaPae">Zona Pae</label>
        									<input type="tel" class="form-control" name="zonaPae" id="zonaPae" value="<?php echo $registrosSede["Zona_Pae"]; ?>">
        								</div>
        							<?php endif ?>

        							<div class="form-group col-sm-3">
                    					<label for="municipio">Municipio</label>
                    					<select class="form-control" name="municipio" id="municipio" required>
                      						<option value="">Seleccione uno</option>
                      						<?php
                        						$consulta = " SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE 1=1 ";

                        						$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
                        						if($DepartamentoOperador != ''){
                          							$consulta = $consulta." AND CodigoDANE LIKE '$DepartamentoOperador%' ";
                        						}
                        						$consulta = $consulta." ORDER BY ciudad ASC ";
                        						$resultado = $Link->query($consulta);
                        						if($resultado->num_rows > 0){
                          							while($row = $resultado->fetch_assoc()) {
                            							$selected = (isset($registrosSede["cod_mun_sede"]) && $registrosSede["cod_mun_sede"] == $row["codigoDANE"] ) ? " selected " : "";
                            							echo '<option value="' . $row["codigoDANE"] . '" ' . $selected . '>
                                    					' . $row["ciudad"] .
                                  						'</option>';
                          							}
                        						}
                      						?>
                    					</select>
                  					</div>

                  					<div class="form-group col-sm-3">
                    					<label for="institucion">Institución</label>
                    					<select class="form-control institucion"  name="institucion"  required="">
                      						<option value="">Seleccione uno</option>
                      						<?php
                        						$consultaInstituciones = "SELECT codigo_inst, nom_inst FROM instituciones WHERE cod_mun = '". $registrosSede["cod_mun_sede"] ."' ORDER BY nom_inst ASC ";
                        						$resultadoInstituciones = $Link->query($consultaInstituciones);
                        						if($resultadoInstituciones->num_rows > 0){
                          							while($registrosInstituciones = $resultadoInstituciones->fetch_assoc()) {
                    						?>
		                        			<option value="<?php echo $registrosInstituciones["codigo_inst"]; ?>" <?php if(isset($registrosSede["cod_inst"]) && $registrosSede["cod_inst"] == $registrosInstituciones["codigo_inst"]) { echo 'selected'; } ?>>
		                           
		                           			<?php echo $registrosInstituciones["nom_inst"]; ?>
		                        			</option>
                      						<?php
                          							}
                        						}
                      						?>
                    					</select>
                  					</div>

                  					<div class="form-group col-sm-3">
		                				<label for="codigo">Código Sede</label>
		                				<input type="number" class="form-control" name="codigo" id="codigo" min="0" required step="1" value="<?php echo $registrosSede["cod_sede"]; ?>" required>
		                				<input type="hidden" name="id" id="id" value="<?php echo $registrosSede["id"]; ?>">
		              				</div>

		              				<div class="form-group col-sm-3">
		                				<label for="nombre">Nombre Sede</label>
		                				<input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $registrosSede["nom_sede"]; ?>" required>
		              				</div>

        							<div class="form-group col-sm-3">
		                				<label for="direccion">Dirección</label>
		                				<input type="text" class="form-control" name="direccion" id="direccion" value="<?php echo $registrosSede["direccion"]; ?>">
		              				</div>

			              			<div class="form-group col-sm-3">
			                			<label for="telefono">Teléfono</label>
			                			<input type="tel" class="form-control" name="telefono" id="telefono" value="<?php echo $registrosSede["telefonos"]; ?>">
			              			</div>

			              			<div class="form-group col-sm-3">
			                			<label for="email">Email</label>
			                			<input type="email" class="form-control" name="email" id="email" value="<?php echo $registrosSede["email"]; ?>" required>
			              			</div>

		        					<div class="form-group col-sm-3">
			                			<label for="coordinador">Coordinador </label>
			                			<select class="form-control" name="coordinador" id="coordinador" required>
			                				<option value="">Seleccione uno</option>
			                				<?php
			                    				$codigoCiudad = $_SESSION['p_CodDepartamento'];
			                    				$consulta1= " SELECT num_doc AS numeroDocumento, nombre AS nombreCoordinador FROM usuarios WHERE id_perfil = '7' AND cod_mun LIKE '$codigoCiudad%' ORDER BY nombre ASC;";
			                    				$result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
			                    				if($result1){
			                      					while($row1 = $result1->fetch_assoc()){
			                  				?>
			                        		<option value="<?php echo $row1['numeroDocumento']; ?>" <?php if(isset($registrosSede["id_coordinador"]) && $registrosSede["id_coordinador"] == $row1["numeroDocumento"]){ echo "selected"; } ?>>
			                          		<?php echo $row1['nombreCoordinador']; ?>
			                     			</option>
			                  				<?php
			                      					}
			                    				}
			                  				?>
			                			</select>
			              			</div>

	        						<div class="form-group col-sm-3">
		                				<label for="jornada">Jornada </label>
		                				<select class="form-control" name="jornada" id="jornada">
		                					<option value="">Seleccione uno</option>
		                					<?php
		                    					$consulta1= "SELECT id AS idJornada, nombre AS nombreJornada FROM jornada";
		                    					$result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
		                    					if($result1){
		                      						while($row1 = $result1->fetch_assoc()){
		                  					?>
		                        			<option value="<?php echo $row1['idJornada']; ?>" <?php if(isset($registrosSede["jornada"]) && $registrosSede["jornada"] == $row1["idJornada"]){ echo "selected"; } ?>>
		                          			<?php echo $row1['nombreJornada']; ?>
		                        			</option>
		                  					<?php
		                      						}
		                    					}
		                  					?>
		                				</select>
		              				</div>

		              				<div class="form-group col-sm-3">
		                				<label for="complemento">Tipo complemento </label>
		                				<select class="form-control" name="complemento" id="complemento">
		                					<option value="">Seleccione uno</option>
		                					<?php
		                    					$consulta1= "SELECT CODIGO AS codigoTipoComplemento, ID AS idTipoComplemento, DESCRIPCION AS descripcionTipoComplemento FROM tipo_complemento WHERE jornada = '". $registrosSede["jornada"] ."'";
		                    					$result1 = $Link->query($consulta1);
		                    					if($result1){
		                      						while($row1 = $result1->fetch_assoc()){
		                  					?>
		                        			<option value="<?php echo $row1['codigoTipoComplemento']; ?>" <?php if(isset($registrosSede["Tipo_Complemento"]) && $registrosSede["Tipo_Complemento"] == $row1["codigoTipoComplemento"]){ echo "selected"; } ?>>
		                          			<?php echo $row1['descripcionTipoComplemento']; ?>
		                        			</option>
		                  					<?php
		                      						}
		                    					}
		                  					?>
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
		                        			<option value="<?php echo $row1['idVariacion']; ?>" <?php if(isset($registrosSede["cod_variacion_menu"]) && $registrosSede["cod_variacion_menu"] == $row1["idVariacion"]){ echo "selected"; } ?>>
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
	        							<input type="number" class="form-control" name="manipuladora" id="manipuladora" min="0" max="7" value="<?php echo $registrosSede['cantidad_Manipuladora']; ?>">
	        						</div>

		              				<div class="form-group col-lg-3">
				                		<label for="sector">Sector</label>
				                			<div class="radio">
												<div>			  <!-- <label> -->
												    <input type="radio" name="sector" id="sector1" value="1" required <?php if(isset($registrosSede["sector"]) && $registrosSede["sector"] == "1"){ echo "checked"; } ?>>
												    Rural
												    <br>
												</div>
												  <!-- </label> -->
												  <!-- <label> -->
												<div>			  	
												    <input type="radio" name="sector" id="sector2" value="2" required <?php if(isset($registrosSede["sector"]) && $registrosSede["sector"] == "2"){ echo "checked"; } ?>>
												    Urbano
												  <!-- </label> -->
												</div>			  
											</div>
										<label for="sector" class="error" style="display: none;"></label>
	        						</div>

		              				<div class="form-group col-lg-3">
			                			<label for="validacion">Tipo validación</label>
			                				<div class="radio">
			                					<div>
											  	<!-- <label> -->
											    	<input type="radio" name="validacion" id="validacion1" value="Planilla" required <?php if(isset($registrosSede["tipo_validacion"]) && $registrosSede["tipo_validacion"] == "Planilla"){ echo "checked"; } ?>>
											    	Planilla
											    	<br>
												</div>			    
											  <!-- </label> -->
											  <!-- <label> -->
												<div>			  	
											    	<input type="radio" name="validacion" id="validacion2" value="Lector Biométrico" required <?php if(isset($registrosSede["tipo_validacion"]) && $registrosSede["tipo_validacion"] == "Lector Biométrico"){ echo "checked"; } ?>>
											    	Lector Biométrico
											  <!-- </label> -->
												</div>			  
											</div>
										<label for="validacion" class="error" style="display: none;"></label>
	        						</div>


		              				<div class="form-group col-lg-3">
			                			<label for="estado">Estado</label>
			                				<div class="radio">
			                					<div>
											  	<!-- <label> -->
											    	<input type="radio" name="estado" id="estado1" value="1" required <?php if(isset($registrosSede["estado"]) && $registrosSede["estado"] == "1"){ echo "checked"; } ?>>
											    	Activo
											    	<br>
											    <!-- </label>	 -->
												</div>			    
											  	
												<div>			  
											  	<!-- <label> -->
											    	<input type="radio" name="estado" id="estado2" value="0" required <?php if(isset($registrosSede["estado"]) && $registrosSede["estado"] == "0"){ echo "checked"; } ?>>
											    	Inactivo
											  <!-- </label> -->
												</div>			  
											</div>
										<label for="estado" class="error" style="display: none;"></label>
	        						</div>
        						</div>
      						</div>
      					</div>
        				<!-- </div> -->
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
<!-- <script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/iCheck.min.js"></script> -->
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