<?php
$usuario = '';
$tipoUsuario = '';
$idUsr = '';
$fotoUsr = '';
require_once "db/conexion.php";
require_once "config.php";
//include 'autentication.php';
// $idUsr = $_SESSION['id_usuario']; 
// $fotoUsr = $_SESSION['foto']; 
?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $tituloProyecto; ?> <?php if(isset($titulo)){echo " - $titulo";} ?></title>

    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/favicon.ico" />

    <!-- Plugins -->
    <link href="<?php echo $baseUrl; ?>/theme/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/animate.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/dropzone/basic.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/dropzone/dropzone.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/jasny/jasny-bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/codemirror/codemirror.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/toggle/toggle.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/css/style.css" rel="stylesheet">

</head>

<body>
    <input type="hidden" id="inputBaseUrl" value="<?php echo $baseUrl; ?>">
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span> 

                                <?php if ($fotoUsr  != ''){ ?>

                                <img alt="image" class="img-circle" src="<?php echo $baseUrl; ?>/<?php echo $fotoUsr ; ?>" /> 

<?php } ?>

                            </span>
                            
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
                    <a href="#">
                        <i class="fa fa-sign-out"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </nav>
        </div>
