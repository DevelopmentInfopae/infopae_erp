<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$usuarioSession = 90;
	$id = $_POST["id"];
	$nombre = $_POST["nombre"];
	$fechaHora = date("Y-m-d H-i-s");
	$nombreEtc = $_POST["nombreEtc"];
	$departamento = $_POST["departamento"];
	$numeroContrato = $_POST["numeroContrato"];
	$nombredepartamento = $_POST["nombredepartamento"];

	$consulta1 = "UPDATE parametros
								SET
									Operador = '$nombre',
									NumContrato = '$numeroContrato',
									CodDepartamento = '$departamento',
									NombreETC = '$nombreEtc',
									Departamento = '$nombredepartamento'
								WHERE id = '$id'";
	$resultado1 = $Link->query($consulta1) or die ("Unable to execute query.". mysql_error($Link));
	if ($resultado1){
		if (isset($_FILES["foto"]["name"])){
			$dimensiones = getimagesize($_FILES["foto"]["tmp_name"]);

			$ratio = getAspectRatio($dimensiones[0], $dimensiones[1]);
			if ($ratio < 6){
				$result = array(
					"estado" => 0, 
					"mensaje" => "Por favor ingresar una imagen tipo banner"
				);
			} else if($_FILES["foto"]["size"] > 5242880){
				$result = array(
					"estado" => 0, 
					"mensaje" => "La imagen supera el tamaño permitido 5 MegaBytes. Por favor ingresar una imagen de igual o menor tamaño"
				);
			} else if($_FILES["foto"]["type"] == "image/jpg" || $_FILES["foto"]["type"] == "image/jpeg"){
				$rutaFoto = "../../upload/logotipos/logo" . $id . ".jpg";
				$subido = move_uploaded_file($_FILES["foto"]["tmp_name"], "../" . $rutaFoto);

				if ($subido){
					$consulta2 = " UPDATE parametros SET LogoETC = '$rutaFoto' WHERE id = '$id' ";
					$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
				}

				$result = array(
					"estado" => 1, 
					"mensaje" => "Los parámetros han sido actualizado exitosamente."
				);

				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('$fechaHora', '$usuarioSession', '24', 'Actualizó los parámetros iniciales del sistema y el logotipo')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			} else {
				$result = array(
					"estado" => 0, 
					"mensaje" => "La extensión del la imagen no es la permitida. Tipo de archivo permitido: .jpg, .jpeg"
				);
			}
		} else {
			$result = array(
				"estado" => 1, 
				"mensaje" => "Los parámetros han sido actualizado exitosamente."
			);

			$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('$fechaHora', '$usuarioSession', '24', 'Actualizó los parámetros iniciales del sistema')";
			$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
		}
	} else {
		$result = array(
			"estado" => 0, 
			"mensaje" => "Los parámetros NO han sido actualizado."
		);
	}

	function getAspectRatio($width, $height) {
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

    // return array('aspect'=>$aspect, 'ratio'=>$ratio);
    return round($ratio);
}

	echo json_encode($result);