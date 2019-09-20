<?php
    include 'config.php';
    $usuario = '';
    $tipoUsuario = '';
    $idUsr = '';
    $fotoUsr = '';
    require_once 'db/conexion.php';
    include 'autentication.php';
    include 'php/funciones.php';
    $idUsr = $_SESSION['id_usuario'];
    $fotoUsr = $_SESSION['foto'];

    $dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
    if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $tituloProyecto; ?> <?php if(isset($titulo)){echo " - $titulo";} ?></title>

    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/favicon.ico" />

    <link href="<?php echo $baseUrl; ?>/theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/font-awesome/css/font-awesome.css" rel="stylesheet">

    <!-- Toastr style -->
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/toastr/toastr.min.css" rel="stylesheet">

    <!-- Gritter -->
    <link href="<?php echo $baseUrl; ?>/theme/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

    <link href="<?php echo $baseUrl; ?>/theme/css/animate.css" rel="stylesheet">

    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/dropzone/basic.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/dropzone/dropzone.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/codemirror/codemirror.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/steps/jquery.steps.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/toggle/toggle.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/select2/select2.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/fullcalendar/fullcalendar.css" rel="stylesheet">


    <link href="<?php echo $baseUrl; ?>/theme/css/style.css" rel="stylesheet">

</head>

<body class="gray-bg">

    <div class="middle-box text-center animated fadeInDown">
        <h1>404</h1>
        <h3 class="font-bold">Página no encontrada</h3>

        <div class="error-desc">
            <p>¡Vaya! no hemos podido encontrar la página que buscas.</p>
            <p>Trata de volver al inicio de nuestro sitio dando clic en el botón.</p>
            <!-- <form class="form-inline m-t" role="form" action="<?= $baseUrl; ?>"> -->
                <!-- <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search for page">
                </div> -->
                <button type="button" class="btn btn-primary" onclick="window.location.href = '<?= $baseUrl; ?>'"><i class="fa fa-reply"></i> Inicio</button>
            <!-- </form> -->
        </div>
    </div>

</body>

</html>
