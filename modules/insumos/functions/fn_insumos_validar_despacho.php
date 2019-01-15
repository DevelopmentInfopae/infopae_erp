<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

$mesesNom = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$sedes = json_decode($_POST['sedes']);
$meses = json_decode($_POST['meses']);
$productos = json_decode($_POST['productos']);

$condicional = "";

foreach ($productos as $key => $producto) {
	$condicional.= " OR CodigoProducto = '".$producto."' ";
}

$condicional = trim($condicional, " OR");

$valida = 0;

$tablaErr = "";

$cantEstudiantes = 0;

foreach ($meses as $key => $mes) {
	$tabla = "insumosmovdet".$mes.$_SESSION['periodoActual'];
	foreach ($sedes as $key => $sede) {

		$consultaSede = "SELECT MAX(sedes_cobertura.cant_Estudiantes) as cant_Estudiantes, sede.* FROM sedes".$_SESSION['periodoActual']." AS sede
					INNER JOIN sedes_cobertura ON sedes_cobertura.cod_sede = sede.cod_sede
			 AND sede.cod_sede = '".$sede."' GROUP BY sede.cod_sede";
		$resultadoSede = $Link->query($consultaSede);
		if ($resultadoSede->num_rows > 0) {
			if ($sedeN = $resultadoSede->fetch_assoc()) {
				$sedeNombre = $sedeN['nom_sede'];
				$manipuladoras = $sedeN['cantidad_Manipuladora'];
				$cantEstudiantes = $sedeN['cant_Estudiantes'];
			}
		} else {
			$valida++;
			$tablaErr.="Error al obtener información de sedes.";
		}

		$consulta = "SELECT * FROM $tabla WHERE BodegaDestino = '".$sede."' AND (".$condicional.")";
		if ($resultado = $Link->query($consulta)) {
			if ($resultado->num_rows > 0) {
				$valida++;
				$tablaErr.="</br><strong>N° ".$valida." </strong> Mes : ".$mesesNom[$mes].", Sede : ".$sedeNombre.".";
			} else {
				if ($manipuladoras == 0 || $manipuladoras == "") {
					$valida++;
					$tablaErr.="</br>Sede <strong>".$sedeNombre."</strong> sin manipuladoras registradas.";
				}
				// echo "Sede : ".$sede." CantEstudi : ".$cantEstudiantes."\n";
				if ($cantEstudiantes == 0) {
					$valida++;
					$tablaErr.="</br>Sede <strong>".$sedeNombre."</strong> sin estudiantes registrados.";
				}
			}
		} else {
			if ($manipuladoras == 0 || $manipuladoras == "") {
				$valida++;
				$tablaErr.="</br>Sede <strong>".$sedeNombre."</strong> sin manipuladoras registradas.";
			}
			// echo "Sede : ".$sede." CantEstudi : ".$cantEstudiantes."\n";
			if ($cantEstudiantes == 0) {
				$valida++;
				$tablaErr.="</br>Sede <strong>".$sedeNombre."</strong> sin estudiantes registrados.";
			}
		}
	}
}

// echo "valida : ".$valida;

if ($valida > 0) {
	echo '{"respuesta" : [{"respuesta" : "1", "coincide" : "'.$tablaErr.'"}]}';
} else {
	echo '{"respuesta" : [{"respuesta" : "0"}]}';
}

