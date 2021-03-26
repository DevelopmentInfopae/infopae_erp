<?php
	include '../../header.php';
	$titulo = 'Nuevo Manipuladora Valores Nomina';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/manipuladoras_valores_nomina/index.php'; ?>">Manipuladoras Valores Nomina</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="guardarManipuladoraValoresNomina"><i class="fa fa-check "></i> Guardar </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<form id="formCrearManipuladoraValorNomina">
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group col-sm-6 col-md-3">
	                  				<label for="tipoComplemento">Tipo de complemento</label>
	                  				<select class="form-control" name="complemento" id="complemento" required>
	                  					<option value="">Seleccione una</option>
	                  					<?php
	                  					    $consultaComplementos = "SELECT CODIGO FROM tipo_complemento";
                      						$res_complementos = $Link->query($consultaComplementos) or die('Error al consultar complementos: '. mysqli_error($Link));
                      						if($res_complementos->num_rows > 0) {
                        						while($dataComplementos = $res_complementos->fetch_assoc()) { 
	                  					?>
	                  					<option value="<?= $dataComplementos["CODIGO"]; ?>"><?= $dataComplementos["CODIGO"]; ?></option>
	                  					<?php
					                        }
					                      }
					                    ?>
					                </select>    
                				</div>

								<div class="form-group col-sm-6 col-md-3">
									<label for="Tipo">Tipo</label>
									<select class="form-control" name="tipo" id="tipo" required>
										<option value = "">Seleecione una</option>
										<option value = 1> Pago por día</option>
										<option value = 2> Pago por titular</option>
									</select>
								</div>

								<div class="form-group col-sm-6 col-md-3">
									<label for="limiteInferior">Limite Inferior</label>
									<input type="number" name="limiteInferior" id="limiteInferior" class="form-control" min="1" required>
								</div>

								<div class="form-group col-sm-6 col-md-3">
									<label for="limiteSuperior">Limite Superior</label>
									<input type="number" name="limiteSuperior" id="limiteSuperior" class="form-control" min="1" required>
								</div>
							</div>
							<div class="col-sm-12">
								<div class="form-group col-sm-6 col-md-3">
									<label for="valor">Valor</label>
									<input type="number" name="valor" id="valor" class="form-control" min="1" required>
								</div>
							</div>
						</div>
						<br>
						<div class="row">
          					<div class="col-sm-12">
          						<div class="row">
          							<div class="col-sm-3 col-lg-2 text-center">
      									<a href="#" class="btn btn-primary" id="guardarManipuladoraValoresNominaContinuar"><i class="fa fa-check "></i> Guardar y Continuar </a>
          							</div>
          						</div>
          					</div>
          				</div>
					</form>
				</div> <!-- contentBackground -->
			</div><!-- float-e-margins -->
		</div><!--  col-lg-12 -->
	</div> <!-- row -->
</div> <!-- fadeInRight -->

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
<script src="<?php echo $baseUrl; ?>/modules/manipuladoras_valores_nomina/js/manipuladoras_valores_nomina.js"></script>
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>
<?php mysqli_close($Link); ?>

</body>
</html>