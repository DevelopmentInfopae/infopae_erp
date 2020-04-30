<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$email = $_POST["email"];
	$nombre = $_POST["nombre"];
	$perfil = $_POST["perfil"];
	$telefono = $_POST["telefono"];
	$direccion = $_POST["direccion"];
	$municipio = $_POST["municipio"];
	$tipoUsuario = $_POST["tipoUsuario"];
	$numeroDocumento = $_POST["numeroDocumento"];
    $clave = sha1(strtoupper(substr($nombre, 0, 1)) . $numeroDocumento);

  //Realizar la validación del correo electrónico.
  $consulta0 = "SELECT email FROM usuarios WHERE email LIKE '%$email%'";
	$resultado0 = $Link->query($consulta0);
	if($resultado0->num_rows > 0){
		$resultadoAJAX = array(
			"estado" => 0,
			"mensaje" => "El Correo electrónico ingresado ya se encuentra registrado. Por favor intente nuevamente con un correo diferente."
		);
	} else {
		if (isset($_FILES["foto"]["name"])){
			$dimensiones = getimagesize($_FILES["foto"]["tmp_name"]);

			// Valida el ratio/aspecto permitido
			if ($dimensiones[0] != $dimensiones[1])
			{
				$resultadoAJAX = array(
					"estado" => 0,
					"mensaje" => "Por favor ingresar una imagen de ratio aspecto 1:1 o cuadrada tipo documento."
				);
			} else if($_FILES["foto"]["size"] > 5120000){ // Valida el tamaño permitido
				$resultadoAJAX = array(
					"estado" => 0,
					"mensaje" => "La imagen supera el tamaño permitido 5 MegaBytes. Por favor ingresar una imagen de igual o menor tamaño"
				);
			} else if($_FILES["foto"]["type"] == "image/jpg" || $_FILES["foto"]["type"] == "image/jpeg" || $_FILES["foto"]["type"] == "image/png"){ // Valida tipo de imagen permitido
				// Se ejecuta la consulta para guardar el usuario.
				$consulta1 = " INSERT INTO usuarios ( nombre, clave, direccion, cod_mun, telefono, email, id_perfil, nueva_clave, num_doc, Tipo_Usuario) VALUES ('$nombre', '$clave', '$direccion', '$municipio', '$telefono', '$email', '$perfil', '0', '$numeroDocumento', '$tipoUsuario')";
				$resultado1 = $Link->query($consulta1);
				if ($resultado1){
					$id = $Link->insert_id;
					$rutaFoto = "../../upload/usuarios/U" . $id . ".jpg";
					$subido = move_uploaded_file($_FILES["foto"]["tmp_name"], "../" . $rutaFoto);

					if ($subido){
						$consulta2 = " UPDATE usuarios SET foto = '$rutaFoto' WHERE id = '$id' ";
						$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
					}

					$resultadoAJAX = array(
						"estado" => 1,
						"mensaje" => "El usuario y la foto han sido guardadas exitosamente."
					);
				} else {
					$resultadoAJAX = array(
						"estado" => 0,
						"mensaje" => "El usuario NO han sido guardado. Al parecer existe un problema con el servidor. Por favor intente de nuevo o comuníquese con el administrador de InfoPAE."
					);
				}

				// Registro de la bitácora
				$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '27', 'Creó al usuario <strong>$nombre</strong>')";
				$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
			} else {
				$resultadoAJAX = array(
					"estado" => 0,
					"mensaje" => "La extensión del la imagen no es la permitida. Tipo de archivo permitido: .jpg, .jpeg"
				);
			}
		} else { // Se guarda sin foto
			// Se ejecuta la consulta para guardar el usuario.
				$consulta1 = " INSERT INTO usuarios ( nombre, clave, direccion, cod_mun, telefono, email, id_perfil, nueva_clave, num_doc, Tipo_Usuario) VALUES ('$nombre', '$clave', '$direccion', '$municipio', '$telefono', '$email', '$perfil', '0', '$numeroDocumento', '$tipoUsuario')";
				$resultado1 = $Link->query($consulta1);
				if ($resultado1){
					$resultadoAJAX = array(
						"estado" => 1,
						"mensaje" => "El usuario han sido guardado exitosamente."
					);

					// Registro de la bitácora
					$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '27', 'Creó al usuario <strong>$nombre</strong>')";
					$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
				}
		}
	}
	echo json_encode($resultadoAJAX);