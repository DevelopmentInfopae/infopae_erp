<?php
include '../../config.php';
require_once '../../autentication.php';
require_once '../../db/conexion.php';
require('rotation.php');
set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set('America/Bogota');

$totales = null;
$tamannoFuente = 7;
$mes = $_POST['mes'];
$anno = $_SESSION['p_ano'];
$tipoComplemento = $_POST['tipo'];
$operador = $_SESSION['p_Operador'];
$institucion = (isset($_POST['institucion']) && $_POST['institucion'] != '') ? $_POST['institucion'] : "";
$municipioNm = $_POST['municipioNm'];
$tipoPlanilla = $_POST['tipoPlanilla'];
$periodoActual = $_SESSION['periodoActual'];
$departamento = $_SESSION['p_Departamento'];
$sedeParametro = (isset($_POST['sede']) && $_POST['sede'] != '') ? $_POST['sede'] : "";
$municipio = $_POST['municipio'];
$hojaNovedades = $_POST['hojaNovedades'];
$formatoPlanilla = $_POST['formatoPlanilla'];

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
foreach ($dias as $clave => $dia)
{
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
$auxDias = trim($dia_consulta, ", ");
$auxDias = str_replace(" ", "", $auxDias);
$auxDias = str_replace("D", "", $auxDias);
$auxDias = array_map('intval', explode(',', $auxDias));
//var_dump($auxDias);

$consultaPriorizacion = '';
$consultaSemanas = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE MES = '$mes' ";
$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las semanas involucradas ' . mysqli_error($Link));
if ($respuestaSemanas->num_rows > 0) {
	while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
		$planillaSemanas[$dataSemanas['semana']] = $dataSemanas['semana'];
		$tablaPriorizacion = "priorizacion".$dataSemanas['semana'];
		$consulta = " show tables like '$tablaPriorizacion' "; 
		$result = $Link->query($consulta) or die ('Error al consultar existencia de tablas de priorizacion: '. mysqli_error($Link));
		$existe = $result->num_rows;
		if ($existe == 1) {
			$consultaPriorizacion .= " SELECT s.codigo_DANE, p.cod_sede, s.cod_inst, s.nom_inst, s.nom_sede, s.cod_mun_sede, p.num_est_focalizados
									FROM sedes$periodoActual s
									INNER JOIN $tablaPriorizacion AS p ON s.cod_sede = p.cod_sede
									INNER JOIN ubicacion AS u ON s.cod_mun_sede = u.codigoDANE
									WHERE u.codigoDANE = '$municipio'  ";
			if ($institucion != '') { $consultaPriorizacion .= " and s.cod_inst = '$institucion' "; }									
			if ($sedeParametro != '') { $consultaPriorizacion .= " and p.cod_sede = '$sedeParametro' "; } 
			$consultaPriorizacion .= ' UNION ALL ';		
		}					
	}
}
$consultaPriorizacion = trim($consultaPriorizacion, "UNION ALL ");

$resultado_sedes = $Link->query($consultaPriorizacion) or die ('Unable to execute query. '. mysqli_error($Link)); 
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
		if ($this->tipoPlanilla == 9) {
			$tamannoFuente = 10;
			$tituloPlanilla = "FORMATO RESUMEN DEL  REGISTRO DE ATENCIÓN MENSUAL -DILIGENCIADO POR EL OPERADOR";
			$logo = "../../upload/logotipos/logo8.jpg";
			$this->Image($logo, 250 ,3, 80, 18,'jpg', '');
			$this->Ln(20);
			$this->SetFont('Arial','B',$tamannoFuente);
			$this->SetTextColor(250,250,250);
			$this->SetFillColor(0,0,0);
			$this->Cell(0,8,utf8_decode(strtoupper($tituloPlanilla)),1,1,'C',True);
			// $this->Ln();
		}
		if($this->tipoPlanilla == 1 || $this->tipoPlanilla == 2 || $this->tipoPlanilla == 3 || $this->tipoPlanilla == 4 || $this->tipoPlanilla == 5 || $this->tipoPlanilla == 6 || $this->tipoPlanilla == 7 || $this->tipoPlanilla == 8 ){
			$tamannoFuente = 7;
			$logoInfopae = $_SESSION['p_Logo ETC'];
			if ($this->tipoPlanilla == 5 || $this->tipoPlanilla == 7 || $this->tipoPlanilla == 8)
			{
				$tituloPlanilla = "Registro de novedades y/o suplentes del programa de alimentaciÓn escolar - pae";
			}
			else if ($this->tipoPlanilla == 6)
			{
				$tituloPlanilla = "Registro de novedades - suplentes del programa de alimentaciÓn escolar - pae";
			}
			else
			{
				$tituloPlanilla = "REGISTRO Y CONTROL DIARIO DE ASISTENCIA DE TITULAR DE DERECHO DEL PROGRAMA DE ALIMENTACIÓN ESCOLAR - PAE";
			}
			
			// piedras
			// $logooperador = "../../upload/logotipos/logo_operador_planilla.jpg";
			// $this->Image($logooperador, 5 ,3, 48, 14,'jpg', '');
	
			// $logoMinEducacion = "../../upload/logotipos/logo_min_educacion.jpg";
			// $this->Image($logoMinEducacion, 100 ,3, 73, 14,'jpg', '');
	
			// $logoEtcMunicipio = "../../upload/logotipos/logo_etc_municipio.jpg";
			// $this->Image($logoEtcMunicipio, 210 ,3, 33, 14,'jpg', '');
	
			// $logoEtcMunicipio2 = "../../upload/logotipos/logo_etc_municipio2.jpg";
			// $this->Image($logoEtcMunicipio2, 280 ,3, 33, 14,'jpg', '');

			// ibague
			$logoMinEducacion = "../../upload/logotipos/logo_min_educacion.jpg";
			$this->Image($logoMinEducacion, 5 ,3, 73, 14,'jpg', '');

			$logooperador = "../../upload/logotipos/logo_etc_uapa.jpg";
			$this->Image($logooperador, 100 ,3, 60, 14,'jpg', '');

			$logooperador = "../../upload/logotipos/logo_operador_planilla.jpg";
			$this->Image($logooperador, 180 ,3, 60, 14,'jpg', '');

			$logoEtcMunicipio = "../../upload/logotipos/logo_etc_municipio.jpg";
			$this->Image($logoEtcMunicipio, 270 ,3, 70, 14,'jpg', '');
	
			// $logoEtcMunicipio2 = "../../upload/logotipos/logo_etc_municipio2.jpg";
			// $this->Image($logoEtcMunicipio2, 280 ,3, 33, 14,'jpg', '');
	
			$this->Ln(16);
			$this->SetFont('Arial','B',$tamannoFuente);
			$this->SetTextColor(250,250,250);
			$this->SetFillColor(0,0,0);
			$this->Cell(0,5,utf8_decode(strtoupper($tituloPlanilla)),0,0,'C',True);
			$this->Ln();
		}


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
$pdf= new PDF('L','mm',array(356,216));
$pdf->set_data($tipoPlanilla);
$pdf->SetMargins(3, 4, 3, 3);
$pdf->SetAutoPageBreak(false,5);
$pdf->AliasNbPages();
include '../../php/funciones.php';


$lineas = 25;
$alturaLinea = 4;


if($tipoPlanilla == 2 || $tipoPlanilla == 3 || $tipoPlanilla == 4) {
	// modificaciones planillas para mostrar en orden con complementos
	$complementos = [];
	if ($tipoComplemento == "") {
		$consultaComplementos  = "SELECT DISTINCT tipo_complem AS tipo_complem FROM entregas_res_".$mes.$_SESSION['periodoActual']. " WHERE 1 = 1 ";
		if ($municipio !== '') {
			$consultaComplementos .= " AND cod_mun_res = '$municipio' ";
		}
		if ($institucion !== '') {
			$consultaComplementos .= " AND cod_inst = '$institucion' ";
		}
		if ($sedeParametro !== '') {
			$consultaComplementos .= " AND cod_sede = '$sedeParametro' ";
		}
		$respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complementos 203' .mysqli_error($Link));
		if ($respuestaComplementos->num_rows > 0) {
			while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
				$complementos[] = $dataComplementos['tipo_complem'];
			}
		}
	}else{
		$complementos[] = $tipoComplemento;
	}
	// exit(var_dump($complementos));
	$condicionCoordinador = '';
  	if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != '') {
  		$codigoSedes = "";
  		$documentoCoordinador = $_SESSION['num_doc'];
  		$consultaCodigoSedes = "SELECT cod_sede FROM sedes$periodoActual WHERE id_coordinador = $documentoCoordinador;";
		$respuestaCodigoSedes = $Link->query($consultaCodigoSedes) or die('Error al consultar el código de la sede ' . mysqli_error($Link));
		if ($respuestaCodigoSedes->num_rows > 0) {
			$codigoInstitucion = '';
			while ($dataCodigoSedes = $respuestaCodigoSedes->fetch_assoc()) {
				$codigoSedeRow = $dataCodigoSedes['cod_sede'];
				$consultaCodigoInstitucion = "SELECT cod_inst FROM sedes$periodoActual WHERE cod_sede = $codigoSedeRow;";
				$respuestaCodigoInstitucion = $Link->query($consultaCodigoInstitucion) or die ('Error al consultar el código de la institución ' . mysqli_error($Link));
				if ($respuestaCodigoInstitucion->num_rows > 0) {
					$dataCodigoInstitucion = $respuestaCodigoInstitucion->fetch_assoc();
					$codigoInstitucionRow = $dataCodigoInstitucion['cod_inst'];
					if ($codigoInstitucionRow == $codigoInstitucion || $codigoInstitucion == '') {
						$codigoSedes .= "'$codigoSedeRow'".",";
						$codigoInstitucion = $codigoInstitucionRow; 
					}
				}
			}
		}
		$codigoSedes = substr($codigoSedes, 0 , -1);
		$condicionCoordinador = " AND cod_sede IN ($codigoSedes) ";
  	}

	
  	foreach ($complementos as $key => $value) {
  		$tipoComplemento = $value;
  		$documentosOriginal =[];

  		/***********************************************************************************************************************************************/
  		if($tipoPlanilla == 2){
  			$documentos = "'"."',";
  			$estudiantes = [];
  			$estudiantesAuxiliar = [];
  			$consultaPlanillaSemanas2 = " SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE SEMANA BETWEEN '$semanaInicial' AND '$semanaFinal' ";
			$respuestaPlanillaSemanas2 = $Link->query($consultaPlanillaSemanas2) or die ('Error al consultar las semanas ' .mysqli_error($Link));
			if ($respuestaPlanillaSemanas2->num_rows > 0) {
				while($dataPlanillaSemanas2 = $respuestaPlanillaSemanas2->fetch_assoc()){
					$semanasFoca[$dataPlanillaSemanas2['semana']] = $dataPlanillaSemanas2['semana'];
				}
			}

			// vamos a tomar como array inicial la focalizacion semanaInicial
			$consulta = "SELECT focalizacion$semanaInicial.id, 
								focalizacion$semanaInicial.tipo_doc, 
								focalizacion$semanaInicial.num_doc, 
								tp.Abreviatura AS tipo_doc_nom, 
								focalizacion$semanaInicial.nom1, 
								focalizacion$semanaInicial.nom2, 
								focalizacion$semanaInicial.ape1, 
								focalizacion$semanaInicial.ape2,  
								focalizacion$semanaInicial.genero,
								date_format(focalizacion$semanaInicial.fecha_nac, '%d-%m-%Y') as fecha, 
								focalizacion$semanaInicial.dir_res, 
								focalizacion$semanaInicial.cod_mun_res, 
								focalizacion$semanaInicial.telefono, 
								focalizacion$semanaInicial.cod_mun_nac, 
								focalizacion$semanaInicial.fecha_nac, 
								focalizacion$semanaInicial.cod_estrato, 
								focalizacion$semanaInicial.sisben, 
								focalizacion$semanaInicial.cod_discap, 
								UPPER(et.DESCRIPCION) AS etnia,  
								focalizacion$semanaInicial.resguardo, 
								focalizacion$semanaInicial.cod_pob_victima, 
								focalizacion$semanaInicial.des_dept_nom, 
								focalizacion$semanaInicial.nom_mun_desp, 
								focalizacion$semanaInicial.cod_inst, 
								focalizacion$semanaInicial.cod_sede, 
								focalizacion$semanaInicial.cod_grado, 
								focalizacion$semanaInicial.nom_grupo, 
								focalizacion$semanaInicial.cod_jorn_est, 
								focalizacion$semanaInicial.estado_est, 
								focalizacion$semanaInicial.repitente,
								focalizacion$semanaInicial.edad, 
								focalizacion$semanaInicial.zona_res_est, 
								focalizacion$semanaInicial.activo, 
								focalizacion$semanaInicial.tipo_complemento
							FROM focalizacion$semanaInicial
							INNER JOIN etnia AS et ON et.ID = focalizacion$semanaInicial.etnia
							INNER JOIN tipodocumento AS tp ON tp.id = focalizacion$semanaInicial.tipo_doc
							INNER JOIN sedes$periodoActual s ON (s.cod_sede = focalizacion$semanaInicial.cod_sede AND s.cod_inst = focalizacion$semanaInicial.cod_inst )
							WHERE focalizacion$semanaInicial.cod_mun_res = '$municipio' AND focalizacion$semanaInicial.tipo_complemento = '$tipoComplemento'  ";
			if ($institucion != '') { $consulta .= " and focalizacion$semanaInicial.cod_inst = '$institucion'"; }			
			if ($sedeParametro != ''){ $consulta .= " and focalizacion$semanaInicial.cod_sede = '$sedeParametro'"; }
			if ($condicionCoordinador != ''){ $consulta .= " $condicionCoordinador "; }
			$consulta .= " ORDER BY s.nom_inst, s.nom_sede, cod_grado, nom_grupo, ape1, ape2, nom1, nom2 asc "; 
			// echo "$consulta<br>";
			$resultado = $Link->query($consulta) or die ('Unable to execute query. planilla en blanco <br>'.$consulta.'<br>'.mysqli_error($Link)); 
			$codigo = ''; 
			if($resultado->num_rows >= 1){
				while($row = $resultado->fetch_assoc()){
					if($codigo != $row['cod_sede']){
						$codigo = $row['cod_sede'];
					}
					$estudiantes[$codigo][] = $row; 
				}
				foreach ($estudiantes as $codigo => $value2) {
					foreach ($value2 as $key3 => $value3) {
						$documentosOriginal[] = $value3['num_doc'];
					}
				}
				foreach ($documentosOriginal as $key => $value) {
					$documentos .= "'" . $value . "',";
				}		
			}
			$documentos = trim($documentos, ",");
			// echo "$documentos <br>";
			foreach ($semanasFoca as $key => $value) {
				$tablaFocalizacion = "focalizacion".$value;
				$consulta = " show tables like '$tablaFocalizacion' "; 
				$result = $Link->query($consulta) or die ('Error al consultar existencia de tablas de focalizacion: '. mysqli_error($Link));
				$existe = $result->num_rows;
				if ($existe == 1) {
					if ($value !== $semanaInicial) {
						$consulta = "SELECT focalizacion$value.id, 
											focalizacion$value.tipo_doc, 
											focalizacion$value.num_doc, 
											tp.Abreviatura AS tipo_doc_nom, 
											focalizacion$value.nom1, 
											focalizacion$value.nom2, 
											focalizacion$value.ape1, 
											focalizacion$value.ape2,  
											focalizacion$value.genero, 
											date_format(focalizacion$value.fecha_nac, '%d-%m-%Y') as fecha,
											focalizacion$value.dir_res, 
											focalizacion$value.cod_mun_res, 
											focalizacion$value.telefono, 
											focalizacion$value.cod_mun_nac, 
											focalizacion$value.fecha_nac, 
											focalizacion$value.cod_estrato, 
											focalizacion$value.sisben, 
											focalizacion$value.cod_discap, 
											UPPER(et.DESCRIPCION)  AS etnia,  
											focalizacion$value.resguardo, 
											focalizacion$value.cod_pob_victima, 
											focalizacion$value.des_dept_nom, 
											focalizacion$value.nom_mun_desp, 
											focalizacion$value.cod_inst, 
											focalizacion$value.cod_sede, 
											focalizacion$value.cod_grado, 
											focalizacion$value.nom_grupo, 
											focalizacion$value.cod_jorn_est, 
											focalizacion$value.estado_est, 
											focalizacion$value.repitente,
											focalizacion$value.edad, 
											focalizacion$value.zona_res_est, 
											focalizacion$value.activo, 
											focalizacion$value.tipo_complemento
										FROM focalizacion$value
										INNER JOIN etnia AS et ON et.ID = focalizacion$value.etnia
										INNER JOIN tipodocumento AS tp ON tp.id = focalizacion$value.tipo_doc
										INNER JOIN sedes$periodoActual s ON (s.cod_sede = focalizacion$value.cod_sede AND s.cod_inst = focalizacion$value.cod_inst )
										WHERE focalizacion$value.cod_mun_res = '$municipio' AND focalizacion$value.tipo_complemento = '$tipoComplemento' 
											AND focalizacion$value.num_doc NOT IN ($documentos)"; 
						if ($institucion != '') { $consulta .= " and focalizacion$value.cod_inst = '$institucion'"; }			
						if ($sedeParametro != ''){ $consulta .= " and focalizacion$value.cod_sede = '$sedeParametro'"; }
						if ($condicionCoordinador != ''){ $consulta .= " $condicionCoordinador "; }
						$consulta .= " ORDER BY s.nom_inst, s.nom_sede, cod_grado, nom_grupo, ape1, ape2, nom1, nom2 asc "; 
						// echo "$consulta<br>";
						$resultado = $Link->query($consulta) or die ('Unable to execute query. Tercera consulta: los niños<br>'.$consulta.'<br>'.mysqli_error($Link));
						$codigo = '';
						if($resultado->num_rows >= 1){
							$documentos .= ",";
							while($row = $resultado->fetch_assoc()){
								if($codigo != $row['cod_sede']){
									$codigo = $row['cod_sede'];
								}
								$estudiantes[$codigo][] = $row;
							}
							foreach ($estudiantes as $codigo => $value2) {
								foreach ($value2 as $key3 => $value3) {
									$documentosOriginal[] = $value3['num_doc'];
								}
							}
							// $documentos = '';
							foreach ($documentosOriginal as $key => $value) {
								$documentos .= "'" . $value . "',";
							}
							$documentos = trim($documentos, ",");


							// se ordena el nuevo array con los niños que se agregaron en el cambio de la focalizacion y se ordena nuevamente por el grupo
							foreach ($estudiantes as $estudianteCod_sede => $valorEstudiantes) {
								$gruposI = [];
								foreach ($valorEstudiantes as $keySedeCurso => $valorSedeCurso) {
									$gruposI[$keySedeCurso] = $valorSedeCurso['nom_grupo'];	
								}
								array_multisort($gruposI, SORT_ASC, $estudiantes[$estudianteCod_sede]);
							}
						}
					}	
				}
			}	
  		}
  		else {
  			$estudiantes = [];
  			$consulta = "SELECT e.id, 
							e.tipo_doc, 
							e.num_doc, 
							e.tipo_doc_nom, 
							e.nom1, 
							e.nom2, 
							e.ape1, 
							e.ape2,  
							e.genero, 
							date_format(e.fecha_nac, '%d-%m-%Y') as fecha,
							e.dir_res, 
							e.cod_mun_res, 
							e.telefono, 
							e.cod_mun_nac, 
							e.fecha_nac, 
							e.cod_estrato, 
							e.sisben, 
							e.cod_discap, 
							UPPER(et.DESCRIPCION)  AS etnia, 
							e.resguardo, 
							e.cod_pob_victima, 
							e.des_dept_nom, 
							e.nom_mun_desp, 
							e.cod_inst, 
							e.cod_sede, 
							e.cod_grado, 
							e.nom_grupo, 
							e.cod_jorn_est, 
							e.estado_est, 
							e.repitente,
							e.edad, 
							e.zona_res_est, 
							e.id_disp_est, 
							e.TipoValidacion, 
							e.activo, 
							e.tipo_complem, 
							". trim($dia_consulta, ", ") ."
						FROM entregas_res_$mes$anno2d AS e
						INNER JOIN etnia AS et ON et.ID = e.etnia
						WHERE cod_mun_res = '$municipio' AND tipo_complem='$tipoComplemento' AND tipo = 'F' ";
			if ($institucion != '') { $consulta .= " and cod_inst = '$institucion'"; }			
			if ($sedeParametro != ''){ $consulta .= " and cod_sede = '$sedeParametro'"; }
			if ($condicionCoordinador != ''){ $consulta .= " $condicionCoordinador "; }
			$consulta .= " ORDER BY e.nom_inst, e.nom_sede, cod_grado, nom_grupo, ape1,ape2,nom1,nom2 asc "; 
			// var_dump($consulta);
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
  		/************************************************************************************************************************************************/
		// exit(var_dump($estudiantes));
		foreach ($estudiantes as $estudiantesSede) {
			// Consulta que retorna la cantidad de estudiantes de una sede seleccionada.
			$codigoSede = $estudiantesSede[0]['cod_sede'];
			$auxIntitucion = $estudiantesSede[0]['cod_inst'];
			$consulta = "SELECT count(id) AS titulares, sum(". str_replace(",", "+", trim($dia_consulta, ", ")) .") AS entregas FROM entregas_res_$mes$anno2d WHERE cod_inst='$auxIntitucion' AND tipo_complem ='$tipoComplemento' AND cod_sede = '$codigoSede'"; 
			$resultado = $Link->query($consulta) or die ('Unable to execute query. <br>'.$consulta.'<br>'. mysqli_error($Link));
			if($resultado->num_rows > 0) {
				while($row = $resultado->fetch_assoc()) {
					$totales = $row;
				}
			}

			if ($formatoPlanilla == 1) {
				$paginas = 0;
				$tempPaginas = 0;
				foreach ($estudiantesSede as $estudiantesKey => $estudiantesValue) {
					$grupos[$estudiantesKey] = $estudiantesValue['nom_grupo'];	
				}
				$grupoTemp = $estudiantesSede[0]['nom_grupo'];
				foreach ($grupos as $keyGrupos => $valueGrupos) {
					if ($valueGrupos == $grupoTemp) {
						$tempPaginas++;
					}
					else{
						$paginas += ceil($tempPaginas / $lineas);
						$grupoTemp = $valueGrupos;
						$tempPaginas = 1;
						// echo "$paginas<br>";
					}
				}
				$paginas += ceil($tempPaginas / $lineas);
			}
			if ($formatoPlanilla == 0) {
				$paginas = ceil(count($estudiantesSede) / $lineas);
			}
			
			$pagina = 1;
			$linea = 1;
			$tipoPlanilla2 = $tipoPlanilla;
			$pdf->set_data($tipoPlanilla2);
			$pdf->AddPage();
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetDrawColor(0,0,0);

			include 'planillas_header_v4.php';

			$pdf->SetLineWidth(.05);
			//Inicia impresión de estudiantes de la sede
			$nEstudiante = 0;
			$pdf->SetFont('Arial','',$tamannoFuente);

			$racionesProgramadas = 0;
			$grupoEncurso = $estudiantesSede[0]['nom_grupo'];

			foreach ($estudiantesSede as $estudiante) {
				$nEstudiante++;

				if ($formatoPlanilla == 1){
					if($linea > $lineas || $grupoEncurso != $estudiante['nom_grupo']) {

						$Y = $pdf->GetY();
						$pdf->Cell(60,8, utf8_decode(strtoupper('FIRMA RESPONSABLE DEL OPERADOR:')),'LB',0,'L', False);
						$pdf->SetXY(63, $Y);
						$pdf->Cell(108,8, '','BR',0,'L',False);
						$pdf->Cell(60,8,'FIRMA RECTOR ESTABLECIMIENTO EDUCATIVO: ','B',0,'L', False);
						$pdf->Cell(0,8, '','BR',0,'L',False);

						$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
						$alturaCuadroFilas = $alturaLinea * ($linea-1);
						$pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
						include 'planillas_footer_v4.php';
						$tipoPlanilla2 = $tipoPlanilla;
						$pdf->set_data($tipoPlanilla2);
						$pdf->AddPage();
						$pagina++;
						include 'planillas_header_v4.php';
						$pdf->SetFont('Arial','',$tamannoFuente);
						$linea = 1;
						$grupoEncurso = $estudiante['nom_grupo'];
					}
				}
				if ($formatoPlanilla == 0) {
					if($linea > $lineas) {
                        $pdf->SetFont('Arial','B',$tamannoFuente);
						$Y = $pdf->GetY();
						$pdf->Cell(60,8, utf8_decode(strtoupper('FIRMA RESPONSABLE DEL OPERADOR:')),'LB',0,'L', False);
						$pdf->SetXY(63, $Y);
						$pdf->Cell(108,8, '','BR',0,'L',False);
						$pdf->Cell(60,8,'FIRMA RECTOR ESTABLECIMIENTO EDUCATIVO: ','B',0,'L', False);
						$pdf->Cell(0,8, '','BR',0,'L',False);

						$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
						$alturaCuadroFilas = $alturaLinea * ($linea-1);
						$pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
						include 'planillas_footer_v4.php';
						$tipoPlanilla2 = $tipoPlanilla;
						$pdf->set_data($tipoPlanilla2);
						$pdf->AddPage();
						$pagina++;
						include 'planillas_header_v4.php';
						$pdf->SetFont('Arial','',$tamannoFuente);
						$linea = 1;
					}
				}


				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->Cell(8,$alturaLinea,utf8_decode($nEstudiante),'R',0,'C',False);
				$pdf->Cell(10,$alturaLinea,utf8_decode($estudiante['tipo_doc_nom']),'R',0,'C',False);
				$pdf->Cell(22,$alturaLinea,utf8_decode($estudiante['num_doc']),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea,utf8_decode($estudiante['nom1']),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea,utf8_decode($estudiante['nom2']),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea,utf8_decode($estudiante['ape1']),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea,utf8_decode($estudiante['ape2']),'R',0,'L',False);
				$pdf->Cell(22,$alturaLinea,utf8_decode($estudiante["fecha"]),'R',0,'C',False);
				$etniaEncurso = substr($estudiante['etnia'],0,11);
				$pdf->Cell(16,$alturaLinea,utf8_decode($etniaEncurso), 'R', 0, 'C', False);
				$pdf->Cell(5,$alturaLinea,utf8_decode($estudiante['genero']),'R',0,'C',False);
				$pdf->Cell(5,$alturaLinea,utf8_decode($estudiante['cod_grado']),'R',0,'C',False);
				$pdf->Cell(13,$alturaLinea,utf8_decode($tipoComplemento),'R',0,'C',False);
				$dia = $primer_dia;

				// Aqui es donde se cambia de acuerdo a la plantilla
				$entregasEstudiante = 0;
				for($j = 0 ; $j < 22 ; $j++) {
					if($tipoPlanilla != 2) {
						if($tipoPlanilla == 3) { $pdf->SetTextColor(120,120,120); }
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
            $pdf->SetFont('Arial','B',$tamannoFuente);
			$Y = $pdf->GetY();
			$pdf->Cell(60,8, utf8_decode(strtoupper('FIRMA RESPONSABLE DEL OPERADOR:')),'LB',0,'L', False);
			$pdf->SetXY(63, $Y);
			$pdf->Cell(108,8, '','BR',0,'L',False);
			$pdf->Cell(60,8,'FIRMA RECTOR ESTABLECIMIENTO EDUCATIVO: ','B',0,'L', False);
			$pdf->Cell(0,8, '','BR',0,'L',False);

			//Termina impresión de estudiantes de la sede
			$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
			$alturaCuadroFilas = $alturaLinea * ($linea-1);
			$pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
			include 'planillas_footer_v4.php';

			// manejo planilla novedades al final de cada sede 
			// exit(var_dump($hojaNovedades));
			for ($m=0; $m < (int)$hojaNovedades ; $m++) { 
				$tipoPlanillaN = 5;
				$pdf->set_data($tipoPlanillaN);
				$linea = 1;
				$lineas = 25;
				$paginas = (int)$hojaNovedades;
				$pagina = $m+1;
				$pdf->AddPage();
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFillColor(255,255,255);
				$pdf->SetDrawColor(0,0,0);

				include 'planillas_header_v4.php';
				$pdf->SetLineWidth(.05);
				for($i = 0 ; $i < 25 ; $i++){
					if($linea > $lineas){
						$Y = $pdf->GetY();
						$pdf->Cell(60,8, utf8_decode(strtoupper('FIRMA RESPONSABLE DEL OPERADOR:')),'LB',0,'L', False);
						$pdf->SetXY(63, $Y);
						$pdf->Cell(108,8, '','BR',0,'L',False);
						$pdf->Cell(60,8,'FIRMA RECTOR ESTABLECIMIENTO EDUCATIVO: ','B',0,'L', False);
						$pdf->Cell(0,8, '','BR',0,'L',False);

						$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
						$alturaCuadroFilas = $alturaLinea * ($linea-1);
						$pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
						include 'planillas_footer_v4.php';
						$pdf->AddPage();
						include 'planillas_header_v4.php';
						$pdf->SetFont('Arial','',$tamannoFuente);
						$linea = 1;
					}

					$x = $pdf->GetX();
					$y = $pdf->GetY();
					$pdf->Cell(8,$alturaLinea,"",'R',0,'C',False);
					$pdf->Cell(10,$alturaLinea,"",'R',0,'C',False);
					$pdf->Cell(22,$alturaLinea,"",'R',0,'L',False);
					$pdf->Cell(26.5,$alturaLinea,"",'R',0,'L',False);
					$pdf->Cell(26.5,$alturaLinea,"",'R',0,'L',False);
					$pdf->Cell(26.5,$alturaLinea,"",'R',0,'L',False);
					$pdf->Cell(26.5,$alturaLinea,"",'R',0,'L',False);
					$pdf->Cell(22,$alturaLinea,"",'R',0,'C',False);
					$pdf->Cell(16,$alturaLinea,"",'R',0,'C',False);
					$pdf->Cell(5,$alturaLinea,"",'R',0,'C',False);
					$pdf->Cell(5,$alturaLinea,"",'R',0,'C',False);
					$pdf->Cell(13,$alturaLinea,"",'R',0,'C',False);

					// Aqui es donde se cambia de acuerdo a la plantilla
					for($j = 0 ; $j < 22 ; $j++) {
						$pdf->Cell(6,$alturaLinea,utf8_decode(''),'R',0,'C',False);
					}
					// Termina donde se cambia de acuerdo a la plantilla
					$pdf->SetXY($x, $y);
					$pdf->Cell(0,$alturaLinea,'','B',1);
					$linea++;
				}

				$Y = $pdf->GetY();
				$pdf->Cell(60,8, utf8_decode(strtoupper('FIRMA RESPONSABLE DEL OPERADOR:')),'LB',0,'L', False);
				$pdf->SetXY(63, $Y);
				$pdf->Cell(108,8, '','BR',0,'L',False);
				$pdf->Cell(60,8,'FIRMA RECTOR ESTABLECIMIENTO EDUCATIVO: ','B',0,'L', False);
				$pdf->Cell(0,8, '','BR',0,'L',False);

				$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
				$alturaCuadroFilas = $alturaLinea * ($linea-1);
				$pdf->Cell(0,$alturaCuadroFilas,"",1,0,'R',False);
				include 'planillas_footer_v4.php';
			}
		}
  	}
}

else if ($tipoPlanilla == 5){ 
	foreach ($sedes as $sede){
			$linea = 1;
			$lineas = 25;
			$codigoSede = $sede['cod_sede'];
			$pdf->AddPage();
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetDrawColor(0,0,0);

			include 'planillas_header_v4.php';
			$pdf->SetLineWidth(.05);

			for($i = 0 ; $i < 25 ; $i++)
			{
				if($linea > $lineas)
				{
					$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
					$alturaCuadroFilas = $alturaLinea * ($linea-1);
					$pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
					include 'planillas_footer_v4.php';
					$pdf->AddPage();
					include 'planillas_header_v4.php';
					$pdf->SetFont('Arial','',$tamannoFuente);
					$linea = 1;
				}

				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->Cell(8,$alturaLinea,"",'R',0,'C',False);
				$pdf->Cell(10,$alturaLinea,"",'R',0,'C',False);
				$pdf->Cell(22,$alturaLinea,"",'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea,"",'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea,"",'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea,"",'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea,"",'R',0,'L',False);
				$pdf->Cell(22,$alturaLinea,"",'R',0,'C',False);
				$pdf->Cell(16,$alturaLinea,"",'R',0,'C',False);
				$pdf->Cell(5,$alturaLinea,"",'R',0,'C',False);
				$pdf->Cell(5,$alturaLinea,"",'R',0,'C',False);
				$pdf->Cell(13,$alturaLinea,"",'R',0,'C',False);
				// $pdf->Cell(13,$alturaLinea,"",'R',0,'C',False);

				// Aqui es donde se cambia de acuerdo a la plantilla
				for($j = 0 ; $j < 22 ; $j++)
				{
					$pdf->Cell(6,$alturaLinea,utf8_decode(''),'R',0,'C',False);
				}
				// Termina donde se cambia de acuerdo a la plantilla
				$pdf->SetXY($x, $y);
				$pdf->Cell(0,$alturaLinea,'','B',1);
				$linea++;
			}
			$Y = $pdf->GetY();
			$pdf->Cell(60,8, utf8_decode(strtoupper('FIRMA RESPONSABLE DEL OPERADOR:')),'LB',0,'L', False);
			$pdf->SetXY(63, $Y);
			$pdf->Cell(108,8, '','BR',0,'L',False);
			$pdf->Cell(60,8,'FIRMA RECTOR ESTABLECIMIENTO EDUCATIVO: ','B',0,'L', False);
			$pdf->Cell(0,8, '','BR',0,'L',False);

			$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
			$alturaCuadroFilas = $alturaLinea * ($linea-1);
			$pdf->Cell(0,$alturaCuadroFilas,"",1,0,'R',False);

			include 'planillas_footer_v4.php';
	}
}
else if ($tipoPlanilla == 6)
{
	// Consultar las semana seleccionadas
	$consulta_semana = "SELECT DISTINCT(SEMANA) AS semana FROM planilla_semanas WHERE MES = '$mes' AND DIA <= '$diaFinalSemanaFinal';";
	$respuesta_semana = $Link->query($consulta_semana) or die("Error al consulta planilla_semanas: ". $Link->error);
	if ($respuesta_semana->num_rows == 0)
	{
		echo "<script>alert('No existen registros con los filtros seleccionados.'); window.close(); </script>";
		exit();
	}

	$consulta_suplentes = '';
	$parametro_sede_suplente = '';
	if($sedeParametro != '') { $parametro_sede_suplente = " AND cod_sede = '$sedeParametro' "; }
	if($institucion != '') { $parametro_institucion_suplente = " AND cod_inst = '$institucion' "; } else {$parametro_institucion_suplente = " ";}

	while ($semana_suplente = $respuesta_semana->fetch_assoc())
	{
		$numero_semana = $semana_suplente['semana'];
		$consulta_suplentes .= "(SELECT
													id, tipo_doc, num_doc, tipo_doc_nom, nom1, nom2, ape1, ape2, dir_res, cod_mun_res, telefono, cod_mun_nac, fecha_nac, cod_estrato, sisben, cod_discap, etnia, resguardo, cod_pob_victima, des_dept_nom, nom_mun_desp, cod_inst, cod_sede, cod_grado, nom_grupo, cod_jorn_est, estado_est, repitente,edad, zona_res_est, id_disp_est, TipoValidacion, activo
												FROM suplentes$numero_semana
												WHERE cod_mun_res = $municipio $parametro_institucion_suplente $parametro_sede_suplente) UNION ALL ";
	}
	$consulta_suplentes_general = "SELECT * FROM (". trim($consulta_suplentes, "UNION ALL ") .") AS TG GROUP BY num_doc, cod_sede ORDER BY cod_sede, cod_grado, nom_grupo, ape1, ape2, nom1, nom2";

	$respuesta_suplentes = $Link->query(trim($consulta_suplentes_general, 'UNION ALL ')) or die ('No existen suplentes en los rangos seleccionados ');

	$codigo = '';
	if($respuesta_suplentes->num_rows > 0)
	{
		while($suplentes = $respuesta_suplentes->fetch_assoc())
		{
			if($codigo != $suplentes['cod_sede']) { $codigo = $suplentes['cod_sede']; }
			$estudiantes[$codigo][] = $suplentes;
		}
	}
	else
	{
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

		include 'planillas_header_v4.php';

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
				$alturaCuadroFilas = $alturaLinea * ($linea-1);
				$pdf->Cell(0,$alturaCuadroFilas, '',1,0,'R',False);
				include 'planillas_footer_v4.php';
				$pdf->AddPage();
				$pagina++;
				include 'planillas_header_v4.php';
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
			for($j = 0 ; $j < 22 ; $j++) {
				$pdf->Cell(6,$alturaLinea,utf8_decode(' '),'R',0,'C',False);
			}
			$pdf->SetTextColor(0,0,0);
			$pdf->SetXY($x, $y);
			$pdf->Cell(0,$alturaLinea,'','B',1);
			$linea++;
		}

		//Termina impresión de estudiantes de la sede
		$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
		$alturaCuadroFilas = $alturaLinea * ($linea-1);
		$pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);

		include 'planillas_footer_v4.php';
	}
}
else if ($tipoPlanilla == 7){
	foreach ($sedes as $sede){
		// Consulta que retorna la cantidad de estudiantes de una sede seleccionada.
		$codigoSede = $sede['cod_sede'];
		$codigoInstitucion = $sede['cod_inst'];
		// var_dump($codigoSede);
		$consulta = "SELECT count(id) AS titulares, sum(". str_replace(",", "+", trim($dia_consulta, ", ")) .") AS entregas FROM entregas_res_$mes$anno2d WHERE cod_inst='$codigoInstitucion' AND tipo_complem ='$tipoComplemento' AND cod_sede = '$codigoSede'";
		// exit(var_dump($consulta));
		$resultado = $Link->query($consulta) or die ('Unable to execute query. <br>'.$consulta.'<br>'. mysqli_error($Link));
		if($resultado->num_rows > 0) {
			while($row = $resultado->fetch_assoc()) {
				$totales = $row;
			}
		}
 		$consulta_suplente_repitentes_sede = "	SELECT 	ent.*, 
														UPPER(e.DESCRIPCION)  AS etnia
												FROM entregas_res_$mes$anno2d ent
												INNER JOIN etnia e ON e.id = ent.etnia
												WHERE cod_inst = '$codigoInstitucion' 
												AND cod_sede = '$codigoSede' AND (tipo = 'S' OR tipo = 'R') 
												AND tipo_complem='$tipoComplemento' 
												ORDER BY cod_sede, cod_grado, nom_grupo, ape1,ape2,nom1,nom2 ASC";
		// echo "<br><br>$consulta_suplente_repitentes_sede<br><br>";
		// exit(var_dump($consulta_suplente_repitentes_sede));
		$respuesta_suplente_repitentes_sede = $Link->query($consulta_suplente_repitentes_sede) or die("Error al consultar suplentes y repitentes en entregas_res_$mes$anno2d: ". $Link->error);
		if ($respuesta_suplente_repitentes_sede->num_rows > 0)
		{	
			$linea = 1;
			$lineas = 25;
			$pagina = 1;
			$paginas = ceil($respuesta_suplente_repitentes_sede->num_rows / $lineas);
			$numero_estudiantes = 0;
			$codigoSede = $sede['cod_sede'];
			$pdf->AddPage();
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetDrawColor(0,0,0);

			include 'planillas_header_v4.php';
			$pdf->SetLineWidth(.05);

			while($suplente_repitente_sede = $respuesta_suplente_repitentes_sede->fetch_assoc())
			{	
				$numero_estudiantes++;
				$suplente_repitente_sede = (object) $suplente_repitente_sede;

				if($linea > $lineas)
				{
					$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
					$pdf->Cell(0, $alturaLinea, '', 'T');
					include 'planillas_footer_v4.php';
					$pdf->AddPage();
					$pagina++;
					include 'planillas_header_v4.php';
					$pdf->SetFont('Arial','',$tamannoFuente);
					$linea = 1;
				}

				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->SetFont('','',$tamannoFuente);
				$pdf->Cell(8,$alturaLinea, $numero_estudiantes,'LR',0,'C',False);
				$pdf->Cell(10,$alturaLinea, $suplente_repitente_sede->tipo_doc_nom,'R',0,'C',False);
				$pdf->Cell(22,$alturaLinea, $suplente_repitente_sede->num_doc,'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea, utf8_decode(mb_strtoupper($suplente_repitente_sede->nom1)),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea, utf8_decode(mb_strtoupper($suplente_repitente_sede->nom2)),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea, utf8_decode(mb_strtoupper($suplente_repitente_sede->ape1)),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea, utf8_decode(mb_strtoupper($suplente_repitente_sede->ape2)),'R',0,'L',False);
				$pdf->Cell(22,$alturaLinea, $suplente_repitente_sede->fecha_nac,'R',0,'C',False);
				$pdf->Cell(16,$alturaLinea, $suplente_repitente_sede->etnia,'R',0,'C',False);
				$pdf->Cell(5,$alturaLinea, $suplente_repitente_sede->genero,'R',0,'C',False);
				$pdf->Cell(5,$alturaLinea, $suplente_repitente_sede->cod_grado,'R',0,'C',False);
				$pdf->Cell(13,$alturaLinea, utf8_decode(mb_strtoupper($tipoComplemento)),'R',0,'C',False);

				// Impresión de las 24 columnas para los días.
				$total_entregas_por_estudiante = 0;
				$auxOtrosDiasMes = 0;
				for($j = 0 ; $j < 22 ; $j++){
					$auxIndice = $j+1;
					if (in_array($auxIndice, $auxDias)) {
						$dia_entrega_estudiante = $suplente_repitente_sede->{'D'.$auxIndice};
						$total_entregas_por_estudiante += $dia_entrega_estudiante;
						$entrega_complemento = $dia_entrega_estudiante == 1 ? 'x' : '';
						$pdf->Cell(6,$alturaLinea, $entrega_complemento,'R',0,'C',False);
					}else{
						$entrega_complemento = '';
						$auxOtrosDiasMes++;
					}
				}
				for($j = 0 ; $j < $auxOtrosDiasMes ; $j++){
					$pdf->Cell(6,$alturaLinea, '','R',0,'C',False);
				}

				$pdf->Cell(0, $alturaLinea, $total_entregas_por_estudiante, 'R', 0, 'C');


				// Se utiliza pero imprimir los border del fila.
				$pdf->SetXY($x, $y);
				$pdf->Cell(0,$alturaLinea, '','B',1);
				$linea++;
			}

			$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
			$pdf->Cell(0,$alturaLinea, '', 'T', 0, 'R', False);

			include 'planillas_footer_v4.php';
		}
		else
		{
			echo "<script>alert('No existen registros con los filtros seleccionados.'); window.close(); </script>"; 
		}
	}
}
else if ($tipoPlanilla == 8)
{
	foreach ($sedes as $sede)
	{
		$codigoSede = $sede['cod_sede'];
		$codigoInstitucion = $sede['cod_inst'];
		$consulta_suplente_repitentes_sede = "SELECT *
		FROM entregas_res_$mes$anno2d
		WHERE cod_inst = '$codigoInstitucion'
			AND cod_sede = '$codigoSede'
			AND (tipo = 'S' OR tipo = 'R')
			AND tipo_complem='$tipoComplemento'
		ORDER BY cod_sede, cod_grado, nom_grupo, ape1,ape2,nom1,nom2 ASC";
		$respuesta_suplente_repitentes_sede = $Link->query($consulta_suplente_repitentes_sede) or die("Error al consultar suplentes y repitentes en entregas_res_$mes$anno2d: ". $Link->error);
		if ($respuesta_suplente_repitentes_sede->num_rows > 0)
		{
			$linea = 1;
			$pagina = 1;
			$lineas = 25;
			$paginas = ceil($respuesta_suplente_repitentes_sede->num_rows / $lineas);
			$numero_estudiantes = 0;
			$codigoSede = $sede['cod_sede'];
			$pdf->AddPage();
			$pdf->SetTextColor(0,0,0);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetDrawColor(0,0,0);

			include 'planillas_header_v4.php';
			$pdf->SetLineWidth(.05);

			while($suplente_repitente_sede = $respuesta_suplente_repitentes_sede->fetch_assoc())
			{
				$numero_estudiantes++;
				$suplente_repitente_sede = (object) $suplente_repitente_sede;

				if($linea > $lineas)
				{
					$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
					$pdf->Cell(0, $alturaLinea, '', 'T');
					include 'planillas_footer_v4.php';
					$pdf->AddPage();
					$pagina++;
					include 'planillas_header_v4.php';
					$pdf->SetFont('Arial','',$tamannoFuente);
					$linea = 1;
				}

				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->SetFont('','',$tamannoFuente);
				$pdf->Cell(8,$alturaLinea, $numero_estudiantes,'LR',0,'C',False);
				$pdf->Cell(10,$alturaLinea, $suplente_repitente_sede->tipo_doc_nom,'R',0,'C',False);
				$pdf->Cell(22,$alturaLinea, $suplente_repitente_sede->num_doc,'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea, utf8_decode(mb_strtoupper($suplente_repitente_sede->nom1)),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea, utf8_decode(mb_strtoupper($suplente_repitente_sede->nom2)),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea, utf8_decode(mb_strtoupper($suplente_repitente_sede->ape1)),'R',0,'L',False);
				$pdf->Cell(26.5,$alturaLinea, utf8_decode(mb_strtoupper($suplente_repitente_sede->ape2)),'R',0,'L',False);
				$pdf->Cell(22,$alturaLinea, $suplente_repitente_sede->fecha_nac,'R',0,'C',False);
				$pdf->Cell(16,$alturaLinea, $suplente_repitente_sede->etnia,'R',0,'C',False);
				$pdf->Cell(5,$alturaLinea, $suplente_repitente_sede->genero,'R',0,'C',False);
				$pdf->Cell(5,$alturaLinea, $suplente_repitente_sede->cod_grado,'R',0,'C',False);
				$pdf->Cell(13,$alturaLinea, utf8_decode(mb_strtoupper($tipoComplemento)),'R',0,'C',False);


				// Impresión de las 24 columnas para los días.
				$total_entregas_por_estudiante = 0;
				$auxOtrosDiasMes = 0;
				for($j = 0 ; $j < 22 ; $j++)
				{
					$auxIndice = $j+1;
					if (in_array($auxIndice, $auxDias)){
						$dia_entrega_estudiante = $suplente_repitente_sede->{'D'.$auxIndice};
						$total_entregas_por_estudiante += $dia_entrega_estudiante;	
						$pdf->SetTextColor(190,190,190);
						$entrega_complemento = $dia_entrega_estudiante == 1 ? 'x' : '';
						$pdf->Cell(6,$alturaLinea, $entrega_complemento,'R',0,'C',False);
					}else{
						$entrega_complemento = '';
						$auxOtrosDiasMes++;
					}
				}
				for($j = 0 ; $j < $auxOtrosDiasMes ; $j++){
					$pdf->Cell(6,$alturaLinea, '','R',0,'C',False);
				}

				$pdf->SetTextColor(0,0,0);
				$pdf->Cell(0, $alturaLinea, '', 'R', 0, 'C');


				// Se utiliza pero imprimir los border del fila.
				$pdf->SetXY($x, $y);
				$pdf->Cell(0,$alturaLinea, '','B',1);
				$linea++;
			}

			$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
			$pdf->Cell(0,$alturaLinea, '', 'T', 0, 'R', False);

			include 'planillas_footer_v4.php';
		}
		else
		{
			echo "<script>alert('No existen registros con los filtros seleccionados.');  window.close(); </script>"; 
		}
	}
}

if ($tipoPlanilla == 9) {
	// consulta numero sedes
	$sedesAsignadas = $sedesAtendidas = 0;
	$consultaNumeroSedes = " SELECT DISTINCT(cod_sede) AS cod_sede, id FROM sedes$periodoActual WHERE 1=1 ";
	if (isset($institucion) && $institucion != '') {
		$consultaNumeroSedes .= " AND s.cod_inst = '$institucion' ";
	}
	if (isset($sedeParametro) && $sedeParametro!= '') {
		$consultaNumeroSedes .= " AND s.cod_sede = '$sedeParametro' ";
	}
	$respuestaNumeroSedes = $Link->query($consultaNumeroSedes) or die ('Error al consultar las sedes Ln 1152');
	if ($respuestaNumeroSedes->num_rows > 0) {
		while ($dataNumeroSedes = $respuestaNumeroSedes->fetch_assoc()) {
			$sedesAsignadas += 1;
			$consultaAtencion = " SELECT id, cod_sede FROM sedes_cobertura WHERE cod_sede = '" .$dataNumeroSedes['cod_sede']. "' AND mes = '".$mes."'"; 
			$respuestaAtencion = $Link->query($consultaAtencion) or die ('Error al consultar Ln 1154');
			if ($respuestaAtencion->num_rows > 0) {
				$sedesAtendidas +=1;
			}
		}
	}

	// consultaCoberturas 
	$coberturaComplemento = $coberturaAlmuerzo = $canDias = 0;
	$consultaCobertura = " SELECT CODIGO, Jornada FROM tipo_complemento WHERE valorRacion > 0 ";
	if (isset($tipoComplemento) && $tipoComplemento != '' ) {
		$consultaCobertura .= " AND CODIGO = '" .$tipoComplemento. "'";
	}
	$respuestaCobertura = $Link->query($consultaCobertura) or die ('Error al consultar las cobertura Ln 1157');
	if ($respuestaCobertura->num_rows > 0) {	
		while ($dataCobertura = $respuestaCobertura->fetch_assoc()) {
			$cbTotal[$dataCobertura['CODIGO']][$dataCobertura['Jornada']] = 0;
			$complementos[$dataCobertura['CODIGO']] = $dataCobertura['Jornada'];
		}
	}

	$consultaSemanas1 = " SELECT MIN(CONSECUTIVO) AS minConsecutivo FROM planilla_semanas WHERE SEMANA = '" .$_POST['semana_inicial']. "' ";
	$respuestaSemanas1 = $Link->query($consultaSemanas1) or die ('Error al consultar el consecutivo Ln 1169');
	if ($respuestaSemanas1->num_rows > 0) {
		$dataSemanas1 = $respuestaSemanas1->fetch_assoc();
		$minConsecutivo = $dataSemanas1['minConsecutivo'];
		$consultaSemanas2 = " SELECT MAX(CONSECUTIVO) AS maxConsecutivo FROM planilla_semanas WHERE SEMANA = '" .$_POST['semana_final']. "' ";
		$respuestaSemanas2 = $Link->query($consultaSemanas2) or die ('Error al consultar el consecutivo Ln 1174');
		if ($respuestaSemanas2->num_rows > 0) {
			$dataSemanas2 = $respuestaSemanas2->fetch_assoc();
			$maxConsecutivo = $dataSemanas2['maxConsecutivo'];
			$consultaSemanasImplicitas = " SELECT DISTINCT(SEMANA) AS semana 
												FROM planilla_semanas 
												WHERE CONSECUTIVO BETWEEN $minConsecutivo AND $maxConsecutivo ";
			$respuestaSemanasImplicitas = $Link->query($consultaSemanasImplicitas) or die ('Error al consultar las semanas implicitas' );
			if ($respuestaSemanasImplicitas->num_rows > 0) {
				while ($dataSemanasImplicitas = $respuestaSemanasImplicitas->fetch_assoc()) {
					$consultaCanDias = " SELECT COUNT(ID) AS dias FROM planilla_semanas WHERE SEMANA = '" .$dataSemanasImplicitas['semana']. "' ";
					$respuestaCantDias = $Link->query($consultaCanDias) or die ('Error al consultar los dias 1192');
					if ($respuestaCantDias->num_rows > 0) {
						$dataCantDias = $respuestaCantDias->fetch_assoc();
						$canDias = $dataCantDias['dias'];
						foreach ($complementos as $key => $value) {
							$consultaPriorizacion = " SELECT SUM(".$key.") FROM priorizacion".$dataSemanasImplicitas['semana'];
						}
					}	
				}
			}									
		}
	}

	// consultaEntregasporEtnia
	$totalIndigenas = $totalAfrocolombianos = $totalRaizal = $totalSinPertenencia = $totalRom = 0;
	$consultaEntregasEtnia = " SELECT COUNT(ent.id) AS total, e.descripcion, e.indigena
								FROM entregas_res_$mes$anno2d ent
								INNER JOIN etnia e ON e.id = ent.etnia WHERE 1=1";
	if (isset($institucion) && $institucion != '') {
		$consultaEntregasEtnia .= " AND ent.cod_inst = '" .$institucion. "' "; 
	}
	if (isset($sedeParametro) && $sedeParametro != '') {
		$consultaEntregasEtnia .= " AND ent.cod_sede = '" .$sedeParametro. "' "; 
	}
	if (isset($tipoComplemento) && $tipoComplemento != '') {
		$consultaEntregasEtnia .= " AND ent.tipo_complem = '" .$tipoComplemento. "' "; 
	}	
	$consultaEntregasEtnia .= " GROUP BY ent.etnia ";

	$respuestaEntregasEtnia = $Link->query($consultaEntregasEtnia) or die ('Error al consultar las etnias Ln 1185');
	if ($respuestaEntregasEtnia->num_rows > 0) {
		while ($dataRespuestaEntregasEtnia = $respuestaEntregasEtnia->fetch_assoc()) {
			if ($dataRespuestaEntregasEtnia['indigena'] == 1) {
				$totalIndigenas += $dataRespuestaEntregasEtnia['total'];
			}
			if ($dataRespuestaEntregasEtnia['indigena'] == 0) {
				if ($dataRespuestaEntregasEtnia['descripcion'] == 'AFRODESCENDIENTE' || $dataRespuestaEntregasEtnia['descripcion'] == 'Afrodecendientes') {
					$totalAfrocolombianos = $dataRespuestaEntregasEtnia['total'];
				}
				if ($dataRespuestaEntregasEtnia['descripcion'] == 'RAIZAL' || $dataRespuestaEntregasEtnia['descripcion'] == 'Razal' ) {
					$totalRaizal = $dataRespuestaEntregasEtnia['total'];
				}
				if ($dataRespuestaEntregasEtnia['descripcion'] == 'ROM' || $dataRespuestaEntregasEtnia['descripcion'] == 'Rom' ) {
					$totalRom = $dataRespuestaEntregasEtnia['total'];
				}
				if ($dataRespuestaEntregasEtnia['descripcion'] == 'NO APLICA' || $dataRespuestaEntregasEtnia['descripcion'] == 'No Aplica' ) {
					$totalSinPertenencia = $dataRespuestaEntregasEtnia['total'];
				}
			}
		}
	}

	$complementoAtendidoManana = $complementoAtendidoTarde = 0;
	$consultaEntregasComplemento = " SELECT tipo_complem, 
											sum(". str_replace(",", "+", trim($dia_consulta, ", ")) .") AS total 
										FROM entregas_res_$mes$anno2d ent
										WHERE 1=1 ";
	if (isset($institucion) && $institucion != '') {
		$consultaEntregasComplemento .= " AND ent.cod_inst = '" .$institucion. "' ";
	}		
	if (isset($sedeParametro) && $sedeParametro != '') {
		$consultaEntregasComplemento .= " AND ent.cod_sede = '" .$sedeParametro. "' ";
	}							
	if (isset($tipoComplemento) && $tipoComplemento != '') {
		$consultaEntregasComplemento .= " AND ent.tipo_complem = '" .$tipoComplemento. "' ";
	}
	$consultaEntregasComplemento .= " GROUP BY tipo_complem ";
	$respuestaEntregasComplemento = $Link->query($consultaEntregasComplemento) or die('Error al consultar las entregas por complemento Ln 1239');
	if ($respuestaEntregasComplemento->num_rows > 0) {
		while ($dataEntregasComplemento = $respuestaEntregasComplemento->fetch_assoc()) {
			// var_dump($dataEntregasComplemento);
			foreach ($complementos as $key => $value) {
				if ($value == 2) {
					if ($dataEntregasComplemento['tipo_complem'] == $key) {
						$complementoAtendidoManana += $dataEntregasComplemento['total'];
					}
				}
				if ($value == 3) {
					if ($dataEntregasComplemento['tipo_complem'] == $key) {
						$complementoAtendidoTarde += $dataEntregasComplemento['total'];
					}
				}
			}
			
		}
	}
	
	$tamannoFuente = 7;
	$pdf->AddPage();
	$pdf->SetTextColor(0,0,0);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(160,160,160);
	$pdf->SetFont('Arial','',$tamannoFuente);

	// fila 1
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(37, 15, utf8_decode('DEPARTAMENTO'), 1, 0, 'C', True);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(28, 15, utf8_decode($_SESSION['p_Departamento']), 'B', 0, 'C', False);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Multicell(20, 3.75, utf8_decode(" "."\n CODIGO DANE \n"." "), 1, 'C', True);
	$pdf->setXY($x+20, $y);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(20, 15, utf8_decode($_SESSION['p_CodDepartamento']), 'B', 0, 'C', False);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Multicell(37, 3, utf8_decode("NÚMERO DE \n ESTABLECIMIENTOS \n EDUCATIVOS \n ASIGNADOS PARA \n ATENCIÓN "),1 , 'C', True);
	$pdf->setXY($x+37, $y);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(18, 15, $sedesAsignadas, 'B', 0, 'C', False);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Multicell(37, 3.75, utf8_decode("RACIONES \n PROGRAMADOS \n COMPLEMENTO \n ALIMENTARIO "),1 , 'C', True);
	$pdf->setXY($x+37, $y);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(18, 15, $coberturaComplemento, 'B', 0, 'C', False);
	$pdf->Cell(29, 15, '', 'TLR', 0, 'C', True);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetFillColor(225,225,225);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(35, 15, utf8_decode("INDÍGENA "),'TB',0 ,'C', True);
	$pdf->setXY($x+35, $y);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(18, 15, $totalIndigenas, 'B', 0, 'C', False);
	
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Multicell(35, 3.75, utf8_decode("\n"."AFROCOLOMBIANO / \n PALENQUEROS "."\n"." "), 'TB', 'C', True);
	$pdf->setXY($x+35, $y);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(18, 15, $totalAfrocolombianos, 'BR', 1, 'C', False);

	// fila 2
	$pdf->SetFillColor(160,160,160);
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Cell(37, 15, utf8_decode('MUNICIPIO'), 1, 0, 'C', True);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(28, 15, utf8_decode($_POST['municipioNm']), 'B', 0, 'C', False);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Multicell(20, 3.75, utf8_decode(" "."\n CODIGO DANE \n"." "), 1, 'C', True);
	$pdf->setXY($x+20, $y);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(20, 15, utf8_decode($_SESSION['p_Municipio']), 'B', 0, 'C', False);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Multicell(37, 3, utf8_decode("NÚMERO DE \n ESTABLECIMIENTOS \n EDUCATIVOS \n ATENDIDOS EFEC-\nTIVAMENTE  EN EL MES "),1 , 'C', True);
	$pdf->setXY($x+37, $y);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(18, 15, $sedesAtendidas, 'B', 0, 'C', False);

	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->SetFont('Arial','B',$tamannoFuente);
	$pdf->Multicell(37, 3, utf8_decode(" "."\n"." "."\n"." "."\n"." "."\n"." "."\n"."RACIONES \n ATENDIDAS". " "."\n"." "."\n"." "."\n"." "."\n"." " ),1 , 'C', True);
	$pdf->setXY($x+37, $y);
	$pdf->Cell(9, 15, 'A.M.', 'BR', 0, 'C', False);
	$pdf->SetFont('Arial','',$tamannoFuente);
	$pdf->Cell(9, 15, $complementoAtendidoManana, 'BR', 0, 'C', False);
	

}

if($tipoPlanilla == 1)
{
	// exit(var_dump($tipoPlanilla));
	foreach ($sedes as $sede){ 
		// Consulta que retorna la cantidad de estudiantes de una sede seleccionada.
		$codigoSede = $sede['cod_sede'];
		$mesAdicional = 0;	
		// Cuando piden la planilla vacia
		$pdf->AddPage();
		$pdf->SetTextColor(0,0,0);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetDrawColor(0,0,0);

		$pdf->SetFont('Arial','',$tamannoFuente);
		$lineas = 25;
		$linea = 1;
		$alturaLinea = 4;
		// $codigoSede = null;

		include 'planillas_header_v4.php';
		$pdf->SetLineWidth(.05);
		$pdf->SetFont('Arial','',$tamannoFuente);
		//Inicia impresión de estudiantes de la sede
		$nEstudiante = 0;
		$pdf->SetFont('Arial','',$tamannoFuente);
		for($i = 0 ; $i < 25 ; $i++){
			$nEstudiante++;
			if($linea > $lineas){
				$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
				$alturaCuadroFilas = $alturaLinea * ($linea-1);
				$pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
				include 'planillas_footer_v4.php';
				$pdf->AddPage();
				include 'planillas_header_v4.php';
				$pdf->SetFont('Arial','',$tamannoFuente);
				$linea = 1;
			}
			$x = $pdf->GetX();
			$y = $pdf->GetY();
			$pdf->Cell(8,$alturaLinea,utf8_decode(''),'R',0,'C',False);
			$pdf->Cell(10,$alturaLinea,utf8_decode(''),'R',0,'C',False);
			$pdf->Cell(22,$alturaLinea,utf8_decode(''),'R',0,'L',False);
			$pdf->Cell(26.5,$alturaLinea,utf8_decode(''),'R',0,'L',False);
			$pdf->Cell(26.5,$alturaLinea,utf8_decode(''),'R',0,'L',False);
			$pdf->Cell(26.5,$alturaLinea,utf8_decode(''),'R',0,'L',False);
			$pdf->Cell(26.5,$alturaLinea,utf8_decode(''),'R',0,'L',False);
			$pdf->Cell(22,$alturaLinea,utf8_decode(''),'R',0,'C',False);
			$pdf->Cell(16,$alturaLinea,utf8_decode(''),'R',0,'C',False);
			$pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
			$pdf->Cell(5,$alturaLinea,utf8_decode(''),'R',0,'C',False);
			$pdf->Cell(13,$alturaLinea,utf8_decode(''),'R',0,'C',False);
			// $pdf->Cell(13,$alturaLinea,utf8_decode(''),'R',0,'C',False);

			for($j = 0 ; $j < 22 ; $j++){
				$pdf->Cell(6,$alturaLinea,utf8_decode(''),'R',0,'C',False);
			}

			$pdf->SetXY($x, $y);
			$pdf->Cell(0,$alturaLinea,'','B',1);
			$linea++;
		}

		$Y = $pdf->GetY();
		$pdf->Cell(60,8, utf8_decode(strtoupper('FIRMA RESPONSABLE DEL OPERADOR:')),'LB',0,'L', False);
		$pdf->SetXY(63, $Y);
		$pdf->Cell(108,8, '','BR',0,'L',False);
		$pdf->Cell(60,8,'FIRMA RECTOR ESTABLECIMIENTO EDUCATIVO: ','B',0,'L', False);
		$pdf->Cell(0,8, '','BR',0,'L',False);

		//Termina impresión de estudiantes de la sede
		$pdf->SetXY($xCuadroFilas, $yCuadroFilas);
		$alturaCuadroFilas = $alturaLinea * ($linea-1);
		$pdf->Cell(0,$alturaCuadroFilas,utf8_decode(''),1,0,'R',False);
	
		include 'planillas_footer_v4.php';
	}	
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



