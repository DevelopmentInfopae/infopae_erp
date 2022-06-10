<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';
include_once 'funciones.php';
date_default_timezone_set('America/Bogota');

$log = "";
$semanas = "";
$reporte = "";
$respuesta = 1;
$fecha = date('Y-m-d H:i:s');
$carpeta = 'upload/novedades/';
$carpetaFisica = '../../../upload/novedades/';

if(isset($_POST['semanas'])){
	$semanas = $_POST['semanas'];
	$semanas = explode(',', $semanas);
}

//Verificando la existencia del directorio
if (!file_exists($carpetaFisica)) {
    mkdir($carpetaFisica, 0777);
}

if ( $_FILES['foto']['size'][0] > 0 ) {
   $reporte = null;
   for($x=0; $x<count($_FILES["foto"]["name"]); $x++) {
	   $file = $_FILES["foto"];
	   $tipo = $file["type"][$x];
	   $size = $file["size"][$x];
	   $nombre = $file["name"][$x];
	   $ruta_provisional = $file["tmp_name"][$x];
	   $dimensiones = getimagesize($ruta_provisional);
	   $width = $dimensiones[0];
	   $height = $dimensiones[1];
	   $rand1 = rand();
	   $rand2 = rand();
	   $ext = pathinfo($nombre, PATHINFO_EXTENSION);
	   $nombre = $rand1.$rand2.'.'.$ext;
	   $nombreFoto = $rand1.$rand2;
	   $bandera = 0;

	   if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'image/gif' && $tipo != 'application/pdf') {
        	$reporte .= "<p style='color: red'>Error $nombre, el archivo no es una imagen o un PDF.</p>";
			$bandera++;
	   } else if ($size > 220000) {
        	$reporte .= "<p style='color: red'>Error, el tamaño máximo permitido es 200 KB </p>";
			$bandera++;
	   }
		if($bandera == 0) {
			$consulta = "INSERT INTO novedades_priorizacion (
								num_novedad,
								id_usuario,
								fecha_hora,
								cod_sede,
								APS,
								CAJMRI,
								CAJTRI,
								CAJMPS,
								CAJTPS,
								RPC,
								Etario1_APS,
								Etario1_CAJMRI,
								Etario1_CAJTRI,
								Etario1_CAJMPS,
								Etario1_CAJTPS,
								Etario1_RPC,
								Etario2_APS,
								Etario2_CAJMRI,
								Etario2_CAJTRI,
								Etario2_CAJMPS,
								Etario2_CAJTPS,
								Etario2_RPC,
								Etario3_APS,
								Etario3_CAJMRI,
								Etario3_CAJTRI,
								Etario3_CAJMPS,
								Etario3_CAJTPS,
								Etario3_RPC,
								Semana,
								observaciones,
								arch_adjunto,
								estado)
							VALUES ( 1, ";

			$aux = $_SESSION['id_usuario'];
			$consulta .= " $aux, ";
			$consulta .= " '$fecha', ";
			$aux = $_POST['sede'];
			$consulta .= " $aux, ";
			$aux = $_POST['APSTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRITotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTRITotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPSTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTPSTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['RPCTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTRI1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTPS1'];
			$consulta .= " $aux, ";
			$aux = $_POST['RPC1'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTRI2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTPS2'];
			$consulta .= " $aux, ";
			$aux = $_POST['RPC2'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTRI3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTPS3'];
			$consulta .= " $aux, ";
			$aux = $_POST['RPC3'];
			$consulta .= " $aux, ";
			$aux = $semanas[0];
			$consulta .= " '$aux', ";
			$aux = $_POST['observaciones'];
			$consulta .= " '$aux', ";
			$consulta .= " '$carpeta', ";
			$consulta .= " 1 ) ";

			$Link->query($consulta) or die ('Error insertando la novedad de priorización.'. mysqli_error($Link));
			$nuevoId = $Link->insert_id;

			//Actualizando sedes coberturas
			$sede = $_POST['sede'];
			$mes = $_POST['mes'];
			$semanaSC = $semanas[0];

			$consulta = " update sedes_cobertura set ";
			$aux = $_POST['totalTotal'];
			$consulta .= " cant_Estudiantes = $aux , ";
			$consulta .= " num_est_focalizados = $aux , ";
			$consulta .= " num_est_activos = $aux , ";
			$aux = $_POST['APSTotal'];
			$consulta .= " APS = $aux , ";
			$aux = $_POST['CAJMRITotal'];
			$consulta .= " CAJMRI = $aux , ";
			$aux = $_POST['CAJTRITotal'];
			$consulta .= " CAJTRI = $aux , ";
			$aux = $_POST['CAJMPSTotal'];
			$consulta .= " CAJMPS = $aux , ";
			$aux = $_POST['CAJTPSTotal'];
			$consulta .= " CAJTPS = $aux , ";
			$aux = $_POST['RPCTotal'];
			$consulta .= " RPC = $aux , ";
			$aux = $_POST['APS1'];
			$consulta .= " Etario1_APS = $aux , ";
			$aux = $_POST['CAJMRI1'];
			$consulta .= " Etario1_CAJMRI = $aux , ";
			$aux = $_POST['CAJTRI1'];
			$consulta .= " Etario1_CAJTRI = $aux , ";
			$aux = $_POST['CAJMPS1'];
			$consulta .= " Etario1_CAJMPS = $aux , ";
			$aux = $_POST['CAJTPS1'];
			$consulta .= " Etario1_CAJTPS = $aux , ";
			$aux = $_POST['RPC1'];
			$consulta .= " Etario1_RPC = $aux , ";
			$aux = $_POST['APS2'];
			$consulta .= " Etario2_APS = $aux , ";
			$aux = $_POST['CAJMRI2'];
			$consulta .= " Etario2_CAJMRI = $aux , ";
			$aux = $_POST['CAJTRI2'];
			$consulta .= " Etario2_CAJTRI = $aux , ";
			$aux = $_POST['CAJMPS2'];
			$consulta .= " Etario2_CAJMPS = $aux , ";
			$aux = $_POST['CAJTPS2'];
			$consulta .= " Etario2_CAJTPS = $aux , ";
			$aux = $_POST['RPC2'];
			$consulta .= " Etario2_RPC = $aux , ";
			$aux = $_POST['APS3'];
			$consulta .= " Etario3_APS = $aux , ";
			$aux = $_POST['CAJMRI3'];
			$consulta .= " Etario3_CAJMRI = $aux , ";
			$aux = $_POST['CAJTRI3'];
			$consulta .= " Etario3_CAJTRI = $aux , ";
			$aux = $_POST['CAJMPS3'];
			$consulta .= " Etario3_CAJMPS = $aux, ";
			$aux = $_POST['CAJTPS3'];
			$consulta .= " Etario3_CAJTPS = $aux, ";
			$aux = $_POST['RPC3'];
			$consulta .= " Etario3_RPC = $aux ";
			$consulta .= " where cod_sede = $sede and mes = '$mes' and semana = '$semanaSC' ";
			$Link->query($consulta) or die ('Error al actualizar sedes cobertura para la semana $semanaSC, mes $mes, sede $sede '. mysqli_error($Link));

			if($nuevoId > 0){
				//Colocando el archivo en la carpeta
				$nombre = $nuevoId.'.'.$ext;
				$src = $carpetaFisica.$nombre;
				$srcw = $carpeta.$nombre;
				if(move_uploaded_file($ruta_provisional, $src)){
					//Actualizando la URL de la priorización
					$consulta = " update novedades_priorizacion set arch_adjunto = '$srcw' where id = $nuevoId ";
					$Link->query($consulta) or die ('Error actualizando la URL del archivo de priorización'. mysqli_error($Link));
				} else {
					$reporte = "El archivo adjunto no ha sido cargado exitosamente. ->" . $ruta_provisional . "  -> " . $src ;
					$respuesta++;
				}
			}

			// Cuando hay más de una semana se hancen las inserciones con la misma información y el mismo archivo adjunto
			$indiceSemana = 0;
			foreach ($semanas as $semana) {
				if($indiceSemana > 0){
					$consulta = "INSERT INTO novedades_priorizacion (
											num_novedad,
											id_usuario,
											fecha_hora,
											cod_sede,
											APS,
											CAJMRI,
											CAJTRI,
											CAJMPS,
											CAJTPS,
											RPC,
											Etario1_APS,
											Etario1_CAJMRI,
											Etario1_CAJTRI,
											Etario1_CAJMPS,
											Etario1_CAJTPS,
											Etario1_RPC,
											Etario2_APS,
											Etario2_CAJMRI,
											Etario2_CAJTRI,
											Etario2_CAJMPS,
											Etario2_CAJTPS,
											Etario2_RPC,
											Etario3_APS,
											Etario3_CAJMRI,
											Etario3_CAJTRI,
											Etario3_CAJMPS,
											Etario3_CAJTPS,
											Etario3_RPC,
											Semana,
											observaciones,
											arch_adjunto,
											estado
									)
									VALUES
									( 1, ";

					$aux = $_SESSION['id_usuario'];
					$consulta .= " $aux, ";
					$consulta .= " '$fecha', ";
					$aux = $_POST['sede'];
					$consulta .= " $aux, ";
					$aux = $_POST['APSTotal'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMRITotal'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJTRITotal'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMPSTotal'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJTPSTotal'];
					$consulta .= " $aux, ";
					$aux = $_POST['RPCTotal'];
					$consulta .= " $aux, ";
					$aux = $_POST['APS1'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMRI1'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJTRI1'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMPS1'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJTPS1'];
					$consulta .= " $aux, ";
					$aux = $_POST['RPC1'];
					$consulta .= " $aux, ";
					$aux = $_POST['APS2'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMRI2'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJTRI2'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMPS2'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJTPS2'];
					$consulta .= " $aux, ";
					$aux = $_POST['RPC2'];
					$consulta .= " $aux, ";
					$aux = $_POST['APS3'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMRI3'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJTRI3'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMPS3'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJTPS3'];
					$consulta .= " $aux, ";
					$aux = $_POST['RPC3'];
					$consulta .= " $aux, ";
					$aux = $semana;
					$consulta .= " '$aux', ";
					$aux = $_POST['observaciones'];
					$consulta .= " '$aux', ";
					$consulta .= " '$srcw', ";
					$consulta .= " 1 ) ";
					$Link->query($consulta) or die ('Error insertando la novedad de priorización.'. mysqli_error($Link));

					// Actualización de sedes cobertura
					$semanaSC = $semana;
					$consulta = " update sedes_cobertura set ";
					$aux = $_POST['totalTotal'];
					$consulta .= " cant_Estudiantes = $aux , ";
					$consulta .= " num_est_focalizados = $aux , ";
					$consulta .= " num_est_activos = $aux , ";
					$aux = $_POST['APSTotal'];
					$consulta .= " APS = $aux , ";
					$aux = $_POST['CAJMRITotal'];
					$consulta .= " CAJMRI = $aux , ";
					$aux = $_POST['CAJTRITotal'];
					$consulta .= " CAJTRI = $aux , ";
					$aux = $_POST['CAJMPSTotal'];
					$consulta .= " CAJMPS = $aux , ";
					$aux = $_POST['CAJTPSTotal'];
					$consulta .= " CAJTPS = $aux , ";
					$aux = $_POST['RPCTotal'];
					$consulta .= " RPC = $aux , ";
					$aux = $_POST['APS1'];
					$consulta .= " Etario1_APS = $aux , ";
					$aux = $_POST['CAJMRI1'];
					$consulta .= " Etario1_CAJMRI = $aux , ";
					$aux = $_POST['CAJTRI1'];
					$consulta .= " Etario1_CAJTRI = $aux , ";
					$aux = $_POST['CAJMPS1'];
					$consulta .= " Etario1_CAJMPS = $aux , ";
					$aux = $_POST['CAJTPS1'];
					$consulta .= " Etario1_CAJTPS = $aux , ";
					$aux = $_POST['RPC1'];
					$consulta .= " Etario1_RPC = $aux , ";
					$aux = $_POST['APS2'];
					$consulta .= " Etario2_APS = $aux , ";
					$aux = $_POST['CAJMRI2'];
					$consulta .= " Etario2_CAJMRI = $aux , ";
					$aux = $_POST['CAJTRI2'];
					$consulta .= " Etario2_CAJTRI = $aux , ";
					$aux = $_POST['CAJMPS2'];
					$consulta .= " Etario2_CAJMPS = $aux , ";
					$aux = $_POST['CAJTPS2'];
					$consulta .= " Etario2_CAJTPS = $aux , ";
					$aux = $_POST['RPC2'];
					$consulta .= " Etario2_RPC = $aux , ";
					$aux = $_POST['APS3'];
					$consulta .= " Etario3_APS = $aux , ";
					$aux = $_POST['CAJMRI3'];
					$consulta .= " Etario3_CAJMRI = $aux , ";
					$aux = $_POST['CAJTRI3'];
					$consulta .= " Etario3_CAJTRI = $aux , ";
					$aux = $_POST['CAJMPS3'];
					$consulta .= " Etario3_CAJMPS = $aux, ";
					$aux = $_POST['CAJTPS3'];
					$consulta .= " Etario3_CAJTPS = $aux, ";
					$aux = $_POST['RPC3'];
					$consulta .= " Etario3_RPC = $aux ";
					$consulta .= " where cod_sede = $sede and mes = '$mes' and semana = '$semanaSC' ";
					$Link->query($consulta) or die ('Error al actualizar sedes cobertura para la semana $semanaSC, mes $mes, sede $sede '. mysqli_error($Link));
				}
				$indiceSemana++;
			}
		}else{
			$respuesta++;
		}
	}
} // termina el if con archivo adjunto 

// comienza actualizacion sin archivo adjunto 
else {
	$carpeta = '';
	$consulta = "INSERT INTO novedades_priorizacion (
								num_novedad,
								id_usuario,
								fecha_hora,
								cod_sede,
								APS,
								CAJMRI,
								CAJTRI,
								CAJMPS,
								CAJTPS,
								RPC,
								Etario1_APS,
								Etario1_CAJMRI,
								Etario1_CAJTRI,
								Etario1_CAJMPS,
								Etario1_CAJTPS,
								Etario1_RPC,
								Etario2_APS,
								Etario2_CAJMRI,
								Etario2_CAJTRI,
								Etario2_CAJMPS,
								Etario2_CAJTPS,
								Etario2_RPC,
								Etario3_APS,
								Etario3_CAJMRI,
								Etario3_CAJTRI,
								Etario3_CAJMPS,
								Etario3_CAJTPS,
								Etario3_RPC,
								Semana,
								observaciones,
								arch_adjunto,
								estado)
							VALUES ( 1, ";

	$aux = $_SESSION['id_usuario'];
	$consulta .= " $aux, ";
	$consulta .= " '$fecha', ";
	$aux = $_POST['sede'];
	$consulta .= " $aux, ";
	$aux = $_POST['APSTotal'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJMRITotal'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJTRITotal'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJMPSTotal'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJTPSTotal'];
	$consulta .= " $aux, ";
	$aux = $_POST['RPCTotal'];
	$consulta .= " $aux, ";
	$aux = $_POST['APS1'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJMRI1'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJTRI1'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJMPS1'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJTPS1'];
	$consulta .= " $aux, ";
	$aux = $_POST['RPC1'];
	$consulta .= " $aux, ";
	$aux = $_POST['APS2'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJMRI2'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJTRI2'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJMPS2'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJTPS2'];
	$consulta .= " $aux, ";
	$aux = $_POST['RPC2'];
	$consulta .= " $aux, ";
	$aux = $_POST['APS3'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJMRI3'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJTRI3'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJMPS3'];
	$consulta .= " $aux, ";
	$aux = $_POST['CAJTPS3'];
	$consulta .= " $aux, ";
	$aux = $_POST['RPC3'];
	$consulta .= " $aux, ";
	$aux = $semanas[0];
	$consulta .= " '$aux', ";
	$aux = $_POST['observaciones'];
	$consulta .= " '$aux', ";
	$consulta .= " '$carpeta', ";
	$consulta .= " 1 ) ";
	$Link->query($consulta) or die ('Error insertando la novedad de priorización.'. mysqli_error($Link));
	$nuevoId = $Link->insert_id;

	//Actualizando sedes coberturas
	$sede = $_POST['sede'];
	$mes = $_POST['mes'];
	$semanaSC = $semanas[0];
	$consulta = " update sedes_cobertura set ";
	$aux = $_POST['totalTotal'];
	$consulta .= " cant_Estudiantes = $aux , ";
	$consulta .= " num_est_focalizados = $aux , ";
	$consulta .= " num_est_activos = $aux , ";
	$aux = $_POST['APSTotal'];
	$consulta .= " APS = $aux , ";
	$aux = $_POST['CAJMRITotal'];
	$consulta .= " CAJMRI = $aux , ";
	$aux = $_POST['CAJTRITotal'];
	$consulta .= " CAJTRI = $aux , ";
	$aux = $_POST['CAJMPSTotal'];
	$consulta .= " CAJMPS = $aux , ";
	$aux = $_POST['CAJTPSTotal'];
	$consulta .= " CAJTPS = $aux , ";
	$aux = $_POST['RPCTotal'];
	$consulta .= " RPC = $aux , ";
	$aux = $_POST['APS1'];
	$consulta .= " Etario1_APS = $aux , ";
	$aux = $_POST['CAJMRI1'];
	$consulta .= " Etario1_CAJMRI = $aux , ";
	$aux = $_POST['CAJTRI1'];
	$consulta .= " Etario1_CAJTRI = $aux , ";
	$aux = $_POST['CAJMPS1'];
	$consulta .= " Etario1_CAJMPS = $aux , ";
	$aux = $_POST['CAJTPS1'];
	$consulta .= " Etario1_CAJTPS = $aux , ";
	$aux = $_POST['RPC1'];
	$consulta .= " Etario1_RPC = $aux , ";
	$aux = $_POST['APS2'];
	$consulta .= " Etario2_APS = $aux , ";
	$aux = $_POST['CAJMRI2'];
	$consulta .= " Etario2_CAJMRI = $aux , ";
	$aux = $_POST['CAJTRI2'];
	$consulta .= " Etario2_CAJTRI = $aux , ";
	$aux = $_POST['CAJMPS2'];
	$consulta .= " Etario2_CAJMPS = $aux , ";
	$aux = $_POST['CAJTPS2'];
	$consulta .= " Etario2_CAJTPS = $aux , ";
	$aux = $_POST['RPC2'];
	$consulta .= " Etario2_RPC = $aux , ";
	$aux = $_POST['APS3'];
	$consulta .= " Etario3_APS = $aux , ";
	$aux = $_POST['CAJMRI3'];
	$consulta .= " Etario3_CAJMRI = $aux , ";
	$aux = $_POST['CAJTRI3'];
	$consulta .= " Etario3_CAJTRI = $aux , ";
	$aux = $_POST['CAJMPS3'];
	$consulta .= " Etario3_CAJMPS = $aux, ";
	$aux = $_POST['CAJTPS3'];
	$consulta .= " Etario3_CAJTPS = $aux, ";
	$aux = $_POST['RPC3'];
	$consulta .= " Etario3_RPC = $aux ";
	$consulta .= " where cod_sede = $sede and mes = '$mes' and semana = '$semanaSC' ";
	$Link->query($consulta) or die ('Error al actualizar sedes cobertura para la semana $semanaSC, mes $mes, sede $sede '. mysqli_error($Link));

	// Cuando hay más de una semana se hancen las inserciones con la misma información y el mismo archivo adjunto
	$indiceSemana = 0;
	foreach ($semanas as $semana) {
		if($indiceSemana > 0){
			$consulta = "INSERT INTO novedades_priorizacion (
											num_novedad,
											id_usuario,
											fecha_hora,
											cod_sede,
											APS,
											CAJMRI,
											CAJTRI,
											CAJMPS,
											CAJTPS,
											RPC,
											Etario1_APS,
											Etario1_CAJMRI,
											Etario1_CAJTRI,
											Etario1_CAJMPS,
											Etario1_CAJTPS,
											Etario1_RPC,
											Etario2_APS,
											Etario2_CAJMRI,
											Etario2_CAJTRI,
											Etario2_CAJMPS,
											Etario2_CAJTPS,
											Etario2_RPC,
											Etario3_APS,
											Etario3_CAJMRI,
											Etario3_CAJTRI,
											Etario3_CAJMPS,
											Etario3_CAJTPS,
											Etario3_RPC,
											Semana,
											observaciones,
											arch_adjunto,
											estado)
									VALUES
									( 1, ";

			$aux = $_SESSION['id_usuario'];
			$consulta .= " $aux, ";
			$consulta .= " '$fecha', ";
			$aux = $_POST['sede'];
			$consulta .= " $aux, ";
			$aux = $_POST['APSTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRITotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTRITotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPSTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTPSTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['RPCTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTRI1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTPS1'];
			$consulta .= " $aux, ";
			$aux = $_POST['RPC1'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTRI2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTPS2'];
			$consulta .= " $aux, ";
			$aux = $_POST['RPC2'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTRI3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJTPS3'];
			$consulta .= " $aux, ";
			$aux = $_POST['RPC3'];
			$consulta .= " $aux, ";
			$aux = $semana;
			$consulta .= " '$aux', ";
			$aux = $_POST['observaciones'];
			$consulta .= " '$aux', ";
			$consulta .= " '$srcw', ";
			$consulta .= " 1 ) ";
			$Link->query($consulta) or die ('Error insertando la novedad de priorización.'. mysqli_error($Link));

			// Actualización de sedes cobertura
			$semanaSC = $semana;
			$consulta = " update sedes_cobertura set ";
			$aux = $_POST['totalTotal'];
			$consulta .= " cant_Estudiantes = $aux , ";
			$consulta .= " num_est_focalizados = $aux , ";
			$consulta .= " num_est_activos = $aux , ";
			$aux = $_POST['APSTotal'];
			$consulta .= " APS = $aux , ";
			$aux = $_POST['CAJMRITotal'];
			$consulta .= " CAJMRI = $aux , ";
			$aux = $_POST['CAJTRITotal'];
			$consulta .= " CAJTRI = $aux , ";
			$aux = $_POST['CAJMPSTotal'];
			$consulta .= " CAJMPS = $aux , ";
			$aux = $_POST['CAJTPSTotal'];
			$consulta .= " CAJTPS = $aux , ";
			$aux = $_POST['RPCTotal'];
			$consulta .= " RPC = $aux , ";
			$aux = $_POST['APS1'];
			$consulta .= " Etario1_APS = $aux , ";
			$aux = $_POST['CAJMRI1'];
			$consulta .= " Etario1_CAJMRI = $aux , ";
			$aux = $_POST['CAJTRI1'];
			$consulta .= " Etario1_CAJTRI = $aux , ";
			$aux = $_POST['CAJMPS1'];
			$consulta .= " Etario1_CAJMPS = $aux , ";
			$aux = $_POST['CAJTPS1'];
			$consulta .= " Etario1_CAJTPS = $aux , ";
			$aux = $_POST['RPC1'];
			$consulta .= " Etario1_RPC = $aux , ";
			$aux = $_POST['APS2'];
			$consulta .= " Etario2_APS = $aux , ";
			$aux = $_POST['CAJMRI2'];
			$consulta .= " Etario2_CAJMRI = $aux , ";
			$aux = $_POST['CAJTRI2'];
			$consulta .= " Etario2_CAJTRI = $aux , ";
			$aux = $_POST['CAJMPS2'];
			$consulta .= " Etario2_CAJMPS = $aux , ";
			$aux = $_POST['CAJTPS2'];
			$consulta .= " Etario2_CAJTPS = $aux , ";
			$aux = $_POST['RPC2'];
			$consulta .= " Etario2_RPC = $aux , ";
			$aux = $_POST['APS3'];
			$consulta .= " Etario3_APS = $aux , ";
			$aux = $_POST['CAJMRI3'];
			$consulta .= " Etario3_CAJMRI = $aux , ";
			$aux = $_POST['CAJTRI3'];
			$consulta .= " Etario3_CAJTRI = $aux , ";
			$aux = $_POST['CAJMPS3'];
			$consulta .= " Etario3_CAJMPS = $aux, ";
			$aux = $_POST['CAJTPS3'];
			$consulta .= " Etario3_CAJTPS = $aux, ";
			$aux = $_POST['RPC3'];
			$consulta .= " Etario3_RPC = $aux ";
			$consulta .= " where cod_sede = $sede and mes = '$mes' and semana = '$semanaSC' ";
			$Link->query($consulta) or die ('Error al actualizar sedes cobertura para la semana $semanaSC, mes $mes, sede $sede '. mysqli_error($Link));
			$indiceSemana++;
		} // if
	} // foreach
}

echo json_encode(array("log"=>$log, "respuesta"=>$respuesta, "reporte"=>$reporte));
