<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];
$codigoMenu = "";
$mes = '';
$semana = '';
$dia = '';
$tipoComplemento = '';
$grupoEtario = '';
$descripcionMenu = 'No encontrado';
$Orden_Ciclo = '';

if(isset($_POST['mes']) && $_POST['mes'] != ''){
	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
if(isset($_POST['semana']) && $_POST['semana'] != ''){
	$semana = mysqli_real_escape_string($Link, $_POST['semana']);
}
if(isset($_POST['dia']) && $_POST['dia'] != ''){
	$dia = mysqli_real_escape_string($Link, $_POST['dia']);
}
if(isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != ''){
	$tipoComplemento = mysqli_real_escape_string($Link, $_POST['tipoComplemento']);
}
if(isset($_POST['grupoEtario']) && $_POST['grupoEtario'] != ''){
	$grupoEtario = mysqli_real_escape_string($Link, $_POST['grupoEtario']);
}
if(isset($_POST['variacion']) && $_POST['variacion'] != ''){
	$variacion = mysqli_real_escape_string($Link, $_POST['variacion']);
}
$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = " 	SELECT p.* 
				FROM planilla_semanas ps 
				LEFT JOIN productos$periodoActual p ON ps.MENU = p.Orden_Ciclo 
				WHERE ps.MES = \"$mes\" AND ps.SEMANA = \"$semana\" AND ps.DIA = \"$dia\"AND p.Cod_Tipo_complemento = \"$tipoComplemento\"AND p.Cod_Grupo_Etario = \"$grupoEtario\" AND  p.Codigo LIKE \"01%\" AND p.Nivel = 3 AND p.cod_variacion_menu = \"$variacion\" ";
// echo "$consulta";
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los menú. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = 1;
	while($row = $resultado->fetch_assoc()){
		$codigoMenu = $row["Codigo"];
		$descripcionMenu = $row["Descripcion"];	
		$Orden_Ciclo = $row['Orden_Ciclo'];
	}
}if($resultado){
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"codigoMenu" => $codigoMenu,
		"descripcionMenu" => $descripcionMenu,
		"Orden_Ciclo" => $Orden_Ciclo
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha presentado un error."
	);
}
echo json_encode($resultadoAJAX);