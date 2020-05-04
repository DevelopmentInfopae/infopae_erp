$(document).ready(function(){
	dataset1 = $('#box-table-movimientos').DataTable({
		order: [ 1, 'desc' ],
		dom: 'lr<"inputFiltro"f>tip',
		pageLength: 100,
		lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "TODO"]],
		responsive: true,
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
		}
	});
});

