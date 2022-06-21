$(document).ready(function(){

	datatables = $('.dataTablesNovedadesPriorizacion').DataTable({
		buttons: [ {	
					extend: 'excel', 
					title: 'Novedades menu', 
					className: 'btnExportarExcel', 
					exportOptions: { 
						columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ] 
					} 
				} ],
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
    	responsive: true,
    	columnDefs: [
    		{
        		targets: 0, // your case first column
        		className : "text-center",
   			},
   			{
        		targets: 1, // your case first column
        		className : "text-center",
   			},
   			{
        		targets: 2, // your case first column
        		className : "text-center",
   			}
   		]
	}); 

	// Evento para ver
	$(document).on('click', '.dataTablesNovedadesPriorizacion tbody td:nth-child(-n+9)', function(){
		var tr = $(this).closest('tr');
		var datos = datatables.row(tr).data(); 
		$('#formVerNovedad #idNovedad').val(datos[0]);
		$('#formVerNovedad').submit();
	});

	// Botón de acciones para la tabla.
	var botonAcciones = '<div class="dropdown pull-right">'+ '<button class="btn btn-primary btn-sm" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+ 'Nuevo <span class="caret"></span>'+ '</button>'+ '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'; 
	botonAcciones += "<li><a tabindex=\"0\" aria-controls=\"box-table\" href=\"intercambio_alimento.php\">Nuevo intercambio de alimento</a></li>";
	botonAcciones += "<li><a tabindex=\"0\" aria-controls=\"box-table\" href=\"intercambio_preparacion.php\">Nuevo intercambio de preparación</a></li>";
	botonAcciones += "<li><a tabindex=\"0\" aria-controls=\"box-table\" href=\"intercambio_dia_menu.php\">Nuevo intercambio de día de menú</a></li>";
	botonAcciones += '</ul>'+ '</div>';

	var opciones = $('#opcion').val();
	if (opciones == 2) {
		$('.containerBtn').html(botonAcciones);
	}

	if(sessionStorage.getItem("infopae_mes") != null){
		$( "#mes" ).val(sessionStorage.getItem("infopae_mes"));
		cargarSemanas();	
	}

	$( "#mes" ).change(function() {
		sessionStorage.setItem("infopae_mes", $("#mes").val());
		cargarSemanas();
	});

	if(sessionStorage.getItem("infopae_semana") != null){
		$( "#semana" ).val(sessionStorage.getItem("infopae_semana"));
	}

	$( "#semana" ).change(function() {
		sessionStorage.setItem("infopae_semana", $("#semana").val());
	});

	if(sessionStorage.getItem("infopae_estado") != null){
		$( "#estado" ).val(sessionStorage.getItem("infopae_estado"));
	}

	$( "#estado" ).change(function() {
		sessionStorage.setItem("infopae_estado", $("#estado").val());
	});

	if(sessionStorage.getItem("infopae_complemento") != null){
		$( "#complemento" ).val(sessionStorage.getItem("infopae_complemento"));
	}

	$( "#complemento" ).change(function() {
		sessionStorage.setItem("infopae_complemento", $("#complemento").val());
	});

	if(sessionStorage.getItem("infopae_tipoNovedad") != null){
		$( "#tipoNovedad" ).val(sessionStorage.getItem("infopae_tipoNovedad"));
	}

	$( "#tipoNovedad" ).change(function() {
		sessionStorage.setItem("infopae_tipoNovedad", $("#tipoNovedad").val());
	});

});

function cargarSemanas(){
	var formData = new FormData();
	formData.append('mes', $('#mes').val());
	$.ajax({
		type: "post",
		url: "functions/fn_buscar_semanas.php",
		dataType: "json",
		contentType: false,
		processData: false,
		data: formData,
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				$('#semana').html(data.opciones);
				$('#semana').val(sessionStorage.getItem("infopae_semana"));
				sessionStorage.setItem("infopae_semana", $("#semana").val());
				$('#loader').fadeOut();
			}
			else{
				Command:toastr.error(data.mensaje,"Error",{onHidden:function(){$('#loader').fadeOut();}});
			}
		},
		error: function(data){
			console.log(data);
			Command:toastr.error("Al parecer existe un problema con el servidor.","Error en el Servidor",{onHidden:function(){$('#loader').fadeOut();}});
		}
	});
}


	