$(document).ready(function(){
	$('.select2').select2();
	
	$('#tabla_bitacora').DataTable({
		buttons: [ {extend: 'excel', title: 'Bitácora Usuarios', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3] } } ],
    	dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    	order: [ 0, 'desc'],
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
    	pageLength: 25,
    	columnDefs: [
    		{width: "20px", targets: 0},
      		{width: "60px", targets: 1},
      		{width: "60px", targets: 2},
      		{width: "80px", targets: 3} 
    	],
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

  		toastr.options = {
		    "closeButton": true,
		    "debug": false,
		    "progressBar": true,
		    "preventDuplicates": false,
		    "positionClass": "toast-top-right",
		    "onclick": null,
		    "showDuration": "400",
		    "hideDuration": "1000",
		    "timeOut": "2000",
		    "extendedTimeOut": "1000",
		    "showEasing": "swing",
		    "hideEasing": "linear",
		    "showMethod": "fadeIn",
		    "hideMethod": "fadeOut"
		}

	$('#fechaFinal').on('change', function(){
		var fechaInicial = $('#fechaInicial').val();
		var fechaFinal = $('#fechaFinal').val();
		if (fechaInicial > fechaFinal) {
			Command: toastr.warning( 'La Fecha Inicial no puede ser mayor a la Fecha Final', "Error al consultar",
          	// 			{
           //  			onHidden : function(){ $('#loader').fadeOut(); }
          	// 			}
        			);
			$('#fechaFinal').val(fechaInicial);
		}
	});	  
});

