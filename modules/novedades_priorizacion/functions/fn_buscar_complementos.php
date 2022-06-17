<?php
require_once '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
$consultaComplementos = "SELECT CODIGO, ID FROM tipo_complemento ORDER BY CODIGO ";
$respuestaComplementos = $Link->query($consultaComplementos) or die (mysqli_error($Link));
if ($respuestaComplementos->num_rows > 0) {
	while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
		$complementos[$dataComplementos['CODIGO']] = $dataComplementos['CODIGO'];
		$com[] = $dataComplementos['CODIGO'];
	}
}
$respuesta['cantGruposEtarios'] = $cantGruposEtarios;
$respuesta['complementos'] = $com;

echo json_encode($respuesta);