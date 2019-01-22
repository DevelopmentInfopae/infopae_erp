<?php
	include '../../header.php';
	$titulo = "Nueva Bodega";
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
      	<a href="<?php echo $baseUrl . '/modules/bodegas/index.php'; ?>">Bodegas</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <a class="btn btn-primary" id="guardarBodega"><i class="fa fa-check"></i> Guardar </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formGuardarBodega">
          	<div class="row">
        			<div class="col-xs-12">
        				<div class="row">

	        				<div class="form-group col-sm-4">
		                <label for="codigo">Código</label>
		                <input type="number" class="form-control" name="codigo" id="codigo" pattern="[0-9]*" required>
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="nombre">Nombre bodega</label>
		                <input type="text" class="form-control" name="nombre" id="nombre" required>
		              </div>

									<div class="form-group col-sm-4">
		                <label for="telefono">Teléfono</label>
		                <input type="tel" class="form-control" name="telefono" id="telefono">
		              </div>

        				</div>

								<div class="row">

									<div class="form-group col-sm-4">
		                <label for="direccion">Dirección</label>
		                <input type="text" class="form-control" name="direccion" id="direccion">
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="ciudad">Ciudad</label>
		                <select class="form-control" name="ciudad" id="ciudad" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $consulta1= "SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion WHERE CodigoDANE LIKE '". $_SESSION['p_CodDepartamento'] ."%' ORDER BY ciudad ASC; ";
		                    $resultado1 = $Link->query($consulta1) or die (mysqli_error($Link));
		                    if($resultado1){
		                      while($registros1 = $resultado1->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $registros1['CodigoDANE']; ?>" <?php if(isset($row['cod_mun']) && $row['cod_mun'] == $registros1['CodigoDANE'] || $municipio_defecto["CodMunicipio"] == $registros1["CodigoDANE"]){ echo ' selected '; } ?>>
		                          <?php echo $registros1['Ciudad']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="responsable">Responsable</label>
		                <input type="text" class="form-control" name="responsable" id="responsable" required>
		              </div>

								</div>

        			</div>
          	</div>
          	<div class="row">
          		<div class="col-sm-3 col-lg-2 text-center">
          			<a class="btn btn-primary"  id="guardarBodegaContinuar"><i class="fa fa-check "></i> Guardar y Continuar </a>
          		</div>
          	</div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- <div class="modal inmodal fade" id="ventanaInformar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
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
</div> -->

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/bodegas/js/bodegas.js"></script>

<!-- Section Scripts -->
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>
<?php mysqli_close($Link); ?>

</body>
</html>