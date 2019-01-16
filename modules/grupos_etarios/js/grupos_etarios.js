$(document).ready(function () {
	$(document).on('click', '#crearGrupoEtario', function () { crearGrupoEtario(); });
	$(document).on('click', '#guardarGrupoEtario', function () { guardarGrupoEtario(false); });
	$(document).on('click', '#guardarGrupoEtarioContinuar', function () { guardarGrupoEtario(true); });
	$(document).on('click', '#actualizarGrupoEtario', function () { actualizarGrupoEtario(false); });
	$(document).on('click', '#actualizarGrupoEtarioContinuar', function () { actualizarGrupoEtarioContinuar(true); });
	$(document).on('click', '.editarGrupoEtario', function () { editarGrupoEtario($(this).data('codigogrupoetario')); });
	$(document).on('click', '.confirmarGrupoEtario', function () { confirmarEliminarGrupoEtario($(this).data('codigogrupoetario')); });
	$(document).on('click', '#eliminarGrupoEtario', function () { eliminarGrupoEtario(); });

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

function crearGrupoEtario()
{
	window.open('grupos_etarios_crear.php', '_self');
}

function editarGrupoEtario(codigoGrupoEtario)
{
	$('#formEditarGrupoEtario #codigoGrupoEtario').val(codigoGrupoEtario);
	$('#formEditarGrupoEtario').submit();
}

function confirmarEliminarGrupoEtario(codigoGrupoEtario)
{
	$('#idAEliminar').val(codigoGrupoEtario);
	$('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar el grupo etario?');
	$('#ventanaConfirmar').	modal('toggle');
}

function guardarGrupoEtario(continuar)
{
	if($('#formCrearGrupoEtario').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_grupos_etarios_crear.php",
      data: $('#formCrearGrupoEtario').serialize(),
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
                  $("#formCrearGrupoEtario")[0].reset();
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
          'Al parecer existe un error en el proceso',
          "Error al crear",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function actualizarGrupoEtario(continuar)
{
	if($('#formActualizarGrupoEtario').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_grupos_etarios_actualizar.php",
      data: $('#formActualizarGrupoEtario').serialize(),
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
          'Al parecer existe un error en el proceso',
          "Error al crear",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function eliminarGrupoEtario()
{
	$.ajax({
    type: "POST",
    url: "functions/fn_grupos_etarios_eliminar.php",
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
    {
    	console.log(data.responseText);
      Command: toastr.error(
        'Al parecer existe un error en el proceso',
        "Error al crear",
        {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
      );
    }
  });
}