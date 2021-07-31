<?php

require_once '../../config.php';
require_once '../../db/conexion.php';
require '../../vendor/autoload.php';

// definimos los parametros para el nuevo libro de excel que vamos a crear
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Supervisor;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Layout;

// creamos un nuevo libro de trabajo
$spreadsheet = new Spreadsheet();

// accedemos a la hoja activa de ese libro 
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('D2', 'Sistema de Información Tecnológico InfoPAE');
$sheet->mergeCells('D2:P4');
$sheet->setCellValue('D5', 'Información Titular de Derecho');
$sheet->mergeCells('D5:P7');
$sheet->mergeCells('B2:C7');

$titulos1 = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí',
        // 'color' => '#033B73'
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];


$titulos = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí',
        'color' => ['argb' => 'FDFEFE']
        // 'color' => '#033B73'
    ],
    'fill' => [
      'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
      'color' => ['argb' => '0C1846'],
      ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$titulos2 = [
    'font' => [
        'bold' => true,
        'size'  => 10,
        'name' => 'calibrí',
        // 'color' => ['argb' => 'FDFEFE']
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        // 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_LEFT,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$color = [
 'fill' => [
     'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
     'color' => ['argb' => 'FFFFFF'],
    ],
];

$sheet->getStyle("D2:P4")->applyFromArray($titulos1);
$sheet->getStyle("D5:P7")->applyFromArray($titulos1);
$sheet->getStyle("D7:P7")->applyFromArray($titulos1);
$sheet->getStyle('B2:C7')->applyFromArray($titulos1);

$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
$drawing->setName('Logo');
$drawing->setDescription('Logo');
$drawing->setPath('../../upload/logotipos/infopae.png');
$drawing->setHeight(90);
$drawing->setCoordinates('B2');
$drawing->setOffsetX(25);
$drawing->setOffsetY(17);
$drawing->setWorksheet($spreadsheet->getActiveSheet());

$infor = [
    'font' => [
        'size'  => 9,
        'name' => 'calibrí'
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$informacion = [];
$periodoActual = $_SESSION['periodoActual'];
$semana = $_POST['semana'];
$documentoTitular = $_POST['num_doc_exportar'];
if ($semana < 10) {
	$semana = "0".$semana;
}

$consultaInformacionBasica = "SELECT 
								f.num_doc AS documento, 
								td.nombre AS tipodocumento, 
								f.nom1 AS primerNombre, 
								f.nom2 AS segundoNombre, 
								f.ape1 AS primerApellido, 
								f.ape2 AS segundoApellido, 
								f.genero AS genero, 
								f.dir_res AS direccion, 
								f.telefono AS telefono, 
								f.edad AS edad, 
								f.cod_estrato AS estrato,
								f.sisben AS sisben, 
								d.nombre AS discapacidad, 
								e.DESCRIPCION AS etnia, 
								p.nombre AS victima, 
								s.nom_sede AS sede, 
								s.nom_inst AS institucion, 
								j.nombre AS jornada, 
								u.Ciudad AS municipio, 
								f.zona_res_est AS zona, 
								f.nom_acudiente AS nombreAcudiente, 
								f.doc_acudiente AS documentoAcudiente, 
								f.tel_acudiente AS telefonoAcudiente 
							FROM focalizacion$semana f 
							INNER JOIN tipodocumento td ON f.tipo_doc = td.id
							INNER JOIN discapacidades d ON f.cod_discap = d.id
							INNER JOIN etnia e ON f.etnia = e.ID
							INNER JOIN pobvictima p ON f.cod_pob_victima = p.id
							INNER JOIN sedes21 s ON f.cod_sede = s.cod_sede
							INNER JOIN jornada j ON f.cod_jorn_est = j.id
							INNER JOIN ubicacion u ON f.cod_mun_res = u.CodigoDANE
							WHERE f.num_doc = '".$documentoTitular."'";

$respuestaInformacionBasica = $Link->query($consultaInformacionBasica) or die('Error al consultar la focalizacion '. mysqli_error($Link));
if ($respuestaInformacionBasica->num_rows > 0) {
	$dataInformacionBasica = $respuestaInformacionBasica->fetch_assoc();
	$informacion = $dataInformacionBasica;
}						

$numFila = 10;
$sheet->setCellValue("B".$numFila, "INFORMACIÓN BASICA");
$sheet->mergeCells("B".$numFila .":". "F".$numFila);
$tituloInformacionBasica = "B".$numFila .":". "F".$numFila;
$numFila ++;

$sheet->setCellValue("B".$numFila, "Tipo de Documento");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["tipodocumento"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Documento");
$sheet->setCellValue("C".$numFila, number_format($informacion["documento"],0,',','.'));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Primer Nombre");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["primerNombre"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Segundo Nombre");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["segundoNombre"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Primer Apellido");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["primerApellido"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Segundo Apellido");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["segundoApellido"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$stringGenero = '';
if ($informacion['genero'] == 'M') {
	$stringGenero = 'Masculino';
}elseif ($informacion['genero'] == 'F') {
	$stringGenero = 'Femenino';
}else { $stringGenero = 'No definido';}
$sheet->setCellValue("B".$numFila, "Género");
$sheet->setCellValue("C".$numFila, strtoupper($stringGenero));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Dirección");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["direccion"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Teléfono");
$sheet->setCellValue("C".$numFila, $informacion["telefono"]);
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Edad");
$sheet->setCellValue("C".$numFila, $informacion["edad"]);
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Estrato");
$sheet->setCellValue("C".$numFila, $informacion["estrato"]);
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Sisben");
$sheet->setCellValue("C".$numFila, $informacion["sisben"]);
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Discapacidad");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["discapacidad"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Etnia");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["etnia"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Población victima");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["victima"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Sede Educativa");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["sede"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Institución Educativa");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["institucion"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Jornada");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["jornada"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$sheet->setCellValue("B".$numFila, "Municipio");
$sheet->setCellValue("C".$numFila, strtoupper($informacion["municipio"]));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$stringZona = '';
if ($informacion['zona'] == 1) {
	$stringZona = 'Rural';
}elseif($informacion['zona'] == 2){
	$stringZona = 'Urbano';
}else{ $stringZona = 'Indefinido';}
$sheet->setCellValue("B".$numFila, "Zona");
$sheet->setCellValue("C".$numFila, strtoupper($stringZona));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$stringNombreAcudiente = '';
if ($informacion['nombreAcudiente'] == '') {
	$stringNombreAcudiente = "No registra";
}else {$stringNombreAcudiente = $informacion['nombreAcudiente'];}
$sheet->setCellValue("B".$numFila, "Nombre Acudiente");
$sheet->setCellValue("C".$numFila, strtoupper($stringNombreAcudiente));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$stringDocumentoAcudiente = '';
if ($informacion['documentoAcudiente'] == '') {
	$stringDocumentoAcudiente = "No registra";
}else {$stringDocumentoAcudiente = $informacion['documentoAcudiente'];}
$sheet->setCellValue("B".$numFila, "Documento Acudiente");
$sheet->setCellValue("C".$numFila, strtoupper($stringDocumentoAcudiente));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$stringTelefonoAcudiente = '';
if ($informacion['telefonoAcudiente'] == '') {
	$stringTelefonoAcudiente = "No registra";
}else{ $stringTelefonoAcudiente = $informacion['telefonoAcudiente'];}
$sheet->setCellValue("B".$numFila, "Teléfono Acudiente");
$sheet->setCellValue("C".$numFila, strtoupper($stringTelefonoAcudiente));
$sheet->mergeCells("C".$numFila .":". "F".$numFila);
$numFila ++;

$subtitulosInformacionBasica = "B11:B33";
$tablaInformacionBasica = "C11:F33";

$meses;
$consultaMeses = "SELECT DISTINCT(mes) FROM planilla_semanas;";
$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar planilla semanas');
if ($respuestaMeses->num_rows > 0) {
	while ($dataMeses = $respuestaMeses->fetch_assoc()) {
		$meses[] = $dataMeses;
	}
}

// var_dump($respuesta);
$numFila = 10;
$letra = "I";
$sheet->setCellValue($letra.$numFila, "CONSUMO COMPLEMENTOS ALIMENTARIOS");
$sheet->mergeCells($letra.$numFila .":". "P".$numFila);
$tituloConsumos = $letra.$numFila .":". "P".$numFila;
$numFila ++;

$sheet->setCellValue($letra.$numFila, "Semana");
$letra ++;
$sheet->setCellValue($letra.$numFila, "Complemento");
$letra ++;
$sheet->setCellValue($letra.$numFila, "Lunes");
$letra ++;
$sheet->setCellValue($letra.$numFila, "Martes");
$letra ++;
$sheet->setCellValue($letra.$numFila, "Miércoles");
$letra ++;
$sheet->setCellValue($letra.$numFila, "Jueves");
$letra ++;
$sheet->setCellValue($letra.$numFila, "Viernes");
$letra ++;
$sheet->setCellValue($letra.$numFila, "Validación");
$subtitulosConsumos = "I".$numFila .":". "P".$numFila;	

$numFila ++;
$letra = "I";
$filaInicial = $numFila;
foreach ($meses as $key => $mes) {
	$validacionMes = '';
	$consultaMes = "SELECT num_doc FROM entregas_res_".$mes['mes'].$periodoActual. " WHERE num_doc = '" .$documentoTitular. "';";
	$respuestaMes = $Link->query($consultaMes) or die ('Error al validar el mes ' . mysqli_error($Link));
	if ($respuestaMes->num_rows > 0) {
		$validacionMes = 'si';
	}
	$numeroDia = 1;
	$consultaSemanas = "SELECT DISTINCT(semana) FROM planilla_semanas WHERE mes = '". $mes['mes'] ."';";
	$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las semanas '. mysqli_error($Link));
	if ($respuestaSemanas->num_rows > 0) {
		$semanas = [];
		while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
			$semanas[] = $dataSemanas;
		}
	}	
	foreach ($semanas as $key => $semana) {
		$semanaComplemento = 1;	
		$concatenacionDias = '';	
		$consultaDias = "SELECT dia FROM planilla_semanas WHERE mes = '" .$mes['mes'] . "' AND semana = '" .$semana['semana'] . "';";
		$respuestaDias = $Link->query($consultaDias) or die ('Error al consultar los dias' . mysqli_error($Link));
		if ($respuestaDias->num_rows > 0) {
			$dias = [];
			while ($dataDias = $respuestaDias->fetch_assoc()) {
				$dias[] = $dataDias;
			}
		}
		if ($validacionMes == 'si') {
			$letra = "I";
			$sheet->setCellValue($letra.$numFila, "Semana".$semana['semana']);
			$consultaComplemento = "SELECT tipo_complem".$semanaComplemento. " AS complemento, TipoValidacion FROM entregas_res_".$mes['mes'].$periodoActual. " WHERE num_doc = '" .$documentoTitular. "';";
			$respuestaComplemento = $Link->query($consultaComplemento) or die ('Error al consultar el tipo de complemento ' . mysqli_error($Link));
			if ($respuestaComplemento->num_rows > 0) {
				$dataComplemento = $respuestaComplemento->fetch_assoc();
				$complemento = $dataComplemento['complemento'];
				$validacion = $dataComplemento['TipoValidacion'];
			}
			$letra = "J";
			$sheet->setCellValue($letra.$numFila, strtoupper($complemento));
			$letra = "P";
			$sheet->setCellValue($letra.$numFila, strtoupper($validacion));
		}
		
		foreach ($dias as $key => $dia) {
			$D = '';
			$concatenacionDias = "D".$numeroDia;
			$consultaEntregaD = "SELECT $concatenacionDias AS D FROM entregas_res_".$mes['mes'].$periodoActual. " WHERE num_doc = '" .$documentoTitular. "';";
			$respuestaEntregaD = $Link->query($consultaEntregaD) or die ('Error al consultar el Día ' . mysqli_error($Link));
			if ($respuestaEntregaD->num_rows > 0) {
				$dataEntregaD = $respuestaEntregaD->fetch_assoc();
				$D = $dataEntregaD['D'];
			}			
			$consultaNOMDIAS = "SELECT NOMDIAS FROM planilla_semanas WHERE mes = '" .$mes['mes']. "' AND semana = '" .$semana['semana']. "' AND dia = '" .$dia['dia']. "';";
			$respuestaNOMDIAS = $Link->query($consultaNOMDIAS) or die ('Error al consultar el nombre del dia ' . mysqli_error($Link));
			if ($respuestaNOMDIAS->num_rows > 0) {
				$dataNOMDIAS = $respuestaNOMDIAS->fetch_assoc();
				$NOMDIAS = $dataNOMDIAS['NOMDIAS'];
			}
			if ($D == '1') {
				$D = "X";
			}elseif($D == '0'){
				$D = "-";
			}elseif($D == ''){
				$D = 'R';
			}
			if (strtolower($NOMDIAS) == 'lunes' && $D != 'R') {
				$letra = "K";
				$sheet->setCellValue($letra.$numFila, $D);
			}
			else if (strtolower($NOMDIAS) == 'martes' && $D != 'R') {
				$letra = "L";
				$sheet->setCellValue($letra.$numFila, $D);
			}
			else if (strtolower($NOMDIAS) == 'miércoles' || strtolower($NOMDIAS) == 'miercoles' && $D != 'R') {
				$letra = "M";
				$sheet->setCellValue($letra.$numFila, $D);
			}
			else if (strtolower($NOMDIAS) == 'jueves' && $D != 'R') {
				$letra = "N";
				$sheet->setCellValue($letra.$numFila, $D);
			}
			else if (strtolower($NOMDIAS) == 'viernes' && $D != 'R') {
				$letra = "O";
				$sheet->setCellValue($letra.$numFila, $D);
				$numeroFilaFinal = $numFila;
			}
			$numeroDia ++;
			// $numeroFilaFinal ++;
			
 		}
 		$numFila ++;
 		$semanaComplemento ++;
	}
}
$sheet->setCellValue("I".($numeroFilaFinal+1), "Blanco : No Actividad,    X : Consumio,    - : No Consumio");
$sheet->mergeCells("I".($numeroFilaFinal+1). ":" ."P".($numeroFilaFinal+1));
// $numeroFilaFinal = ++$numFila;
$tablaConsumos = "I".$filaInicial .":". "P".($numeroFilaFinal+1);

$sheet->getColumnDimension("I")->setWidth(18); 
$sheet->getColumnDimension("J")->setWidth(18); 
$sheet->getColumnDimension("K")->setWidth(11); 
$sheet->getColumnDimension("L")->setWidth(11); 
$sheet->getColumnDimension("M")->setWidth(11); 
$sheet->getColumnDimension("N")->setWidth(11); 
$sheet->getColumnDimension("O")->setWidth(11); 
$sheet->getColumnDimension("P")->setWidth(13); 
$sheet->getColumnDimension("B")->setWidth(35); 

$sheet->getStyle("A1:Z1000")->applyFromArray($color);
$sheet->getStyle($tituloInformacionBasica)->applyFromArray($titulos);
$sheet->getStyle($subtitulosInformacionBasica)->applyFromArray($titulos2);
$sheet->getStyle($tablaInformacionBasica)->applyFromArray($infor);
$sheet->getStyle($tituloConsumos)->applyFromArray($titulos);
$sheet->getStyle($subtitulosConsumos)->applyFromArray($titulos2);
$sheet->getStyle($tablaConsumos)->applyFromArray($infor);

// exit();
$writer = new Xlsx($spreadsheet);
$writer->setIncludeCharts(TRUE);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="titular_derecho.xlsx"');
$writer->save('php://output','titular_derecho.xlsx');