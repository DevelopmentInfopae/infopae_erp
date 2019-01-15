<?php
require_once '../../../config.php';
require_once '../../../db/conexion.php';

$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
$periodoActual = $_SESSION['periodoActual'];
$opciones = "<option value=\"\">Seleccione una</option>";

$consulta = "SELECT s.cod_sede, s.nom_sede FROM sedes$periodoActual s WHERE s.cod_inst = $institucion ORDER BY s.nom_sede asc";
$resultado = $Link->query($consulta);
if($resultado->num_rows > 0){
	while($row = $resultado->fetch_assoc()) {
		$codigo = $row['cod_sede'];
		$nombre = $row['nom_sede'];
		$opciones .= " <option value=\"$codigo\">$nombre</option> ";
	}
	$respuestaAJAX = [
		"estado" => 1,
		"opciones" => $opciones,
		"mensaje" => "Instituciones cargados correctamente."
	];
}
echo json_encode($respuestaAJAX);
