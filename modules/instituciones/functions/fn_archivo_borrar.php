<?php
var_dump($_POST);
$nombre = $_POST['nombre'];
$ruta = "upload/".$nombre.".jpg";
unlink($ruta);