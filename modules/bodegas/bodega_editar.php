<?php
	include '../../header.php';
	$titulo = "Actualizar Bodega";

  $codigoBodega = (isset($_POST['codigoBodega']) && $_POST['codigoBodega'] != '') ? $_POST['codigoBodega'] : '';
  $consulta1 = "SELECT * FROM bodegas WHERE ID = '$codigoBodega'";
  $resultado1 = $Link->query($consulta1) or die('Error consulta bodega: ' . mysqli_error($Link));
  if ($resultado1->num_rows > 0)
  {
    $registros1 = $resultado1->fetch_assoc();
    $nombreBodega = $registros1['NOMBRE'];
    $direccionBodega = $registros1['DIRECCION'];
    $telefonoBodega = $registros1['TELEFONO'];
    $ciudadBodega = $registros1['CIUDAD'];
    $responsableBodega = $registros1['RESPONSABLE'];
  }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
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
      <a class="btn btn-primary" id="actualizarBodega"><i class="fa fa-check"></i> Guardar </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formActualizarBodega">
          	<div class="row">
        			<div class="col-xs-12">
        				<div class="row">

	        				<div class="form-group col-sm-4">
		                <label for="codigo">Código</label>
		                <input type="text" class="form-control" name="codigo" id="codigo" value="<?php if (isset($codigoBodega) && $codigoBodega != '') { echo $codigoBodega; } ?>" <?php if (isset($codigoBodega) && $codigoBodega != '') { echo 'readOnly'; } ?> required>
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="nombre">Nombre bodega</label>
		                <input type="text" class="form-control" name="nombre" id="nombre" value="<?php if (isset($nombreBodega) && $nombreBodega != '') { echo $nombreBodega; } ?>" required>
		              </div>

									<div class="form-group col-sm-4">
		                <label for="telefono">Teléfono</label>
		                <input type="tel" class="form-control" name="telefono" id="telefono" value="<?php if (isset($telefonoBodega) && $telefonoBodega != '') { echo $telefonoBodega; } ?>">
		              </div>

        				</div>

								<div class="row">

									<div class="form-group col-sm-4">
		                <label for="direccion">Dirección</label>
		                <input type="text" class="form-control" name="direccion" id="direccion" value="<?php if (isset($direccionBodega) && $direccionBodega != '') { echo $direccionBodega; } ?>">
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="ciudad">Ciudad</label>
		                <select class="form-control" name="ciudad" id="ciudad" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $consulta2= "SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion WHERE CodigoDANE LIKE '". $_SESSION['p_CodDepartamento'] ."%' ORDER BY ciudad ASC; ";
		                    $resultado2 = $Link->query($consulta2) or die (mysqli_error($Link));
		                    if($resultado2){
		                      while($registros2 = $resultado2->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $registros2['CodigoDANE']; ?>" <?php if(isset($ciudadBodega) && $registros2['CodigoDANE'] == $ciudadBodega){ echo 'selected'; } ?>>
		                          <?php echo $registros2['Ciudad']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>

		              <div class="form-group col-sm-4">
		                <label for="responsable">Responsable</label>
		                <input type="text" class="form-control" name="responsable" id="responsable" value="<?php if (isset($responsableBodega) && $responsableBodega != '') { echo $responsableBodega; } ?>" required>
		              </div>

								</div>

        			</div>
          	</div>
          	<div class="row">
          		<div class="col-sm-3 col-lg-2 text-center">
          			<a class="btn btn-primary"  id="actualizarBodegaContinuar"><i class="fa fa-check "></i> Guardar y Continuar </a>
          		</div>
          	</div>
          </form>
        </div>
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