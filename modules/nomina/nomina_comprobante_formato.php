<?php
error_reporting(E_ALL);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
include '../../php/funciones.php';
require('../../fpdf181/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        // $this->RoundedRect(13, 7, 117, 29, 3, '1234', '');
        $this->SetTitle(utf8_decode('Factura de venta'));
        //izquierda
  		  $this->Image($this->parametros["LogoOperador"], 13 ,7, 90, 25,'png', '');
        $cx = 110;
        $cy = 9;
        $this->setXY($cx, $cy);
        $this->SetFont('Arial','B',$this->enc_font+2);
        $this->Cell(93, 7, utf8_decode('Comprobante de pago '.$this->numero),'',0,'L',0);
        $this->SetFont('Arial','B',$this->enc_font);
        $this->setXY($cx, $cy+7);
        $this->Cell(23, 7, utf8_decode('Fecha : '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->Cell(70, 7, utf8_decode($this->fecha),'',0,'L',0);
        $this->SetFont('Arial','B',$this->enc_font);
        $cy = $this->getY();
        $this->setXY($cx, $cy+7);
        $this->Cell(23, 7, utf8_decode('Tipo : '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->Cell(70, 7, utf8_decode($this->tipo),'',0,'L',0);
        $this->SetFont('Arial','B',$this->enc_font);
        $cy = $this->getY()+5;
        $this->setXY(13, $cy);
        $this->Cell(196, 7, utf8_decode(''),'B',1,'C',0);
        $this->ln(3);

        $this->SetFont('Arial','B',$this->enc_font);
        $this->Cell(66, 7, utf8_decode('Nombre beneficiario '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->Cell(70, 7, utf8_decode($this->nombre),'',1,'L',0);

        $this->SetFont('Arial','B',$this->enc_font);
        $this->Cell(66, 7, utf8_decode('Cédula / NIT '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->Cell(70, 7, utf8_decode($this->Nitcc),'',1,'L',0);

        $this->SetFont('Arial','B',$this->enc_font);
        $this->Cell(66, 7, utf8_decode('Periodo líquidado '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->Cell(70, 7, utf8_decode($this->mes." ".$this->periodo),'',1,'L',0);

        $this->SetFont('Arial','B',$this->enc_font);
        $this->Cell(66, 7, utf8_decode('Tipo de contrato '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->Cell(70, 7, utf8_decode($this->TipoContrato),'',1,'L',0);

        $this->SetFont('Arial','B',$this->enc_font);
        $this->Cell(66, 7, utf8_decode('Concepto '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->MultiCell(130, 7, utf8_decode('Servicios cómo manipuladora de alimentos en las siguientes sedes : '),'','L',0);

        $this->SetFont('Arial','B',$this->enc_font);
        $this->Cell(66, 7, utf8_decode('Municipio '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->Cell(70, 7, utf8_decode(ucwords(mb_strtolower($this->muni))),'',1,'L',0);

        $this->SetFont('Arial','B',$this->enc_font);
        $this->Cell(66, 7, utf8_decode('Institución '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->MultiCell(130, 7, utf8_decode(ucwords(mb_strtolower($this->inst))),'','L',0);

        $this->SetFont('Arial','B',$this->enc_font);
        $this->Cell(66, 7, utf8_decode('Sede educativa '),'',0,'L',0);
        $this->SetFont('Arial','',$this->enc_font);
        $this->MultiCell(130, 7, utf8_decode(ucwords(mb_strtolower($this->sed))),'','L',0);


        $this->Cell(196, 7, utf8_decode(''),'B',1,'C',0);

        $this->SetFont('Arial','B',$this->title_font);
        $this->Cell(28, 7, utf8_decode('Complemento'),'B',0,'C',0);
        $this->Cell(28, 7, utf8_decode('Liquida por'),'B',0,'C',0);
        $this->Cell(38, 7, utf8_decode('Raciones promedio día'),'B',0,'C',0);
        $this->Cell(18, 7, utf8_decode('Días'),'B',0,'C',0);
        $this->Cell(28, 7, utf8_decode('Valor Ración'),'B',0,'C',0);
        $this->Cell(28, 7, utf8_decode('Valor Día'),'B',0,'C',0);
        $this->Cell(28, 7, utf8_decode('Total'),'B',1,'C',0);
        $this->SetFont('Arial','',$this->body_font);
    }

    function Footer()
    {
    	$this->SetFont('Arial', '', 8);
        $this->SetXY(23, -21);
        $this->Cell(78, 7 , utf8_decode('Firma pagador'),'T',1,'C');
        $this->SetXY(121, -21);
        $this->Cell(78, 7 , utf8_decode('Firma beneficiario'),'T',1,'C');
        $this->SetXY(7, -14);
        $this->Cell(196, 7 , utf8_decode('Impreso por InfoPAE © '.date('Y')),'',1,'C');
        $this->SetXY(195, -14);
        $this->Cell(7, 7 , utf8_decode('Pagina N° '.$this->PageNo()),'',1,'C');
    }

    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

    function formatMoney($number, $decimals = 2){
    	return "$".number_format($number, $decimals, ',', '.');
    }
}

$pdf = new PDF('P', 'mm', array(216, 279));
$pdf->AliasNbPages();
$pdf->SetMargins(13, 7);
$cc = isset($_POST['cc_empleado']) ? $_POST['cc_empleado'] : '1100959620';
$num_liq = isset($_POST['num_liq']) ? $_POST['num_liq'] : NULL;
$periodoActual = $_SESSION['periodoActual'];
$tipo_empleado = [
					1 => 'Empleado',
					2 => 'Manipulador',
					3 => 'Contratista',
					4 => 'Transportador',
				];
$tipo_contrato = [
					1 => 'OPS',
					2 => 'Nómina',
					3 => 'Obra labor',
					4 => 'Servicios',
				];
$liquida_por_arr = [
					1 => 'Día',
					2 => 'Ración',
					3 => 'Mes',
					4 => 'Factura',
					];
$meses_texto = [
                    '01' => 'Enero',
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
                    '12' => 'Diciembre'
                  ];
$consulta_parametros = "SELECT * FROM parametros WHERE id = 1";
$result_parametros = $Link->query($consulta_parametros);
$parametros = false;
if ($result_parametros->num_rows > 0) {
    $parametros = $result_parametros->fetch_assoc();
}
$pdf->parametros = $parametros;
$pdf->enc_font = 13;
$pdf->title_font = 9;
$pdf->body_font = 8;
$pdf->SetFillColor(208, 208, 208);

$consulta = "SELECT 
				  pagos_nomina.*,
                  CONCAT(pagos_nomina.documento, '-', pagos_nomina.numero) AS numero,
                  CONCAT('De ', pagos_nomina.semquin_inicial, ' a ', pagos_nomina.semquin_final) as periodo,
                  empleados.nombre,
                  empleados.Nitcc,
                  empleados.TipoContrato,
                  empleados.tipo,
                  ubicacion.ciudad,
                  sedes.cod_inst,
                  sedes.nom_sede,
                  instituciones.nom_inst,
                  tipo_complem,
                  (pagos_nomina.auxilio_transporte + pagos_nomina.auxilio_extra + pagos_nomina.otros_devengados) as total_devengados,
                  (pagos_nomina.desc_eps + pagos_nomina.desc_afp + pagos_nomina.otros_deducidos + pagos_nomina.retefuente + pagos_nomina.reteica) as tota_deducidos
              FROM pagos_nomina 
                LEFT JOIN empleados ON empleados.Nitcc = pagos_nomina.doc_empleado
                LEFT JOIN ubicacion ON ubicacion.CodigoDANE = pagos_nomina.cod_mun_sede
                LEFT JOIN sedes$periodoActual as sedes ON sedes.cod_sede = pagos_nomina.cod_sede
                LEFT JOIN instituciones ON instituciones.codigo_inst = sedes.cod_inst
              WHERE pagos_nomina.doc_empleado = '$cc'
              ".($num_liq ? " AND pagos_nomina.numero = '".$num_liq."'" : "")."
              ORDER BY pagos_nomina.numero ASC, pagos_nomina.cod_mun_sede ASC, sedes.cod_inst ASC, pagos_nomina.cod_sede ASC";
// exit($consulta);
$numero = "";
$muni = "";
$inst = "";
$sed = "";
$resultado = $Link->query($consulta);
$total = 0;
if($resultado->num_rows > 0)
{
  while($registros = $resultado->fetch_assoc())
  {

  	$print_new_muni = false;
  	$print_new_inst = false;
  	$print_new_sede = false;

	if ($muni == '' || (($muni != $registros['ciudad']))) {
		if ($muni != '') {
			$print_new_muni = true;
		}
		$muni = $registros['ciudad'];
		$pdf->muni = $muni;
	}

	if ($inst == '' || (($inst != $registros['nom_inst']))) {
		if ($inst != '') {
		  	$print_new_inst = true;
		}
		$inst = $registros['nom_inst'];
		$pdf->inst = $inst;
	}

	if ($sed == '' || (($sed != $registros['nom_sede']))) {
		if ($sed != '') {
		  	$print_new_sede = true;
		}
		$sed = $registros['nom_sede'];
		$pdf->sed = $sed;
	}

  	if ($numero == "" || ($numero != $registros['numero'])) {
		$numero = $registros['numero'];
  		$fecha = $registros['Fecha'];
  		$numeroDia = date('d', strtotime($fecha));
		$dia = date('l', strtotime($fecha));
		$mes = date('F', strtotime($fecha));
		$anio = date('Y', strtotime($fecha));
		$fecha_text = $mes." ".$numeroDia." de ".$anio;
		$pdf->fecha = $fecha_text;
  		$pdf->numero = $registros['numero'];
  		$pdf->tipo = $tipo_empleado[$registros['tipo']];
  		$pdf->nombre = $registros['nombre'];
  		$pdf->Nitcc = $registros['Nitcc'];
  		$pdf->periodo = $registros['periodo'];
  		$pdf->TipoContrato = $tipo_contrato[$registros['TipoContrato']];
  		$pdf->mes = $meses_texto[$registros['mes']];
  		if ($total > 0) {
  			$pdf->ln(3);
  			$pdf->SetFont('Arial','B',$pdf->enc_font);
        	$pdf->Cell(166, 7, utf8_decode('Valor total pagado    '),'',0,'R',1);
        	$pdf->Cell(30, 7, utf8_decode($pdf->formatMoney($total)),'',0,'R',1);
        	$pdf->SetFont('Arial','',$pdf->body_font);
  		}
		$pdf->AddPage();
		$total = 0;
  	}

  	if ($print_new_muni == true) {
  		$pdf->SetFont('Arial','B',$pdf->enc_font);
        $pdf->Cell(66, 7, utf8_decode('Municipio '),'',0,'L',0);
        $pdf->SetFont('Arial','',$pdf->enc_font);
        $pdf->Cell(70, 7, utf8_decode(ucwords(mb_strtolower($pdf->muni))),'',1,'L',0);
        $pdf->SetFont('Arial','',$pdf->body_font);
  	}

  	if ($print_new_inst == true) {
        $pdf->SetFont('Arial','B',$pdf->enc_font);
        $pdf->Cell(66, 7, utf8_decode('Institución '),'',0,'L',0);
        $pdf->SetFont('Arial','',$pdf->enc_font);
        $pdf->MultiCell(130, 7, utf8_decode(ucwords(mb_strtolower($pdf->inst))),'','L',0);
        $pdf->SetFont('Arial','',$pdf->body_font);
  	}

  	if ($print_new_sede == true) {
        $pdf->SetFont('Arial','B',$pdf->enc_font);
        $pdf->Cell(66, 7, utf8_decode('Sede educativa '),'',0,'L',0);
        $pdf->SetFont('Arial','',$pdf->enc_font);
        $pdf->MultiCell(130, 7, utf8_decode(ucwords(mb_strtolower($pdf->sed))),'','L',0);
        $pdf->SetFont('Arial','',$pdf->body_font);
  	}

    $pdf->Cell(28, 7, utf8_decode($registros['tipo_complem']),'B',0,'C',0);
    $pdf->Cell(28, 7, utf8_decode($liquida_por_arr[$registros['liquida_por']]),'B',0,'C',0);
    $pdf->Cell(38, 7, utf8_decode($registros['cobertura']),'B',0,'C',0);
    $pdf->Cell(18, 7, utf8_decode($registros['dias_laborados']),'B',0,'C',0);
    $pdf->Cell(28, 7, utf8_decode($registros['liquida_por'] == 2 ? $pdf->formatMoney($registros['valor_base']) : 0),'B',0,'C',0);
    $pdf->Cell(28, 7, utf8_decode($registros['liquida_por'] == 1 ? $pdf->formatMoney($registros['valor_base']) : 0),'B',0,'C',0);
    $pdf->Cell(28, 7, utf8_decode($pdf->formatMoney($registros['total_pagado'])),'B',1,'C',0);
	$total += $registros['total_pagado'];
  }
}

if ($total > 0) {
	$pdf->ln(3);
	$pdf->SetFont('Arial','B',$pdf->enc_font);
	$pdf->Cell(166, 7, utf8_decode('Valor total pagado     '),'',0,'R',1);
	$pdf->Cell(30, 7, utf8_decode($pdf->formatMoney($total)),'',0,'R',1);
	$pdf->SetFont('Arial','',$pdf->body_font);
}

$pdf->SetFont('Arial','',5);
$pdf->Output("comprobante_pago.pdf", "I");