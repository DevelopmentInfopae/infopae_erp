<?php
    include '../../config.php';
    $usuario = '';
    $tipoUsuario = '';
    $idUsr = '';
    $fotoUsr = '';
    require_once '../../db/conexion.php';
    include '../../autentication.php';
    include '../../php/funciones.php';
    $idUsr = $_SESSION['id_usuario'];
    $fotoUsr = $_SESSION['foto'];

    $dato_municipio = $Link->query("SELECT CodMunicipio FROM parametros") or die(mysqli_error($Link));
    if ($dato_municipio->num_rows > 0) { $municipio_defecto = $dato_municipio->fetch_array(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Entregas Biometricas</title>
    <link rel="shortcut icon" href="<?php echo $baseUrl; ?>/favicon.ico" />
    <link href="<?php echo $baseUrl; ?>/theme/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $baseUrl; ?>/theme/font-awesome/css/font-awesome.css" rel="stylesheet">
    <!-- CSS de toda la aplicación -->
    <link href="<?php echo $baseUrl; ?>/theme/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="barra-top">
        <button></button>
        <div class="logo-dashboard"></div>
        <h1></h1>
        <div class="fecha-hora"></div>
    </div>

    <div class="contenedor-dashboard">
        <div class="row">
            <div class="col-sm-6">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas inventore dolore deleniti cumque saepe eaque officia alias, ut dolor sit ducimus? Deserunt, quam perspiciatis consequatur magni temporibus debitis itaque fuga.</p>
            </div>
            <div class="col-sm-6">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas inventore dolore deleniti cumque saepe eaque officia alias, ut dolor sit ducimus? Deserunt, quam perspiciatis consequatur magni temporibus debitis itaque fuga.</p>
            </div>
        </div>    

        <div class="row">
            <div class="col-sm-12 grafica-dashboard">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas inventore dolore deleniti cumque saepe eaque officia alias, ut dolor sit ducimus? Deserunt, quam perspiciatis consequatur magni temporibus debitis itaque fuga.</p>
            </div>
        </div>    


        <div class="row">
            <div class="col-sm-6">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas inventore dolore deleniti cumque saepe eaque officia alias, ut dolor sit ducimus? Deserunt, quam perspiciatis consequatur magni temporibus debitis itaque fuga.</p>
            </div>
            <div class="col-sm-6">
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptas inventore dolore deleniti cumque saepe eaque officia alias, ut dolor sit ducimus? Deserunt, quam perspiciatis consequatur magni temporibus debitis itaque fuga.</p>
            </div>
        </div>    

    </div>










    <!-- Mainly scripts -->
    <script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
</body>
</html>