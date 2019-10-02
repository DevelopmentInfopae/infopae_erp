<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? mysqli_real_escape_string($Link, $_POST["institucion"]) : "";
$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);

$log = "";
$respuesta = "<option value=\"\">Todos</option>";

// $consulta = " select distinct s.cod_sede, s.nom_sede from sedes$periodoActual s where s.cod_inst = '$institucion' order by s.nom_sede asc ";
$consulta = " SELECT DISTINCT distinct s.cod_sede, s.nom_sede FROM sedes_cobertura sc LEFT JOIN sedes$periodoActual s ON sc.cod_sede = s.cod_sede WHERE s.cod_inst = '$institucion' ORDER BY s.nom_sede ASC ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$codigo = $row['cod_sede'];
		$nombre = $row['nom_sede'];
		$respuesta .= " <option value=\"$codigo\">$nombre</option> ";
	}
}
echo json_encode(array("log"=>$log, "respuesta"=>$respuesta));
