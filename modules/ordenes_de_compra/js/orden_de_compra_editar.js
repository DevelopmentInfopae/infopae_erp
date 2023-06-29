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

	jQuery.extend(jQuery.validator.messages, {step:"Por favor ingresa un número entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", 
      email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", 
      date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", 
      number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", 
      creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", 
      accept: "Por favor, escribe un valor con una extensión aceptada.", 
      maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), 
      minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), 
      rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), 
      range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), 
      max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), 
      min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") 
   });

	$('.select2').select2({ width: "resolve" });
	$('.mes').select2();
	$('.semana').select2();
	$('.tipoAlimento').select2();
	$('.proveedor').select2();
	$('.municipio').select2();

	$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
	$(document).on('ifChecked', '#selectVarios', function(){ $('#table tbody input[type=checkbox]').iCheck('check'); });
	$(document).on('ifUnchecked', '#selectVarios', function(){ $('#table tbody input[type=checkbox]').iCheck('uncheck'); });

    buscar_sedes_editar($('#Num_OCO').val());

    var mes = $('#mesE').val();
    var semana = $('#semana').val();
    buscar_semanas(mes);
    buscar_dias(mes, semana);

    var tipo = $('#tipoComplem').val();
	var municipioEdit = $('#municipio_edit').val();

    buscar_municipio(tipo, municipioEdit);

	$('#tipoComplem').change(function(){
		var tipo = $(this).val();
		buscar_municipio(tipo, null);

		// reset a los campos hijos
		$('#tipoDespacho').select2('val', '');
		$('#proveedorEmpleado').select2('val', '');
		$('#municipio').select2('val', '');
	});

	$('#tipoDespacho').change(function(){
		var tipoAlimento = $(this).val();
		buscar_proveedores(tipoAlimento);

		// reset a los campos hijos
		$('#proveedorEmpleado').select2('val', '');
	});

	$('#proveedorEmpleado').change(function(){
		var usuario = $(this).val();
		var usuarioNm = $("#proveedorEmpleado option:selected").text();
		$('#proveedorEmpleadoNm').val(usuarioNm);
	});

	$('#ruta').change(function(){
		if($('#ruta').val() != ''){
			$('#municipio').select2('val', '');
			$('#municipio').prop( "disabled", true );
			$('#institucion').prop( "disabled", true );
			$('#sede').prop( "disabled", true );
		}else{
			$('#municipio').prop( "disabled", false );
			$('#institucion').prop( "disabled", false );
			$('#sede').prop( "disabled", false );	
		}
	});

	$('#municipio').change(function(){
		var tipo = $('#tipoComplem').val();
		var municipio = $(this).val();
		var mes = $('#mes').val();
		var semana = $('#semana').val();
		buscar_institucion(municipio, tipo, mes, semana);
		if($('#municipio').val() != ''){
			$('#ruta').select2('val', '');
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
		var mes = $('#mes').val();
		var semana = $('#semana').val();
		var diasActuales = new Array();
		var tipo = $('#tipoComplem').val();
		var tipoDespacho = $('#tipoDespacho').val();
		var proveedor = $('#proveedorEmpleado').val();
		var municipio = $('#municipio').val();
		var ruta = $('#ruta').val();
		var institucion = $('#institucion').val();
		var sede = $('#sede').val();
		var bandera=0;
		var consecutivo = 0;

		if(mes == ''){
			bandera++;
			Command: toastr.warning("Debe seleccionar un mes para realizar la orden.", "No hay mes seleccionado.", {onHidden : function(){}});
    		$('#mes').select2('open').select2('close');
		}

		$('#dias .dia:checked').each(function() {
			var aux = $(this).val();
			diasActuales.push(aux);
		});
		if (diasActuales.length == 0 && bandera == 0) {
			bandera++;
			Command: toastr.warning("Debe seleccionar al menos un día para realizar la orden.", "No hay día seleccionado.", {onHidden : function(){}});
    		$('#dias').focus();
		}

		if (tipo == '' && bandera == 0) {
			bandera++;
			Command: toastr.warning("Debe seleccionar un tipo de ración para realizar la orden.", "No hay tipo de ración seleccionada.", {onHidden : function(){}});
    		$('#tipoComplem').select2('open').select2('close');
		}

		if (tipoDespacho == '' && bandera == 0) {
			bandera++;
			Command: toastr.warning("Debe seleccionar un tipo de alimento para realizar la orden.", "No hay tipo de alimento seleccionado.", {onHidden : function(){}});
    		$('#tipoDespacho').select2('open').select2('close');
		}

		if (proveedor == '' && bandera == 0) {
			bandera++;
			Command: toastr.warning("Debe seleccionar un proveedor para realizar la orden.", "No hay proveedor seleccionado.", {onHidden : function(){}});
    		$('#proveedorEmpleado').select2('open').select2('close');
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

			var datos = {   "dias" : diasActuales, 
                            "mes":mes, 
                            "semana":semana, 
                            "municipio":municipio, 
                            "ruta":ruta, 
                            "tipo":tipo, 
                            "institucion":institucion, 
                            "sede":sede, 
                            "consecutivo":consecutivo, 
                            "itemsActuales":itemsActuales};
			$.ajax({
				type: "POST",
				url: "functions/fn_agregar_items.php",
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
		if ($('#table tbody input[type=checkbox]').length){
			$('#selectVarios').prop( "checked", false );
			$( "#table tbody input:checked" ).each(function(){
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
function editarDespacho(){
    var num_oco = $('#Num_OCO').val();
	var bandera = 0;
	var itemsDespacho = [];
	var mes = $('#mesE').val();
	var semana = $('#semana').val();
	var dias = new Array();
	var semanaDias = new Array();
  	var tipo = $('#tipoComplem').val();
	var tipoDespacho = $('#tipoDespacho').val();
	var proveedorEmpleado = $('#proveedorEmpleado').val();
	var proveedorEmpleadoNm = $('#proveedorEmpleadoNm').val();
	var bodega = $('#bodega').val();
	var semanasImplicitas = new Array();

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
	$( "#table tbody input[type=checkbox]" ).each(function() {
		if ($(this).prop('checked') == true){
			itemsDespacho.push($(this).val());
			string_variaciones += $(this).val()+"-"+($(this).data('variacion') == 0 ? 3 : $(this).data('variacion'))+", ";
		}
	});

	// Validaciones para generar el despacho
	if(mes == ''){
		Command: toastr.warning('El campo <strong>mes</strong> es obligatorio.', 'Advertencia');
		$('#mes').select2('open').select2('close');
		bandera++;
	}else if (dias.length == 0) {
		bandera++;
		Command: toastr.warning("Debe seleccionar al menos un día para realizar la orden.", "No hay día seleccionado.", {onHidden : function(){}});
    		$('#dias').focus();
	}else	if(tipo == ''){
		Command: toastr.warning('El campo <strong>tipo de ración</strong> es obligatorio.', 'Advertencia');
		$('#tipoComplem').select2('open').select2('close');
		bandera++;
	} else if (tipoDespacho == '') {
		Command: toastr.warning('El campo <strong>tipo de alimento</strong> es obligatorio.', 'Advertencia');
		$('#tipoDespacho').select2('open').select2('close');
		bandera++;
	}else if (proveedorEmpleado == '') {
		Command: toastr.warning('El campo <strong>Proveedor / Empleado</strong> es obligatorio.', 'Advertencia');
		$('#proveedorEmpleado').select2('open').select2('close');
		bandera++;
	}else if (bodega == '') {
		Command: toastr.warning('El campo <strong>Bodega</strong> es obligatorio.', 'Advertencia');
		$('#bodega').select2('open').select2('close');
		bandera++;
	}else if (itemsDespacho.length == 0) {
		Command: toastr.warning('Debe agregar al menos una <strong>sede</strong> para el despacho.', 'Advertencia');
		bandera++;
	} 

	var rutaMunicipio = "";
	if($( "#municipio" ).val() != ''){
		rutaMunicipio = $( "#municipio option:selected" ).text();
		
	}else if($( "#ruta" ).val() != ''){
		rutaMunicipio = $( "#ruta option:selected" ).text();
	}

	if(bandera == 0) {
		$.ajax({
			type: "POST",
			url: "functions/fn_orden_de_compra_editar.php",
			dataType: "HTML",
			data: {
				"proveedorEmpleado":proveedorEmpleado,
				"proveedorEmpleadoNm":proveedorEmpleadoNm,
        		"mes" : mes,
				"semana":semana,
				"semanaDias":semanasImplicitas,
				"dias":dias,
				"tipo":tipo,
				"tipoDespacho":tipoDespacho,
				"itemsDespacho":itemsDespacho,
				"itemsDespachoVariaciones":string_variaciones,
				"rutaMunicipio":rutaMunicipio,
				"bodega":bodega,
                "num_oco" : num_oco
			},
			beforeSend: function(){
				$('#loader').fadeIn();
			}
		})
		.done(function(data) {
			if (data == 1) {
				Command: toastr.success('La Orden de compra se ha <strong>actualizado<strong> con éxito.','¡Proceso exitoso!',{onHidden : function(){ location.href='ordenes_de_compra.php';}});
				$('#generar').attr('disabled', true);
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

function buscar_dias(mes, semana){
	var datos = {"mes" : mes,  "semana":semana};
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_dias_semana_check.php",
		data: datos,
		beforeSend: function(){},
		success: function(data){
			$('#dias').html(data);
			$('.i-checks').iCheck({ checkboxClass: 'icheckbox_square-green' });
		}
	});
}

function buscar_municipio(tipo, municipioEdit){
	var datos = {"tipo":tipo, "municipio":municipioEdit};
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_municipio.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			$('#municipio').select2('destroy');
			$('#municipio').html(data);
			$('#municipio').select2('');
			if ($('#municipio').val() != '') {
				$('#ruta').select2('val', '');
				$('#ruta').prop( "disabled", true );
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function buscar_institucion(municipio, tipo, mes, semana){
	var datos = {"municipio":municipio, "tipo":tipo, "mes":mes, "semana":semana};
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_institucion.php",
		data: datos,
		beforeSend: function(){
			$('#loader').fadeIn();
		},
		success: function(data){
			//$('#debug').html(data);
			$('#institucion').html(data);
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
		$('#loader').fadeOut();
	});
}

function buscar_semanas(mes){
  	var datos = {"mes":mes};
  	$.ajax({
    	type: "POST",
    	url: "functions/fn_buscar_semanas_orden_compra_nueva.php",
    	data: datos,
    	beforeSend: function(){},
    	success: function(data){
      		$('#semana').html(data);
    	}
  	});
}

function buscar_sede(semana, municipio, tipo, institucion, mes){
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_sede.php",
		data: {
			"semana":semana,
			"municipio":municipio,
			"tipo":tipo,
			"institucion":institucion,
			"mes":mes
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

function buscar_proveedores(tipoAlimento){
	var datos = {"tipoAlimento":tipoAlimento};
	$.ajax({
		type: "POST",
		url: "functions/fn_buscar_proveedores.php",
		data: datos,
		beforeSend: function(){},
		success: function(data){
			//$('#debug').html(data);
			$('#proveedorEmpleado').html(data);
		}
	});
}

function buscar_sedes_editar(Num_OCO){
    var consecutivo = 0;
    var mes = $('#mesE').val();
    $( "td" ).each(function(){
        var aux = $(this).children('td input[type=hidden]').val();
        if (aux != null) {
            itemsActuales.push(aux);
            consecutivo++;
        }
    });

    var datos = {   "Num_OCO" : Num_OCO, 
                    "mes" : mes,
                    "consecutivo":consecutivo  
                };
    $.ajax({
        type: "POST",
        url: "functions/fn_agregar_items_editar.php",
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