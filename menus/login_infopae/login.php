<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cátering</title>
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
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Contraseña" required="" name="password" id="password">
                </div>
                 <!-- <div class="form-group">
                    <select name="periodo" id="periodo" class="form-control">
                    <option value="2017" selected="selected">2017</option>
                    <option value="2016">2016</option>
                </select> -->
                </div>
                <button type="button" id="btnLogin" class="btn btn-primary block full-width m-b">Entrar</button>

                <a href="theme/#"><small>Olvidó su contraseña?</small></a>
              </form>
              <div id="debug"></div>
            <p class="m-t"> <small>&copy; 2018</small> </p>
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
