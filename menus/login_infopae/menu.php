<li>
    <a href="#"><i class="fa fa-th-large"></i> <span class="nav-label">Menus</span><span class="fa arrow"></span></a>
    <ul class="nav nav-second-level collapse">
        <li> <a href="<?php echo $baseUrl; ?>/modules/menus"><i class="fa fa-clock-o"></i> <span class="nav-label">Menus</span></a> </li>
    </ul>
</li>

<?php $_SESSION['perfil'] = 1; ?>

<?php //Accesos perfil de rector ?>
<?php if($_SESSION['perfil'] == 6){ ?>
    <li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/institucion.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Mi institucion</span></a> </li>
<?php } ?>
<?php //Terminan los accesos perfil de rector ?>


<?php //Accesos perfil de administrador ?>
<?php if($_SESSION['perfil'] == 1){?>
    <li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/instituciones.php"><i class="fa fa-graduation-cap"></i> <span class="nav-label">Instituciones</span></a> </li>

    <li> <a href="<?php echo $baseUrl; ?>/modules/instituciones/sedes.php"><i class="fa fa-bank"></i> <span class="nav-label">Sedes educativas</span></a> </li>
<?php } ?>
<?php //Terminan los accesos perfil de administrador ?>
