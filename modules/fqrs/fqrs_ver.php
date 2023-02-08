<?php
	include '../../header.php';

    if ($permisos['fqrs'] == "0") {
        ?><script type="text/javascript">
            window.open('<?= $baseUrl ?>', '_self');
        </script>
    <?php exit(); }
	      	  else {
				?><script type="text/javascript">
				  const list = document.querySelector(".li_fqrs");
				  list.className += " active ";
				</script>
				<?php
				}

	$titulo = 'Ver Fqrs';

  	$id_fqrs = (isset($_POST['id_fqrs']) && $_POST['id_fqrs'] != '') ? mysqli_real_escape_string($Link, $_POST['id_fqrs']) : '';
	$consulta = "SELECT f.ID AS id_fqrs,
					u.Ciudad AS nombre_municipio,
					s.nom_inst AS nombre_institucion,
				    s.nom_sede AS nombre_sede,
				    tc.Descripcion AS descripcion_tipo_caso,
				    CASE tc.tipo
				    	WHEN 'F' THEN 'Felicitaciones'
				    	WHEN 'Q' THEN 'Queja'
				    	WHEN 'R' THEN 'Reclamo'
				    	WHEN 'R' THEN 'Sugerencia'
				    END AS nombre_tipo_caso,
				    tp.Descripción AS descripcion_tipo_persona,
				    td.Abreviatura AS abreviatura_tipo_documento,
				    f.num_doc AS numero_documento,
				    f.nombre_completo AS nombre_persona,
				    f.estado AS id_estado,
				    IF (f.estado = 0, '<span class=\"label label-warning\">Abierto</span>', '<span class=\"label label-primary\">Cerrado</span>') AS estado,
				    f.fecha_creacion,
					f.direccion,
					f.telefono,
					f.email,
    				f.observacion,
    				us.nombre AS nombre_usuario_responsable,
    				f.fecha_solucion,
    				f.solucion
				FROM fqrs f
					INNER JOIN ubicacion u ON u.CodigoDANE = f.cod_mun
				    INNER JOIN tipo_casosfqrs tc ON tc.ID = f.tipo_caso
				    INNER JOIN tipo_personafqrs tp ON tp.ID = f.tipo_persona
				    INNER JOIN tipodocumento td ON td.id = f.tipo_doc
				    INNER JOIN sedes".$_SESSION['periodoActual']." s ON s.cod_sede = f.cod_sede
				    LEFT JOIN usuarios us ON us.id = f.id_responsable
				WHERE f.ID = '". $id_fqrs ."'";
    // exit(var_dump($consulta));            
	$resultado = $Link->query($consulta) or die ("Error al consultar datos del fqrs: ". mysqli_error($Link));
	if ($resultado->num_rows > 0) {
		$caso = $resultado->fetch_object();
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
      	<a href="<?= $baseUrl . '/modules/fqrs'; ?>">fqrs</a>
      </li>
      <li class="active">
        <strong><?= $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
  	<?php if ($caso->id_estado == 0 && ($_SESSION['perfil'] == "0" || $permisos['fqrs'] == "2")) { ?>
	    <div class="title-action">
	      <a href="#" class="btn btn-primary" id="boton_editar_caso"><i class="fa fa-check "></i> Guardar </a>
	    </div>
	<?php } ?>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
        	<h1><span><?= $caso->nombre_tipo_caso; ?></span></h1>
        	<h3><?= $caso->descripcion_tipo_caso; ?></h3>
			<hr>

        	<div class="row">
    			<div class="col-sm-5">
        			<div class="row">
    					<div class="col-sm-4">
    						<label>Municipio</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->nombre_municipio; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label>Institución</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->nombre_institucion; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label>Sede</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->nombre_sede; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label>Fecha de creación</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->fecha_creacion; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label>Estado</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->estado; ?>
    					</div>
    				</div>
    			</div>

    			<div class="col-sm-4">
        			<div class="row">
    					<div class="col-sm-4">
    						<label>Tipo de persona</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->descripcion_tipo_persona; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label><?= $caso->abreviatura_tipo_documento; ?></label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->numero_documento; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label>Nombre</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->nombre_persona; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label>Dirección</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->direccion; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label>Teléfono</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->telefono; ?>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col-sm-4">
    						<label>Correo electrónico</label>
    					</div>
    					<div class="col-sm-8">
    						<?= $caso->email; ?>
    					</div>
    				</div>
    			</div>
        	</div>
			<br>
        	<div class="row">
				<div class="col-sm-12">
        			<div class="row">
    					<div class="col-sm-12">
    						<label>Observación: </label>
    					</div>
    					<div class="col-sm-12">
        					<p><?= $caso->observacion; ?></p>
    					</div>
    				</div>
    			</div>
        	</div>

        	<?php if ($caso->id_estado == 1) { ?>
        		<hr>

				<h1><span>Solución</span></h1>

        		<div class="row">
	    			<div class="col-sm-5">
	        			<div class="row">
	    					<div class="col-sm-4">
	    						<label>Usuario responsable </label>
	    					</div>
	    					<div class="col-sm-8">
	    						<?= $caso->nombre_usuario_responsable; ?>
	    					</div>
	    				</div>
	    				<div class="row">
	    					<div class="col-sm-4">
	    						<label>Fecha solución </label>
	    					</div>
	    					<div class="col-sm-8">
	    						<?= $caso->fecha_solucion; ?>
	    					</div>
	    				</div>
	    			</div>
	        	</div>
				<br>
	        	<div class="row">
					<div class="col-sm-12">
	        			<div class="row">
	    					<div class="col-sm-12">
	    						<label>Descripción solución: </label>
	    					</div>
	    					<div class="col-sm-12">
	        					<p><?= $caso->solucion; ?></p>
	    					</div>
	    				</div>
	    			</div>
	        	</div>
        	<?php } ?>
        </div>
      </div>
    </div>
  </div>

<?php if ($caso->id_estado == 0 && ($_SESSION['perfil'] == "0" || $permisos['fqrs'] == "2")) { ?>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
        	<form id="formulario_editar_fqrs" method="post">
        		<input type="hidden" name="id_caso" id="id_caso" value="<?= $id_fqrs; ?>">
  				<div class="row">
					<div class="col-sm-12">
						<div class="row">
							<div class="form-group col-sm-6">
				                <label for="solucion">Descripción de la solución *</label>
				                <textarea class="form-control" name="solucion" id="solucion" rows="8" maxlength="2000" required></textarea>
			              	</div>
						</div>
					</div>
  				</div>

	          	<div class="row">
      				<div class="col-sm-3 col-lg-2">
  						<a href="#" class="btn btn-primary" id="boton_editar_caso"><i class="fa fa-check "></i> Guardar </a>
      				</div>
	          	</div>
  			</form>
        </div>
      </div>
    </div>
  </div>
<?php } ?>
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
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<!-- Section Scripts -->
<script src="<?= $baseUrl; ?>/modules/fqrs/js/fqrs.js"></script>
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

	// $(document).ready(function() {
	// 	$('#tipoJuridico').trigger('change');
	// });
</script>

<?php mysqli_close($Link); ?>

</body>
</html>