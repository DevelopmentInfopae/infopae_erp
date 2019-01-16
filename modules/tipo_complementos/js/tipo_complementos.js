$(document).ready(function () {
	$(document).on('click', '#crearTipoComplemento', function () { crearTipoComplemento(); });
	$(document).on('click', '#guardarTipoComplemento', function () { guardarTipoComplemento(false); });
	$(document).on('click', '#guardarTipoComplementoContinuar', function () { guardarTipoComplemento(true); });
	$(document).on('click', '#actualizarTipoComplemento', function () { actualizarTipoComplemento(false); });
	$(document).on('click', '#actualizarTipoComplementoContinuar', function () { actualizarTipoComplemento(true); });
	$(document).on('click', '.editarTipoComplemento', function () { editarTipoComplemento($(this).data('idtipocomplemento')); });
	$(document).on('click', '.confirmarEliminarTipoComplemento', function () { confirmarEliminarTipoComplemento($(this).data('idtipocomplemento')); });
	$(document).on('click', '#eliminarTipoComplemento', function () { eliminarTipoComplemento(); });

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

function crearTipoComplemento() { window.open('tipo_complementos_crear.php', '_self'); }

function editarTipoComplemento(idTipoComplemento)
{
	$('#formEditarTipoComplemento #idTipoComplemento').val(idTipoComplemento);
	$('#formEditarTipoComplemento').submit();
}

function confirmarEliminarTipoComplemento(idTipoComplemento)
{
	$('#idAEliminar').val(idTipoComplemento);
	$('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar el tipo de complemento?');
	$('#ventanaConfirmar').	modal('toggle');
}

function guardarTipoComplemento(continuar)
{
	if($('#formCrearTipoComplemento').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_tipo_complementos_crear.php",
      data: $('#formCrearTipoComplemento').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data)
      { console.log(data);
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Creado",
            {
              onHidden : function(){
                if(continuar){
                  $("#formCrearTipoComplemento")[0].reset();
                  $('#loader').fadeOut();
                }else{
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
            "Error al crear",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data)
      {
        console.log(data.responseText);
      	Command: toastr.error(
          'Al parecer existe un error en el proceso.',
          "Error al crear",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function actualizarTipoComplemento(continuar)
{
	if($('#formActualizarTipoComplemento').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_tipo_complementos_actualizar.php",
      data: $('#formActualizarTipoComplemento').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data)
      {
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Creado",
            {
              onHidden : function(){
                if(continuar){
                  $('#loader').fadeOut();
                }else{
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
            "Error al crear",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data)
      {
        console.log(data.responseText);
      	Command: toastr.error(
          'Al parecer existe un problema en el proceso',
          "Error al crear",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function eliminarTipoComplemento()
{
	$.ajax({
    type: "POST",
    url: "functions/fn_tipo_complementos_eliminar.php",
    data: { id: $('#idAEliminar').val() },
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
    {
      console.log(data.responseText);
    	Command: toastr.error(
        'Al parecer existe un problema en el proceso',
        "Error",
        {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
      );
    }
  });
}