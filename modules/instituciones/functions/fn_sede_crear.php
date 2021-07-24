<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	// Declaración de variables pasadas mediante AJAX
	$periodoActual = $_SESSION["periodoActual"];
	$zona = (isset($_POST["zonaPae"])  && $_POST["email"] != '') ? mysqli_real_escape_string($Link, $_POST["zonaPae"])  : '';
	$email = (isset($_POST["email"]) && $_POST["email"] != '') ? mysqli_real_escape_string($Link, $_POST["email"])  : '';
	$codigo = (isset($_POST["codigo"]) && $_POST["codigo"] != '') ? mysqli_real_escape_string($Link, $_POST["codigo"]) : '';
	$nombre = (isset($_POST["nombre"]) && $_POST["nombre"] != '') ? mysqli_real_escape_string($Link, $_POST["nombre"]) : '';
	$sector = (isset($_POST["sector"]) && $_POST["sector"] != '') ? mysqli_real_escape_string($Link, $_POST["sector"]) : '';
	$jornada = (isset($_POST["jornada"]) && $_POST["jornada"] != '') ? mysqli_real_escape_string($Link, $_POST["jornada"]) : '';
	$telefono = (isset($_POST["telefono"]) && $_POST["telefono"] != '') ? mysqli_real_escape_string($Link, $_POST["telefono"]) : '';
	$direccion = (isset($_POST["direccion"]) && $_POST["direccion"] != '') ? mysqli_real_escape_string($Link, $_POST["direccion"]) : '';
	$municipio = (isset($_POST["municipio"]) && $_POST["municipio"] != '') ? mysqli_real_escape_string($Link, $_POST["municipio"]) : '';
	$variacion = (isset($_POST["variacion"]) && $_POST["variacion"] != '') ? mysqli_real_escape_string($Link, $_POST["variacion"]) : '';
	$validacion = (isset($_POST["validacion"]) && $_POST["validacion"] != '') ? mysqli_real_escape_string($Link, $_POST["validacion"]) : '';
	$complemento = (isset($_POST["complemento"]) && $_POST["complemento"] != '') ? mysqli_real_escape_string($Link, $_POST["complemento"]) : '';
	$institucion = (isset($_POST["institucion"]) && $_POST["institucion"] != '') ? mysqli_real_escape_string($Link, $_POST["institucion"]) : '';
	$coordinador = (isset($_POST["coordinador"]) && $_POST["coordinador"] != '') ? mysqli_real_escape_string($Link, $_POST["coordinador"]) : '';
	$manipuladoras = (isset($_POST['manipuladora']) && $_POST['manipuladora'] != '') ? mysqli_real_escape_string($Link, $_POST['manipuladora']) : '0';
	$nombreInstitucion = (isset($_POST["nombreInstitucion"]) && $_POST["nombreInstitucion"] != '') ? mysqli_real_escape_string($Link, $_POST["nombreInstitucion"]) : '';

	$columnaZonaPae = '';
	$valorZonaPae = '';
	if ($zona != 'undefined') {
		$columnaZonaPae = ' Zona_Pae, ';
		$valorZonaPae = "'".$zona."'" .",";
	}

	// Consultar si la sede ya existe en la BD
  	$consulta1 = "SELECT cod_sede AS codigoSede FROM sedes$periodoActual WHERE cod_sede = '$codigo';";
	$resultado1 = $Link->query($consulta1);
	if($resultado1->num_rows > 0){
    	$respuestaAJAX = [
    		"estado" => 0,
    		"mensaje" => "El código de sede N°: <strong>".$codigo."</strong> ya se encuentra registrado en el sistema. Por favor intente con un código diferente."
    	];
    	exit(json_encode($respuestaAJAX));
	} 
	if ($email != "") {
	 	// Validar que el correo electrónico
		$consultaEmail = "SELECT email AS emailSede FROM sedes$periodoActual WHERE email = '$email';";
		$resultadoEmail = $Link->query($consultaEmail);
		if($resultadoEmail->num_rows > 0){
			$respuestaAJAX = [
		    	"estado" => 0,
		    	"mensaje" => "El email: <strong>".$email."</strong> ya se encuentra registrado en el sistema. Por favor intente con un email diferente."
		    ];
		    exit(json_encode($respuestaAJAX));
	 	}	
	} 
	
	// // Insertar la sede
	$consultaCrear = "INSERT INTO sedes$periodoActual (cod_inst, cod_sede, nom_sede, cod_mun_sede, nom_inst, tipo_validacion, $columnaZonaPae Tipo_Complemento, direccion, telefonos, email, id_coordinador, sector, cod_variacion_menu, estado, jornada, cantidad_Manipuladora) VALUE ( '$institucion', '$codigo', '$nombre', '$municipio', '$nombreInstitucion', '$validacion', $valorZonaPae '$complemento', '$direccion', '$telefono', '$email', '$coordinador', '$sector', '$variacion', '0', '$jornada', '$manipuladoras')";
	// exit(var_dump($consultaCrear));
	$resultadoCrear = $Link->query($consultaCrear);
	if($resultadoCrear){
		$id = $Link->insert_id;
		if(isset($_FILES["imagen"]["name"])){
			$dimensiones = getimagesize($_FILES["imagen"]["tmp_name"]);
			$ratioAspecto = getAspectRatio($dimensiones[0], $dimensiones[1]);

			// Valida es ratio aspecto de la imagen a subir
			if($ratioAspecto >= 1.33 && $ratioAspecto <= 1.34){
				// Valida el tamaño de la imagen
				if($_FILES["imagen"]["size"] < 5242880){
							// Valida si el tipo de archivo es permitido.
					if($_FILES["imagen"]["type"] == "image/jpg" || $_FILES["imagen"]["type"] == "image/jpeg" || $_FILES["imagen"]["type"] == "image/png"){
						$tipoArchivo = str_replace("image/", "", $_FILES["imagen"]["type"]);
						$rutaImagen = "../../upload/logotipos/logoSede" . $id . "." . $tipoArchivo;
						$subido = move_uploaded_file($_FILES["imagen"]["tmp_name"], "../" . $rutaImagen);
						if ($subido){
							$consulta2 = " UPDATE sedes$periodoActual SET url_foto = '$rutaImagen' WHERE id = '$id' ";
							$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
						}

						$respuestaAJAX = [
						"estado" => 1,
						"mensaje" => "La sede ha sido guardada con éxito!"
						];

						$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '37', 'Creó la sede: ". $nombre ." y se cargo la imágen')";
						$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));
						exit(json_encode($respuestaAJAX));
					} else if($_FILES["imagen"]["type"] != "image/jpg" || $_FILES["imagen"]["type"] != "image/jpeg" || $_FILES["imagen"]["type"] != "image/png"){
						$respuestaAJAX = array(
							"estado" => 0,
							"mensaje" => "La extensión del la imagen no es la permitida. Tipo de archivo permitido: .jpg, .jpeg .png"
						);
						exit(json_encode($respuestaAJAX));
					}
				} else if($_FILES["imagen"]["size"] > 5242880){
					$result = [
						"estado" => 0,
						"mensaje" => "La imagen supera el tamaño permitido 5 MegaBytes. Por favor ingresar una imagen de igual o menor tamaño"
					];
					exit(json_encode($respuestaAJAX));
				}

			} else if($ratioAspecto <= 1.33 || $ratioAspecto >= 1.34) {
				$respuestaAJAX = [
					"estado" => 0,
					"mensaje" => "Por favor ingresar una imagen de ratio / aspecto 4:3"
				];
				exit(json_encode($respuestaAJAX));
			}

		} else {
			// Registro de la Bitácora
			$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '37', 'Creó la sede: <strong>". $nombre ."</strong>')";
			$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

			$respuestaAJAX = [
			    "estado" => 1,
			    "mensaje" => "La sede ha sido guardada con éxito!"
			];
			exit(json_encode($respuestaAJAX));
		}
	} else {
		$respuestaAJAX = [
		   	"estado" => 0,
		   	"mensaje" => "La sede NO ha sido guardada con éxito!"
		];
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
    return round($ratio, 2);
	}

	mysqli_close($Link);
