<?php
	require_once '../../../db/conexion.php';
    require_once '../../../config.php';

    $post = (object) $_POST;

    $semana = (isset($post->semana) && $post->semana != '') ? mysqli_real_escape_string($Link, $post->semana) : '';
    $horario = (isset($post->horario) && $post->horario != '') ? mysqli_real_escape_string($Link, $post->horario) : '';
    $fecha_desde = (isset($post->fecha_desde) && $post->fecha_desde != '') ? mysqli_real_escape_string($Link, $post->fecha_desde) : '';
    $fecha_hasta = (isset($post->fecha_hasta) && $post->fecha_hasta != '') ? mysqli_real_escape_string($Link, $post->fecha_hasta) : '';
    $cronograma_id = (isset($post->cronograma_id) && $post->cronograma_id != '') ? mysqli_real_escape_string($Link, $post->cronograma_id) : '';

    $c_editar = "UPDATE cronograma SET semana = '".$semana."', fecha_desde = '".$fecha_desde."', fecha_hasta = '".$fecha_hasta."', horario = '".$horario."' WHERE id = '".$cronograma_id."'";
    $r_editar = $Link->query($c_editar) or die("Error al editar el cronograma: ". $Link->error);
    if ($r_editar) {
        $r_ajax = json_encode([
            "response"=>1,
            "message"=>"El cronograma ha sido actualizado exitosamente."
        ]);
    } else {
        $r_ajax = json_encode([
            "response"=>0,
            "message"=>"No fue posible actualizar el cronograma."
        ]);
    }

    echo $r_ajax;