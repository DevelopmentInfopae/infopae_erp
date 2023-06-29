<?php
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(30,$tamannoFuente,utf8_decode('DEPARTAMENTO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(60,$tamannoFuente,utf8_decode($departamento),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(26,$tamannoFuente,utf8_decode('CÓDIGO DANE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(40,$tamannoFuente,utf8_decode($_SESSION['p_CodDepartamento']),0,0,'L',False);
if($codigoSede){ 	
	if(isset($sedes[$codigoSede])){ 
		$codigo_DANE = $sedes[$codigoSede]['codigo_DANE'];
		$nombre_sede = $sedes[$codigoSede]['nom_sede'];
		$codigo_sede = $codigoSede;
		$nombre_institucion = $sedes[$codigoSede]['nom_inst'];
		$codigo_institucion = $sedes[$codigoSede]['cod_inst'];
	}else{
		$nombre_sede = '';
		$codigo_sede = "";
		$nombre_institucion = '';
		$codigo_institucion = "";
	}
} else {
	$nombre_sede = '';
	$codigo_sede = "";
	$nombre_institucion = '';
	$codigo_institucion = "";
}

//Código para obtener el total programado en el mes seleccionado, búsqueda por Tipo de complemento, Sede y Mes.
$semanasMes = [];
$consSemanasMes = "SELECT SEMANA FROM planilla_semanas WHERE MES = '".$mes."' AND DIA >= $diaInicialSemanaInicial AND DIA <= $diaFinalSemanaFinal GROUP BY SEMANA;"; //obtenemos las semanas que hay en el mes
$resSemanasMes = $Link->query($consSemanasMes) or die($Link->error);
if ($resSemanasMes->num_rows > 0) {
	while ($dsm = $resSemanasMes->fetch_assoc()) {
		$semanasMes[$dsm['SEMANA']] = 1; //guardamos las semanas en un array
	}
}

$totalProgramadoMes = 0; //variable para sumar los totales de cada semana y obtener un total del mes
$diasCubiertos = 0;
$tpm = 0;
$diasSemana = 0;
if ($tipoPlanilla != 6 && $tipoPlanilla != 1 && $tipoPlanilla != 9){	
	//recorremos el array de las semanas obtenidas para cambiar la tabla de priorización, ej : priorizacion01, priorizacion02, etc
	foreach ($semanasMes as $semana => $set) { 
		//obtenemos el total de priorizaciones por complemento seleccionado de la semana en turno, luego lo multiplicamos por el número de días que tiene la semana en turno.
		$consTotalEntregado = "SELECT DISTINCT SEMANA AS SM, MES AS MS, COUNT(DIA) AS dias_semana,
							(
								(SELECT SUM(".$tipoComplemento.") FROM priorizacion".$semana." WHERE cod_sede = '".$codigoSede."') *
							    (SELECT COUNT(DIA) FROM planilla_semanas WHERE SEMANA = SM AND MES = MS)
							) AS total_entregas
							FROM planilla_semanas WHERE SEMANA = '".$semana."' AND MES = '".$mes."';";	
		$resTotalEntregado = $Link->query($consTotalEntregado);
		// var_dump($consTotalEntregado);
		//Si el SQL se ejecuta correctamente y hay datos, es decir si encuentra la tabla priorización$semana (priorizacion03, priorizacion03b, etc)
		if ($resTotalEntregado !== FALSE && $resTotalEntregado->num_rows > 0) {
		    $te = $resTotalEntregado->fetch_assoc();
		    $totalProgramadoMes+=$te['total_entregas']; //suma de entregas en dias cubiertos
		    $tpm = $te['total_entregas']; //guardado del total de entregas de la última semana que si está priorizada (Para cálculo en caso de que falten semanas por priorizar)
		    $diasSemana = $te['dias_semana']; //guardamos los días que tiene la semana en turno.
		    $diasCubiertos += $te['dias_semana']; //Suma de los números(cuenta) de días cubiertos.
        } else { //En caso de no encontrar datos para la semana en turno o la tabla para dicha semana, multiplicamos la priorización de la semana del mes seleccionado que si tiene tabla de priorización, por el número de dias de dicho mes, la priorización mencionada ya se guardó en la variable $totalProgramadoMes en el turno de la semana anterior.
		    // Esto sucede cuando sólo hay priorización del primer día o primera semana en el Mes seleccionado.
			//consultamos los días que tiene el mes seleccionado
			$consDiasMes = "SELECT * FROM planilla_dias WHERE mes = '".$mes."'";
			$resDiasMes = $Link->query($consDiasMes);
			if ($resDiasMes->num_rows > 0) {
				$dms = $resDiasMes->fetch_assoc();
				$diasMes = 0;
				for ($i=1; $i < 31; $i++) { //recorremos los campos D1, D2, etc que no estén vacíos.
					if (!empty($dms['D'.$i])) {
						$diasMes++;
					}
				}
			}
			$diasNoPriorizados = $diasMes-$diasCubiertos; //a los días obtenidos del mes, les restamos los días que ya se cubrieron
			$tpm = (($tpm / $diasSemana) * $diasNoPriorizados) + $totalProgramadoMes; //Se divide el total de entregas de la última semana priorizada por el número de días de la misma, al resultado se le multiplica por el número de días que hacen falta por cubrir. Luego, al total obtenido, se le suma el total de los días cubiertos.
			$totalProgramadoMes = $tpm;
			break;
		}
	}
}
//Código para obtener el total programado en el mes seleccionado, búsqueda por Tipo de complemento, Sede y Mes.

// $pdf->SetFont('Arial','B',$tamannoFuente);
// $pdf->Cell(35,$tamannoFuente,utf8_decode('NOMBRE SEDE:'),0,0,'L',False);
// $pdf->SetFont('Arial','',$tamannoFuente);
// $pdf->Cell(90,$tamannoFuente,utf8_decode($nombre_sede),0,0,'L',False);
// $pdf->SetFont('Arial','B',$tamannoFuente);
// $pdf->Cell(20,$tamannoFuente,utf8_decode('CÓDIGO DANE:'),0,0,'L',False);
// $pdf->SetFont('Arial','',$tamannoFuente);
// $pdf->Cell(0,$tamannoFuente,utf8_decode($codigo_sede),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(65,$tamannoFuente,utf8_decode('NOMBRE INSTITUCIÓN O CENTRO EDUCATIVO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,$tamannoFuente,utf8_decode($nombre_institucion).' --  '.utf8_decode($nombre_sede) ,0,0,'L',False);

$pdf->Ln(5);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(30,$tamannoFuente,utf8_decode('MUNICIPIO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(60,$tamannoFuente,utf8_decode($municipioNm),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(26,$tamannoFuente,utf8_decode('CÓDIGO DANE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(40,$tamannoFuente,utf8_decode($_POST['municipio']),0,0,'L',False);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(65,$tamannoFuente,utf8_decode('CÓDIGO DANE:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(0,$tamannoFuente,utf8_decode($codigo_DANE),0,0,'L',False);
$pdf->Ln(5);

$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(30,$tamannoFuente,utf8_decode('OPERADOR:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(126,$tamannoFuente,utf8_decode($operador),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(35,$tamannoFuente,utf8_decode('MES ATENCIÓN:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);

$mesPos = $_POST['mes'];
$consultaNombreMes = " SELECT NombreMes FROM planilla_dias WHERE mes = '$mesPos' ";
$respuestaNombreMes = $Link->query($consultaNombreMes) or die ('Error al consultar el nombre del mes ' . mysqli_error($Link));
if ($respuestaNombreMes->num_rows > 0) {
	$dataNombreMes = $respuestaNombreMes->fetch_assoc();
	$mesNm = $dataNombreMes['NombreMes'];
}
if($mesAdicional > 0) {
	$mesAdicional = intval($_POST['mes']+1);
	if($mesAdicional > 12) {
		$mesAdicional = 1;
	}
	$mesNm.=' - '.$mesAdicional;
}
$pdf->Cell(30,$tamannoFuente,utf8_decode(strtoupper($mesNm)),0,0,'L',False);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(10,$tamannoFuente,utf8_decode('AÑO:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(40,$tamannoFuente,utf8_decode($_SESSION['p_ano']),0,0,'L',False);
$pdf->Ln(5);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(30,$tamannoFuente,utf8_decode('CONTRATO N°:'),0,0,'L',False);
$pdf->SetFont('Arial','',$tamannoFuente);
$pdf->Cell(80,$tamannoFuente,utf8_decode($_SESSION['p_Contrato']),0,0,'L',False);

$x = 3;
// Condición que oculta o muestra la sección de información de las raciones.
if ($tipoPlanilla == 1 || $tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4 || $tipoPlanilla == 5 || $tipoPlanilla == 7 || $tipoPlanilla == 8) {
	$y = 40;
	$altura = 5;
	// $pdf->SetXY($x, $y);
	$pdf->Ln(5);

	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(72,$altura,utf8_decode('RACIONES MENSUALES PROGRAMADAS ALMUERZOS:'),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(20,$altura,(($tipoComplemento == "APS") && ($tipoPlanilla != 1) ? $totalProgramadoMes : "" ), "B", 0, 'C', False);

	// $pdf->Cell(10);
	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(69,$altura,utf8_decode('RACIONES MENSUALES ENTREGADAS ALMUERZOS:'),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// if ($tipoPlanilla == 3) { $pdf->SetTextColor(190,190,190); }
	// $pdf->Cell(20,$altura,(($tipoComplemento == "APS" && ($tipoPlanilla == 4 || $tipoPlanilla == 7 || $tipoPlanilla == 8)) ? $totales['entregas'] : "" ), "B", 0, 'C', False);
	// $pdf->SetTextColor(0,0,0);

	// $pdf->Cell(10);
	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(30,$altura,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(5,$altura,(($tipoComplemento == "APS") && ($tipoPlanilla == 4) ? "X" : ""),1,0,'C',False);

	// $pdf->Cell(10);
	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(30,$altura,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(5,$altura,'',1,1,'C',False);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(80,$altura,utf8_decode('RACIONES  PROGRAMADOS COMPLEMENTO ALIMENTARIO:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(20,$altura,(($tipoComplemento == "CAJMPS" || $tipoComplemento == "CAJMRI") && ($tipoPlanilla != 1) ? $totalProgramadoMes : ""), "B", 0, 'C', False);

    $pdf->Ln(5);
    
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(25,$altura,utf8_decode('RACIONES ATENDIDAS:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(20,$altura,(($tipoComplemento == "CAJMPS" || $tipoComplemento == "CAJMRI") && ($tipoPlanilla != 1) ? $totalProgramadoMes : ""), "B", 0, 'C', False);

	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(8,$altura,utf8_decode('AM.'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	if ($tipoPlanilla == 3) { $pdf->SetTextColor(190,190,190); }
	$pdf->Cell(20,$altura,(($tipoComplemento == "CAJMPS" || $tipoComplemento == "CAJMRI") && ($tipoPlanilla == 4 || $tipoPlanilla == 7 || $tipoPlanilla == 8) ? $totales['entregas'] : "" ),"B",0,'C',False);
	$pdf->SetTextColor(0,0,0);

	// $pdf->Cell(10);
	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(30,$altura,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(5,$altura,(($tipoComplemento == "CAJMPS") && ($tipoPlanilla == 4) ? "X" : ""),1,0,'C',False);

	// $pdf->Cell(10);
	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(30,$altura,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(5,$altura,(($tipoComplemento == "CAJMRI") && ($tipoPlanilla == 4) ? "X" : ""),1,1,'C',False);

	$pdf->Ln(5);

	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(72,$altura,utf8_decode('RACIONES MENSUALES PROGRAMADAS CAJT:'),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(20,$altura,(($tipoComplemento == "CAJTRI" || $tipoComplemento == "CAJTPS") && ($tipoPlanilla != 1) ? $totalProgramadoMes : ""), "B", 0, 'C', False);

	$pdf->Cell(35);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(8,$altura,utf8_decode('PM.'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	if ($tipoPlanilla == 3) { $pdf->SetTextColor(190,190,190); }
	$pdf->Cell(20,$altura,(($tipoComplemento == "CAJTRI" || $tipoComplemento == "CAJTPS") && ($tipoPlanilla == 4 || $tipoPlanilla == 7 || $tipoPlanilla == 8) ? $totales['entregas'] : "" ),"B",0,'C',False);
	$pdf->SetTextColor(0,0,0);

	$pdf->Ln(5);
	// $pdf->Cell(10);
	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(30,$altura,utf8_decode("PREPARAR EN SITIO:"),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(5,$altura,(($tipoComplemento == "CAJTPS") && ($tipoPlanilla != 1) ? "X" : ""),1,0,'C',False);

	// $pdf->Cell(10);
	// $pdf->SetFont('Arial','B',$tamannoFuente);
	// $pdf->Cell(30,$altura,utf8_decode("INDUSTRIALIZADA:"),0,0,'L',False);
	// $pdf->SetFont('Arial','',$tamannoFuente);
	// $pdf->Cell(5,$altura,(($tipoComplemento == "CAJTRI") && ($tipoPlanilla != 1) ? "X" : ""),1,1,'C',False);

	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(80,$altura,utf8_decode('RACIONES PROGRAMADOS ALMUERZO:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(20,$altura,(($tipoComplemento == "APS") && ($tipoPlanilla != 1) ? $totalProgramadoMes : "" ), "B", 0, 'C', False);
	$pdf->Ln(5);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(80,$altura,utf8_decode('RACIONES ENTREGADAS ALMUERZO:'),0,0,'L',False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(20,$altura,(($tipoComplemento == "APS" && ($tipoPlanilla == 4 || $tipoPlanilla == 7 || $tipoPlanilla == 8)) ? $totales['entregas'] : "" ), "B", 0, 'C', False);

	$pdf->Ln(5);

} else {
	$y = 40;
	$pdf->SetXY($x, $y);
}

$pdf->Ln(1.5);
$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFillColor(225,225,225);
$pdf->Cell(0,12,utf8_decode(''),0,0,'R',True);
$pdf->SetXY($x, $y);
$pdf->Cell(0,12,utf8_decode(''),'T',0,'R',False);
$pdf->SetXY($x, $y);
$pdf->Cell(0,12,utf8_decode(''),'L',0,'R',False);
$pdf->SetXY($x, $y);
$pdf->Cell(0,12,utf8_decode(''),'R',0,'R',False);

$pdf->SetXY($x, $y);
$pdf->SetFont('Arial','B',$tamannoFuente);
$pdf->Cell(8,12,utf8_decode('No'),'R',0,'C',False);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell(10,12,utf8_decode('Tipo'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(10,12,utf8_decode('Dcto'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(10,12,utf8_decode(''),'R',0,'C',False);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell(22,12,utf8_decode('N° Doc de'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(22,12,utf8_decode('Identidad'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(22,12,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$anchoDatosNombre = 26.5;
$pdf->Cell($anchoDatosNombre,12,utf8_decode('1° Nombre'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell($anchoDatosNombre,12,utf8_decode('del Titular'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell($anchoDatosNombre,12,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell($anchoDatosNombre,12,utf8_decode('2° Nombre'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell($anchoDatosNombre,12,utf8_decode('del Titular'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell($anchoDatosNombre,12,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell($anchoDatosNombre,12,utf8_decode('1° Apellido'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell($anchoDatosNombre,12,utf8_decode('del Titular'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell($anchoDatosNombre,12,utf8_decode(''),'R',0,'C',False);


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell($anchoDatosNombre,12,utf8_decode('2° Apellido'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell($anchoDatosNombre,12,utf8_decode('del Titular'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell($anchoDatosNombre,12,utf8_decode(''),'R',0,'C',False);
$x = $pdf->GetX();
$y = $pdf->GetY();


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell(22,12,utf8_decode('Fecha Nacimiento'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(22,12,utf8_decode('DD/MM/AAAA'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(22,12,utf8_decode(''),'R',0,'C',False);
$x = $pdf->GetX();
$y = $pdf->GetY();

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell(16,12,utf8_decode('Pertenencia'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(16,12,utf8_decode('Étnica'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(16,12,utf8_decode(''),'R',0,'C',False);
$x = $pdf->GetX();
$y = $pdf->GetY();

$pdf->SetXY($x, $y);
$pdf->RotatedText($x+3.5,$y+10,utf8_decode("1.Sexo"),90);
$pdf->Cell(10,12,utf8_decode(''),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(5,12,utf8_decode(''),'R',0,'C',False);
$x = $pdf->GetX();
$y = $pdf->GetY();

$pdf->SetXY($x, $y);
$pdf->RotatedText($x+3.5,$y+10,utf8_decode("2.Grado"),90);
$pdf->Cell(10,12,utf8_decode(''),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(5,12,utf8_decode(''),'R',0,'C',False);
$x = $pdf->GetX();
$y = $pdf->GetY();


$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetXY($x, $y-1.6);
$pdf->Cell(13,12,utf8_decode('3.Tipo'),0,0,'C',False);
$pdf->SetXY($x, $y+1.6);
$pdf->Cell(13,12,utf8_decode('comp'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(13,12,utf8_decode(''),'R',0,'C',False);

$x = $pdf->GetX();
$y = $pdf->GetY();

$xT = $pdf->GetX();
$yT = $pdf->GetY();
$pdf->Cell(132,4,utf8_decode('Fecha de Entrega - Escriba el día hábil al cual corresponde la entrega del Complemento Alimentario'),'BR',0,'C',False);

$pdf->SetXY($x, $y+4);

if($tipoPlanilla != 1){
	$dia = (int)$primer_dia;
	for($i = 0 ; $i < 22 ; $i++){
			$auxDia = 'D'.$dia;
			if(isset($dias_encabezado[$auxDia])){
		 		$pdf->Cell(6,4,utf8_decode($dias_encabezado[$auxDia]),'R',0,'C',False);
			}else{
		 		$pdf->Cell(6,4,"",'R',0,'C',False);
			}
		$dia++;
	}
}else{
	for($i = 0 ; $i < 22 ; $i++){
	  $pdf->Cell(6,4,"",'R',0,'C',False);
	}
}

$x = $pdf->GetX();
$y = $pdf->GetY();

$pdf->SetXY($x, $y-4);
$pdf->Cell(0,7,utf8_decode('Total'),0,0,'C',False);
$pdf->SetXY($x, $y);
$pdf->Cell(0,7,utf8_decode('días'),0,0,'C',False);

$pdf->setXY($xT, $yT+8);
$pdf->Cell(132,4,utf8_decode('Número de días de atención - Marque con X el día que el Titular de Derecho recibe complemento alimentario'),'TR',0,'C',False);

$pdf->SetXY($x, $y);
$pdf->Ln(8);

$xCuadroFilas = $pdf->GetX();
$yCuadroFilas = $pdf->GetY();
