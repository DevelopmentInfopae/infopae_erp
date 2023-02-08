$(document).ready(function(){
    consultarInforme()                                                           
})

function consultarInforme(){
	$.ajax({
		type : "post",
		url : "functions/fn_buscar_datatables.php",
		beforeSend : function(){ $('#loader').fadeIn() },
	}).done(function(data){
		if( $.fn.DataTable.isDataTable( '#box-table-movimientos' )){
			dataset1.destroy();
		}
		$('.rowTable').css('display', 'block');
		data = JSON.parse(data);
		$('#table').html(data['tabla'])
        semanas = data['semanas'];
        arraySemanas = [];
        $.each(semanas, function(index, value) {
            arraySemanas.push(value);
        }); 

		arraySemanas.forEach(element => {
            var botonAcciones = '';
		    $('.box-table-'+element).DataTable({
			    pageLength: 25,
				responsive: true,
				dom : 'lr<"containerBtn'+element+'"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
				buttons : [
					$.extend( true, {}, {
						extend: 'excel',
						title:'manipuladoras x sede', 
						className:'btnExportarExcel', 
                        tabindex: element,
						exportOptions: {}
					} ),
					$.extend( true, {}, {
						extend: 'pdfHtml5'
					} )
				],
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
                                        '<li><a tabindex="'+element+'" aria-controls="box-table-'+element+'" href="#" onclick="exportar(\''+element+'\')"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                                    '</ul>'+
                                '</div>';
            $('.containerBtn'+element).html(botonAcciones);	 
	    });
	}).fail(function(data){
		console.log(data);
	}).always(function(){
		$('#loader').fadeOut();
	})
}

function exportar(e) {
    console.log(e);
    $('#semana').val(e);
    $('#formInformes').attr('target','_self');
    $('#formInformes').attr('action', 'informes_xlsx.php');
    // $('#formInformes').attr('method', 'post');
    $('#formInformes').submit();
}