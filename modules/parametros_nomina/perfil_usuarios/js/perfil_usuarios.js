$(document).ready(function () { 

	$('#box-table').DataTable({
		buttons: [ {extend: 'excel', title: 'Perfil Usuarios', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } } ],
    	dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    	order: [ 0, 'asc'],
    	oLanguage: {
      		sLengthMenu: 'Mostrando _MENU_ registros por página',
      		sZeroRecords: 'No se encontraron registros',
      		sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
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
		}); 

		var botonAcciones = '<div class="dropdown pull-right">'+
                      			'<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        			'Acciones <span class="caret"></span>'+
                      			'</button>'+
                      			'<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        			'<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                      			'</ul>'+
                    		'</div>';
  		$('.containerBtn').html(botonAcciones);

});