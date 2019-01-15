<?php
session_start();
date_default_timezone_set('America/Bogota');
$tituloProyecto = 'InfoPAE2019';
// $baseUrl = 'http://192.254.194.178/~infopae/giron2019';
$baseUrl = 'http://localhost/infopae2019';
$rootUrl = $_SERVER['DOCUMENT_ROOT'].'/infopae2019';
$_SESSION['periodoActualCompleto'] = 2019;
$_SESSION['periodoActual'] = 19;
$_SESSION['mesPeriodoActual'] = 1;
