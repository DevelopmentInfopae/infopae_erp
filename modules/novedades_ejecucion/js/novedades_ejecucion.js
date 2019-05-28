$(document).ready(function() {
		$('.select2').select2();

    jQuery.extend(jQuery.validator.messages, { required: "Campo obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

		$(document).on('change', '#mes', function() { buscar_semana_mes(); });
		$(document).on('change', '#institucion', function() { buscar_sedes_insitucion(); buscar_complementos(); });
		$(document).on('change', '#sede', function() { buscar_complementos(); });
		$(document).on('change', '#semana', function() { buscar_complementos(); });
		$(document).on('click', '#boton_buscar_novedades', function() { validar_campos_formulario(); });
});

function buscar_sedes_insitucion()
{
	institucion = $('#institucion').val();
	$.ajax({
		url: 'functions/fn_buscar_sedes.php',
		type: 'POST',
		dataType: 'HTML',
		data: {'institucion': institucion},
	})
	.done(function(data)
	{
		$('#sede').html(data);
		$('#sede').select2('val', '');
	})
	.fail(function(data)
	{
		console.log(data.responseText);
	});
}

function buscar_semana_mes()
{
	mes = $('#mes').val();

	$.ajax({
		url: 'functions/fn_buscar_semanas.php',
		type: 'POST',
		dataType: 'HTML',
		data: {'mes': mes},
	})
	.done(function(data) {
		$('#semana').html(data);
		$('#semana').select('val', '');
	})
	.fail(function(data) {
		console.log(data.responseText);
	});
}

function buscar_complementos()
{
	sede = $('#sede').val();
	semana = $('#semana').val();
	institucion = $('#institucion').val();

	if (semana != '')
	{
		$.ajax({
			url: 'functions/fn_buscar_complementos.php',
			type: 'POST',
			dataType: 'HTML',
			data: {
				'sede': sede,
				'semana': semana,
				'institucion': institucion
			},
		})
		.done(function(data)
		{
			$('#tipo_complemento').html(data);
			$('#tipo_complemento').select2('val', '');
		})
		.fail(function(data)
		{
			console.log(data.responseText);
		});
	}
	else
	{
		Command: toastr.warning('Debe seleccionar la semana para obtener los tipos de complementos', 'Mensaje de advertencia.');
	}
}

function validar_campos_formulario()
{
	if ($('#formulario_buscar_novedades').valid())
	{
		buscar_novedades();
	}
}

function buscar_novedades()
{
	sede = $('#sede').val();
	semana = $('#semana').val();
	institucion = $('#institucion').val();
	tipo_complemento = $('#tipo_complemento').val();

	$('.tabla_novedades_focalizacion').DataTable({
    ajax: {
      method: 'POST',
      url: 'functions/fn_novedades_focalizacion_index_buscar_datatables.php',
      data{
      	'sede': sede,
      	'semana': semana,
      	'instituciones': instituciones,
      	'tipo_complemento': tipo_complemento
      },
      success: function(data)
      {
        console.log(data);
      },
      error: function(data)
      {
        console.log(data.responseText);
      }
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
    initComplete: function(settings, json)
    {
        console.log(json);
        $('#loader').fadeOut();
      }
    }).on("draw", function(){ $('#loader').fadeOut(); $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green', radioClass: 'iradio_square-green', }); });

		// Evento para cambiar de estado
		$(document).on('change', '.dataTablesSedes tbody input[type=checkbox].estadoSede', function(){
			var tr = $(this).closest('tr');
			var datos = datatables.row( tr ).data();

			confirmarCambioEstado(datos.codigoSede, datos.estadoSede);
		});

    // Evento para editar
    $(document).on('click', '.dataTablesNovedadesFocalizacion tbody .editarSede', function(){
      var tr = $(this).closest('tr');
      var datos = datatables.row( tr ).data();

      editarSede(datos.codigoSede, datos.nombreSede);
    });

		// Botón de acciones para la tabla.
    var botonAcciones = '<div class="dropdown pull-right">'+ '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+ 'Acciones <span class="caret"></span>'+ '</button>'+ '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+ '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>' + '</ul>'+ '</div>';
    $('.containerBtn').html(botonAcciones);
}

function crearNovedadPriorizacion()
{
  window.open('novedades_ejecucion_crear.php', '_self');
}
