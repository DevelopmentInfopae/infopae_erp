<?php 
require_once '../../config.php';
require_once '../../db/conexion.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

if (isset($_POST['idDispositivoexportar'])) {
	$iddispositivo = $_POST['idDispositivoexportar'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('B3', 'REFERENCIA : ');
$sheet->mergeCells('B3:D4');
$sheet->mergeCells('E3:G4');
$sheet->setCellValue('B6', 'N° DE SERIAL : ');
$sheet->mergeCells('B6:D7');
$sheet->mergeCells('E6:G7');
$sheet->setCellValue('B9', 'MUNICIPIO : ');
$sheet->mergeCells('B9:D10');
$sheet->mergeCells('E9:G10');
$sheet->setCellValue('I3', 'INSTITUCIÓN : ');
$sheet->mergeCells('I3:K4');
$sheet->mergeCells('L3:N4');
$sheet->setCellValue('I6', 'SEDE : ');
$sheet->mergeCells('I6:K7');
$sheet->mergeCells('L6:N7');
$sheet->setCellValue('I9', 'USUARIO : ');
$sheet->mergeCells('I9:K10');
$sheet->mergeCells('L9:N10');
$sheet->setCellValue('A13', 'TIPO DOCUMENTO');
$sheet->mergeCells('A13:C14');
$sheet->setCellValue('D13', 'N° DE DOCUMENTO');
$sheet->mergeCells('D13:F14');
$sheet->setCellValue('G13', 'NOMBRE ESTUDIANTE');
$sheet->mergeCells('G13:I14');
$sheet->setCellValue('J13', 'GRADO');
$sheet->mergeCells('J13:L14');
$sheet->setCellValue('M13', 'ID BIOMÉTRICO ESTUDIANTE');
$sheet->mergeCells('M13:O14');

$titulos = [
    'font' => [
        'bold' => true,
        'size'  => 7,
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

$color = [  
	'fill' => [
    	'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    	'color' => ['argb' => 'AEADAD'],
    	],
    ];

$sheet->getStyle("B3:D4")->applyFromArray($titulos)->applyFromArray($color);
$sheet->getStyle("B6:D7")->applyFromArray($titulos)->applyFromArray($color);
$sheet->getStyle("B9:D10")->applyFromArray($titulos)->applyFromArray($color);
$sheet->getStyle("I3:K4")->applyFromArray($titulos)->applyFromArray($color);
$sheet->getStyle("I6:K7")->applyFromArray($titulos)->applyFromArray($color);
$sheet->getStyle("I9:K10")->applyFromArray($titulos)->applyFromArray($color);

$sheet->getStyle("A13:O14")->applyFromArray($titulos)->applyFromArray($color);

$infor = [
    'font' => [
        'size'  => 7,
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

$sheet->getStyle("E3:G4")->applyFromArray($infor);
$sheet->getStyle("E6:G7")->applyFromArray($infor);
$sheet->getStyle("E9:G10")->applyFromArray($infor);
$sheet->getStyle("L3:N4")->applyFromArray($infor);
$sheet->getStyle("L6:N7")->applyFromArray($infor);
$sheet->getStyle("L9:N10")->applyFromArray($infor);




$consultaDispositivo = "SELECT usuarios.nombre as nom_usu, sedes".$_SESSION['periodoActual'].".nom_sede, ubicacion.Ciudad, instituciones.nom_inst, dispositivos.* FROM dispositivos INNER JOIN sedes".$_SESSION['periodoActual']." ON sedes".$_SESSION['periodoActual'].".cod_sede = dispositivos.cod_sede INNER JOIN ubicacion ON ubicacion.CodigoDANE = sedes".$_SESSION['periodoActual'].".cod_mun_sede INNER JOIN instituciones ON instituciones.codigo_inst = sedes".$_SESSION['periodoActual'].".cod_inst INNER JOIN usuarios ON usuarios.id = dispositivos.id_usuario WHERE dispositivos.id = ".$iddispositivo;
$resultadoDispositivo = $Link->query($consultaDispositivo);
if ($resultadoDispositivo->num_rows > 0) {
	$Dispositivo = $resultadoDispositivo->fetch_assoc();

	$sheet->setCellValue('E3', $Dispositivo['referencia']);
	$sheet->setCellValue('E6', $Dispositivo['num_serial']);
	$sheet->setCellValue('E9', $Dispositivo['Ciudad']);
	$sheet->setCellValue('L3', $Dispositivo['nom_inst']);
	$sheet->setCellValue('L6', $Dispositivo['nom_sede']);
	$sheet->setCellValue('L9', strtoupper($Dispositivo['nom_usu']));

}
				$focalizaciones = [];
               $consultarFocalizacion = "SELECT 
                                          table_name AS tabla
                                         FROM 
                                          information_schema.tables
                                         WHERE 
                                          table_schema = DATABASE() AND table_name like 'focalizacion%'";
               $resultadoFocalizacion = $Link->query($consultarFocalizacion);
               if ($resultadoFocalizacion->num_rows > 0) {
                 while ($focalizacion = $resultadoFocalizacion->fetch_assoc()) {
                   $focalizaciones[] = $focalizacion['tabla'];
                 }
               }


              if (strlen($iddispositivo) == 1) {
                 $idDisp = "00".$iddispositivo;
               } else if (strlen($iddispositivo) == 2) {
                 $idDisp = "0".$iddispositivo;
               } else if (strlen($iddispositivo) == 3) {
                 $idDisp = $iddispositivo;
               }
               $grados = [];
               $consultarGrados = "SELECT * FROM grados ";
               $resultadoGrados = $Link->query($consultarGrados);
               if ($resultadoGrados->num_rows > 0) {
                 while ($gradosInfo = $resultadoGrados->fetch_assoc()) {
                   $grados[$gradosInfo['id']] = $gradosInfo['nombre'];
                 }
               }

               $selectFoc = "";
               $sqlFoc = ""; 
               foreach ($focalizaciones as $focalizacion => $valor) {
                $selectFoc = " ".$valor.".nom1, ".$valor.".ape1, ".$valor.".cod_grado, ".$valor.".cod_sede, ";
                $sqlFoc.=" INNER JOIN ".$valor." ON ".$valor.".num_doc = biometria.num_doc ";
               }

                $consultarBiometria = "SELECT ".$selectFoc." tipodocumento.nombre as tdocnom, biometria.* FROM biometria INNER JOIN tipodocumento ON biometria.tipo_doc = tipodocumento.id ".$sqlFoc." WHERE id_dispositivo = ".$idDisp;
                $resultadoBiometria = $Link->query($consultarBiometria);
                $numfila = 15;
                if ($resultadoBiometria->num_rows > 0) {
                  	while ($biometria = $resultadoBiometria->fetch_assoc()) { 
	                    if ($biometria['cod_sede'] == $Dispositivo['cod_sede']) {
		                     $sheet->setCellValue("A".$numfila, $biometria['tdocnom']);
		                     $sheet->mergeCells("A".$numfila.":C".$numfila);
		                     $sheet->setCellValue("D".$numfila, $biometria['num_doc']);
		                     $sheet->mergeCells("D".$numfila.":F".$numfila);
		                     $sheet->setCellValue("G".$numfila, $biometria['nom1'] ." ".$biometria['ape1']);
		                     $sheet->mergeCells("G".$numfila.":I".$numfila);
		                     $sheet->setCellValue("J".$numfila, $grados[$biometria['cod_grado']]);
		                     $sheet->mergeCells("J".$numfila.":L".$numfila);
		                     $sheet->setCellValue("M".$numfila, $biometria['id_bioest']);
		                     $sheet->mergeCells("M".$numfila.":O".$numfila);
		                  $numfila++;
	                   }
               		}
           		} else {
           			$sheet->setCellValue("A".$numfila, "No hay biometrías registradas.");
           			$sheet->mergeCells("A".$numfila.":O".$numfila);
           		}

$nombreArchivo = strtoupper(str_replace(" ", "_", $Dispositivo['tipo'])."_".$Dispositivo['num_serial']."_".str_replace(" ", "_", $Dispositivo['referencia'])."_".str_replace(" ", "_", $Dispositivo['nom_sede']));
$spreadsheet->getActiveSheet()->setTitle($Dispositivo['referencia']." ".$Dispositivo['num_serial']);
$sheet->getStyle("A15:O".$numfila)->applyFromArray($infor);

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$nombreArchivo.'.xlsx"');
$writer->save('php://output',$nombreArchivo.'.xlsx');

} else {
	echo "Por favor defina el dispositivo desde <a href='index.php'>Aquí</a>";
}

