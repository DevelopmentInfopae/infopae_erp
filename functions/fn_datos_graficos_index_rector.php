<?php
include '../config.php';
require_once '../db/conexion.php';
//var_dump($_POST);
// echo "<br><br><br><br><br><br>";

$actualizar = 0;
$timeOption = 1;
$codInst = 0;

// timeOption 1 = semana
// timeOption 2 = mes

if(isset($_POST['actualizar']) && $_POST['actualizar'] != ''){
	$actualizar = $_POST['actualizar'];
}

if(isset($_POST['timeOption']) && $_POST['timeOption'] != ''){
	$timeOption = $_POST['timeOption'];
}

if(isset($_POST['codInst']) && $_POST['codInst'] != ''){
	$codInst = $_POST['codInst'];
}



$barras = array();
$barrasTotalesMes = array();
$barrasTotalesSemana = array();
$entregas = array();
$entregasTotalesMes = array();
$entregasTotalesSemana = array();

$labelsSemanas = array();


$bandera = 0;









// La bandera la usamos para detectar si se encoontro el archivo de la consulta y en caso de que no, se actualiza los array y se genera el archivo.


if($timeOption == 1){
 	$rutaArchivo = "arrays_rector_semana.txt";
}else{
	$rutaArchivo = "arrays_rector_mes.txt";
}

if($actualizar == 0){
	$contenido = '';
	if($file = fopen($rutaArchivo, "r")){
		while(!feof($file)) {
			$contenido .=fgets($file);
		}
		fclose($file);
		if($contenido != ''){
			echo $contenido;
		}
		else{
			$bandera++;
		}
	}else{
		$bandera++;
	}
}

if($actualizar == 1 || $bandera > 0){
	$periodoActual = $_SESSION['periodoActual'];


	$Link = new mysqli($Hostname, $Username, $Password, $Database);
	if ($Link->connect_errno) {
	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$Link->set_charset("utf8");

	$consulta = " select ps.dia, ps.semana, ps.mes, ps.ano, (select sum(sc.num_est_focalizados) from sedes_cobertura sc where sc.semana = ps.semana and sc.cod_inst = '$codInst') as cantidad from planilla_semanas ps";






	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	$cantidadTotal = 0;
	$mesInicial = 0;

	$semanaInicial = 0;
	$semanaConsecutivo = 0;
	$totalSemana = 0;

	if($resultado->num_rows >= 1){
	    while($row = $resultado->fetch_assoc()){
	    	if($mesInicial != $row['mes'] && $mesInicial == 0 ){
	    		$mesInicial = $row['mes'];
	    	}else if($mesInicial != $row['mes'] && $mesInicial != 0 ){
	    		// Cuando se termina el mes guarda los valores
	    		$tiempo = intval($mesInicial);
	    		$cantidad = intval($cantidadTotal);
	    		unset($aux);
	    		$aux[] = $tiempo;
	    		$aux[] = $cantidad;
	    		$barrasTotalesMes[] = $aux;

	    		$cantidadTotal = 0;
	    		$mesInicial = $row['mes'];
	    	}
	    	// Tratamiento de semanas
	    	if($semanaInicial != $row['semana'] && $semanaInicial == 0 ){
	    		$semanaInicial = $row['semana'];
	    		$semanaConsecutivo++;
	    	}else if($semanaInicial != $row['semana'] && $semanaInicial != 0 ){
	    		// Cuando se termina la semana guarda los valores
	    		$tiempo = intval($semanaConsecutivo);
	    		$cantidad = intval($totalSemana);
	    		unset($aux);
	    		$aux[] = $tiempo;
	    		$aux[] = $cantidad;
	    		$barrasTotalesSemana[] = $aux;
	    		$labelsSemanas[] = array($semanaConsecutivo,$semanaInicial,true);
	    		$totalSemana = 0;
	    		$semanaInicial = $row['semana'];
	    		$semanaConsecutivo++;
	    	}
	    	// Mientras no se acabe el mes ni la semana, el va guardando cada día.
	    	$dia = $row['dia'];
	    	$mes = $row['mes'];
	    	$anno = $row['ano'];
	    	$tiempo = intval(strtotime("$anno-$mes-$dia")*1000);
	    	$cantidad = intval($row['cantidad']);
	    	$cantidadTotal = $cantidadTotal + $cantidad;
	    	$totalSemana = $totalSemana + $cantidad;
	    	unset($aux);
	    	$aux[] = $tiempo;
	    	$aux[] = $cantidad;
	    	$barras[] = $aux;
	    }
	}

	// Guarda el total de la ultima semana
	$tiempo = intval($semanaConsecutivo);
	$cantidad = intval($totalSemana);
	unset($aux);
	$aux[] = $tiempo;
	$aux[] = $cantidad;
	$barrasTotalesSemana[] = $aux;
	$labelsSemanas[] = array($semanaConsecutivo,$semanaInicial,true);

	// Guarda el total del ultimo mes
	$tiempo = intval($mesInicial);
	$cantidad = intval($cantidadTotal);
	unset($aux);
	$aux[] = $tiempo;
	$aux[] = $cantidad;
	$barrasTotalesMes[] = $aux;






	// ENTREGAS !!!!!!!!!!!!!!!!



	// Se va a recoger la información de planilla_dias para saber en que días se hicieron las entregas.
	$meses = array();
	$consulta = " select * from planilla_dias ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
	    while($row = $resultado->fetch_assoc()){
	    	$meses[] = $row;
	    }
	}

	// Se correra el array para validar hasta donde estan creadas las tablas de entregas res.
	$mesesEntregados = 0;
	foreach ($meses as $mes) {
		$mesConsulta = $mes['mes'];
		$consulta = " show tables like 'entregas_res_$mesConsulta$periodoActual' ";
		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
		if($resultado->num_rows >= 1){
			$mesesEntregados = $mes['mes'];
		}
		else{
			break;
		}
	}

	// var_dump($meses);
	// var_dump($mesesEntregados);

	$indiceDia = 1;
	$indiceSemana = 1;
	$semanaInicial = 0;
	$mesInicial = 0;
	$buscarEntregas = 0;
	$entregasTotalesMes = array();
	$entregasTotalesSemana = array();
	$cantidadTotalMes = 0;
	$cantidadTotalSemana = 0;
	$consulta = " select * from planilla_semanas where mes <= $mesesEntregados ";
	//echo "<br><br>".$consulta."<br><br>";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	if($resultado->num_rows >= 1){
		while($row = $resultado->fetch_assoc()){
			$semana = $row['SEMANA'];
			$mes = $row['MES'];
			if($mesInicial != $mes && $mesInicial == 0){
				$mesInicial = $mes;
				$buscarEntregas = 1;
			}
			else if($mesInicial != $mes && $mesInicial != 0){
				$entregasTotalesMes[] = array(intval($mesInicial),intval($cantidadTotalMes));
				$cantidadTotalMes = 0;
				$mesInicial = $mes;
				$buscarEntregas = 1;
				$indiceDia = 1;
			}
			if($semanaInicial != $semana && $semanaInicial == 0){
				$semanaInicial = $semana;
			}
			else if($semanaInicial != $semana && $semanaInicial != 0){
				$semanaInicial = $semana;
				$entregasTotalesSemana[] = array(intval($indiceSemana),intval($cantidadTotalSemana));
				$cantidadTotalSemana = 0;
				$indiceSemana++;
			}

			if($buscarEntregas == 1){
				// Cuando hay cambio de mes invocamos las entragas realizadas durante el mes
				$consulta2 = " select ";
				for ($i = 1 ; $i <= 22 ; $i++) {
					if($i > 1){
						$consulta2.= " , ";
					}
					$consulta2.= " sum(D$i) as D$i ";
				}
				$consulta2.= " from entregas_res_$mes$periodoActual ";
				if($codInst != 0){
					$consulta2 .= " where cod_inst = '$codInst' ";
				}
				//echo "<br><br>".$consulta2."<br><br>";
				$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
				if($resultado2->num_rows >= 1){
					$entregasMes = $resultado2->fetch_assoc();
				}
				$buscarEntregas = 0;
			}

			// Como ya se hizo la búsqueda de las entregas para los días del mes empezamos con la captura de los totales
			// para las semanas y los meses.
			//var_dump($entregasMes);
			$aux = 'D'.$indiceDia;
			$cantidadTotalMes = $cantidadTotalMes + $entregasMes[$aux];
			$cantidadTotalSemana = $cantidadTotalSemana + $entregasMes[$aux];
			$indiceDia++;
		}
	}
	$entregasTotalesMes[] = array(intval($mesInicial),intval($cantidadTotalMes));
	$entregasTotalesSemana[] = array(intval($indiceSemana),intval($cantidadTotalSemana));




	// Calculo de impresión de totales, se va a generar la cadena html para mostrar los totales.


    // Se va a recoger la información de planilla_dias para saber en que días se hicieron las entregas.
    $meses = array();
    $consulta = " select * from planilla_dias ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
        while($row = $resultado->fetch_assoc()){
            $meses[] = $row;
        }
    }

    // Se correra el array para validar hasta donde estan creadas las tablas de entregas res.
    $mesesEntregados = array();
    foreach ($meses as $mes) {
        $mesConsulta = $mes['mes'];
        $consulta = " show tables like 'entregas_res_$mesConsulta$periodoActual' ";
        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){
            $mesesEntregados[] = $mes['mes'];
        }
        else{
            break;
        }
    }

    // Array de lo que se deberia ENTREGAR
    $consulta = " select sum(sc.APS) as APS, sum(sc.CAJMRI) as CAJMRI, sum(sc.CAJTRI) as CAJTRI, sum(sc.CAJMPS) as CAJMPS from planilla_semanas ps left join sedes_cobertura sc on ps.semana = sc.semana ";
    if($codInst != 0){
		$consulta .= " where sc.cod_inst = '$codInst' ";
	}




    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
        $row = $resultado->fetch_assoc();
        $totalesPorEntregar = $row;
    }

    // Se manejará un array de totales para la suma de los totales entregados de cada complemento
    $totalesEntregados = array();
    $consulta = "";
    $aux = 0;
    foreach ($mesesEntregados as $mes) {
        if($aux > 0){
            $consulta .= " union ";
        }
        $consulta .= " select sum(D1+D2+D3+D4+D5+D6+D7+D8+D9+D10+D11+D12+D13+D14+D15+D16+D17+D18+D19+D20+D21+D22) as cantidad, tipo_complem as complemento  from entregas_res_$mes$periodoActual ";
        if($codInst != 0){
			$consulta .= " where cod_inst = '$codInst' ";
		}
		$consulta .= " group by tipo_complem ";
        $aux++;
    }
		if($consulta != ''){
			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			if($resultado->num_rows >= 1){
				while($row = $resultado->fetch_assoc()){
					if(isset($totalesEntregados[$row['complemento']])){
						$totalesEntregados[$row['complemento']] = $totalesEntregados[$row['complemento']] + $row['cantidad'];
					}else{
						$totalesEntregados[$row['complemento']] = $row['cantidad'];
					}
				}
			}
		}




    // Impresión

    // var_dump($totalesPorEntregar);
    // APS
    // CAJMRI
    // CAJTRI
    // CAJMPS

		$htmlTotales = "";

		if($totalesPorEntregar['APS'] > 0){
			$entregar = $totalesPorEntregar['APS'];

			if(isset($totalesEntregados['APS']) && $totalesEntregados['APS'] >0){
				$entregado = $totalesEntregados['APS'];
			} else{
				$entregado = 0;
			}



        $porcentaje = ($entregado/$entregar)*100;

        $entregar = number_format($entregar, 0, '.', ',');
        $entregado = number_format($entregado, 0, '.', ',');
        $porcentaje = number_format($porcentaje, 2, '.', ',');

 		$htmlTotales .= "<li> <h2 class='no-margins'> $entregado de $entregar </h2> <small>Alimento preparado en sitio</small> <div class='stat-percent'>$porcentaje% <i class='fa fa-level-up text-navy'></i></div> <div class='progress progress-mini'> <div style='width: $porcentaje%' class='progress-bar'></div> </div> </li>";
    }

    if($totalesPorEntregar['CAJMPS'] > 0){
        $entregar = $totalesPorEntregar['CAJMPS'];
        $entregado = $totalesEntregados['CAJMPS'];
        $porcentaje = ($entregado/$entregar)*100;

        $entregar = number_format($entregar, 0, '.', ',');
        $entregado = number_format($entregado, 0, '.', ',');
        $porcentaje = number_format($porcentaje, 2, '.', ',');

 		$htmlTotales .= "<li> <h2 class='no-margins'> $entregado de $entregar </h2> <small>Complemento Alimentario Mañana</small> <div class='stat-percent'>$porcentaje% <i class='fa fa-level-up text-navy'></i></div> <div class='progress progress-mini'> <div style='width: $porcentaje%' class='progress-bar'></div> </div> </li>";
    }


    if($totalesPorEntregar['CAJMRI'] > 0){
        $entregar = $totalesPorEntregar['CAJMRI'];
        $entregado = $totalesEntregados['CAJMRI'];
        $porcentaje = ($entregado/$entregar)*100;

        $entregar = number_format($entregar, 0, '.', ',');
        $entregado = number_format($entregado, 0, '.', ',');
        $porcentaje = number_format($porcentaje, 2, '.', ',');

 		$htmlTotales .= "<li> <h2 class='no-margins'> $entregado de $entregar </h2> <small>Complemento Alimentario RI</small> <div class='stat-percent'>$porcentaje% <i class='fa fa-level-up text-navy'></i></div> <div class='progress progress-mini'> <div style='width: $porcentaje%' class='progress-bar'></div> </div> </li>";
    }


	// var_dump($barrasTotalesMes);
	// var_dump($barrasTotalesSemana);
	// var_dump($labelsSemanas);
	// var_dump($entregasTotalesMes);
	// var_dump($entregasTotalesSemana);





	if($timeOption == 1){
		echo json_encode(array("barras"=>$barrasTotalesSemana, "entregas"=>$entregasTotalesSemana, "labels"=>$labelsSemanas, "totales"=>$htmlTotales));
	}else{
		echo json_encode(array("barras"=>$barrasTotalesMes, "entregas"=>$entregasTotalesMes, "labels"=>$labelsSemanas, "totales"=>$htmlTotales));
	}



	// Escribiendo archivo plano
	$file = fopen("arrays_rector_semana.txt", "w");
	fwrite($file, json_encode(array("barras"=>$barrasTotalesSemana, "entregas"=>$entregasTotalesSemana, "labels"=>$labelsSemanas, "totales"=>$htmlTotales)) . PHP_EOL);
	fclose($file);

	$file = fopen("arrays_rector_mes.txt", "w");
	fwrite($file, json_encode(array("barras"=>$barrasTotalesMes, "entregas"=>$entregasTotalesMes, "labels"=>$labelsSemanas, "totales"=>$htmlTotales)) . PHP_EOL);
	fclose($file);
}
