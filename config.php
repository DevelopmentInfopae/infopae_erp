<?php
session_start();
date_default_timezone_set('America/Bogota');
$tituloProyecto = 'InfoPAE ONLINE';

$baseUrl = 'http://infopaedemos.com/infopaeOnline/app';

$token_seguridad = sha1($baseUrl);

$rootUrl = "/".$_SERVER['DOCUMENT_ROOT'].'/infopaeOnline/app/';

$infopaeData = '/home/infopaedemos/infopaedata/';

$_SESSION['periodoActualCompleto'] = 2021;
$_SESSION['periodoActual'] = 21;
$_SESSION['mesPeriodoActual'] = 1;
