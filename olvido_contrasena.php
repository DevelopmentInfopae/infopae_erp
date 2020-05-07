<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $tituloProyecto; ?></title>
    <link rel="shortcut icon" href="favicon.ico" />

    <link href="theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="theme/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="theme/css/animate.css" rel="stylesheet">
    <link href="theme/css/style.css" rel="stylesheet">

</head>

<body class="gray-bg fnd-login">

    <div class="middle-box text-center loginscreen animated fadeInDown caja-login">
        <div>
            <div>


                <img src="img/logo.png" alt="logo" class="logo" >


            </div>





            <form class="m-t" role="form" action="">
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Usuario" required="" id="username" name="username">

                </div>


                <button type="button" class="btn btn-primary block full-width m-b" onclick="javascript:doRestablecer()">Restablecer</button>
     

          
              </form>
              <div id="debug"></div>
            <p class="m-t"> <small>&copy; 2020</small> </p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="theme/js/jquery-3.1.1.min.js"></script>
    <script src="theme/js/bootstrap.min.js"></script>

    <script src="js/funciones.js"></script>
    <script src="js/login.js"></script>









<script type="text/javascript">

function doRestablecer() {


    showLoad();



     var user=$("#username").val();

     if(user == ''){
        alert('Por favor ingrese su usuario');
        $("#username").focus();


     }

     else{







  $.ajax({
    type: "POST",
    url: "functions/fn_olvidocontrasena.php",
        data:{
             user:user

            },

    success: function(i){
        //alert(i);

        //$('#debug').html(i);

        console.log(i);


      if (i==1) {
      alert('Se ha enviado un correo con su nueva contraseña.');
      window.location = "index.php";
      }
      else if (i==3) {
      alert('No se encontro el usuario en la base de datos.');
      }
      else{
        alert('Se ha presentado un error al intentar restablecer su contraseña');
        $('#debug').html(i);
      }

/*
      else if (i==4) {
      window.location = "carga_de_archivos.php";
      } else if (i==0) {
      alert('Usuario o contraseña incorrectos ');
      }*/

    }
  });
}

}

function showBtn(){
  $('#imgLoading').css('display','none');
  $('#btnLogin').css('display','block');
}

function showLoad(){
  $('#btnLogin').css('display','none');
  $('#imgLoading').css('display','block');
}


 var tecla;
    function capturaTecla(e)
    {
        if(document.all)
            tecla=event.keyCode; // ie
        else
        {
            tecla=e.which;   // Netscape/Firefox/Opera
        }
     if(tecla==13)
        {
            //alert('A pulsado la tecla enter');
            doLogin();
        }
    }

        //document.onkeydown = capturaTecla;
function iSubmitEnter(oEvento, oFormulario){
     var iAscii;

     if (oEvento.keyCode)
         iAscii = oEvento.keyCode;
     else if (oEvento.which)
         iAscii = oEvento.which;
     else
         return false;

     if (iAscii == 13) oFormulario.submit();

     return true;
}

</script>



</body>

</html>