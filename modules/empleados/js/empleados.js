$(document).ready(function(){
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
	$(document).on('change', '#numeroDocumento', function() { cargarDatosUsuario($(this).val()); });

	//Configuración de los radio button
	$('input').iCheck({ radioClass: "iradio_square-green" });

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
})

function crearEmpleado() { window.open('empleados_crear.php', '_self'); }

function editarEmpleado(idEmpleado)
{
	$('#formEditarEmpleado #idEmpleado').val(idEmpleado);
	$('#formEditarEmpleado').submit();
}

function cargarDatosUsuario(numeroDocumento)
{
	$.ajax({
    type: "post",
    url: "functions/fn_empleados_cargar_datos_usuario.php",
    data: { documento: numeroDocumento },
    dataType: 'json',
    success: function(data)
    {
    	$('#email').val(data.email);
    	$('#direccion').val(data.direccion);
    	$('#telefono').val(data.telefono);
    },
    error: function(data)
    {
    	console.log(data);
    }
  });
}

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
    	}
    	else
    	{
	    	$('#municipioResidencia').html(data);
    	}
    }
  });
}

function guardarEmpleado(continuar)
{
	if ($('#formCrearEmpleado').valid())
	{
		$.ajax({
			type: 'post',
			url: 'functions/fn_empleados_crear.php',
			data: $('#formCrearEmpleado').serialize(),
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
		$.ajax({
			type: 'post',
			url: 'functions/fn_empleados_editar.php',
			data: $('#formEditarEmpleado').serialize(),
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