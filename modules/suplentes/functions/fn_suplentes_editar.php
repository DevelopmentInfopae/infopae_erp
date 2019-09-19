<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$id = (isset($_POST['id'])) ? $_POST['id'] : "";
	$nom1 = (isset($_POST['nom1'])) ? $_POST['nom1'] : "";
	$nom2 = (isset($_POST['nom2'])) ? $_POST['nom2'] : "";
	$ape1 = (isset($_POST['ape1'])) ? $_POST['ape1'] : "";
	$ape2 = (isset($_POST['ape2'])) ? $_POST['ape2'] : "";
	$etnia = (isset($_POST['etnia'])) ? $_POST['etnia'] : "";
	$sisben = (isset($_POST['sisben'])) ? $_POST['sisben'] : "";
	$genero = (isset($_POST['genero'])) ? $_POST['genero'] : "";
	$sector = (isset($_POST['sector'])) ? $_POST['sector'] : "";
	$estado = (isset($_POST['estado'])) ? $_POST['estado'] : "";
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
	$semana = isset($_POST['semana']) ? $Link->real_escape_string($_POST['semana']) : '';
	$cod_pob_victima = (isset($_POST['cod_pob_victima'])) ? $_POST['cod_pob_victima'] : "";

	// Algoritmo para calcular la esdad del estudiante suplente.
	$date1 = new DateTime($fecha_nac);
	$date2 = new DateTime(date('d-m-Y'));
	$diff = $date1->diff($date2);
	$edad = $diff->y;

	// Consulta utilizada para insertar un nuevo estudiante como suplente.
	$nuevo_suplente = "UPDATE suplentes$semana SET ape1 = '$ape1', ape2 = '$ape2', nom1 = '$nom1', nom2 = '$nom2', genero = '$genero', dir_res = '$dir_res', cod_mun_res = '$cod_mun_res', telefono = '$telefono', cod_mun_nac = '$cod_mun_nac', fecha_nac = '$fecha_nac', cod_estrato = '$cod_estrato', sisben = '$sisben', cod_discap = '$cod_discap', etnia = '$etnia', cod_pob_victima = '$cod_pob_victima', cod_grado = '$cod_grado', nom_grupo = '$nom_grupo', cod_jorn_est = '$cod_jorn_est', repitente = '$repitente', edad = '$edad', zona_res_est = '$sector', activo = '$estado' WHERE id = '$id'";

	// Condición que verifica si la consulta se ejecutó exitosamente.
	if ($Link->query($nuevo_suplente) === TRUE)
	{
		$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '48', 'Ingresó el suplente con número de identificación <strong>".$num_doc."	</strong>')";
		$Link->query($sqlBitacora);

		$respuestaAJAX = [
			"success" => 1,
			"message" => "El suplente fue actualizado exitosamente.". $nuevo_suplente
		];
	}
	else
	{
		$respuestaAJAX = [
			"success" => 0,
			"message" => "No fue posible actualizar el suplente.". $nuevo_suplente
		];
	}

	echo json_encode($respuestaAJAX);
