<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../../config.php';
require_once '../../db/conexion.php';

$periodoActual = $_SESSION['periodoActual'];

//var_dump($_GET);

$recibido = "<pre class='xdebug-var-dump' dir='ltr'> <small>G:\xampp\htdocs\infopae2019\modules\entregas_biometricas\desencadenador.php:2:</small> 
<b>array</b> <i>(size=10)</i>   

'Logid' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'1014'</font> <i>(length=4)</i>   
'Userid' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'1'</font> <i>(length=1)</i>   
'CheckTime' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'Jun 18 2012 10:12AM'</font> <i>(length=19)</i>   
'CheckType' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'2'</font> <i>(length=1)</i>   
'Sensorid' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'3'</font> <i>(length=1)</i>   
'WorkType' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'4'</font> <i>(length=1)</i>   
'AttFlag' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'5'</font> <i>(length=1)</i>   
'Checked' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'1'</font> <i>(length=1)</i>   
'Exported' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'1'</font> <i>(length=1)</i>   
'OpenDoorFlag' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'1'</font> <i>(length=1)</i> </pre><br> ";

//echo "<br>$recibido<br>";

$celular = "";
$mensaje = "";

$dispositivoId = "";
$usrDispositivo = "";
$fecha = "";
$anno = "";
$mes = "";
$dia = "";
$semana = "";
$menu = "";
$grupoEtario = 0;
$rangoEdad = "";

if(isset($_GET['Sensorid']) && $_GET['Sensorid'] != ''){
	$dispositivoId = mysqli_real_escape_string($Link, $_GET['Sensorid']);
}
if(isset($_GET['Userid']) && $_GET['Userid'] != ''){
	$usrDispositivo = mysqli_real_escape_string($Link, $_GET['Userid']);
}
if(isset($_GET['CheckTime']) && $_GET['CheckTime'] != ''){
	$fecha = mysqli_real_escape_string($Link, $_GET['CheckTime']);
}







if($dispositivoId < 10){
	$dispositivoId = '00'.$dispositivoId;
} else if($dispositivoId >= 10){
	$dispositivoId = '0'.$dispositivoId;
}


/* Ojo declaración de variables solo para pruebas */
// $dispositivoId = '004';
// $usrDispositivo = 1534;
// $fecha = '2020-02-28 21:09:09';


//$fecha = substr($fecha, -4); 
$date = new DateTime($fecha);
//$fecha = $date->format('Y-m-d H:i:s');
$anno = $date->format('Y');
$mes = $date->format('m');
$dia = $date->format('d');

// $anno = '2019';
// $mes = '05';
// $dia = '20';

/* Inserción MySQL */
$consulta = " insert into biometria_reg ( dispositivo_id, usr_dispositivo_id, fecha ) values ( \"$dispositivoId\", $usrDispositivo, \"$fecha\" ) ";
$Link->query($consulta) or die ('Error al insertar en la tabla de biometrias_reg'. mysqli_error($Link));

/* Semana para hacer las consultas de focalización */

$consulta = " SELECT semana FROM planilla_semanas WHERE ano = \"$anno\" AND mes = \"$mes\" AND dia = \"$dia\" ";
//echo "<br>$consulta<br>";
$resultado = $Link->query($consulta) or die ('Error al buscar el numero de la semana'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$semana = $row["semana"];
}
//echo "<br>$semana<br>";


/* Datos del estudiante */
$consulta = " SELECT f.nom1, f.nom2, f.ape1, f.ape2, f.telefono, f.edad, f.Tipo_complemento FROM focalizacion$semana f LEFT JOIN biometria b ON b.tipo_doc = f.tipo_doc AND b.num_doc = f.num_doc WHERE b.id_dispositivo = \"$dispositivoId\" AND b.id_bioest = $usrDispositivo ";
//echo "<br>$consulta<br>";
$resultado = $Link->query($consulta) or die ('Error al buscar los datos del estudiante'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$celular = $row["telefono"];
	$nombre = $row["nom1"].' '.$row["nom2"].' '.$row["ape1"].' '.$row["ape2"];
	$edad = $row["edad"];
	$complemento = $row["Tipo_complemento"];
	if($edad < 9){
		$grupoEtario = 1;
		$rangoEdad = "4-8 años + 11 meses";
	}else if($edad >= 9 && $edad < 14 ){
		$grupoEtario = 2;
		$rangoEdad = "9-13 años + 11 meses";
	}else{
		$grupoEtario = 3;
		$rangoEdad = "14-17 años + 11 meses";
	}
}

/* Menu del día */
$consulta = " SELECT ps.SEMANA, ps.DIA, p.Id, p.Codigo, p.Descripcion FROM planilla_semanas ps LEFT JOIN productos$periodoActual p ON ps.MENU = p.Orden_Ciclo WHERE ps.MES = \"$mes\" AND ps.DIA = \"$dia\" AND p.Cod_Tipo_complemento = \"$complemento\" AND p.Cod_Grupo_Etario = \"$grupoEtario\" AND p.Codigo LIKE \"01%\" AND p.Nivel = 3 ";
//echo "<br>$consulta<br>";
$resultado = $Link->query($consulta) or die ('Error al insertar en la tabla de biometrias_reg'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$codigoProductoMenu = $row["Codigo"];
}

/* Id Ficha tecnica del menú */
$consulta = " SELECT Id FROM fichatecnica f WHERE f.Codigo = \"$codigoProductoMenu\" ";
$resultado = $Link->query($consulta) or die ('Error al insertar en la tabla de biometrias_reg'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$idFichaTecnicaMenu = $row["Id"];
}

/* Preparaciones del menú */
$preparaciones = '';
$consulta = " SELECT f.id as idFichaTecnica,fd.* FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = \"$idFichaTecnicaMenu\" ";
//echo "<br>$consulta<br>";
$resultado = $Link->query($consulta) or die ('Error al insertar en la tabla de biometrias_reg'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$aux = $row["Componente"]."\n";
		$aux = mb_strtolower($aux, 'UTF-8');
		$aux = ucfirst($aux);
		$preparaciones .= $aux;
	}
}
//echo $preparaciones;
$mensaje = "Buenos días,\nSu hijo(a) $nombre Acaba de recibir el complemento $complemento $rangoEdad, que incluye: \n";
$preparaciones = str_replace($rangoEdad, "", $preparaciones);
$preparaciones = str_replace("normal", "", $preparaciones);
$mensaje .= trim($preparaciones," ");


//echo "<br>$rangoEdad<br>";
echo "<br>$celular<br>";
echo "<br>$mensaje<br><br><br>";





// Update the path below to your autoload.php, 
// see https://getcomposer.org/doc/01-basic-usage.md 
//3115384551

require_once 'Twilio/autoload.php'; 
use Twilio\Rest\Client; 
 
$sid    = "AC50f30f90876425a1ce68db58927d4496"; 
$token  = "e28ccd4f3f84ef7468bf7645185eb263"; 
$twilio = new Client($sid, $token); 

$message = $twilio->messages 
->create("whatsapp:+57$celular", // to 
	array( 
		"from" => "whatsapp:+14155238886",
		"body" => $mensaje
	) 
); 
print($message->sid);
