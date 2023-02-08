
<?php 

function buscar_dias_semanas($Link, $periodoActual){
	// segunda manera
	$consultaTablas = " SHOW TABLES LIKE 'entregas_res_%'";
   	$resTablas = $Link->query($consultaTablas);
   	if ($resTablas->num_rows > 0) {
   		while ($dataMeses = $resTablas->fetch_assoc()) {
			$aux = (array_values($dataMeses));
			$aux = substr($aux[0], 13, -2);
			$meses[] = $aux;
			$consDiasSemanas = " SELECT   GROUP_CONCAT(if( DIA < 10, if( LEFT(DIA,1) != '0', CONCAT( '0',DIA ), DIA ), DIA )) AS Dias, 
                              	MES, 
                              	SEMANA 
                           	FROM planilla_semanas 
                           	WHERE MES = '$aux' AND CONCAT(ANO, '-', MES_REAL, '-',  if( DIA < 10, if( LEFT(DIA,1) != '0', CONCAT( '0',DIA ), DIA ), DIA ) )<= '".date('Y-m-d')."'
                           	GROUP BY SEMANA";
			// exit(var_dump($consDiasSemanas));			 	
         	$resDiasSemanas = $Link->query($consDiasSemanas);
			if ($resDiasSemanas->num_rows > 0) {
				while ($dataDiasSemanas = $resDiasSemanas->fetch_assoc()) {
         			$semanaPos = $dataDiasSemanas['SEMANA'];
         			$arrDias = explode(",", $dataDiasSemanas['Dias']);
         			sort($arrDias);
         			$diasSemanas[$aux][$semanaPos] = $arrDias; //obtenemos un array ordenado del siguiente modo array[mes][semana] = array[dias]
				}
			}
   		}
	}
	return $diasSemanas;
}

function buscar_tipo_complementos($Link){
	$tipoComplementos = [];
	$consComplemento="SELECT * FROM tipo_complemento";
	$resComplemento = $Link->query($consComplemento);
	if ($resComplemento->num_rows > 0) {
		while ($Complemento = $resComplemento->fetch_assoc()) {
			$tipoComplementos[] = $Complemento['CODIGO'];
		}
	}
	return $tipoComplementos;
}

function buscar_meses_nombre( $mes ){
	$mesesNom = array(	'1' => "Enero", 
                  		"2" => "Febrero", 
                  		"3" => "Marzo", 
                  		"4" => "Abril", 
                  		"5" => "Mayo", 
                  		"6" => "Junio", 
                  		"7" => "Julio", 
                  		"8" => "Agosto", 
                  		"9" => "Septiembre", 
                  		"10" => "Octubre", 
                  		"11" => "Noviembre", 
                  		"12" => "Diciembre");
	return ($mesesNom[$mes]);
}
