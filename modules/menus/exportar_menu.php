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

if (isset($_POST['idProducto'])) {
	$codigo = $_POST['idProducto'];
} else { 
	echo "<script>alert('Error al obtener datos del menú.');location.href='index.php';</script>";
}

$annoActual = $_SESSION['periodoActual'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'APORTES NUTRICIONALES MENÚ : ');
$sheet->mergeCells('A1:C2');

$sheet->setCellValue('A3', 'GRUPO ALIMENTICIO');
$sheet->mergeCells('A3:A4');
$sheet->setCellValue('B3', 'CÓDIGO ALIMENTO');
$sheet->mergeCells('B3:B4');
$sheet->setCellValue('C3', 'ALIMENTO');
$sheet->mergeCells('C3:C4');
$sheet->setCellValue('D3', 'NOMBRE DE INGREDIENTE');
$sheet->mergeCells('D3:D4');
$sheet->setCellValue('E3', 'CÓDIGO');
$sheet->mergeCells('E3:E4');
$sheet->setCellValue('F3', 'PESO BRUTO (g)');
$sheet->mergeCells('F3:F4');
$sheet->setCellValue('G3', 'PESO NETO (g)');
$sheet->mergeCells('G3:G4');

$sheet->setCellValue('H3', 'CALORÍAS Y NUTRIENTES');
$sheet->mergeCells('H3:AC3');

$sheet->setCellValue('H4', 'CALORIAS (Kcal)');
$sheet->setCellValue('I4', 'Kcal DESDE LA GRASA');
$sheet->setCellValue('J4', 'PROTEÍNA (g)');
$sheet->setCellValue('K4', 'GRASA (g)');
$sheet->setCellValue('L4', 'GRASA SATURADA (g)');
$sheet->setCellValue('M4', 'GRASA POLIINSATURADA (g)');
$sheet->setCellValue('N4', 'GRASA MONOINSATURADA (g)');
$sheet->setCellValue('O4', 'GRASA TRANS (g)');
$sheet->setCellValue('P4', 'CARBOHIDRATOS (g)');
$sheet->setCellValue('Q4', 'FIBRA DIETARIA (g)');
$sheet->setCellValue('R4', 'AZÚCARES (g)');
$sheet->setCellValue('S4', 'COLESTEROL (mg)');
$sheet->setCellValue('T4', 'SODIO (g)');
$sheet->setCellValue('U4', 'ZINC (g)');
$sheet->setCellValue('V4', 'CALCIO (mg)');
$sheet->setCellValue('W4', 'HIERRO (mg)');
$sheet->setCellValue('X4', 'VIT A (mg)');
$sheet->setCellValue('Y4', 'VIT C (mg)');
$sheet->setCellValue('Z4', 'VIT B1 (mg)');
$sheet->setCellValue('AA4', 'VIT B2 (mg)');
$sheet->setCellValue('AB4', 'VIT B3 (mg)');
$sheet->setCellValue('AC4', 'ÁCIDO FÓLICO (g)');

$Cantidad_de_columnas_a_crear=30; 
$Contador=1; 
$Letra='A'; 
while($Contador<$Cantidad_de_columnas_a_crear) 
{ 
    $sheet->getColumnDimension($Letra)->setWidth(15); 
    $Contador++; 
    $Letra++; 
}

$sheet->getRowDimension("4")->setRowHeight(30);

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

$sheet->getStyle("A1:AC4")->applyFromArray($titulos);


$consMenu = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo = '".$codigo."'";
$resMenu = $Link->query($consMenu);
if ($resMenu->num_rows > 0) {
	$Menu = $resMenu->fetch_assoc();
	$idProducto = $Menu['Id'];
	$codigoMenu = $codigo;
	$nomMenu = $Menu['Descripcion'];
}

$sheet->setCellValue('D1', strtoupper('Menú '.$codigoMenu." ".$nomMenu));
$sheet->mergeCells('D1:M2');

$consultaProducto = "select id from fichatecnica where Codigo = '".$codigo."'";

      //echo "<br>Consulta producto<br>".$consultaProducto."<br>";

      $result = $Link->query($consultaProducto) or die ('Unable to execute query. '. mysqli_error($Link));
      $row = $result->fetch_assoc();
      $idProducto = $row['id'];

$consultaSubProductos =  "SELECT f.id as idFichaTecnica,fd.* FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = '".$idProducto."' ";

$result = $Link->query($consultaSubProductos) or die ('Unable to execute query. '. mysqli_error($Link));
      while ($row = $result->fetch_assoc()){
         $subProductos[] = $row;
      }

$consultaCantidadesGrupo = "SELECT idft, count(idFT) AS materias, max(cantidad), mac.grupo_alim FROM fichatecnicadet fd LEFT JOIN menu_aportes_calynut mac ON fd.codigo = mac.cod_prod WHERE fd.idFT IN (SELECT f.id FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = '".$idProducto."') GROUP BY idFT  ";

$result = $Link->query($consultaCantidadesGrupo) or die ('Unable to execute query. '. mysqli_error($Link));
      while ($row = $result->fetch_assoc()){
         $cantidadesGrupo[] = $row;
      }

$consultaMateriasPrimas = " SELECT fd.*, mac.* FROM fichatecnicadet fd LEFT JOIN menu_aportes_calynut mac ON fd.codigo = mac.cod_prod WHERE fd.idFT IN (SELECT f.id FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = '".$idProducto."' ) ";

$result = $Link->query($consultaMateriasPrimas) or die ('Unable to execute query. '. mysqli_error($Link));
      while ($row = $result->fetch_assoc()){
         $materiasPrimas[] = $row;
      }

$consultaSubProductos =  "SELECT mvn.*
      FROM productos$annoActual  p
      left join menu_valref_nutrientes mvn on p.Cod_Grupo_Etario = mvn.Cod_Grupo_Etario and p.Cod_Tipo_complemento = mvn.Cod_tipo_complemento
      where p.Codigo = '$codigoMenu'";

$result = $Link->query($consultaSubProductos) or die ('Unable to execute query. '. mysqli_error($Link));
      while ($row = $result->fetch_assoc()){
         $valoresMenu = $row;
      }

$tpesoNeto = 0;
$tkcal = 0;
$tkcaldgrasa =0;
$tProteinas = 0;
$tgrasas = 0;
$tgrasaSaturada = 0;
$tgrasaInsaturada = 0;
$tgrasaMonoInsaturada = 0;



$tgrasaTrans = 0;
$tcarbohidratos = 0;
$tFibra_dietaria = 0;
$tAzucares = 0;
$tColesterol = 0;
$tSodio = 0;
$tZinc = 0;
$tCalcio = 0;
$tHierro = 0;
$tVit_A = 0;
$tVit_C = 0;
$tVit_B1 = 0;
$tVit_B2 = 0;
$tVit_B3 = 0;
$tAcido_Fol = 0;

$numfila = 5;

for ($i=0; $i < count($subProductos) ; $i++) {

  $subproducto = $subProductos[$i]['idFichaTecnica'];

  $indice = 0;

  for ($j=0; $j < count($cantidadesGrupo) ; $j++) {
    if($cantidadesGrupo[$j]['idft'] == $subproducto){
      $indice = $j;
    }
  }

  $materias = $cantidadesGrupo[$indice]['materias'];
  $filaMaterias = $numfila+$materias-1;

  $sheet->setCellValue('A'.$numfila, $cantidadesGrupo[$indice]['grupo_alim']);
  $sheet->mergeCells('A'.$numfila.':A'.$filaMaterias);
  $sheet->setCellValue('B'.$numfila, $subProductos[$i]['codigo']);
  $sheet->mergeCells('B'.$numfila.':B'.$filaMaterias);
  $sheet->setCellValue('C'.$numfila, $subProductos[$i]['Componente']);
  $sheet->mergeCells('C'.$numfila.':C'.$filaMaterias);

    unset($ingredientes);
    for ($l=0; $l < count($materiasPrimas) ; $l++) {
      if($materiasPrimas[$l]['IdFT'] == $subproducto){
        $ingredientes[] = $materiasPrimas[$l];
      }
    }

    //var_dump($ingredientes);

    for ($k=0; $k < $materias ; $k++) {
      $aux = 0;

      $numfilaActual = $numfila+$k;

      	$sheet->setCellValue('D'.$numfilaActual, $ingredientes[$k]['Componente']);
      	$sheet->setCellValue('E'.$numfilaActual, $ingredientes[$k]['codigo']);
      	$sheet->setCellValue('F'.$numfilaActual, $ingredientes[$k]['PesoBruto']);
      	$sheet->setCellValue('G'.$numfilaActual, $ingredientes[$k]['Cantidad']);

      	$valorcelda = "";

      	$tpesoNeto = $tpesoNeto + $ingredientes[$k]['Cantidad'];
      	if (is_null($ingredientes[$k]['kcalxg']) ) {
            $valorcelda = '0'; $tkcal = $tkcal + 0;
          }else{
            $aux = $ingredientes[$k]['kcalxg'];
            $aux = ($aux * $ingredientes[$k]['PesoNeto'])/100;
            // Formula de las calorias ultima modificación 20160919
            //$aux = $aux * 9;
            //$aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda =  $aux;
            $tkcal = $tkcal + $aux;
          } 

        $sheet->setCellValue('H'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['kcaldgrasa']) ) {
            $valorcelda = '0'; $tkcaldgrasa = $tkcaldgrasa + 0;
      	}else{
            $aux = $ingredientes[$k]['kcaldgrasa'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            $tkcaldgrasa = $tkcaldgrasa + $aux;
      	} 

      	$sheet->setCellValue('I'.$numfilaActual, $valorcelda);

      	if (is_null($ingredientes[$k]['Proteinas']) ) {
            $valorcelda = '0'; $tProteinas = $tProteinas + 0;
          }else{
            $aux = $ingredientes[$k]['Proteinas'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            $tProteinas = $tProteinas + $aux;
          }

        $sheet->setCellValue('J'.$numfilaActual, $valorcelda);

	    $grasas = $ingredientes[$k]['Grasa_Sat'] + $ingredientes[$k]['Grasa_poliins'] + $ingredientes[$k]['Grasa_Monoins'] + $ingredientes[$k]['Grasa_Trans'];


	    $grasas = ($grasas / 100) * $ingredientes[$k]['Cantidad'];

	    $valorcelda = $grasas;
	    $tgrasas = $tgrasas + $grasas;

	    $sheet->setCellValue('K'.$numfilaActual, $valorcelda);
	    
	    if (is_null($ingredientes[$k]['Grasa_Sat']) ) {
            $valorcelda = '0'; $tgrasaSaturada = $tgrasaSaturada + 0;
          }else{
            $aux = $ingredientes[$k]['Grasa_Sat'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            $tgrasaSaturada = $tgrasaSaturada + $aux;
          }
        $sheet->setCellValue('L'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Grasa_poliins']) ) {
	        $valorcelda = '0'; $tgrasaInsaturada = $tgrasaInsaturada + 0;
	      }else{
	        $aux = $ingredientes[$k]['Grasa_poliins'];
	        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
	        $valorcelda = $aux;
	        $tgrasaInsaturada = $tgrasaInsaturada + $aux;
	      }
	    $sheet->setCellValue('M'.$numfilaActual, $valorcelda);

	    if (is_null($ingredientes[$k]['Grasa_Monoins']) ) {
            $valorcelda = '0'; $tgrasaMonoInsaturada = $tgrasaMonoInsaturada + 0;
          }else{
            $aux = $ingredientes[$k]['Grasa_Monoins'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Grasa_Monoins'];
            $tgrasaMonoInsaturada = $tgrasaMonoInsaturada + $aux;
          }
        $sheet->setCellValue('N'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Grasa_Trans']) ) {
            $valorcelda = '0'; $tgrasaTrans = $tgrasaTrans + 0;
          }else{
            $aux = $ingredientes[$k]['Grasa_Trans'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Grasa_Trans'];
            $tgrasaTrans = $tgrasaTrans + $aux;
          }

        $sheet->setCellValue('O'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Fibra_dietaria']) || is_null($ingredientes[$k]['Azucares']) ) {
            $valorcelda = '0'; $tcarbohidratos = $tcarbohidratos + 0;
          }else{
            $aux = $ingredientes[$k]['Fibra_dietaria'] + $ingredientes[$k]['Azucares'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            $tcarbohidratos = $tcarbohidratos + $aux;
          } 

        $sheet->setCellValue('P'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Fibra_dietaria']) ) {
            $valorcelda = '0'; $tFibra_dietaria = $tFibra_dietaria + 0;
          }else{
            $aux = $ingredientes[$k]['Fibra_dietaria'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Fibra_dietaria'];
            $tFibra_dietaria = $tFibra_dietaria + $aux;
          }

        $sheet->setCellValue('Q'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Azucares']) ) {
            $valorcelda = '0'; $tAzucares = $tAzucares + 0;
          }else{
            $aux = $ingredientes[$k]['Azucares'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;

            //echo $ingredientes[$k]['Azucares'];
            $tAzucares = $tAzucares + $aux;
          }

        $sheet->setCellValue('R'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Colesterol']) ) {
            $valorcelda = '0'; $tColesterol = $tColesterol + 0;
          }else{
            $aux = $ingredientes[$k]['Colesterol'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Colesterol'];
            $tColesterol = $tColesterol + $aux;
          }

        $sheet->setCellValue('S'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Sodio']) ) {
            $valorcelda = '0'; $tSodio = $tSodio + 0;
          }else{
            $aux = $ingredientes[$k]['Sodio'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Sodio'];
            $tSodio = $tSodio + $aux;
          }

        $sheet->setCellValue('T'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Zinc']) ) {
            $valorcelda = '0'; $tZinc = $tZinc + 0;
          }else{
            $aux = $ingredientes[$k]['Zinc'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Sodio'];
            $tZinc = $tZinc + $aux;
          }

        $sheet->setCellValue('U'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Calcio']) ) {
            $valorcelda = '0'; $tCalcio = $tCalcio + 0;
          }else{
            $aux = $ingredientes[$k]['Calcio'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Calcio'];
            $tCalcio = $tCalcio + $aux;
          }

        $sheet->setCellValue('V'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Hierro']) ) {
            $valorcelda = '0'; $tHierro = $tHierro + 0;
          }else{
            $aux = $ingredientes[$k]['Hierro'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Hierro'];
            $tHierro = $tHierro + $aux;
          }

        $sheet->setCellValue('W'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Vit_A']) ) {
            $valorcelda = '0'; $tVit_A = $tVit_A + 0;
          }else{
            $aux = $ingredientes[$k]['Vit_A'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Vit_A'];
            $tVit_A = $tVit_A + $aux;
          }

        $sheet->setCellValue('X'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Vit_C']) ) {
            $valorcelda = '0'; $tVit_C = $tVit_C + 0;
          }else{
            $aux = $ingredientes[$k]['Vit_C'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Vit_C'];
            $tVit_C = $tVit_C + $aux;
          }

        $sheet->setCellValue('Y'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Vit_B1']) ) {
            $valorcelda = '0'; $tVit_B1 = $tVit_B1 + 0;
          }else{
            $aux = $ingredientes[$k]['Vit_B1'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Vit_B1'];
            $tVit_B1 = $tVit_B1 + $aux;
          }

        $sheet->setCellValue('Z'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Vit_B2']) ) {
            $valorcelda = '0'; $tVit_B2 = $tVit_B2 + 0;
          }else{
            $aux = $ingredientes[$k]['Vit_B2'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Vit_B2'];
            $tVit_B2 = $tVit_B2 + $aux;
          }

        $sheet->setCellValue('AA'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Vit_B3']) ) {
            $valorcelda = '0'; $tVit_B3 = $tVit_B3 + 0;
          }else{
            $aux = $ingredientes[$k]['Vit_B3'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;
            //echo $ingredientes[$k]['Vit_B3'];
            $tVit_B3 = $tVit_B3 + $aux;
          }

        $sheet->setCellValue('AB'.$numfilaActual, $valorcelda);

        if (is_null($ingredientes[$k]['Acido_Fol']) ) {
            $valorcelda = '0'; $tAcido_Fol = $tAcido_Fol + 0;
          }else{
            $aux = $ingredientes[$k]['Acido_Fol'];
            $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
            $valorcelda = $aux;

            //echo $ingredientes[$k]['Acido_Fol'];
            $tAcido_Fol = $tAcido_Fol + $aux;
          }

        $sheet->setCellValue('AC'.$numfilaActual, $valorcelda);
	 	 } 
      $numfila = $filaMaterias+1;
  } 


$sheet->setCellValue('A'.$numfila, 'TOTAL MENÚ');
$sheet->mergeCells('A'.$numfila.':G'.$numfila);
	$sheet->setCellValue('H'.$numfila, $tkcal);
	$sheet->setCellValue('I'.$numfila, $tkcaldgrasa);
	$sheet->setCellValue('J'.$numfila, $tProteinas);
	$sheet->setCellValue('K'.$numfila, $tgrasas);
	$sheet->setCellValue('L'.$numfila, $tgrasaSaturada);
	$sheet->setCellValue('M'.$numfila, $tgrasaInsaturada);
	$sheet->setCellValue('N'.$numfila, $tgrasaMonoInsaturada);
	$sheet->setCellValue('O'.$numfila, $tgrasaTrans);
	$sheet->setCellValue('P'.$numfila, $tcarbohidratos);
	$sheet->setCellValue('Q'.$numfila, $tFibra_dietaria);
	$sheet->setCellValue('R'.$numfila, $tAzucares);
	$sheet->setCellValue('S'.$numfila, $tColesterol);
	$sheet->setCellValue('T'.$numfila, $tSodio);
	$sheet->setCellValue('U'.$numfila, $tZinc);
	$sheet->setCellValue('V'.$numfila, $tCalcio);
	$sheet->setCellValue('W'.$numfila, $tHierro);
	$sheet->setCellValue('X'.$numfila, $tVit_A);
	$sheet->setCellValue('Y'.$numfila, $tVit_C);
	$sheet->setCellValue('Z'.$numfila, $tVit_B1);
	$sheet->setCellValue('AA'.$numfila, $tVit_B2);
	$sheet->setCellValue('AB'.$numfila, $tVit_B3);
	$sheet->setCellValue('AC'.$numfila, $tAcido_Fol);
	$numfila++;
$sheet->setCellValue('A'.$numfila, 'RECOMENDACIÓN DE INGESTA DE CALORIAS Y NUTRIENTES');
$sheet->mergeCells('A'.$numfila.':G'.$numfila);
	$sheet->setCellValue('H'.$numfila, $valoresMenu['kcalxg']);
	$sheet->setCellValue('I'.$numfila, $valoresMenu['kcaldgrasa']);
	$sheet->setCellValue('J'.$numfila, $valoresMenu['Proteinas']);
	$sheet->setCellValue('K'.$numfila, $valoresMenu['Grasa_Sat']+$valoresMenu['Grasa_poliins']+$valoresMenu['Grasa_Monoins']+$valoresMenu['Grasa_Trans']);
	$sheet->setCellValue('L'.$numfila, $valoresMenu['Grasa_Sat']);
	$sheet->setCellValue('M'.$numfila, $valoresMenu['Grasa_poliins']);
	$sheet->setCellValue('N'.$numfila, $valoresMenu['Grasa_Monoins']);
	$sheet->setCellValue('O'.$numfila, $valoresMenu['Grasa_Trans']);
	$sheet->setCellValue('P'.$numfila, $valoresMenu['Fibra_dietaria'] + $valoresMenu['Azucares']);
	$sheet->setCellValue('Q'.$numfila, $valoresMenu['Fibra_dietaria']);
	$sheet->setCellValue('R'.$numfila, $valoresMenu['Azucares']);
	$sheet->setCellValue('S'.$numfila, $valoresMenu['Colesterol']);
	$sheet->setCellValue('T'.$numfila, $valoresMenu['Zinc']);
	$sheet->setCellValue('U'.$numfila, $valoresMenu['Sodio']);
	$sheet->setCellValue('V'.$numfila, $valoresMenu['Calcio']);
	$sheet->setCellValue('W'.$numfila, $valoresMenu['Hierro']);
	$sheet->setCellValue('X'.$numfila, $valoresMenu['Vit_A']);
	$sheet->setCellValue('Y'.$numfila, $valoresMenu['Vit_C']);
	$sheet->setCellValue('Z'.$numfila, $valoresMenu['Vit_B1']);
	$sheet->setCellValue('AA'.$numfila, $valoresMenu['Vit_B2']);
	$sheet->setCellValue('AB'.$numfila, $valoresMenu['Vit_B3']);
	$sheet->setCellValue('AC'.$numfila, $valoresMenu['Acido_Fol']);
	$numfila++;
$sheet->setCellValue('A'.$numfila, '% DE ADECUACIÓN');
$sheet->mergeCells('A'.$numfila.':G'.$numfila);

if($valoresMenu['kcalxg'] != 0){$pkcal = number_format (($tkcal/$valoresMenu['kcalxg'])*100,2);}else{$pkcal = 0;}
if($valoresMenu['kcaldgrasa'] != 0){$pkcaldgrasa = number_format (($tkcaldgrasa/$valoresMenu['kcaldgrasa'])*100,2);}else{$pkcaldgrasa = 0;}
if($valoresMenu['Proteinas'] != 0){$pProteinas = number_format (($tProteinas/$valoresMenu['Proteinas'])*100,2);}else{$pProteinas = 0;}
if(($valoresMenu['Grasa_Sat']+$valoresMenu['Grasa_poliins']+$valoresMenu['Grasa_Monoins']+$valoresMenu['Grasa_Trans']) != 0){$pgrasas = number_format (($tgrasas/($valoresMenu['Grasa_Sat']+$valoresMenu['Grasa_poliins']+$valoresMenu['Grasa_Monoins']+$valoresMenu['Grasa_Trans']))*100,2);}else{$pgrasas = 0;}
if($valoresMenu['Grasa_Sat'] != 0){$pgrasaSaturada = number_format (($tgrasaSaturada/$valoresMenu['Grasa_Sat'])*100,2);}else{$pgrasaSaturada = 0;}
if($valoresMenu['Grasa_poliins'] != 0){$pgrasaInsaturada = number_format (($tgrasaInsaturada/$valoresMenu['Grasa_poliins'])*100,2);}else{$pgrasaInsaturada = 0;}
if($valoresMenu['Grasa_Monoins'] != 0){$pgrasaMonoInsaturada = number_format (($tgrasaMonoInsaturada/$valoresMenu['Grasa_Monoins'])*100,2);}else{$pgrasaMonoInsaturada = 0;}
if($valoresMenu['Grasa_Trans'] != 0){$pgrasaTrans = number_format (($tgrasaTrans/$valoresMenu['Grasa_Trans'])*100,2);}else{$pgrasaTrans = 0;}
if(($valoresMenu['Fibra_dietaria']+$valoresMenu['Azucares']) != 0){$pcarbohidratos = number_format (($tcarbohidratos/($valoresMenu['Fibra_dietaria']+$valoresMenu['Azucares']))*100,2);}else{$pcarbohidratos = 0;}
if($valoresMenu['Fibra_dietaria'] != 0){$pFibra = number_format (($tFibra_dietaria/$valoresMenu['Fibra_dietaria'])*100,2);}else{$pFibra = 0;}
if($valoresMenu['Azucares'] != 0){$pAzucares = number_format (($tAzucares/$valoresMenu['Azucares'])*100,2);}else{$pAzucares = 0;}
if($valoresMenu['Colesterol'] != 0){$pColesterol = number_format (($tColesterol/$valoresMenu['Colesterol'])*100,2);}else{$pColesterol = 0;}
if($valoresMenu['Sodio'] != 0){$pSodio = number_format (($tSodio/$valoresMenu['Sodio'])*100,2);}else{$pSodio = 0;}
if($valoresMenu['Zinc'] != 0){$pZinc = number_format (($tZinc/$valoresMenu['Zinc'])*100,2);}else{$pZinc = 0;}
if($valoresMenu['Calcio'] != 0){$pCalcio = number_format (($tCalcio/$valoresMenu['Calcio'])*100,2);}else{$pCalcio = 0;}
if($valoresMenu['Hierro'] != 0){$pHierro = number_format (($tHierro/$valoresMenu['Hierro'])*100,2);}else{$pHierro = 0;}
if($valoresMenu['Vit_A'] != 0){$pVit_A = number_format (($tVit_A/$valoresMenu['Vit_A'])*100,2);}else{$pVit_A = 0;}
if($valoresMenu['Vit_C'] != 0){$pVit_C = number_format (($tVit_C/$valoresMenu['Vit_C'])*100,2);}else{$pVit_C = 0;}
if($valoresMenu['Vit_B1'] != 0){$pVit_B1 = number_format (($tVit_B1/$valoresMenu['Vit_B1'])*100,2);}else{$pVit_B1 = 0;}
if($valoresMenu['Vit_B2'] != 0){$pVit_B2 = number_format (($tVit_B2/$valoresMenu['Vit_B2'])*100,2);}else{$pVit_B2 = 0;}
if($valoresMenu['Vit_B3'] != 0){$pVit_B3 = number_format (($tVit_B3/$valoresMenu['Vit_B3'])*100,2);}else{$pVit_B3 = 0;}
if($valoresMenu['Acido_Fol'] != 0){$pAcido_Fol = number_format (($tAcido_Fol/$valoresMenu['Acido_Fol'])*100,2);}else{$pAcido_Fol = 0;}

	$sheet->setCellValue('H'.$numfila, $pkcal."%");
	$sheet->setCellValue('I'.$numfila, $pkcaldgrasa."%");
	$sheet->setCellValue('J'.$numfila, $pProteinas."%");
	$sheet->setCellValue('K'.$numfila, $pgrasas."%");
	$sheet->setCellValue('L'.$numfila, $pgrasaSaturada."%");
	$sheet->setCellValue('M'.$numfila, $pgrasaInsaturada."%");
	$sheet->setCellValue('N'.$numfila, $pgrasaMonoInsaturada."%");
	$sheet->setCellValue('O'.$numfila, $pgrasaTrans."%");
	$sheet->setCellValue('P'.$numfila, $pcarbohidratos."%");
	$sheet->setCellValue('Q'.$numfila, $pFibra."%");
	$sheet->setCellValue('R'.$numfila, $pAzucares."%");
	$sheet->setCellValue('S'.$numfila, $pColesterol."%");
	$sheet->setCellValue('T'.$numfila, $pSodio."%");
	$sheet->setCellValue('U'.$numfila, $pZinc."%");
	$sheet->setCellValue('V'.$numfila, $pCalcio."%");
	$sheet->setCellValue('W'.$numfila, $pHierro."%");
	$sheet->setCellValue('X'.$numfila, $pVit_A."%");
	$sheet->setCellValue('Y'.$numfila, $pVit_C."%");
	$sheet->setCellValue('Z'.$numfila, $pVit_B1."%");
	$sheet->setCellValue('AA'.$numfila, $pVit_B2."%");
	$sheet->setCellValue('AB'.$numfila, $pVit_B3."%");
	$sheet->setCellValue('AC'.$numfila, $pAcido_Fol."%");

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

$sheet->getStyle("A5:AC".$numfila)->applyFromArray($infor);

$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="menu_'.$codigo.'_exportar.xlsx"');
$writer->save('php://output','menu_'.$codigo.'_exportar.xlsx');
