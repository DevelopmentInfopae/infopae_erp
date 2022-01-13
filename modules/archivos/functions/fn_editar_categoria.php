<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';
include_once 'funciones.php';
//var_dump($_POST);
if(isset($_POST['categoria']) && $_POST['categoria'] != '' && isset($_POST['id']) && $_POST['id'] != ''){
	$categoria = $_POST['categoria'];
	$id = $_POST['id'];
    $consulta = " update mod_archivos_categorias set categoria = \"$categoria\" where id = $id";
    $Link->query($consulta) or die ('Error al editar registro en la tabla de categorias '. mysqli_error($Link));
    if($Link->affected_rows > 0){
        echo '1';
    }
}
