<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';
// exit(var_dump($_POST));

$iddet = $_POST['iddet'];
$numdoc = $_POST['num_doc'];
$mestabla = $_POST['mestabla'];
$tabla = "insumosmovdet".$mestabla.$_SESSION['periodoActual'];
$insmov = "insumosmov".$mestabla.$_SESSION['periodoActual'];

$consNumDets = "SELECT * FROM $tabla WHERE Numero = '$numdoc'";
$resNumDets = $Link->query($consNumDets);
$numDets = $resNumDets->num_rows;

if ($numDets > 1) {
	$delete = "DELETE FROM $tabla WHERE Id = '$iddet' AND Numero = '$numdoc'";
	if ($Link->query($delete)===true) {
		echo "1";
	} else {
		echo $delete;
	}
} else {
	$deleteDespacho = "DELETE FROM $insmov WHERE Numero  = '".$numdoc."'";
	if ($Link->query($deleteDespacho)===true) {
		$deleteDespachoDet = "DELETE FROM $tabla WHERE Numero  = '".$numdoc."'";
		if ($Link->query($deleteDespachoDet)===true) {
			echo "1";
		} else {
			echo $deleteDespachoDet;
		}
	} else {
		echo $deleteDespacho;
	}
}
