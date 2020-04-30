$(document).ready(function(){
	// Configuración para la tabla de sedes.
	datatables = $('.dataTablesNovedadesPriorizacion').DataTable({
		ajax: {
			method: 'POST',
			url: 'functions/fn_novedades_menu_buscar_datatables.php'
		},
		columns:[
			{ data: 'mes', className: "text-center" },
			{ data: 'semana', className: "text-center" },
			{ data: 'tipo' },
			{ data: 'tipo_complemento', className: "text-center" },
			{ data: 'grupo_etario' },
			{ data: 'fecha_registro' },
			{ data: 'fecha_vencimiento' },
			{ data: 'estado', className: "text-center" }
		],
		buttons: [ {extend: 'excel', title: 'Sedes', className: 'btnExportarExcel'
		//, exportOptions: { columns: [0,1,2,3,4,5,6,7] } 
	
		} ],
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
	}).on("draw", function(){ $('#loader').fadeOut(); $('.estadoSede').bootstrapToggle(); });

	// Evento para ver
	$(document).on('click', '.dataTablesNovedadesPriorizacion tbody td:nth-child(-n+9)', function(){
		var tr = $(this).closest('tr');
		var datos = datatables.row(tr).data();
		$('#formVerNovedad #idNovedad').val(datos.id);
		$('#formVerNovedad').submit();
	});

	// Evento para cambiar de estado
	$(document).on('change', '.dataTablesSedes tbody input[type=checkbox].estadoSede', function(){
		var tr = $(this).closest('tr');
		var datos = datatables.row( tr ).data();
		confirmarCambioEstado(datos.codigoSede, datos.estadoSede);
	});

	// Evento para editar
	$(document).on('click', '.dataTablesNovedadesPriorizacion tbody .editarSede', function(){
		var tr = $(this).closest('tr');
		var datos = datatables.row( tr ).data();
		editarSede(datos.codigoSede, datos.nombreSede);
	});

	// Botón de acciones para la tabla.
	var botonAcciones = '<div class="dropdown pull-right">'+ '<button class="btn btn-primary btn-sm" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+ 'Nuevo <span class="caret"></span>'+ '</button>'+ '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'; 

	//botonAcciones += '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'; 

	botonAcciones += "<li><a tabindex=\"0\" aria-controls=\"box-table\" href=\"intercambio_alimento.php\">Nuevo intercambio de alimento</a></li>";

	botonAcciones += "<li><a tabindex=\"0\" aria-controls=\"box-table\" href=\"intercambio_preparacion.php\">Nuevo intercambio de preparación</a></li>";

	botonAcciones += "<li><a tabindex=\"0\" aria-controls=\"box-table\" href=\"intercambio_dia_menu.php\">Nuevo intercambio de día de menú</a></li>";

	botonAcciones += '</ul>'+ '</div>';

	$('.containerBtn').html(botonAcciones);
});





















































function crearNovedadPriorizacion(){
	window.open('novedades_priorizacion_crear.php', '_self');
}


function confirmarCambioEstado(codigoSede, estado){
	$('#codigoACambiar').val(codigoSede);
	$('#estadoACambiar').val(estado);

	if(estado){ textoEstado = 'Activar' } else { textoEstado = 'Inactivar'; }

	$('#ventanaConfirmar .modal-body p').html('¿Esta seguro de <strong>' + textoEstado + '</strong> la Sede?');
	$('#ventanaConfirmar').modal();
}

function revertirEstado(){
	$codigoSede = $('#codigoACambiar').val();
	var estado = $('#inputEstadoSede' + $codigoSede).prop('checked');
	if (estado) {
		$('#inputEstadoSede' + $codigoSede).bootstrapToggle('off');
	} else {
		$('#inputEstadoSede' + $codigoSede).bootstrapToggle('on');
	}
}

function cambiarEstado(){
	$.ajax({
		type: "POST",
		dataType: 'json',
		data: {
			codigo: $('#codigoACambiar').val(),
			estado: $('#estadoACambiar').val()
		},
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			if(data.estado == 1){
				Command: toastr.success(
					data.mensaje,
					"Cambio de estado", { onHidden : function(){ $('#loader').fadeOut(); } }
				);
			} else {
				Command: toastr.error(
					data.mensaje,
					"Error al cambiar estado", { onHidden : function(){ $('#loader').fadeOut(); } }
				);
			}
		},
		error: function(data){console.log(data);
			Command: toastr.error(
				"Al parecer existe un error con el servidor. Por favor comuníquese con el adminstrador del sitio InfoPAE.",
				"Error al cambiar estado",
				{ onHidden : function(){ $('#loader').fadeOut(); } }
			);
		}
	});
}
