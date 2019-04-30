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
	$semana = $Link->real_escape_string($_POST["semana"]);
	$etnia = (isset($_POST['etnia'])) ? $_POST['etnia'] : "";
	$cod_sede = (isset($_POST['sede'])) ? $_POST['sede'] : "";
	$sisben = (isset($_POST['sisben'])) ? $_POST['sisben'] : "";
	$genero = (isset($_POST['genero'])) ? $_POST['genero'] : "";
	$sector = (isset($_POST['sector'])) ? $_POST['sector'] : "";
	$dir_res = (isset($_POST['dir_res'])) ? $_POST['dir_res'] : "";
	$num_doc = (isset($_POST['num_doc'])) ? $_POST['num_doc'] : "";
	$cod_mun = (isset($_POST['cod_mun'])) ? $_POST['cod_mun'] : "";
	$tipo_doc = (isset($_POST['tipo_doc'])) ? $_POST['tipo_doc'] : "";
	$telefono = (isset($_POST['telefono'])) ? $_POST['telefono'] : "";
	$cod_inst = (isset($_POST['cod_inst'])) ? $_POST['cod_inst'] : "";
	$nom_sede = (isset($_POST['nombre_sede'])) ? $_POST['nombre_sede'] : "";
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
	$nom_inst = (isset($_POST['nombre_institucion'])) ? $_POST['nombre_institucion'] : "";
	$cod_pob_victima = (isset($_POST['cod_pob_victima'])) ? $_POST['cod_pob_victima'] : "";

	// Consulta que valida si el suplente ya se encuentra focalizado.
	$consulta_suplente_focalizado = "SELECT * FROM focalizacion$semana WHERE num_doc = '$cod_mun'";
	$respuesta_suplente_focalizado = $Link->query($consulta_suplente_focalizado)
	if ($respuesta_suplente_focalizado->num_rows > 0)
	{
		$respuestaAJAX = [
			"success" => 0,
			"message" => "El número de documento ingresado ya se encuentra focalizado."
		];
		echo json_encode($respuestaAJAX);
		exit;
	}

	// Consulta que retorna si el estudiante ya existe como suplente.
	$resultado_existe_suplente = $Link->query("SELECT num_doc  FROM suplentes$semana WHERE num_doc = '$num_doc';");
	if ($resultado_existe_suplente->num_rows > 0)
	{
		$respuestaAJAX = [
			"success" => 0,
			"message" => "El número de documento ingresado ya se encuentra registrado como suplente."
		];
		echo json_encode($respuestaAJAX);
		exit;
	}

	// Algoritmo para calcular la esdad del estudiante suplente.
	$date1 = new DateTime($fecha_nac);
	$date2 = new DateTime(date('Y-m-d'));
	$diff = $date1->diff($date2);
	$edad = $diff->y;

	// Consulta utilizada para insertar un nuevo estudiante como suplente.
	$nuevo_suplente = "INSERT INTO suplentes$semana (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, cod_pob_victima, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, repitente, edad, zona_res_est, activo)
	VALUES ('$tipo_doc', '$num_doc', '$abreviatura', '$ape1', '$ape2', '$nom1', '$nom2', '$genero', '$dir_res', '$cod_mun_res', '$telefono', '$cod_mun_nac', '$fecha_nac', '$cod_estrato', '$sisben', '$cod_discap', '$etnia', '$cod_pob_victima', '$cod_sede', '$cod_inst', '$cod_mun', '$cod_mun', '$nom_sede', '$nom_inst', '$cod_grado', '$nom_grupo', '$cod_jorn_est', '$repitente', '$edad', '$sector', '1')";
	// Condición que verifica si la consulta se ejecutó exitosamente.
	if ($Link->query($nuevo_suplente) === TRUE)
	{
		$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '48', 'Ingresó el suplente con número de identificación <strong>".$num_doc."	</strong>')";
		$Link->query($sqlBitacora);

		$respuestaAJAX = [
			"success" => 1,
			"message" => "El suplente ha sido agregado exitosamente."
		];
	} else {
		$respuestaAJAX = [
			"success" => 0,
			"message" => "No fue posible agregar el suplente.". $nuevo_suplente
		];
	}

	echo json_encode($respuestaAJAX);
