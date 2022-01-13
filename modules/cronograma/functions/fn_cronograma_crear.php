<?php
	require_once '../../../db/conexion.php';
    require_once '../../../config.php';

    $post = (object) $_POST;

    $sede = (isset($post->sede) && $post->sede != '') ? mysqli_real_escape_string($Link, $post->sede) : '';
    $fecha_desde = (isset($post->fecha_desde) && $post->fecha_desde != '') ? mysqli_real_escape_string($Link, $post->fecha_desde) : '';
    $fecha_hasta = (isset($post->fecha_hasta) && $post->fecha_hasta != '') ? mysqli_real_escape_string($Link, $post->fecha_hasta) : '';
    $mes = (isset($post->mes) && $post->mes != '') ? (int) mysqli_real_escape_string($Link, $post->mes) : '';
    $semana = (isset($post->semana) && $post->semana != '') ? (int) mysqli_real_escape_string($Link, $post->semana) : '';
    $horario = (isset($post->horario) && $post->horario != '') ? mysqli_real_escape_string($Link, $post->horario) : '';

    $c_cronograma_existente = "SELECT * FROM cronograma WHERE cod_sede = '".$sede."' AND mes='".$mes."';";
    $r_cronograma_existente = $Link->query($c_cronograma_existente) or die("Error al consultar el cronograma existente: ". $Link->error);

    if ($r_cronograma_existente->num_rows > 0) {
        echo json_encode([
            "response"=>0,
            "message"=>"Ya existe un cronograma asignado para la Sede en el mes seleccionado."
        ]);
        exit();
    }

    $c_crear = "INSERT INTO cronograma (mes, semana, cod_sede, fecha_desde, fecha_hasta, horario) VALUES ('$mes', '$semana', '$sede', '$fecha_desde', '$fecha_hasta', '$horario');";
    $r_crear = $Link->query($c_crear) or die("Error al insertar cronograma: ". $Link->error);
    if ($r_crear) {
        $r_ajax = json_encode([
            "response"=>1,
            "message"=>"El cronograma ha sido creado exitosamente."
        ]);
    } else {
        $r_ajax = json_encode([
            "response"=>0,
            "message"=>"No fue posible crear el cronograma."
        ]);
    }

    echo $r_ajax;