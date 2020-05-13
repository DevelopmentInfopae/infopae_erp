var datatables = null;

$(document).ready(function(){
	// dataset1 = $('#box-table-movimientos').DataTable({
	// 	order: [ 1, 'desc' ],
	// 	dom: 'lr<"inputFiltro"f>tip',
	// 	pageLength: 100,
	// 	lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "TODO"]],
	// 	responsive: true,
	// 	oLanguage: {
	// 		sLengthMenu: 'Mostrando _MENU_ registros por página',
	// 		sZeroRecords: 'No se encontraron registros',
	// 		sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
	// 		sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
	// 		sInfoFiltered: '(Filtrado desde _MAX_ registros)',
	// 		sSearch:         'Buscar: ',
	// 		oPaginate:{
	// 			sFirst:    'Primero',
	// 			sLast:     'Último',
	// 			sNext:     'Siguiente',
	// 			sPrevious: 'Anterior'
	// 		}
	// 	}
	// });

	cargarRegistros();
	
});



// box-table-movimientos



function cargarRegistros(){
	console.log('Cargar registros');

	console.log(datatables);
	if(datatables !== null){
		if ( $.fn.DataTable.isDataTable( '#box-table-movimientos' ) ) { datatables.destroy(); }
	}
	
	datatables = $('.box-table-movimientos').DataTable({
	ajax: {
		method: 'POST',
		url: 'functions/fn_buscar_registros.php'
	},
	columns:[
		{ data: 'fecha',className: "fecha-hora"},
		    {
    	sortable: false,
    	className: "textoCentrado",
    	"render": function ( data, type, full, meta ) {

			var evento = full.evento;
    		// var tipoDocumento = full.tipo_doc;
    		// var documento = full.num_doc;
    		// var asistencia = full.asistencia;
    		// var index = auxRepitentes.indexOf(documento);
    		// if (index > -1) {opciones = "Si"; }else{opciones = "No"; }
            

    		// var repite = full.repite; 
    		var opciones = ""; 
			// if (repite == 1) {opciones = "Si"; }else{opciones = "No"; }

			opciones = "<span class=\""+evento+"\">"+evento+"</span>";
			
    		return opciones;
			}

    	},
		{ data: 'Nombre',className: ""},
		{ data: 'Nitcc',className: "documento"},
		{ data: 'cargo',className: "cargo-tabla"}
	],
	bSort: false,
	bPaginate: false,

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
	//pageLength: 10,
	responsive: true,
	"preDrawCallback": function( settings ) {
		//$('#loader').fadeIn();
	}
	}).on("draw", function(){
		//$('#loader').fadeOut();
		
	});

	setTimeout(cargarRegistros,5000);
}