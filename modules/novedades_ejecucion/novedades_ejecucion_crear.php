<?php
include '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2>Nueva novedad de focalización</h2>
		<div class="debug"></div>
		<ol class="breadcrumb">
			<li> <a href="<?php echo $baseUrl; ?>">Home</a> </li>
			<li> <a href="<?php echo $baseUrl; ?>/modules/novedades_ejecucion">Novedades de focalización</a> </li>
			<li class="active"> <strong>Novedad de focalización crear</strong> </li>
		</ol>
	</div>
	<div class="col-lg-4">
		<div class="title-action">
			<!-- <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a> <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a> -->
			<a href="#" target="_self" class="btn btn-primary guaradarNovedad"><i class="fa fa-check"></i> Guardar</a>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
    <form class="col-lg-12" action="" method="post" name="formNovedadesEjecucion" id="formNovedadesEjecucion" enctype="multipart/form-data">

			<div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <div class="row">
            <div class="col-sm-4 form-group" required>
              <label for="municipio">Municipio</label>
              <select class="form-control" name="municipio" id="municipio" required>
                <option value="">Seleccione uno</option>
              </select>
            </div><!-- /.col -->
            <div class="col-sm-4 form-group">
              <label for="institucion">Institución</label>
              <select class="form-control" name="institucion" id="institucion" required>
                <option value="">Seleccione una</option>
              </select>
            </div><!-- /.col -->
            <div class="col-sm-4 form-group">
              <label for="sede">Sede</label>
              <select class="form-control" name="sede" id="sede" required>
                <option value="">Selecciones una</option>
              </select>
            </div><!-- /.col -->
            <div class="col-sm-4 form-group">
              <label for="mes">Mes</label>
              <select class="form-control" name="mes" id="mes" required>
                <option value="">Seleccione uno</option>
              </select>
            </div><!-- /.col -->
            <div class="col-sm-4 form-group">
              <label for="semana">Semana</label>
              <select class="form-control" name="semana" id="semana" required>
                <option value="">Seleccione uno</option>
              </select>
              <div id="semana"> </div>
            </div><!-- /.col -->
            <div class="col-sm-4 form-group">
              <label for="tipoComplemento">Tipo complemento</label>
              <select class="form-control" name="tipoComplemento" id="tipoComplemento" required>
                <option value="">Seleccione uno</option>
              </select>
            </div><!-- /.col -->
          </div><!-- -/.row -->
          <div class="row">
            <div class="col-sm-4 form-group">
              <button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1"><strong>Buscar</strong></button>
            </div>
          </div>
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->

      <div class="wrapper wrapper-content animated fadeInRight tablaFocalizacion">
        <div class="row">
          <div class="col-lg-12">
            <div class="ibox float-e-margins">
              <div class="ibox-content contentBackground">
								<h2>Focalizados</h2>
								<div class="table-responsive">
									<table class="table table-striped table-hover selectableRows dataTablesNovedadesEjecucionFocalizados">
										<thead>
											<tr>
												<th>Documento</th>
												<th>Numero</th>
												<th>Nombre titular de derecho</th>
												<th>Complemento</th>
												<th>L</th>
												<th>M</th>
												<th>X</th>
												<th>J</th>
												<th>V</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
										<tfoot>
											<tr>
												<th>Documento</th>
												<th>Numero</th>
												<th>Nombre titular de derecho</th>
												<th>Complemento</th>
												<th>L</th>
												<th>M</th>
												<th>X</th>
												<th>J</th>
												<th>V</th>
											</tr>
										</tfoot>
									</table>
								</div>
								<h2>No focalizados</h2>
								<div class="table-responsive">
									<table class="table table-striped table-hover selectableRows dataTablesNovedadesEjecucionReserva">
										<thead>
											<tr>
												<th>Documento</th>
												<th>Numero</th>
												<th>Nombre titular de derecho</th>
												<th>Complemento</th>
												<th>L</th>
												<th>M</th>
												<th>X</th>
												<th>J</th>
												<th>V</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
										<tfoot>
											<tr>
												<th>Documento</th>
												<th>Numero</th>
												<th>Nombre titular de derecho</th>
												<th>Complemento</th>
												<th>L</th>
												<th>M</th>
												<th>X</th>
												<th>J</th>
												<th>V</th>
											</tr>
										</tfoot>
									</table>
								</div>

              </div>
            </div>



						<div class="ibox float-e-margins">
							<div class="ibox-content contentBackground">
								<div class="row">
									<div class="col-sm-12 form-group">
										<label for="observaciones">Observaciones</label>
										<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80"></textarea>
									</div><!-- /.col -->
								</div><!-- -/.row -->
							</div><!-- /.ibox-content -->
						</div><!-- /.ibox float-e-margins -->

						<div class="ibox float-e-margins">
							<div class="ibox-title">
								<h5>Adjuntar Archivo</h5>
							</div>
							<div class="ibox-content">
								<div class="row" name="subirArchivos">
									<div class="col-sm-12 form-group">
										<label for="departamento">Archivo</label>
										<div class="fileinput fileinput-new input-group" data-provides="fileinput"> <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Elegir archivo</span><span class="fileinput-exists">Change</span><input type="file" name="foto[]" id="foto" accept="image/jpeg,image/gif,image/png,application/pdf"></span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a> </div>
									</div><!-- /.col -->
								</div>
								<div class="row">
									<div class="col-sm-3 form-group">
										<button type="button" class="btn btn-primary guaradarNovedad"><i class="fa fa-check"></i> Guardar </button>
									</div><!-- /.col -->
								</div><!-- /.row -->
							</div>
						</div>














          </div>
        </div>
      </div>
    </form>
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->


<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <p>This is a small modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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













<!-- iCheck -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
		<script>
				$(document).ready(function () {
						$('.i-checks').iCheck({
								checkboxClass: 'icheckbox_square-green',
								radioClass: 'iradio_square-green',
						});
				});
		</script>






<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/js/novedades_ejecucion_crear.js"></script>



<script>

</script>

























<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>
<?php mysqli_close($Link); ?>
</body>
</html>
