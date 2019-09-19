function actualizarFechasLotes(){
  console.log('actualizarFechasLotes');
  var bandera = 0;



  $('.inputLote').each(function(){
    if($(this).val()== '' && bandera == 0){
      alert('El numero de lote es un campo obligatorio.');
      bandera++;
      $(this).focus();
    }
  });

  if(bandera == 0){
    $('.inputFechaVencimiento').each(function(){
      if($(this).val()== '' && bandera == 0){
        alert('La fecha de vencimiento es un campo obligatorio.');
        bandera++;
        $(this).focus();
      }
    });
  }





  if(bandera == 0){
    console.log('Iniciando Ajax');


    $.ajax({
      type: "POST",
      url: "fn_despacho_actualizar_fechas_lotes.php",
      data: $("#formulario1").serialize(),
      beforeSend: function(){
        $('#loader').fadeIn();
      },
      success: function(data){
        //$('#debug').html(data);
        console.log(data);
        $(window).unbind('beforeunload');
        window.location.href = 'despachos.php';
      }
    })
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
      $('#loader').fadeOut();
    });





  }
  else{
    console.log('No se envía');
  }
}
