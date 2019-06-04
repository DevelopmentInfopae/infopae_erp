$(document).ready(function(){

  var fotos = "0";
  $('#btnEnviar').click(function(event){
    var bandera = 0;
   
    if($('#dispositivo').val() == ''){
      alert('Debe seleccionar un dispositivo');
      $('#dispositivo').focus();
      bandera++;
    } else if($('#archivo').val() == ''){
      alert('Debe seleccionar un archivo');
      $('#archivo').focus();
      bandera++;
    } 

    if(bandera == 0){
      console.log('Se ha escogido un archivo.');
      var formData = new FormData($("#formArchivos")[0]);
      console.log(formData);
      var ruta = "functions/fn_archivo_guardar.php";
      var auxContenido = '';
      $.ajax({
        url: ruta,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        beforeSend: function(){
          $("#btnEnviar").prop("disabled",true);
        },
        success: function(datos){
          try {
            var obj = JSON.parse(datos);           
            if(obj.respuesta == 1){
              //alert('El archivo se ha cargado con exito.'); 
              //console.log(obj.nombre);
              //console.log(obj.dispositivo);
              convertirArchivo(obj.nombre, obj.dispositivo);
            }
            else{
              alert(obj.respuesta);  
              $('.debug').html(respuesta);
            }
          }
          catch(err) {
            $('.debug').html(err.message);
            $('.debug').append('<br/><br/>');
            $('.debug').append(datos);
          }
        }
      })
      .done(function(){ })
      .fail(function(){ })
      .always(function(){});
      // Termina el ajax
    }
  });
});

function convertirArchivo(nombre, dispositivo){
  console.log('LLamando a node');
   var ruta = $('#ruta').val(); 
  console.log(ruta);
  //var ruta = "http://127.0.0.1:8080"; 
  var datos = {
    "nombre":nombre,
    "dispositivo":dispositivo,
    "ruta":ruta
  };       
  $.ajax({
    type: "GET",
    url: ruta,
    crossDomain : true,
    data: datos,
    beforeSend: function(){}, 
    success: function(data){
      console.log('Success');
      console.log(data);  
      if(data == 1){
        leer_csv(nombre, dispositivo);
      }  
      else{
        alert(data);  
      }          
      // console.log(JSON.stringify(data));   
    }
  })
  .done(function(){ 
    console.log('Done convertirArchivo');
  })
  .fail(function(){
    console.log('Fail convertirArchivo');
  })
  .always(function(){
    //console.log('Always convertirArchivo');
  }); 
  // Termina Ajax
}

function leer_csv(nombre, dispositivo){
  console.log('Leer Excel');
  //console.log(nombre);
  //console.log(dispositivo);
  nombre = nombre.toLowerCase();
  var datos = {
    "nombre":nombre,
    "dispositivo":dispositivo
  };
  var ruta = "functions/fn_csv_to_db.php";
  $.ajax({
    url: ruta,
    type: "POST",
    data: datos,
    beforeSend: function(){ },
    success: function(datos){
      try {
        var obj = JSON.parse(datos);           
        if(obj.respuesta == 1){
          alert('El archivo se ha cargado con exito.'); 
          location.reload();
        }
        else{
          alert(obj.respuesta);  
          $('.debug').html(obj.respuesta);
        }
      }
      catch(err) {
        $('.debug').html(err.message);
        $('.debug').append('<br/><br/>');
        $('.debug').append(datos);
      }
    }
  })
  .done(function(){ })
  .fail(function(){ })
  .always(function(){
    $("#btnEnviar").prop("disabled",false);
  });
  // Termina el ajax
}