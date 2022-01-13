	<?php
	  include '../../header.php';

	  if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    	?><script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	  <?php exit(); }

	  $titulo = 'Dias de contrato';

	  $arrayDiasContrato = [];
	  $conDiasContrato = "SELECT * FROM planilla_semanas";
	  $resDiasContrato = $Link->query($conDiasContrato);
	  if ($resDiasContrato->num_rows > 0)
	  {
	  	while($regDiasContrato = $resDiasContrato->fetch_assoc())
	  	$arrayDiasContrato[] = $regDiasContrato;
	  }
	?>

	<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	  <div class="col-lg-8">
	    <h2><?php echo $titulo; ?></h2>
	    <ol class="breadcrumb">
	      <li>
	        <a href="<?php echo $baseUrl; ?>">Home</a>
	      </li>
	      <li class="active">
	        <strong><?php echo $titulo; ?></strong>
	      </li>
	    </ol>
	  </div><!-- /.col -->
	  <div class="col-lg-4">
	  	<?php if ($_SESSION['perfil'] == "0" || $permisos['configuracion'] == "2"): ?>
	  		<div class="title-action">
	      		<a href="#" id="confirmarGuardarRegistrosMes" class="btn btn-primary"><i class="fa fa-check"></i> Guardar </a>
	    	</div><!-- /.title-action -->
	  	<?php endif ?>
	  </div><!-- /.col -->
	</div><!-- /.row -->

	<div class="wrapper wrapper-content animated fadeInRight">
	  <div class="row">
	    <div class="col-lg-12">
	      <div class="ibox float-e-margins">
	        <div class="ibox-content contentBackground">
	        	<div class="row">
	        		<div class="col-md-6 col-md-offset-3">
								<div id="diasContrato"></div>
								<input type="hidden" id="periodoActualCompleto" value="<?php echo $_SESSION['periodoActualCompleto']; ?>">
	        		</div>
	        	</div>
	      	</div>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- Ventana formulario -->
	<div class="modal inmodal fade" id="ventanaFormulario" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
	  <div class="modal-dialog">
	    <div class="modal-content">
	      <div class="modal-header text-info" style="padding: 15px;">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
	        <h3><i class="fa fa-th-large fa-lg" aria-hidden="true"></i> Asignar menú</h3>
	      </div>
	      <div class="modal-body">
	        <form action="" name="frmCrearDiasContrato" id="frmCrearDiasContrato">
	          <div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="ciclo">Ciclo</label>
									<select class="form-control" name="ciclo" id="ciclo" required>
										<option value="">Seleccione uno</option>
										<option value="1">Ciclo 1</option>
										<option value="2">Ciclo 2</option>
										<option value="3">Ciclo 3</option>
										<option value="4">Ciclo 4</option>
										<option value="5">Ciclo 5</option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="menu">Menú</label>
									<select class="form-control" name="menu" id="menu" required>
										<option value="">Seleccione uno</option>
									</select>
								</div>
							</div>
	          </div>
	          <input type="hidden" name="dia" id="dia">
	          <input type="hidden" name="mes" id="mes">
	          <input type="hidden" name="semana" id="semana">
	          <input type="hidden" name="diaSemana" id="diaSemana">
	          <input type="hidden" name="semanaCompleta" id="semanaCompleta">
	        </form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal">Cancelar</button>
	        <button type="button" class="btn btn-primary btn-sm" id="crearDiaContrato">Aceptar</button>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- Ventana confirmación -->
	<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
	  <div class="modal-dialog modal-sm">
	    <div class="modal-content">
	      <div class="modal-header text-info" style="padding: 15px;">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
	        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
	      </div>
	      <div class="modal-body">
	          <p class="text-center"></p>
	      </div>
	      <div class="modal-footer">
	        <input type="hidden" id="idAConfirmar">
	        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
	        <button type="button" class="btn btn-primary btn-sm" id="btnGuardarPlanillaDiasMes" data-dismiss="modal">Si</button>
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
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/fullcalendar/moment.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/fullcalendar/fullcalendar.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/fullcalendar/locale-all.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/modules/dias_contrato/js/dias_contrato.js"></script>
	<script>
		// Configuración del plugin calendar.
		$('#diasContrato').fullCalendar({
	    locale: 'es-us',
	    header: {
			  left:   'title',
			  center: '',
			  right:  'contratoBtn month prev,next'
			},
			defaultView: 'month',
			weekends: false,
	    // customButtons: {
	    // 	contratoBtn: {
	    // 		text: 'Guardar mes',
	    // 		click: function(){
	    // 			var moment = $('#diasContrato').fullCalendar('getDate');
  			// 		$('#ventanaConfirmar .modal-body p').html('¿Realmente desea guardar el mes actual?');
  			// 		$('#idAConfirmar').val(((moment.month())+1));
  			// 		$('#ventanaConfirmar').modal();

	    // 		}
	    // 	}
	    // },
			validRange: {
				start: $('#periodoActualCompleto').val()+'-<?php echo (strlen($_SESSION["mesPeriodoActual"]) == 1) ? "0".$_SESSION["mesPeriodoActual"] : $_SESSION["mesPeriodoActual"]; ?>-01',
				end: $('#periodoActualCompleto').val()+'-12-31'
			},
	    dayClick: function(date, jsEvent, view)
	    {
	    	calcularSemanaContrato(date);
	    },
	    eventSources:
	    [
		    {
		      events:
		      [
		    	<?php
		    	if (isset($arrayDiasContrato) && $arrayDiasContrato != '')
		    	{
		    		foreach ($arrayDiasContrato as $diasContrato)
		    		{
		    			$dia = (strlen($diasContrato["DIA"]) == 1) ? "0".$diasContrato["DIA"] : $diasContrato["DIA"];
		    	?>
		        {
		          title  : 'Ciclo: <?php echo $diasContrato["CICLO"]; ?> - Menú <?php echo $diasContrato["MENU"]; ?>',
		          start  : '<?php echo $diasContrato["ANO"]; ?>-<?php echo $diasContrato["MES"]; ?>-<?php echo $dia; ?>'
		        },
		    	<?php
		    		}
		    	}
		    	?>
		      ]
		    }
		  ]
	  });
	</script>
	<?php mysqli_close($Link); ?>

</body>
</html>