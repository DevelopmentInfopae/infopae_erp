<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST["semana"]) : "";
$opciones = "<option value=\"\">Seleccione una</option>";
$respuestaAJAX = [
	"estado" => 0,
	"mensaje" => "No se encontró focalización para esta semana."
];

//Revisar si la tabla existencia
$resultado = $Link->query("show tables like 'focalizacion$semana'");
if($resultado->num_rows > 0){
	$consulta = "SELECT distinct f.Tipo_complemento FROM focalizacion$semana f ORDER BY Tipo_complemento asc";
	$resultado = $Link->query($consulta);
	if($resultado->num_rows > 0){
		while($row = $resultado->fetch_assoc()) {
			$tipoComplemento = $row['Tipo_complemento'];
			$opciones .= " <option value=\"$tipoComplemento\">$tipoComplemento</option> ";
		}
		$respuestaAJAX = [
			"estado" => 1,
			"opciones" => $opciones,
			"mensaje" => "Instituciones cargados correctamente."
		];
	}
}
echo json_encode($respuestaAJAX);
