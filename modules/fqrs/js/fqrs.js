$(document).ready(function() {
	$(document).on('click', '.ver_fqrs', function() { ver_fqrs($(this).data('id_fqrs')); });
	$(document).on('click', '#tabla_fqrs tbody td:nth-child(-n+8)', function(){ ver_fqrs($(this).parent().attr('id')); });
	$(document).on('click', '#boton_editar_caso', function() { editar_caso(); });
});

function ver_fqrs(id)
{
	$('#formulario_ver_fqrs #id_fqrs').val(id);
	$('#formulario_ver_fqrs').submit();
}

function editar_caso()
{
	if (validar_campos()) {
		$.ajax({
            url: 'functions/fn_fqrs_editar.php',
            type: 'POST',
            dataType: 'JSON',
            data: $('#formulario_editar_fqrs').serialize(),
        })
        .done(function(data) {
            if (data.estado == '1') {
                Command: toastr.success(data.mensaje, 'Correcto', {onHidden: function() {
                    // $('#formulario_editar_fqrs')[0].reset();
                    window.location.href = 'index.php';
                }});
            } else {
                Command: toastr.error(data.mensaje, 'Error');
            }
        })
        .fail(function(data) {
            console.log(data.responseText);
        });
	}
}

function validar_campos()
{
	if ($('#solucion').val() == '') {
        Command: toastr.error('Campo descripción de la solución es obligatorio', 'Error en la validación', {onHidden: function() {
            $('#solusolucioncion').focus();
        }});
        return false;
	}

	return true;
}