<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	$id = (isset($_POST["id"]) && $_POST["id"] != '') ? mysqli_real_escape_string($Link, $_POST["id"]) : '';
	$nombre = (isset($_POST["nombre"]) && $_POST["nombre"] != '') ? mysqli_real_escape_string($Link, $_POST["nombre"]) : '';
	$municipio = (isset($_POST["municipio"]) && $_POST["municipio"] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : '';
	$nombreEtc = (isset($_POST["nombreEtc"]) && $_POST["nombreEtc"] != '') ? mysqli_real_escape_string($Link, $_POST["nombreEtc"]) : '';
	$mesContrato = (isset($_POST["mesContrato"]) && $_POST["mesContrato"] != '') ? mysqli_real_escape_string($Link, $_POST["mesContrato"]) : '';
	$departamento = (isset($_POST["departamento"]) && $_POST["departamento"] != '') ? mysqli_real_escape_string($Link, $_POST["departamento"]) : '';
	$cantidadCupos = (isset($_POST["cantidadCupos"]) && $_POST["cantidadCupos"] != '') ? mysqli_real_escape_string($Link, $_POST["cantidadCupos"]) : '';
	$numeroContrato = (isset($_POST["numeroContrato"]) && $_POST["numeroContrato"] != '') ? mysqli_real_escape_string($Link, $_POST["numeroContrato"]) : '';
	$nombredepartamento = (isset($_POST["nombredepartamento"]) && $_POST["nombredepartamento"] != '') ? mysqli_real_escape_string($Link, $_POST["nombredepartamento"]) : '';
	$nombre_representante_legal = (isset($_POST["nombre_representante_legal"]) && $_POST["nombre_representante_legal"] != '') ? mysqli_real_escape_string($Link, $_POST["nombre_representante_legal"]) : '';
	$documento_representante_legal = (isset($_POST["documento_representante_legal"]) && $_POST["documento_representante_legal"] != '') ? mysqli_real_escape_string($Link, $_POST["documento_representante_legal"]) : '';
	$NIT = (isset($_POST["NIT"]) && $_POST["NIT"] != '') ? mysqli_real_escape_string($Link, $_POST["NIT"]) : '';
	$ValorContrato = (isset($_POST["ValorContrato"]) && $_POST["ValorContrato"] != '') ? mysqli_real_escape_string($Link, $_POST["ValorContrato"]) : '';
	$PermitirRepitentes = (isset($_POST["PermitirRepitentes"]) && $_POST["PermitirRepitentes"] != '') ? mysqli_real_escape_string($Link, $_POST["PermitirRepitentes"]) : '';
	$mostrar_boton_enviar_archivos=(isset($_POST["mostrar_boton_enviar_archivos"]) && $_POST["mostrar_boton_enviar_archivos"] != '') ? mysqli_real_escape_string($Link, $_POST["mostrar_boton_enviar_archivos"]) : '';
	$color_primario = (isset($_POST["color_primario"]) && $_POST["color_primario"] != '') ? mysqli_real_escape_string($Link, $_POST["color_primario"]) : '';
	$color_secundario = (isset($_POST["color_secundario"]) && $_POST["color_secundario"] != '') ? mysqli_real_escape_string($Link, $_POST["color_secundario"]) : '';
	$color_texto = (isset($_POST["color_texto"]) && $_POST["color_texto"] != '') ? mysqli_real_escape_string($Link, $_POST["color_texto"]) : '';
	$menu_menu_dia = (isset($_POST["menu_menu_dia"]) && $_POST["menu_menu_dia"] != '') ? mysqli_real_escape_string($Link, $_POST["menu_menu_dia"]) : '';
	$menu_menu_dia = ($menu_menu_dia == 'true') ? 1 : 0;
	$menu_ejecucion_semanal = (isset($_POST["menu_ejecucion_semanal"]) && $_POST["menu_ejecucion_semanal"] != '') ? mysqli_real_escape_string($Link, $_POST["menu_ejecucion_semanal"]) : '';
	$menu_ejecucion_semanal = ($menu_ejecucion_semanal == 'true') ? 1 : 0;
	$menu_operador = (isset($_POST["menu_operador"]) && $_POST["menu_operador"] != '') ? mysqli_real_escape_string($Link, $_POST["menu_operador"]) : '';
	$menu_operador = ($menu_operador == 'true') ? 1 : 0;
	$menu_noticias = (isset($_POST["menu_noticias"]) && $_POST["menu_noticias"] != '') ? mysqli_real_escape_string($Link, $_POST["menu_noticias"]) : '';
	$menu_noticias = ($menu_noticias == 'true') ? 1 : 0;
	$menu_encuesta = (isset($_POST["menu_encuesta"]) && $_POST["menu_encuesta"] != '') ? mysqli_real_escape_string($Link, $_POST["menu_encuesta"]) : '';
	$menu_encuesta = ($menu_encuesta == 'true') ? 1 : 0;
	$menu_fqrs = (isset($_POST["menu_fqrs"]) && $_POST["menu_fqrs"] != '') ? mysqli_real_escape_string($Link, $_POST["menu_fqrs"]) : '';
	$menu_fqrs = ($menu_fqrs == 'true') ? 1 : 0;
	$menu_ver_cronograma = (isset($_POST["menu_ver_cronograma"]) && $_POST["menu_ver_cronograma"] != '') ? mysqli_real_escape_string($Link, $_POST["menu_ver_cronograma"]) : '';
	$menu_ver_cronograma = ($menu_ver_cronograma == 'true') ? 1 : 0;
	$integrantes_union_temporal = (isset($_POST["integrantes_union_temporal"]) && $_POST["integrantes_union_temporal"] != '') ? mysqli_real_escape_string($Link, $_POST["integrantes_union_temporal"]) : '';
	$direccion = (isset($_POST["direccion"]) && $_POST["direccion"] != '') ? mysqli_real_escape_string($Link, $_POST["direccion"]) : '';
	$telefono = (isset($_POST["telefono"]) && $_POST["telefono"] != '') ? mysqli_real_escape_string($Link, $_POST["telefono"]) : '';
	$email = (isset($_POST["email"]) && $_POST["email"] != '') ? mysqli_real_escape_string($Link, $_POST["email"]) : '';
	$pagina_web = (isset($_POST["pagina_web"]) && $_POST["pagina_web"] != '') ? mysqli_real_escape_string($Link, $_POST["pagina_web"]) : '';
	$facebook = (isset($_POST["facebook"]) && $_POST["facebook"] != '') ? mysqli_real_escape_string($Link, $_POST["facebook"]) : '';
	$twitter = (isset($_POST["twitter"]) && $_POST["twitter"] != '') ? mysqli_real_escape_string($Link, $_POST["twitter"]) : '';
	$tipoBusqueda = (isset($_POST['tipoBusqueda']) && $_POST['tipoBusqueda'] != '') ? mysqli_real_escape_string($Link, $_POST['tipoBusqueda']) : "";
	$diasAtencion = (isset($_POST['diasAtencion']) && $_POST['diasAtencion'] != '') ? mysqli_real_escape_string($Link, $_POST['diasAtencion']) : "";
	$sideBar = (isset($_POST['sideBar']) && $_POST['sideBar'] != '') ? mysqli_real_escape_string($Link, $_POST['sideBar']) : "";
	$formatoPlanillas = (isset($_POST['formatoPlanillas']) && $_POST['formatoPlanillas'] != '') ? mysqli_real_escape_string($Link, $_POST['formatoPlanillas']) : "";
	$formatos = (isset($_POST['formatos']) && $_POST['formatos'] != '') ? mysqli_real_escape_string($Link, $_POST['formatos']) : "";

	$consulta1 = "UPDATE parametros
				SET
					Operador = '$nombre',
					integrantes_union_temporal = '$integrantes_union_temporal',
					NumContrato = '$numeroContrato',
					CodDepartamento = '$departamento',
					NombreETC = '$nombreEtc',
					Departamento = '$nombredepartamento',
					CantidadCupos = '$cantidadCupos',
					MesContrato = '$mesContrato',
					CodMunicipio = '$municipio',
					nombre_representante_legal = '$nombre_representante_legal',
					documento_representante_legal = '$documento_representante_legal',
					NIT = '$NIT',
					ValorContrato = '$ValorContrato',
					PermitirRepitentes = '$PermitirRepitentes',
					mostrar_boton_enviar_archivos = '$mostrar_boton_enviar_archivos',
					color_primario = '$color_primario',
					color_secundario = '$color_secundario',
					color_texto = '$color_texto',
					menu_menu_dia = '$menu_menu_dia',
					menu_ejecucion_semanal = '$menu_ejecucion_semanal',
					menu_operador = '$menu_operador',
					menu_noticias = '$menu_noticias',
					menu_encuesta = '$menu_encuesta',
					menu_fqrs = '$menu_fqrs',
					menu_ver_cronograma = '$menu_ver_cronograma',
					direccion = '$direccion',
					telefono = '$telefono',
					email = '$email',
					pagina_web = '$pagina_web',
					facebook = '$facebook',
					twitter = '$twitter',
					tipo_busqueda = '$tipoBusqueda',
					diasAtencion = '$diasAtencion',
					side_bar = '$sideBar',
					formatoPlanillas = '$formatoPlanillas',
					assistance_format = '$formatos'
				WHERE id = '$id'";
	$resultado1 = $Link->query($consulta1) or die ("Unable to execute query.". $Link->error);
	if ($resultado1) {
		if (isset($_FILES["LogoETC"])) {
			$logo_etc = subir_imagen($_FILES["LogoETC"], "logo_etc", $id, $Link);
			if ($logo_etc->estado == 0) {
				echo json_encode($logo_etc);
				exit();
			}
		}

		if (isset($_FILES["LogoOperador"])) {
			$logo_etc = subir_imagen($_FILES["LogoOperador"], "logo_operador", $id, $Link);
			if ($logo_etc->estado == 0) {
				echo json_encode($logo_etc);
				exit();
			}
		}

		if (isset($_FILES["logo_header"])) {
			$logo_etc = subir_imagen($_FILES["logo_header"], "logo_header", $id, $Link);
			if ($logo_etc->estado == 0) {
				echo json_encode($logo_etc);
				exit();
			}
		}

		if (isset($_FILES["logo_footer"])) {
			$logo_etc = subir_imagen($_FILES["logo_footer"], "logo_footer", $id, $Link);
			if ($logo_etc->estado == 0) {
				echo json_encode($logo_etc);
				exit();
			}
		}

		$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('". date("Y-m-d H-i-s") ."', '". $_SESSION['idUsuario'] ."', '24', 'Actualizó los parámetros iniciales del sistema')";
		$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

		$respuesta = array(
			"estado" => 1,
			"mensaje" => "Parámetros actualizados correctamente."
		);

	} else {
		$result = array(
			"estado" => 0,
			"mensaje" => "Los parámetros NO han sido actualizado."
		);
	}

	function subir_imagen($imagen, $nombre_imagen, $id, $Link)
	{
		$dimensiones = getimagesize($imagen["tmp_name"]);
		$ratio = getAspectRatio($dimensiones[0], $dimensiones[1]);

		if ($nombre_imagen == "logo_etc") {
			$ratio_permitido = 4;
			$campo = "LogoETC";
		} else if ($nombre_imagen == "logo_operador") {
			$ratio_permitido = 2;
			$campo = "LogoOperador";
		} else if ($nombre_imagen == "logo_header") {
			$ratio_permitido = 4;
			$campo = "logo_header";
		}else {
			$ratio_permitido = 4;
			$campo = "logo_footer";
		}

		if ($ratio < $ratio_permitido) {
			return (object) [
				"estado" => 0,
				"mensaje" => "Por favor ingresar una imagen tipo banner para la imagen: ". $nombre_imagen
			];
		} else if($imagen["size"] > 1048576) {
			return (object) [
				"estado" => 0,
				"mensaje" => "La imagen supera el tamaño permitido 1 MegaBytes. Por favor ingresar una imagen de igual o menor tamaño para la imagen ". $nombre_imagen
			];
		} else if($imagen["type"] == "image/jpg" || $imagen["type"] == "image/jpeg" || $imagen["type"] == "image/png") {
			$rutaFoto = "../../upload/logotipos/" . $nombre_imagen . ".jpg";
			$subido = move_uploaded_file($imagen["tmp_name"], "../" . $rutaFoto);

			if ($subido) {
				$consulta2 = " UPDATE parametros SET $campo = '$rutaFoto' WHERE id = '$id' ";
				$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
			}

			return (object) [
				"estado" => 1,
				"mensaje" => "Los parámetros han sido actualizado exitosamente."
			];
		} else {
			return (object) [
				"estado" => 0,
				"mensaje" => "La extensión del la imagen no es la permitida. Tipo de archivo permitido: .jpg, .jpeg .png para la imagen ". $nombre_imagen
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

	echo json_encode($respuesta);