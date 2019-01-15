<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';
require_once 'fn_insumos_generar_presentaciones.php';

function obtenerNumDoc($tabla, $Link){
	$consulta = "SELECT Numero FROM ".$tabla." ORDER BY Numero DESC LIMIT 1";
	$resultado = $Link->query($consulta);
	if ($resultado->num_rows > 0) {
		if ($num = $resultado->fetch_assoc()) {
			$numDoc = $num['Numero'];
			$numDoc = $numDoc+1;
		}
		return $numDoc;
	} else {
		return "1";
	}
}

function calcularCantidad($cins, $sede, $Link){

	$consultaParametro = "SELECT CantidadCupos FROM parametros";
	$resultadoParametro = $Link->query($consultaParametro);
	if ($resultadoParametro->num_rows > 0) {
		if ($parametro = $resultadoParametro->fetch_assoc()) {
			$cantCuposCalcular = $parametro['CantidadCupos'];
		}
	} else {
		$cantCuposCalcular = 100;
	}


	$datos = datosProducto($cins, $sede, $Link);
	$cantxMes = $datos['cantxMes'];

	if (strpos($datos['uMedida2'], " kg") || strpos($datos['uMedida2'], " lt")) {
		$cantxMes = $cantxMes * 1000;
	} else if (strpos($datos['uMedida2'], " lb")) {
		$cantxMes = $cantxMes * 500;
	}

	$conteoIns = substr($cins, 2, 2);
	if ($conteoIns == "01") { //cupos
		$consultaCupos = "SELECT MAX(cant_Estudiantes) AS Cupos FROM sedes_cobertura WHERE cod_sede = '".$sede."'";
		$resultadoCupos = $Link->query($consultaCupos);
		if ($resultadoCupos->num_rows > 0) {
			if ($cuposInf = $resultadoCupos->fetch_assoc()) {
				$cupos = $cuposInf['Cupos'];
			}
			$cantidad = ($cupos / $cantCuposCalcular) * $cantxMes;
		}
	} else if ($conteoIns == "02") {//manipuladores
		$consultaManipuladores = "SELECT cantidad_Manipuladora AS manipuladores FROM sedes".$_SESSION['periodoActual']." WHERE cod_sede = '".$sede."'";
		$resultadoManipuladores = $Link->query($consultaManipuladores);
		if ($resultadoManipuladores->num_rows > 0) {
			if ($manipInf = $resultadoManipuladores->fetch_assoc()) {
				$manipuladores = $manipInf['manipuladores'];
			}
			$cantidad = $cantxMes * $manipuladores;
		}
	} else if ($conteoIns == "03") {//individual
		$cantidad = $cantxMes;
	}
	$presentaciones = calcularPresentaciones($cantidad, $cins, $Link);
	$presentaciones[6] = $cantidad;
	return $presentaciones;

}


function datosProducto($cins, $sede, $Link){

	$datosP = [];
	$consProducto = "SELECT FORMAT(CantidadUnd1, 0) AS CantidadUnd1, NombreUnidad1, NombreUnidad2, CantidadUnd2 FROM productos".$_SESSION['periodoActual']." WHERE Codigo = '".$cins."'";
	$resultado = $Link->query($consProducto);
	if ($resultado->num_rows > 0) {
		if ($producto = $resultado->fetch_assoc()) {
			$datosP['cantxMes'] = str_replace(",", "", $producto['CantidadUnd1']);
			$datosP['uMedida'] = $producto['NombreUnidad1'];
			$datosP['uMedida2'] = $producto['NombreUnidad2'];
			$datosP['cantUMedida'] = $producto['CantidadUnd2'];
		}
	}

	return $datosP;
}

if (isset($_POST['idDespacho'])) {
	$idDespacho = $_POST['idDespacho'];
} else {
	$idDespacho = "";
}

if (isset($_POST['numDoc'])) {
	$numDoc = $_POST['numDoc'];
} else {
	$numDoc = "";
}

if (isset($_POST['tipo_despacho'])) {
	$tipo_despacho = $_POST['tipo_despacho'];
} else {
	$tipo_despacho = "";
}

if (isset($_POST['nomTipoMov'])) {
	$nomTipoMov = $_POST['nomTipoMov'];
} else {
	$nomTipoMov = "";
}

if (isset($_POST['proveedor'])) {
	$proveedor = $_POST['proveedor'];
} else {
	$proveedor = "";
}

if (isset($_POST['nombre_proveedor'])) {
	$nombre_proveedor = $_POST['nombre_proveedor'];
} else {
	$nombre_proveedor = "";
}

if (isset($_POST['municipio_desp'])) {
	$municipio_desp = $_POST['municipio_desp'];
} else {
	$municipio_desp = "";
}

// if (isset($_POST['institucion_desp'])) {
// 	$institucion_desp = $_POST['institucion_desp'];
// } else {
// 	$institucion_desp = "";
// }

// if (isset($_POST['sede_desp'])) {
// 	$sede_desp = $_POST['sede_desp'];
// } else {
// 	$sede_desp = "";
// }

if (isset($_POST['ruta_desp'])) {
	$ruta_desp = $_POST['ruta_desp'];
} else {
	$ruta_desp = "";
}

if (isset($_POST['sede'])) { //sedes seleccionadas a despachar
	$sedes = $_POST['sede'];
} else {
	$sedes = "";
}

if (isset($_POST['productoDespacho'])) {
	$productoDespacho = $_POST['productoDespacho'];
} else {
	$productoDespacho = "";
}

if (isset($_POST['DescInsumo'])) {
	$DescInsumo = $_POST['DescInsumo'];
} else {
	$DescInsumo = "";
}

if (isset($_POST['idDetDespacho'])) {
	$idDetDespacho = $_POST['idDetDespacho'];
} else {
	$idDetDespacho = "";
}

if (isset($_POST['meses_despachar'])) {
	$meses_despachar = $_POST['meses_despachar'];
} else {
	$meses_despachar = "";
}

if (isset($_POST['bodega_origen'])) {
	$bodega_origen = $_POST['bodega_origen'];
} else {
	$bodega_origen = "";
}

if (isset($_POST['tipo_transporte'])) {
	$tipo_transporte = $_POST['tipo_transporte'];
} else {
	$tipo_transporte = "";
}

if (isset($_POST['placa_vehiculo'])) {
	$placa_vehiculo = $_POST['placa_vehiculo'];
} else {
	$placa_vehiculo = "";
}

if (isset($_POST['conductor'])) {
	$conductor = $_POST['conductor'];
} else {
	$conductor = "";
}

$consultarResponsable = "SELECT * FROM usuarios WHERE id = ".$_SESSION['idUsuario'];
$resultadoResponsable = $Link->query($consultarResponsable);
if ($resultadoResponsable->num_rows > 0) {
	if ($responsable = $resultadoResponsable->fetch_assoc()) {
		$nombreResp = $responsable['nombre'];
		$loginResp = $responsable['email'];
	}
}

$error = 0;
$descError = "";

$validaTablas = 0;
$validaProductos = 0;

foreach ($meses_despachar as $key => $mes) {

	$insumosmov = "insumosmov".$mes.$_SESSION['periodoActual'];

	$updateinsumosmov ="UPDATE $insumosmov SET Tipo = '".$nomTipoMov."', BodegaOrigen = '".$bodega_origen."', BodegaDestino = '".$sedes[0]."', Nombre = '".$nombre_proveedor."', Nitcc = '".$proveedor."', TipoTransporte = '".$tipo_transporte."', Placa = '".$placa_vehiculo."', ResponsableRecibe = '".$conductor."' WHERE Id = '".$idDespacho."' ; ";

	if ($Link->query($updateinsumosmov)===true) {
		$validaProductos++;
	} else {
		exit(" Error ".$updateinsumosmov);
	}

	$insumosmovdet = "insumosmovdet".$mes.$_SESSION['periodoActual'];

	foreach ($idDetDespacho as $key => $det) {
			$datos = datosProducto($productoDespacho[$key], $sedes[0], $Link);
			$presentaciones = calcularCantidad($productoDespacho[$key], $sedes[0], $Link);
				$updateinsumosmovdet ="UPDATE $insumosmovdet SET CodigoProducto = '".$productoDespacho[$key]."', Descripcion = '".$DescInsumo[$key]."', Cantidad = '".$presentaciones[6]."', BodegaOrigen = '".$bodega_origen."', BodegaDestino = '".$sedes[0]."', Umedida = '".$datos['uMedida2']."', CantUmedida = '".$datos['cantUMedida']."', CantU2 = '".$presentaciones[1]."', CantU3 = '".$presentaciones[2]."', CantU4 = '".$presentaciones[3]."', CantU5 = '".$presentaciones[4]."', CanTotalPresentacion = '".$presentaciones[5]."' WHERE Id = '".$det."';";

			if ($Link->query($updateinsumosmovdet)===true) {
				$validaProductos++;
			} else {
				exit(" Error ".$updateinsumosmovdet);
			}
	}

	if (sizeof($productoDespacho) > sizeof($idDetDespacho)) {
		$insertinsumosmovdet = "INSERT INTO $insumosmovdet (Documento, Numero, Item, CodigoProducto, Descripcion, Cantidad, BodegaOrigen, BodegaDestino, Id, Umedida, CantUmedida, Factor, Id_Usuario, CantU2, CantU3, CantU4, CantU5, CanTotalPresentacion) VALUES ";
		for ($i=sizeof($idDetDespacho); $i < sizeof($productoDespacho); $i++) { 
			$datos = datosProducto($productoDespacho[$i], $sedes[0], $Link);
			$presentaciones = calcularCantidad($productoDespacho[$i], $sedes[0], $Link);
			$insertinsumosmovdet.="('DESI', '".$numDoc."', '0', '".$productoDespacho[$i]."', '".$DescInsumo[$i]."', '".$presentaciones[6]."', '".$bodega_origen."', '".$sedes[0]."', '', '".$datos['uMedida2']."', '".$datos['cantUMedida']."', '1', '".$_SESSION['idUsuario']."', '".$presentaciones[1]."', '".$presentaciones[2]."', '".$presentaciones[3]."', '".$presentaciones[4]."', '".$presentaciones[5]."'), ";
		}

		$insertinsumosmovdet=trim($insertinsumosmovdet, ", ");

		if ($Link->query($insertinsumosmovdet)===true) {
			$validaProductos++;
		} else {
			exit(" Error ".$insertinsumosmovdet);
		}
	}

}


if ($validaProductos > 0) {

	$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '55', 'Actualizó los datos del despacho de Insumos con número : <strong>".$numDoc."</strong> ')";
	$Link->query($sqlBitacora);

	echo "1";
} else {
	echo "0";
}

