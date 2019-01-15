<?php
  include '../../header.php';
  $titulo = 'Usuarios';
?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Novedades de focalización</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong>Novedades de focalización</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
			<?php if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){ ?>
				<a href="#" class="btn btn-primary" onclick="crearNovedadPriorizacion();"><i class="fa fa-plus"></i> Nuevo </a>
			<?php } ?>
		</div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<!-- Seccion de filtros -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">


          <table class="table table-striped table-hover selectableRows dataTablesNovedadesPriorizacion">
            <thead>
              <tr>
                <th>ID</th>
                <th>Municipio</th>
                <th>Institución</th>
                <th>Sede</th>
                <th>Tipo Doc.</th>
                <th>Documento</th>
								<th>Titular</th>
                <th>Complemento</th>
                <th>Semana</th>
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
								<th>ID</th>
								<th>Municipio</th>
								<th>Institución</th>
								<th>Sede</th>
								<th>Tipo Doc.</th>
								<th>Documento</th>
								<th>Titular</th>
								<th>Complemento</th>
								<th>Semana</th>
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
  </div>

</div>


<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p class="text-center"></p>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="codigoACambiar">
        <input type="hidden" id="estadoACambiar">
        <button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
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

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/js/novedades_ejecucion.js"></script>












<script>
  $(document).ready(function(){
		$('#loader').fadeIn();
    // Configuración para la tabla de sedes.
    datatables = $('.dataTablesNovedadesPriorizacion').DataTable({
      ajax: {
        method: 'POST',
        url: 'functions/fn_novedades_focalizacion_index_buscar_datatables.php',
        // data:{
        //   municipio: $('#municipio').val(),
        //   institucion: $('#institucion').val()
        // }
      },
      columns:[
        { data: 'id'},
        { data: 'municipio'},
        { data: 'nom_inst'},
				{ data: 'nom_sede'},
        { data: 'Abreviatura'},
        { data: 'num_doc_titular'},
        { data: 'nombre'},
        { data: 'tipo_complem'},
        { data: 'semana'},
        { data: 'd1'},
        { data: 'd2'},
        { data: 'd3'},
        { data: 'd4'},
        { data: 'd5'}
				// {
				// 	sortable: false,
				// 	"render": function ( data, type, full, meta ) {
				// 		var numDoc = full.num_doc
				// 		var d5 = full.d5;
				// 		var accion = ' <div class="i-checks"><label> <input disabled type="checkbox" name="'+numDoc+'_d5" id="'+numDoc+'_d5" value="1" ';
				// 		if(d5 == 1){
				// 			accion = accion + ' checked ';
				// 		}
				// 		accion = accion + ' > <i></i></label></div> ';
				// 		return accion;
				// 	}
				// }

        // { data: 'codigoInstitucion'},
        // { data: 'nombreInstitucion'},
        // { data: 'nombreCoordinador'},
        // { data: 'nombreJornada'},
        // { data: 'tipoValidacion'},




        //{ defaultContent: '<div class="btn-group">'+ '<div class="dropdown pull-right">'+ '<button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">'+ 'Acciones <span class="caret"></span>'+ '</button>'+ '<ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">'+ '<li>'+ '<a href="#" class="editarSede"><i class="fa fa-pencil fa-lg"></i> Editar</a>'+ '</li>'+ '<li>'+ '<a href="#">'+ 'Estado: &nbsp;'+ '<input type="checkbox" class="estadoSede" data-toggle="toggle" data-on="Activo" data-off="Inactivo" data-size="mini" data-width="70" data-height="24">'+ '</a>'+ '</li>'+ '</ul>'+ '</div>'+ '</div>'}
      ],
      buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel', exportOptions: { columns: [0,1,2,3,4,5,6,7] } } ],
      dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"html5buttons"B>',
      oLanguage: {
        sLengthMenu: 'Mostrando _MENU_ registros',
        sZeroRecords: 'No se encontraron registros',
        sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
        sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
        sInfoFiltered: '(Filtrado desde _MAX_ registros)',
        sSearch:         'Buscar: ',
        oPaginate:{
          sFirst:    'Primero',
          sLast:     'Último',
          sNext:     'Siguiente',
          sPrevious: 'Anterior'
        }
      },
      pageLength: 10,
      responsive: true,
      "preDrawCallback": function( settings ) {
        $('#loader').fadeIn();
      }
    }).on("draw", function(){ $('#loader').fadeOut(); $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', }); });


//$('#loader').fadeOut();





		// Evento para ver
		$(document).on('click', '.dataTablesNovedadesPriorizacion tbody td:nth-child(-n+8)', function(){
			console.log('Click en la fila');
			// var tr = $(this).closest('tr');
			// var datos = datatables.row( tr ).data();
			// $('#formVerNovedad #idNovedad').val(datos.id);
			// $('#formVerNovedad').submit();
		});

		// Evento para cambiar de estado
		$(document).on('change', '.dataTablesSedes tbody input[type=checkbox].estadoSede', function(){
			var tr = $(this).closest('tr');
			var datos = datatables.row( tr ).data();
			alert('Hola');
			confirmarCambioEstado(datos.codigoSede, datos.estadoSede);
		});

    // Evento para editar
    $(document).on('click', '.dataTablesNovedadesPriorizacion tbody .editarSede', function(){
      var tr = $(this).closest('tr');
      var datos = datatables.row( tr ).data();
      editarSede(datos.codigoSede, datos.nombreSede);
    });

		// Botón de acciones para la tabla.
    var botonAcciones = '<div class="dropdown pull-right">'+ '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+ 'Acciones <span class="caret"></span>'+ '</button>'+ '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+ '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>' + '</ul>'+ '</div>';
  $('.containerBtn').html(botonAcciones);
  });
</script>

<form action="novedades_priorizacion_ver.php" method="post" name="formVerNovedad" id="formVerNovedad">
  <input type="hidden" name="idNovedad" id="idNovedad">
</form>

</body>
</html>
