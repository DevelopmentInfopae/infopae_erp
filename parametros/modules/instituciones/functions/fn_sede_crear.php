<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';

	// Declaración de variables pasadas mediante AJAX
	$periodoActual = $_SESSION["periodoActual"];
	$codigo = mysqli_real_escape_string($Link, $_POST["codigo"]);
	$institucion = mysqli_real_escape_string($Link, $_POST["institucion"]);
	$nombreInstitucion = mysqli_real_escape_string($Link, $_POST["nombreInstitucion"]);
	$nombre = mysqli_real_escape_string($Link, $_POST["nombre"]);
	$validacion = mysqli_real_escape_string($Link, $_POST["validacion"]);
	$complemento = mysqli_real_escape_string($Link, $_POST["complemento"]);
	$coordinador = mysqli_real_escape_string($Link, $_POST["coordinador"]);
	$sector = mysqli_real_escape_string($Link, $_POST["sector"]);
	$direccion = mysqli_real_escape_string($Link, $_POST["direccion"]);
	$telefono = mysqli_real_escape_string($Link, $_POST["telefono"]);
	$email = mysqli_real_escape_string($Link, $_POST["email"]);
	$municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);
	$variacion = mysqli_real_escape_string($Link, $_POST["variacion"]);
	$jornada = mysqli_real_escape_string($Link, $_POST["jornada"]);

	// Consultar si la sede ya existe en la BD
  $consulta1 = "SELECT cod_sede AS codigoSede FROM sedes$periodoActual WHERE cod_sede = '$codigo';";
	$resultado1 = $Link->query($consulta1);
	if($resultado1->num_rows > 0){
    $respuestaAJAX = [
    	"estado" => 0,
    	"mensaje" => "El codigo de sede N°: <strong>".$codigo."</strong> ya se encuentra registrado en el sistema. Por favor intente con un código diferente."
    ];
	 } else {
	 	// Validar que el correo electrónico
	 	$consultaEmail = "SELECT email AS emailSede FROM sedes$periodoActual WHERE email = '$email';";
		$resultadoEmail = $Link->query($consultaEmail);
		if($resultadoEmail->num_rows > 0){
			$respuestaAJAX = [
	    	"estado" => 0,
	    	"mensaje" => "El email: <strong>".$email."</strong> ya se encuentra registrado en el sistema. Por favor intente con un código diferente."
	    ];
		} else {
			// // Insertar la sede
			$consultaCrear = "INSERT INTO sedes$periodoActual (cod_inst, cod_sede, nom_sede, cod_mun_sede, nom_inst, tipo_validacion, Tipo_Complemento, direccion, telefonos, email, id_coordinador, sector, cod_variacion_menu, estado, jornada) VALUE ( '$institucion', '$codigo', '$nombre', '$municipio', '$nombreInstitucion', '$validacion', '$complemento', '$direccion', '$telefono', '$email', '$coordinador', '$sector', '$variacion', '0', '$jornada')"; 
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
							} else {
								$result = array(
									"estado" => 0, 
									"mensaje" => "La extensión del la imagen no es la permitida. Tipo de archivo permitido: .jpg, .jpeg .png"
								);
							}
						} else {
							$result = [
								"estado" => 0, 
								"mensaje" => "La imagen supera el tamaño permitido 5 MegaBytes. Por favor ingresar una imagen de igual o menor tamaño"
							];
						}
						
					} else {
						$respuestaAJAX = [
							"estado" => 0, 
							"mensaje" => "Por favor ingresar una imagen de ratio / aspecto 4:3"
						];
					}

				} else {
					// Registro de la Bitácora
					$consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '37', 'Creó la sede: <strong>". $nombre ."</strong>')";
					$Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

					$respuestaAJAX = [
			    	"estado" => 1,
			    	"mensaje" => "La sede ha sido guardada con éxito!"
			    ];
				}
			} else {
				$respuestaAJAX = [
		    	"estado" => 0,
		    	"mensaje" => "La sede NO ha sido guardada con éxito!"
		    ];
			}
		}
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
	echo json_encode($respuestaAJAX);