<?php
function redondeoWappsi($valor){
	$frac  = $valor - (int) $valor;
	$valor = (int) $valor;
	if($frac >= 0.5){
		$valor++;
	}
	return $valor;
}
