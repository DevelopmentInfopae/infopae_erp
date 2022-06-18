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

$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
$consultaComplementos = "SELECT CODIGO, ID FROM tipo_complemento ORDER BY CODIGO ";
$respuestaComplementos = $Link->query($consultaComplementos) or die (mysqli_error($Link));
if ($respuestaComplementos->num_rows > 0) {
	while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
		$complementos[$dataComplementos['CODIGO']] = $dataComplementos['CODIGO'];
	}
}
$concatComplementos = ''; 
foreach ($complementos as $key => $value) {
	$concatComplementos .= $value.',';
}

for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
	foreach ($complementos as $key => $value) {
		$concatComplementos .= 'Etario'.$i.'_'.$value.',';
	}
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
									$concatComplementos
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
			foreach ($complementos as $key => $value) {
				$aux = $_POST[$value.'Total'];
				$consulta .= " $aux, ";
			}
			for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
				foreach ($complementos as $key => $value) {
					$aux = $_POST[$value.$i];
					$consulta .= " $aux, ";
				}
			}
			$aux = $_POST['semana'];
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
			$semanaSC = $_POST['semana'];

			$consulta = " UPDATE sedes_cobertura SET ";
			$aux = $_POST['totalTotal'];
			$consulta .= " cant_Estudiantes = $aux, ";
			$consulta .= " num_est_focalizados = $aux, ";
			$consulta .= " num_est_activos = $aux, ";

			foreach ($complementos as $key => $value) {
				$aux = $_POST[$value.'Total'];
				$consulta .= $value . "= $aux, ";
			}

			for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
				foreach ($complementos as $key => $value) {
					$aux = $_POST[$value.$i];
					$consulta .= " Etario".$i."_".$value. " = $aux, ";
				}
			}

			$consulta .= " WHERE cod_sede = $sede AND mes = '$mes' AND semana = '$semanaSC' ";
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
									$concatComplementos
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
	foreach ($complementos as $key => $value) {
		$aux = $_POST[$value.'Total'];
		$consulta .= " $aux, ";
	}
	for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
		foreach ($complementos as $key => $value) {
			$aux = $_POST[$value.$i];
			$consulta .= " $aux, ";
		}
	}
	$aux = $_POST['semana'];
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
	$semanaSC = $_POST['semana'];

	$consulta = " UPDATE sedes_cobertura SET ";
	$aux = $_POST['totalTotal'];
	$consulta .= " cant_Estudiantes = $aux, ";
	$consulta .= " num_est_focalizados = $aux, ";
	$consulta .= " num_est_activos = $aux, ";

	foreach ($complementos as $key => $value) {
		$aux = $_POST[$value.'Total'];
		$consulta .= $value . "= $aux, ";
	}

	for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
		foreach ($complementos as $key => $value) {
			$aux = $_POST[$value.$i];
			$consulta .= " Etario".$i."_".$value. " = $aux, ";
		}
	}
	$consulta = trim($consulta,', ');
	$consulta .= " WHERE cod_sede = $sede AND mes = '$mes' AND semana = '$semanaSC' "; 
	$Link->query($consulta) or die ("Error al actualizar sedes cobertura para la semana $semanaSC, mes $mes, sede $sede ". mysqli_error($Link));
}

echo json_encode(array("log"=>$log, "respuesta"=>$respuesta, "reporte"=>$reporte));
