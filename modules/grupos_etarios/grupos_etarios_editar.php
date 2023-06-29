<?php
include '../../header.php';

if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
?>	<script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php exit(); }
  	  else {
        ?><script type="text/javascript">
          const list = document.querySelector(".li_configuracion");
          list.className += " active ";
          const list2 = document.querySelector(".li_gruposEtarios");
          list2.className += " active ";
        </script>
        <?php
        }

	$titulo = 'Actualizar Grupo etario';

  $codigoGrupoEtario = (isset($_POST['codigoGrupoEtario']) && $_POST['codigoGrupoEtario'] != '') ? mysqli_real_escape_string($Link, $_POST['codigoGrupoEtario']) : '';
  $consulta = "SELECT DESCRIPCION AS descripcion, EDADINICIAL AS edadInicial, EDADFINAL AS edadFinal FROM grupo_etario WHERE ID = '$codigoGrupoEtario';";
  $resultado = $Link->query($consulta) or die('Error al consultar el grupo estario: '. mysqli_error($Link));
  if($resultado->num_rows > 0)
  {
    $registros = $resultado->fetch_assoc();
    $descripcion = $registros['descripcion'];
    $edadInicial = $registros['edadInicial'];
    $edadFinal = $registros['edadFinal'];
  }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/grupos_etarios'; ?>">Grupos Etarios</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="actualizarGrupoEtario"><i class="fa fa-check "></i> Guardar </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formActualizarGrupoEtario">
          	<div class="row">
        			<div class="col-sm-12">

        				<div class="form-group col-sm-6 col-md-4">
	                <label for="descripcion">Descripción</label>
	                <input type="text" class="form-control" name="descripcion" id="descripcion" maxlength="200" value="<?php echo $descripcion; ?>" required>
                  <input type="hidden" name="codigo" id="codigo" value="<?php echo $codigoGrupoEtario; ?>">
	              </div>

	              <div class="form-group col-sm-6 col-md-4">
	                <label for="edadInicial">Edad inicial</label>
	                <input type="number" class="form-control" name="edadInicial" id="edadInicial" min="3" max="17" value="<?php echo $edadInicial; ?>" required>
	              </div>

								<div class="form-group col-sm-6 col-md-4">
	                <label for="edadFinal">Edad final</label>
	                <input type="number" class="form-control" name="edadFinal" id="edadFinal" min="3" max="17" value="<?php echo $edadFinal; ?>" required>
	              </div>

              </div>
          	</div>
          	<div class="row">
          		<div class="col-sm-12">
          			<div class="row-">
          				<div class="col-sm-3 col-lg-2 text-center">
      							<a href="#" class="btn btn-primary" id="actualizarGrupoEtario"><i class="fa fa-check "></i> Guardar y Continuar </a>
          				</div>
          			</div>
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

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/grupos_etarios/js/grupos_etarios.js"></script>
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>
<?php mysqli_close($Link); ?>

</body>
</html>