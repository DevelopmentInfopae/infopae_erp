$(document).ready(function(){
	$('#municipio').change(function(){ buscarInstitucion($(this).val()); });
	$('#jornada').change(function(){ buscarTipoComplemento($(this).val()); });

	$('.titular').click(function(){
		var aux = $(this).attr('numDoc');
		$('#verTitular #numDoc').val(aux);
		aux = $(this).attr('tipoDoc');
		$('#verTitular #tipoDoc').val(aux);
		aux = $(this).attr('semana');
		$('#verTitular #semana').val(aux);
		$('#verTitular').submit();
	});

	// Configuración del plugin toastr
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

function buscarInstitucion(municipio){
	$.ajax({
	  type: "post",
	  url: "functions/fn_buscar_instituciones.php",
	  data: {"municipio":municipio, "modulo": "sede"},
	  beforeSend: function(){ $('#loader').fadeIn(); },
	  success: function(data){
	  	$('#loader').fadeOut();
	    $('#institucion').html(data);
	  },
	  error: function(data){
	  	$('#loader').fadeOut();
	  	console.log(data);
	  }
	});
}

function buscarTipoComplemento(jornada){
	$.ajax({
	  type: "post",
	  url: "functions/fn_sede_buscar_tipo_complemento.php",
	  data: {"jornada":jornada},
	  beforeSend: function(){ $('#loader').fadeIn(); },
	  success: function(data){
	  	$('#loader').fadeOut();
	    $('#complemento').html(data);
	  },
	  error: function(data){
	  	$('#loader').fadeOut();
	  	console.log(data);
	  }
	});
}

function guardarSede(continuar){
  if($('#formCrearSede').valid()){
  	var formData = new FormData();
  	formData.append('municipio', $('#municipio').val());
  	formData.append('institucion', $('#institucion').val());
  	formData.append('nombreInstitucion', $('#institucion option:selected').text());
  	formData.append('codigo', $('#codigo').val());
  	formData.append('nombre', $('#nombre').val());
  	formData.append('direccion', $('#direccion').val());
  	formData.append('telefono', $('#telefono').val());
  	formData.append('email', $('#email').val());
  	formData.append('coordinador', $('#coordinador').val());
  	formData.append('jornada', $('#jornada').val());
  	formData.append('complemento', $('#complemento').val());
  	formData.append('sector', $('input[name=sector]:checked').val());
  	formData.append('validacion', $('input[name=validacion]:checked').val());
  	formData.append('variacion', $('#variacion').val());
  	formData.append('imagen', $('#imagen')[0].files[0]);

    $.ajax({
      type: "POST",
      url: "functions/fn_sede_crear.php",
      contentType: false,
      processData: false,
      data: formData,
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje, 
            "Creado", {
              onHidden : function(){
                if(continuar){
                  $('#loader').fadeOut();
                  $("#formCrearSede")[0].reset();
                }else{
                  window.open('sedes.php', '_self');
                }
              }
            }
          );
        } else {
          Command: toastr.error(
            data.mensaje, 
            "Error al crear", { onHidden : function(){ $('#loader').fadeOut(); } }
          );
        }
      },
      error: function(data){ console.log(data);
         Command: toastr.error(
            "Al parecer existe un error al ejecutar el proceso. Por favor contacte con el administrador del InfoPAE.", 
            "Error al crear", { onHidden : function(){ $('#loader').fadeOut(); } }
          );
      }
    });
  }
}