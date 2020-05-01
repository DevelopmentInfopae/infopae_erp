$(document).ready(function(){
	$('.select2').select2();

	$(document).on('change', '#tipoJuridico', function() { obtenerTipoRegimen(); obtenerTipoDocumento(); })
	$(document).on('click', '#crearProveedor', function() { crearProveedor(); });
	$(document).on('click', '.editarProveedores', function() { editarProveedores($(this).data('idproveedor')); });
	$(document).on('click', '#tablaProveedores tbody td:nth-child(-n+6)', function(){ editarProveedores($(this).parent().attr('id')); });
	$(document).on('click', '#guardarProveedor', function() { guardarProveedor(false); });
	$(document).on('click', '#actualizarProveedor', function() { actualizarProveedor(false); });
	$(document).on('click', '#eliminarProveedor', function() { eliminarProveedor(); });
	$(document).on('click', '#guardarProveedorContinuar', function() { guardarProveedor(true); });
	$(document).on('click', '#actualizarProveedorContinuar', function() { actualizarProveedor(true); });
	$(document).on('click', '.confirmarEliminarProveedores', function() { confirmarEliminarProveedores($(this).data('idproveedor'), $(this).data('razonsocialproveedor')); });
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

function obtenerTipoRegimen()
{
	var tipoJuridico = $('#tipoJuridico').val();

	if (tipoJuridico == 1) {
		$('#tipoRegimen').val(1);
		$('#tipoRegimen option:eq(0)').prop('disabled', 'disabled');
		$('#tipoRegimen option:eq(2)').prop('disabled', 'disabled');
	} else if (tipoJuridico == 2) {
		$('#tipoRegimen').val(2);
		$('#tipoRegimen option:eq(0)').prop('disabled', 'disabled');
		$('#tipoRegimen option:eq(2)').prop('disabled', '');
	} else {
		$('#tipoRegimen').val(0);
		$('#tipoRegimen option:eq(0)').prop('disabled', 'disabled');
		$('#tipoRegimen option:eq(2)').prop('disabled', '');
	}
}

function obtenerTipoDocumento()
{
	var tipoJuridico = $('#tipoJuridico').val();

	$.ajax({
		url: 'functions/fn_proveedores_obtener_tipo_documento.php',
		type: 'POST',
		dataType: 'html',
		data: {
			'tipoJuridico': tipoJuridico
		},
	})
	.done(function(data) {
		$('#tipoDocumento').html(data);
	})
	.fail(function(data) {
		console.log(data.responseText);
		Command: toastr.error( 'Al parecer existe un problema en la base de datos. Por favor comuníquese con el administrador del sitio Info PAE.', 'Error', {
			onHidden: function() {
				$('#loader').fadeOut();
			}
		});
	});
}

function crearProveedor() { window.open('proveedores_crear.php', '_self'); }

function editarProveedores(idProveedor)
{
	$('#formEditarProveedor #idProveedor').val(idProveedor);
	$('#formEditarProveedor').submit();
}

function cargarDatosUsuario(numeroDocumento)
{
	$.ajax({
    type: "post",
    url: "functions/fn_proveedores_cargar_datos_usuario.php",
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

function guardarProveedor(continuar)
{
	if ($('#formCrearProveedor').valid()) {
		$.ajax({
			type: 'post',
			url: 'functions/fn_proveedores_crear.php',
			data: $('#formCrearProveedor').serialize(),
			dataType: 'json',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data) {
				if (data.estado == 1) {
					Command: toastr.success( data.mensaje, 'Guardado', {
						onHidden: function() {
							if(continuar) {
                  				$("#formCrearProveedor")[0].reset();
                  				$('#tipoalimento').select2([]);
                  				$('#loader').fadeOut();
                			} else {
                  				window.open('index.php', '_self');
                			}
						}
					});
				} else {
					Command: toastr.warning( data.mensaje, 'Advertencia', {
						onHidden: function() {
							$('#loader').fadeOut();
						}
					});
				}
			},
			error: function(data)
			{
				console.log(data.responseText);
				Command: toastr.error( 'Al parecer existe un problema en la base de datos. Por favor comuníquese con el administrador del sitio Info PAE.', 'Error', {
					onHidden: function() {
						$('#loader').fadeOut();
					}
				});
			}
		});
	}
}

function actualizarProveedor(continuar)
{
	if ($('#formEditarProveedor').valid())
	{
		$.ajax({
			type: 'post',
			url: 'functions/fn_proveedores_editar.php',
			data: $('#formEditarProveedor').serialize(),
			dataType: 'json',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data) {
				if (data.estado == 1) {
					Command: toastr.success(data.mensaje, 'Guardado', {
						onHidden: function() {
							if(continuar) {
                  				$('#loader').fadeOut();
                			} else {
                  				window.open('index.php', '_self');
                			}
						}
					});
				} else {
					Command: toastr.warning(data.mensaje, 'Advertencia', {
						onHidden: function() {
							$('#loader').fadeOut();
						}
					});
				}
			},
			error: function(data)
			{ console.log(data.responseText);
				Command: toastr.error('Al parecer existe un problema en la base de datos. Por favor comuníquese con el administrador del sitio Info PAE.', 'Error', {
					onHidden: function() {
						$('#loader').fadeOut();
					}
				});
			}
		});
	}
}

function confirmarEliminarProveedores(idProveedor, razonSocialProveedor)
{
	$('#idAEliminar').val(idProveedor);
	$('#nombreAEliminar').val(razonSocialProveedor);
	$('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar el empleado?');
	$('#ventanaConfirmar').modal('toggle');
}

function eliminarProveedor()
{
	$.ajax({
		type: 'post',
		url: 'functions/fn_proveedores_eliminar.php',
		data: {
			proveedor: $('#idAEliminar').val(),
			razonSocial: $('#nombreAEliminar').val()
		},
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