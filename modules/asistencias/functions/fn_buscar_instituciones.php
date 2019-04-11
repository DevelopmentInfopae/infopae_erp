<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$municipio = '';
if(isset($_POST['municipio']) && $_POST['municipio'] != ''){
		$municipio = $_POST['municipio'];
}
$opciones = "<option value=\"\">Seleccione uno</option>";

$consulta = " select * from instituciones where cod_mun = \"$municipio\" ";
$consulta = $consulta." order by nom_inst asc ";

// echo $consulta;

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