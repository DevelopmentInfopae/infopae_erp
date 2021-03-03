<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';
include_once 'funciones.php';
//var_dump($_POST);
if(isset($_POST['id']) && $_POST['id'] != ''){
	$id = $_POST['id'];
    $consulta = "delete from mod_archivos_categorias where id = $id";
    //echo $consulta;
    $Link->query($consulta) or die ('Error al eliminar registro en la tabla de categorias '. mysqli_error($Link));
    if($Link->affected_rows > 0){
        echo '1';
    }
}
