<?php
include '../../config.php';
include '../../autentication.php';



// $CFG->dataroot  = 'G:\xampp\htdocs\moodle_guanenta\moodledata';
// $CFG->admin     = 'admin';

// $CFG->directorypermissions = 0777;

// require_once(__DIR__ . '/lib/setup.php');



//$imgpath = __DIR__ . '/../imagen1.png';

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