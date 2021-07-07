$(document).ready(function(){
    toastr.options = {
    "closeButton": true,
    "debug": false,
    "progressBar": true,
    "preventDuplicates": false,
    "positionClass": "toast-top-right",
    "onclick": null,
    "showDuration": "200",
    "hideDuration": "1000",
    "timeOut": "2000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }
 
});

function confirmarCambio (id, opcion, modulo){
	$('#id').val(id);
  $('#estadoACambiar').val(opcion);
  $('#moduloACambiar').val(modulo);
  if(opcion){ textoEstado = 'Desactivar' } else { textoEstado = 'Activar'; }

  var moduloString;
  if (modulo ==  "entregas_biometricas") {
    moduloString = "Entregas Biometricas";
  }else if (modulo ==  "instituciones") {
    moduloString = "Instituciones";
  }else if (modulo == 'archivos') {
    moduloString = "Archivos Globales";
  }else if (modulo == 'titulares') {
    moduloString = "Titulares de derecho";
  }else if (modulo == "menus") {
    moduloString = "Menús";
  }else if (modulo == "diagnostico") {
    moduloString = "Diagnóstico Infraestructura";
  }else if (modulo == "dispositivos") {
    moduloString = "Dispositivos Biométricos";
  }else if (modulo == "despachos") {
    moduloString = "Despachos";
  }else if (modulo == "ordenes") {
    moduloString = "Ordenes de Compra";
  }else if (modulo == "entregas") {
    moduloString = "Entregas de Complementos Alimentarios";
  }else if (modulo == "novedades") {
    moduloString = "Novedades";
  }else if (modulo == "nomina") {
    moduloString = "Nómina";
  }else if (modulo == "fqrs") {
    moduloString = "FQRS";
  }else if (modulo == "informes") {
    moduloString = "Informes";
  }else if (modulo == "asistencia") {
    moduloString = "Asistencias";
  }else if (modulo == "control") {
    moduloString = "Control de Acceso";
  }else if (modulo == "procesos") {
    moduloString = "Procesos";
  }else if (modulo == "configuracion") {
    moduloString = "Configuración";
  }

  $('#ventanaConfirmar .modal-body p').html('¿Esta seguro de ' + textoEstado + ' el modulo ' + moduloString + '?');
  $('#ventanaConfirmar').modal();

}


function cambiarEstado() {
  $.ajax({
  	url: 'functions/fn_editar_permisos.php',
  	type: 'POST',
  	data: {
      idPerfil: $('#id').val(), 
      estado : $('#estadoACambiar').val(), 
      modulo : $('#moduloACambiar').val() 
    },
    dataType: 'json',
  	beforeSend: function(){ $('#loader').fadeIn(); }
  })
  .done(function(data) {
    if(data.estado == 1){
      Command: toastr.success(
        data.mensaje,
        "Actualizado",
          {
            onHidden : function(){
              $('#loader').fadeOut();
                location.reload();                                    
            }
          }  
      );
    }
    else{
      Command: toastr.warning(
        data.mensaje,
        "Error al actualizar",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
      );
    }
  })
  .fail(function() {
  	console.log("error");
  })
  .always(function() {
  	$('#loader').fadeOut();
  });
  
}