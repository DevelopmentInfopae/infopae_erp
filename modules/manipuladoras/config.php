<?php
session_start();
date_default_timezone_set('America/Bogota');
$tituloProyecto = 'InfoPAE2019';
$baseUrl = 'http://127.0.0.1/infopae2019/modules/manipuladoras';
$nodeUrl = 'http://127.0.0.1:8080';
$token_seguridad = sha1($baseUrl);
$rootUrl = "/".$_SERVER['DOCUMENT_ROOT'].'/infopae2019/modules/manipuladoras';
$_SESSION['periodoActualCompleto'] = 2019;
$_SESSION['periodoActual'] = 19;
$_SESSION['mesPeriodoActual'] = 1;