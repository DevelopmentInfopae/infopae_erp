<?php
$usuario = '';
$tipoUsuario = '';
include 'config.php';
include 'autentication.php';
?>



<!DOCTYPE html>
<?php header('Access-Control-Allow-Origin: *'); ?>
<html>

<head>
    <?php 
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
     ?>

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
    <link href="<?php echo $baseUrl; ?>/theme/css/style.css" rel="stylesheet">







<link href="<?php echo $baseUrl; ?>/theme/css/bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $baseUrl; ?>/theme/font-awesome/css/font-awesome.css" rel="stylesheet">
<link href="<?php echo $baseUrl; ?>/theme/css/animate.css" rel="stylesheet">
<link href="<?php echo $baseUrl; ?>/theme/css/plugins/dropzone/basic.css" rel="stylesheet">
<link href="<?php echo $baseUrl; ?>/theme/css/plugins/dropzone/dropzone.css" rel="stylesheet">
<link href="<?php echo $baseUrl; ?>/theme/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
<link href="<?php echo $baseUrl; ?>/theme/css/plugins/codemirror/codemirror.css" rel="stylesheet">
<link href="<?php echo $baseUrl; ?>/theme/css/style.css" rel="stylesheet">










</head>

<body>



    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <!-- <span> <img alt="image" class="img-circle" src="theme/img/profile_small.jpg" /> </span> -->
                            <a data-toggle="dropdown" class="dropdown-toggle" href="theme/#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $usuario; ?></strong>
                             </span> <span class="text-muted text-xs block"><?php echo $tipoUsuario; ?><b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <!-- <li><a href="theme/profile.html">Profile</a></li> -->
                                <!-- <li><a href="theme/contacts.html">Contacts</a></li> -->
                                <!-- <li><a href="theme/mailbox.html">Mailbox</a></li> -->
                                <!-- <li class="divider"></li> -->
                                <li><a href="theme/login.html">Cambiar Contraseña</a></li>
                                <li><a href="theme/login.html">Actualizar datos</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/cerrar_sesion.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                        <div class="logo-element">
                            Ctrin+
                        </div>
                    </li>
                    <?php include 'menu.php'; ?>
                </ul>
            </div>
        </nav>
        <div id="page-wrapper" class="gray-bg dashbard-1">

            <div class="row">
                <div class="" id="loader">
                    <div class="" id="loaderFondo">
                        <div class="" id="loaderContenedor">
                            <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div><!-- /#loader -->
            </div>


        <div class="row border-bottom">
        <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="theme/#"><i class="fa fa-bars"></i> </a>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <span class="m-r-sm text-muted welcome-message">Bienvenido a <?php echo $tituloProyecto; ?></span>
                </li>
                <li>
                    <a href="<?php echo $baseUrl; ?>/cerrar_sesion.php">
                        <i class="fa fa-sign-out"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </nav>
        </div>
