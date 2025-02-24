<?php 
	include '../../header.php';
	$codigoInstitucion = $_POST["codigoInstitucion"];

	$consulta1 = "SELECT * FROM instituciones WHERE codigo_inst = '$codigoInstitucion' LIMIT 1";
	$resultado1 = $Link->query($consulta1);
	if($resultado1){
		$registros1 = $resultado1->fetch_assoc();
	}
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Editar Institución</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl.$_SESSION['rutaDashboard']; ?>">Home <?php echo $codigoInstitucion; ?></a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/instituciones/instituciones.php'; ?>">Instituciones</a>
      </li>
      <li class="active">
        <strong>Editar Institución</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <a class="btn btn-primary" onclick="actualizarInstitucion();"><i class="fa fa-check "></i> Guardar </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
        	<h2><?php echo $registros1["nom_inst"]; ?></h2>
          <form id="formEditarInstitucion" action="function/fn_usuario_crear.php" method="post">
          	<div class="row">
        			<div class="col-xs-12">
        				<div class="row">
        					
	        				<div class="col-sm-4 col-md-4 col-lg-3">
	        					<div class="form-group">
			                <label for="codigo">Código</label>
			                <input type="text" class="form-control" name="codigo" id="codigo" value="<?php echo $registros1["codigo_inst"]; ?>" disabled required>
			                <input type="hidden" name="id" id="id" value="<?php echo $registros1["id"]; ?>">
			              </div>
	        				</div>
	        				<div class="col-sm-4 col-md-4 col-lg-3">
			              <div class="form-group">
			                <label for="nombre">Nombre</label>
			                <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $registros1["nom_inst"]; ?>" required>
			              </div>
	        				</div>
	        				<div class="col-sm-4 col-md-4 col-lg-3">
	        					<div class="form-group">
			                <label for="telefono">Teléfono</label>
			                <input type="tel" class="form-control" name="telefono" value="<?php echo $registros1["tel_int"]; ?>" id="telefono">
			              </div>
	        				</div>
	        				<div class="col-sm-4 col-md-4 col-lg-3">
	        					<div class="form-group">
			                <label for="email">Email</label>
			                <input type="email" class="form-control" name="email" value="<?php echo $registros1["email_inst"]; ?>" id="email">
			            	</div>
	        				</div>
        				</div>
        				<div class="row">
	        				<div class="col-sm-4 col-md-4 col-lg-3">
	        					<div class="form-group">
			                <label for="municipio">Municipio </label>
			                <select class="form-control" name="municipio" id="municipio" required>
			                	<option value="">Seleccione uno</option>
			                	<?php
			                    $codigoCiudad = $_SESSION['codCiudad'];
			                    $consulta2= " SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion where CodigoDANE LIKE '$codigoCiudad%' order by ciudad asc; ";
			                    $resultado2 = $Link->query($consulta2);
			                    if($resultado2){
			                      while($registros2 = $resultado2->fetch_assoc()){
			                  ?>
			                        <option value="<?php echo $registros2['CodigoDANE']; ?>" <?php if(isset($registros1['cod_mun']) && $registros1['cod_mun'] == $registros2['CodigoDANE']){ echo ' selected '; } ?>>
			                          <?php echo $registros2['Ciudad']; ?>
			                        </option>
			                  <?php
			                      }
			                    }
			                  ?>
			                </select>
			              </div>
	        				</div>
	        				<div class="col-sm-4 col-md-4 col-lg-3">
	        					<div class="form-group">
			                <label for="rector">Rector </label>
			                <select class="form-control" name="rector" id="rector" required>
			                	<option value="">Seleccione uno</option>
			                	<?php
			                    $codigoCiudad = $_SESSION['codCiudad'];
			                    $consulta3= " SELECT num_doc, nombre FROM usuarios WHERE id_perfil = '6' AND cod_mun LIKE '$codigoCiudad%' ORDER BY nombre ASC;"; echo $consulta3;
			                    $resultado3 = $Link->query($consulta3);
			                    if($resultado3){
			                      while($registros3 = $resultado3->fetch_assoc()){
			                  ?>
			                        <option value="<?php echo $registros3['num_doc']; ?>" <?php if(isset($registros1['cc_rector']) && $registros1['cc_rector'] == $registros3['num_doc']){ echo ' selected '; } ?> >
			                          <?php echo $registros3['nombre']; ?>
			                        </option>
			                  <?php
			                      }
			                    }
			                  ?>
			                </select>
			              </div>
	        				</div>
	        				<div class="col-sm-4 col-md-4 col-lg-3">
	        					<div class="form-group">
			                <label for="estado">Estado</label>
			                <div class="radio">
											  <label>
											    <input type="radio" name="estado" id="estado1" value="1" <?php echo ($registros1["estado"] == "1") ? "checked" : ""; ?> required>
											    Activar
											  </label>
											  <label>
											    <input type="radio" name="estado" id="estado2" value="0" <?php echo ($registros1["estado"] == "0") ? "checked" : ""; ?>  required>
											    Inactivar
											  </label>
											</div>
											<label for="estado"></label>
			              </div>
	        				</div>
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
       
<div class="modal inmodal fade" id="ventanaInformar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header text-primary" style="padding: 15px;">
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

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/instituciones/js/instituciones.js"></script>
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	$('input').iCheck({
	  labelHover: false,
	  cursor: true,
	  radioClass: "iradio_square-green"
	});
</script>
<?php mysqli_close($Link); ?>

</body>
</html>