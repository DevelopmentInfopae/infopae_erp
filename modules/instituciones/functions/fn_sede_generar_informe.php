<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require_once '../../../fpdf181/fpdf.php';

	$semana = (isset($_REQUEST['semana']) && $_REQUEST['semana']) ? mysqli_real_escape_string($Link, $_REQUEST['semana']) : '';

	$consultaComplementos = " SELECT CODIGO FROM tipo_complemento ORDER BY CODIGO ";
	$respuestaComplementos = $Link->query($consultaComplementos) or die ('Error consultado complementos Ln 9');
	if ($respuestaComplementos->num_rows > 0) {
		while ($dataComplementos = $respuestaComplementos->fetch_object()) {
			$complementos[] = $dataComplementos;
		}
	}
	
	$aux = 74/count($complementos);
	$titulos = ['Codigo sede','Nombre sede'];
	$anc_col = [24, 96];
	$sum = '';
	$sumFoc = '';
	foreach ($complementos as $key => $value) {
		$sum .= " SUM($value->CODIGO) AS $value->CODIGO, ";
		$sumFoc .= " SUM(IF(Tipo_complemento = '$value->CODIGO', 1, 0)) AS '$value->CODIGO', ";
		array_push($titulos, $value->CODIGO);
		array_push($anc_col, $aux);
	}
	$sum = trim($sum, ', ');
	$sumFoc = trim($sumFoc, ', ');

	// Datos de Priorización.
	$arrayDatPri = [];
	$conPri = "SELECT 	sedc.cod_sede, 
						sed.nom_sede, 
						$sum
						FROM sedes_cobertura sedc
            	INNER JOIN sedes".$_SESSION['periodoActual']." sed ON sedc.cod_sede = sed.cod_sede
						WHERE semana = '$semana'
						GROUP BY cod_sede;"; 
	$resPri = $Link->query($conPri);
	if($resPri->num_rows > 0) {
		while ($regPri = $resPri->fetch_assoc()) {
			$arrayDatPri[] = $regPri;
		}
	}

	// Datos de focalización.
	$arrayDatFoc = [];
	$conFoc = "SELECT 	cod_sede,  
						$sumFoc
					FROM focalizacion$semana
					GROUP BY cod_sede
					ORDER BY cod_sede";
	$resFoc = $Link->query($conFoc); 
	if($resFoc->num_rows > 0) {
		while($regFoc = $resFoc->fetch_assoc()) {
			$arrayDatFoc[] = $regFoc;
		}
	}

	// Creación de PDF
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->AliasNbPages();
	$pdf->SetDrawColor(188,188,188);

	$pdf->Image("../../../img/logo_simbolo_infopae2.png",15,8,18);
	$pdf->SetFont('Arial','B',11);
	$pdf->Cell(30,15,'','TRBL',0,'C');
	$pdf->Cell(130,15,utf8_decode(strtoupper("Informe de diferencias de Titulares focalizacion")),'TB',0,'C');
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(30,15,utf8_decode('Página').' '.$pdf->PageNo().' / {nb}','TRBL',1,'R');
	$pdf->Ln();

	$pdf->SetFont('Arial','I',8);
	$pdf->Cell(190,4,utf8_decode("* Número de columna positivo: Existen más titulares de derecho en Priorización que en Focalización."),0,1);
	$pdf->Cell(190,4,utf8_decode("* Número de columna negativo: Existen más titulares de derecho en Focalización que en Priorización."),0,1);
	$pdf->Ln();

	$pdf->SetFont('Arial','B',8);
	$pdf->SetFillColor(26,179,148);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetDrawColor(188,188,188);
	for($i=0;$i<count($titulos);$i++)
	{
    	$pdf->Cell($anc_col[$i],7,utf8_decode(strtoupper($titulos[$i])),1,0,'C',true);
	}
	$pdf->Ln();

	$pdf->SetTextColor(0,0,0);
	$pdf->SetFont('Arial','',8);
	$pdf->SetFillColor(237,85,101);

	// Validaciones
	foreach ($arrayDatPri as $i => $datPri) {
		foreach ($arrayDatFoc as $j => $datFoc) {
			if($datPri['cod_sede'] == $datFoc['cod_sede']) {
				$pdf->SetTextColor(0,0,0);
				$pdf->Cell($anc_col[0],7,$datPri['cod_sede'],1,0,'C');
				$pdf->Cell($anc_col[1],7,utf8_decode(strtoupper(substr($datPri['nom_sede'], 0, 57))),1,0);

				foreach ($complementos as $key => $value) {
					(($datPri[$value->CODIGO] - $datFoc[$value->CODIGO]) != 0 ) ? $pdf->SetTextColor(237,85,101) : $pdf->SetTextColor(0,0,0);
					$pdf->Cell($anc_col[2],7,($datPri[$value->CODIGO] - $datFoc[$value->CODIGO]),1,0,'C');
				}
				$pdf->ln();
				break;
			}
		}
	}

	$pdf->Output('I', 'Informe_diferencias_titulares.fpd');
