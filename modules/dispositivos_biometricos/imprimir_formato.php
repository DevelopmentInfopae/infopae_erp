<?php
require_once '../../db/conexion.php';
require_once '../../config.php';
require_once '../../fpdf181/fpdf.php';

set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

$tamannoFuente = 8;
$periodo_actual = $_SESSION["periodoActual"];
$mes = (isset($_POST["mes"])) ? $Link->real_escape_string($_POST["mes"]) : "";
$semana = (isset($_POST["semana_inicial"])) ? $Link->real_escape_string($_POST["semana_inicial"]) : "";
$municipio = (isset($_POST["municipio"])) ? $Link->real_escape_string($_POST["municipio"]) : "";
$institucion = (isset($_POST["institucion"])) ? $Link->real_escape_string($_POST["institucion"]) : "";
$sede = (isset($_POST["sede"])) ? $Link->real_escape_string($_POST["sede"]) : "";

$condicion_sede = ($sede != "") ? "AND foc.cod_sede = '$sede'" : "";
$consulta_focalizacion = "SELECT
														ubi.Ciudad,
												    ubi.Departamento,
												    sed.nom_inst,
												    sed.nom_sede,
												    foc.cod_grado,
												    foc.nom_grupo,
												    tid.Abreviatura,
												    foc.num_doc,
												    foc.nom1,
												    foc.nom2,
												    foc.ape1,
												    foc.ape2
													FROM focalizacion$semana foc
													INNER JOIN sedes$periodo_actual sed ON sed.cod_inst = foc.cod_inst AND sed.cod_sede = foc.cod_sede
													INNER JOIN tipodocumento tid ON tid.id = foc.tipo_doc
													INNER JOIN ubicacion ubi ON ubi.CodigoDANE = sed.cod_mun_sede
													WHERE foc.cod_inst = '$institucion' $condicion_sede
													ORDER BY nom_inst, nom_sede, nom_grupo, nom1, nom2, ape1, ape2;";
$respuesta_focalizacion = $Link->query($consulta_focalizacion) or die("Error al consultar focalizacion$semana: ". $Link->error);
if ($respuesta_focalizacion->num_rows > 0)
{
	$consulta_parametros = "SELECT * FROM parametros;";
	$respuesta_parametros = $Link->query($consulta_parametros) or die("Error al consultar parametros: ". $Link->error);
	if ($respuesta_parametros->num_rows > 0)
	{
		$parametros = $respuesta_parametros->fetch_assoc();
	}

	class PDF extends FPDF
	{
		protected $B = 0;
		protected $I = 0;
		protected $U = 0;
		protected $HREF = '';

		function WriteHTML($html)
		{
		    // Intérprete de HTML
		    $html = str_replace("\n",' ',$html);
		    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		    foreach($a as $i=>$e)
		    {
		        if($i%2==0)
		        {
		            // Text
		            if($this->HREF)
		                $this->PutLink($this->HREF,$e);
		            else
		                $this->Write(5,$e);
		        }
		        else
		        {
		            // Etiqueta
		            if($e[0]=='/')
		                $this->CloseTag(strtoupper(substr($e,1)));
		            else
		            {
		                // Extraer atributos
		                $a2 = explode(' ',$e);
		                $tag = strtoupper(array_shift($a2));
		                $attr = array();
		                foreach($a2 as $v)
		                {
		                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
		                        $attr[strtoupper($a3[1])] = $a3[2];
		                }
		                $this->OpenTag($tag,$attr);
		            }
		        }
		    }
		}

		function OpenTag($tag, $attr)
		{
		    // Etiqueta de apertura
		    if($tag=='B' || $tag=='I' || $tag=='U')
		        $this->SetStyle($tag,true);
		    if($tag=='A')
		        $this->HREF = $attr['HREF'];
		    if($tag=='BR')
		        $this->Ln(5);
		}

		function CloseTag($tag)
		{
		    // Etiqueta de cierre
		    if($tag=='B' || $tag=='I' || $tag=='U')
		        $this->SetStyle($tag,false);
		    if($tag=='A')
		        $this->HREF = '';
		}

		function SetStyle($tag, $enable)
		{
		    // Modificar estilo y escoger la fuente correspondiente
		    $this->$tag += ($enable ? 1 : -1);
		    $style = '';
		    foreach(array('B', 'I', 'U') as $s)
		    {
		        if($this->$s>0)
		            $style .= $s;
		    }
		    $this->SetFont('',$style);
		}

		function PutLink($URL, $txt)
		{
		    // Escribir un hiper-enlace
		    $this->SetTextColor(0,0,255);
		    $this->SetStyle('U',true);
		    $this->Write(5,$txt,$URL);
		    $this->SetStyle('U',false);
		    $this->SetTextColor(0);
		}
	}

	$pdf = new PDF("P","mm", "Letter");
	$pdf->SetAutoPageBreak(TRUE, 10);

	$pdf->SetTextColor(0,0,0);
	$pdf->SetLineWidth(0.5);
	$pdf->SetDrawColor(128, 128, 128);
	$pdf->SetFillColor(128,128,128);

	$fila = 1;
	$nit = $parametros["NIT"];
	$año_contrato = $parametros["ano"];
	$nombre_contrato = $parametros["Operador"];
	$numero_contrato = $parametros["NumContrato"];

	while($focalizado = $respuesta_focalizacion->fetch_assoc())
	{
		$nombre_grupo = $focalizado["nom_grupo"];
		$abreviatura_documento = $focalizado["Abreviatura"];
		$numero_documento = $focalizado["num_doc"];
		$nombre_municipio = strtoupper($focalizado["Ciudad"]);
		$nombre_departamento = strtoupper($focalizado["Departamento"]);
		$nombre_sede = strtoupper($focalizado["nom_sede"]);
		$nombre_institucion = strtoupper($focalizado["nom_inst"]);
		$nombre_estudiante = strtoupper($focalizado["nom1"] . (($focalizado["nom2"] != "null") ? " ". $focalizado["nom2"] ." " : " ") . $focalizado["ape1"] . (($focalizado["ape2"] != "null") ? " ". $focalizado["ape2"] ."" : ""));

		if (($fila % 2) == 0)
		{
			$ordenada1 = $pdf->GetY();
		}
		else
		{
			$pdf->AddPage();
			$ordenada1 = 9;
		}

		// exit(var_dump($parametros["LogoETC"]));
		$pdf->Image($parametros["LogoETC"], 10, $ordenada1, 80);
		$pdf->Image($parametros["LogoOperador"], 85, $ordenada1-4, 30);
		$pdf->Image("../../img/logo_infopae.png", 118, $ordenada1-2, 30);
		// var_dump($rootUrl ."/img/logo_infopae.png");


		$pdf->SetLineWidth(0.2);
		$pdf->Cell(148, 10, "");
		$pdf->SetFont("Arial", "B", 8);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Cell(15, 10, utf8_decode("ID Lector"), 1, 0, "C", TRUE);
		$pdf->Cell(9, 10, "", 1);
		$pdf->Cell(15, 10, utf8_decode("ID Est."), 1, 0, "C", TRUE);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell(9, 10, "", 1, 1);
		$pdf->Ln(15);

		$pdf->SetFont("Arial", "B", 14);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Cell(0, 10, utf8_decode("Autorización de tratamiento de datos personales"), 1, 1, "C", TRUE);
		$pdf->Ln(15);

		$pdf->SetFont("Arial", "", 12);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->WriteHTML(utf8_decode("En cumplimiento de la Ley 1581 de 2012 y actuando como representante legal del menor <b>". $nombre_estudiante ."</b>, identificado con <b>". $abreviatura_documento ." # ". $numero_documento ."</b>, quién se encuentra focalizado como beneficiario del <b>Programa de Alimentación Escolar PAE</b> de la sede educativa <b>". $nombre_sede ."</b> del grupo <b>". $nombre_grupo ."</b> perteneciente a <b>". $nombre_institucion . "</b> del municipio de ". $nombre_municipio ." (". $nombre_departamento .") para la vigencia ". $año_contrato ." <b>AUTORIZO</b> expresamente la toma de la huella dactilar y el tratamiento de los datos sensibles. Así mismo, he  sido informado que la finalidad del uso de los datos será exclusivamente para la validación de identidad y el control diario en la entrega del complemento alimentario, cuyo responsable del tratamiento de datos será el operados del contrato PAE #". $numero_contrato ." ". $nombre_contrato ." identificado con NIT ". $nit .". "));
		$pdf->Ln(10);

		$ordenada = $pdf->GetY();
		$pdf->SetFont("Arial", "B", 12);
		$pdf->MultiCell(55, 5, utf8_decode("Firma de Autorización del Representante Legal C.C."), 0, "L");
		$pdf->SetXY(65, $ordenada);
		$pdf->Cell(80, 10, "", "B");
		$pdf->SetFont("Arial", "B", 12);
		$pdf->Cell(15, 10, "Fecha", 0, 0, "C");
			$pdf->SetTextColor(238, 238, 238);
		$pdf->Cell(0, 10, "dd  /  mm  /  aaaa", "B", 1, "C");
		$pdf->Ln(10);

		if (($fila % 2) != 0)
		{
			$pdf->SetTextColor(225, 225, 225);
			$pdf->Cell(0, 5, "_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _", 0, 1);
			$pdf->Ln(10);
		}

		$fila++;
	}

	$pdf->Output("I", "Autorización_tratamiento_datos_personales");
}
else
{
	echo "nada";
}
