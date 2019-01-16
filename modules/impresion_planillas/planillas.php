<?php
include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
require('rotation.php');
$periodoActual = $_SESSION['periodoActual'];

//var_dump($_POST);

set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');
$tamannoFuente = 8;

// Variables de inicio
$municipioNm = $_POST['municipioNm'];
$operador = $_SESSION['p_Operador'];


$departamento = $_SESSION['p_Departamento'];
$anno = $_SESSION['p_ano'];
$anno2d = substr($anno,2);
$tipoComplemento = $_POST['tipo'];
$tipoPlanilla = $_POST['tipoPlanilla'];
// Tipo planilla 1 = Vacia
// Tipo planilla 2 = Blanca
// Tipo planilla 3 = Programada
// Tipo planilla 4 = Novedades

$mes = $_POST['mes'];
if($mes < 10){
  if(substr($mes,0,1)!="0"){
    $mes = '0'.$mes;
  }
}



$institucion = $_POST['institucion'];
$sedeParametro = '';
if(isset($_POST['sede']) && $_POST['sede'] != ''){
	$sedeParametro = $_POST['sede'];
}

// Terminanlas variables de inicio


//Primera consulta: los dias de la s entregas
$consulta = "SELECT ID,ANO,MES,D1 AS 'D01',D2 AS D02,D3 AS D03,D4 AS D04,D5 AS D05,D6 AS D06,D7 AS D07,D8 AS D08,D9 AS D09,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22
FROM planilla_dias where ano='$anno' AND mes='$mes'";

//echo "$consulta";


$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()){
    $dias = $row;
  }
}


// Revisando si tiene más de un mes
$aux = 0;
$auxVal = 0;
$mesAdicional = 0;
$totalDias = 0;
foreach ($dias as $dia){
  if($aux > 2 && $dia != ''){
    $totalDias++;
    //echo "<br>".$dia."-".$totalDias;

    if( $auxVal < intval($dia)){
      $auxVal = intval($dia);
    }
    else{
      $mesAdicional++;
    }
  }
  $aux++;
}
// Termina de revisar si tiene más de un mes




//Segunda consulta: las sedes
$consulta = "SELECT DISTINCT s.cod_sede, s.cod_inst,s.nom_inst,s.nom_sede,s.cod_mun_sede,sc.num_est_focalizados
FROM sedes$periodoActual s
INNER JOIN sedes_cobertura AS sc ON (s.cod_inst=sc.cod_inst AND s.cod_Sede=sc.cod_Sede)
WHERE sc.cod_inst= '$institucion' AND sc.ano = '$anno' AND sc.mes='$mes' ";
if($sedeParametro != ''){
	$consulta .= " and sc.cod_sede = '$sedeParametro' ";
}



//var_dump($consulta);

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()){
    $codigo = $row['cod_sede'];
    $sedes[$codigo] = $row;
  }
}



if($tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4) {
	$consulta = "SELECT id, tipo_doc, num_doc, tipo_doc_nom, nom1, nom2, ape1, ape2, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben,
	cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente,edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo_complem,D1 AS 'D01',D2 AS D02,D3 AS D03,D4 AS D04,D5 AS D05,D6 AS D06,D7 AS D07,D8 AS D08,D9 AS D09,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22
	FROM entregas_res_$mes$anno2d WHERE cod_inst=$institucion AND tipo_complem='$tipoComplemento' ";
	if($sedeParametro != ''){ $consulta .= " and cod_sede = '$sedeParametro' "; }
	$consulta .= " Order By cod_sede, cod_grado,ape1,ape2,nom1,nom2 asc ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. Tercera consulta: los niños<br>'.$consulta.'<br>'.mysqli_error($Link));

  $codigo = '';
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()){
      if($codigo != $row['cod_sede']){
        $codigo = $row['cod_sede'];
      }
      $estudiantes[$codigo][] = $row;
    }
  }
}

































class PDF extends PDF_Rotate{
  function Header()
  {
    $tamannoFuente = 8;
    $this->SetFont('Arial','B',$tamannoFuente);
    $logoInfopae = $_SESSION['p_Logo ETC'];
    $logosEnte = 'imagenes/logos_planilla_ente.jpg';
    $logosPae = 'imagenes/logos_planilla_pae.jpg';
    $this->Image($logosEnte, 23 ,4, 43.39, 18.1,'jpg', '');
    $this->Image($logosPae, 291.22 ,7.5, 61.38, 10.23,'jpg', '');
    $this->SetTextColor(0,0,0);
    $this->Cell(34);
    $this->Cell(250.83,18.1,utf8_decode('REGISTRO Y CONTROL DIARIO DE ASISTENCIA DE TITULAR DE DERECHO DEL PROGRAMA DE ALIMENTACIÓN ESCOLAR - PAE '),0,0,'C',False);
    $this->Ln(10);
    $this->SetLineWidth(1.5);
    $this->Cell(0,10,'','B',0,'C',False);
    $this->Ln(10);
  }

  // Pie de página
  function Footer()
  {
      $tamannoFuente = 8;
      // Posición: a 1,5 cm del final
      $this->SetY(-15);
      // Arial italic 8
      $this->SetFont('Arial','I',8);
      // Número de página
      //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
  }

  function RotatedText($x,$y,$txt,$angle)
  {
  	//Text rotated around its origin
  	$this->Rotate($angle,$x,$y);
  	$this->Text($x,$y,$txt);
  	$this->Rotate(0);
  }

  function RotatedImage($file,$x,$y,$w,$h,$angle)
  {
  	//Image rotated around its upper-left corner
  	$this->Rotate($angle,$x,$y);
  	$this->Image($file,$x,$y,$w,$h);
  	$this->Rotate(0);
  }
}

//CREACION DEL PDF
  // Creación del objeto de la clase heredada
  $pdf= new PDF('L','mm',array(330,216));
  $pdf->SetMargins(3, 4, 3, 3);
  $pdf->SetAutoPageBreak(false,5);
  $pdf->AliasNbPages();
  include '../../php/funciones.php';


  $lineas = 20;
  $alturaLinea = 5;

if($tipoPlanilla != 1){
  foreach ($estudiantes as $estudiantesSede){

    $codigoSede = $estudiantesSede[0]['cod_sede'];



    $consulta = "SELECT count(id) as titulares, sum(D1 +D2 +D3 +D4 +D5 +D6 +D7+D8 +D9+D10+D11+D12+D13+D14+D15+D16+D17+D18+D19+D20+D21+D22) as entregas FROM entregas_res_$mes$anno2d WHERE cod_inst='$institucion' AND tipo_complem ='$tipoComplemento' AND cod_sede = '$codigoSede'";
		if($sedeParametro != ''){
			$consulta .= " and cod_sede = '$sedeParametro' ";
		}
    $resultado = $Link->query($consulta) or die ('Unable to execute query. <br>'.$consulta.'<br>'. mysqli_error($Link));
    if($resultado->num_rows >= 1){
      while($row = $resultado->fetch_assoc()){
        $totales = $row;
      }
    }
    //var_dump($totales);












    $paginas = ceil(count($estudiantesSede) / $lineas);
    $pagina = 1;
    $linea = 1;

    $pdf->AddPage();
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);

    $pdf->SetFont('Arial','',$tamannoFuente);
    include 'planillas_header.php';
    $pdf->SetLineWidth(.05);
    $pdf->SetFont('Arial','',$tamannoFuente);
    //Inicia impresión de estudiantes de la sede
    $nEstudiante = 0;
    $pdf->SetFont('Arial','',$tamannoFuente);
    foreach ($estudiantesSede as $estudiante){
      $nEstudiante++;
      if($linea > $lineas){
        $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
        $pdf->Ln(7);
        $alturaCuadroFilas = $alturaLinea * ($linea-1);
        $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
        include 'planillas_footer.php';
        $pdf->AddPage();
        $pagina++;
        include 'planillas_header.php';
        $pdf->SetFont('Arial','',$tamannoFuente);
        $linea = 1;
      }
      $x = $pdf->GetX();
      $y = $pdf->GetY();
      $pdf->Cell(8,$alturaLinea,utf8_decode($nEstudiante),'R',0,'C',False);
      $pdf->Cell(10,$alturaLinea,utf8_decode($estudiante['tipo_doc_nom']),'R',0,'C',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode($estudiante['num_doc']),'R',0,'L',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode($estudiante['nom1']),'R',0,'L',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode($estudiante['nom2']),'R',0,'L',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode($estudiante['ape1']),'R',0,'L',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode($estudiante['ape2']),'R',0,'L',False);


      $aux = $estudiante['fecha_nac'];
      $aux = date("d/m/Y", strtotime($aux));
      $pdf->Cell(14,$alturaLinea,utf8_decode($aux),'R',0,'C',False);


      $pdf->Cell(5,$alturaLinea,utf8_decode($estudiante['genero']),'R',0,'C',False);
      $pdf->Cell(7,$alturaLinea,utf8_decode($estudiante['cod_grado']),'R',0,'C',False);
      $pdf->Cell(13,$alturaLinea,utf8_decode($tipoComplemento),'R',0,'C',False);
      $dia = 0;




      // Aqui es donde se cambia de acuerdo a la plantilla
      $entregasEstudiante = 0;
      for($j = 0 ; $j < 30 ; $j++){
        if($tipoPlanilla != 2){

          if($tipoPlanilla == 3){
            $pdf->SetTextColor(190,190,190);
          }

          $dia++;
          if($dia < 10){
            $auxDia = 'D0'.$dia;
          }else{
            $auxDia = 'D'.$dia;
          }
          if(isset($estudiante[$auxDia]) && $estudiante[$auxDia] == 1){
            $pdf->Cell(5,$alturaLinea,utf8_decode('x'),'R',0,'C',False);
            $entregasEstudiante++;
          }
          else{
            $pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
          }

        }
        else{
          $pdf->Cell(5,$alturaLinea,utf8_decode(' '),'R',0,'C',False);
        }
      }
      $pdf->SetTextColor(0,0,0);



      if($tipoPlanilla == 4){
        $pdf->Cell(0,$alturaLinea,$entregasEstudiante,'R',0,'C',False);
      }
      // Termina donde se cambia de acuerdo a la plantilla



      $pdf->SetXY($x, $y);
      $pdf->Cell(0,$alturaLinea,'','B',1);
      $linea++;
    }
    //Termina impresión de estudiantes de la sede
  $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
  $pdf->Ln(7);
  $alturaCuadroFilas = $alturaLinea * ($linea-1);
  $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
  include 'planillas_footer.php';
  }
}
else{
  // Cuando piden la planilla vacia
  $pdf->AddPage();
  $pdf->SetTextColor(0,0,0);
  $pdf->SetFillColor(255,255,255);
  $pdf->SetDrawColor(0,0,0);

  $pdf->SetFont('Arial','',$tamannoFuente);
  $lineas = 20;
  $linea = 1;
  $alturaLinea = 5;



  $codigoSede = null;






  include 'planillas_header.php';
  $pdf->SetLineWidth(.05);
    $pdf->SetFont('Arial','',$tamannoFuente);
    //Inicia impresión de estudiantes de la sede
    $nEstudiante = 0;
    $pdf->SetFont('Arial','',$tamannoFuente);
    for($i = 0 ; $i < 20 ; $i++){
      $nEstudiante++;
      if($linea > $lineas){
        $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
        $pdf->Ln(7);
        $alturaCuadroFilas = $alturaLinea * ($linea-1);
        $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
        include 'planillas_footer.php';
        $pdf->AddPage();
        include 'planillas_header.php';
        $pdf->SetFont('Arial','',$tamannoFuente);
        $linea = 1;
      }
      $x = $pdf->GetX();
      $y = $pdf->GetY();
      $pdf->Cell(8,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(10,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(20,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(14,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(7,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(13,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $dia = 0;




      // Aqui es donde se cambia de acuerdo a la plantilla
      for($j = 0 ; $j < 30 ; $j++){
        $pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      }
      // Termina donde se cambia de acuerdo a la plantilla



      $pdf->SetXY($x, $y);
      $pdf->Cell(0,$alturaLinea,'','B',1);
      $linea++;
    }
    //Termina impresión de estudiantes de la sede
  $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
  $pdf->Ln(7);
  $alturaCuadroFilas = $alturaLinea * ($linea-1);
  $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
  include 'planillas_footer.php';
} // Termina el else para la planilla vacia

$pdf->Output();


function mesNombre($mes){
  if($mes == 1){
    return 'Enero';
  }
  else if($mes == 2){
    return 'Febrero';
  }
  else if($mes == 3){
    return 'Marzo';
  }
  else if($mes == 4){
    return 'Abril';
  }
  else if($mes == 5){
    return 'Mayo';
  }
  else if($mes == 6){
    return 'Junio';
  }
  else if($mes == 7){
    return 'Julio';
  }
  else if($mes == 8){
    return 'Agosto';
  }
  else if($mes == 9){
    return 'Septiembre';
  }
  else if($mes == 10){
    return 'Octubre';
  }
  else if($mes == 11){
    return 'Noviembre';
  }
  else if($mes == 12){
    return 'Diciembre';
  }
}
