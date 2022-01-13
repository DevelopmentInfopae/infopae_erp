<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config.php';
require_once '../../db/conexion.php';
require 'Twilio/autoload.php'; 
use Twilio\Rest\Client; 

$periodoActual = $_SESSION['periodoActual'];

$celular = "";
$mensaje = "";
$dispositivoId = "";
$usrDispositivo = "";
$fecha = date('Y-m-d');
$anno = "";
$mes = "";
$dia = "";
$semana = "";
$menu = "";
$grupoEtario = 0;
$rangoEdad = "";

$date = new DateTime($fecha);
$anno = $date->format('Y');
$mes = $date->format('m');
$dia = $date->format('d');

$consulta = " SELECT semana FROM planilla_semanas WHERE ano = \"$anno\" AND mes = \"$mes\" AND dia = \"$dia\" ";
$resultado = $Link->query($consulta) or die ('Error al buscar el numero de la semana'. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$semana = $row["semana"];
}

$consultaBiometriaReg = " SELECT dispositivo_id, usr_dispositivo_id, text_message FROM biometria_reg WHERE YEAR(fecha) = $anno AND MONTH(fecha) = $mes AND DAY(fecha) = $dia AND text_message = 0 ";
$respuestaBiometriaReg = $Link->query($consultaBiometriaReg) or die ('Error al consultar la biometria_reg' . mysqli_error($Link));
if ($respuestaBiometriaReg->num_rows > 0) {
	while ($dataBiometriaReg = $respuestaBiometriaReg->Fetch_assoc()) {
		$biometriasReg[] = $dataBiometriaReg;
	}
	foreach ($biometriasReg as $key => $biometriaReg) {
		/* Datos del estudiante */
		$consulta = " SELECT f.nom1, 
							f.nom2, 
							f.ape1, 
							f.ape2, 
							f.telefono, 
							f.edad, 
							f.Tipo_complemento 
						FROM focalizacion$semana f 
						LEFT JOIN biometria b ON b.tipo_doc = f.tipo_doc AND b.num_doc = f.num_doc 
						WHERE b.id_dispositivo = " .$biometriaReg['dispositivo_id']. " AND b.id_bioest = '" .$biometriaReg['usr_dispositivo_id']. "' ";
		// exit(var_dump($consulta));
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
		$resultado = $Link->query($consulta) or die ('Error al insertar en la tabla de biometrias_reg'. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			while($row = $resultado->fetch_assoc()){
				$aux = $row["Componente"]."\n";
				$aux = mb_strtolower($aux, 'UTF-8');
				$aux = ucfirst($aux);
				$preparaciones .= $aux;
			}
		}

		$updateBiometriaReg = " UPDATE biometria_reg SET text_message = 1 WHERE dispositivo_id =  '" .$biometriaReg['dispositivo_id']. "' AND usr_dispositivo_id = '" .$biometriaReg['usr_dispositivo_id']. "' AND fecha LIKE '" .$anno.'-'.$mes.'-'.$dia. "%' ";
		$Link->query($updateBiometriaReg);
 
		//echo $preparaciones;
		$mensaje = "Buenos días,\nSu hijo(a) $nombre Acaba de recibir el complemento $complemento $rangoEdad, que incluye: \n";
		$preparaciones = str_replace($rangoEdad, "", $preparaciones);
		$preparaciones = str_replace("normal", "", $preparaciones);
		$mensaje .= trim($preparaciones," ");

		$sid    = "AC39e560b6694977ce4d1bf253e8da3c01"; 
		$token  = "41c81be6b42e7a5f0576bf285981a86e"; 
		$twilio = new Client($sid, $token); 

		$message = $twilio->messages 
		->create("whatsapp:+57$celular", // to 
			array( 
				"from" => "whatsapp:+14155238886",
				"body" => $mensaje
			) 
		); 
		print($message->sid);				
	}
}

