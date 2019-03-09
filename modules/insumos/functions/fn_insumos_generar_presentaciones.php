<?php
function calcularPresentaciones($cantDespacho, $codInsumo, $Link){
	$auxAlimento = [];
	// echo "\n Cantidad : ".$cantDespacho."\n";
	$cantidadNecesaria = $cantDespacho/1000;

$consulta = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo = '".$codInsumo."'";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	if ($insumo = $resultado->fetch_assoc()) {
		$nombre = $insumo['NombreUnidad2'];
		$auxAlimento['factor'] = $insumo['CantidadUnd2']/$insumo['CantidadUnd2'];
		$auxAlimento['cantidadPresentacion'] = 1/$insumo['CantidadUnd2'];
		$auxAlimento['CantidadUnd2'] = $insumo['CantidadUnd2']/1000;
		$auxAlimento['CantidadUnd3'] = $insumo['CantidadUnd3']/1000;
		$auxAlimento['CantidadUnd4'] = $insumo['CantidadUnd4']/1000;
		$auxAlimento['CantidadUnd5'] = $insumo['CantidadUnd5']/1000;
	}
}


		$valor2 = '';
		$valor3 = '';
		$valor4 = '';
		$valor5 = '';
		$necesario2 = 0;
		$necesario3 = 0;
		$necesario4 = 0;
		$necesario5 = 0;
		$tomado2 = 0;
		$tomado3 = 0;
		$tomado4 = 0;
		$tomado5 = 0;
		$tomadoTotal = 0;

if($auxAlimento['CantidadUnd3'] > 0){

	//echo "\nCantidad: $cantidadNecesaria";

	//Unidad 2
	//$valor2 = $auxAlimento['CantidadUnd3'] /  $auxAlimento['cantidadPresentacion'];

	$valor2 = $auxAlimento['factor'] / $auxAlimento['cantidadPresentacion'];
	$necesario = $cantidadNecesaria;
	$necesarioFactor = $cantidadNecesaria / $auxAlimento['cantidadPresentacion'];
	$necesarioEntera = intval($necesario);
	$necesario2 = $necesarioEntera;
	$tomado = $necesarioEntera * ($auxAlimento['factor'] / $auxAlimento['cantidadPresentacion']);
	$tomado2 = $tomado;
	$saldo = intval($necesarioFactor) - intval($tomado);
	$tomadoTotal = $tomadoTotal + $tomado;

	if( $saldo > 0 && $auxAlimento['CantidadUnd3'] == 0){
		$necesario2 = $necesario2 + 1;
		$tomado = 1 * ($auxAlimento['factor'] / $auxAlimento['cantidadPresentacion']);
		$tomado2 = $tomado2+$tomado;
		$saldo = 0;
		$tomadoTotal = $tomadoTotal + $tomado;
	}

	// echo "\nNecesario Factor: $necesarioFactor";
	// echo "\nNecesario: $necesario";
	// echo "\nEntera: $necesarioEntera";
	// echo "\nTomado: $tomado";
	// echo "\nSaldo: $saldo";
	// echo "\nCantidad: $necesario2";

	// Unidad 3
	$valor3 = $auxAlimento['CantidadUnd3'] /  $auxAlimento['cantidadPresentacion'];
	$valor = $valor3;
	$necesario = $saldo / $valor;
	$necesarioEntera = intval($necesario);
	$necesario3 = $necesarioEntera;
	$tomado = $necesarioEntera  * $valor;
	$tomado3 = $tomado;
	$saldo = intval($saldo) - intval($tomado);
	$tomadoTotal = $tomadoTotal + $tomado;
	if($necesario < 1 && $auxAlimento['CantidadUnd4'] == 0){
		$necesario3 = $necesario3 + 1;
		$saldo = 0;
		$tomado = $valor;
		$tomado3 = $tomado3+$tomado;
		$tomadoTotal = $tomadoTotal + $tomado;
	}
	// echo "\nValor: $valor";
	// echo "\nNecesario: $necesario";
	// echo "\nEntera: $necesarioEntera";
	// echo "\nTomado: $tomado";
	// echo "\nSaldo: $saldo";
	// echo "\nCantidad: $necesario3";
	// echo "\n";


	// Unidad 4
	if($auxAlimento['CantidadUnd4'] > 0  ){
		$valor4 = $auxAlimento['CantidadUnd4'] /  $auxAlimento['cantidadPresentacion'];
		$valor = $valor4;
		$necesario = $saldo / $valor;
		$necesarioEntera = intval($necesario);
		$necesario4 = $necesarioEntera;
		$tomado = $necesarioEntera  * $valor;
		$tomado4 = $tomado;

		if($saldo < 1 && $auxAlimento['CantidadUnd5'] == 0){
			$necesario4 = $necesario4 + 1;
			$saldo = 0;
			$tomado = $valor;
			$tomado4 = $tomado4+$tomado;
		}else{
			$saldo = intval($saldo) - intval($tomado);
		}
		$tomadoTotal = $tomadoTotal + $tomado;


		if( $saldo > 0 && $auxAlimento['CantidadUnd5'] == 0 ){
			$necesario4 = $necesario4 + 1;
			$tomado = 1 * ($auxAlimento['CantidadUnd4'] / $auxAlimento['cantidadPresentacion']);
			$tomado4 = $tomado4+$tomado;
			$saldo = 0;
			$tomadoTotal = $tomadoTotal + $tomado;
		}

		// echo "\nValor: $valor";
		// echo "\nNecesario: $necesario";
		// echo "\nEntera: $necesarioEntera";
		// echo "\nTomado: $tomado";
		// echo "\nSaldo: $saldo";
		// echo "\nCantidad: $necesario4";
		// echo "\n";
	}

	// Unidad 5
	if($auxAlimento['CantidadUnd5'] > 0  ){
		$valor5 = $auxAlimento['CantidadUnd5'] /  $auxAlimento['cantidadPresentacion'];
		$valor = $valor5;
		$necesario = $saldo / $valor;
		$necesarioEntera = intval($necesario);
		$necesario5 = $necesario5 + $necesarioEntera;
		$tomado = $necesarioEntera  * $valor;
		$tomado5 = $tomado;
		$saldo = intval($saldo) - intval($tomado);
		$necesario = $saldo / $valor;
		$tomadoTotal = $tomadoTotal + $tomado;


		if( $saldo > 0 && $necesario < 1){
			$necesario5 = $necesario5 + 1;
			$saldo = 0;
			$tomado = $valor;
			$tomado5 = $tomado5+$tomado;
			$tomadoTotal = $tomadoTotal + $tomado;
		}



		// echo "\nValor: $valor";
		// echo "\nNecesario: $necesario";
		// echo "\nEntera: $necesarioEntera";
		// echo "\nTomado: $tomado";
		// echo "\nSaldo: $saldo";
		// echo "\nCantidad: $necesario5";
		// echo "\n";
	}

	// Se va a revizar las cantidades de las presentaciones de abajo hacia arriba para el caso de 2 o mas presentaciones
	// pequeñas que tienen el mismo contenido de una presentación mayor.

	// echo "\nResumen:";
	// echo "\nUnidad 2 - $valor2 - $necesario2";
	// echo "\nUnidad 3 - $valor3 - $necesario3";
	// echo "\nUnidad 4 - $valor4 - $necesario4";
	// echo "\nUnidad 5 - $valor5 - $necesario5";

	// Iniciamos con la 5 por que es la unidad más pequeña.
	//Comparando la variable 5 con las anteriores
	if($valor5 != '' && $necesario5 != 0  && $valor5 > 0){
		$restar = 0;

		$aux = $valor5 * $necesario5;
		$aux = intval($aux/$valor2);
		$necesario2 = $necesario2 + $aux;
		$tomadoTotal = $tomadoTotal + ($valor2 * $aux);
		$necesario5 = (intval($valor5 * $necesario5) - intval($aux * $valor2)) / $valor5;
		if ($necesario5 < 1) {
			$tomadoTotal = $tomadoTotal - $tomado5;
			$necesario5 = 0;
		}

		$aux = $valor5 * $necesario5;
		$aux = intval($aux/$valor3);
		$necesario3 = $necesario3 + $aux;
		$tomadoTotal = $tomadoTotal + ($valor3 * $aux);
		$necesario5 = (intval($valor5 * $necesario5) - intval($aux * $valor3)) / $valor5;
		if ($necesario5 < 1) {
			$tomadoTotal = $tomadoTotal - $tomado5;
			$necesario5 = 0;
		}

		$aux = $valor5 * $necesario5;
		$aux = intval($aux/$valor4);
		$necesario4 = $necesario4 + $aux;
		$tomadoTotal = $tomadoTotal + ($valor4 * $aux);
		$necesario5 = (intval($valor5 * $necesario5) - intval($aux * $valor4)) / $valor5;

		if ($necesario5 < 1) {
			$tomadoTotal = $tomadoTotal - $tomado5;
			$necesario5 = 0;
		}

	}
	//Comparando la variable 4 con las anteriores
	if($valor4 != '' && $necesario4 != 0 && $valor4 > 0){

		$aux = $valor4 * $necesario4;
		$aux = intval($aux/$valor2);
		$necesario2 = $necesario2 + $aux;
		$necesario4 = (intval($valor4 * $necesario4) - intval($aux * $valor2)) / $valor4;
		if ($necesario4 < 1) {
			$tomadoTotal = $tomadoTotal - $tomado4;
			$necesario4 = 0;
		}

		$aux = $valor4 * $necesario4;
		$aux = intval($aux/$valor3);
		$necesario3 = $necesario3 + $aux;
		$necesario4 = (intval($valor4 * $necesario4) - intval($aux * $valor3)) / $valor4;
		if ($necesario4 < 1) {
			$tomadoTotal = $tomadoTotal - $tomado4;
			$necesario4 = 0;
		}

	}



	//Comparando la variable 3 con las anteriores
	if($valor3 != '' && $necesario3 != 0 && $valor3 > 0){

		$aux = $valor3 * $necesario3;
		$aux = intval($aux/$valor2);
		$necesario2 = $necesario2 + $aux;
		$necesario3 = (intval($valor3 * $necesario3) - intval($aux * $valor2)) / $valor3;
		if ($necesario3 < 1) {
			$tomadoTotal = $tomadoTotal - $tomado3;
			$necesario3 = 0;
		}

	}


	// echo "\nNuevo Resumen:";
	// echo "\nUnidad 2 - $valor2 - $necesario2";
	// echo "\nUnidad 3 - $valor3 - $necesario3";
	// echo "\nUnidad 4 - $valor4 - $necesario4";
	// echo "\nUnidad 5 - $valor5 - $necesario5";
	// echo "\n\n";

	$tomadoTotal = $tomadoTotal /  ($auxAlimento['factor'] / $auxAlimento['cantidadPresentacion']);

	$presentaciones[1] = $necesario2;
	$presentaciones[2] = $necesario3;
	$presentaciones[3] = $necesario4;
	$presentaciones[4] = $necesario5;
	$presentaciones[5] = $tomadoTotal;
	
} else {


	if (strpos($nombre, " kg") || strpos($nombre, " lt")) {
		$factorC = 1000;
	} else if (strpos($nombre, " lb")) {
		$factorC = 500;
	} else if (strpos($nombre, " g") || $nombre == "g" || strpos($nombre, " cc") || $nombre == "cc") {
		$factorC = 1;
	} else {
		$factorC = 1;
	}

	// $cantidadNecesaria = $cantidadNecesaria * $factorC;
	// $desp =  $cantidadNecesaria/($auxAlimento['CantidadUnd2']*$factorC);
	// $necesario = ceil($desp);

	// $total = $necesario * $auxAlimento['CantidadUnd2'];

	$cantidadNecesaria = ($cantidadNecesaria < 1 ? $cantidadNecesaria * 1000 : $cantidadNecesaria);
	$cantidadNecesaria = ceil($cantidadNecesaria);

	$presentaciones[1] = $cantidadNecesaria;
	$presentaciones[2] = 0;
	$presentaciones[3] = 0;
	$presentaciones[4] = 0;
	$presentaciones[5] = $cantidadNecesaria;
}

	return $presentaciones;
//Termina el calculo de las presentaciones
}