<?php
    include 'header.php';
    if ($_SESSION['dashboard'] != "3") {
        ?><script type="text/javascript">
              window.open('<?= $baseUrl.$_SESSION['rutaDashboard'] ?>', '_self');
        </script>
        <?php exit(); }

    else {
    ?><script type="text/javascript">
        const list = document.querySelector(".li_inicio");
        list.className += " active ";
    </script>
    <?php
    }

    $arrayDiasContrato = [];
    $conDiasContrato = "SELECT * FROM planilla_semanas";
    $resDiasContrato = $Link->query($conDiasContrato);
    if ($resDiasContrato->num_rows > 0) {
        while($regDiasContrato = $resDiasContrato->fetch_assoc())
        $arrayDiasContrato[] = $regDiasContrato;
    }
    // exit(var_dump($_SESSION));
    // exit(var_dump(phpinfo()));
?>

<div class="wrapper wrapper-content contenedor_results">
    <div class="row">
        <div class="col-lg-12"> 
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h1><b>Calendario ejecución<b></h1>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-1"></div>    
                        <div class="col-md-10">
							<div id="diasContrato"></div>
							<input type="hidden" id="periodoActualCompleto" value="<?php echo $_SESSION['periodoActualCompleto']; ?>">
	        		    </div>
                    </div> <!--  row -->
                </div><!--  ibox-content -->
            </div><!--  ibox -->
        </div> <!--  col-lg-12 -->
    </div> <!--  row -->
</div> <!-- wrapper -->




<!-- Mainly scripts -->
<script src="theme/js/jquery-3.1.1.min.js"></script>
<script src="theme/js/bootstrap.min.js"></script>
<script src="theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="theme/js/inspinia.js"></script>
<script src="theme/js/plugins/pace/pace.min.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/fullcalendar/moment.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/fullcalendar/locale-all.js"></script>

<!-- jQuery UI -->
<script src="theme/js/plugins/jquery-ui/jquery-ui.min.js"></script>

<script type="text/javascript">
	$('#diasContrato').fullCalendar({
        // googleCalendarApiKey: 'API DE CALENDARIO',
        locale: 'es',
	    header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,basicWeek,Day',	
		},
		defaultView: 'month',
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
		dayNamesShort: ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'],	
		weekends: false,
		validRange: {
			start: $('#periodoActualCompleto').val()+'-<?php echo (strlen($_SESSION["mesPeriodoActual"]) == 1) ? "0".$_SESSION["mesPeriodoActual"] : $_SESSION["mesPeriodoActual"]; ?>-01',
			end: $('#periodoActualCompleto').val()+'-12-31'
		},
	    dayClick: function(date, jsEvent, view){
	    	// calcularSemanaContrato(date);
	    },
        eventSources:
	    [
		    {
		      events:
		      [
		    	<?php
		    	if (isset($arrayDiasContrato) && $arrayDiasContrato != '') {
		    		foreach ($arrayDiasContrato as $diasContrato){
		    			$dia = (strlen($diasContrato["DIA"]) == 1) ? "0".$diasContrato["DIA"] : $diasContrato["DIA"];
		    	?>
		        {
		            title  : 'Ciclo: <?php echo $diasContrato["CICLO"]; ?> - Menú <?php echo $diasContrato["MENU"]; ?>',
		            start  : '<?php echo $diasContrato["ANO"]; ?>-<?php echo $diasContrato["MES"]; ?>-<?php echo $dia; ?>',
                    classNames : 'text-center'
                    // googleCalendarId: '12345678#holiday@group.v.calendar.google.com',
                    // textColor: 'orange' // an option!
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

</body>
</html>


