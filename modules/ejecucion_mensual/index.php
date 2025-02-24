
<?php
    include '../../header.php';

    if ($permisos['informes'] == "0" || $permisos['informes'] == "1") {
?>      <script type="text/javascript">
            window.open('<?= $baseUrl ?>', '_self');
        </script>
<?php 
        exit(); 
    } else {
?>  <script type="text/javascript">
        const list = document.querySelector(".li_informes");
        list.className += " active ";
        const list2 = document.querySelector(".li_informeEjecucionMensual");
        list2.className += " active ";
    </script>
<?php
    }
    $nameLabel = get_titles('informes', 'informeEjecucionMensual', $labels);
    $titulo = $nameLabel;
    $periodoActual = $_SESSION['periodoActual'];
    $grupos_etarios = $_SESSION['cant_gruposEtarios'];

    // primero buscamos los meses activos de las planillas
    $respuestaMeses = $Link->query(" SELECT DISTINCT MES AS mes FROM planilla_semanas ");
    if ($respuestaMeses->num_rows > 0) {
        while ($dataMeses = $respuestaMeses->fetch_object()) {
            $meses[] = $dataMeses;
            $ultimoMes = $dataMeses->mes;
        }
        $nomMes = array( "01" => "ENERO", 
                        "02" => "FEBRERO", 
                        "03" => "MARZO", 
                        "04" => "ABRIL", 
                        "05" => "MAYO", 
                        "06" => "JUNIO", 
                        "07" => "JULIO", 
                        "08" => "AGOSTO", 
                        "09" => "SEPTIEMBRE", 
                        "10" => "OCTUBRE", 
                        "11" => "NOVIEMBRE", 
                        "12" => "DICIEMBRE");                
    }

?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading contendor_titulos">
	<div class="col-md-6 col-lg-8">
		<h1><strong><?= $nameLabel; ?></strong></h1>
		<ol class="breadcrumb">
		  	<li>
				<a href="<?php echo $baseUrl.$_SESSION['rutaDashboard']; ?>">Inicio</a>
		  	</li>
		  	<li class="active">
				<strong><?= $nameLabel ?></strong>
		  	</li>
		</ol>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight contenedorParametros">
    <div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-sm-11">
                            <h2 style="display:inline;">Parámetros de Consulta</h2>
                        </div>
                        <div class="col-sm-1" id="father">
                            <div class="" id="loaderAjax">
                                <i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i>
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>    
				<div class="ibox-content contentBackground">
					<form class="col-lg-12" action="index.php" name="formEjecucionMensual" id="formEjecucionMensual" method="GET" >
						<div class="row">
                            <div class="col-md-3 col-sm-12 form-group form-mes">
                                <label for="mes">Mes*</label>
                                <select name="mes" id="mes" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php
                                        foreach ($meses as $key => $value) {
                                    ?>
                                            <option value="<?= $value->mes ?>" <?= (isset($_GET['mes']) && $_GET['mes'] == $value->mes) ? 'selected' : '' ?> ><?= $nomMes[$value->mes] ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>       
                        </div><!--  row -->

                        <div class="row">
                            <div class="col-md-3 col-sm-12 form-group">
                                <button class="btn_generates" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong><i class="fa fa-search"></i> Buscar</strong></button>
                            </div>
                        </div>
                    </form>        
                </div><!--  ibox-content -->
            </div><!--  ibox -->
        </div><!--  col-lg-12 -->
    </div><!--  row -->
</div><!--  wrapper -->

<?php if(isset($_GET['mes']) && $_GET['mes']!= ''): ?>
    <div class="wrapper wrapper-content animated fadeInRight contenedor_results" id='date_table' >
        <div class="row">
            <div class="col-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content contentBackground">		
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered  selectableRows" id="box-table-movimientos" >
                                <thead>
                                    <tr>
                                        <th id='title_ejecution'  class="text-center">EJECUCIÓN DEL RECURSO</th>
                                        <th  class="text-center">CONTRATO</th>
                                        <?php  foreach($meses as $key => $value){?>
                                            <th  class="text-center"><?= "EJECUCIÓN " . $nomMes[$value->mes] ?></th>                                            
                                            <?php if ($_GET['mes'] ==  $value->mes) {break;}  ?>
                                        <?php } ?> 
                                        <th  class="text-center">TOTAL EJECUTADO A LA FECHA</th>
                                        <th  class="text-center">% DE EJECUCIÓN FINANCIERA</th>
                                        <th  class="text-center">TOTAL POR EJECUTAR</th>
                                    </tr>
                                    
                                </thead>
                                <br>
                                <tbody id="tbodyInformeEjecucion">
                                    <tr></tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center">EJECUCIÓN DEL RECURSO</th>
                                        <th class="text-center">CONTRATO</th>
                                        <?php  foreach($meses as $key => $value){?>
                                            <th  class="text-center"><?= "EJECUCIÓN " . $nomMes[$value->mes] ?></th>                                            
                                            <?php if ($_GET['mes'] ==  $value->mes) {break;}  ?>
                                        <?php } ?> 
                                        <th class="text-center">TOTAL EJECUTADO A LA FECHA</th>
                                        <th class="text-center">% DE EJECUCIÓN FINANCIERA</th>
                                        <th class="text-center">TOTAL POR EJECUTAR</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div><!-- /.ibox-content -->
                </div><!-- /.ibox float-e-margins -->
            </div><!-- /.col-lg-12 -->
        </div><!-- /.row -->
    </div><!-- /.wrapper wrapper-content animated fadeInRight -->
<?php endif; ?>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<!--  -->
<script type="text/javascript">
    $(document).ready(function() {
        
        $('select').select2();

        // validamos y enviamos el formulario
        $('#btnBuscar').click(function(){
            $('#formEjecucionMensual').submit();
        })

        bandera = 0;

        // manejo de mes formulario 
        if($('#mes').val() == ''){
			bandera++;
			Command: toastr.warning("Debe seleccionar un <strong>mes</strong> para realizar el informe.", "No hay mes seleccionado.", {onHidden : function(){}});
    		$('#mes').select2('open').select2('close');
            $('#mes').parent('.form-mes').find('.select2-selection--single').css('border-color', '#FF5252')
		}else{
            var mes = $('#mes').val();
            $('#mes').parent('.form-mes').find('.select2-selection--single').css('border-color', '#e7eaec')
        }
        
        if (bandera == 0) {        
            datosForm = {
                "mes" : mes,
            }
                
            dataset1 = $('#box-table-movimientos').DataTable({    
                ajax: {
                    method: 'POST',
                    url: 'functions/fn_get_datatable.php',
                    data:{
                        datos: datosForm
                    }
                },
                columns:[
                    {
                        className: "text-center",
                        data: 'title',
                        render: function(data, type, row) {
                            if (data) {
                                return data;  
                            } else {
                                return null;  // Si no tiene valor, no renderizamos nada
                            }
                        }
                    },
                    { className: "text-center", data: 'contract'},
                    <?php  foreach($meses as $key => $value){?>
                        { className: "text-center", data: 'ejecution_<?= $value->mes ?>'},                                         
                        <?php if (isset($_GET['mes']) && $_GET['mes'] ==  $value->mes) {break;}  ?>
                    <?php } ?> 
                    { className: "text-center ", data: 'total_ejecution'},
                    { className: "text-center ", data: 'total_percentage'},
                    { className: "text-center ", data: 'total_to_execute'},
                ],
                pageLength: 25,
                responsive: true,
                dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
                buttons : [ {extend: 'excel', title: 'Cobertura mensual', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8] } }],
                // order: [ 1, 'asc'],
                ordering: false,
                oLanguage: {
                    sLengthMenu: 'Mostrando _MENU_ registros por página',
                    sZeroRecords: 'No se encontraron registros',
                    sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
                    sInfoFiltered: '(Filtrado desde _MAX_ registros)',
                    sSearch:         '_INPUT_',
                    sSearchPlaceholder : 'Buscar: ',
                    oPaginate:{
                        sFirst:    'Primero',
                        sLast:     'Último',
                        sNext:     'Siguiente',
                        sPrevious: 'Anterior'
                    }
                },
                initComplete: function() {
                    var btnAcciones = '<div class="dropdown pull-right" id=""> ' +
                              '<button class="btn_options_table" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true"><span class="fa fa-ellipsis-v"></span></button>'+
                              '<ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">'+
                                    '<li><a href="#" onclick="export_informe()"> <i class="fa fa-file-excel-o"></i> &nbsp Exportar Tabla </a></li>'+
                              '</ul>'+
                           '</div>';
                    $('.containerBtn').html(btnAcciones);
                    $('#loader').fadeOut();
                    setTimeout(function() {
                        $('#title_ejecution').attr('rowspan', 2);  // Aplica el rowspan después de un pequeño retraso
                    }, 50);  // Retraso de 50ms
                }, 
                preDrawCallback: function( settings ) {
                    $('#loader').fadeIn();
                },
                drawCallback: function(settings) {
                }
            }).on("draw", function(){ 
                $('#loader').fadeOut();
            })
        }	    
    })

    const export_informe = () => {
        $('#formEjecucionMensual').attr('target','_self');
		$('#formEjecucionMensual').attr('action', 'ejecucion_mensual_xlsx.php');
		$('#formEjecucionMensual').attr('method', 'post');
		$('#formEjecucionMensual').submit();
		$('#formEjecucionMensual').attr('target','_blank');
		$('#formEjecucionMensual').attr('method', 'get');
    }
</script>

