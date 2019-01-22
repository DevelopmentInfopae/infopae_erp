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
$tamannoFuente = 7;

// Variables de inicio
$municipioNm = $_POST['municipioNm'];
$operador = $_SESSION['p_Operador'];


$departamento = $_SESSION['p_Departamento'];
$anno = $_SESSION['p_ano'];
$anno2d = substr($anno,2);
$tipoComplemento = $_POST['tipo'];
$tipoPlanilla = $_POST['tipoPlanilla'];

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
$consulta_sedes = "SELECT DISTINCT s.cod_sede, s.cod_inst,s.nom_inst,s.nom_sede,s.cod_mun_sede,sc.num_est_focalizados
            FROM sedes$periodoActual s
            INNER JOIN sedes_cobertura AS sc ON (s.cod_inst=sc.cod_inst AND s.cod_Sede=sc.cod_Sede)
            WHERE sc.cod_inst= '$institucion' AND sc.ano = '$anno' AND sc.mes='$mes'";
if($sedeParametro != '') { $consulta_sedes .= " and sc.cod_sede = '$sedeParametro' "; }
$resultado_sedes = $Link->query($consulta_sedes) or die ('Unable to execute query. '. mysqli_error($Link));

if($resultado_sedes->num_rows > 0) {
  while($registros_sedes = $resultado_sedes->fetch_assoc()) {
    $codigo = $registros_sedes['cod_sede'];
    $sedes[$codigo] = $registros_sedes;
  }
} else {
    echo "<script>alert('No existe datos para los parametros seleccionados.'); window.close();</script>";
}


if($tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4) {
	$consulta = "SELECT id, tipo_doc, num_doc, tipo_doc_nom, nom1, nom2, ape1, ape2, etnia, genero, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben,
	cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente,edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo_complem, D1 AS 'D01',D2 AS D02,D3 AS D03,D4 AS D04,D5 AS D05,D6 AS D06,D7 AS D07,D8 AS D08,D9 AS D09,D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22
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


class PDF extends PDF_Rotate
{
  function set_data($data) {
    $this->tipoPlanilla = $data;
  }

  function Header() {
    $tamannoFuente = 7;
    $logoInfopae = $_SESSION['p_Logo ETC'];
    if ($this->tipoPlanilla == 5) {
      $tituloPlanilla = "Registro de novedades - repitentes del programa de alimentaciÓn escolar - pae";
    } else {
      $tituloPlanilla = "REGISTRO Y CONTROL DIARIO DE ASISTENCIA DE TITULAR DE DERECHO DEL PROGRAMA DE ALIMENTACIÓN ESCOLAR - PAE";
    }


    $this->SetFont('Arial','B',$tamannoFuente);
    $this->Image($logoInfopae, 3 ,3, 100, 18,'jpg', '');
    $this->SetTextColor(0,0,0);
    $this->Cell(100, 18);
    $this->Cell(0,18.1,utf8_decode(strtoupper($tituloPlanilla)),0,0,'C',False);
    $this->Ln(10);
    $this->SetLineWidth(0.8);
    $this->Cell(0,10,'','B',0,'C',False);
    $this->Ln(10);
  }

  function Footer() {
      $tamannoFuente = 8;
      $this->SetY(-15);
      $this->SetFont('Arial','I',8);
  }

  function RotatedText($x,$y,$txt,$angle) {
  	$this->Rotate($angle,$x,$y);
  	$this->Text($x,$y,$txt);
  	$this->Rotate(0);
  }

  function RotatedImage($file,$x,$y,$w,$h,$angle) {
  	$this->Rotate($angle,$x,$y);
  	$this->Image($file,$x,$y,$w,$h);
  	$this->Rotate(0);
  }
}

//CREACION DEL PDF
// Creación del objeto de la clase heredada
$pdf= new PDF('L','mm',array(356,216));
$pdf->set_data($tipoPlanilla);
$pdf->SetMargins(3, 4, 3, 3);
$pdf->SetAutoPageBreak(false,5);
$pdf->AliasNbPages();
include '../../php/funciones.php';


$lineas = 25;
$alturaLinea = 4;

if($tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4){
    foreach ($estudiantes as $estudiantesSede) {
        $codigoSede = $estudiantesSede[0]['cod_sede'];

        $consulta = "SELECT count(id) as titulares, sum(D1 +D2 +D3 +D4 +D5 +D6 +D7+D8 +D9+D10+D11+D12+D13+D14+D15+D16+D17+D18+D19+D20+D21+D22) as entregas FROM entregas_res_$mes$anno2d WHERE cod_inst='$institucion' AND tipo_complem ='$tipoComplemento' AND cod_sede = '$codigoSede'";
		if($sedeParametro != '') {
			$consulta .= " and cod_sede = '$sedeParametro' ";
		}
        $resultado = $Link->query($consulta) or die ('Unable to execute query. <br>'.$consulta.'<br>'. mysqli_error($Link));
        if($resultado->num_rows >= 1) {
            while($row = $resultado->fetch_assoc()) {
                $totales = $row;
            }
        }

        $paginas = ceil(count($estudiantesSede) / $lineas);
        $pagina = 1;
        $linea = 1;

        $pdf->AddPage();
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetDrawColor(0,0,0);

        include 'planillas_header.php';

        $pdf->SetLineWidth(.05);
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
          $pdf->Cell(27.2,$alturaLinea,utf8_decode($estudiante['nom1']),'R',0,'L',False);
          $pdf->Cell(27.2,$alturaLinea,utf8_decode($estudiante['nom2']),'R',0,'L',False);
          $pdf->Cell(27.2,$alturaLinea,utf8_decode($estudiante['ape1']),'R',0,'L',False);
          $pdf->Cell(27.2,$alturaLinea,utf8_decode($estudiante['ape2']),'R',0,'L',False);

          $aux = $estudiante['fecha_nac'];
          $aux = date("d/m/Y", strtotime($aux));
          $pdf->Cell(14,$alturaLinea,utf8_decode($aux),'R',0,'C',False);

          $pdf->Cell(7,$alturaLinea,utf8_decode(($estudiante['etnia'] == 1 || $estudiante['etnia'] == 0) ? "" : "X"),'R',0,'C',False);
          $pdf->Cell(5,$alturaLinea,utf8_decode($estudiante['genero']),'R',0,'C',False);
          $pdf->Cell(7,$alturaLinea,utf8_decode($estudiante['cod_grado']),'R',0,'C',False);
          $pdf->Cell(13,$alturaLinea,utf8_decode($tipoComplemento),'R',0,'C',False);
          $dia = 0;

            // Aqui es donde se cambia de acuerdo a la plantilla
            $entregasEstudiante = 0;
            for($j = 0 ; $j < 24 ; $j++) {
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
                    $pdf->Cell(6,$alturaLinea,utf8_decode('x'),'R',0,'C',False);
                    $entregasEstudiante++;
                  }
                  else{
                    $pdf->Cell(6,$alturaLinea,utf8_decode(''),'R',0,'C',False);
                  }

                }
                else{
                  $pdf->Cell(6,$alturaLinea,utf8_decode(' '),'R',0,'C',False);
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
} else if ($tipoPlanilla == 5) {
    foreach ($sedes as $sede){
        $linea = 1;
        $lineas = 25;
        $alturaLinea = 4.5;
        $codigoSede = $sede['cod_sede'];

        $pdf->AddPage();
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetDrawColor(0,0,0);

        include 'planillas_header.php';
        $pdf->SetLineWidth(.05);

        for($i = 0 ; $i < 25 ; $i++) {
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
        $pdf->Cell(8,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(10,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(20,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.7,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.7,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.7,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.7,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(14,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(13,$alturaLinea,"",'R',0,'C',False);

        // Aqui es donde se cambia de acuerdo a la plantilla
        for($j = 0 ; $j < 24 ; $j++){
            $pdf->Cell(6,$alturaLinea,utf8_decode(''),'R',0,'C',False);
        }
        // Termina donde se cambia de acuerdo a la plantilla
        $pdf->SetXY($x, $y);
        $pdf->Cell(0,$alturaLinea,'','B',1);
        $linea++;
        }

        $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
        $pdf->Ln(7);
        $alturaCuadroFilas = $alturaLinea * ($linea-1);
        $pdf->Cell(0,$alturaCuadroFilas,"",1,0,'R',False);

        include 'planillas_footer.php';
    }
} else{
    // Cuando piden la planilla vacia
    $pdf->AddPage();
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);

    $pdf->SetFont('Arial','',$tamannoFuente);
    $lineas = 25;
    $linea = 1;
    $alturaLinea = 4;
    $codigoSede = null;

    include 'planillas_header.php';
    $pdf->SetLineWidth(.05);
    $pdf->SetFont('Arial','',$tamannoFuente);
    //Inicia impresión de estudiantes de la sede
    $nEstudiante = 0;
    $pdf->SetFont('Arial','',$tamannoFuente);
    for($i = 0 ; $i < 25 ; $i++){
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
      $pdf->Cell(27.2,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(27.2,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(27.2,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(27.2,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(14,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(7,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(7,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(13,$alturaLinea,utf8_decode(''),'R',0,'C',False);

      for($j = 0 ; $j < 24 ; $j++){
        $pdf->Cell(6,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      }

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
