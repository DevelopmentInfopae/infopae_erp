var itemsActuales = [];
var dataset1;

$(document).ready(function(){

  	// Configuración del pligin toast
	toastr.options = {
		"closeButton": true,
		"debug": false,
		"progressBar": true,
		"preventDuplicates": false,
		"positionClass": "toast-top-right",
		"onclick": null,
		"showDuration": "2000",
		"hideDuration": "2000",
		"timeOut": "2000",
		"extendedTimeOut": "2000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	}

  	jQuery.extend(jQuery.validator.messages, {
    	step:"Por favor ingresa un número entero", 
    	required: "Este campo es obligatorio.", 
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
    	min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") 
  	});

  $('.select2').select2({ width: "resolve" });
  $('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
  $(document).on('ifChecked', '#selectVarios', function(){ $('#box-table-a tbody input[type=checkbox]').iCheck('check'); });
  $(document).on('ifUnchecked', '#selectVarios', function(){ $('#box-table-a tbody input[type=checkbox]').iCheck('uncheck'); });

  	$('#subtipo').change(function(){ // En el cambio de tipo de despacho se buscara el proveedor/empleado segun sea el caso 
		var subtipo = $(this).val();
		buscar_proveedor_empleado(subtipo);
		var subtipoNm = $("#subtipo option:selected").text();
		$('#subtipoNm').val(subtipoNm);
		$('#proveedorEmpleado').select2('val','');
 	});

  $('#proveedorEmpleado').change(function(){ // en cambio de un proveedor/empleado buscamos las bodegas asignadas a ese usuario
    	var usuario = $(this).val();
    	var usuarioNm = $("#proveedorEmpleado option:selected").text();
    	$('#proveedorEmpleadoNm').val(usuarioNm);
    	buscar_bodegas(usuario);
  });

  $('#mes').change(function(){  // en cambio de cada semana buscamos los dias y la semanas de nuevo mes seleccionado
    	var mes = $('#mes').val();
    	var semana = $('#semana').val();
    	buscar_semanas(mes);
    	buscar_dias(mes, semana);
    	// reset a los campos hijos
    	$('#semana').select2('val','');
    	// $('#table tbody tr').remove(); 
		$('#bodyTable').empty(); 
    	$('#rowTable').css('display', 'none');	
  	});

  	$('#semana').change(function(){ // en cambio de cada mes actualizamos los días de la semana y reseteamos el formulario por que en cada semana puede ser difirente la priorizacion
    	var mes = $('#mes').val();
    	var semana = $('#semana').val();
    	buscar_dias(mes, semana);
	 	$('#tipoRacion').select2('val', '');
	 	$('#municipio').select2('val', '');
	 	$('#ruta').select2('val', '');
	 	// $('#table tbody tr').remove(); 
		 $('#bodyTable').empty(); 
	 	// $('#table tbody tr').remove(); 
	 	$('#rowTable').css('display', 'none');
  	});

  	$('#tipoRacion').change(function(){ // en cada cambio de de tipo complemento se buscan los municipios que tengan priorización de ese complemento
    	var tipo = $(this).val();
    	buscar_municipio(tipo);
		// reset a los campos hijos
		$('#tipoDespacho').select2('val', '');
		$('#municipio').select2('val', '');
    	$('#bodyTable').empty(); 
    	// $('#table tbody tr').remove(); 
    	$('#rowTable').css('display', 'none');
  	});

  	$('#municipio').change(function(){
	 	var tipo = $('#tipoRacion').val();
	 	var municipio = $(this).val();
	 	var mes = $('#mes').val();
	 	var semana = $('#semana').val();
	 	buscar_institucion(municipio, tipo, mes, semana);
	 	if($('#municipio').val() != ''){
		 	$('#ruta').val('');
		 	$('#ruta').prop( "disabled", true );
	 	}else{
		 	$('#ruta').prop( "disabled", false );	
	 	}
	 	// reset a los campos hijos
	 	$('#institucion').select2('val', '');
	 	$('#sede').select2('val', '');
  	});

  $('#institucion').change(function(){
		var institucion = $(this).val();
		var tipo = $('#tipoRacion').val();
		var municipio = $('#municipio').val();
		var semana = $('#semana').val();
		var mes = $('#mes').val();
		buscar_sede(semana, municipio, tipo, institucion, mes);
		$('#sede').select2('val', '');
  	});

  	$('#btnAgregar').click(function(){
    	itemsActuales = [];
    	$('#selectVarios').prop( "checked", false );
    	var dias = new Array();
    	var mes = $('#mes').val();
    	var semana = $('#semana').val();
    	var tipo = $('#tipoRacion').val();
		var tipoDespacho = $('#tipoDespacho').val();
    	var municipio = $('#municipio').val();
    	var ruta = $('#ruta').val();
    	var institucion = $('#institucion').val();
    	var sede = $('#sede').val();
    	var consecutivo = $('#box-table-a tbody input[type=checkbox]').length;
    	$('#dias .dia:checked').each(function() {
      	var aux = $(this).val();
      	dias.push(aux);
    	});
    	var bandera=0;

		if(mes == ''){
			bandera++;
			Command: toastr.warning("Debe seleccionar un mes para realizar la orden.", "No hay mes seleccionado.", {onHidden : function(){}});
    		$('#mes').select2('open').select2('close');
		}

		if (dias.length == 0 && bandera == 0) {
			bandera++;
			Command: toastr.warning("Debe seleccionar al menos un día para realizar el despacho.", "No hay día seleccionado.", {onHidden : function(){}});
    		$('#dias').focus();
		}

    	if(tipo == '' && bandera == 0){
			bandera++;
			Command: toastr.warning("Debe seleccionar un complemento para realizar el despacho.", "No hay complemento seleccionado.", {onHidden : function(){}});
    		$('#tipoRacion').select2('open').select2('close');
   	}

		if(tipoDespacho == '' && bandera == 0){
			bandera++;
			Command: toastr.warning("Debe seleccionar un tipo de despacho para realizar el despacho.", "No hay complemento seleccionado.", {onHidden : function(){}});
    		$('#tipoDespacho').select2('open').select2('close');
   	}


    	if(municipio == '' && ruta == '' && bandera == 0){
			bandera++;
			Command: toastr.warning("Debe seleccionar un municipio o ruta para realizar la orden.", "No hay municipio seleccionado.", {onHidden : function(){}});
    		$('#municipio').select2('open').select2('close');
    	}
    
		if(bandera == 0){
			$( "td" ).each(function(){
				var aux = $(this).children('td input[type=hidden]').val();
				if (aux != null) {
					itemsActuales.push(aux);
					consecutivo++;
				}
			});

      	var datos = { "dias" : dias, "mes" : mes, "semana":semana, "municipio":municipio, "ruta":ruta, "tipo":tipo,"institucion":institucion,"sede":sede,"consecutivo":consecutivo,"itemsActuales":itemsActuales};
      	$.ajax({
        		type: "POST",
        		url: "functions/fn_despacho_agregar_items.php",
        		data: datos,
        		beforeSend: function(){
          		$('#loader').fadeIn();
        		},
        		success: function(data){
					$('#rowTable').css('display', 'block')				
					$('#bodyTable').append(data);
					$('.i-checks').iCheck({
						checkboxClass: 'icheckbox_square-green'
					});
					consecutivo = 0;
					$( "td" ).each(function(){
						var aux = $(this).children('td input[type=hidden]').val();
						if (aux != null) {
							consecutivo++;
						}
					});
					$('#mostrando').html('Mostrando ' + consecutivo + ' de ' + consecutivo + ' registros. ')
        		}
     		})
      	.done(function(){ })
      	.fail(function(){ })
      	.always(function(){
        		$('#loader').fadeOut();
      	});
    	}
  	});

  	$('#btnQuitar').click(function(){
    	if ($('#box-table-a tbody input[type=checkbox]').length){
      	$('#selectVarios').prop( "checked", false );
      	$( "#box-table-a tbody input:checked" ).each(function(){
        		$(this).closest('tr').remove();
      	});
			consecutivo = 0;
			$( "td" ).each(function(){
				var aux = $(this).children('td input[type=hidden]').val();
				if (aux != null) {
					consecutivo++;
				}
			});
			$('#mostrando').html('Mostrando ' + consecutivo + ' de ' + consecutivo + ' registros. ')
    	}
  	});
});

// Funcion para crear y guardar el despacho.
function generarDespacho(){
  	var bandera = 0;
  	var itemsDespacho = [];
  	var dias = new Array();
  	var semanasImplicitas = new Array();
  	var semanaDias = new Array();
  	var placa = $('#placa').val();
  	var mes = $('#mes').val();
  	var semana = $('#semana').val();
  	var subtipo = $('#subtipo').val();
  	var tipo = $('#tipoRacion').val();
  	var subtipoNm = $('#subtipoNm').val();
  	var conductor = $('#conductor').val();
  	var tipoDespacho = $('#tipoDespacho').val();
  	var bodegaOrigen = $('#bodegaOrigen').val();
  	var tipoTransporte = $('#tipoTransporte').val();
  	var proveedorEmpleado = $('#proveedorEmpleado').val();
  	var proveedorEmpleadoNm = $('#proveedorEmpleadoNm').val();
  	var auxSemanaImplicita = '';
  	var auxTemporal = '';
  	var auxSemanaDia = '';
  	var auxTemporalDia = '';
  	
	$('#dias .dia:checked').each(function() {
    	var aux = $(this).val();
    	dias.push(aux);
    	auxSemanaImplicita = $(this).parent().prev().val();
    	if (auxSemanaImplicita !== auxTemporal) {
      	semanasImplicitas.push(auxSemanaImplicita);
      	auxTemporal = auxSemanaImplicita;
    	}
  	});
  	var string_variaciones = '';

  	$( "#box-table-a tbody input[type=checkbox]" ).each(function() {
		if ($(this).prop('checked') == true){ 
			itemsDespacho.push($(this).val());
			string_variaciones += $(this).val()+"-"+($(this).data('variacion') == 0 ? 3 : $(this).data('variacion'))+", ";
		}
  	});

  	// Validaciones para generar el despacho
  	if(subtipo == ''){
    	Command: toastr.warning('El campo <strong>tipo de despacho</strong> es obligatorio.', 'Advertencia');
		$('#subtipo').select2('open').select2('close');
   	bandera++;
  	}
	if (proveedorEmpleado == '' && bandera == 0 ) {
    	Command: toastr.warning('El campo <strong>Proveedor / Empleado</strong> es obligatorio.', 'Advertencia');
		$('#proveedorEmpleado').select2('open').select2('close');
    	bandera++;
  	} 
  	if (mes == '' && bandera == 0) {
    	Command: toastr.warning('El campo <strong>mes</strong> es obligatorio.', 'Advertencia');
		$('#mes').select2('open').select2('close');
    	bandera++;
  	} 
  	if (dias.length == 0 && bandera == 0) {
    	Command: toastr.warning('Debe seleccionar al menos un <strong>día</strong> para el despacho.', 'Advertencia');
    	bandera++;
  	} 
	if (itemsDespacho.length == 0 && bandera == 0) {
    	Command: toastr.warning('Debe agregar al menos una <strong>sede</strong> para el despacho.', 'Advertencia');
    	bandera++;
  	} 
	if (bodegaOrigen == '' && bandera == 0) {
    	Command: toastr.warning('El campo <strong>bodega origen</strong> es obligatorio.', 'Advertencia');
		$('#bodegaOrigen').select2('open').select2('close');
    	bandera++;
  	} 
	if (tipoTransporte == '' && bandera == 0 ) {
    	Command: toastr.warning('El campo <strong>tipo de transporte</strong> es obligatorio.', 'Advertencia');
		$('#tipoTransporte').select2('open').select2('close');
    	bandera++;
  	}
  	if(bandera == 0) {
    	$.ajax({
      	type: "POST",
      	url: "functions/fn_despacho_generar.php",
      	dataType: "HTML",
      	data: {
        		"subtipo":subtipo,
        		"subtipoNm":subtipoNm,
        		"proveedorEmpleado":proveedorEmpleado,
        		"proveedorEmpleadoNm":proveedorEmpleadoNm,
        		"mes" : mes,
        		"semana":semana,
        		"dias":dias,
        		"semanaDias":semanasImplicitas,
        		"tipo":tipo,
        		"tipoDespacho":tipoDespacho,
        		"itemsDespacho":itemsDespacho,
        		"itemsDespachoVariaciones":string_variaciones,
        		"bodegaOrigen":bodegaOrigen,
        		"tipoTransporte":tipoTransporte,
        		"placa":placa,
        		"conductor":conductor
      	},
      	beforeSend: function(){
        		$('#loader').fadeIn();
      	}
    	})
    	.done(function(data) {
      	if (data == 1) {
        		Command: toastr.success('El despacho se ha registrado con éxito.','¡Proceso exitoso!',{onHidden : function(){ location.href='despachos.php';}});
      	} else {
        		Command: toastr.error(data, '¡Error en el proceso!');
      	}	
    	})
    	.fail(function(data){
      	console.log(data.responseText);
    	})
    	.always(function(){
      	$('#loader').fadeOut();
    	});
  	}
}

function buscar_bodegas(usuario){
  	var datos = {"usuario":usuario};
  	$.ajax({
    	type: "POST",
    	url: "functions/fn_despacho_buscar_bodegas.php",
    	data: datos,
    	beforeSend: function(){},
    	success: function(data){
      	$('#bodegaOrigen').html(data);
    	}
  	});
}

function buscar_semanas(mes){
  	var datos = {"mes":mes};
  	$.ajax({
    	type: "POST",
    	url: "functions/fn_despacho_buscar_semanas_despacho_nuevo.php",
    	data: datos,
    	beforeSend: function(){},
    	success: function(data){
      	$('#semana').html(data);
    	}
  	});
}

function buscar_dias(mes,semana){
  	var datos = {"mes" : mes,  "semana":semana };
  	$.ajax({
    	type: "POST",
    	url: "functions/fn_despacho_buscar_dias_semana_check.php",
    	data: datos,
    	beforeSend: function(){},
    	success: function(data){
      	$('#dias').html(data);
      	$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
    	}
  	});
}

function buscar_proveedor_empleado(subtipo){
  	var datos = {"subtipo":subtipo};
   $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_proveedor_empleado.php",
      data: datos,
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        $('#proveedorEmpleado').html(data);
		  $('#loader').fadeOut();
      }
   });
}

function buscar_institucion(municipio,tipo){
  	var datos = {"municipio":municipio,"tipo":tipo};
   $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_institucion.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        	$('#institucion').html(data);
      }
   })
   .done(function(){ })
   .fail(function(){ })
   .always(function(){
      $('#loader').fadeOut();
   });
}

function buscar_municipio(tipo){
    var datos = {"tipo":tipo};
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_municipio.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        $('#municipio').html(data);
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
}

function buscar_sede(semana, municipio, tipo, institucion){
    $.ajax({
      type: "POST",
      url: "functions/fn_despacho_buscar_sede.php",
      data: {
        "semana":semana,
        "municipio":municipio,
        "tipo":tipo,
        "institucion":institucion
      },
      beforeSend: function(){
        $('#loader').fadeIn();
      }
    })
    .done(function(data){ $('#sede').html(data); })
    .fail(function(data){ console.log(data); })
    .always(function(){
      $('#loader').fadeOut();
    });
}
