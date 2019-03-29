$(document).ready(function(){
	cargar_semanas($('#mes').val());
	$(document).on('change', '#mes', function (){ cargar_semanas($(this).val()); });
  $(document).on('change', '#semana_inicial', function (){ cargar_semanas($('#mes').val(), $("option:selected", this).val()); });
  $(document).on('change', '#municipio', function(){ buscar_institucion($(this).val()); });
  $(document).on('change', '#institucion', function(){ buscar_sede($(this).val()); });
  $(document).on('change', '#semana_final, #municipio, #institucion, #sede', function(){ buscar_complemento(); });
  $(document).on('click', '#boton_buscar', function(){  });
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
	if($('#formulario_alimentos').valid()) {
		buscar_alimentos();
	}
}

function buscar_alimentos() {
	$('#tablaTrazabilidad').DataTable({
    ajax: {
      method: 'POST',
      url: 'functions/fn_buscar_alimentos.php',
      dataType: 'HTML',
      data:{
    		mes: $('#mes').val(),
				sede: $('#sede').val(),
				municipio: $('#municipio').val(),
				institucion: $('#institucion').val(),
				semana_final: $('#semana_final').val(),
				semana_inicial: $('#semana_inicial').val()
				// ruta: $('#ruta').val(),
      }
    },
    columns:[
      { data: 'CodigoProducto'},
      { data: 'Descripcion'},
      { data: 'Cantidad'},
      { data: 'CantidadPresentacion'},
      { data: 'CantU2'},
      { data: 'Umedida2'},
      { data: 'CantU3'},
      { data: 'Umedida3'},
      { data: 'CantU4'},
      { data: 'Umedida4'},
      { data: 'CantU5'},
      { data: 'Umedida5'},
    ],
    destroy: true,
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Menus', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7,8,9,10,11]}}],
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
    initCompleted: function(){
    	$('#loader').fadeOut();
    }
  })/*.on("draw", function(){
    $('.checkDespacho').iCheck({ checkboxClass: 'icheckbox_square-green' });
    $('#selecTodos').on('ifChecked', function(){ $('.checkDespacho').iCheck('check'); });
    $('#selecTodos').on('ifUnchecked', function(){ $('.checkDespacho').iCheck('uncheck'); });
    $('.checkDespacho').on('ifChecked', function(){ $('#sede_'+$(this).data('num')).prop('checked', true); });
    $('.checkDespacho').on('ifUnchecked', function(){ $('#sede_'+$(this).data('num')).prop('checked', false); });
  })*/;

  var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';
  $('.containerBtn').html(btnAcciones);
	// $.ajax({
	// 	url: 'functions/buscar_alimentos.php',
	// 	type: 'POST',
	// 	dataType: 'HTML',
	// 	data: {

	// 	},
	// })
	// .done(function(data) {
	// 	console.log("success");
	// })
	// .fail(function() {
	// 	console.log("error");
	// })
	// .always(function() {
	// 	console.log("complete");
	// });

}