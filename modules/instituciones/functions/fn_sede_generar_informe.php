<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
	require_once '../../../fpdf181/fpdf.php';

	// // Send Headers
	// header('Content-type: application/pdf');
	// header('Content-Disposition: attachment; filename="myPDF.pdf');

	// // Send Headers: Prevent Caching of File
	// header('Cache-Control: private');
	// header('Pragma: private');
	// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

	$semana = (isset($_REQUEST['semana']) && $_REQUEST['semana']) ? mysqli_real_escape_string($Link, $_REQUEST['semana']) : '';
	$titulos = ['Codigo sede','Nombre sede','APS','CAJMRI','CAJTRI','CAJMPS', 'CAJTPS', 'RPC'];
	$anc_col = [24,98,10,13,13,13,13,10];

	// Datos de Priorización.
	$arrayDatPri = [];
	$conPri = "SELECT sedc.cod_sede, sed.nom_sede, SUM(APS) AS APS, SUM(CAJMRI) AS CAJMRI, SUM(CAJTRI) AS CAJTRI, SUM(CAJMPS) AS CAJMPS, SUM(CAJTPS) AS CAJTPS, SUM(RPC) AS RPC
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
	$conFoc = "SELECT cod_sede, SUM(IF(Tipo_complemento = 'APS', 1, 0)) AS APS, SUM(IF(Tipo_complemento = 'CAJMRI', 1, 0)) AS CAJMRI, SUM(IF(Tipo_complemento = 'CAJTRI', 1, 0)) AS CAJTRI, SUM(IF(Tipo_complemento = 'CAJMPS', 1, 0)) AS CAJMPS, SUM(IF(Tipo_complemento = 'CAJTPS', 1, 0)) AS CAJTPS,  SUM(IF(Tipo_complemento = 'RPC', 1, 0)) AS RPC
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
	foreach ($arrayDatPri as $i => $datPri)
	{
		foreach ($arrayDatFoc as $j => $datFoc)
		{
			if($datPri['cod_sede'] == $datFoc['cod_sede'])
			{
				// Mostrar sede
				$pdf->SetTextColor(0,0,0);
				$pdf->Cell($anc_col[0],7,$datPri['cod_sede'],1,0,'C');
				$pdf->Cell($anc_col[1],7,utf8_decode(strtoupper(substr($datPri['nom_sede'], 0, 57))),1,0);
				if(($datPri['APS'] - $datFoc['APS']) != 0 ){ $pdf->SetTextColor(237,85,101); } else { $pdf->SetTextColor(0,0,0); }
				$pdf->Cell($anc_col[2],7,($datPri['APS'] - $datFoc['APS']),1,0,'C');
				if(($datPri['CAJMRI'] - $datFoc['CAJMRI']) != 0 ){ $pdf->SetTextColor(237,85,101); } else { $pdf->SetTextColor(0,0,0); }
				$pdf->Cell($anc_col[3],7,($datPri['CAJMRI'] - $datFoc['CAJMRI']),1,0,'C');
				if(($datPri['CAJTRI'] - $datFoc['CAJTRI']) != 0 ){ $pdf->SetTextColor(237,85,101); } else { $pdf->SetTextColor(0,0,0); }
				$pdf->Cell($anc_col[4],7,($datPri['CAJTRI'] - $datFoc['CAJTRI']),1,0,'C');
				if(($datPri['CAJMPS'] - $datFoc['CAJMPS']) != 0 ){ $pdf->SetTextColor(237,85,101); } else { $pdf->SetTextColor(0,0,0); }
				$pdf->Cell($anc_col[5],7,($datPri['CAJMPS'] - $datFoc['CAJMPS']),1,0,'C');
				if(($datPri['CAJTPS'] - $datFoc['CAJTPS']) != 0 ){ $pdf->SetTextColor(237,85,101); } else { $pdf->SetTextColor(0,0,0); }
				$pdf->Cell($anc_col[6],7,($datPri['CAJTPS'] - $datFoc['CAJTPS']),1,0,'C');
				if(($datPri['RPC'] - $datFoc['RPC']) != 0 ){ $pdf->SetTextColor(237,85,101); } else { $pdf->SetTextColor(0,0,0); }
				$pdf->Cell($anc_col[7],7,($datPri['RPC'] - $datFoc['RPC']),1,1,'C');
				break;
			}
		}
	}

	$pdf->Output('I', 'Informe_diferencias_titulares.fpd');
