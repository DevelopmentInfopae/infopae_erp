<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

$periodoActual = $_SESSION['periodoActual'];
$sede = json_decode($_POST['sedes']);
$productos = json_decode($_POST['productos']);
$mes = $_POST['mes'];
$complemento = $_POST['complemento'];

$tablaErr = "";
// validacion insumos contados por manipuladoras
$codigoProductoManipuladora = '0502';
foreach ($productos as $key => $producto) {
	$conteo = substr($producto, 0, 4);
	if ($conteo == $codigoProductoManipuladora) {
		if ($complemento == 'Total cobertura') {
			$consultaCantidadManipuladoras = " SELECT cantidad_Manipuladora AS manipuladoras , nom_sede AS nomSede FROM sedes$periodoActual WHERE cod_sede = $sede ";
		}else {
			$consultaCantidadManipuladoras = " SELECT Manipuladora_$complemento AS manipuladoras , nom_sede AS nomSede FROM sedes$periodoActual WHERE cod_sede = $sede ";
		}
		// exit(var_dump($consultaCantidadManipuladoras));
		$respuestaCantidadManipuladoras = $Link->query($consultaCantidadManipuladoras) or die ('Error al consultar las manipuladoras' . mysqli_error($Link));
		if ($respuestaCantidadManipuladoras->num_rows) {
			$dataCantidadManipuladoras = $respuestaCantidadManipuladoras->fetch_assoc();
			$manipuladoras = $dataCantidadManipuladoras['manipuladoras'];
			$nomSede = $dataCantidadManipuladoras['nomSede'];
			if ($manipuladoras == "0") {
				$tablaErr.="</br>Sede <strong>".$nomSede."</strong> sin manipuladoras registradas en el complemento. <strong>" .$complemento. "</strong>" ;
			}
		}
	}
}
if ($tablaErr != "") {
	echo '{"respuesta" : [{"respuesta" : "0", "coincide" : "'.$tablaErr.'"}]}';
}else if ($tablaErr == "") {
	echo '{"respuesta" : [{"respuesta" : "1", "coincide" : "'.$tablaErr.'"}]}';
}

