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

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

// creamos un nuevo libro de trabajo
$spreadsheet = new Spreadsheet();

// accedemos a la hoja activa de ese libro 
$sheet = $spreadsheet->getActiveSheet();

// array para nombre de los meses
$nombreMeses = array(   '01' => 'Enero', 
                        '02' => 'Febrero', 
                        '03' => 'Marzo', 
                        '04' => 'Abril', 
                        '05' => 'Mayo', 
                        '06' => 'Junio', 
                        '07' => 'Julio', 
                        '08' => 'Agosto', 
                        '09' => 'Septiembre', 
                        '10' => 'Octubre', 
                        '11' => 'Noviembre', 
                        '12' => 'Diciembre');

// array para agregar letras del abecedario si crece alguna tabla o grafica agregar las letras en el siguiente array
$letrasAbecedario = array(  '1'=>'A', '2'=>'B', '3'=>'C', '4'=>'D', '5'=>'E', '6'=>'F', '7'=>'G', '8'=>'H', '9'=>'I', '10'=>'J', '11'=>'K', '12'=>'L', '13'=>'M',
                            '14'=>'N', '15'=>'0', '16'=>'P', '17'=>'Q', '18'=>'R', '19'=>'S', '20'=>'T', '21'=>'U', '22'=>'V', '23'=>'W', '24'=>'X', '25'=>'Y', 
                            '26'=>'Z', '27'=>'AA', '28'=>'AB', '29'=>'AC', '30'=>'AD', '31'=>'AE', '32'=>'AF', '33'=>'AG', '34'=>'AH', '35'=>'AI', '36'=>'AJ', '37'=>'AK', 
                            '38'=>'AL', '39'=>'AM', '40'=>'AN', '41'=>'A0', '42'=>'AP', '43'=>'AQ', '44'=>'AR', '45'=>'AS', '46'=>'AT', '47'=>'AU', '48'=>'AV', '49'=>'AW', '50'=>'AX', '51'=>'AY', '52'=>'AZ',);

// arrays de estilos 
$titulos1 = [
    'font' => [
        'bold' => true,
        'size'  => 12,
        'name' => 'calibrí',
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
        'size'  => 9,
        'name' => 'calibrí',
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

$infor2 = [
    'font' => [
        'size'  => 9,
        'name' => 'calibrí'
    ],
    'alignment' => [
        'wrapText' => true,
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ],
];

$borderRight = [
    'borders' => [
        'diagonalDirection' => Borders::DIAGONAL_BOTH,
        'right' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
];

$borderBot = [
   'borders' => [
   'diagonalDirection' => Borders::DIAGONAL_BOTH,
      'bottom' => [
         'borderStyle' => Border::BORDER_THIN,
      ],
   ],
];

// declaracion de variables
$periodoActual = $_SESSION['periodoActual'];
$cantGruposEtarios = $_SESSION['cant_gruposEtarios'];
if ($cantGruposEtarios == 5) {
   $mes = $_POST['mesiConsulta'];
   if($mes < 10){
      $mes = '0'.$mes;
   }
   $mes = trim($mes);
   $anno = $_POST['annoi'];
   $anno = substr($anno, -2);
   $anno = trim($anno);
   $mesAnno = $mes.$anno;
   $corteDeVariables = 16;
   if(isset($_POST['seleccionarVarios'])){
      $corteDeVariables++;
   }
   if(isset($_POST['informeRuta'])){
      $corteDeVariables++;
   }
   if(isset($_POST['ruta'])){
      $corteDeVariables++;
   }
   if(isset($_POST['rutaNm'])){
      $corteDeVariables++;
   }
   if(isset($_POST['paginasObservaciones'])){
      $paginasObservaciones = $_POST['paginasObservaciones'];
      $corteDeVariables++;
   }
   $imprimirMes = 0;
   if(isset($_POST['imprimirMes'])){
      if($_POST['imprimirMes'] == 'on'){
         $imprimirMes = 1;   
      }
      $corteDeVariables++;
   }
   $_SESSION['observacionesDespachos'] = "";
   if(isset($_POST['observaciones'])){
      if($_POST['observaciones'] != ""){
         $_SESSION['observacionesDespachos'] = $_POST['observaciones'];
      }
      $corteDeVariables++;
   }
   $despachos = array_slice($_POST, $corteDeVariables);
   $despachos = array_values($despachos);

   $sheet->setCellValue('K2', 'PROGRAMA DE ALIMENTACIÓN ESCOLAR');
   $sheet->mergeCells('K2:U4');
   $sheet->setCellValue('K5', "REMISIÓN ENTREGA DE VÍVERES EN INSTITUCIÓN EDUCATIVA");
   $sheet->mergeCells('K5:U7');
   $sheet->mergeCells('B2:J7');

   $sheet->getStyle("K2:U4")->applyFromArray($titulos1);
   $sheet->getStyle("K5:U7")->applyFromArray($titulos1);
   $sheet->getStyle('B2:J7')->applyFromArray($titulos1);
      
   $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
   $logoInfopae = $_SESSION['p_Logo ETC']; 
   $drawing->setName('LogoOperador');
   $drawing->setDescription('LogoOperador');
   $drawing->setPath($logoInfopae);
   $drawing->setHeight(90);
   $drawing->setCoordinates('B2');
   $drawing->setOffsetX(25);
   $drawing->setOffsetY(17);
   $drawing->setWorksheet($spreadsheet->getActiveSheet());

   $linea = 9;
   foreach ($despachos as $key => $despacho) {
      unset($sedes);
      unset($items);
      unset($menus);
      unset($sedesCobertura);
      unset($complementosCantidades);
      unset($coberturaPorGrupo);
      $ciclo = '';
      $consulta = "  SELECT de.*, 
                              tc.descripcion, 
                              s.nom_sede, 
                              s.nom_inst, 
                              u.Ciudad, 
                              td.Descripcion as tipoDespachoNm, 
                              tc.jornada
                           FROM despachos_enc$mesAnno de
                           LEFT JOIN sedes$anno s on de.cod_sede = s.cod_sede
                           LEFT JOIN ubicacion u on s.cod_mun_sede = u.CodigoDANE
                           LEFT JOIN tipo_complemento tc on de.Tipo_Complem = tc.CODIGO
                           LEFT JOIN tipo_despacho td on de.TipoDespacho = td.Id
                           WHERE de.Num_Doc = $despacho ";

      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      if($resultado->num_rows >= 1){
         $row = $resultado->fetch_assoc();
         $municipio = $row['Ciudad'];
         $institucion = $row['nom_inst'];
         $sede = $row['nom_sede'];
         $codSede = $row['cod_Sede'];
         $semana = $row['Semana'];
         $cobertura = $row['Cobertura'];
         $modalidad = $row['Tipo_Complem'];
         $descripcionTipo = $row['descripcion'];
         $tipoDespachoNm = $row['tipoDespachoNm'];
         $jornada = $row['jornada'];
         $fechaDespacho = $row['FechaHora_Elab'];
         $fechaDespacho = strtotime($fechaDespacho);
         $fechaDespacho = date("d/m/Y",$fechaDespacho);
         $auxDias = $row['Dias'];
         $diasDespacho = $row['Dias'];
         $auxDias = str_replace(",", ", ", $auxDias);
         $auxMenus = $row['Menus'];
         $auxMenus = str_replace(",", ", ", $auxMenus);
         $tipo = $modalidad;
         $cantSedeGrupo1 = $sedeGrupo1 = $coberturaPorGrupo[] = $row['Cobertura_G1'];
         $cantSedeGrupo2 = $sedeGrupo2 = $coberturaPorGrupo[] = $row['Cobertura_G2'];
         $cantSedeGrupo3 = $sedeGrupo3 = $coberturaPorGrupo[] = $row['Cobertura_G3'];
         $cantSedeGrupo4 = $sedeGrupo4 = $coberturaPorGrupo[] = $row['Cobertura_G4'];
         $cantSedeGrupo5 = $sedeGrupo5 = $coberturaPorGrupo[] = $row['Cobertura_G5'];
         $sedes[] = $codSede;
      }
      // var_dump($coberturaPorGrupo);
      $consGrupoEtario = " SELECT Descripcion FROM grupo_etario ";
      $resGrupoEtario = $Link->query($consGrupoEtario);
      if ($resGrupoEtario->num_rows > 0) {
         while ($ge = $resGrupoEtario->fetch_assoc()) {
            $get[] = $ge['Descripcion'];
         }
      } 

      $semanasIn = explode(',',$semana);
      $semanaIn = '';
      foreach ($semanasIn as $key => $value) {
         $semanaIn .= "'".trim($value)."',";
      }
      $semanaIn = trim($semanaIn,','); 

      // Iniciando la busqueda de los días que corresponden a esta semana de contrato.
      $arrayDiasDespacho = explode(',', $diasDespacho);
      $dias = '';
      $consulta = " SELECT * FROM planilla_semanas WHERE SEMANA IN ($semanaIn) "; 
      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      if($resultado->num_rows >= 1){
         $mesInicial = '';
         $mesesIniciales = 0;
         $bandera = 0;
         while($row = $resultado->fetch_assoc()){
            $clave = array_search(intval($row['DIA']), $arrayDiasDespacho);
            if($clave !== false){
               if($bandera != $row['CICLO']){
                  $ciclo .= $row['CICLO'] .', ';
                  $bandera = $row['CICLO'];
               }
               if($mesInicial != $row['MES']){
                  $mesesIniciales++;
                  if($mesesIniciales > 1){
                     $dias .= " de  $mes ";
                  }
                  $mesInicial = $row['MES'];
                  $mes = $row['MES'];
                  $mes = $nombreMeses[$mes];
               }else{
                  if($dias != ''){
                     $dias .= ', ';
                  }
               }
               $dias = $dias.intval($row['DIA']);
            }
         }
         $ciclo = trim($ciclo, ', ');
         $dias .= " de  $mes";
      }else {
         $dias = $diasDespacho;
         $nombreTabla = 'despachos_enc'.$mesAnno;
         $mesTabla = substr($nombreTabla,13,-2); 
         $dias .= " DE " . $nombreMeses[$mesTabla];
      }

      $cantDias = explode(',', $diasDespacho);
      $cantDias = count($cantDias);
      $auxDias = "X ".$cantDias." DIAS ".strtoupper($dias);

      $jm = $jt = 0;
      if($jornada == 2){
         $jm = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3 + $sedeGrupo4 + $sedeGrupo5;
      }else if($jornada == 3){
         $jt = $sedeGrupo1 + $sedeGrupo2 + $sedeGrupo3 + $sedeGrupo4 + $sedeGrupo5;
      }

      $etario_1 = str_replace(" + 11 meses", "", $get[0]);
      $etario_2 = str_replace(" + 11 meses", "", $get[1]);
      $etario_3 = str_replace(" + 11 meses", "", $get[2]);
      $etario_4 = str_replace(" + 11 meses", "", $get[3]);
      $etario_5 = str_replace(" + 11 meses", "", $get[4]);

      $etarioNom[] = $etario_1 = str_replace(" años", "", $etario_1) . " AÑOS";
      $etarioNom[] = $etario_2 = str_replace(" años", "", $etario_2) . " AÑOS";
      $etarioNom[] = $etario_3 = str_replace(" años", "", $etario_3) . " AÑOS";
      $etarioNom[] = $etario_4 = str_replace(" años", "", $etario_4) . " AÑOS";
      $etarioNom[] = $etario_5 = str_replace(" años", "", $etario_5) . " AÑOS";

      $alimentos = array();
      $consulta = "  SELECT DISTINCT cod_alimento
                        FROM despachos_det$mesAnno
                        WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho
                        ORDER BY cod_alimento ASC ";

      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      if($resultado->num_rows >= 1){
         while($row = $resultado->fetch_assoc()){
            $alimento = array();
            $alimento['codigo'] = $row['cod_alimento'];
            $alimentos[] = $alimento;
         }
      }

      for ($i=0; $i < count($alimentos) ; $i++) {
         $auxCodigo = $alimentos[$i]['codigo'];
         $consulta = " SELECT distinct p.Codigo,
                              p.Descripcion AS Componente,
                              p.nombreunidad2 presentacion,
                              p.cantidadund1 cantidadPresentacion,
                              m.grupo_alim, m.orden_grupo_alim, ftd.UnidadMedida,
                              (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 1 ) as cant_grupo1,
                              (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 2 ) as cant_grupo2,
                              (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 3) as cant_grupo3,
                              (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 4) as cant_grupo4,
                              (SELECT Cantidad FROM despachos_det$mesAnno WHERE Tipo_Doc = 'DES' and Num_Doc = $despacho and cod_Alimento = $auxCodigo and Id_GrupoEtario = 5) as cant_grupo5,
                              (SELECT cantu2 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu2,
                              (SELECT cantu3 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu3,
                              (SELECT cantu4 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu4,
                              (SELECT cantu5 FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantu5,
                              (SELECT Umedida FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS Umedida,
                              (SELECT cantotalpresentacion FROM productosmovdet$mesAnno WHERE Documento = 'DES' AND Numero = $despacho AND CodigoProducto = $auxCodigo limit 1 ) AS cantotalpresentacion,
                              (SELECT Redondeo FROM tipo_despacho WHERE Id = p.TipoDespacho) AS redondeo,
                              p.cantidadund2,
                              p.cantidadund3,
                              p.cantidadund4,
                              p.cantidadund5,
                              p.nombreunidad2,
                              p.nombreunidad3,  
                              p.nombreunidad4,
                              p.nombreunidad5
                           FROM productos$anno p
                           LEFT JOIN fichatecnicadet ftd ON ftd.codigo=p.Codigo
                           INNER JOIN menu_aportes_calynut m ON p.Codigo = m.cod_prod
                           WHERE p.Codigo = $auxCodigo
                           ORDER BY m.orden_grupo_alim ASC, p.Descripcion DESC ";

         $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
         if($resultado->num_rows >= 1){
            $alimento['cant_total'] = 0;
            $alimento['cant_grupo1'] = 0;
            $alimento['cant_grupo2'] = 0;
            $alimento['cant_grupo3'] = 0;
            $alimento['cant_grupo4'] = 0;
            $alimento['cant_grupo5'] = 0;
            $alimento['grupo_alim'] = '';
            $alimento['componente'] = '';
            $alimento['presentacion'] = '';
            while($row = $resultado->fetch_assoc()){
               $alimento['componente'] = $row['Componente'];
               $alimento['presentacion'] = $row['Umedida'];
               $alimento['cantidadpresentacion'] = $row['cantidadPresentacion'];
               $alimento['grupo_alim'] = $row['grupo_alim'];
               $alimento['orden_grupo_alim'] = $row['orden_grupo_alim'];
               $alimento['cant_grupo1'] = $row['cant_grupo1'];
               $alimento['cant_grupo2'] = $row['cant_grupo2'];
               $alimento['cant_grupo3'] = $row['cant_grupo3'];
               $alimento['cant_grupo4'] = $row['cant_grupo4'];
               $alimento['cant_grupo5'] = $row['cant_grupo5'];
               $alimento['cant_total'] = $row['cant_grupo1'] + $row['cant_grupo2'] + $row['cant_grupo3'] + $row['cant_grupo4'] + $row['cant_grupo5'];
               $alimento['cantu2'] = $row['cantu2'];
               $alimento['cantu3'] = $row['cantu3'];
               $alimento['cantu4'] = $row['cantu4'];
               $alimento['cantu5'] = $row['cantu5'];
               $alimento['redondeo'] = $row['redondeo'];
               $alimento['cantotalpresentacion'] = $row['cantotalpresentacion'];
               $alimento['cantidadund2'] = $row['cantidadund2'];
               $alimento['cantidadund3'] = $row['cantidadund3'];
               $alimento['cantidadund4'] = $row['cantidadund4'];
               $alimento['cantidadund5'] = $row['cantidadund5'];
               $alimento['nombreunidad2'] = $row['nombreunidad2'];
               $alimento['nombreunidad3'] = $row['nombreunidad3'];
               $alimento['nombreunidad4'] = $row['nombreunidad4'];
               $alimento['nombreunidad5'] = $row['nombreunidad5'];
               if($row['cant_grupo1'] > 0){
                  $sedeGrupo1 = $cantSedeGrupo1;
               }
               if($row['cant_grupo2'] > 0){
                  $sedeGrupo2 = $cantSedeGrupo2;
               }
               if($row['cant_grupo3'] > 0){
                  $sedeGrupo3 = $cantSedeGrupo3;
               }
               if($row['cant_grupo4'] > 0){
                  $sedeGrupo4 = $cantSedeGrupo4;
               }
               if($row['cant_grupo5'] > 0){
                  $sedeGrupo5 = $cantSedeGrupo5;
               }
            }
         }
         $alimentos[$i] = $alimento;
      }

      /*************************************************************/
      unset($sort);
      unset($grupo);
      $sort = array();
      $grupo = array();
      foreach($alimentos as $kOrden=>$vOrden) {
         $sort['componente'][$kOrden] = $vOrden['componente'];
         $sort['grupo_alim'][$kOrden] = $vOrden['orden_grupo_alim']; //Se cambia el orden de acuerdo al orden por grupo de alimento
         $sort['cantidadpresentacion'][$kOrden] = $vOrden['cantidadpresentacion'];
         $grupo[$kOrden] = $vOrden['grupo_alim'];
      }
      array_multisort($sort['grupo_alim'], SORT_ASC,$alimentos);
      sort($grupo);
      /*************************************************************/

                       
      // primera fila del formato
      $sheet->setCellValue("B$linea", 'OPERADOR: ');
      $sheet->mergeCells("B$linea:C$linea");
      $sheet->getStyle("B$linea:C$linea")->applyFromArray($titulos2);
      $sheet->setCellValue("D$linea", $_SESSION['p_Operador']);
      $sheet->mergeCells("D$linea:K$linea");
      $sheet->getStyle("D$linea:K$linea")->applyFromArray($infor);
      $sheet->setCellValue("L$linea", 'FECHA DE ELABORACIÓN: ' );
      $sheet->mergeCells("L$linea:N$linea");
      $sheet->getStyle("L$linea:N$linea")->applyFromArray($titulos2);
      $sheet->setCellValue("O$linea", $fechaDespacho);
      $sheet->mergeCells("O$linea:U$linea");
      $sheet->getStyle("O$linea:U$linea")->applyFromArray($infor);

      // segunda fila del formato
      $linea++;
      $sheet->setCellValue("B$linea", 'ETC: ');
      $sheet->mergeCells("B$linea:C$linea");
      $sheet->getStyle("B$linea:C$linea")->applyFromArray($titulos2);
      $sheet->setCellValue("D$linea", strtoupper($_SESSION['p_Nombre ETC']));
      $sheet->mergeCells("D$linea:J$linea");
      $sheet->getStyle("D$linea:J$linea")->applyFromArray($infor);
      $sheet->setCellValue("K$linea", 'MUNICIPIO O VEREDA: ' );
      $sheet->mergeCells("K$linea:M$linea");
      $sheet->getStyle("K$linea:M$linea")->applyFromArray($titulos2);
      $sheet->setCellValue("N$linea", $municipio);
      $sheet->mergeCells("N$linea:U$linea");
      $sheet->getStyle("N$linea:U$linea")->applyFromArray($infor);

      // tercera fila del formato
      $linea++;
      $sheet->setCellValue("B$linea", 'INSTITUCIÓN O CENTRO EDUCATIVO: ');
      $sheet->mergeCells("B$linea:E$linea");
      $sheet->getStyle("B$linea:E$linea")->applyFromArray($titulos2);
      $sheet->setCellValue("F$linea", $institucion);
      $sheet->mergeCells("F$linea:K$linea");
      $sheet->getStyle("F$linea:K$linea")->applyFromArray($infor);
      $sheet->setCellValue("L$linea", 'SEDE EDUCATIVA: ' );
      $sheet->mergeCells("L$linea:N$linea");
      $sheet->getStyle("L$linea:N$linea")->applyFromArray($titulos2);
      $sheet->setCellValue("O$linea", $sede);
      $sheet->mergeCells("O$linea:U$linea");
      $sheet->getStyle("O$linea:U$linea")->applyFromArray($infor); 

      // cuarta fila del formato
      $linea++;
      $sheet->setCellValue("B$linea", 'TIPO COMPLEMENTO: ');
      $sheet->mergeCells("B$linea:E$linea");
      $sheet->getStyle("B$linea:E$linea")->applyFromArray($titulos2);
      $sheet->setCellValue("F$linea", $modalidad);
      $sheet->mergeCells("F$linea:K$linea");
      $sheet->getStyle("F$linea:K$linea")->applyFromArray($infor);
      $sheet->setCellValue("L$linea", 'TIPO DESPACHO: ' );
      $sheet->mergeCells("L$linea:N$linea");
      $sheet->getStyle("L$linea:N$linea")->applyFromArray($titulos2);
      $sheet->setCellValue("O$linea", strtoupper($tipoDespachoNm));
      $sheet->mergeCells("O$linea:U$linea");
      $sheet->getStyle("O$linea:U$linea")->applyFromArray($infor); 

      // titulos segunda seccion 
      $linea = $linea+1;
      $sheet->setCellValue("B$linea", 'RANGO DE EDAD');
      $sheet->mergeCells("B$linea:D".($linea+1));
      $sheet->getStyle("B$linea:D".($linea+1))->applyFromArray($titulos2);   
      $sheet->setCellValue("E$linea", 'N° DE RACIONES ADJUDICADAS');
      $sheet->mergeCells("E$linea:G".($linea+1));
      $sheet->getStyle("E$linea:G".($linea+1))->applyFromArray($titulos2);
      $sheet->setCellValue("H$linea", 'N° DE RACIONES ATENDIDAS');
      $sheet->mergeCells("H$linea:J".($linea+1));
      $sheet->getStyle("H$linea:J".($linea+1))->applyFromArray($titulos2);
      $sheet->setCellValue("K$linea", 'N° DE DÍAS A ATENDER');
      $sheet->mergeCells("K$linea:N".($linea+1));
      $sheet->getStyle("K$linea:N".($linea+1))->applyFromArray($titulos2);
      $sheet->setCellValue("O$linea", 'N° DE MENÚ Y SEMANA DEL CICLO DE MENÚS ENTREGADO');
      $sheet->mergeCells("O$linea:R".($linea+1));
      $sheet->getStyle("O$linea:R".($linea+1))->applyFromArray($titulos2);
      $sheet->setCellValue("S$linea", 'TOTAL RACIONES');
      $sheet->mergeCells("S$linea:U".($linea+1));
      $sheet->getStyle("S$linea:U".($linea+1))->applyFromArray($titulos2);

      // informacion segunda seccion 
      $linea = $linea+2;
      $lineaTemporal = $linea-1;
      for ($i=0; $i < $cantGruposEtarios ; $i++) { 
         $lineaTemporal++;
         $sheet->setCellValue("B$lineaTemporal", $get[$i]);
         $sheet->mergeCells("B$lineaTemporal:D$lineaTemporal");
         $sheet->getStyle("B$lineaTemporal:D$lineaTemporal")->applyFromArray($infor);   

         $sheet->setCellValue("E$lineaTemporal", $coberturaPorGrupo[$i]);
         $sheet->mergeCells("E$lineaTemporal:G$lineaTemporal");
         $sheet->getStyle("E$lineaTemporal:G$lineaTemporal")->applyFromArray($infor);

         $sheet->setCellValue("H$lineaTemporal", $coberturaPorGrupo[$i]);
         $sheet->mergeCells("H$lineaTemporal:J$lineaTemporal");
         $sheet->getStyle("H$lineaTemporal:J$lineaTemporal")->applyFromArray($infor);
      }
      $sheet->setCellValue("K$linea", $auxDias);
      $sheet->mergeCells("K$linea:N".($linea+2));
      $sheet->getStyle("K$linea:N".($linea+2))->applyFromArray($infor2);
      $sheet->setCellValue("K".($linea+3), "SEMANA: " .$semana);
      $sheet->mergeCells("K".($linea+3). ":N".($linea+4));
      $sheet->getStyle("K". ($linea+3). ":N".($linea+4))->applyFromArray($infor2);
      $sheet->getStyle("K". ($linea). ":N".($linea+4))->applyFromArray($borderRight);

      $sheet->setCellValue("O$linea", "SEMANA: " .$ciclo);
      $sheet->mergeCells("O" .($linea). ":R".($linea+1));
      $sheet->getStyle("O". ($linea). ":R".($linea+1))->applyFromArray($infor2);
      $sheet->setCellValue("O" .($linea+2),  "MENUS: " . $auxMenus);
      $sheet->mergeCells("O" .($linea+2). ":R".($linea+4));
      $sheet->getStyle("O". ($linea+2). ":R".($linea+4))->applyFromArray($infor2);
      $sheet->getStyle("O". ($linea). ":R".($linea+4))->applyFromArray($borderRight);

      $sheet->setCellValue("S$linea", 'JM: '. $jm );
      $sheet->mergeCells("S".($linea). ":U".($linea+1));
      $sheet->getStyle("S".($linea).":U".($linea+1))->applyFromArray($infor2);
      $sheet->setCellValue("S" .($linea+2), 'JT: '. $jt );
      $sheet->mergeCells("S".($linea+2). ":U".($linea+4));
      $sheet->getStyle("S".($linea+2).":U".($linea+4))->applyFromArray($infor2);
      $sheet->getStyle("S". ($linea). ":U".($linea+4))->applyFromArray($borderRight);

      $sheet->getStyle("K". ($linea). ":U".($linea+4))->applyFromArray($borderBot);

      // informacion tercera seccion 
      $linea = $linea+5;
      $sheet->setCellValue("B$linea", 'GRUPO ALIMENTO');
      $sheet->mergeCells("B$linea:C".($linea+2));
      $sheet->getStyle("B$linea:C".($linea+2))->applyFromArray($titulos2);

      $sheet->setCellValue("D$linea", 'ALIMENTO');
      $sheet->mergeCells("D$linea:I".($linea+2));
      $sheet->getStyle("D$linea:I".($linea+2))->applyFromArray($titulos2);     

      $sheet->setCellValue("J$linea", 'CNT DE ALIMENTOS POR NÚMEROS DE RACIONES');
      $sheet->mergeCells("J$linea:N".($linea));
      $sheet->getStyle("J$linea:N".($linea))->applyFromArray($titulos2);  

      $auxLetra = 'I';
      for ($i=0; $i < $cantGruposEtarios ; $i++) { 
         $auxLetra++;
         $sheet->setCellValue($auxLetra.($linea+1), $etarioNom[$i]);
         $sheet->mergeCells( $auxLetra.($linea+1) .":". $auxLetra.($linea+2));
         $sheet->getStyle( $auxLetra.($linea+1) .":". $auxLetra.($linea+2))->applyFromArray($titulos2);  
      }

      $sheet->setCellValue("O$linea", 'UNIDAD DE MEDIDA');
      $sheet->mergeCells("O$linea:O".($linea+2));
      $sheet->getStyle("O$linea:O".($linea+2))->applyFromArray($titulos2);  

      $sheet->setCellValue("P$linea", 'TOTAL REQ');
      $sheet->mergeCells("P$linea:Q".($linea+2));
      $sheet->getStyle("P$linea:Q".($linea+2))->applyFromArray($titulos2); 

      $sheet->setCellValue("R$linea", 'CANTIDAD ENTREGADA');
      $sheet->mergeCells("R$linea:U".($linea));
      $sheet->getStyle("R$linea:U".($linea))->applyFromArray($titulos2);

      $sheet->setCellValue("R".($linea+1), 'TOTAL');
      $sheet->mergeCells("R".($linea+1). ":S".($linea+2));
      $sheet->getStyle("R".($linea+1). ":S".($linea+2))->applyFromArray($titulos2);  

      $sheet->setCellValue("T".($linea+1), 'C');
      $sheet->mergeCells("T".($linea+1). ":T".($linea+2));
      $sheet->getStyle("T".($linea+1). ":T".($linea+2))->applyFromArray($titulos2);  

      $sheet->setCellValue("U".($linea+1), 'NC');
      $sheet->mergeCells("U".($linea+1). ":U".($linea+2));
      $sheet->getStyle("U".($linea+1). ":U".($linea+2))->applyFromArray($titulos2); 

      // comienzo seccion alimentos
      $linea = $linea+2;
      $lineaInicial = $linea+1;
      $grupoFinal = "";
      $lineaGrupoInicial = 0;
      for ($j=0; $j < count($alimentos ) ; $j++) {
         $alimento = $alimentos[$j];
         $linea++;
         if($alimento['componente'] != ''){
            $sheet->setCellValue("B$linea", $alimento['grupo_alim']);

            if ($grupoFinal == $alimento['grupo_alim'] || $grupoFinal == "") {
               $lineaGrupoInicial++;
            }else{
               $sheet->mergeCells("B".($linea-$lineaGrupoInicial).":C$lineaFinal");
               $lineaGrupoInicial = 1;
            }

            $sheet->setCellValue("D$linea", $alimento['componente']);
            $sheet->mergeCells("D$linea:I$linea");

            // grupo1
            if($alimento['presentacion'] == 'u'){
               $aux = number_format($alimento['cant_grupo1'], 2, '.', '');
            }else{
               $aux = 0+$alimento['cant_grupo1'];
               $aux = number_format($aux, 2, '.', '');
            }
            if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
            $sheet->setCellValue("J$linea", $aux);

            // grupo2
            if($alimento['presentacion'] == 'u'){
               $aux = number_format($alimento['cant_grupo2'], 2, '.', '');
            }else{
               $aux = 0+$alimento['cant_grupo2'];
               $aux = number_format($aux, 2, '.', '');
            }
            if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
            $sheet->setCellValue("K$linea", $aux);

            // grupo3
            if($alimento['presentacion'] == 'u'){
               $aux = number_format($alimento['cant_grupo3'], 2, '.', '');
            }else{
               $aux = 0 + $alimento['cant_grupo3'];
               $aux = number_format($aux, 2, '.', '');
            }
            if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
            $sheet->setCellValue("L$linea", $aux);

            // grupo4
            if($alimento['presentacion'] == 'u'){
               $aux = number_format($alimento['cant_grupo4'], 2, '.', '');
            }else{
               $aux = 0 + $alimento['cant_grupo4'];
               $aux = number_format($aux, 2, '.', '');
            }
            if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
            $sheet->setCellValue("M$linea", $aux);

            // grupo5
            if($alimento['presentacion'] == 'u'){
               $aux = number_format($alimento['cant_grupo5'], 2, '.', '');
            }else{
               $aux = 0 + $alimento['cant_grupo5'];
               $aux = number_format($aux, 2, '.', '');
            }
            if($alimento['grupo_alim'] == "Contramuestra"){ $aux = "0"; }
            $sheet->setCellValue("N$linea", $aux);

            $sheet->setCellValue("O$linea",  $alimento['presentacion']);

            // total requerido
            if ($alimento['presentacion'] == 'u') {
               $aux = number_format($alimento['cant_total'], 2, '.', '');
            } else {
               $aux = number_format($alimento['cant_total'], 2, '.', '');
            }
            $sheet->setCellValue("P$linea",  $aux);
            $sheet->mergeCells("P$linea:Q$linea");

            // total entregado
            if($alimento['cantotalpresentacion'] > 0){
               $aux = 0+$alimento['cantotalpresentacion'];
               $aux = number_format($aux, 2, '.', '');
            }
            $aux = number_format($alimento['cant_total'], 2, '.', '');
            if ($alimento['redondeo'] == 1) {
               if ($aux <= 0.5) {
                  $aux = ceil($aux);
               }else if ($aux > 0.5) {
                  $aux = round($aux,0);
               }  
            }
            if ($alimento['presentacion'] == 'u') {
               if (strpos($alimento['componente'], "HUEVO") !== FALSE) {
                  $aux = ceil(0+$aux);
               } else {
                  $aux = round(0+$aux);
               }
            }
            $aux = number_format($aux, 2, '.', '');
            $sheet->setCellValue("R$linea",  $aux);
            $sheet->mergeCells("R$linea:S$linea");
            $lineaFinal = $linea;
            $grupoFinal = $alimento['grupo_alim'];

            if (!isset($alimentos[$j+1])) {
               // var_dump("B".($linea-$lineaGrupoInicial+1).":C$lineaFinal");
               $sheet->mergeCells("B".($linea-$lineaGrupoInicial+1).":C$lineaFinal");
            }
            
         }
         $sheet->getStyle("B".($lineaInicial). ":U".($lineaFinal))->applyFromArray($infor); 
      } // for alimentos  
      $linea = $linea+3; 
   } // for despachos


   // $sheet->getColumnDimension("B")->setWidth(25); 
   $color = [
    'fill' => [
        'fillType' =>  \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'color' => ['argb' => 'FFFFFF'],
        ],
       ];

   $sheet->getStyle("A1:BB".($linea+50))->applyFromArray($color);

   $writer = new Xlsx($spreadsheet);
   $reader->setReadDataOnly(false);
   // exit(var_dump($writer));
   $writer->setIncludeCharts(TRUE);
   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
   header('Content-Disposition: attachment; filename="informe_despacho_individual.xlsx"');
   $writer->save('php://output','informe_despacho_individual.xlsx');
}






