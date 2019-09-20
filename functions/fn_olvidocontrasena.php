<?php
include '../config.php';
require_once '../db/conexion.php';
require_once '../wslib/nusoap.php';
date_default_timezone_set('America/Bogota');
$fecha = date('Y-m-d H:i:s');
if(isset($_POST['user']) && $_POST['user'] != ''){

	$usuario = mysqli_real_escape_string($Link, $_POST['user']);
	$pass = generaPass();
	$sqlup=" update usuarios SET clave= sha1('$pass'), nueva_clave = 0 where email='".$usuario."' ";
    $Link->query($sqlup);
    $registroAfectados = $Link->affected_rows;

	$auxConsulta = " select id from usuarios where email = '".$usuario."' ";
    $result = $Link->query($auxConsulta);
    $row = $result->fetch_assoc();
    $logIdUsr = $row['id'];
	$consulta = " insert into log (id_usuario,fecha,descripcion) values ('$logIdUsr','$fecha','Solicito reestablecer contraseña') ";
	$Link->query($consulta);

	if ($registroAfectados > 0) {
		echo '1';		
	    $cliente = new nusoap_client("https://www.xlogam.com/xlogamservices/servicio.php",false);

	    $id = 3;
	    $to = $usuario;
	    $subject = 'Se ha restablecido su contraseña de acceso a InfoPAE';

	    $headers = "From: InfoPAE <" . strip_tags('info@infopae.com.co') . ">\r\n";
	    $headers .= "Reply-To: ". strip_tags('info@infopae.com.co') . "\r\n";
	    $headers .= "CC: info@infopae.com.co\r\n";
	    $headers .= "MIME-Version: 1.0\r\n";
	    $headers .= "Content-Type: text/html; charset=utf-8\r\n";

	    $message = "<table> </tr> <tr><td><br></td></tr> <tr> <td align='left'><strong>Señor(a):</strong></td> </tr> <tr> <td align='left'><p>De acuerdo a su solicitud de envio de clave, a continuación presentamos la informaci&oacute;n que le permitirá ingresar a nuestro sistema:</p> <br> <b>Usuario:</b>  ".$usuario."<br> <b>Contrase&ntilde;a:</b>  ".$pass."</td> </tr> </table> ";
	    
	    $parametros = array('id'=>$id,'para'=>$to,'asunto'=>$subject,'cabeceras'=>$headers,'mensaje'=>$message);
	    $respuesta = $cliente->call("comunicacion",$parametros);

	    if($respuesta != '1'){
	        echo $respuesta;
	    }
	}
	else{
		echo "3";
	}
}














function generaPass(){
//Se define una cadena de caractares. Te recomiendo que uses esta.

$cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";

//Obtenemos la longitud de la cadena de caracteres

$longitudCadena=strlen($cadena);

//Se define la variable que va a contener la contraseña

$pass = "";

//Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras

$longitudPass=10;

//Creamos la contraseña

for($i=1 ; $i<=$longitudPass ; $i++){

//Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1

$pos=rand(0,$longitudCadena-1);
//Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
$pass .= substr($cadena,$pos,1);
}

return $pass;

}