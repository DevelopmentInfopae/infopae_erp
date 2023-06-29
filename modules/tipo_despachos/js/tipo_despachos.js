$(document).ready(function () {

    jQuery.extend(jQuery.validator.messages, {  required: "Campo obligatorio.", 
                                                remote: "Por favor, rellena este campo.", 
                                                email: "Por favor, escribe una dirección de correo válida", 
                                                url: "Por favor, escribe una URL válida.", 
                                                date: "Por favor, escribe una fecha válida.", 
                                                dateISO: "Por favor, escribe una fecha (ISO) válida.", 
                                                number: "Por favor, escribe un número entero válido.", 
                                                digits: "Por favor, escribe sólo dígitos.", 
                                                creditcard: "Por favor, escribe un número de tarjeta válido.", 
                                                equalTo: "Por favor, escribe el mismo valor de nuevo.", 
                                                accept: "Por favor, escribe un valor con una extensión aceptada.", 
                                                maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), 
                                                minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), 
                                                rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), 
                                                range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), 
                                                max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), 
                                                min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

	$(document).on('click', '#guardarTipoDespacho', function () { guardarTipoDespacho(); });
	$(document).on('click', '#actualizarTipoDespacho', function () { actualizarTipoDespacho(); });
	$(document).on('click', '.editarTipoDespacho', function () { editarTipoDespacho( $(this).data('codigotipodespacho'), $(this).data('descripciontipodespacho'), $(this).data('redondeo') ); });
	$(document).on('click', '.confirmarEliminarTipoDespacho', function () { confirmarEliminarTipoDespacho($(this).data('codigotipodespacho')); });
	$(document).on('click', '#eliminarTipoDespacho', function () { eliminarTipoDespacho(); });

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

function editarTipoDespacho(codigoTipoDespacho, nombreTipoDespacho, redondeo) {
    var r0 = (redondeo == 0) ? 'selected' : '';
    var r1 = (redondeo == 1) ? 'selected' : ''; 
    var formulario =    '<form id="formActualizarTipoDespachos">'+
                            '<div class="row">'+
                                '<div class="col-sm-12">'+
                                    '<label for="nombre">Nombre</label>'+
                                    '<input type="text" class="form-control" name="nombreAEditar" id="nombreAEditar" maxlength="20" value="'+ nombreTipoDespacho +'" required>'+
                                    '<input type="hidden" name="codigoAEditar" id="codigoAEditar" value="'+ codigoTipoDespacho +'">'+
                                '</div>'+
                            '</div> <br>'+
                            '<div class="row">'+
                                '<div class="col-sm-12">'+
                                    '<label for="redondeo">Redondeo</label>'+
                                        '<select id="redondeo" name="redondeo" class="form-control">'+
                                            '<option value=0 '+ r0 +'>No</option>'+
                                            '<option value=1 '+ r1 +'>Si</option>'+
                                        '</select>'+
                                '</div>'+
                            '</div>'+
                        '</form>';
    $('#ventanaFormulario .modal-body p').html(formulario);
    $('#ventanaFormulario'). modal('toggle');
}

function confirmarEliminarTipoDespacho(codigoTipoDespacho){
	$('#idAEliminar').val(codigoTipoDespacho);
	$('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar el tipo de despacho?');
	$('#ventanaConfirmar').	modal('toggle');
}

function guardarTipoDespacho(){
	if($('#formCrearTipoDespacho').valid()){
        $.ajax({
            type: "post",
            url: "functions/fn_tipo_despachos_crear.php",
            data: $('#formCrearTipoDespacho').serialize(),
            dataType: 'json',
            beforeSend: function(){ $('#loader').fadeIn(); },
            success: function(data) {
                if(data.estado == 1){
                    Command: toastr.success(
                        data.mensaje,
                        "Creado",{
                            onHidden : function(){ window.open('index.php', '_self'); }
                        }
                    );
                }
                else {
                    Command: toastr.warning(
                        data.mensaje,
                        "Error al crear", {
                            onHidden : function(){ $('#loader').fadeOut(); }
                        }
                    );
                }
            }, error: function(data) { 
                console.log(data.responseText);
                Command: toastr.error(
                    "Al parecer existe un problema en el proceso.",
                    "Error al crear",{
                        onHidden : function(){ $('#loader').fadeOut(); }
                    }
                );
            }
        });
    }
}

function actualizarTipoDespacho() {
	if($('#formActualizarTipoDespachos').valid()){
        $.ajax({
            type: "post",
            url: "functions/fn_tipo_despachos_actualizar.php",
            data: $('#formActualizarTipoDespachos').serialize(),
            dataType: 'json',
            beforeSend: function(){ $('#loader').fadeIn(); },
            success: function(data) {
                if(data.estado == 1){
                    Command: toastr.success(
                        data.mensaje,
                        "Creado", {
                            onHidden : function(){ window.open('index.php', '_self'); }
                        }
                    );
                } else{
                    Command: toastr.warning(
                        data.mensaje,
                        "Error al crear", {
                            onHidden : function(){ $('#loader').fadeOut(); }
                        }
                    );
                }
            }, error: function(data) { 
                console.log(data.responseText);
                Command: toastr.error(
                    'Al parecer existe un problema en el proceso.',
                    "Error al crear", {
                        onHidden : function(){ $('#loader').fadeOut(); }
                    }
                );
            }
        });
    }
}

function eliminarTipoDespacho(){
	$.ajax({
        type: "POST",
        url: "functions/fn_tipo_despachos_eliminar.php",
        data: { codigo: $('#idAEliminar').val() },
        dataType: 'json',
        beforeSend: function(){ $('#loader').fadeIn(); },
        success: function(data) {
            if(data.estado == 1){
                Command: toastr.success(
                    data.mensaje,
                    "Eliminado", {
                        onHidden : function(){
                            window.open('index.php', '_self');
                        }
                    }
                );
            } else {
                Command: toastr.warning(
                data.mensaje,
                    "Error al actualizar", {
                        onHidden : function(){ $('#loader').fadeOut(); }
                    }
                );
            }
        }, error: function(data) { 
            console.log(data);
    	    Command: toastr.error(
                data.responseText,
                "Error", {
                    onHidden : function(){ $('#loader').fadeOut(); }
                }
            );
        }
    });
}