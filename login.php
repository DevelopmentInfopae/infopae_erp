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
  <style>
    #emContra{
      color : white;
      font-size : 10px;
    }
  </style>

    <div class="middle-box text-center loginscreen animated fadeInDown caja-login">
        <div>
            <div>


                <img src="img/logo.png" alt="logo" class="logo" >


            </div>

            <form class="m-t" role="form" action="">
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Usuario" required="" id="username" name="username">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Contraseña" required="" name="password" id="password">
                    <em id='emContra'>Su contraseña se encuentra encriptada de forma segura con el algoritmo de cifrado AES-256</em>
                </div>
                
                 <!-- <div class="form-group">
                    <select name="periodo" id="periodo" class="form-control">
                    <option value="2017" selected="selected">2017</option>
                    <option value="2016">2016</option>
                </select> -->
                </div>
                <button type="button" id="btnLogin" class="btn btn-primary block full-width m-b">Entrar</button>

                <a href="olvido_contrasena.php"><small>Olvidó su contraseña?</small></a>
              </form>
              <div id="debug"></div>
            <p class="m-t"> <small>&copy; 2024</small> </p>
        </div>
    </div>

    <div class="modal inmodal fade" id="ventanaInformar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header text-info" style="padding: 15px;">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
            <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
          </div>
          <div class="modal-body">
              <p class="text-center">Existe un sistema de InfoPAE iniciada. No es posible iniciar sesión. Cierre sesión e intentelo nuevamente.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Aceptar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Mainly scripts -->
    <script src="theme/js/jquery-3.1.1.min.js"></script>
    <script src="theme/js/bootstrap.min.js"></script>

    <script src="js/funciones.js"></script>
    <script src="js/login.js"></script>

    <script>
        $(document).ready(function() {
            $("#btnLogin").click(function(){
                doLogin();
            });
        });
    </script>

</body>

</html>
