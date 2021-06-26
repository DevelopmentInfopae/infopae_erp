<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual']; 

$institucionRector = "";
$municipio = '';
if(isset($_POST['municipio']) && $_POST['municipio'] != ''){
		$municipio = mysqli_real_escape_string($Link, $_POST['municipio']);
}

$validacion = '';
if(isset($_POST['validacion']) && $_POST['validacion'] != ''){
	$validacion = mysqli_real_escape_string($Link, $_POST['validacion']);
}
$opciones = "<option value=\"\">Seleccione uno</option>";

// Si es ususario de tipo rector buscar la institución del rector.
if($_SESSION["perfil"] == 6){
	$documentoRector = mysqli_real_escape_string($Link, $_SESSION['num_doc']);
	$consulta = " SELECT codigo_inst, nom_inst FROM instituciones WHERE cc_rector = \"$documentoRector\" ";
	//echo "<br><br>$consulta<br><br>";
	$resultado = $Link->query($consulta) or die ('No se pudo cargar la institucion del rector. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		$row = $resultado->fetch_assoc();
		if($row['codigo_inst'] != ""){
			$institucionRector = $row['codigo_inst'];	
		}
	}
} 
else if($_SESSION['perfil'] == 7) {
	$documentoCoordinador = $_SESSION['num_doc'];
	$consulta = " SELECT codigo_inst, nom_inst FROM instituciones WHERE 1 = 1 ";
	$consultaInstitucion = "SELECT i.codigo_inst FROM instituciones i LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1 ";
	$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar el codigo de institucion ' . mysqli_error($Link));
	if ($respuestaInstitucion->num_rows > 0) {
		$dataInstitucion = $respuestaInstitucion->fetch_assoc();
		$institucionRector = $dataInstitucion['codigo_inst'];
	}
}
else{
	$consulta = " select * from instituciones where 1=1 ";
}

$consulta.= " and cod_mun = \"$municipio\" and codigo_inst in (select cod_inst from sedes$periodoActual where 1=1 ";
if($validacion == 'Tablet'){
	$consulta.= " and (tipo_validacion = \"$validacion\" or tipo_validacion = \"Lector de Huella\" ) ";
}else{
	if($validacion != ''){
		$consulta.= " and tipo_validacion = \"$validacion\" ";
	}
}
$consulta.= " and cod_mun_sede = \"$municipio\") ";





//$consulta = " select * from instituciones where cod_mun = \"$municipio\" and codigo_inst in (select cod_inst from sedes$periodoActual where (tipo_validacion = \"$validacion\" or tipo_validacion = \"Lector de Huella\" ) and cod_mun_sede = \"$municipio\") ";

if($institucionRector != ""){
	$consulta.= " and codigo_inst = \"$institucionRector\" ";
}

$consulta = $consulta." order by nom_inst asc ";

// echo "<br><br>$consulta<br><br>";

$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
		$respuesta = 1;
		while($row = $resultado->fetch_assoc()){
				
				$id = $row["codigo_inst"];
				$valor = $row["nom_inst"];
				
				$opciones .= "<option value=\"$id\"";
				if($municipio == $id){
						$opciones .= " selected ";
				}
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