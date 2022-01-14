$(document).ready(function(){
	 
	CargarTablas();

	$(window).on('blur', function(){
		$('#loader').fadeOut();
	});

});

// function arreglarDivs(){
// 	var heights = $(".col-sm-4").map(function() {
//         return $(this).height();
//     }).get(),
//     maxHeight = Math.max.apply(null, heights);
//     $(".col-sm-4").height(maxHeight);
//     $(".col-sm-8").height(maxHeight);
// }

function CargarTablas(){
	$('#loader').fadeIn();
	$.ajax({
		url: 'functions/fn_tabla_bancos.php',
		type: 'POST',
		// dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
		// data: {param1: 'value1'},
		success:function(data){
			console.log(data);
			data = JSON.parse(data);
					$('#tHeadBancos').html(data['thead']);
					$('#tBodyBancos').html(data['tbody']);
					$('#tFootBancos').html(data['tfoot']);
					info = data['info'];
					dataset1 = $('#box-table').DataTable({
					    order: [ 0, 'asc' ],
					    pageLength: 25,
					    responsive: true,
					    dom : 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
					    buttons : [ {extend: 'excel', title: 'Bancos', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } }],
					    oLanguage: {
					      sLengthMenu: 'Mostrando _MENU_ registros por página',
					      sZeroRecords: 'No se encontraron registros',
					      sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
					      sInfoFiltered: '(Filtrado desde _MAX_ registros)',
					      sSearch:         'Buscar: ',
					      sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
					      oPaginate:{
					        sFirst:    'Primero',
					        sLast:     'Último',
					        sNext:     'Siguiente',
					        sPrevious: 'Anterior'
					      }
					    }
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
			$('#loader').fadeOut();	    
		}
	})
	.done(function() {
		console.log("success");
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});
	
}