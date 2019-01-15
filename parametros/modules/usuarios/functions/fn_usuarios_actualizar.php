<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$id = mysqli_real_escape_string($Link, $_POST["id"]);
	$email = mysqli_real_escape_string($Link, $_POST["email"]);
	$estado = mysqli_real_escape_string($Link, $_POST["estado"]);
	$nombre = mysqli_real_escape_string($Link, $_POST["nombre"]);
	$perfil = mysqli_real_escape_string($Link, $_POST["perfil"]);
	$telefono = mysqli_real_escape_string($Link, $_POST["telefono"]);
	$direccion = mysqli_real_escape_string($Link, $_POST["direccion"]);
	$municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);
	$fotoCargada = mysqli_real_escape_string($Link, $_POST["fotoCargada"]);
	$tipoUsuario = mysqli_real_escape_string($Link, $_POST["tipoUsuario"]);
	$numeroDocumento = mysqli_real_escape_string($Link, $_POST["numeroDocumento"]);

	//Validamos que el correo electronico a editar no se encuentre registrado.
	$consulta0 = "SELECT email FROM usuarios WHERE email LIKE '%$email%' AND id <> '$id';"; 
	$resultado0 = $Link->query($consulta0);
	if($resultado0->num_rows > 0){
		$resultadoAJAX = array(
			"estado" => 0, 
			"mensaje" => "El Correo electrónico ingresado ya se encuentra registrado. Por favor intente nuevamente con un correo diferente."
		);
	} else {
		$consultaEstado = ($estado != "") ? ", estado = '$estado'" : "";

		$consulta1 = " UPDATE usuarios SET num_doc = '$numeroDocumento', nombre = '$nombre', direccion = '$direccion', cod_mun = '$municipio', telefono = '$telefono', email = '$email', id_perfil = '$perfil', Tipo_Usuario = '$tipoUsuario'$consultaEstado  WHERE id = '$id' ";
		$resultado1 = $Link->query($consulta1) or die ("Unable to execute query.". mysql_error($Link));
		if ($resultado1){
			if (isset($_FILES["foto"]["name"])){
				$dimensiones = getimagesize($_FILES["foto"]["tmp_name"]);
				if ($dimensiones[0] != $dimensiones[1]){
					$resultadoAJAX = array(
						"estado" => 0, 
						"mensaje" => "Por favor ingresar una imagen de ratio aspecto 1:1 o cuadrada tipo documento"
					);
				} else if($_FILES["foto"]["size"] > 5120000){
					$resultadoAJAX = array(
						"estado" => 0, 
						"mensaje" => "La imagen supera el tamaño permitido 5 MegaBytes. Por favor ingresar una imagen de igual o menor tamaño"
					);
				} else if($_FILES["foto"]["type"] == "image/jpg" || $_FILES["foto"]["type"] == "image/jpeg" || $_FILES["foto"]["type"] == "image/png"){
					$rutaFoto = "../../upload/usuarios/U" . $id . ".jpg";
					$subido = move_uploaded_file($_FILES["foto"]["tmp_name"], "../" . $rutaFoto);

					if ($subido){
						$consulta2 = " UPDATE usuarios SET foto = '$rutaFoto' WHERE id = '$id' ";
						$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
					}

					$resultadoAJAX = array(
						"estado" => 1, 
						"mensaje" => "El usuario han sido actualizado exitosamente."
					);

					$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Actualizó los datos y la foto del usuario <strong>$nombre</strong>')";
					$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
				} else {
					$resultadoAJAX = array(
						"estado" => 0, 
						"mensaje" => "La extensión del la imagen no es la permitida. Tipo de archivo permitido: .jpg, .jpeg"
					);
				}
			} else {
				$resultadoAJAX = array(
					"estado" => 1, 
					"mensaje" => "El usuario han sido actualizado exitosamente."
				);

				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Actualizó los datos del usuario <strong>$nombre</strong>')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			}
		} else {
			$resultadoAJAX = array(
				"estado" => 0, 
				"mensaje" => "El usuario NO han sido actualizado. Al parecer existe un problema con el servidor. Por favor intente de nuevo o comuníquese con el administrador de InfoPAE"
			);
		}
	}

	echo json_encode($resultadoAJAX);