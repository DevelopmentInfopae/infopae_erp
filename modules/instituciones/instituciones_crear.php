<?php
	include '../../header.php';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Nueva Institución</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
      	<a href="<?php echo $baseUrl . '/modules/instituciones/instituciones.php'; ?>">Instituciones</a>
      </li>
      <li class="active">
        <strong>Nueva institución</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <a class="btn btn-primary" onclick="guardarInstitucion(false);"><i class="fa fa-check "></i> Guardar </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formCrearInstitucion" action="function/fn_usuario_crear.php" method="post">
          	<div class="row">
        			<div class="col-xs-12">
        				<div class="row">

	        				<div class="form-group col-sm-4">
		                <label for="numeroDocumento">Código</label>
		                <input type="text" class="form-control" name="codigo" id="codigo" required>
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="nombre">Nombre Institución</label>
		                <input type="text" class="form-control" name="nombre" id="nombre" required>
		              </div>

									<div class="form-group col-sm-4">
		                <label for="telefono">Teléfono</label>
		                <input type="tel" class="form-control" name="telefono" id="telefono">
		              </div>

        				</div>
        				<div class="row">

									<div class="form-group col-sm-4">
		                <label for="email">Email</label>
		                <input type="email" class="form-control" name="email" id="email">
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="municipio">Municipio </label>
		                <select class="form-control" name="municipio" id="municipio" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $codigoCiudad = $_SESSION['p_CodDepartamento'];
		                    $consulta1= " SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion where CodigoDANE LIKE '$codigoCiudad%' order by ciudad asc; ";
		                    $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
		                    if($result1){
		                      while($row1 = $result1->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $row1['CodigoDANE']; ?>" <?php if(isset($row['cod_mun']) && $row['cod_mun'] == $row1['CodigoDANE'] || $municipio_defecto["CodMunicipio"] == $row1['CodigoDANE']){ echo ' selected '; } ?>>
		                          <?php echo $row1['Ciudad']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="rector">Rector </label>
		                <select class="form-control" name="rector" id="rector" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $codigoCiudad = $_SESSION['codCiudad'];
		                    $consulta1= " SELECT num_doc, nombre FROM usuarios WHERE id_perfil = '6' AND cod_mun LIKE '$codigoCiudad%' ORDER BY nombre ASC;";
		                    $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
		                    if($result1){
		                      while($row1 = $result1->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $row1['num_doc']; ?>">
		                          <?php echo $row1['nombre']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

        				</div>
        			</div>
          	</div>

          	<div class="row">
          		<div class="col-sm-3 col-lg-2 text-center">
          			<a class="btn btn-primary" onclick="guardarInstitucion(true);"><i class="fa fa-check "></i> Guardar y Continuar </a>
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

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/instituciones.js"></script>
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>
<?php mysqli_close($Link); ?>

</body>
</html>