<?php
	include '../../header.php';
	$titulo = 'Actualizar Tipo complemento';

  $idTipoComplemento = (isset($_POST['idTipoComplemento']) && $_POST['idTipoComplemento'] != '') ? mysqli_real_escape_string($Link, $_POST['idTipoComplemento']) : '';
  $consulta = "SELECT * FROM tipo_complemento WHERE ID = '$idTipoComplemento';";
  $resultado = $Link->query($consulta) or die('Error al consultar el grupo estario: '. mysqli_error($Link));
  if($resultado->num_rows > 0)
  {
    $registros = $resultado->fetch_assoc();
    $codigo = $registros['CODIGO'];
    $descripcion = $registros['DESCRIPCION'];
    $jornada = $registros['Jornada'];
    $valorRacion = $registros['ValorRacion'];
  }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/tipo_complementos'; ?>">Tipo complementos</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="actualizarTipoComplemento"><i class="fa fa-check "></i> Guardar </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formActualizarTipoComplemento">
          	<div class="row">
        			<div class="col-sm-12">

        				<div class="form-group col-sm-6 col-md-2">
                  <label for="codigo">Código</label>
                  <input type="text" class="form-control" name="codigo" id="codigo" maxlength="10" value="<?php if(isset($codigo) && $codigo != ''){ echo $codigo; } ?>" disabled required>
                  <input type="hidden" name="id" id="id" value="<?php echo $idTipoComplemento; ?>">
                </div>

                <div class="form-group col-sm-6 col-md-6">
                  <label for="descripcion">Descripción</label>
                  <input type="text" class="form-control" name="descripcion" id="descripcion" maxlength="200" value="<?php if(isset($descripcion) && $descripcion != ''){ echo $descripcion; } ?>">
                </div>

                <div class="form-group col-sm-6 col-md-2">
                  <label for="jornada">Jornada</label>
                  <select class="form-control" name="jornada" id="jornada" required>
                    <option value="">Seleccionar</option>
                    <?php
                    $consulta = "SELECT id AS codigoJornada, nombre AS nombreJornada FROM jornada;";
                    $resultado = $Link->query($consulta) or die ('Error al consultar las jornadas: '. mysqli_error($Link));
                    if($resultado->num_rows > 0)
                    {
                      while ($registros = $resultado->fetch_assoc())
                      {
                    ?>
                    <option value="<?php echo $registros['codigoJornada']; ?>" <?php if(isset($jornada) && $jornada == $registros['codigoJornada']){ echo 'selected'; } ?>><?php echo $registros['nombreJornada']; ?></option>
                    <?php
                      }
                    }
                    ?>
                  </select>
                </div>

                <div class="form-group col-sm-6 col-md-2">
                  <label for="valorRacion">Valor ración</label>
                  <input type="number" class="form-control" name="valorRacion" id="valorRacion" value="<?php if(isset($valorRacion) && $valorRacion != ''){ echo $valorRacion; } ?>" required>
                </div>

              </div>
          	</div>
          	<div class="row">
          		<div class="col-sm-12">
          			<div class="row-">
          				<div class="col-sm-3 col-lg-2 text-center">
      							<a href="#" class="btn btn-primary" id="actualizarTipoComplementoContinuar"><i class="fa fa-check "></i> Guardar y Continuar </a>
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

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/tipo_complementos/js/tipo_complementos.js"></script>
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>
<?php mysqli_close($Link); ?>

</body>
</html>