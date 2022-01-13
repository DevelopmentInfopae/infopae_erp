<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';
	// exit(var_dump($_POST));
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
	$nomAcudiente = (isset($_POST['nomAcudiente'])) ? $_POST['nomAcudiente'] : "";
	$docAcudiente = (isset($_POST['docAcudiente'])) ? $_POST['docAcudiente'] : "";
	$telAcudiente = (isset($_POST['telAcudiente'])) ? $_POST['telAcudiente'] : "";
	$parentescoAcudiente = (isset($_POST['parentescoAcudiente'])) ? $_POST['parentescoAcudiente'] : "";


	$consultaDatabase = "SELECT 
							table_name as tabla 
						FROM 
							information_schema.tables 
						WHERE 
							table_schema = '$Database' AND table_name = 'suplentes" .$semana."';";

	$respuestaDatabase = $Link->query($consultaDatabase) or die('Error al consultar la tabla suplentes.$semana ' .mysqli_error($Link));
	if ($respuestaDatabase->num_rows > 0) {
		$existeTabla = 'si';
	}else if ($respuestaDatabase->num_rows == 0) {
		$existeTabla = 'no';
	}

	if ($existeTabla == 'si') {
		// Consulta que valida si el suplente ya se encuentra focalizado.
		$consulta_suplente_focalizado = "SELECT * FROM focalizacion$semana WHERE num_doc = '$num_doc'";
		$respuesta_suplente_focalizado = $Link->query($consulta_suplente_focalizado);
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
		$nuevo_suplente = "INSERT INTO suplentes$semana (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, cod_pob_victima, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, repitente, edad, zona_res_est, activo, nom_acudiente, doc_acudiente, tel_acudiente, parentesco_acudiente)
			VALUES ('$tipo_doc', '$num_doc', '$abreviatura', '$ape1', '$ape2', '$nom1', '$nom2', '$genero', '$dir_res', '$cod_mun_res', '$telefono', '$cod_mun_nac', '$fecha_nac', '$cod_estrato', '$sisben', '$cod_discap', '$etnia', '$cod_pob_victima', '$cod_sede', '$cod_inst', '$cod_mun', '$cod_mun', '$nom_sede', '$nom_inst', '$cod_grado', '$nom_grupo', '$cod_jorn_est', '$repitente', '$edad', '$sector', '1','$nomAcudiente', '$docAcudiente', '$telAcudiente', '$parentescoAcudiente')";
		// Condición que verifica si la consulta se ejecutó exitosamente.
		if ($Link->query($nuevo_suplente) === TRUE){
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
		exit();

	}if ($existeTabla == 'no') {
		// Consulta que valida si el suplente ya se encuentra focalizado.
		$consulta_suplente_focalizado = "SELECT * FROM focalizacion$semana WHERE num_doc = '$cod_mun'";
		$respuesta_suplente_focalizado = $Link->query($consulta_suplente_focalizado);
		if ($respuesta_suplente_focalizado->num_rows > 0)
			{
			$respuestaAJAX = [
				"success" => 0,
				"message" => "El número de documento ingresado ya se encuentra focalizado."
			];
			echo json_encode($respuestaAJAX);
			exit;
		}

		$consulta_crear_tabla_suplente = "CREATE TABLE `suplentes$semana` (
			`id` INT(11) NOT NULL AUTO_INCREMENT,
			`tipo_doc` INT(11) NULL DEFAULT '0',
			`num_doc` VARCHAR(24) NULL DEFAULT '-',
			`tipo_doc_nom` VARCHAR(10) NULL DEFAULT NULL,
			`ape1` VARCHAR(50) NULL DEFAULT '-',
			`ape2` VARCHAR(50) NULL DEFAULT '-',
			`nom1` VARCHAR(50) NULL DEFAULT '-',
			`nom2` VARCHAR(50) NULL DEFAULT '-',
			`genero` VARCHAR(1) NULL DEFAULT '-',
			`dir_res` VARCHAR(200) NULL DEFAULT NULL,
			`cod_mun_res` INT(11) NULL DEFAULT NULL,
			`telefono` VARCHAR(50) NULL DEFAULT NULL,
			`cod_mun_nac` INT(11) NULL DEFAULT '0',
			`fecha_nac` DATE NULL DEFAULT NULL,
			`cod_estrato` INT(11) NULL DEFAULT '0',
			`sisben` DECIMAL(5,3) NULL DEFAULT '0.000',
			`cod_discap` INT(11) NULL DEFAULT '0',
			`etnia` INT(11) NULL DEFAULT '0',
			`resguardo` INT(11) NULL DEFAULT '0',
			`cod_pob_victima` INT(11) NULL DEFAULT '0',
			`des_dept_nom` INT(11) NULL DEFAULT NULL,
			`nom_mun_desp` INT(11) NULL DEFAULT NULL,
			`cod_sede` BIGINT(20) NULL DEFAULT '0',
			`cod_inst` BIGINT(20) NULL DEFAULT '0',
			`cod_mun_inst` INT(11) NULL DEFAULT NULL,
			`cod_mun_sede` INT(11) NULL DEFAULT NULL,
			`nom_sede` VARCHAR(200) NULL DEFAULT NULL,
			`nom_inst` VARCHAR(200) NULL DEFAULT NULL,
			`cod_grado` INT(11) NULL DEFAULT '0',
			`nom_grupo` INT(11) NULL DEFAULT NULL,
			`cod_jorn_est` INT(11) UNSIGNED NULL DEFAULT '0',
			`estado_est` VARCHAR(20) NULL DEFAULT NULL,
			`repitente` VARCHAR(2) NULL DEFAULT NULL,
			`edad` VARCHAR(4) NULL DEFAULT '0',
			`zona_res_est` INT(11) NULL DEFAULT '0',
			`id_disp_est` INT(11) UNSIGNED NULL DEFAULT '0',
			`TipoValidacion` VARCHAR(50) NULL DEFAULT '',
			`activo` TINYINT(1) UNSIGNED NULL DEFAULT '0',
			`nom_acudiente` VARCHAR(200) NULL DEFAULT '',
			`doc_acudiente` VARCHAR(20) NULL DEFAULT '',
			`tel_acudiente` VARCHAR(50) NULL DEFAULT '',
			`parentesco_acudiente` VARCHAR(50) NULL DEFAULT '',
		PRIMARY KEY (`id`),
			INDEX `Acel_est1` (`num_doc`, `cod_jorn_est`, `cod_grado`, `cod_pob_victima`, `cod_inst`, `cod_discap`) USING BTREE
		)
		COLLATE='utf8_general_ci'
		ENGINE=InnoDB;";
		$respuesta_crear_tabla_suplente = $Link->query($consulta_crear_tabla_suplente) or die("Error al crear tabla de suplente$semana: ". $Link->error);
		if (! $respuesta_crear_tabla_suplente){
			$respuesta_ajax = [
			'success' => 0,
			'message' => 'No fue posible crear la tabla para la importación de datos.'
			];
			echo json_encode($respuesta_ajax);
			exit();
		}

		// Algoritmo para calcular la esdad del estudiante suplente.
		$date1 = new DateTime($fecha_nac);
		$date2 = new DateTime(date('Y-m-d'));
		$diff = $date1->diff($date2);
		$edad = $diff->y;

		// Consulta utilizada para insertar un nuevo estudiante como suplente.
		$nuevo_suplente = "INSERT INTO suplentes$semana (tipo_doc, num_doc, tipo_doc_nom, ape1, ape2, nom1, nom2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, cod_pob_victima, cod_sede, cod_inst, cod_mun_inst, cod_mun_sede, nom_sede, nom_inst, cod_grado, nom_grupo, cod_jorn_est, repitente, edad, zona_res_est, activo, nom_acudiente, doc_acudiente, tel_acudiente, parentesco_acudiente)
			VALUES ('$tipo_doc', '$num_doc', '$abreviatura', '$ape1', '$ape2', '$nom1', '$nom2', '$genero', '$dir_res', '$cod_mun_res', '$telefono', '$cod_mun_nac', '$fecha_nac', '$cod_estrato', '$sisben', '$cod_discap', '$etnia', '$cod_pob_victima', '$cod_sede', '$cod_inst', '$cod_mun', '$cod_mun', '$nom_sede', '$nom_inst', '$cod_grado', '$nom_grupo', '$cod_jorn_est', '$repitente', '$edad', '$sector', '1','$nomAcudiente', '$docAcudiente', '$telAcudiente', '$parentescoAcudiente')";
		// Condición que verifica si la consulta se ejecutó exitosamente.
		if ($Link->query($nuevo_suplente) === TRUE){
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
		exit();
	}						


