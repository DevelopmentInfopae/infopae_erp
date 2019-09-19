$(document).ready(function(){
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
  
function guardarParametros(){
  if($("#formParametros").valid()){
    if($("#id").val() != ''){ ruta = "functions/fn_parametros_actualizar.php" } else { ruta = "functions/fn_parametros_crear.php"; }

    var formData = new FormData();
    formData.append('id', $("#id").val());
    formData.append('anio', $("#anio").val());
    formData.append('nombre', $('#nombre').val());
    formData.append('foto', $('#foto')[0].files[0]);
    formData.append('nombreEtc', $("#nombreEtc").val());
    formData.append('departamento', $("#departamento").val());
    formData.append('nombredepartamento', $("#departamento option:selected").text());
    formData.append('numeroContrato', $('#numeroContrato').val());

    $.ajax({
      type: "POST",
      url: ruta,
      contentType: false,
      processData: false,
      data: formData,
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje, 
            "Guardado", 
            {
              onHidden : function(){
                $('#loader').fadeOut();
                ruta = $('#inputBaseUrl').val();
                window.open(ruta, "_self");
              }
            }
          );
        } else {
          Command: toastr.error(
            data.mensaje, 
            "Error al guardar", 
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data){ console.log(data);
        Command: toastr.error(
          "Al parecer existe un problema en el servidor. Por favor comuníquese con el adminstrador del sitio InfoPAE.", 
          "Error al procesar", 
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }

  var heights = $("campos .col-sm-4").map(function() { return $(this).height(); }).get(),
  maxHeight = Math.max.apply(null, heights);
  $("campos .col-sm-4").height(maxHeight);
}
