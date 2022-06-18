<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$registros = 0;

$periodoActual = mysqli_real_escape_string($Link, $_SESSION['periodoActual']);
$mes = (isset($_POST['mes']) && $_POST['mes'] != '') ? mysqli_real_escape_string($Link, $_POST["mes"]) : "";
$sede = (isset($_POST['sede']) && $_POST['sede'] != '') ? mysqli_real_escape_string($Link, $_POST["sede"]) : "";
$semanas = (isset($_POST['semanas']) && $_POST['semanas'] != '') ? $_POST["semanas"] : "";

$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
$consultaComplementos = "SELECT CODIGO, ID FROM tipo_complemento ORDER BY CODIGO ";
$respuestaComplementos = $Link->query($consultaComplementos) or die (mysqli_error($Link));
if ($respuestaComplementos->num_rows > 0) {
	while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
		$complementos[$dataComplementos['CODIGO']] = $dataComplementos['CODIGO'];
		$com[] = $dataComplementos['CODIGO'];
	}
}

$semana = $semanas[0];
$consulta = "SELECT * FROM priorizacion$semana WHERE cod_sede = '$sede'";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$respuesta = [];
	while($row = $resultado->fetch_assoc()){
		$respuesta['registros'] = ++$registros;
		$respuesta['cantEstudiantes'] = $row['cant_Estudiantes'];
		$respuesta['numEstFocalizados'] = $row['num_est_focalizados'];
		$respuesta['cantGruposEtarios'] = $cantGruposEtarios;
		$respuesta['complementos'] = $com;
		foreach ($complementos as $key => $value) {
			if (isset($row[$key])) {
				$respuesta[$key] = $row[$key];
				for ($i=1; $i <= $cantGruposEtarios ; $i++) { 
					$respuesta[$key.$i] = $row['Etario'.$i.'_'.$key];
				}
			}
		}
	}
}

echo json_encode($respuesta);