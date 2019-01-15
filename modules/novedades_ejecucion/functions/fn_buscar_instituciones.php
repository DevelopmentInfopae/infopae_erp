<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$municipio = mysqli_real_escape_string($Link, $_POST["municipio"]);
$opciones = "<option value=\"\">Seleccione una</option>";

$consulta = "SELECT i.codigo_inst, i.nom_inst FROM instituciones i WHERE cod_mun = $municipio";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	while($row = $resultado->fetch_assoc()) {
		$codigo = $row['codigo_inst'];
		$nombre = $row['nom_inst'];
		$opciones .= " <option value=\"$codigo\">$nombre</option> ";
	}
	$respuestaAJAX = [
		"estado" => 1,
		"opciones" => $opciones,
		"mensaje" => "Instituciones cargados correctamente."
	];
}
echo json_encode($respuestaAJAX);
