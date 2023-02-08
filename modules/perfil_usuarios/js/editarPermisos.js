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

  	$('input').iCheck({
    	checkboxClass: 'icheckbox_square',
    	radioClass: "iradio_square-green"
  	});

  	$(".i-checks").on('ifChanged', function (e) {
    	if ($(this).prop('checked') == true) {
      		changeDashboard($(this).val());
    	}
  	});
	
});

function changeDashboard(d){
	var id_perfil = $('#id_perfil').val();
  	$.ajax({
  		url: 'functions/fn_editar_dashboard.php',
  		type: 'POST',
  		data: {
      		dashboard: d, 
			id_perfil : id_perfil
    	},
		dataType: 'json',
  		beforeSend: function(){ $('#loader').fadeIn(); }
  	})
  	.done(function(data) {
    	if(data.estado == 1){
      		Command: toastr.success(
									data.mensaje,
									"Actualizado", {
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
        						"Error al actualizar", {
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

function confirmarCambio (id, opcion, modulo){
	$('#id').val(id);
  $('#estadoACambiar').val(opcion);
  $('#moduloACambiar').val(modulo);
  if (id != 6 && id != 7) {
    if (opcion == "0") { textoEstado1 = 'Lectura'; valorEstado1 = "1";  textoEstado2 = 'Lectura y escritura'; valorEstado2 = "2"; }
    if (opcion == "1") { textoEstado1 = 'Inactivar'; valorEstado1 = "0";  textoEstado2 = 'Lectura y escritura'; valorEstado2 = "2"; }
    if (opcion == "2") { textoEstado1 = 'Inactivar'; valorEstado1 = "0";  textoEstado2 = 'Lectura'; valorEstado2 = "1"; }
  }
  else{
    if (opcion == "0") { textoEstado1 = 'Lectura'; valorEstado1 = "1"; }
    if (opcion == "1") { textoEstado1 = 'Inactivar'; valorEstado1 = "0"; }
  }

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

  // $('#ventanaConfirmar .modal-body p').html('¿Esta seguro de ' + textoEstado + ' el modulo ' + moduloString + '?');
  // $('#ventanaConfirmar').modal();
  if (id != 6 && id != 7) {
    $('#ventanaConfirmar .modal-body').html('<div class= "row">'+
                                            '<div class= "col-sm-12">'+
                                              '<h3>'+moduloString+'</h3>'+                                             
                                            '</div>'+
                                            '<div class= "col-sm-6">'+
                                              '<label>'+
                                                '<input type="radio" name="estado" id="estado" value="'+valorEstado1+'"> ' +textoEstado1+
                                              '</label>'+
                                            '</div>'+
                                            '<div class= "col-sm-6">'+ 
                                              '<label>'+ 
                                                '<input type="radio" name="estado" id="estado2" value="'+valorEstado2+'"> ' +textoEstado2+
                                              '</label>'+  
                                            '</div>'+  
                                          '</div>'      
                                            );
    $('#ventanaConfirmar').modal();
  }else {
    $('#ventanaConfirmar .modal-body').html('<div class= "row">'+
                                              '<div class= "col-sm-12">'+
                                                '<h3>'+moduloString+'</h3>'+                                             
                                              '</div>'+
                                              '<div class= "col-sm-12">'+
                                                '<label>'+
                                                  '<input type="radio" name="estado" id="estado" value="'+valorEstado1+'"> ' +textoEstado1+
                                                '</label>'+
                                              '</div>'+ 
                                            '</div>'      
                                            );
    $('#ventanaConfirmar').modal();
  }
  

}

function cambiarEstado() {
  $.ajax({
  	url: 'functions/fn_editar_permisos.php',
  	type: 'POST',
  	data: {
      idPerfil: $('#id').val(), 
      // estado : $('#estadoACambiar').val(), 
      modulo : $('#moduloACambiar').val(),
      estado : $('input:radio[name=estado]:checked').val()
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

