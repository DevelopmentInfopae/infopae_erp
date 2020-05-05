<?php
	include '../../header.php';
	$titulo = 'Nueva nómina';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/nomina'; ?>">Nomina</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="crear_nomina"><i class="fa fa-check "></i> Guardar </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<form id="form_filtrar_empleados" method="post">
						<div class="row">
							<div class="form-group col-sm-6 col-md-3">
								<label>Tipo de Empleado</label>
								<select name="tipo" id="tipo" class="form-control" required>
									<option value="">Seleccione...</option>
									<option value="1">Empleado</option>
									<option value="2">Manipulador(a)</option>
									<option value="3">Contratista</option>
									<option value="4">Transportador</option>
								</select>
							</div>
							<div class="form-group col-sm-6 col-md-3">
								<label>Mes</label>
								<select name="mes" id="mes" class="form-control" required>
									<option value="">Seleccione primero tipo empleado</option>
								</select>
							</div>
							<div class="form-group col-sm-6 col-md-3">
								<label class="manipuladora_mostrar">Semana Inicial</label>
								<label class="manipuladora_ocultar" style="display: none;">Quincena Inicial</label>
								<select name="semana_inicial" id="semana_inicial" class="form-control" required>
									<option value="">Seleccione primero el mes</option>
								</select>
							</div>
							<div class="form-group col-sm-6 col-md-3">
								<label class="manipuladora_mostrar">Semana Final</label>
								<label class="manipuladora_ocultar" style="display: none;">Quincena Final</label>
								<select name="semana_final" id="semana_final" class="form-control" required>
									<option value="">Seleccione primero el mes</option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-6 col-md-3">
								<label>Municipio</label>
								<select name="municipio" id="municipio" class="form-control">
									<option value="">Todos los municipios</option>
									<?php 
										if (is_null($parametros['CodMunicipio'])) {
											$consulta_municipio = "SELECT * FROM ubicacion WHERE CodigoDANE LIKE '".$parametros['CodDepartamento']."%'";
										} else {
											$consulta_municipio = "SELECT * FROM ubicacion WHERE CodigoDANE LIKE '".$parametros['CodMunicipio']."%'";
										}
										$result_municipio = $Link->query($consulta_municipio);
										if ($result_municipio->num_rows > 0) {
											while ($municipio = $result_municipio->fetch_assoc()) { ?>
												<option value="<?= $municipio['CodigoDANE'] ?>"><?= $municipio['Ciudad'] ?></option>
											<?php }
										}
									?>
								</select>
							</div>
							<div class="form-group col-sm-6 col-md-3 manipuladora_mostrar">
								<label>Institución</label>
								<select name="institucion" id="institucion" class="form-control">
									<option value="">Todas las instituciones</option>
								</select>
							</div>
							<div class="form-group col-sm-6 col-md-3 manipuladora_mostrar">
								<label>Sede</label>
								<select name="sede" id="sede" class="form-control">
									<option value="">Todas las sedes</option>
								</select>
							</div>
							<div class="form-group col-sm-6 col-md-3 manipuladora_mostrar">
								<label>Tipo complemento</label>
								<select name="tipo_complemento" id="tipo_complemento" class="form-control">
									<option value="">Todos los complementos</option>
									<?php 
									$consulta = "SELECT * FROM tipo_complemento";
									$result = $Link->query($consulta);
									if ($result->num_rows > 0) {
										while($tcom = $result->fetch_assoc()){ ?>
											<option value="<?= $tcom['CODIGO'] ?>"><?= $tcom['CODIGO'] ?></option>
										<?php }
									}
									 ?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="form-group col-sm-12 col-md-12">
								<button type="button" class="btn btn-primary" id="aplicar_filtro">Buscar</button>
							</div>
						</div>
					</form>
					<form id="form_crear_nomina" method="post">
						<div class="row div_table" >
							
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/nomina/js/nomina.js"></script>
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	// Configuración del plugin datepicker
	$.fn.datepicker.dates['en'] = {
	  days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
	  daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sab", "Dom"],
	  daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
	  months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
	  monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"]
	};
	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd'
	});
</script>
<?php mysqli_close($Link); ?>

