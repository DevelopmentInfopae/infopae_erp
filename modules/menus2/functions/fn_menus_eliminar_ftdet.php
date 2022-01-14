<?php 

require_once '../../../db/conexion.php';
require_once '../../../config.php';

$idftd = $_POST['idftd'];
$idproducto = $_POST['idproducto'];

function tablaPorMeses($tabla){
	global $Link;
	$tablaMeses = array();
	for ($i=date('m'); $i >= 1 ; $i--) { 
	  if (strlen($i) == 1) {
	    $mes = "0".$i;
	  } else {
	    $mes = $i;
	  }
	   $tablaDespMes = "SHOW TABLES LIKE '".$tabla.$mes.$_SESSION['periodoActual']."'";
	   $resultado = $Link->query($tablaDespMes);
	   if ($resultado->num_rows > 0) {
	     $tablaMeses[] = $tabla.$mes.$_SESSION['periodoActual'];
	   }
	}
	return $tablaMeses;
}

function verificarDespachoMenu($tipoComplemento, $ordenCiclo){
	global $Link;
	$mesesDespachos = tablaPorMeses('despachos_enc');
	$valida = 0;
	for ($i=0; $i < sizeof($mesesDespachos); $i++) { 
		unset($resultado);
		$sql = "SELECT Tipo_Complem, Menus FROM ".$mesesDespachos[$i]." WHERE Tipo_Complem = '".$tipoComplemento."' AND Menus like '%".$ordenCiclo."%'";
		$resultado = $Link->query($sql);
		if ($resultado->num_rows > 0) {
			$valida++;
		}
	}
	if ($valida > 0) {
		return true;
	} else if ($valida == 0){
		return false;
	}
}

if (substr($idproducto, 0, 2) == "02") {
	$valida = 0;
	$consultarMenus = "SELECT productos".$_SESSION['periodoActual'].".Cod_Tipo_complemento, productos".$_SESSION['periodoActual'].".Orden_Ciclo, productos".$_SESSION['periodoActual'].".Descripcion FROM productos".$_SESSION['periodoActual'].", fichatecnica, fichatecnicadet WHERE fichatecnicadet.codigo = ".$idproducto." AND fichatecnicadet.IdFT = fichatecnica.Id AND productos".$_SESSION['periodoActual'].".Codigo = fichatecnica.Codigo";
	$resultadoMenus = $Link->query($consultarMenus);
	if ($resultadoMenus->num_rows > 0) {
		while ($menu = $resultadoMenus->fetch_assoc()) {
			if (verificarDespachoMenu($menu['Cod_Tipo_complemento'], $menu['Orden_Ciclo'])) {
				$valida++;
			}
		}
		if ($valida == 0) {
			$eliminarFTDET = "DELETE FROM fichatecnicadet WHERE Id = ".$idftd;
			if ($Link->query($eliminarFTDET)===true) {
				echo "1";
			} else {
				echo "Error al eliminar ftdet : ".$eliminarFTDET;
			}
		} else {
			echo "0";
		}
	} else {
		$eliminarFTDET = "DELETE FROM fichatecnicadet WHERE Id = ".$idftd;
		if ($Link->query($eliminarFTDET)===true) {
			echo "1";
		} else {
			echo "Error al eliminar ftdet : ".$eliminarFTDET;
		}
	}
} else if (substr($idproducto, 0, 2) == "01") {

	$obtOrdCiclo = "SELECT Orden_Ciclo, Cod_Tipo_complemento FROM productos".$_SESSION['periodoActual']." WHERE Codigo = ".$idproducto;
	$resOrdCiclo = $Link->query($obtOrdCiclo);
	if ($resOrdCiclo->num_rows>0) {
		if ($ordCiclo = $resOrdCiclo->fetch_assoc()) {
			$tipoComplemento = $ordCiclo['Orden_Ciclo'];
			$Orden_Ciclo = $ordCiclo['Orden_Ciclo'];
		}
	}


	if (verificarDespachoMenu($tipoComplemento, $Orden_Ciclo)) {
		echo "0";
	} else {
		$eliminarFTDET = "DELETE FROM fichatecnicadet WHERE Id = ".$idftd;
		if ($Link->query($eliminarFTDET)===true) {
			echo "1";
		} else {
			echo "Error al eliminar ftdet : ".$eliminarFTDET;
		}
	}
} else {
	echo $idproducto;
}

 ?>