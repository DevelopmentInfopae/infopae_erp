$(document).ready(function(){
	// Configuración para la validación del formulario de búsqueda de sedes.
  jQuery.extend(jQuery.validator.messages, { required: "Campo obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
	cargar_semanas($('#mes').val());

	$(document).on('change', '#mes', function (){ cargar_semanas($(this).val()); });
  $(document).on('change', '#semana_inicial', function (){ cargar_semanas($('#mes').val(), $("option:selected", this).val()); });
  $(document).on('change', '#municipio', function(){ buscar_institucion($(this).val()); });
  $(document).on('change', '#institucion', function(){ buscar_sede($(this).val()); });
  $(document).on('change', '#semana_final, #municipio, #institucion, #sede', function(){ buscar_complemento(); });
  $(document).on('click', '#boton_buscar', function(){ validar_formulario_alimentos(); });

  $('#municipio').trigger('change');
});

function cargar_semanas($mes, $semana = '') {
	$.ajax({
		url: 'functions/fn_buscar_semanas_mes.php',
		type: 'POST',
		dataType: 'HTML',
		data: {
			'mes': $mes,
			'semana' : $semana
		},
	})
	.done(function(data) {
		if ($semana != '') {
			$('#semana_final').html(data);
			$('#semana_final').val("");
			$('#tipo_complemento').html('<option value="">Seleccione</option>');
			buscar_complemento();
		} else {
			$('#semana_inicial').html(data);
			$('#semana_final').html('<option value="">Seleccione</option>');

			if ($("#municipio").val() == "" && $("#institucion").val() == "" && $("#sede").val() == "") {
				$('#tipo_complemento').html('<option value="">Seleccione</option>');
			} else {
				buscar_complemento();
			}
		}

	})
	.fail(function(data) {
		console.log(data.responseText);
	});
}

function buscar_institucion(municipio){
  $.ajax({
    type: 'POST',
    url: 'functions/fn_buscar_institucion_municipio.php',
    data: { 'municipio': municipio }
  })
  .done(function(data){ $('#institucion').html(data); })
  .fail(function(data){ console.log(data.responseText); });
}

function buscar_sede(institucion){
    $.ajax({
      type: 'POST',
      url: 'functions/fn_buscar_sedes_institucion.php',
      dataType: 'HTML',
      data: { 'institucion': institucion }
    })
    .done(function(data){ $('#sede').html(data); })
    .fail(function(data){ data.responseText; });
}

function buscar_complemento(){
	$.ajax({
		url: 'functions/fn_buscar_complemento.php',
		type: 'POST',
		dataType: 'HTML',
		data: {
			mes: $('#mes').val(),
			sede: $('#sede').val(),
			municipio: $('#municipio').val(),
			institucion: $('#institucion').val(),
			semana_final: $('#semana_final').val(),
			semana_inicial: $('#semana_inicial').val()
		},
	})
	.done(function(data) {
		$('#tipo_complemento').html(data);
	})
	.fail(function(data) {
		console.log(data.responseText);
	});
}

function validar_formulario_alimentos() {
	if($('#formulario_buscar_alimentos').valid()) {
		buscar_alimentos();
	}
}

function buscar_alimentos() {
	$('#loader').fadeIn();

	$('#tabla_productos').DataTable({
    ajax: {
      method: 'POST',
      url: 'functions/fn_buscar_alimentos.php',
      data:{
    		mes: $('#mes').val(),
				sede: $('#sede').val(),
				municipio: $('#municipio').val(),
				institucion: $('#institucion').val(),
				semana_final: $('#semana_final').val(),
				semana_inicial: $('#semana_inicial').val(),
				tipo_complemento: $('#tipo_complemento').val()
      }
    },
    columns:[
      { data: 'codigo_producto'},
      { data: 'descripcion'},
      { data: 'cantidad_requerida', className: "text-right"},
      { data: 'cantidad_presentacion', className: "text-right"},
      { data: 'cantidad_unidad_1', className: "text-right"},
      { data: 'nombre_unidad_1'},
      { data: 'cantidad_unidad_2', className: "text-right"},
      { data: 'nombre_unidad_2'},
      { data: 'cantidad_unidad_3', className: "text-right"},
      { data: 'nombre_unidad_3'},
      { data: 'cantidad_unidad_4', className: "text-right"},
      { data: 'nombre_unidad_4'}
    ],
    destroy: true,
    pageLength: 25,
    responsive: true,
    buttons : [{extend:'excel', title:'Alimentos', className:'btnExportarExcel'}],
    dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
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
		initComplete: function() {
		  var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';
		  $('.containerBtn').html(btnAcciones);
			$('#loader').fadeOut();
		}
  });
}