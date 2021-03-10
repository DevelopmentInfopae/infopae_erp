<?php
    require_once '../../../db/conexion.php';
    require_once '../../../config.php';

    $post = (object) $_POST;

    $id = (isset($post->id) && $post->id != '') ? mysqli_real_escape_string($Link, $post->id) : '';
    $fecha = (isset($post->fecha) && $post->fecha != '') ? mysqli_real_escape_string($Link, $post->fecha) : '';
    $titulo = (isset($post->titulo) && $post->titulo != '') ? mysqli_real_escape_string($Link, $post->titulo) : '';
    $descripcion = (isset($post->descripcion) && $post->descripcion != '') ? mysqli_real_escape_string($Link, $post->descripcion) : '';

    $consulta = "UPDATE noticias SET fecha = '$fecha', titulo = '$titulo', descripcion = '$descripcion' WHERE id = '$id'";
    $respuesta = $Link->query($consulta) or die ("Error al actualizar el registro de noticia: ". $Link->error);

    if ($respuesta) {
        if (isset($_FILES["imagen"])) {
            $logo_etc = subir_imagen($_FILES["imagen"], "image", $id, $Link);
            if ($logo_etc->estado == 0) {
                echo json_encode($logo_etc);
                exit();
            }
        }

        echo json_encode([
            "estado" => 1,
            "mensaje" => "La noticia han sido actualizada exitosamente."
        ]);
    }

    function subir_imagen($imagen, $nombre_imagen, $id, $Link)
    {
        $dimensiones = getimagesize($imagen["tmp_name"]);
        $ratio = getAspectRatio($dimensiones[0], $dimensiones[1]);


        if ($ratio < 3) {
            return (object) [
                "estado" => 0,
                "mensaje" => "Por favor ingresar una imagen tipo banner para la imagen: ". $nombre_imagen
            ];
        } else if($imagen["size"] > 1048576) {
            return (object) [
                "estado" => 0,
                "mensaje" => "La imagen supera el tamaño permitido 1 MegaBytes. Por favor ingresar una imagen de igual o menor tamaño para la imagen ". $nombre_imagen
            ];
        } else if($imagen["type"] == "image/jpg" || $imagen["type"] == "image/jpeg") {
            $ruta_imagen = "../../upload/noticias/noticia_" . $id . ".jpg";
            $subido = move_uploaded_file($imagen["tmp_name"], "../" . $ruta_imagen);

            if ($subido) {
                $consulta2 = " UPDATE noticias SET imagen = '$ruta_imagen' WHERE id = '$id' ";
                $resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
            }

            return (object) [
                "estado" => 1,
                "mensaje" => "Los parámetros han sido actualizado exitosamente."
            ];
        } else {
            return (object) [
                "estado" => 0,
                "mensaje" => "La extensión del la imagen no es la permitida. Tipo de archivo permitido: .jpg, .jpeg para la imagen ". $nombre_imagen
            ];
        }
    }

    function getAspectRatio($width, $height)
    {
        $wx = getDivisorList($width);
        $hx = getDivisorList($height);

        $aspect = '';
        $ratio = 0;

        foreach($wx as $div => $num) {
            if(isset($hx[$div])) {
                $aspect = $num.":".$hx[$div];
                $ratio = $num / $hx[$div];
                break;
            }
        }

        return round($ratio);
    }

    function getDivisorList($px) {
        $dlist = [];
        $i = 1;
        while($px / $i >= 1) {
            if($px % $i == 0) {
                $div = $px / $i;
                $dlist[$div] = $px / $div;
            }
            $i++;
        }

        return $dlist;
    }

    // $c_cronograma_existente = "SELECT * FROM cronograma WHERE cod_sede = '".$sede."' AND mes='".$mes."';";
    // $r_cronograma_existente = $Link->query($c_cronograma_existente) or die("Error al consultar el cronograma existente: ". $Link->error);

    // if ($r_cronograma_existente->num_rows > 0) {
    //     echo json_encode([
    //         "response"=>0,
    //         "message"=>"Ya existe un cronograma asignado para la Sede en el mes seleccionado."
    //     ]);
    //     exit();
    // }

    // $c_crear = "INSERT INTO cronograma (mes, semana, cod_sede, fecha_desde, fecha_hasta, horario) VALUES ('$mes', '$semana', '$sede', '$fecha_desde', '$fecha_hasta', '$horario');";
    // $r_crear = $Link->query($c_crear) or die("Error al insertar cronograma: ". $Link->error);
    // if ($r_crear) {
    //     $r_ajax = json_encode([
    //         "response"=>1,
    //         "message"=>"El cronograma ha sido creado exitosamente."
    //     ]);
    // } else {
    //     $r_ajax = json_encode([
    //         "response"=>0,
    //         "message"=>"No fue posible crear el cronograma."
    //     ]);
    // }

    // echo $r_ajax;