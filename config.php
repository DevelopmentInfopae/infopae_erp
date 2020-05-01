<?php
session_start();
date_default_timezone_set('America/Bogota');
$tituloProyecto = 'InfoPAE2019';
//$baseUrl = 'http://192.254.194.178/~infopae/infopae2019';
// $baseUrl = 'http://192.168.1.52/infopae2019';
$baseUrl = 'http://localhost/infopae2019';
$token_seguridad = sha1($baseUrl);
$rootUrl = "/".$_SERVER['DOCUMENT_ROOT'].'/infopae2019';
$_SESSION['periodoActualCompleto'] = 2020;
$_SESSION['periodoActual'] = 20;
$_SESSION['mesPeriodoActual'] = 1;
