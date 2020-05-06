$(document).ready(function(){
	set_select();
  	$(document).on('click', '#tablaEmpleados tbody td:nth-child(-n+6)', function(){ verEmpleado($(this)); });
	$(document).on('click', '#crearEmpleado', function() { crearEmpleado(); });
	$(document).on('click', '.editarEmpleado', function() { editarEmpleado($(this).data('idempleado')); });
	$(document).on('click', '#guardarEmpleado', function() { guardarEmpleado(false); });
	$(document).on('click', '#actualizarEmpleado', function() { actualizarEmpleado(false); });
	$(document).on('click', '#eliminarEmpleado', function() { eliminarEmpleado(); });
	$(document).on('click', '#guardarEmpleadoContinuar', function() { guardarEmpleado(true); });
	$(document).on('click', '#actualizarEmpleadoContinuar', function() { actualizarEmpleado(true); });
	$(document).on('click', '.confirmarEliminarEmpleado', function() { confirmarEliminarEmpleado($(this).data('idempleado')); });
	$(document).on('change', '#departamentoNacimiento', function() { cargarMunicipios($(this).val(), true); });
	$(document).on('change', '#departamentoResidencia', function() { cargarMunicipios($(this).val(), false); });
	$(document).on('change', '#tipo', function() { tipoEmpleado($(this).val()); });
	$(document).on('change', '#manipulador_municipio', function() { buscar_institucion(); });
	$(document).on('change', '#manipulador_institucion', function() { buscar_sede(); });
	$(document).on('click', '.add_fila_manipuladora', function() { add_fila_manipuladora(); });
	$(document).on('change', '.manipulador_municipio', function() { buscar_institucion_2($(this)); });
	$(document).on('change', '.manipulador_institucion', function() { buscar_sede_2($(this)); });
	$(document).on('click', '.delete_row', function() { delete_fila_manipuladora($(this)); });
	$(document).on('change', '#TipoContrato', function() { tipoContrato($(this)); });



	// $(document).on('change', '#numeroDocumento', function() { cargarDatosUsuario($(this).val()); });

	//Configuración de los radio button

	// Configuración del plugin toastr.
  toastr.options = {
    "closeButton": true,
    "debug": false,
    "progressBar": true,
    "preventDuplicates": false,
    "positionClass": "toast-top-right",
    "onclick": null,
    "showDuration": "400",
    "hideDuration": "1000",
    "timeOut": "2000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };

  $('input').iCheck({
	     radioClass: 'iradio_square-green',
 		 checkboxClass: 'icheckbox_square-green',
	});
})
function verEmpleado(control){
  idEmpleado = control.parent().data('idempleado');
  $('#formVerEmpleado #codigoEmpleado').val(idEmpleado);
  $('#formVerEmpleado').submit();
}
function crearEmpleado() { window.open('empleados_crear.php', '_self'); }

function editarEmpleado(idEmpleado)
{
	$('#formEditarEmpleado #idEmpleado').val(idEmpleado);
	$('#formEditarEmpleado').submit();
}

// function cargarDatosUsuario(numeroDocumento)
// {
// 	$.ajax({
//     type: "post",
//     url: "functions/fn_empleados_cargar_datos_usuario.php",
//     data: { documento: numeroDocumento },
//     dataType: 'json',
//     success: function(data)
//     {
//     	$('#email').val(data.email);
//     	$('#direccion').val(data.direccion);
//     	$('#telefono').val(data.telefono);
//     },
//     error: function(data)
//     {
//     	console.log(data);
//     }
//   });
// }

function cargarMunicipios(idDepartamento, comboNacimiento)
{
	$.ajax({
    type: "post",
    url: "functions/fn_empleados_cargar_municipios.php",
    data: { departamento: idDepartamento },
    dataType: 'html',
    success: function(data)
    {
    	if (comboNacimiento)
    	{
    		$('#municipioNacimiento').html(data);
    		$('#manipulador_municipio').html(data);
    	}
    	else
    	{
	    	$('#municipioResidencia').html(data);
	    	$('#manipulador_municipio').html(data);
    	}
    }
  });
}

function guardarEmpleado(continuar)
{
	inputs = $('.form-data');

	
	if ($('#formCrearEmpleado').valid())
	{
    	var formData = new FormData();
    	inputs = $('.form-data');
    	$.each(inputs, function(index, input){
    		if ($(input).prop('type') == 'checkbox') {
    			formData.append($(input).prop('name'), $(input).is(':checked'));
    		} else if ($(input).prop('type') == 'radio') {
    			r_name = $(input).prop('name');
    			formData.append(r_name, $('input[name="'+r_name+'"]:checked').val());
    		} else {
    			formData.append($(input).prop('name'), $(input).val());
    		}
    	});
		formData.append('foto', $('#foto')[0].files[0]);
		var tc = [];
		$('select[name="manipulador_tipo_complemento[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    tc.push(item);
		});
		var mn = [];
		$('select[name="manipulador_municipio[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    mn.push(item);
		});
		var inst = [];
		$('select[name="manipulador_institucion[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    inst.push(item);
		});
		var sed = [];
		$('select[name="manipulador_sede[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    sed.push(item);
		});


		formData.append('manipulador_tipo_complemento', JSON.stringify(tc));
		formData.append('manipulador_municipio', JSON.stringify(mn));
		formData.append('manipulador_institucion', JSON.stringify(inst));
		formData.append('manipulador_sede', JSON.stringify(sed));

		$.ajax({
      		type: "POST",
			url: 'functions/fn_empleados_crear.php',
			data: formData,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data)
			{
				if (data.estado == 1)
				{
					Command: toastr.success(
						data.mensaje,
						'Guardado',
						{ onHidden: function()
							{
								if(continuar)
								{
                  $("#formCrearEmpleado")[0].reset();
                  $('#loader').fadeOut();
                }
                else
                {
                  window.open('index.php', '_self');
                }
							}
						}
					);
				}
				else
				{
					Command: toastr.warning(
						data.mensaje,
						'Advertencia',
						{ onHidden: function()
							{
								$('#loader').fadeOut();
							}
						}
					);
				}
			},
			error: function(data)
			{ console.log(data);
				Command: toastr.error(
					'Al parecer existe un problema en la base de datos. Por favor comuníquese con el administrador del sitio Info PAE.',
					'Error',
					{ onHidden: function()
						{
							$('#loader').fadeOut();
						}
					}
				);
			}
		});
	}
}

function actualizarEmpleado(continuar)
{
	if ($('#formEditarEmpleado').valid())
	{

		var formData = new FormData();
    	inputs = $('.form-data');
    	$.each(inputs, function(index, input){
    		if ($(input).prop('type') == 'checkbox') {
    			formData.append($(input).prop('name'), $(input).is(':checked'));
    		} else if ($(input).prop('type') == 'radio') {
    			r_name = $(input).prop('name');
    			formData.append(r_name, $('input[name="'+r_name+'"]:checked').val());
    		} else {
    			formData.append($(input).prop('name'), $(input).val());
    		}
    	});
		formData.append('foto', $('#foto')[0].files[0]);
		var tc = [];
		$('select[name="manipulador_tipo_complemento[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    tc.push(item);
		});
		var mn = [];
		$('select[name="manipulador_municipio[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    mn.push(item);
		});
		var inst = [];
		$('select[name="manipulador_institucion[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    inst.push(item);
		});
		var sed = [];
		$('select[name="manipulador_sede[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    sed.push(item);
		});
		var mid = [];
		$('input[name="id_manipulador[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    mid.push(item);
		});
		var mstatus = [];
		$('select[name="estado_manipulador[]"]').each(function(index, select){
		    item = {};
		    item[index] = $(select).val();
		    mstatus.push(item);
		});

		formData.append('manipulador_tipo_complemento', JSON.stringify(tc));
		formData.append('manipulador_municipio', JSON.stringify(mn));
		formData.append('manipulador_institucion', JSON.stringify(inst));
		formData.append('manipulador_sede', JSON.stringify(sed));
		formData.append('manipulador_id', JSON.stringify(mid));
		formData.append('manipulador_estado', JSON.stringify(mstatus));

		$.ajax({
			type: 'POST',
			url: 'functions/fn_empleados_editar.php',
			data: formData,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data)
			{
				if (data.estado == 1)
				{
					Command: toastr.success(
						data.mensaje,
						'Guardado',
						{ onHidden: function()
							{
								if(continuar)
								{
                  $('#loader').fadeOut();
                }
                else
                {
                  window.open('index.php', '_self');
                }
							}
						}
					);
				}
				else
				{
					Command: toastr.warning(
						data.mensaje,
						'Advertencia',
						{ onHidden: function()
							{
								$('#loader').fadeOut();
							}
						}
					);
				}
			},
			error: function(data)
			{ console.log(data);
				Command: toastr.error(
					'Al parecer existe un problema en la base de datos. Por favor comuníquese con el administrador del sitio Info PAE.',
					'Error',
					{ onHidden: function()
						{
							$('#loader').fadeOut();
						}
					}
				);
			}
		});
	}
}

function confirmarEliminarEmpleado(idEmpleado)
{
	$('#idAEliminar').val(idEmpleado);
	$('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar el empleado?');
	$('#ventanaConfirmar').modal('toggle');
}

function eliminarEmpleado()
{
	$.ajax({
		type: 'post',
		url: 'functions/fn_empleados_eliminar.php',
		data: { empleado: $('#idAEliminar').val() },
		dataType: 'json',
		beforeSend: function() { $('#loader').fadeIn(); },
		success: function(data)
		{
			if (data.estado == 1)
			{
				Command: toastr.success(
					data.mensaje,
					'Eliminado',
					{ onHidden: function()
						{
							window.open('index.php', '_self');
						}
					}
				);
			}
			else
			{
				Command: toastr.warning(
					data.mensaje,
					'Advertencia',
					{ onHidden: function()
						{
							$('#loader').fadeOut();
						}
					}
				);
			}
		},
		error: function(data)
		{ console.log(data);
			Command: toastr.error(
				'Al parecer existe un problema en la base de datos. Por favor comuníquese con el administrador del sitio Info PAE.',
				'Error',
				{ onHidden: function()
					{
						$('#loader').fadeOut();
					}
				}
			);
		}
	});
}

function buscar_institucion(){
	municipio = $('#manipulador_municipio').val();
  var datos = {"municipio":municipio};
    $.ajax({
      type: "POST",
      url: "../despachos/functions/fn_despacho_buscar_institucion.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        //$('#debug').html(data);
        $('#manipulador_institucion').html(data);
        $('#manipulador_sede').html('<option value="">Seleccione...</option>');
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
}

function buscar_institucion_2(select_municipio){
  municipio = select_municipio.val();
  var datos = {"municipio":municipio};
    $.ajax({
      type: "POST",
      url: "../despachos/functions/fn_despacho_buscar_institucion.php",
      data: datos,
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        //$('#debug').html(data);
        instituciones = $('.manipulador_institucion');
        instituciones.eq($('.manipulador_municipio').index(select_municipio)).html(data);
        sedes = $('.manipulador_sede');
        sedes.eq($('.manipulador_municipio').index(select_municipio)).html('<option value="">Seleccione...</option>');
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });
}

function buscar_sede(){


	municipio = $('#manipulador_municipio').val();
	institucion = $('#manipulador_institucion').val();

    $.ajax({
      type: "POST",
      url: "../despachos/functions/fn_despacho_buscar_sede.php",
      data: {
        "municipio":municipio,
        "institucion":institucion
      },
      beforeSend: function(){
        $('#loader').fadeIn();
      }
    })
    .done(function(data){ $('#manipulador_sede').html(data); })
    .fail(function(data){ console.log(data); })
    .always(function(){
      $('#loader').fadeOut();
    });
}

function buscar_sede_2(select_institucion){

	inst_index = $('.manipulador_institucion').index(select_institucion);

	municipio = $('.manipulador_municipio').eq(inst_index).val();
	institucion = select_institucion.val();

    $.ajax({
      type: "POST",
      url: "../despachos/functions/fn_despacho_buscar_sede.php",
      data: {
        "municipio":municipio,
        "institucion":institucion
      },
      beforeSend: function(){
        $('#loader').fadeIn();
      }
    })
    .done(function(data){ $('.manipulador_sede').eq(inst_index).html(data); })
    .fail(function(data){ console.log(data); })
    .always(function(){
      $('#loader').fadeOut();
    });
}

function tipoEmpleado(tipo){
	if (tipo == 2) {
		$('.div_manipulador').fadeIn();
		selects = $('.div_manipulador').find('select');
		$.each(selects, function(index, select){
			$(select).prop('required', true);
		});
	} else {
		$('.div_manipulador').fadeOut();
		selects = $('.div_manipulador').find('select');
		$.each(selects, function(index, select){
			$(select).prop('required', false);
		});
	}

	options = $('#TipoContrato').find('option');
	if (tipo == 2) {
		$('#TipoContrato').val(3);
		$.each(options, function(index, option){
			if ($(option).val() != 3) {
				$(option).prop('disabled', true);
			} else {
				$(option).prop('disabled', false);
			}
		});
	} else if (tipo == 1 || tipo == 3) {
		$('#TipoContrato').val('');
		$.each(options, function(index, option){
			$(option).prop('disabled', false);
		});
	} else if (tipo == 4) {
		$('#TipoContrato').val(4);
		$.each(options, function(index, option){
			if ($(option).val() != 4) {
				$(option).prop('disabled', true);
			} else {
				$(option).prop('disabled', false);
			}
		});
	}
	$('#TipoContrato').trigger('change');

}

function add_fila_manipuladora(){
	$('#loader').fadeIn();
	var dpto_residencia = $('#departamentoResidencia').val();
	if (dpto_residencia != '') {
		$.ajax({
			url : 'functions/fn_empleados_manipulador_nueva_fila.php',
			type : 'POST',
			data : { 'dpto_residencia' : dpto_residencia }
		}).done(function(data){
			$('#manipulador_tbody').append(data);
			$('#loader').fadeOut();
		}).fail(function(data){
			console.log(data);
		});
	} else {
			Command: toastr.error(
				'Primero seleccione el departamento de residencia',
				'Error',
				{ onHidden: function()
					{
						$('#loader').fadeOut();
					}
				}
			);
	}
}

function delete_fila_manipuladora(delete_row){
	del_index = $('.delete_row').index(delete_row);
	$('.row_manipulador').eq(del_index).remove();
}

$(document).on('ifChecked', '#estado2', function(){
	selects = $('select[name="estado_manipulador[]"]');
	$.each(selects, function(index, select){
		$(select).val(0);
	});
});

$(document).on('ifChecked', '#estado1', function(){
	selects = $('select[name="estado_manipulador[]"]');
	$.each(selects, function(index, select){
		$(select).val(1);
	});
});

function tipoContrato(select){
	tipoc = select.val();

	if (tipoc == 1 || tipoc == 2) {
		$('.div_base_mes').fadeIn();
		$('#ValorBaseMes').prop('required', true);
	} else {
		$('.div_base_mes').fadeOut();
		$('#ValorBaseMes').prop('required', false);
	}

}

function set_select(){
	$('select.form-control').select2({width : "100%"});
}