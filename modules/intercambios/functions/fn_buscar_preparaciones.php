<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];

//menu
// SELECT p.* FROM planilla_semanas ps
// LEFT JOIN productos$periodoActual p ON ps.MENU = p.Orden_Ciclo 
// WHERE ps.MES = "05" AND ps.SEMANA = "16" AND ps.DIA = "2"
// AND p.Cod_Tipo_complemento = "APS"
// AND p.Cod_Grupo_Etario = "1"
// AND  p.Codigo LIKE '01%' AND p.Nivel = 3 


// Consulta para buscar las preparaciones d eun menú
// SELECT f.id as idFichaTecnica,fd.* FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo 
// WHERE fd.IdFT = '424'


$codigoMenu = '';
$idFichaTecnicaMenu = '';

if(isset($_POST['codigoMenu']) && $_POST['codigoMenu'] != ''){
	$codigoMenu = mysqli_real_escape_string($Link, $_POST['codigoMenu']);
}

$opciones = "<option value=\"\">Seleccione uno</option>";

// Consultando en ficha tecnicamediante el codigo para encontrar el id de la ficha tecnica.
$consulta = " SELECT Id FROM fichatecnica f WHERE f.Codigo = \"$codigoMenu\" ";
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$idFichaTecnicaMenu = $row["Id"];
}

$consulta = " SELECT f.id as idFichaTecnica,fd.* FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = \"$idFichaTecnicaMenu\" ";

// echo $consulta;

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = 1;
	while($row = $resultado->fetch_assoc()){
		$id = $row["codigo"];
		$valor = $row["Componente"];

		$opciones .= "<option value=\"$id\"";
		$opciones .= ">";
		$opciones .= "$valor</option>";
	}
}if($resultado){
	$resultadoAJAX = array(
		"estado" => 1,
		"mensaje" => "Se ha cargado con exito.",
		"opciones" => $opciones
	);
}else{
	$resultadoAJAX = array(
		"estado" => 0,
		"mensaje" => "Se ha presentado un error."
	);
}
echo json_encode($resultadoAJAX);