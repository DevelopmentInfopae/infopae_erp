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
    $consulta_parametros = "SELECT * FROM parametros WHERE id = 1";
    $result_parametros = $Link->query($consulta_parametros);
    $parametros = false;
    if ($result_parametros->num_rows > 0) {
        $parametros = $result_parametros->fetch_assoc();
    }
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
    <link href="<?php echo $baseUrl; ?>/theme/fontawesome-free-5.11.2-web/css/all.css" rel="stylesheet">

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
    <link href="<?php echo $baseUrl; ?>/theme/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">

    <link href="<?php echo $baseUrl; ?>/theme/css/style.css" rel="stylesheet">

</head>
<body>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span>
                                <?php
                                $fotoUsr = substr( $fotoUsr, 5);
                                $foto = $baseUrl.$fotoUsr;
                                if(!is_url_exist($foto)){
                                    $foto = $baseUrl."/img/no_image48.jpg";
                                }
                                ?>
                                <img alt="image" style="width: 48px; height: auto;" class="img-circle" src="<?php echo $foto; ?>" />
                            </span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="theme/#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"><?php echo $usuario; ?>



                            </strong>
                             </span> <span class="text-muted text-xs block"><?php echo $tipoUsuario; ?><b class="caret"></b></span> </span> </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <!-- <li><a href="theme/profile.html">Profile</a></li> -->
                                <!-- <li><a href="theme/contacts.html">Contacts</a></li> -->
                                <!-- <li><a href="theme/mailbox.html">Mailbox</a></li> -->
                                <!-- <li class="divider"></li> -->
                                <!-- <li><a href="theme/login.html">Cambiar Contraseña</a></li> -->
                                <li><a href="<?php echo $baseUrl; ?>/modules/perfil">Actualizar datos</a></li>
                                <li><a href="<?php echo $baseUrl; ?>/cerrar_sesion.php">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                        <div class="logo-element">
                            InfoPAE
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


<!-- Button trigger modal -->
<input type="hidden" name="inputBaseUrl" id="inputBaseUrl" value="<?php echo $baseUrl; ?>">

<div class="modal inmodal fade" id="modalEliminar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar el producto?</span>
         <input type="hidden" name="codigoProductoEli" id="codigoProductoEli">
         <input type="hidden" name="ordenCicloEli" id="ordenCicloEli">
         <input type="hidden" name="tipoComplementoEli" id="tipoComplementoEli">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarProducto()">Si</button>
     </div>
   </div>
 </div>
</div>

<div class="modal inmodal fade" id="modalEliminarFTDet" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar el producto?</span>
         <input type="hidden" name="idftd" id="idftd">
         <input type="hidden" name="numFTD" id="numFTD">
         <input type="hidden" name="idproducto" id="idproducto">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarFTDet()">Si</button>
     </div>
   </div>
 </div>
</div>

<div class="modal inmodal fade" id="modalEliminarInfraestructura" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar los datos de la infraestructura seleccionada?</span>
         <input type="hidden" name="idinfraestructura" id="idinfraestructura">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarInfraestructura()">Si</button>
     </div>
   </div>
 </div>
</div>
