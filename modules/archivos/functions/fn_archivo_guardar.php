<?php
include '../../../config.php';
require_once '../../../db/conexion.php';
include_once 'funciones.php';

//var_dump($_POST);

date_default_timezone_set('America/Bogota');
$fecha = date('Y-m-d H:i:s');
$sede = $_POST['sede'];
$nombreRegistro = $_POST['nombre'];
$categoria = $_POST['categoria'];

$municipio = $_POST['municipio'];
$institucion = $_POST['institucion'];
$sede = $_POST['sede'];

// $carpetaFisica = $rootUrl.'/upload/sedes/'.$sede.'/';
$carpetaFisica = $infopaeData;
//$carpeta = 'upload/modulo_archivos/';

//Verificando la existencia del directorio
if (!file_exists($carpetaFisica)) {
    mkdir($carpetaFisica, 0755);
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
		if($size > 320000000)
		{
			$reporte .= "<p style='color: red'>Error, el tamaño máximo permitido es 310 MB </p>";
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
		else { }
		if($bandera == 0){
			$src = $carpetaFisica.$nombre;
			$srcw = $nombre;





			//resize_crop_image(826,550,$ruta_provisional, $src);

			// var_dump($ruta_provisional);
			// var_dump($src);


			if(move_uploaded_file($ruta_provisional, $src)){
				$Link = new mysqli($Hostname, $Username, $Password, $Database);
				if ($Link->connect_errno) { echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; }
				$Link->set_charset("utf8");
				$consulta = " insert into mod_archivos (fecha_carga, cod_sede, ruta, nombre, categoria, extension, cod_municipio, cod_inst) values ('$fecha', '$sede', '$srcw', '$nombreRegistro', '$categoria', '$ext', '$municipio', '$institucion')";
				$resultado = $Link->query($consulta) or die ('No se pudieron insertar los datos del archivo cargado. '. mysqli_error($Link));
			}
			$reporte = 1;
		}
		echo $reporte;
	}
}
