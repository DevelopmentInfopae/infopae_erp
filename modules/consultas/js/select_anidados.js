function buscar_municipios(a){
  //var departamento = $('#departamento').val();
   var departamento = a;


    var datos = {
        "departamento":departamento

    };

      $.ajax({
        url:'functions/fn_buscar_municipios.php',
        type:'post',
        dataType:'html',
        data:datos,
        beforeSend: function(){


        },
        success:function(response){
           // alert(response);
           $('#municipio').html(response);

        }
      })
       .done(function(data){
          console.log("success");


       })
       .fail(function(){
        console.log("error");

       })
       .always(function(){

        console.log("complete");





        setTimeout(function(){



                                   }, 0);

       });

}

function nombres(){
  $('#municipio_nm').val( $('#municipio option:selected').text() );
  $('#institucion_nm').val( $('#institucion option:selected').text() );
  $('#sede_nm').val( $('#sede option:selected').text() );
  $('#estudiante_nm').val( $('#estudiante option:selected').text() );

}




function buscar_instituciones(){
  console.log('Inicia Buscar Instituciones');
  var municipio = $('#municipio').val();
  var aux = '<option value = "" >TODOS</option>';
  $('#sede').html(aux);
  $('#estudiante').html(aux);








    var datos = {
        "municipio":municipio

    };

      $.ajax({
        url:'functions/fn_buscar_instituciones.php',
        type:'post',
        dataType:'html',
        data:datos,
        beforeSend: function(){


        },
        success:function(response){
           //$('#debug').html(response);
           $('#institucion').html(response);
            console.log('Actualizando el municipio nm');
            $('#municipio_nm').val( $('#municipio option:selected').text() );

        }
      })
       .done(function(data){
          console.log("success");


       })
       .fail(function(){
        console.log("error");

       })
       .always(function(){

        console.log("complete");





        setTimeout(function(){



                                   }, 0);

       });


console.log('Termina Buscar Instituciones');
}








function buscar_sedes(){
  console.log('Inicia Buscar Sedes');
  var aux = '<option value = "" >TODOS</option>';
  $('#estudiante').html(aux);
  $('#estudiante').val('');





  $('#institucion_nm').val( $('#institucion option:selected').text() );

  var institucion = $('#institucion').val();



    var datos = {
        "institucion":institucion

    };

      $.ajax({
        url:'functions/fn_buscar_sedes.php',
        type:'post',
        dataType:'html',
        data:datos,
        beforeSend: function(){


        },
        success:function(response){
           // alert(response);
           $('#sede').html(response);
           $('#institucion_nm').val( $('#institucion option:selected').text() );

        }
      })
       .done(function(data){
          console.log("success");


       })
       .fail(function(){
        console.log("error");

       })
       .always(function(){

        console.log("complete");





        setTimeout(function(){



                                   }, 0);

       });


  console.log('Termina Buscar Sedes');
}



function buscar_estudiantes(){

  $('#sede_nm').val( $('#sede option:selected').text() );

  var sede = $('#sede').val();



    var datos = {
        "sede":sede

    };

      $.ajax({
        url:'functions/fn_buscar_estudiantes.php',
        type:'post',
        dataType:'html',
        data:datos,
        beforeSend: function(){


        },
        success:function(response){
           // alert(response);
           $('#estudiante').html(response);
             $('#sede_nm').val( $('#sede option:selected').text() );

        }
      })
       .done(function(data){
          console.log("success");


       })
       .fail(function(){
        console.log("error");

       })
       .always(function(){

        console.log("complete");





        setTimeout(function(){



                                   }, 0);

       });



}

function graficar (){
  console.log("Iniciando de nuevo Tercer Ajax");

  var municipio = $('#municipio').val();
  var institucion = $('#institucion').val();
  var sede = $('#sede').val();
  var estudiante = $('#estudiante').val();

  var diainicial = $('#diainicial').val();
  var mesinicial = $('#mesinicial').val();
  var annoinicial = $('#annoinicial').val();


  var diafinal = $('#diafinal').val();
  var mesfinal = $('#mesfinal').val();
  var annofinal = $('#annofinal').val();









  var tipoGrafico = $('#tipoGrafico').val();
  var segmento = $('#segmento').val();



  var datos = {







        "municipio":municipio,
        "institucion":institucion,
        "sede":sede,
        "estudiante":estudiante,
        "diainicial":diainicial,
        "mesinicial":mesinicial,
        "annoinicial":annoinicial,
        "diafinal":diafinal,
        "mesfinal":mesfinal,
        "annofinal":annofinal,





        "tipoGrafico":tipoGrafico,
        "segmento":segmento

    };


      $.ajax({
        url:'functions/fn_grafica.php',
        type:'post',
        dataType:'html',
        data:datos,
        beforeSend: function(){


        },
        success:function(response){
           // alert(response);
           $('#contenedorGrafico').html(response);

        }
      })
       .done(function(data){
          console.log("success");


       })
       .fail(function(){
        console.log("error");

       })
       .always(function(){

        console.log("complete");





        setTimeout(function(){



                                   }, 0);

       });


}






// Funciones de consultas detalladas
function actualizaranno(){
  aux = $('#annoinicial').val();
  $('#annofinal').val(aux);
}

function actualizarmes(){
  console.log('Actualizar Mes');
  aux = $('#mesinicial').val();
  $('#mesfinal').val(aux);
  aux = $("#mesinicial option:selected").text();
  console.log(aux);
  $('#mesfinalnm').val(aux);
}


function enviarForm(){
   console.log('Validando el form para hacer la consulta.');
   var municipio = '';
   var mesinicial = '';
   var mesfinal = '';
   var diainicial = '';
   var diafinal = '';

   municipio = $('#municipio').val();
   mesinicial = $('#mesinicial').val();
   mesfinal = $('#mesfinal').val();
   diainicial = $('#diainicial').val();
   diafinal = $('#diafinal').val();

   console.log('Municipio: '+municipio);


   var bandera = 0;



   if( municipio == '' && bandera == 0 ){
     bandera++;
     alert('Debe seleccionar un municipio para hacer la consulta.');
     $('#municipio').focus();
   }

   if( mesinicial == '' && bandera == 0 ){
     bandera++;
     alert('Debe seleccionar el mes inicial hacer la consulta.');
     $('#mesinicial').focus();
   }

   mesinicial = parseInt(mesinicial);
   mesfinal = parseInt(mesfinal);

   if (mesinicial > mesfinal && bandera == 0){
     bandera++;
     alert('La fecha final debe ser posterior o igual a la fecha inicial.');
     $('#mesfinal').focus();
   }


   if(diainicial > diafinal && bandera == 0){
     bandera++;
     alert('La fecha final debe ser posterior o igual a la fecha inicial.');
     $('#diafinal').focus();
   }





   if(bandera == 0){
     $('#parametros').submit();
     console.log('Enviando el form para hacer la consulta.');
   }



 } //Termina la función para envíar el formulario
