<?php
include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
require('rotation.php');
set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');


$tamannoFuente = 7;
$mes = $_POST['mes'];
$anno = $_SESSION['p_ano'];
$tipoComplemento = $_POST['tipo'];
$operador = $_SESSION['p_Operador'];
$institucion = $_POST['institucion'];
$municipioNm = $_POST['municipioNm'];
$tipoPlanilla = $_POST['tipoPlanilla'];
$periodoActual = $_SESSION['periodoActual'];
$departamento = $_SESSION['p_Departamento'];
$sedeParametro = (isset($_POST['sede']) && $_POST['sede'] != '') ? $_POST['sede'] : "";

// Variables para el rango de fechas de búsqueda.
if (isset($_POST["semana_inicial"]) && $_POST["semana_inicial"] != "") {
  $semanaInicial = mysqli_real_escape_string($Link, $_POST["semana_inicial"]);
  $diaInicialSemanaInicial = mysqli_real_escape_string($Link, $_POST["diaInicialSemanaInicial"]);
  $diaFinalSemanaInicial = mysqli_real_escape_string($Link, $_POST["diaFinalSemanaInicial"]);
} else {
  $semanaInicial = $diaInicialSemanaInicial = $diaFinalSemanaInicial = "";
}

if (isset($_POST["semana_final"]) && $_POST["semana_final"] != "") {
  $semanaFinal = mysqli_real_escape_string($Link, $_POST["semana_final"]);
  $diaInicialSemanaFinal = mysqli_real_escape_string($Link, $_POST["diaInicialSemanaFinal"]);
  $diaFinalSemanaFinal = mysqli_real_escape_string($Link, $_POST["diaFinalSemanaFinal"]);
} else {
  $semanaFinal = $diaInicialSemanaFinal = $diaFinalSemanaFinal = "";
}


$anno2d = substr($anno,2);
if($mes < 10){
  if(substr($mes,0,1)!="0"){
    $mes = '0'.$mes;
  }
}


//Primera consulta: los dias de la s entregas
$consulta = "SELECT ID, ANO, MES, D1, D2, D3, D4, D5, D6, D7, D8, D9, D10,D11,D12,D13,D14,D15,D16,D17,D18,D19,D20,D21,D22,D23,D24,D25,D26,D27,D28,D29,D30,D31 FROM planilla_dias where ano='$anno' AND mes='$mes'";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if ($resultado->num_rows >= 1) {
  while ($row = $resultado->fetch_assoc()) {
    $dias = $row;
  }
}

// Revisando si tiene más de un mes
$aux = 0;
$auxVal = 0;
$registro = 0;
$totalDias = 0;
$mesAdicional = 0;
$dia_consulta = "";
$dias_encabezado = [];
// $cantidad_remplazo = 1;
foreach ($dias as $clave => $dia) {
  if($aux > 2 && $dia != ''){
    $totalDias++;

    if( $auxVal < intval($dia)){
      $auxVal = intval($dia);
    }
    else{
      $mesAdicional++;
    }

    if ($dia >= $diaInicialSemanaInicial && $dia <= $diaFinalSemanaFinal) {
      if ($registro == 0) { $primer_dia = substr($clave, 1); }
      $registro++;
      $dia_consulta .=  $clave .", ";
      $dias_encabezado[$clave] = $dia;
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

// Consulta que retorna el identificador y el nombre del registro dónde etnia es igual a "No aplica".
$res_cod_etnia = $Link->query("SELECT * FROM etnia WHERE UPPER(DESCRIPCION) LIKE '%NO APLICA%'") or die (mysqli_error($Link));
if ($res_cod_etnia->num_rows > 0) {
  $datos_etnia = $res_cod_etnia->fetch_assoc();
}

class PDF extends PDF_Rotate
{
  function set_data($data) {
    $this->tipoPlanilla = $data;
  }

  function Header() {
    $tamannoFuente = 10;
    $logoInfopae = $_SESSION['p_Logo ETC'];
    if ($this->tipoPlanilla == 5) {
      $tituloPlanilla = "Registro de novedades - repitentes y/o suplentes del programa de alimentaciÓn escolar - pae";
    } else if ($this->tipoPlanilla == 6) {
      $tituloPlanilla = "Registro de novedades - suplentes del programa de alimentaciÓn escolar - pae";
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

if($tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4)
{
  $consulta = "SELECT id, tipo_doc, num_doc, tipo_doc_nom, nom1, nom2, ape1, ape2, etnia, genero, edad, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente,edad, zona_res_est, id_disp_est, TipoValidacion, activo, tipo_complem, ". trim($dia_consulta, ", ") ."
  FROM entregas_res_$mes$anno2d WHERE cod_inst=$institucion AND tipo_complem='$tipoComplemento'";
  if($sedeParametro != ''){ $consulta .= " and cod_sede = '$sedeParametro' AND tipo = 'F'"; }
  $consulta .= " ORDER BY cod_sede, cod_grado, nom_grupo, ape1,ape2,nom1,nom2 asc ";
  // echo $consulta;
  $resultado = $Link->query($consulta) or die ('Unable to execute query. Tercera consulta: los niños<br>'.$consulta.'<br>'.mysqli_error($Link));

  $codigo = '';
  if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()){
      if($codigo != $row['cod_sede']){
        $codigo = $row['cod_sede'];
      }
      $estudiantes[$codigo][] = $row;
    }
  } else {
    echo "<script>alert('No existen registros con los filtros seleccionados.'); window.close(); </script>";
  }


  foreach ($estudiantes as $estudiantesSede) {
    // Consulta que retorna la cantidad de estudiantes de una sede seleccionada.
    $codigoSede = $estudiantesSede[0]['cod_sede'];
    $consulta = "SELECT count(id) AS titulares, sum(". str_replace(",", "+", trim($dia_consulta, ", ")) .") AS entregas FROM entregas_res_$mes$anno2d WHERE cod_inst='$institucion' AND tipo_complem ='$tipoComplemento' AND cod_sede = '$codigoSede'";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. <br>'.$consulta.'<br>'. mysqli_error($Link));
    if($resultado->num_rows > 0) {
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

    include 'planillas_header_v2.php';

    $pdf->SetLineWidth(.05);
    //Inicia impresión de estudiantes de la sede
    $nEstudiante = 0;
    $pdf->SetFont('Arial','',$tamannoFuente);

    $racionesProgramadas = 0;
    foreach ($estudiantesSede as $estudiante) {
      $nEstudiante++;
      if($linea > $lineas) {
        $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
        $pdf->Ln(7);
        $alturaCuadroFilas = $alturaLinea * ($linea-1);
        $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
        include 'planillas_footer_v2.php';
        $pdf->AddPage();
        $pagina++;
        include 'planillas_header_v2.php';
        $pdf->SetFont('Arial','',$tamannoFuente);
        $linea = 1;
      }

      $x = $pdf->GetX();
      $y = $pdf->GetY();
      $pdf->Cell(8,$alturaLinea,utf8_decode($nEstudiante),'R',0,'C',False);
      $pdf->Cell(10,$alturaLinea,utf8_decode($estudiante['tipo_doc_nom']),'R',0,'C',False);
      $pdf->Cell(22,$alturaLinea,utf8_decode($estudiante['num_doc']),'R',0,'L',False);
      $pdf->Cell(28,$alturaLinea,utf8_decode($estudiante['nom1']),'R',0,'L',False);
      $pdf->Cell(28,$alturaLinea,utf8_decode($estudiante['nom2']),'R',0,'L',False);
      $pdf->Cell(28,$alturaLinea,utf8_decode($estudiante['ape1']),'R',0,'L',False);
      $pdf->Cell(28,$alturaLinea,utf8_decode($estudiante['ape2']),'R',0,'L',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode($estudiante["edad"]),'R',0,'C',False);
      $pdf->Cell(7,$alturaLinea,utf8_decode(($estudiante['etnia'] == $datos_etnia["ID"]) ? "" : "X"),'R',0,'C',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode($estudiante['genero']),'R',0,'C',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode($estudiante['cod_grado']),'R',0,'C',False);
      $pdf->Cell(8,$alturaLinea,utf8_decode($estudiante['nom_grupo']),'R',0,'C',False);
      $pdf->Cell(13,$alturaLinea,utf8_decode($tipoComplemento),'R',0,'C',False);
      $dia = $primer_dia;

      // Aqui es donde se cambia de acuerdo a la plantilla
      $entregasEstudiante = 0;
      for($j = 0 ; $j < 24 ; $j++) {
          if($tipoPlanilla != 2) {
            if($tipoPlanilla == 3) { $pdf->SetTextColor(190,190,190); }

            // if($dia < 10){ $auxDia = 'D0'.$dia; } else { $auxDia = 'D'.$dia; }
            $auxDia = 'D'.$dia;

            if(isset($estudiante[$auxDia]) && $estudiante[$auxDia] == 1 && $tipoPlanilla != 6) {
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

        $dia++;
      }
      $pdf->SetTextColor(0,0,0);


      if($tipoPlanilla == 4) { $pdf->Cell(0,$alturaLinea,$entregasEstudiante,'R',0,'C',False); }
      // Termina donde se cambia de acuerdo a la plantilla

      $pdf->SetXY($x, $y);
      $pdf->Cell(0,$alturaLinea,'','B',1);
      $linea++;
      $racionesProgramadas += $entregasEstudiante;
    }



        //Termina impresión de estudiantes de la sede
        $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
        $pdf->Ln(7);
        $alturaCuadroFilas = $alturaLinea * ($linea-1);
        $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);

        include 'planillas_footer_v2.php';
  }
}
else if ($tipoPlanilla == 5)
{
  foreach ($sedes as $sede)
  {
      $linea = 1;
      $lineas = 25;
      $codigoSede = $sede['cod_sede'];
      $pdf->AddPage();
      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255,255,255);
      $pdf->SetDrawColor(0,0,0);

      include 'planillas_header_v2.php';
      $pdf->SetLineWidth(.05);

      for($i = 0 ; $i < 25 ; $i++)
      {
        if($linea > $lineas)
        {
          $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
          $pdf->Ln(7);
          $alturaCuadroFilas = $alturaLinea * ($linea-1);
          $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
          include 'planillas_footer_v2.php';
          $pdf->AddPage();
          include 'planillas_header_v2.php';
          $pdf->SetFont('Arial','',$tamannoFuente);
          $linea = 1;
        }

        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(8,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(10,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(22,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.4,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.4,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.4,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.4,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(5,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(5,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(8,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(13,$alturaLinea,"",'R',0,'C',False);

        // Aqui es donde se cambia de acuerdo a la plantilla
        for($j = 0 ; $j < 24 ; $j++)
        {
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

      include 'planillas_footer_v2.php';
  }
}
else if ($tipoPlanilla == 6)
{
  $consulta = "SELECT id, tipo_doc, num_doc, tipo_doc_nom, nom1, nom2, ape1, ape2, etnia, genero, edad, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente,edad, zona_res_est, id_disp_est, TipoValidacion, activo
  FROM suplentes WHERE cod_inst=$institucion /*AND tipo_complem='$tipoComplemento'*";
  if($sedeParametro != ''){ $consulta .= " and cod_sede = '$sedeParametro' "; }
  $consulta .= " ORDER BY cod_sede, cod_grado, nom_grupo, ape1,ape2,nom1,nom2 asc ";
  $resultado = $Link->query($consulta) or die ('Unable to execute query. Tercera consulta: los niños<br>'.$consulta.'<br>'.mysqli_error($Link));

  $codigo = '';
  if($resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
      if($codigo != $row['cod_sede']) {
        $codigo = $row['cod_sede'];
      }
      $estudiantes[$codigo][] = $row;
    }
  } else {
    echo "<script>alert('No existen registros con los filtros seleccionados.'); window.close(); </script>";
  }

  foreach ($estudiantes as $estudiantesSede)
  {
    $linea = 1;
    $pagina = 1;
    $codigoSede = $estudiantesSede[0]['cod_sede'];
    $paginas = ceil(count($estudiantesSede) / $lineas);

    $pdf->AddPage();
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetDrawColor(0,0,0);

    include 'planillas_header_v2.php';

    $pdf->SetLineWidth(.05);
    //Inicia impresión de estudiantes de la sede
    $nEstudiante = 0;
    $pdf->SetFont('Arial','',$tamannoFuente);
    foreach ($estudiantesSede as $estudiante)
    {
      $nEstudiante++;
      if($linea > $lineas)
      {
        $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
        $pdf->Ln(7);
        $alturaCuadroFilas = $alturaLinea * ($linea-1);
        $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
        include 'planillas_footer_v2.php';
        $pdf->AddPage();
        $pagina++;
        include 'planillas_header_v2.php';
        $pdf->SetFont('Arial','',$tamannoFuente);
        $linea = 1;
      }

      $x = $pdf->GetX();
      $y = $pdf->GetY();
      $pdf->Cell(8,$alturaLinea,utf8_decode($nEstudiante),'R',0,'C',False);
      $pdf->Cell(10,$alturaLinea,utf8_decode($estudiante['tipo_doc_nom']),'R',0,'C',False);
      $pdf->Cell(22,$alturaLinea,utf8_decode($estudiante['num_doc']),'R',0,'L',False);
      $pdf->Cell(31.4,$alturaLinea,utf8_decode($estudiante['nom1']),'R',0,'L',False);
      $pdf->Cell(31.4,$alturaLinea,utf8_decode($estudiante['nom2']),'R',0,'L',False);
      $pdf->Cell(31.4,$alturaLinea,utf8_decode($estudiante['ape1']),'R',0,'L',False);
      $pdf->Cell(31.4,$alturaLinea,utf8_decode($estudiante['ape2']),'R',0,'L',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode($estudiante["edad"]),'R',0,'C',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode($estudiante["cod_grado"]),'R',0,'C',False);
      $pdf->Cell(8,$alturaLinea,utf8_decode($estudiante["nom_grupo"]),'R',0,'C',False);
      $pdf->Cell(13,$alturaLinea,utf8_decode($tipoComplemento),'R',0,'C',False);
      $dia = 0;

      // Aqui es donde se cambia de acuerdo a la plantilla
      $entregasEstudiante = 0;
      for($j = 0 ; $j < 24 ; $j++) {
        $pdf->Cell(6,$alturaLinea,utf8_decode(' '),'R',0,'C',False);
      }
      $pdf->SetTextColor(0,0,0);
      $pdf->SetXY($x, $y);
      $pdf->Cell(0,$alturaLinea,'','B',1);
      $linea++;
    }

    //Termina impresión de estudiantes de la sede
    $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
    $pdf->Ln(7);
    $alturaCuadroFilas = $alturaLinea * ($linea-1);
    $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);

    include 'planillas_footer_v2.php';
  }
}
else if ($tipoPlanilla == 7)
{
  foreach ($sedes as $sede)
  {
    $codigoSede = $sede['cod_sede'];
    $consulta_suplente_repitentes_sede = "SELECT id, tipo_doc, num_doc, tipo_doc_nom, nom1, nom2, ape1, ape2, etnia, genero, edad, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente,edad, zona_res_est, id_disp_est, TipoValidacion, activo FROM entregas_res_$mes$anno2d WHERE cod_inst=$institucion AND cod_sede = '$codigoSede' AND (tipo = 'S' OR tipo = 'R') AND tipo_complem='$tipoComplemento' ORDER BY cod_sede, cod_grado, nom_grupo, ape1,ape2,nom1,nom2 ASC";
    $respuesta_suplente_repitentes_sede = $Link->query($consulta_suplente_repitentes_sede) or die("Error al consultar suplentes y repitentes en entregas_res_$mes$anno2d: ". $Link->error);
    if ($respuesta_suplente_repitentes_sede->num_rows > 0)
    {
      $linea = 1;
      $lineas = 25;
      $codigoSede = $sede['cod_sede'];
      $pdf->AddPage();
      $pdf->SetTextColor(0,0,0);
      $pdf->SetFillColor(255,255,255);
      $pdf->SetDrawColor(0,0,0);

      include 'planillas_header_v2.php';
      $pdf->SetLineWidth(.05);

      while($suplente_repitente_sede = $respuesta_suplente_repitentes_sede->fetch_assoc())
      {
        if($linea > $lineas)
        {
          $pdf->SetXY($xCuadroFilas, $yCuadroFilas);
          $pdf->Ln(7);
          $alturaCuadroFilas = $alturaLinea * ($linea-1);
          $pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
          include 'planillas_footer_v2.php';
          $pdf->AddPage();
          include 'planillas_header_v2.php';
          $pdf->SetFont('Arial','',$tamannoFuente);
          $linea = 1;
        }

        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(8,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(10,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(22,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.4,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.4,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.4,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(31.4,$alturaLinea,"",'R',0,'L',False);
        $pdf->Cell(5,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(5,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(8,$alturaLinea,"",'R',0,'C',False);
        $pdf->Cell(13,$alturaLinea,"",'R',0,'C',False);

        // Aqui es donde se cambia de acuerdo a la plantilla
        for($j = 0 ; $j < 24 ; $j++)
        {
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

      include 'planillas_footer_v2.php';
    }
    else
    {
      echo "<script>alert('No existen registros con los filtros seleccionados.'); window.close(); </script>";
    }
  }
}
else
{
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

    include 'planillas_header_v2.php';
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
        include 'planillas_footer_v2.php';
        $pdf->AddPage();
        include 'planillas_header_v2.php';
        $pdf->SetFont('Arial','',$tamannoFuente);
        $linea = 1;
      }
      $x = $pdf->GetX();
      $y = $pdf->GetY();
      $pdf->Cell(8,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(10,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(22,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(28,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(28,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(28,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(28,$alturaLinea,utf8_decode(''),'R',0,'L',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(7,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
      $pdf->Cell(8,$alturaLinea,utf8_decode(''),'R',0,'C',False);
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
    include 'planillas_footer_v2.php';
}

$pdf->Output();

function mesNombre($mes)
{
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
