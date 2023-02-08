<?php
include '../../config.php';
include '../../autentication.php';

$archivo="gumball.pdf";
if(isset($_GET['file']) && $_GET['file'] != ""){
    $archivo = $_GET['file'];
}

///home/aulaguanenta/moodledata
$imgpath = $infopaeData.$archivo;
$imginfo = getimagesize($imgpath);
$mimetype = $imginfo['mime'];
header('Content-type: ' . $mimetype);
readfile($imgpath);