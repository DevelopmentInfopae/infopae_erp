<?php
	include '../../header.php';

  	if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
?>		<script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>
<?php exit(); }

	$titulo = 'Actualizar Complemento Alimentario';

  	$idTipoComplemento = (isset($_POST['idTipoComplemento']) && $_POST['idTipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST['idTipoComplemento']) : '';
 	$con_tipo_complemento = "SELECT * FROM tipo_complemento WHERE ID = '$idTipoComplemento';";
  	$res_tipo_complemento = $Link->query($con_tipo_complemento) or die('Error al consultar el Tipo de complemento: '. mysqli_error($Link));
  	if($res_tipo_complemento->num_rows > 0) {
    	$reg_tipo_complemento = $res_tipo_complemento->fetch_assoc();
    	$id = $reg_tipo_complemento['ID'];
    	$codigo = $reg_tipo_complemento['CODIGO'];
    	$descripcion = $reg_tipo_complemento['DESCRIPCION'];
    	$jornada = $reg_tipo_complemento['Jornada'];
    	$valorRacion = $reg_tipo_complemento['ValorRacion'];
    	$numeroRaciones = $reg_tipo_complemento['numero_raciones_contratadas'];
    	$jornadaUnica = $reg_tipo_complemento['jornadaUnica'];
  	}
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-8">
    	<h2><?= $titulo; ?></h2>
    	<ol class="breadcrumb">
      		<li>
        		<a href="<?= $baseUrl; ?>">Home</a>
      		</li>
      		<li>
      			<a href="<?= $baseUrl . '/modules/complementos_alimentarios'; ?>">Complementos Alimentarios</a>
      		</li>
      		<li class="active">
        		<strong><?= $titulo; ?></strong>
      		</li>
    	</ol>
  	</div>
  	<div class="col-lg-4">
    	<div class="title-action">
      		<a href="#" class="btn btn-primary" id="actualizarComplementoAlimentario"><i class="fa fa-check "></i> Guardar </a>
    	</div>
  	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  	<div class="row">
    	<div class="col-lg-12">
      		<div class="ibox float-e-margins">
        		<div class="ibox-content contentBackground">
          			<form id="formActualizarComplementoAlimentario">
          				<div class="row">
        					<div class="col-sm-12">
                				<div class="form-group col-sm-6 col-md-4">
                  					<label for="codigo">Código</label>
                  					<input type="text" class="form-control" name="codigo" id="codigo" maxlength="10" value="<?= $codigo; ?>" required readonly>
                				</div>

                				<div class="form-group col-sm-6 col-md-4">
                  					<label for="descripcion">Descripción</label>
                  					<input type="text" class="form-control" name="descripcion" id="descripcion" maxlength="200" value="<?= $descripcion; ?>" required>
                				</div>

                				<div class="form-group col-sm-6 col-md-4">
                  					<label for="jornada">Jornada</label>
                  					<select class="form-control" name="jornada" id="jornada" required>
                    					<option value="">Seleccione una</option>
                    					<?php
                      						$con_jornadas = "SELECT * FROM jornada WHERE id != 0";
                      						$res_jornadas = $Link->query($con_jornadas) or die('Error al consultar edades de grupo etarios: '. mysqli_error($Link));
                      						if($res_jornadas->num_rows > 0) {
                        						while($reg_jornadas = $res_jornadas->fetch_assoc()) {
                    					?>
                    								<option value="<?= $reg_jornadas["id"]; ?>" <?= (isset($jornada) && $jornada == $reg_jornadas["id"]) ? "selected" : ""; ?>><?= $reg_jornadas["nombre"]; ?></option>
                    					<?php
                        						}
                      						}
                    					?>
                  					</select>
                				</div>
              				</div>

              				<div class="col-sm-12">
                				<div class="form-group col-sm-6 col-md-4">
                  					<label for="valorRacion">Valor Ración</label>
                  					<input type="number" class="form-control" name="valorRacion" id="valorRacion" min="0" max ="1000000" value="<?= $valorRacion; ?>" required>
                				</div>

                				<div class="form-group col-sm-6 col-md-4">
                  					<label for="numeroRaciones">Número Raciones</label>
                  					<input type="number" class="form-control" name="numeroRaciones" id="numeroRaciones" min="0" max ="1000000" value="<?= $numeroRaciones; ?>" required step = "1">
                				</div>

                				<div class="form-group col-sm-6 col-md-4">
									<label for="jornadaUnica">Jornada Única</label>
									<div class="radio">
										<input type="radio" name="jornadaUnica" id="jornadaSi" value="1" required <?= $jornadaUnica == 1 ? "checked" : "" ?>> Si	&nbsp; &nbsp; 
										<input type="radio" name="jornadaUnica" id="jornadaNo" value="0" required <?= $jornadaUnica == 0 ? "checked" : "" ?>>No		
									</div>
                				</div>
              				</div>
          				</div>

          				<div class="row">
          					<div class="col-sm-12">
          						<div class="row-">
          							<div class="col-sm-3 col-lg-2 text-center">
      									<a href="#" class="btn btn-primary" id="actualizarComplementoAlimentarioContinuar"><i class="fa fa-check "></i> Guardar y Continuar </a>
          							</div>
          						</div>
          					</div>
          				</div>
            			<input type="hidden" name="id" id="id" value="<?= $id; ?> ">
          			</form>
        		</div>
      		</div>
    	</div>
  	</div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>

<!-- Section Scripts -->
<script src="<?= $baseUrl; ?>/modules/complementos_alimentarios/js/complementos_alimentarios.js"></script>
<?php mysqli_close($Link); ?>

</body>
</html>