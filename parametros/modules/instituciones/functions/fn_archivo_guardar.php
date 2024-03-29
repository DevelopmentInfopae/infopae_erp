<?php
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';
include '../../../config.php';
include_once 'funciones.php';

//var_dump($_POST);

date_default_timezone_set('America/Bogota');
$fecha = date('Y-m-d H:i:s');
$sede = $_POST['sede'];
$nombreRegistro = $_POST['nombre'];
$categoria = $_POST['categoria'];

$carpetaFisica = $rootUrl.'/uploads/sedes/'.$sede.'/';
$carpeta = 'uploads/sedes/'.$sede.'/';

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
	    else { }
	    if($bandera == 0){
	    	$src = $carpetaFisica.$nombre;
			$srcw = $carpeta.$nombre;
	    	//resize_crop_image(826,550,$ruta_provisional, $src);
	    	if(move_uploaded_file($ruta_provisional, $src)){
				$Link = new mysqli($Hostname, $Username, $Password, $Database);
				if ($Link->connect_errno) { echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error; }
				$Link->set_charset("utf8");
				$consulta = " insert into archivos (fecha_carga, cod_sede, ruta, nombre, categoria, extension) values ('$fecha', '$sede', '$srcw', '$nombreRegistro', '$categoria', '$ext' )";
				$resultado = $Link->query($consulta) or die ('No se pudieron insertar los datos del archivo cargado. '. mysqli_error($Link));
			}
			$reporte = 1;
		}
		echo $reporte;
    }

}
