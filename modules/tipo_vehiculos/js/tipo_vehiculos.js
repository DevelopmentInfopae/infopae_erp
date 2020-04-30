$(document).ready(function () {
	$(document).on('click', '#guardarTipoVehiculo', function () { guardarTipoVehiculo(); });
	$(document).on('click', '#actualizarTipoVehiculo', function () { actualizarTipoVehiculo(); });
	$(document).on('click', '.editarTipoVehiculo', function () { editarTipoVehiculo($(this).data('codigotipovehiculo'), $(this).data('nombretipovehiculo')); });
	$(document).on('click', '.confirmarEliminarTipoVehiculo', function () { confirmarEliminarTipoVehiculo($(this).data('codigotipovehiculo')); });
	$(document).on('click', '#eliminarTipoVehiculo', function () { eliminarTipoVehiculo(); });

	// Configuración inicial del plugin toastr.
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
  }
});

function editarTipoVehiculo(codigoTipoVehiculo, nombreTipoVehiculo)
{
  var formulario = '<form id="formActualizarTipoVehiculos">'+
                      '<div class="row">'+
                        '<div class="col-sm-12">'+

                            '<label for="nombre">Nombre</label>'+
                            '<input type="text" class="form-control" name="nombreAEditar" id="nombreAEditar" maxlength="20" value="'+ nombreTipoVehiculo +'" required>'+
                            '<input type="hidden" name="codigoAEditar" id="codigoAEditar" value="'+ codigoTipoVehiculo +'">'+

                        '</div>'+
                      '</div>'+
                    '</form>';
  $('#ventanaFormulario .modal-body p').html(formulario);
  $('#ventanaFormulario'). modal('toggle');
}

function confirmarEliminarTipoVehiculo(codigoTipoVehiculo)
{
	$('#idAEliminar').val(codigoTipoVehiculo);
	$('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar el tipo de vehículo?');
	$('#ventanaConfirmar').	modal('toggle');
}

function guardarTipoVehiculo()
{
	if($('#formCrearTipoVehiculos').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_tipo_vehiculos_crear.php",
      data: $('#formCrearTipoVehiculos').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data)
      {
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Creado",
            {
              onHidden : function(){ window.open('index.php', '_self'); }
            }
          );
        }
        else
        {
          Command: toastr.warning(
            data.mensaje,
            "Error al crear",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data)
      { console.log(data.responseText);
      	Command: toastr.error(
          "Al parecer existe un problema en el proceso.",
          "Error al crear",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function actualizarTipoVehiculo()
{
	if($('#formActualizarTipoVehiculos').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_tipo_vehiculos_actualizar.php",
      data: $('#formActualizarTipoVehiculos').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data)
      {
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Creado",
            {
              onHidden : function(){ window.open('index.php', '_self'); }
            }
          );
        }
        else
        {
          Command: toastr.warning(
            data.mensaje,
            "Error al crear",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data)
      { console.log(data.responseText);
      	Command: toastr.error(
          'Al parecer existe un problema en el proceso.',
          "Error al crear",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function eliminarTipoVehiculo()
{
	$.ajax({
    type: "POST",
    url: "functions/fn_tipo_vehiculos_eliminar.php",
    data: { codigo: $('#idAEliminar').val() },
    dataType: 'json',
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data)
    {
      if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Eliminado",
            {
              onHidden : function(){
                window.open('index.php', '_self');
              }
            }
          );
        } else {
          Command: toastr.warning(
            data.mensaje,
            "Error al actualizar",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
    },
    error: function(data)
    { console.log(data);
    	Command: toastr.error(
        data.responseText,
        "Error",
        {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
      );
    }
  });
}