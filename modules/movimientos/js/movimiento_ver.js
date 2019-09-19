function anular_movimiento(id,tabla){
  //alert(tabla);
  console.log('Se va a anula el movimiento: '+id);
  var bandera = 0;
  var r = confirm("¿Confirma que desea anular este movimiento?");
  if (r == true) { }
  else { bandera++; }
  if(bandera == 0){
    var datos = { "id":id, "tabla":tabla };
    $.ajax({
      type: "POST",
      url: "fn_movimiento_anular.php",
      data: datos,
      beforeSend: function(){},
      success: function(i){
        if(i == 1){
          alert('El movimiento se ha anulado con exito.');
          location.reload();
        }
        else {
          $('#debug').html(i);
        }
      }//Termina el success
    });
  }
}
