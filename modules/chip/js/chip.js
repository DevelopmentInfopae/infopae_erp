$(document).ready(function(){
    $('select').select2();
    jQuery.extend(jQuery.validator.messages, { 	required: "Campo obligatorio.", 
												remote: "Por favor, rellena este campo.", 
												email: "Por favor, escribe una dirección de correo válida", 
												url: "Por favor, escribe una URL válida.", 
												date: "Por favor, escribe una fecha válida.", 
												dateISO: "Por favor, escribe una fecha (ISO) válida.", 
												number: "Por favor, escribe un número entero válido.", 
												digits: "Por favor, escribe sólo dígitos.", 
												creditcard: "Por favor, escribe un número de tarjeta válido.", 
												equalTo: "Por favor, escribe el mismo valor de nuevo.", 
												accept: "Por favor, escribe un valor con una extensión aceptada.", 
												maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), 
												minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), 
												rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), 
												range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), 
												max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), 
												min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
                                                                
    $('#btnBuscar').click(function(event){ 
        event.preventDefault();
        var tipoInforme = $('#tipoInforme').val();
        consultarInforme(tipoInforme); 
    })                                                                
})

function consultarInforme(tipoInforme){
    if($('#formInformes').valid()){
		datos = $('#formInformes').serialize(),
		$.ajax({
			type : "post",
			url : "functions/fn_buscar_datatables.php",
			data : datos,
			beforeSend : function(){ $('#loader').fadeIn() },
		}).done(function(data){
            // console.log(data);
			if( $.fn.DataTable.isDataTable( '#box-table-movimientos' )){
				dataset1.destroy();
			}
			$('.rowTable').css('display', 'block');
			data = JSON.parse(data);
			$('#table').html(data['tabla'])
 
			if (tipoInforme == 1 || tipoInforme ==2 ) {
				dataset1 = $('#box-table-movimientos').DataTable({
					pageLength: 25,
					responsive: true,
					dom : 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
					buttons : [
						$.extend( true, {}, {
						   extend: 'excel',
						   title:'CHIP', 
						   className:'btnExportarExcel', 
						   exportOptions: {}
						} ),
						$.extend( true, {}, {
						   extend: 'excelHtml5'
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
			}
			if (tipoInforme == 3) {
				var mes = $('#mes').val();
				arrayMes = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
				arrayMes.forEach(element => {
					if (parseInt(element) <= parseInt(mes)) {
						$('.box-table-'+element).DataTable({
							pageLength: 10,
							responsive: true,
							dom : 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
							buttons : [
								$.extend( true, {}, {
								   extend: 'excel',
								   title:'CHIP', 
								   className:'btnExportarExcel', 
								   exportOptions: {}
								} ),
								$.extend( true, {}, {
								   extend: 'excelHtml5'
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
					}
				});
				$('.box-table-T').DataTable({
					pageLength: 10,
					responsive: true,
					dom : 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
					buttons : [
						$.extend( true, {}, {
						   extend: 'excel',
						   title:'CHIP', 
						   className:'btnExportarExcel', 
						   exportOptions: {}
						} ),
						$.extend( true, {}, {
						   extend: 'excelHtml5'
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
			}
			
			
			var botonAcciones = '<div class="dropdown pull-right">'+
                      				'<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        				'Acciones <span class="caret"></span>'+
                      				'</button>'+
                      				'<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        				'<li><a tabindex="0" aria-controls="box-table" href="#" onclick="exportar()"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                      				'</ul>'+
                    				'</div>';
  			$('.containerBtn').html(botonAcciones);	 
		}).fail(function(data){
			console.log(data);
		}).always(function(){
			$('#loader').fadeOut();
		})
	}
}

function exportar() {
    var tipo = $('#tipoInforme').val();
    $('#formInformes').attr('target','_self');
    $('#formInformes').attr('action', 'informes_xlsx.php');
    $('#formInformes').attr('method', 'post');
    $('#formInformes').submit();
    $('#formInformes').attr('target','_blank');
}