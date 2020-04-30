$(document).ready(function(){
	$(document).on('click', '#crearBodega', crearBodega);
	$(document).on('click', '#guardarBodega', function () { guardarBodega(false); });
	$(document).on('click', '#guardarBodegaContinuar', function () { guardarBodega(true); });
	$(document).on('click', '#generarBodegas', function () { generarBodegas(); });
	$(document).on('click', '.editarBodega', function () { editarBodega($(this).data('codigobodega')); });
	$(document).on('click', '#actualizarBodega', function () { actualizarBodega(false); });
	$(document).on('click', '#actualizarBodegaContinuar', function () { actualizarBodega(true); });
	$(document).on('click', '.confirmarEliminarBodega', function () { confirmarEliminarBodega($(this).data('codigobodega')); });
	$(document).on('click', '#eliminarBodega', function () { eliminarBodega(); });
	$(document).on('click', '#asignarUsuariosBodegas', function () { asignarUsuariosBodegas(); });
	$(document).on('change', '#municipio', function () { cargarBodegasMunicipio($(this).val()); });
	$(document).on('change', '#usuario', function () { listarBodegasUsuario($(this).val()); });
	$(document).on('click', '#asignarUsuarioBodega', function () { asignarUsuarioBodega(); });
	$(document).on('click', '#eliminarUsuarioBodega', function () { eliminarUsuarioBodega(); });

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
});

function crearBodega()
{
	window.open('bodega_crear.php', '_self')
}

function guardarBodega(continuar)
{
	if($('#formGuardarBodega').valid())
	{
		$.ajax({
			type: 'post',
			url: 'functions/fn_bodegas_crear.php',
			data: $('#formGuardarBodega').serialize(),
			dataType: 'json',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data)
			{ console.log(data);
				if (data.estado == 1)
				{
					Command: toastr.success(
						data.mensaje,
						'Guardado',
						{ onHidden: function()
							{
								if(continuar)
								{
                  $("#formGuardarBodega")[0].reset();
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

function generarBodegas()
{
	$.ajax({
		type: 'post',
		url: 'functions/fn_bodegas_generar.php',
		data: {},
		dataType: 'json',
		beforeSend: function() { $('#loader').fadeIn(); },
		success: function(data)
		{ console.log(data);
			if (data.estado == 1)
			{
				Command: toastr.success(
					data.mensaje,
					'Guardado',
					{ onHidden: function()
						{
              $('#loader').fadeOut();
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
		{ console.log(data.responseText);
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

function editarBodega(codigoBodega)
{
	$('#formEditarBodega #codigoBodega').val(codigoBodega);
	$('#formEditarBodega').submit();
}

function actualizarBodega(continuar)
{
	if($('#formActualizarBodega').valid())
	{
		$.ajax({
			type: 'post',
			url: 'functions/fn_bodegas_editar.php',
			data: $('#formActualizarBodega').serialize(),
			dataType: 'json',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data)
			{ console.log(data);
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

function confirmarEliminarBodega(codigoBodega)
{
	$('#idAEliminar').val(codigoBodega);
	$('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar la bodega?');
	$('#ventanaConfirmar').modal('toggle');
}

function eliminarBodega()
{
	$.ajax({
		type: 'post',
		url: 'functions/fn_bodegas_eliminar.php',
		data: { codigo: $('#idAEliminar').val() },
		dataType: 'json',
		beforeSend: function() { $('#loader').fadeIn(); },
		success: function(data)
		{ console.log(data);
			if (data.estado == 1)
			{
				Command: toastr.success(
					data.mensaje,
					'Eliminado',
					{ onHidden: function()
						{
							$('#loader').fadeOut();
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

function asignarUsuariosBodegas()
{
	window.open('bodega_asignar_usuarios.php', '_self');
}

function cargarBodegasMunicipio(codigoMunicipio)
{
	$.ajax({
		type: 'post',
		url: 'functions/fn_bodegas_listar_por_municipio.php',
		data: { municipio: codigoMunicipio },
		dataType: 'html',
		beforeSend: function() { $('#loader').fadeIn(); },
		success: function(data)
		{
			$('#bodegaEntrada').html(data);
			$('#loader').fadeOut();
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

function listarBodegasUsuario(codigoUsuario)
{
	$('#tablaUsuariosBodegas').DataTable({
		destroy: true,
    ajax: {
      method: 'POST',
      url: 'functions/fn_bodegas_listar_usuarios.php',
      data: { usuario: codigoUsuario }
    },
    columns:[
      { data: 'input', className: 'text-center'},
      { data: 'nombreUsuario'},
      { data: 'ciudadBodegaEntrada'},
      { data: 'bodegaEntrada'},
      { data: 'ciudadBodegaSalida'},
      { data: 'bodegaSalida'}
    ],
    buttons: [ {extend: 'excel', title: 'Bodegas', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4 ] } } ],
    dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    order: [ 1, 'asc' ],
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
    "preDrawCallback": function( settings ) {
      // $('#loader').fadeIn();
    }
  }).on('draw', function()
  {
  	$('#loader').fadeOut();

  	// Configuración plugin iCheck
  	$('input').iCheck({ checkboxClass: "icheckbox_square-green" });
		$('#usuariosBodegas').on('ifChecked', function () { $('.usuarioBodega').iCheck('check'); });
		$('#usuariosBodegas').on('ifUnchecked', function () { $('.usuarioBodega').iCheck('uncheck'); });
  });
}

function asignarUsuarioBodega()
{
	if ($('#formAsignarUsuariosBodega').valid())
	{
		$.ajax({
			type: 'post',
			url: 'functions/fn_bodegas_crear_usuarios_bodegas.php',
			data: $('#formAsignarUsuariosBodega').serialize(),
			dataType: 'json',
			beforeSend: function() { $('#loader').fadeIn(); },
			success: function(data)
			{ console.log(data);
				if (data.estado == 1)
				{
					Command: toastr.success(
						data.mensaje,
						'Guardado',
						{ onHidden: function()
							{
								listarBodegasUsuario($('#usuario').val());
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

function eliminarUsuarioBodega()
{
	$.ajax({
		type: 'post',
		url: 'functions/fn_bodegas_eliminar_usuarios_bodegas.php',
		data: $('#formEliminarUsuarioBodega').serialize(),
		dataType: 'json',
		beforeSend: function() { $('#loader').fadeIn(); },
		success: function(data)
		{ console.log(data);
			if (data.estado == 1)
			{
				Command: toastr.success(
					data.mensaje,
					'Guardado',
					{ onHidden: function()
						{
							listarBodegasUsuario($('#usuario').val());
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