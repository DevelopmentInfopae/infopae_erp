<?php
    require_once '../../../db/conexion.php';
    require_once '../../../config.php';

    $post = (object) $_POST;

    $id = (isset($post->id) && $post->id != '') ? mysqli_real_escape_string($Link, $post->id) : '';

    $c_noticia = "SELECT * FROM noticias WHERE id = '$id'";
    $r_noticia = $Link->query($c_noticia) or die("Error al consultar la noticia: ". $Link->error);
    if ($r_noticia->num_rows > 0) {
    	$noticia = $r_noticia->fetch_object();

    	echo json_encode([
    		"estado"=>1,
    		"datos"=>$noticia
    	]);

    } else {
    	echo json_encode([
    		"estado"=>0,
    		"mensaje"=>"No fue posible obtener los datos de la noticia."
    	]);
    }