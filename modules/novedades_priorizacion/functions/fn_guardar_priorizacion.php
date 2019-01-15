<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';
include_once 'funciones.php';
// var_dump($_SESSION);
//var_dump($_POST);
$semanas = '';
if(isset($_POST['semanas'])){
	$semanas = $_POST['semanas'];
	$semanas = explode(',', $semanas);
}
//var_dump($semanas);







$log = "";
$reporte = "";
$respuesta = 1;

date_default_timezone_set('America/Bogota');
$fecha = date('Y-m-d H:i:s');

$carpetaFisica = $rootUrl.'/upload/novedades/';
$carpeta = 'upload/novedades/';

//Verificando la existencia del directorio
if (!file_exists($carpetaFisica)) {
    mkdir($carpetaFisica, 0777);
}

if (isset($_FILES["foto"])){
   	$reporte = null;
    for($x=0; $x<count($_FILES["foto"]["name"]); $x++){
	    $file = $_FILES["foto"];
	    $nombre = $file["name"][$x];
	    $tipo = $file["type"][$x];
	    $ruta_provisional = $file["tmp_name"][$x];
	    $size = $file["size"][$x];
	    $dimensiones = getimagesize($ruta_provisional);
	    $width = $dimensiones[0];
	    $height = $dimensiones[1];
	    $rand1 = rand();
	    $rand2 = rand();
	    $ext = pathinfo($nombre, PATHINFO_EXTENSION);
	    $nombre = $rand1.$rand2.'.'.$ext;
	    $nombreFoto = $rand1.$rand2;
	    $bandera = 0;

	    if ($tipo != 'image/jpeg' && $tipo != 'image/jpg' && $tipo != 'image/png' && $tipo != 'image/gif' && $tipo != 'application/pdf')
		{
	        $reporte .= "<p style='color: red'>Error $nombre, el archivo no es una imagen o un PDF.</p>";
			$bandera++;
	    }
	    else if($size > 220000)
	    {
	        $reporte .= "<p style='color: red'>Error, el tamaño máximo permitido es 200 KB </p>";
			$bandera++;
	    }
	    else if($width > 500 || $height > 500)
	    {
	        //$reporte .= "<p style='color: red'>Error $nombre, la anchura y la altura máxima permitida es de 500px</p>";
	    }
	    else if($width < 60 || $height < 60)
	    {
	        //$reporte .= "<p style='color: red'>Error $nombre, la anchura y la altura mínima permitida es de 60px</p>";
	    }

	    if($bandera == 0){
	    	//resize_crop_image(826,550,$ruta_provisional, $src);
			$Link = new mysqli($Hostname, $Username, $Password, $Database);
			if ($Link->connect_errno) { echo "Fallo al contenctar a MySQL: (" . $Link->connect_errno . ") " . $Link->connect_error; }
			$Link->set_charset("utf8");
			$consulta = " insert into novedades_priorizacion
			(
				num_novedad,
				id_usuario,
				fecha_hora,
				cod_sede,
				APS,
				CAJMRI,
				CAJMPS,
				Etario1_APS,
				Etario1_CAJMRI,
				Etario1_CAJMPS,
				Etario2_APS,
				Etario2_CAJMRI,
				Etario2_CAJMPS,
				Etario3_APS,
				Etario3_CAJMRI,
				Etario3_CAJMPS,
				Semana,
				observaciones,
				estado
			)
			values
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
			$aux = $_POST['CAJMPSTotal'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI1'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS1'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI2'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS2'];
			$consulta .= " $aux, ";
			$aux = $_POST['APS3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMRI3'];
			$consulta .= " $aux, ";
			$aux = $_POST['CAJMPS3'];
			$consulta .= " $aux, ";
			$aux = $semanas[0];
			$consulta .= " '$aux', ";
			$aux = $_POST['observaciones'];
			$consulta .= " '$aux', ";
			$consulta .= " 1 ) ";
			//echo "<br><br>$consulta<br><br>";
			$Link->query($consulta) or die ('Error insertando la novedad de priorización.'. mysqli_error($Link));
			$nuevoId = $Link->insert_id;
			// echo "<br><br>Nuevo ID: $nuevoId <br><br>";


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
			$aux = $_POST['CAJMPSTotal'];
			$consulta .= " CAJMPS = $aux , ";
			$aux = $_POST['APS1'];
			$consulta .= " Etario1_APS = $aux , ";
			$aux = $_POST['CAJMRI1'];
			$consulta .= " Etario1_CAJMRI = $aux , ";
			$aux = $_POST['CAJMPS1'];
			$consulta .= " Etario1_CAJMPS = $aux , ";
			$aux = $_POST['APS2'];
			$consulta .= " Etario2_APS = $aux , ";
			$aux = $_POST['CAJMRI2'];
			$consulta .= " Etario2_CAJMRI = $aux , ";
			$aux = $_POST['CAJMPS2'];
			$consulta .= " Etario2_CAJMPS = $aux , ";
			$aux = $_POST['APS3'];
			$consulta .= " Etario3_APS = $aux , ";
			$aux = $_POST['CAJMRI3'];
			$consulta .= " Etario3_CAJMRI = $aux , ";
			$aux = $_POST['CAJMPS3'];
			$consulta .= " Etario3_CAJMPS = $aux ";
			$consulta .= " where cod_sede = $sede and mes = '$mes' and semana = '$semanaSC' ";
			$Link->query($consulta) or die ('Error al actualizar sedes cobertura para la semana $semanaSC, mes $mes, sede $sede '. mysqli_error($Link));





			if($nuevoId > 0){
				//Colocando el archivo en la carpeta
				$nombre = $nuevoId.'.'.$ext;
				$src = $carpetaFisica.$nombre;
				$srcw = $carpeta.$nombre;
				if(move_uploaded_file($ruta_provisional, $src)){
					// echo "<br><br>Se ha movido el archivo<br><br>";
					//Actualizando la URL de la priorización
					$consulta = " update novedades_priorizacion set arch_adjunto = '$srcw' where id = $nuevoId ";
					// echo "<br><br> $consulta <br><br>";
					$Link->query($consulta) or die ('Error actualizando la URL del archivo de priorización'. mysqli_error($Link));
				}
			}
			// Cuando hay más de una semana se hancen las inserciones con la misma información y el mismo archivo adjunto
			$indiceSemana = 0;
			foreach ($semanas as $semana) {
				if($indiceSemana > 0){
					$consulta = " insert into novedades_priorizacion
					(
						num_novedad,
						id_usuario,
						fecha_hora,
						cod_sede,
						APS,
						CAJMRI,
						CAJMPS,
						Etario1_APS,
						Etario1_CAJMRI,
						Etario1_CAJMPS,
						Etario2_APS,
						Etario2_CAJMRI,
						Etario2_CAJMPS,
						Etario3_APS,
						Etario3_CAJMRI,
						Etario3_CAJMPS,
						Semana,
						observaciones,
						arch_adjunto,
						estado
					)
					values
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
					$aux = $_POST['CAJMPSTotal'];
					$consulta .= " $aux, ";
					$aux = $_POST['APS1'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMRI1'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMPS1'];
					$consulta .= " $aux, ";
					$aux = $_POST['APS2'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMRI2'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMPS2'];
					$consulta .= " $aux, ";
					$aux = $_POST['APS3'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMRI3'];
					$consulta .= " $aux, ";
					$aux = $_POST['CAJMPS3'];
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
					$aux = $_POST['CAJMPSTotal'];
					$consulta .= " CAJMPS = $aux , ";
					$aux = $_POST['APS1'];
					$consulta .= " Etario1_APS = $aux , ";
					$aux = $_POST['CAJMRI1'];
					$consulta .= " Etario1_CAJMRI = $aux , ";
					$aux = $_POST['CAJMPS1'];
					$consulta .= " Etario1_CAJMPS = $aux , ";
					$aux = $_POST['APS2'];
					$consulta .= " Etario2_APS = $aux , ";
					$aux = $_POST['CAJMRI2'];
					$consulta .= " Etario2_CAJMRI = $aux , ";
					$aux = $_POST['CAJMPS2'];
					$consulta .= " Etario2_CAJMPS = $aux , ";
					$aux = $_POST['APS3'];
					$consulta .= " Etario3_APS = $aux , ";
					$aux = $_POST['CAJMRI3'];
					$consulta .= " Etario3_CAJMRI = $aux , ";
					$aux = $_POST['CAJMPS3'];
					$consulta .= " Etario3_CAJMPS = $aux ";
					$consulta .= " where cod_sede = $sede and mes = '$mes' and semana = '$semanaSC' ";
					$Link->query($consulta) or die ('Error al actualizar sedes cobertura para la semana $semanaSC, mes $mes, sede $sede '. mysqli_error($Link));
				}
				$indiceSemana++;
			}

		}else{
			$respuesta++;
		}
	}
}
echo json_encode(array("log"=>$log, "respuesta"=>$respuesta, "reporte"=>$reporte));
