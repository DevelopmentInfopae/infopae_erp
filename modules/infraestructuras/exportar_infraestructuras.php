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



$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'FORMATO DIAGNÓSTICO DE INFRAESTRUCTURA');
$sheet->mergeCells('A1:K1');

$sheet->setCellValue('B3', 'RESPONSABLE DILIGENCIAMIENTO : ETC');
$sheet->mergeCells('B3:D3');
$sheet->mergeCells('E3:F3');

$sheet->setCellValue('G3', 'ETAPA DE PLANEACIÓN');
$sheet->mergeCells('G3:K3');

$sheet->setCellValue('A5', 'DATOS DE IDENTIFICACIÓN');
$sheet->mergeCells('A5:I5');
$sheet->setCellValue('J5', 'MODALIDAD DE SUMINISTRO');
$sheet->mergeCells('J5:K5');
$sheet->setCellValue('L5', 'CONCEPTO SANITARIO');
$sheet->mergeCells('L5:N5');
	$sheet->setCellValue('A6', 'NOMBRE DE LA ETC');
	$sheet->mergeCells('A6:A9');
	$sheet->setCellValue('B6', 'CÓDIGO DANE DEL MUNICIPIO');
	$sheet->mergeCells('B6:B9');
	$sheet->setCellValue('C6', 'NOMBRE DEL MUNICIPIO');
	$sheet->mergeCells('C6:C9');
	$sheet->setCellValue('D6', 'CÓDIGO DANE DEL ESTABLECIMIENTO EDUCATIVO');
	$sheet->mergeCells('D6:D9');
	$sheet->setCellValue('E6', 'NOMBRE DEL ESTABLECIMIENTO EDUCATIVO');
	$sheet->mergeCells('E6:E9');
	$sheet->setCellValue('F6', 'CÓDIGO DANE DE LA SEDE');
	$sheet->mergeCells('F6:F9');
	$sheet->setCellValue('G6', 'SEDE EDUCATIVA');
	$sheet->mergeCells('G6:G9');
	$sheet->setCellValue('H6', 'RURAL/URBANA');
	$sheet->mergeCells('H6:H9');
	$sheet->setCellValue('I6', 'ATENCIÓN A POBLACIÓN MAYORITARIAMENTE INDIGENA');
	$sheet->mergeCells('I6:I9');
	$sheet->setCellValue('J6', 'COMPLEMENTO JM/JT');
	$sheet->mergeCells('J6:J9');
	$sheet->setCellValue('K6', 'ALMUERZO');
	$sheet->mergeCells('K6:K9');
	$sheet->setCellValue('L6', 'INSTITUCIÓN EDUCATIVA CUENTA CON COMEDOR ESCOLAR');
	$sheet->mergeCells('L6:L9');
	$sheet->setCellValue('M6', 'CONCEPTO SANITARIO');
	$sheet->mergeCells('M6:M9');
	$sheet->setCellValue('N6', 'FECHA DE EXPEDICIÓN');
	$sheet->mergeCells('N6:N9');
	$sheet->setCellValue('BO5', 'OBSERVACIONES');
	$sheet->mergeCells('BO5:BO9');


$sheet->setCellValue('O5', 'PARÁMETROS');
$sheet->mergeCells('O5:BN5');
	$sheet->setCellValue('O6', 'ÁREA DE ALMACENAMIENTO');
	$sheet->mergeCells('O6:AC6');
		$sheet->setCellValue('O7', 'MATERIAL EN EL QUE SE ENCUENTRA CONSTRUIDO');
		$sheet->mergeCells('O7:R7');
			$sheet->setCellValue('O8', 'PISO');
			$sheet->mergeCells('O8:O9');
			$sheet->setCellValue('P8', 'PAREDES');
			$sheet->mergeCells('P8:P9');
			$sheet->setCellValue('Q8', 'TECHO');
			$sheet->mergeCells('Q8:Q9');
			$sheet->setCellValue('R8', 'MESONES');
			$sheet->mergeCells('R8:R9');
		$sheet->setCellValue('S7', 'EQUIPOS Y DOTACIÓN');
		$sheet->mergeCells('S7:AC7');
			$sheet->setCellValue('S8', 'NEVERA');
			$sheet->mergeCells('S8:V8');
				$sheet->setCellValue('S9', 'TIENE');
				$sheet->setCellValue('T9', 'EN USO');
				$sheet->setCellValue('U9', 'FUNCIONA');
				$sheet->setCellValue('V9', 'CAPACIDAD');
			$sheet->setCellValue('W8', 'CONGELADOR');
			$sheet->mergeCells('W8:Z8');
				$sheet->setCellValue('W9', 'TIENE');
				$sheet->setCellValue('X9', 'EN USO');
				$sheet->setCellValue('Y9', 'FUNCIONA');
				$sheet->setCellValue('Z9', 'CAPACIDAD');
			$sheet->setCellValue('AA8', 'ESTIBAS');
			$sheet->mergeCells('AA8:AA9');
			$sheet->setCellValue('AB8', 'RECIPIENTES PLÁSTICOS');
			$sheet->mergeCells('AB8:AB9');
			$sheet->setCellValue('AC8', 'CANASTILLAS');
			$sheet->mergeCells('AC8:AC9');
	$sheet->setCellValue('AD6', 'ÁREA DE PREPARACIÓN');
	$sheet->mergeCells('AD6:AR6');
		$sheet->setCellValue('AD7', 'MATERIAL EN EL QUE SE ENCUENTRA CONSTRUIDO');
		$sheet->mergeCells('AD7:AG7');
			$sheet->setCellValue('AD8', 'PISO');
			$sheet->mergeCells('AD8:AD9');
			$sheet->setCellValue('AE8', 'PAREDES');
			$sheet->mergeCells('AE8:AE9');
			$sheet->setCellValue('AF8', 'TECHO');
			$sheet->mergeCells('AF8:AF9');
			$sheet->setCellValue('AG8', 'MESONES');
			$sheet->mergeCells('AG8:AG9');
		$sheet->setCellValue('AH7', 'EQUIPOS Y UTENSILIOS');
		$sheet->mergeCells('AH7:AR7');
			$sheet->setCellValue('AH8', 'ESTUFA');
			$sheet->mergeCells('AH8:AL8');
				$sheet->setCellValue('AH9', 'TIENE');
				$sheet->setCellValue('AI9', 'TIPO');
				$sheet->setCellValue('AJ9', 'EN USO');
				$sheet->setCellValue('AK9', 'FUNCIONA');
				$sheet->setCellValue('AL9', 'CAPACIDAD');
			$sheet->setCellValue('AM8', 'LICUADORA');
			$sheet->mergeCells('AM8:AQ8');
				$sheet->setCellValue('AM9', 'TIENE');
				$sheet->setCellValue('AN9', 'TIPO');
				$sheet->setCellValue('AO9', 'EN USO');
				$sheet->setCellValue('AP9', 'FUNCIONA');
				$sheet->setCellValue('AQ9', 'CAPACIDAD');
			$sheet->setCellValue('AR8', 'CUENTA CON LOS UTENSILIOS SUFICIENTES PARA LA PREPARACION DE LOS ALIMENTOS');
			$sheet->mergeCells('AR8:AR9');
	$sheet->setCellValue('AS6', 'ÁREA DE CONSUMO');
	$sheet->mergeCells('AS6:AY6');
		$sheet->setCellValue('AS7', 'MATERIAL EN EL QUE SE ENCUENTRA CONSTRUIDO');
		$sheet->mergeCells('AS7:AU7');
			$sheet->setCellValue('AS8', 'PISO');
			$sheet->mergeCells('AS8:AS9');
			$sheet->setCellValue('AT8', 'PAREDES');
			$sheet->mergeCells('AT8:AT9');
			$sheet->setCellValue('AU8', 'TECHO');
			$sheet->mergeCells('AU8:AU9');
		$sheet->setCellValue('AV7', 'DOTACION');
		$sheet->mergeCells('AV7:AY7');
			$sheet->setCellValue('AV8', 'MESAS');
			$sheet->mergeCells('AV8:AV9');
			$sheet->setCellValue('AW8', 'SILLAS');
			$sheet->mergeCells('AW8:AW9');
			$sheet->setCellValue('AX8', 'LA CANTIDAD DE MESAS Y SILLAS ES SUFICIENTES PARA LA ATENCIÓN DEL PROGRAMA');
			$sheet->mergeCells('AX8:AX9');
			$sheet->setCellValue('AY8', 'CUENTA CON LOS UTENSILIOS SUFICIENTES PARA EL CONSUMO DE LOS ALIMENTOS');
			$sheet->mergeCells('AY8:AY9');
	$sheet->setCellValue('AZ6', 'SERVICIOS PÚBLICOS');
	$sheet->mergeCells('AZ6:BE6');
		$sheet->setCellValue('AZ7', 'ENERGÍA');
		$sheet->mergeCells('AZ7:AZ9');
		$sheet->setCellValue('BA7', 'AGUA APTA PARA CONSUMO');
		$sheet->mergeCells('BA7:BA9');
		$sheet->setCellValue('BB7', 'ACUEDUCTO');
		$sheet->mergeCells('BB7:BB9');
		$sheet->setCellValue('BC7', 'GAS');
		$sheet->mergeCells('BC7:BC9');
		$sheet->setCellValue('BD7', 'ALCANTARILLADO');
		$sheet->mergeCells('BD7:BD9');
		$sheet->setCellValue('BE7', ' ALMACENAMIENTO DE AGUA');
		$sheet->mergeCells('BE7:BE9');
	$sheet->setCellValue('BF6', 'MANEJO Y DISPOSICIÓN DE RESIDUOS');
	$sheet->mergeCells('BF6:BH6');
		$sheet->setCellValue('BF7', 'CUENTA CON CANECAS');
		$sheet->mergeCells('BF7:BF9');
		$sheet->setCellValue('BG7', 'CUENTA CON ÁREA DE ALMACENAMIENTO TEMPORAL');
		$sheet->mergeCells('BG7:BG9');
		$sheet->setCellValue('BH7', 'TIPO DE DISPOSICIÓN FINAL DE LOS RESIDUOS');
		$sheet->mergeCells('BH7:BH9');
	$sheet->setCellValue('BI6', 'SERVICIOS SANITARIOS');
	$sheet->mergeCells('BI6:BN6');
		$sheet->setCellValue('BI7', 'CUENTA CON ÁREA PARA EL LAVADO DE MANOS DE LOS BENEFICIARIOS');
		$sheet->mergeCells('BI7:BI9');
		$sheet->setCellValue('BJ7', 'ESTADO');
		$sheet->mergeCells('BJ7:BJ9');
		$sheet->setCellValue('BK7', 'DOTADO CON IMPLEMENTOS DE ASEO NECESARIOS');
		$sheet->mergeCells('BK7:BK9');
		$sheet->setCellValue('BL7', 'CUENTA CON BAÑO EXCLUSIVO PARA PERSONAL DE MANIPULACIÓN DE ALIMENTOS');
		$sheet->mergeCells('BL7:BL9');
		$sheet->setCellValue('BM7', 'ESTADO');
		$sheet->mergeCells('BM7:BM9');
		$sheet->setCellValue('BN7', 'DOTADO CON IMPLEMENTOS DE ASEO NECESARIOS');
		$sheet->mergeCells('BN7:BN9');

for ($i="A"; $i <= "N" ; $i++) { 
	$sheet->getColumnDimension($i)->setWidth(25);
}

for ($j=1; $j <= 9 ; $j++) { 
	$sheet->getRowDimension($j)->setRowHeight(20);
}

$Cantidad_de_columnas_a_crear=66; 
$Contador=14; 
$Letra='O'; 
while($Contador<$Cantidad_de_columnas_a_crear) 
{ 
    $sheet->getColumnDimension($Letra)->setWidth(15); 
    $Contador++; 
    $Letra++; 
}

$sheet->getColumnDimension("BO")->setWidth(75); 

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

$sheet->getStyle("A1:K1")->applyFromArray($titulos);
$sheet->getStyle("B3:D3")->applyFromArray($titulos);
$sheet->getStyle("G3:K3")->applyFromArray($titulos);
$sheet->getStyle("A5:BO9")->applyFromArray($titulos);

$color = [    
	'font' => [
		'color' => ['argb' => 'FFFFFF'],
    ],
	'fill' => [
    	'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    	'color' => ['argb' => 'AEADAD'],
    	],
    ];

$sheet->getStyle("A5:BO9")->applyFromArray($color);

$numfila = 10;

$sectores = array('1' => 'Rural', '2' => 'Urbano', '0' => 'No especificado.');
$conceptos_sanitario = array('1' => 'Favorable', '2' => 'Favorable con requerimiento','0' => 'Desfavorable');
$estados = array('1' => 'Si', '0' => 'No', '2' => 'No aplica');

$consultarDptoParametro = "SELECT id, nombre FROM departamentos WHERE EXISTS (SELECT CodDepartamento FROM parametros WHERE CodDepartamento = departamentos.id)";
$resultadoDptoParametro = $Link->query($consultarDptoParametro);
if ($resultadoDptoParametro->num_rows > 0) {
	if ($dpto = $resultadoDptoParametro->fetch_assoc()) {
		$nomDpto = $dpto['nombre'];
	}
}

$municipios = array();
$consultaMunicipios = "SELECT DISTINCT
                            ubicacion.CodigoDANE, ubicacion.Ciudad
                        FROM
                            ubicacion,
                            parametros
                        WHERE
                            ubicacion.CodigoDane LIKE CONCAT(parametros.CodDepartamento, '%')
                                AND EXISTS( SELECT DISTINCT
                                    cod_mun
                                FROM
                                    instituciones
                                WHERE
                                    cod_mun = ubicacion.CodigoDANE)
                        ORDER BY ubicacion.Ciudad ASC";
$resultadoMunicipios = $Link->query($consultaMunicipios);
if ($resultadoMunicipios->num_rows > 0) {
	while ($municipio = $resultadoMunicipios->fetch_assoc()) {
		$municipios[$municipio['CodigoDANE']] = $municipio['Ciudad'];
	}
}

$modalidades = array();
$consultarModalidad = "SELECT * FROM modalidad_suministro;";
$resultadoModalidad = $Link->query($consultarModalidad);
if ($resultadoModalidad->num_rows > 0) {
	while ($row = $resultadoModalidad->fetch_assoc()) {
	  $modalidades[$row['id']] = $row['Descripcion'];
	}
}

$consultarInfraestructuras = "SELECT * FROM Infraestructura";
$resultadoInfraestructuras = $Link->query($consultarInfraestructuras);
if ($resultadoInfraestructuras->num_rows>0) {
	while ($Infraestructura = $resultadoInfraestructuras->fetch_assoc()) {

		$datosInstitucion = "SELECT instituciones.nom_inst, sedes".$_SESSION['periodoActual'].".nom_sede, sedes".$_SESSION['periodoActual'].".cod_mun_sede, sedes".$_SESSION['periodoActual'].".sector FROM instituciones, sedes".$_SESSION['periodoActual']." WHERE instituciones.codigo_inst = ".$Infraestructura['cod_inst']." AND sedes".$_SESSION['periodoActual'].".cod_sede = ".$Infraestructura['cod_sede'];
		$resultadoInstitucion = $Link->query($datosInstitucion);
		if ($resultadoInstitucion->num_rows > 0) {
			if ($Institucion = $resultadoInstitucion->fetch_assoc()) {
				$nom_sede = $Institucion['nom_sede'];
				$nom_inst = $Institucion['nom_inst'];
				$cod_mun = $Institucion['cod_mun_sede'];
				$sector_sede = $Institucion['sector'];
			}
		}

		$sheet->setCellValue('A'.$numfila, strtoupper($nomDpto));
		$sheet->setCellValue('B'.$numfila, $cod_mun);
		$sheet->setCellValue('C'.$numfila, $municipios[$cod_mun]);
		$sheet->setCellValue('D'.$numfila, $Infraestructura['cod_inst']);
		$sheet->setCellValue('E'.$numfila, $nom_inst);
		$sheet->setCellValue('F'.$numfila, $Infraestructura['cod_sede']);
		$sheet->setCellValue('G'.$numfila, $nom_sede);
		$sheet->setCellValue('H'.$numfila, $sectores[$sector_sede]);
		$sheet->setCellValue('I'.$numfila, $estados[$Infraestructura['Atencion_MayoritariaI']]);
		$sheet->setCellValue('J'.$numfila, $modalidades[$Infraestructura['id_Complem_JMJT']]);
		$sheet->setCellValue('K'.$numfila, $modalidades[$Infraestructura['id_Almuerzo']]);
		$sheet->setCellValue('L'.$numfila, $estados[$Infraestructura['Comedor_Escolar']]);
		$sheet->setCellValue('M'.$numfila, $conceptos_sanitario[$Infraestructura['Concepto_Sanitario']]);
		$sheet->setCellValue('N'.$numfila, $Infraestructura['Fecha_Expe']);
		$sheet->setCellValue('BO'.$numfila, $Infraestructura['observaciones']);

		$consultarParametros = "SELECT * FROM valores_param_infraestructura WHERE cod_infraestructura = ".$Infraestructura['id'];
		$resultadoParametros = $Link->query($consultarParametros);
		if ($resultadoParametros->num_rows > 0) {
			while ($valoresParametros = $resultadoParametros->fetch_assoc()) {
				switch ($valoresParametros['cod_parametrosInf']) {
					case '1':
						$sheet->setCellValue('O'.$numfila, $valoresParametros['piso']);
						$sheet->setCellValue('P'.$numfila, $valoresParametros['paredes']);
						$sheet->setCellValue('Q'.$numfila, $valoresParametros['techo']);
						$sheet->setCellValue('R'.$numfila, $valoresParametros['mesones']);
						break;
					case '2':
						$sheet->setCellValue('AD'.$numfila, $valoresParametros['piso']);
						$sheet->setCellValue('AE'.$numfila, $valoresParametros['paredes']);
						$sheet->setCellValue('AF'.$numfila, $valoresParametros['techo']);
						$sheet->setCellValue('AG'.$numfila, $valoresParametros['mesones']);
						$sheet->setCellValue('AR'.$numfila, $estados[$valoresParametros['utensilios_suf']]);
						break;
					case '3':
						$sheet->setCellValue('AS'.$numfila, $valoresParametros['piso']);
						$sheet->setCellValue('AT'.$numfila, $valoresParametros['paredes']);
						$sheet->setCellValue('AU'.$numfila, $valoresParametros['techo']);
						$sheet->setCellValue('AX'.$numfila, $estados[$valoresParametros['cant_mesasillas_suf']]);
						$sheet->setCellValue('AY'.$numfila, $estados[$valoresParametros['utensilios_suf']]);
						break;
					case '4':
						$sheet->setCellValue('AZ'.$numfila, $valoresParametros['energia']);
						$sheet->setCellValue('BA'.$numfila, $valoresParametros['agua']);
						$sheet->setCellValue('BB'.$numfila, $estados[$valoresParametros['acueducto']]);
						$sheet->setCellValue('BC'.$numfila, $estados[$valoresParametros['gas']]);
						$sheet->setCellValue('BD'.$numfila, $estados[$valoresParametros['alcantarillado']]);
						$sheet->setCellValue('BE'.$numfila, $valoresParametros['alm_agua']);
						break;
					case '5':
						$sheet->setCellValue('BG'.$numfila, $estados[$valoresParametros['area_alm']]);
						$sheet->setCellValue('BH'.$numfila, $valoresParametros['final_residuos']);
						break;
					case '6':
						$sheet->setCellValue('BI'.$numfila, $estados[$valoresParametros['lavado_manos']]);
						$sheet->setCellValue('BJ'.$numfila, $valoresParametros['estado_lavadomanos']);
						$sheet->setCellValue('BK'.$numfila, $estados[$valoresParametros['manos_implemento_aseo']]);
						$sheet->setCellValue('BL'.$numfila, $estados[$valoresParametros['bano_manipuladoras']]);
						$sheet->setCellValue('BM'.$numfila, $valoresParametros['estado_bano']);
						$sheet->setCellValue('BN'.$numfila, $estados[$valoresParametros['bano_implemento_aseo']]);
						break;
				}
			}
		}

		$consultarDotaciones = "SELECT * FROM dotacion_param_val  WHERE cod_infraestructura = ".$Infraestructura['id'];
		$resultadoDotaciones = $Link->query($consultarDotaciones);
		if ($resultadoDotaciones->num_rows > 0) {
			while ($valoresDotacion = $resultadoDotaciones->fetch_assoc()) {
				switch ($valoresDotacion['cod_dotacion']) {
					case '1':
						$sheet->setCellValue('S'.$numfila, $estados[$valoresDotacion['tiene']]);
						$sheet->setCellValue('T'.$numfila, $estados[$valoresDotacion['enuso']]);
						$sheet->setCellValue('U'.$numfila, $estados[$valoresDotacion['funciona']]);
						$sheet->setCellValue('V'.$numfila, $valoresDotacion['capacidad']);
						break;
					case '2':
						$sheet->setCellValue('W'.$numfila, $estados[$valoresDotacion['tiene']]);
						$sheet->setCellValue('X'.$numfila, $estados[$valoresDotacion['enuso']]);
						$sheet->setCellValue('Y'.$numfila, $estados[$valoresDotacion['funciona']]);
						$sheet->setCellValue('Z'.$numfila, $valoresDotacion['capacidad']);
						break;
					case '3':
						$sheet->setCellValue('AA'.$numfila, $estados[$valoresDotacion['tiene']]);
						break;
					case '4':
						$sheet->setCellValue('AB'.$numfila, $estados[$valoresDotacion['tiene']]);
						break;
					case '5':
						$sheet->setCellValue('AC'.$numfila, $estados[$valoresDotacion['tiene']]);
						break;
					case '6':
						$sheet->setCellValue('AH'.$numfila, $estados[$valoresDotacion['tiene']]);
						$sheet->setCellValue('AI'.$numfila, $valoresDotacion['tipo']);
						$sheet->setCellValue('AJ'.$numfila, $estados[$valoresDotacion['enuso']]);
						$sheet->setCellValue('AK'.$numfila, $estados[$valoresDotacion['funciona']]);
						$sheet->setCellValue('AL'.$numfila, $valoresDotacion['capacidad']);
						break;
					case '7':
						$sheet->setCellValue('AM'.$numfila, $estados[$valoresDotacion['tiene']]);
						$sheet->setCellValue('AN'.$numfila, $valoresDotacion['tipo']);
						$sheet->setCellValue('AO'.$numfila, $estados[$valoresDotacion['enuso']]);
						$sheet->setCellValue('AP'.$numfila, $estados[$valoresDotacion['funciona']]);
						$sheet->setCellValue('AQ'.$numfila, $valoresDotacion['capacidad']);
						break;
					case '8':
						$sheet->setCellValue('AV'.$numfila, $estados[$valoresDotacion['tiene']]);
						break;
					case '9':
						$sheet->setCellValue('AW'.$numfila, $estados[$valoresDotacion['tiene']]);
						break;
					case '10':
						$sheet->setCellValue('BF'.$numfila, $estados[$valoresDotacion['tiene']]);
						break;
				}
			}
		}

		$numfila++;
	}
}


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

$sheet->getStyle("A10:BO".$numfila)->applyFromArray($infor);

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Infraestructuras.xlsx"');
$writer->save('php://output','Infraestructuras.xlsx');

 ?>