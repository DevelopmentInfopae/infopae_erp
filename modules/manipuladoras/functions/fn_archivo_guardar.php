<?php
$respuesta = '';

$dispositivo = $_POST["dispositivo"];
if ($dispositivo < 10) {
	$dispositivo = '00'.$dispositivo;
}
else if ($dispositivo < 100) {
	$dispositivo = '0'.$dispositivo;
}
$ext_permitidas = array("kq","KQ");
$bandera=0;
if (isset($_FILES['archivo'])) {
	$archivo = $_FILES['archivo'];  	
    $nombre = $_FILES['archivo']['name'];
    $nombre = (string)$nombre;
    $pos = strpos($nombre, '.');
    if (!(strpos($nombre,".")===false)) {
	    $file_ext = explode(".",$nombre);
	    $longitudArray = count($file_ext);
	    $longitudArray--;
	    $file_ext = $file_ext[$longitudArray];
	    $nombreSinExt = str_replace('.'.$file_ext, '', $nombre);
	}
	// Validación de la extensión del archivo
    if (isset($file_ext)) {
        if (!in_array($file_ext,$ext_permitidas)) {
			$bandera++;
		}
    }
    // Fin Validación de la extensión del archivo

    // Validación del nombre del archivo
    if ( isset($nombreSinExt) && $bandera == 0 ) {
        if ($nombreSinExt != "BAK") {
        	$bandera++;	           
        }
    }
    // Fin Validación del nombre del archivo
    // Revisando el consecutivo de nombres
    if($bandera==0){
    	if (!file_exists("../usb_bak/".$dispositivo)) {
      		mkdir("../usb_bak/".$dispositivo, 0777, true);
    	}
        if (!file_exists("../usb_bak/".$dispositivo."/backup")) {
            mkdir("../usb_bak/".$dispositivo."/backup", 0777, true);
        }
    	if(file_exists("../usb_bak/".$dispositivo."/$nombre")){
    		$bandera++;
    		$contador=1;
    		while($bandera > 0){    			
    			$nombre = $nombreSinExt.$contador.".".$file_ext;
       			if(file_exists("../usb_bak/".$dispositivo."/$nombre")){
    				$contador++;
    			}
    			else{
    				$bandera = 0;
    			}
    		}

    	}
    }
    // Fin Revizando el consecutivo de nombres
	if($bandera==0){
		// if (move_uploaded_file($archivo['tmp_name'], "../usb_bak/".$dispositivo."/$nombre")) {
		// 	$respuesta = "1"; 
		// }
 	// 	else {
 	// 		$respuesta = "No se ha podido subir el archivo a la carpeta." ;       		
		// }
        if (move_uploaded_file($archivo['tmp_name'], "../usb_bak/".$dispositivo."/$nombre")) {
            date_default_timezone_set('America/Bogota');
            $ahora = date("YmdGis");   
            if (copy("../usb_bak/".$dispositivo."/$nombre", "../usb_bak/$dispositivo/backup/$ahora.KQ")){
                $respuesta = "1"; 
            }
            else {
                $respuesta = "No se ha podido subir el archivo a la carpeta backup." ; 
            }
        }
        else {
            $respuesta = "No se ha podido subir el archivo a la carpeta de dispositivo." ;              
        }
    }
}// Fin del if que recibe un tipo de dato archivo.
else{
	$respuesta = "No se recibido ningun archivo.";	
}

$nombre = substr($nombre, 0, -3);
echo json_encode(array("respuesta"=>$respuesta,"nombre"=>$nombre,"dispositivo"=>$dispositivo));
