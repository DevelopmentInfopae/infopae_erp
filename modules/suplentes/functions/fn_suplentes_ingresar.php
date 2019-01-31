<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$valida = 0;
	$semanas = '';
	$zona_res_est = 0;
	$nom1 = (isset($_POST['nom1'])) ? $_POST['nom1'] : "";
	$nom2 = (isset($_POST['nom2'])) ? $_POST['nom2'] : "";
	$ape1 = (isset($_POST['ape1'])) ? $_POST['ape1'] : "";
	$ape2 = (isset($_POST['ape2'])) ? $_POST['ape2'] : "";
	$etnia = (isset($_POST['etnia'])) ? $_POST['etnia'] : "";
	$sisben = (isset($_POST['sisben'])) ? $_POST['sisben'] : "";
	$genero = (isset($_POST['genero'])) ? $_POST['genero'] : "";
	$sector = (isset($_POST['sector'])) ? $_POST['sector'] : "";
	$dir_res = (isset($_POST['dir_res'])) ? $_POST['dir_res'] : "";
	$num_doc = (isset($_POST['num_doc'])) ? $_POST['num_doc'] : "";
	$cod_mun = (isset($_POST['cod_mun'])) ? $_POST['cod_mun'] : "";
	$tipo_doc = (isset($_POST['tipo_doc'])) ? $_POST['tipo_doc'] : "";
	$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : "";
	$cod_inst = (isset($_POST['cod_inst'])) ? $_POST['cod_inst'] : "";
	$cod_sede = (isset($_POST['cod_sede'])) ? $_POST['cod_sede'] : "";
	$nom_inst = (isset($_POST['nom_inst'])) ? $_POST['nom_inst'] : "";
	$nom_sede = (isset($_POST['nom_sede'])) ? $_POST['nom_sede'] : "";
	$fecha_nac = (isset($_POST['fecha_nac'])) ? $_POST['fecha_nac'] : "";
	$cod_grado = (isset($_POST['cod_grado'])) ? $_POST['cod_grado'] : "";
	$nom_grupo = (isset($_POST['nom_grupo'])) ? $_POST['nom_grupo'] : "";
	$repitente = (isset($_POST['repitente'])) ? $_POST['repitente'] : "";
	$cod_discap = (isset($_POST['cod_discap'])) ? $_POST['cod_discap'] : "";
	$cod_mun_nac = (isset($_POST['cod_mun_nac'])) ? $_POST['cod_mun_nac'] : "";
	$cod_mun_res = (isset($_POST['cod_mun_res'])) ? $_POST['cod_mun_res'] : "";
	$cod_estrato = (isset($_POST['cod_estrato'])) ? $_POST['cod_estrato'] : "";
	$abreviatura = (isset($_POST['abreviatura'])) ? $_POST['abreviatura'] : "";
	$cod_jorn_est = (isset($_POST['cod_jorn_est'])) ? $_POST['cod_jorn_est'] : "";
	$cod_pob_victima = (isset($_POST['cod_pob_victima'])) ? $_POST['cod_pob_victima'] : "";

	// Consulta que retorna la cantidad de tablas con el nombre "Focalización".
    $tablas_focalizacion = [];
    $resultado_focalizacion = $Link->query("SELECT table_name AS nombre_tabla FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name like 'focalizacion%';");
	if ($resultado_focalizacion->num_rows > 0) {
		while ($registro_focalizacion = $resultado_focalizacion->fetch_assoc()) {
			$resultado_estudiante = $Link->query("SELECT * FROM ".$registro_focalizacion['nombre_tabla']." WHERE num_doc = '".$num_doc."';");
			if ($resultado_estudiante->num_rows > 0) {
				$respuestaAJAX = [
					"success" => 0,
					"message" => "No es posible crear el suplente debido a que se encuentra focalizado en la semana ". substr($registro_focalizacion['nombre_tabla'], 12, 2)
				];
				echo json_encode($respuestaAJAX);
				exit;
			}
		}
	}

	// Algoritmo para calcular la esdad del estudiante suplente.
	$date1 = new DateTime($fecha_nac);
	$date2 = new DateTime(date('d-m-Y'));
	$diff = $date1->diff($date2);
	$edad = $diff->y;

	// Consulta utilizada para insertar un nuevo estudiante como suplente.
	$nuevo_suplente = "INSERT INTO suplentes (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, cod_pob_victima, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, repitente, edad, zona_res_est, activo)
	VALUES ('$tipo_doc', '$num_doc', '$abreviatura', '$ape1', '$ape2', '$nom1', '$nom2', '$genero', '$dir_res', '$cod_mun_res', '$telefono', '$cod_mun_nac', '$fecha_nac', '$cod_estrato', '$sisben', '$cod_discap', '$etnia', '$cod_pob_victima', '$cod_sede', '$cod_inst', '$cod_mun', '$cod_mun', '$nom_sede', '$nom_inst', '$cod_grado', '$nom_grupo', '$cod_jorn_est', '$repitente', '$edad', '$sector', '1')";
	// Condición que verifica si la consulta se ejecutó exitosamente.
	if ($Link->query($nuevo_suplente) === TRUE) {
		$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '48', 'Ingresó el suplente con número de identificación <strong>".$num_doc."	</strong>')";
		$Link->query($sqlBitacora);

		$respuestaAJAX = [
			"success" => 1,
			"message" => "El estudiante con número de identificación <strong>". $num_doc ."</strong> fue agregado como suplente exitosamente."
		];
	} else {
		$respuestaAJAX = [
			"success" => 0,
			"message" => "El estudiante con número de identificación <strong>". $num_doc ."</strong> No pudo ser agregado como suplente exitosamente.".$nuevo_suplente
		];
	}

	echo json_encode($respuestaAJAX);
