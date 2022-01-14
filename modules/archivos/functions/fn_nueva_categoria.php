<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';
include_once 'funciones.php';
//var_dump($_POST);
if(isset($_POST['categoria']) && $_POST['categoria'] != ''){
	$categoria = $_POST['categoria'];
    $consulta = " insert into mod_archivos_categorias (categoria) values (\"$categoria\")";
    $Link->query($consulta) or die ('Error al insertar registro en la tabla de categorias '. mysqli_error($Link));
    if($Link->affected_rows > 0){
        echo '1';
    }
}
