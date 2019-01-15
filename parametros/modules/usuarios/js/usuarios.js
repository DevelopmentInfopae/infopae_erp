$(document).ready(function(){
  $(document).on('click', '#box-table tbody td:nth-child(-n+6)', function(){ verUsuario($(this)); });
  $(document).on('click', '.btnEditarUsuario', function() { editarUsuario($(this)); });
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

function verUsuario(control){
  idUsuario = control.parent().data('idusuario');
  $('#formVerUsuario #codigoUsuario').val(idUsuario);
  $('#formVerUsuario').submit();
}

function crearUsuario(){
  window.open('usuarios_crear.php', '_self');
}

function guardarUsuario(continuar){
  if($('#formCrearUsuario').valid()){
    var formData = new FormData();
    formData.append('email', $('#email').val());
    formData.append('nombre', $('#nombre').val());
    formData.append('perfil', $("#perfil").val());
    formData.append('foto', $('#foto')[0].files[0]);
    formData.append('telefono', $("#telefono").val());
    formData.append('direccion', $("#direccion").val());
    formData.append('municipio', $("#municipio").val());
    formData.append('tipoUsuario', $("#tipoUsuario").val());
    formData.append('numeroDocumento', $('#numeroDocumento').val());

    $.ajax({
      type: "POST",
      url: "functions/fn_usuarios_crear.php",
      contentType: false,
      processData: false,
      data: formData,
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje, 
            "Creado", 
            {
              onHidden : function(){
                if(continuar){
                  $("#formCrearUsuario")[0].reset();
                  $('#loader').fadeOut();
                }else{
                  window.open('index.php', '_self');
                }
              }
            }
          );
        } else {
          Command: toastr.error(
            data.mensaje, 
            "Error al crear", 
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      }
    });
  }
}

function editarUsuario(control){
  idUsuario = control.data('idusuario');
  $('#formEditarUsuario #codigoUsuario').val(idUsuario);
  $('#formEditarUsuario').submit();
}

function actualizarUsuario(){
  if($('#formActualizarUsuario').valid()){
    var formData = new FormData();
    var estado = (typeof $('#estado').val() === 'undefined')? "" : $('#estado').val();
    
    formData.append('estado', estado);
    formData.append('id', $('#id').val());
    formData.append('email', $('#email').val());
    formData.append('nombre', $('#nombre').val());
    formData.append('perfil', $("#perfil").val());
    formData.append('foto', $('#foto')[0].files[0]);
    formData.append('telefono', $("#telefono").val());
    formData.append('direccion', $("#direccion").val());
    formData.append('municipio', $("#municipio").val());
    formData.append('tipoUsuario', $("#tipoUsuario").val());
    formData.append('fotoCargada', $('#fotoCargada').prop('src'));
    formData.append('numeroDocumento', $('#numeroDocumento').val());

    $.ajax({
      type: "post",
      url: "functions/fn_usuarios_actualizar.php",
      dataType: "json",
      contentType: false,
      processData: false,
      data: formData,
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){ console.log();
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje, 
            "Actualizado", 
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        } else { 
          Command: toastr.error(
            data.mensaje, 
            "Error al actualizar", 
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data){ console.log(data);
        Command: toastr.error(
          "Al parecer existe un problema con el servidor. Por favor comuníquese con el administrador del sitio InfoPAE.", 
          "Error en el Servidor", 
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function confirmarEliminarUsuario(id){
  $('#idAEliminar').val(id);
  $('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar el Usuario?');
  $('#ventanaConfirmar').modal();
}

function eliminarUsuario(){
  $.ajax({
    type: "POST",
    url: "functions/fn_usuarios_eliminar.php",
    data: { id: $('#idAEliminar').val() },
    dataType: 'json',
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data){
      if(data.estado == 1){
          Command: toastr.success(
            data.mensaje, 
            "Eliminado", 
            {
              onHidden : function(){
                $('#loader').fadeOut(); 
                var textoFiltrado = $(".dataTables_filter input[type=search]").val();
                if(textoFiltrado === undefined){
                  window.location.href = 'index.php';
                }else{
                  window.location.href = 'index.php?filtro=' + textoFiltrado;
                }
              }
            }
          );
        } else {
          Command: toastr.error(
            data.mensaje, 
            "Error al actualizar", 
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
    }
  });
}

function restaurarContrasena(){
  if($('#nombre').valid() && $('#numeroDocumento').valid()){
    $.ajax({
      type: "POST",
      url: "functions/fn_usuarios_restaurar_cantrasena.php",
      dataType: "json",
      data: {
        id: $('#id').val(),
        nombre: $('#nombre').val(),
        numeroDocumento: $('#numeroDocumento').val()
      },
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje, 
            "Contraseña restaurada", 
            {
              onHidden : function(){
                $('#loader').fadeOut();
              }
            }
          );
        } else {
          Command: toastr.error(
            data.mensaje,
            "Error al restaurar", 
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data){ console.log(data)
        Command: toastr.error(
          "Al parecer existe un error en el servidor. Por favor comuníquese con el administrador de InfoPAE.", 
          "Error al procesar", 
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function restaurarContrasenaLista(id){
  $.ajax({
    type: "POST",
    url: "functions/fn_usuarios_restaurar_contrasena_listado.php",
    dataType: "json",
    data: {
      id: id
    },
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data){
      if(data.estado == 1){
        Command: toastr.success(
          data.mensaje, 
          "Procesado", 
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      } else {
        Command: toastr.warning(
          data.mensaje, 
          "Error al procesar", 
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    },
    error: function(data){ console.log(data);
      Command: toastr.error(
        data.mensaje, 
        "Error al procesar", 
        {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
      );
    }
  });
}

function cargarArchivo(){
  var formData = new FormData();
  formData.append('archivo', $('#archivo')[0].files[0]);

  $.ajax({
    type: "POST",
    url: "functions/fn_usuarios_cargar_archivo.php",
    contentType: false,
    processData: false,
    data: formData,
    dataType: 'json',
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data){
      if(data.estado == 1){
        Command: toastr.success(
          data.mensaje, 
          "Procesado", 
          {
            onHidden : function(){
              $('#loader').fadeOut();
              $('.fileinput').fileinput('clear');
            }
          }
        );
      } else {
        Command: toastr.warning(
          data.mensaje, 
          "Error al procesar", 
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    },
    error: function(data){ console.log(data);
      Command: toastr.error(
        "Existe un error con el archivo. Por favor verifique la información suministrada", 
        "Error al procesar", 
        {
          onHidden : function(){ $('#loader').fadeOut(); $('.fileinput').fileinput('clear'); }
        }
      );
    }
  });
}