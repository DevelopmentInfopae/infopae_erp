<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$id = mysqli_real_escape_string($Link, $_POST["id"]);
	$email = mysqli_real_escape_string($Link, $_POST["email"]);
	$nombre = mysqli_real_escape_string($Link, $_POST["nombre"]);
	$telefono = mysqli_real_escape_string($Link, $_POST["telefono"]);
	$direccion = mysqli_real_escape_string($Link, $_POST["direccion"]);
	$municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);
	$fotoCargada = mysqli_real_escape_string($Link, $_POST["fotoCargada"]);
	$numeroDocumento = mysqli_real_escape_string($Link, $_POST["numeroDocumento"]);

	// $perfil = mysqli_real_escape_string($Link, $_POST["perfil"]);
	// $tipoUsuario = mysqli_real_escape_string($Link, $_POST["tipoUsuario"]);
	// $estado = mysqli_real_escape_string($Link, $_POST["estado"]);

	//Validamos que el correo electronico a editar no se encuentre registrado.
	$consulta0 = "SELECT email FROM usuarios WHERE email LIKE '%$email%' AND id <> '$id';";
	$resultado0 = $Link->query($consulta0);
	if($resultado0->num_rows > 0){
		$resultadoAJAX = array(
			"estado" => 0,
			"mensaje" => "El Correo electrónico ingresado ya se encuentra registrado. Por favor intente nuevamente con un correo diferente."
		);
	} else {
		$consulta1 = " UPDATE usuarios SET num_doc = '$numeroDocumento', nombre = '$nombre', direccion = '$direccion', cod_mun = '$municipio', telefono = '$telefono', email = '$email' WHERE id = '$id' ";

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

					$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Actualizó los datos de perfil y la foto del usuario <strong>$nombre</strong>')";
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

				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '24', 'Actualizó los datos de perfil del usuario <strong>$nombre</strong>')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			}
		} else {
			$resultadoAJAX = array(
				"estado" => 0,
				"mensaje" => "El usuario NO han sido actualizado. Al parecer existe un problema con el servidor. Por favor intente de nuevo o comuníquese con el administrador de InfoPAE"
			);
		}
	}

	if( isset($_POST["nuevaContrasenna1"]) && $_POST["nuevaContrasenna1"] != '' ){
		$nuevaContrasenna1 = mysqli_real_escape_string($Link, $_POST["nuevaContrasenna1"]);
		$nuevaContrasenna2 = mysqli_real_escape_string($Link, $_POST["nuevaContrasenna2"]);
		$contrasennaActual = mysqli_real_escape_string($Link, $_POST["contrasennaActual"]);
		$consultaContrasenna = " SELECT email FROM usuarios WHERE clave = '$contrasennaActual' AND id = '$id' ";
		$resultadoContrasenna = $Link->query($consultaContrasenna);
		if($resultadoContrasenna->num_rows > 0){
			$consultaContrasenna = " UPDATE usuarios SET clave = '$nuevaContrasenna1' WHERE id = '$id' ";
			$consultaContrasenna = $Link->query($consultaContrasenna) or die ('Unable to execute query. '. mysqli_error($Link));
			$resultadoAJAX = array(
				"estado" => 1,
				"mensaje" => "El usuario han sido actualizado exitosamente."
			);
		}
		else{
			$resultadoAJAX = array(
				"estado" => 2,
				"mensaje" => "Contraseña actual icorrecta."
			);
		}
	}

	echo json_encode($resultadoAJAX);
