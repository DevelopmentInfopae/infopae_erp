<?php
  	include '../../header.php';
  	set_time_limit (0);
  	ini_set('memory_limit','6000M');
  	$periodoActual = $_SESSION['periodoActual'];

  	if ($permisos['despachos'] == "0") {
?><script type="text/javascript">
  	window.open('<?= $baseUrl ?>', '_self');
</script>
<?php exit(); }

else {
    ?><script type="text/javascript">
      const list = document.querySelector(".li_despachos");
      list.className += " active ";
	  const list2 = document.querySelector(".li_despacho_alimentos");
      list2.className += " active ";
    </script>
  <?php
  }
	
  	require_once '../../db/conexion.php';

   $entregaNom = array(	'1' => 'PRIMERA', 
   							'2' => 'SEGUNDA', 
   							'3' => 'TERCERA', 
   							'4' => 'CUARTA', 
   							'5' => 'QUINTA', 
   							'6' => 'SEXTA', 
   							'7' => 'SÉPTIMA', 
   							'8' => 'OCTAVA', 
   							'9' => 'NOVENA', 
   							'10' => 'DÉCIMA', 
   							'11' => 'UNDÉCIMA', 
   							'12' => 'DUODÉCIMA' );

  	$consultaTipoBusqueda = " SELECT tipo_busqueda FROM parametros ";
  	$respuestaTipoBusqueda = $Link->query($consultaTipoBusqueda) or die ('Error al consultar el tipo de busqueda ' . mysqli_error($Link));
  	if ($respuestaTipoBusqueda->num_rows > 0) {
    	$dataTipoBusqueda = $respuestaTipoBusqueda->fetch_assoc();
   	$tipoBusqueda = $dataTipoBusqueda['tipo_busqueda'];
    	if ($tipoBusqueda == "2") {
      	$consultaNumeroEntrega = " SELECT mes, NumeroEntrega FROM planilla_dias ";
      	$respuestaNumeroEntrega = $Link->query($consultaNumeroEntrega) or die ('Error al consultar el numero de la entrega ' . mysqli_error($Link));
      	if ($respuestaNumeroEntrega->num_rows > 0) {
        		while ($dataNumeroEntrega = $respuestaNumeroEntrega->fetch_assoc()) {
          		$numeroEntrega[$dataNumeroEntrega['mes']] = $dataNumeroEntrega['NumeroEntrega'];
        		}
      	}
    	}
  	}

	$consultaFormatos = " SELECT name, route, icon FROM remission_format WHERE format_status = 1 ORDER BY starting_order "; 
	$respuestaFormatos = $Link->query($consultaFormatos) or die ('Error al consultar los formatos');
	if ($respuestaFormatos->num_rows > 0) {
		while ($dataFormatos = $respuestaFormatos->fetch_assoc()) {
			$formatos[] = $dataFormatos;
		}
	}

	$arrayDetalle = [ 
		'lunes_1' => 'D1',  'martes_1' => 'D2',  'miércoles_1' => 'D3',  'jueves_1' => 'D4',  'viernes_1' => 'D5',
	  	'lunes_2' => 'D6',  'martes_2' => 'D7',  'miércoles_2' => 'D8',  'jueves_2' => 'D9',  'viernes_2' => 'D10',
	  	'lunes_3' => 'D11', 'martes_3' => 'D12', 'miércoles_3' => 'D13', 'jueves_3' => 'D14', 'viernes_3' => 'D15',
	  	'lunes_4' => 'D16', 'martes_4' => 'D17', 'miércoles_4' => 'D18', 'jueves_4' => 'D19', 'viernes_4' => 'D20',
	  	'lunes_5' => 'D21', 'martes_5' => 'D22', 'miércoles_5' => 'D23', 'jueves_5' => 'D24', 'viernes_5' => 'D25'
	];
	// exit(var_dump($labels));
	$nameLabel = get_titles('despachos', 'alimentos', $labels);
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-md-6 col-lg-8">
		<h2><?= $nameLabel ?></h2>
		<ol class="breadcrumb">
		  	<li>
				<a href="<?php echo $baseUrl; ?>">Home</a>
		  	</li>
		  	<li class="active">
				<strong><?= $nameLabel ?></strong>
		  	</li>
		</ol>
	</div>
	<div class="col-md-6 col-lg-4">
		<div class="title-action">
			<?php if($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"){ ?>
				<a href="<?php echo $baseUrl; ?>/modules/despachos/despacho_nuevo.php" target="_self" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a>
			<?php } ?>
		</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<h2>Parámetros de Consulta</h2>
					<form class="col-lg-12" action="despachos.php" name="formDespachos" id="formDespachos" method="post" target="_blank">
					<?php if ($tipoBusqueda == "1"): ?>	
						
						<div class="row">							
							<div class="col-sm-6 col-md-3 form-group">
								<label>Fecha Inicial</label>
								<div class="row compositeDate">
									<div class="col-sm-4 nopadding">
										<select name="annoi" id="annoi" class="form-control select2">
											<option value="">Seleccione...</option>
											<option value="<?php echo $_SESSION['periodoActualCompleto']; ?>" selected ><?php echo $_SESSION['periodoActualCompleto']; ?></option>
										</select>
									</div>

									<div class="col-sm-5 nopadding">
										<?php if(!isset($_GET['pb_mesi']) || $_GET['pb_mesi'] == ''){ $_GET['pb_mesi'] = date("n"); } ?>
										<select name="mesi" id="mesi" onchange="mesFinal();"  class="form-control select2">
											<option value="1" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 1) {echo " selected "; } ?>>Enero</option>
											<option value="2" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 2) {echo " selected "; } ?>>Febrero</option>
											<option value="3" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 3) {echo " selected "; } ?>>Marzo</option>
											<option value="4" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 4) {echo " selected "; } ?>>Abril</option>
											<option value="5" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 5) {echo " selected "; } ?>>Mayo</option>
											<option value="6" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 6) {echo " selected "; } ?>>Junio</option>
											<option value="7" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 7) {echo " selected "; } ?>>Julio</option>
											<option value="8" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 8) {echo " selected "; } ?>>Agosto</option>
											<option value="9" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 9) {echo " selected "; } ?>>Septiembre</option>
											<option value="10" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 10) {echo " selected "; } ?>>Octubre</option>
											<option value="11" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 11) {echo " selected "; } ?>>Noviembre</option>
											<option value="12" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 12) {echo " selected "; } ?>>Diciembre</option>
										</select>
										<input type="hidden" name="mesiConsulta" id="mesiConsulta" value="<?php if (isset($_GET['pb_mesi'])) { echo $_GET['pb_mesi']; } ?>">
									</div>

									<div class="col-sm-3 nopadding">
										<select name="diai" id="diai" class="form-control select2">
											<option value="0">dd</option>
											<option value="1" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 1) { echo " selected "; } ?>>01</option>
											<option value="2" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 2) { echo " selected "; } ?>>02</option>
											<option value="3" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 3) { echo " selected "; } ?>>03</option>
											<option value="4" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 4) { echo " selected "; } ?>>04</option>
											<option value="5" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 5) { echo " selected "; } ?>>05</option>
											<option value="6" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 6) { echo " selected "; } ?>>06</option>
											<option value="7" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 7) { echo " selected "; } ?>>07</option>
											<option value="8" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 8) { echo " selected "; } ?>>08</option>
											<option value="9" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 9) { echo " selected "; } ?>>09</option>
											<option value="10" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 10) { echo " selected "; } ?>>10</option>
											<option value="11" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 11) { echo " selected "; } ?>>11</option>
											<option value="12" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 12) { echo " selected "; } ?>>12</option>
											<option value="13" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 13) { echo " selected "; } ?>>13</option>
											<option value="14" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 14) { echo " selected "; } ?>>14</option>
											<option value="15" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 15) { echo " selected "; } ?>>15</option>
											<option value="16" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 16) { echo " selected "; } ?>>16</option>
											<option value="17" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 17) { echo " selected "; } ?>>17</option>
											<option value="18" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 18) { echo " selected "; } ?>>18</option>
											<option value="19" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 19) { echo " selected "; } ?>>19</option>
											<option value="20" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 20) { echo " selected "; } ?>>20</option>
											<option value="21" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 21) { echo " selected "; } ?>>21</option>
											<option value="22" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 22) { echo " selected "; } ?>>22</option>
											<option value="23" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 23) { echo " selected "; } ?>>23</option>
											<option value="24" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 24) { echo " selected "; } ?>>24</option>
											<option value="25" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 25) { echo " selected "; } ?>>25</option>
											<option value="26" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 26) { echo " selected "; } ?>>26</option>
											<option value="27" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 27) { echo " selected "; } ?>>27</option>
											<option value="28" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 28) { echo " selected "; } ?>>28</option>
											<option value="29" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 29) { echo " selected "; } ?>>29</option>
											<option value="30" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 30) { echo " selected "; } ?>>30</option>
											<option value="31" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 31) { echo " selected "; } ?>>31</option>
										</select>
									</div>
								</div>
							</div>  <!-- form-group -->

							<div class="col-sm-6 col-md-3 form-group">
								<label>Fecha Final</label>
								<div class="row compositeDate">
									<div class="col-sm-4 nopadding">
										<select name="annof" id="annof" class="form-control select2">
											<option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
									 	 </select>
									</div>
									<div class="col-sm-5 nopadding">
									<select name="mesfText" id="mesfText" class="form-control select2">
											<option value="1" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 1) {echo " selected "; } ?>>Enero</option>
											<option value="2" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 2) {echo " selected "; } ?>>Febrero</option>
											<option value="3" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 3) {echo " selected "; } ?>>Marzo</option>
											<option value="4" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 4) {echo " selected "; } ?>>Abril</option>
											<option value="5" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 5) {echo " selected "; } ?>>Mayo</option>
											<option value="6" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 6) {echo " selected "; } ?>>Junio</option>
											<option value="7" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 7) {echo " selected "; } ?>>Julio</option>
											<option value="8" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 8) {echo " selected "; } ?>>Agosto</option>
											<option value="9" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 9) {echo " selected "; } ?>>Septiembre</option>
											<option value="10" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 10) {echo " selected "; } ?>>Octubre</option>
											<option value="11" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 11) {echo " selected "; } ?>>Noviembre</option>
											<option value="12" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 12) {echo " selected "; } ?>>Diciembre</option>
										</select>
										<!-- <input type="text" name="mesfText" id="mesfText" value="mm" readonly="readonly" class="form-control"> -->
										<input type="hidden" name="mesf" id="mesf" value="">
									</div>
									<div class="col-sm-3 nopadding">
										<select name="diaf" id="diaf" class="form-control select2">
											<option value="0">dd</option>
											<option value="1" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 1) {echo " selected "; } ?>>01</option>
											<option value="2" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 2) { echo " selected "; } ?>>02</option>
											<option value="3" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 3) { echo " selected "; } ?>>03</option>
											<option value="4" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 4) { echo " selected "; } ?>>04</option>
											<option value="5" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 5) { echo " selected "; } ?>>05</option>
											<option value="6" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 6) { echo " selected "; } ?>>06</option>
											<option value="7" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 7) { echo " selected "; } ?>>07</option>
											<option value="8" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 8) { echo " selected "; } ?>>08</option>
											<option value="9" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 9) { echo " selected "; } ?>>09</option>
											<option value="10" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 10) { echo " selected "; } ?>>10</option>
											<option value="11" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 11) { echo " selected "; } ?>>11</option>
											<option value="12" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 12) { echo " selected "; } ?>>12</option>
											<option value="13" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 13) { echo " selected "; } ?>>13</option>
											<option value="14" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 14) { echo " selected "; } ?>>14</option>
											<option value="15" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 15) { echo " selected "; } ?>>15</option>
											<option value="16" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 16) { echo " selected "; } ?>>16</option>
											<option value="17" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 17) { echo " selected "; } ?>>17</option>
											<option value="18" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 18) { echo " selected "; } ?>>18</option>
											<option value="19" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 19) { echo " selected "; } ?>>19</option>
											<option value="20" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 20) { echo " selected "; } ?>>20</option>
											<option value="21" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 21) { echo " selected "; } ?>>21</option>
											<option value="22" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 22) { echo " selected "; } ?>>22</option>
											<option value="23" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 23) { echo " selected "; } ?>>23</option>
											<option value="24" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 24) { echo " selected "; } ?>>24</option>
											<option value="25" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 25) { echo " selected "; } ?>>25</option>
											<option value="26" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 26) { echo " selected "; } ?>>26</option>
											<option value="27" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 27) { echo " selected "; } ?>>27</option>
											<option value="28" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 28) { echo " selected "; } ?>>28</option>
											<option value="29" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 29) { echo " selected "; } ?>>29</option>
											<option value="30" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 30) { echo " selected "; } ?>>30</option>
											<option value="31" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 31) { echo " selected "; } ?>>31</option>
										</select>
									</div>
								</div>
							</div> <!-- form-group -->

							<div class="col-sm-6 col-md-3 form-group">
								<label for="semana">Semana</label>
								<select name="semana" id="semana" class="form-control select2">
									<option value="">Seleccione uno</option>
									<?php
										$mes = ($_GET['pb_mesi'] > 9) ? $_GET['pb_mesi'] : "0". $_GET['pb_mesi'];
										$res_sem = $Link->query("SELECT DISTINCT SEMANA_DESPACHO FROM planilla_semanas WHERE MES_DESPACHO = '$mes'") or die (mysqli_error($Link));
										if ($res_sem->num_rows > 0) {
											while ($reg_sem = $res_sem->fetch_assoc()) {
									?>
												<option value="<?= $reg_sem["SEMANA_DESPACHO"]; ?>" <?php if (isset($_GET["pb_semana"]) && $_GET["pb_semana"] == $reg_sem["SEMANA_DESPACHO"]) { echo "selected"; }?>><?= "SEMANA ". $reg_sem["SEMANA_DESPACHO"]; ?></option>
											<?php
												}
										}
										?>
								</select>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="tipo">Tipo Complemento</label>
								<select class="form-control select2" name="tipo" id="tipo">
									<option value="">Seleccione una</option>
									<?php
										$consulta = " SELECT DISTINCT CODIGO FROM tipo_complemento ";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
											while($row = $resultado->fetch_assoc()) {
									?>
										<option value="<?php echo $row["CODIGO"]; ?>" <?php  if (isset($_GET['pb_tipo']) && ($_GET['pb_tipo'] == $row["CODIGO"]) ) { echo ' selected '; } ?>   ><?php echo $row["CODIGO"]; ?></option>
									<?php
											}
										}
									?>
								</select>
							</div>
						</div> <!-- row -->

						<div class="row">
							<div class="col-sm-6 col-md-3 form-group">
								<label for="municipio">Municipio</label>
								<select class="form-control select2" name="municipio" id="municipio">
									<?php if ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7"): ?>
										<option value="">Seleccione uno</option>
									<?php endif ?>
								  	<?php
								  		$condicionMunicipioInstitucion = '';
								  		if ($_SESSION['perfil'] == "6") {
								  			$codigoMunicipio;
								  			$documentoRector = $_SESSION['num_doc'];
								  			$consultaMunicipioInstitucion = "SELECT cod_mun FROM instituciones WHERE cc_rector = $documentoRector; ";
								  			$respuestaMunicipioInstitucion = $Link->query($consultaMunicipioInstitucion) or die ('Error al consultar el municipio de la institución ' . mysqli_error($Link));
								  			if ($respuestaMunicipioInstitucion->num_rows > 0) {
								  				$dataMunicipioInstitucion = $respuestaMunicipioInstitucion->fetch_assoc();
								  				$codigoMunicipio = $dataMunicipioInstitucion['cod_mun'];
								  			}
								  			$condicionMunicipioInstitucion = " AND CodigoDANE = $codigoMunicipio "; 
								  		}
								  		if ($_SESSION['perfil'] == "7") {
								  			$codigoMunicipio = '';
								  			$documentoCoordinador = $_SESSION['num_doc'];
								  			$consultaMunicipioInstitucion = "SELECT i.cod_mun FROM instituciones i INNER JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1";
								  			$respuestaMunicipioInstitucion = $Link->query($consultaMunicipioInstitucion) or die ('Error al consultar el municipio de la institución. ' . mysqli_error($Link));
								  			if ($respuestaMunicipioInstitucion->num_rows > 0) {
									  			$dataMunicipioInstitucion = $respuestaMunicipioInstitucion->fetch_assoc();
									  			$codigoMunicipio = $dataMunicipioInstitucion['cod_mun'];
									  		}
								  			$condicionMunicipioInstitucion = " AND CodigoDANE = $codigoMunicipio ";	
								  		}
										$consulta = " SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE 1=1 and ETC = 0 $condicionMunicipioInstitucion ";
										$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
										if($DepartamentoOperador != ''){
									  		$consulta = $consulta." and CodigoDANE like '$DepartamentoOperador%' ";
										}
										if($_SESSION['p_Municipio'] != '0'){
											$consulta = $consulta." and CodigoDANE = " .$_SESSION['p_Municipio'] ;
										}
										$consulta = $consulta." order by ciudad asc "; 
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
									  		while($row = $resultado->fetch_assoc()) {
								  		?>
									  <option value="<?= $row["codigoDANE"]; ?>" <?php if((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] == $row["codigoDANE"]) || ($municipio_defecto["CodMunicipio"] == $row["codigoDANE"]) || isset($codigoMunicipio)){ echo " selected "; }?>><?= $row["ciudad"]; ?></option>
								  <?php
									  }// Termina el while
									}//Termina el if que valida que si existan resultados
								  ?>
								</select>
							</div> <!-- form-group -->

							<div class="col-sm-6 col-md-3 form-group">
								<label for="institucion">Institución</label>
								<select class="form-control select2" name="institucion" id="institucion">
									<?php if ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7"): ?>
										<option value="">Todas</option>
									<?php endif ?>
		  
								  	<?php
								  		$condicionRector = '';
								  		$codigoInstitucion; 
										if ((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" ) || $municipio_defecto["CodMunicipio"] != "0") {
											// INICIO CODIGO VISTA RECTOR
											if ($_SESSION['perfil'] == '6' && $_SESSION['num_doc'] != "" ) {
												$consultaInstitucion = "SELECT codigo_inst FROM instituciones WHERE cc_rector = " .$_SESSION['num_doc']. " LIMIT 1;";
												$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institución' . mysqli_error($Link));
												if ($respuestaInstitucion->num_rows > 0) {
													$dataInstitucion = $respuestaInstitucion->fetch_assoc();
													$codigoInstitucion = $dataInstitucion['codigo_inst'];	
												}
												$condicionRector = " AND s.cod_inst = $codigoInstitucion ";		
											}
											if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != "") {
												$documentoCoordinador = $_SESSION['num_doc'];
												$consultaInstitucion = "SELECT i.codigo_inst FROM instituciones i LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1 ";
												$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institucion. '.mysqli_error($Link));
												if ($respuestaInstitucion->num_rows > 0) {
													$dataInstitucion = $respuestaInstitucion->fetch_assoc();
													$codigoInstitucion = $dataInstitucion['codigo_inst'];
												}
												$condicionRector = " AND s.cod_inst = $codigoInstitucion ";
											}	
									  		$municipio = (isset($_GET["pb_municipio"])) ? $_GET["pb_municipio"] : $municipio_defecto["CodMunicipio"]; 
									  		$consulta = "SELECT DISTINCT s.cod_inst, s.nom_inst 
															FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc ON s.cod_sede = sc.cod_sede WHERE 1=1";
											if ($municipio != 0 && $municipio != '') {
												$consulta = $consulta." AND s.cod_mun_sede = '$municipio' ";
											}
									  		$consulta = $consulta. "$condicionRector";
									  		$consulta = $consulta." ORDER BY s.nom_inst ASC"; 
									  		$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
									  		if($resultado->num_rows >= 1){
												while($row = $resultado->fetch_assoc()) {
								  	?>
									  				<option value="<?= $row['cod_inst']; ?>" <?php if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?= $row['nom_inst']; ?></option>
								  	<?php
												}
									  		}
										}
								  	?>
								</select>
							</div>  <!-- form-group -->

							<div class="col-sm-6 col-md-3 form-group">
								<label for="sede">sede</label>
								<select class="form-control select2" name="sede" id="sede">
							  		<option value="">Todas</option>
							  		<?php
										$institucion = '';
										if( isset($_GET['pb_institucion']) && $_GET['pb_institucion'] != '' ){
								  			$institucion = $_GET['pb_institucion'];
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
												$condicionCoordinador = " AND s.cod_sede IN ($codigoSedes) ";
								  			}
								  			$consulta = " SELECT DISTINCT s.cod_sede, s.nom_sede from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
								  			$consulta = $consulta."  and s.cod_inst = '$institucion' $condicionCoordinador ";
								  			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
								  			if($resultado->num_rows >= 1){
												while($row = $resultado->fetch_assoc()) {
							  		?>
													<option value="<?= $row['cod_sede']; ?>" <?php if(isset($_GET["pb_sede"]) && $_GET["pb_sede"] == $row['cod_sede'] ){ echo " selected "; }  ?> ><?= $row['nom_sede']; ?></option>
							  		<?php
												}
								  			}
										}
							  		?>
								</select>
							</div>  <!-- form-group -->

							<div class="col-sm-6 col-md-3 form-group">
								<label for="tipoDespacho">Tipo Alimento</label>
								<select class="form-control select2" name="tipoDespacho" id="tipoDespacho">
							  		<!-- <option value="">Todos</option> -->
							  		<?php
										$consulta = " SELECT * FROM tipo_despacho WHERE id != 4 ORDER BY ID DESC ";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
								  			while($row = $resultado->fetch_assoc()) {
							  		?>
								  	<option value="<?= $row["Id"]; ?>"  <?php  if(isset($_GET["pb_tipoDespacho"]) && $_GET["pb_tipoDespacho"] == $row["Id"] ){ echo " selected "; } ?> ><?= $row["Descripcion"]; ?></option>
							  		<?php
								  			}
										}
							  		?>
								</select>
							</div> <!-- form-group -->

							<div class="col-sm-6 col-md-3 form-group">
								<label for="ruta">Ruta</label>
								<select class="form-control select2" name="ruta" id="ruta">
							  		<option value="">Todos</option>
							  		<?php
										$consulta = "SELECT * FROM rutas ORDER BY nombre ASC";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
								  			while($row = $resultado->fetch_assoc()) {
							  		?>
									<option value="<?= $row["ID"]; ?>" <?php if(isset($_GET["pb_ruta"]) && $_GET["pb_ruta"] == $row["ID"] ){ echo " selected ";} ?> ><?= $row["Nombre"]; ?></option>
							  		<?php
								  			}
										}
							  		?>
								</select>
								<input type="hidden" name="rutaNm" id="rutaNm" value="">
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="paginasObservaciones">Páginas de observaciones</label>
								<input type="number" name="paginasObservaciones" id="paginasObservaciones" value="1" class="form-control text-center">
							</div>

							<div class="col-sm-4   form-group">
								<label for="imprimirMes">Imprimir nombre del mes</label>
								<div>
									<input class="i-checks" type="checkbox" name="imprimirMes" id="imprimirMes" checked>
								</div>
							</div>
						</div> <!-- row -->

						<div class="row">
							<div class="col-sm-12 form-group">
								<label for="observaciones">Observaciones</label>
								<textarea class="form-control" name="observaciones" id="observaciones" cols="30" rows="5"></textarea>
							</div>
						</div> <!-- row -->
												
						<div class="row">
							<div class="col-sm-3 form-group">
								<input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_GET['consultar']) && $_GET['consultar'] != '') {echo $_GET['consultar']; } ?>" >
								<button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong><i class="fa fa-search"></i> Buscar</strong></button>
							</div>
						</div> <!-- row -->
					<?php endif ?>

					<!-- si la busqueda se hace por numero de entrega se despliega el siguiente formulario --> 
					<?php if ($tipoBusqueda == "2"): ?>
						<div class="row">
							<div class="col-lg-3 col-md-6 col-sm-12 form-group">
								<label for="numeroEntrega">Número Entrega</label>
								<select class="form-control" id="numeroEntrega" name="numeroEntrega">
								<?php foreach ($numeroEntrega as $mes => $entrega): ?>
									<option value="<?= $mes; ?>" <?= (isset($_GET['pb_entrega']) && $_GET['pb_entrega'] == $mes) ? "selected" : "" ?> ><?= $entregaNom[$entrega]; ?></option>
								<?php endforeach ?>
								</select>
								<input type="hidden" name="mesiConsulta" id="mesiConsulta" value="<?= (isset($_GET['pb_entrega']) && $_GET['pb_entrega'] != '' && $_GET['pb_entrega'] > 10) ? $_GET['pb_entrega'] : str_replace('0','',$_GET['pb_entrega']) ?>">
								<input type="hidden" name="mesi" id="mesi" value="<?= (isset($_GET['pb_entrega']) && $_GET['pb_entrega'] > 10) ? $_GET['pb_entrega'] : str_replace('0','',$_GET['pb_entrega']) ?>">
								<input type="hidden" name="annoi" id="annoi" value="<?= $_SESSION['periodoActualCompleto']; ?>">
							</div> <!-- col -->

							<div class="col-lg-3 col-md-6 col-sm-12 form-group">
								<label for="tipo">Tipo Complemento</label>
								<select class="form-control" name="tipo" id="tipo">
									<option value="">Seleccione una</option>
									<?php
										$consulta = " select DISTINCT CODIGO from tipo_complemento ";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
											while($row = $resultado->fetch_assoc()) {
									?>
										<option value="<?php echo $row["CODIGO"]; ?>" <?php  if (isset($_GET['pb_tipo']) && ($_GET['pb_tipo'] == $row["CODIGO"]) ) { echo ' selected '; } ?>   ><?php echo $row["CODIGO"]; ?></option>
									<?php
											}
										}
									?>
								</select>
							</div> <!-- col -->

							<div class="col-lg-3 col-md-6 col-sm-12 form-group">
								<label for="fechaInicial">Municipio</label>
								<select class="form-control" name="municipio" id="municipio">
									<?php if ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7"): ?>
										<option value="">Seleccione uno</option>
									<?php endif ?>
								  	<?php
								  	$condicionMunicipioInstitucion = '';
								  	if ($_SESSION['perfil'] == "6") {
								  		$codigoMunicipio;
								  		$documentoRector = $_SESSION['num_doc'];
								  		$consultaMunicipioInstitucion = "SELECT cod_mun FROM instituciones WHERE cc_rector = $documentoRector; ";
								  		$respuestaMunicipioInstitucion = $Link->query($consultaMunicipioInstitucion) or die ('Error al consultar el municipio de la institución ' . mysqli_error($Link));
								  		if ($respuestaMunicipioInstitucion->num_rows > 0) {
								  			$dataMunicipioInstitucion = $respuestaMunicipioInstitucion->fetch_assoc();
								  			$codigoMunicipio = $dataMunicipioInstitucion['cod_mun'];
								  		}
								  		$condicionMunicipioInstitucion = " AND CodigoDANE = $codigoMunicipio "; 
								  	}
								  	if ($_SESSION['perfil'] == "7") {
								  		$codigoMunicipio = '';
								  		$documentoCoordinador = $_SESSION['num_doc'];
								  		$consultaMunicipioInstitucion = "SELECT i.cod_mun FROM instituciones i INNER JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1";
								  		$respuestaMunicipioInstitucion = $Link->query($consultaMunicipioInstitucion) or die ('Error al consultar el municipio de la institución. ' . mysqli_error($Link));
								  		if ($respuestaMunicipioInstitucion->num_rows > 0) {
								  			$dataMunicipioInstitucion = $respuestaMunicipioInstitucion->fetch_assoc();
								  			$codigoMunicipio = $dataMunicipioInstitucion['cod_mun'];
								  		}
								  		$condicionMunicipioInstitucion = " AND CodigoDANE = $codigoMunicipio ";	
								  	}

									$consulta = " select DISTINCT codigoDANE, ciudad from ubicacion where 1=1 and ETC = 0 $condicionMunicipioInstitucion ";
									$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
									if($DepartamentoOperador != ''){
									  $consulta = $consulta." and CodigoDANE like '$DepartamentoOperador%' ";
									}
									$consulta = $consulta." order by ciudad asc ";
									$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
									if($resultado->num_rows >= 1){
									  while($row = $resultado->fetch_assoc()) {
								  	?>
									  <option value="<?= $row["codigoDANE"]; ?>" <?php if((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] == $row["codigoDANE"]) || ($municipio_defecto["CodMunicipio"] == $row["codigoDANE"]) || isset($codigoMunicipio)){ echo " selected "; }?>><?= $row["ciudad"]; ?></option>
								  	<?php
									  }// Termina el while
									}//Termina el if que valida que si existan resultados
								  ?>
								</select>
							</div> <!-- col -->

							<div class="col-lg-3 col-md-6 col-sm-12 form-group">
								<label for="institucion">Institución</label>
								<select class="form-control" name="institucion" id="institucion">
									<?php if ($_SESSION['perfil'] != "6" && $_SESSION['perfil'] != "7"): ?>
										<option value="">Todas</option>
									<?php endif ?>
		  
								  	<?php
									  	$condicionRector = '';
									  	$codigoInstitucion;
										if ((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" ) || $municipio_defecto["CodMunicipio"] != "") {
											// INICIO CODIGO VISTA RECTOR
											if ($_SESSION['perfil'] == '6' && $_SESSION['num_doc'] != "" ) {
												$consultaInstitucion = "SELECT codigo_inst FROM instituciones WHERE cc_rector = " .$_SESSION['num_doc']. " LIMIT 1;";
												// echo $consultaInstitucion;
												$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institución' . mysqli_error($Link));
												if ($respuestaInstitucion->num_rows > 0) {
													$dataInstitucion = $respuestaInstitucion->fetch_assoc();
													$codigoInstitucion = $dataInstitucion['codigo_inst'];	
												}
												$condicionRector = " AND s.cod_inst = $codigoInstitucion ";		
											}
											if ($_SESSION['perfil'] == "7" && $_SESSION['num_doc'] != "") {
												$documentoCoordinador = $_SESSION['num_doc'];
												$consultaInstitucion = "SELECT i.codigo_inst FROM instituciones i LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador LIMIT 1 ";
												$respuestaInstitucion = $Link->query($consultaInstitucion) or die ('Error al consultar la institucion. '.mysqli_error($Link));
												if ($respuestaInstitucion->num_rows > 0) {
													$dataInstitucion = $respuestaInstitucion->fetch_assoc();
													$codigoInstitucion = $dataInstitucion['codigo_inst'];
												}
												$condicionRector = " AND s.cod_inst = $codigoInstitucion ";
											}	
										  $municipio = (isset($_GET["pb_municipio"])) ? $_GET["pb_municipio"] : $municipio_defecto["CodMunicipio"];
										  $consulta = "SELECT DISTINCT s.cod_inst, s.nom_inst FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc ON s.cod_sede = sc.cod_sede WHERE 1=1";
										  $consulta = $consulta." AND s.cod_mun_sede = '$municipio' $condicionRector";
										  $consulta = $consulta." ORDER BY s.nom_inst ASC";
										  // var_dump($consulta);
										  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										  if($resultado->num_rows >= 1){
											while($row = $resultado->fetch_assoc()) {
								  	?>
									  			<option value="<?= $row['cod_inst']; ?>" <?php if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?= $row['nom_inst']; ?></option>
								  	<?php
												}
									  		}
										}
								  	?>
								</select>
							</div> <!-- col -->
						</div> <!-- row -->

						<div class="row">
							<div class="col-lg-3 col-md-6 col-sm-12 form-group">
								<label for="sede">sede</label>
								<select class="form-control" name="sede" id="sede">
							  		<option value="">Todas</option>
							  		<?php
										$institucion = '';
										if( isset($_GET['pb_institucion']) && $_GET['pb_institucion'] != '' ){
								  			$institucion = $_GET['pb_institucion'];
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
												$condicionCoordinador = " AND s.cod_sede IN ($codigoSedes) ";
								  			}
								  			$consulta = " SELECT DISTINCT s.cod_sede, s.nom_sede FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede WHERE 1=1 ";
								  			$consulta = $consulta."  and s.cod_inst = '$institucion' $condicionCoordinador ";
								  			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
								  			if($resultado->num_rows >= 1){
												while($row = $resultado->fetch_assoc()) {
							  		?>
									<option value="<?= $row['cod_sede']; ?>" <?php if(isset($_GET["pb_sede"]) && $_GET["pb_sede"] == $row['cod_sede'] ){ echo " selected "; }  ?> ><?= $row['nom_sede']; ?></option>
							  		<?php
												}
								  			}
										}
							  		?>
								</select>
							</div> <!-- col -->

							<div class="col-lg-3 col-md-6 col-sm-12 form-group">
								<label for="tipoDespacho">Tipo Alimento</label>
								<select class="form-control" name="tipoDespacho" id="tipoDespacho">
							  		<option value="">Todos</option>
							  		<?php
										$consulta = " SELECT * FROM tipo_despacho WHERE id != 4 ORDER BY Descripcion ASC ";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
								 	 		while($row = $resultado->fetch_assoc()) {
							  		?>
								  				<option value="<?= $row["Id"]; ?>"  <?php  if(isset($_GET["pb_tipoDespacho"]) && $_GET["pb_tipoDespacho"] == $row["Id"] ){ echo " selected "; } ?> ><?= $row["Descripcion"]; ?></option>
							  		<?php
								  			}
										}
							  		?>
								</select>
							</div> <!-- col -->

							<div class="col-md-6 col-sm-12 form-group">
								<label for="ruta">Ruta</label>
								<select class="form-control" name="ruta" id="ruta">
							  		<option value="">Todos</option>
							  		<?php
										$consulta = "SELECT * FROM rutas ORDER BY nombre ASC";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
								  			while($row = $resultado->fetch_assoc()) {
							  		?>
												<option value="<?= $row["ID"]; ?>" <?php if(isset($_GET["pb_ruta"]) && $_GET["pb_ruta"] == $row["ID"] ){ echo " selected ";} ?> ><?= $row["Nombre"]; ?></option>
							  		<?php
								  			}
										}
							  		?>
								</select>
								<input type="hidden" name="rutaNm" id="rutaNm" value="">
							</div> <!-- col -->

							<div class="col-md-3 col-sm-6 form-group">
								<label for="paginasObservaciones">Páginas de observaciones</label>
								<input type="number" name="paginasObservaciones" id="paginasObservaciones" value="1" class="form-control">
							</div> <!-- col -->
						</div> <!-- row -->	

						<div class="row">
							<div class="col-sm-12 form-group">
								<label for="semana_final">Imprimir nombre del mes</label>
								<div>
									<input class="i_checks" type="checkbox" name="imprimirMes" id="imprimirMes" checked>
								</div>
							</div> <!-- col -->
						</div> <!-- row -->	

						<div class="row">
							<div class="col-sm-12 form-group">
								<label for="observaciones">Observaciones</label>
								<textarea class="form-control" name="observaciones" id="observaciones" cols="30" rows="5"></textarea>
							</div> <!-- col -->
						</div> <!-- row -->	
						<input type="hidden" name="annof" id="annof" value="">
						<input type="hidden" name="mesfText" id="mesfText" value="">
						<input type="hidden" name="mesf" id="mesf" value="">
						<input type="hidden" name="diaf" id="diaf" value="">
						<input type="hidden" name="semana" id="semana" value="">
						<div class="row">
							<div class="col-sm-3 form-group">
								<input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_GET['consultar']) && $_GET['consultar'] != '') {echo $_GET['consultar']; } ?>" >
								<button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong><i class="fa fa-search"></i> Buscar</strong></button>
							</div> <!-- col -->
						</div> <!-- row -->
					<?php endif ?>	

					<?php
						$tablaMes = '';
						if(isset($_GET["pb_btnBuscar"]) && $_GET["pb_btnBuscar"] == 1){
	  						if(isset($_GET["pb_mesi"]) && $_GET["pb_mesi"] != "" ){
								// Ajustado formato del mes inicial para hacer el llamado de la tabla con los registros para ese més.
								$mesinicial = $_GET["pb_mesi"];
								if($mesinicial < 10){
		  							$tablaMes = '0'.$mesinicial;
								}else{
		  							$tablaMes = $mesinicial;
								}
	  						}
	  						// seccion se anexa para cuando la busqueda sea con el numero de la entrega
	  						if (isset($_GET['pb_entrega']) && $_GET['pb_entrega'] != "") {
	  							$mesinicial = $_GET['pb_entrega'];
	  							$tablaMes = $mesinicial;
	  						}

	  						$bandera = 0;
	  						if($tablaMes == ''){
								$bandera++;
								echo "<br> <h3>Debe seleccionar el mes o entrega inicial.</h3> ";
	  						}else{
								$tablaAnno = $_SESSION['periodoActual'];
								$consulta = " show tables like 'productosmov$tablaMes$tablaAnno' ";
								$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
								$existe = $result->num_rows;
								if($existe > 0){
		  							$consulta = " show tables like 'despachos_enc$tablaMes$tablaAnno' ";
		  							$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
		  							$existe = $result->num_rows;
		  							if($existe <= 0){
										$bandera++;
										echo "<br> <h3>No se encontraron registros para este periodo.</h3> ";
		  							}
								}else{
		  							$bandera++;
		  							echo "<br> <h3>No se encontraron registros para este periodo.</h3> ";
								}
	  						}
	  						if($bandera == 0){
		  						$consulta = "  SELECT 	s.cod_mun_sede,
		  														s.cod_inst,
		  														s.cod_sede,
		  														de.Num_doc,
		  														de.FechaHora_Elab,
		  														de.Semana,
																de.Dias,
																de.Tipo_Complem,
																de.tipodespacho,
																(SELECT Descripcion FROM tipo_despacho WHERE id = de.tipodespacho) AS tipodespacho_nm, 
																de.estado,
																u.Ciudad,
																b.NOMBRE AS bodegaOrigen,
																s.nom_inst AS nom_inst,
																s.nom_sede AS bodegaDestino,
																vm.descripcion AS descVariacion
		  													FROM despachos_enc$tablaMes$tablaAnno de
		  													LEFT JOIN sedes$tablaAnno s ON s.cod_sede = de.cod_Sede
		  													LEFT JOIN ubicacion u ON u.codigoDANE = s.cod_mun_sede and u.ETC = 0
		  													LEFT JOIN productosmov$tablaMes$tablaAnno pm ON pm.Numero = de.Num_doc AND pm.Documento = 'DES'
		  													LEFT JOIN bodegas b ON b.ID = pm.BodegaOrigen
		  													LEFT JOIN variacion_menu vm on vm.id = de.cod_variacion_menu 
															WHERE 1 = 1 ";
												
								if(isset($_GET["pb_diai"]) && $_GET["pb_diai"] > 0 ){
									$concatenada = '';
									$consultaDiaPlanilla = "SELECT CONCAT(NOMDIAS,'_',CICLO) AS concat
																			FROM planilla_semanas 
																			WHERE MES = '$tablaMes' 
																				" . ( (isset($_GET["pb_semana"]) && $_GET["pb_semana"] != "") ? " AND SEMANA = '" .$_GET["pb_semana"]. "'" : ""  ).
																				" AND DIA = '" .$_GET["pb_diai"]. "' LIMIT 1 ";		
																									
									$respuestaDiaPlanilla = $Link->query($consultaDiaPlanilla) or die ('Error al consultar el dia ');
									if ($respuestaDiaPlanilla->num_rows > 0) {
										$dataRespuestaDiaPlanilla = $respuestaDiaPlanilla->fetch_assoc();
										$concatenada = $dataRespuestaDiaPlanilla['concat'];
									}								
									if ($concatenada != '') {
										$columna = isset($arrayDetalle[$concatenada]) ? $arrayDetalle[$concatenada] : '';
										$consulta .= " AND Num_Doc IN ( SELECT distinct Num_doc FROM despachos_det$tablaMes$tablaAnno WHERE  $columna  > 0 )";
									}else{
										$consulta .= " AND 1 = 0 ";
									}
								}

								if(isset($_GET["pb_diaf"]) && $_GET["pb_diaf"] > 0 ){
									$concatenada = '';
									$consultaDiaPlanilla = "SELECT CONCAT(NOMDIAS,'_',CICLO) AS concat
																			FROM planilla_semanas 
																			WHERE MES = '$tablaMes' 
																				" . ( (isset($_GET["pb_semana"]) && $_GET["pb_semana"] != "") ? " AND SEMANA = '" .$_GET["pb_semana"]. "'" : ""  ).
																				"AND DIA = '" .$_GET["pb_diaf"]. "' LIMIT 1 ";										
									$respuestaDiaPlanilla = $Link->query($consultaDiaPlanilla) or die ('Error al consultar el dia ');
									if ($respuestaDiaPlanilla->num_rows > 0) {
										$dataRespuestaDiaPlanilla = $respuestaDiaPlanilla->fetch_assoc();
										$concatenada = $dataRespuestaDiaPlanilla['concat'];
									}									
									if ($concatenada != '') {
										$columna = isset($arrayDetalle[$concatenada]) ? $arrayDetalle[$concatenada] : '';
										$consulta .= "  OR Num_Doc IN ( SELECT distinct Num_doc FROM despachos_det$tablaMes$tablaAnno WHERE  $columna  > 0 ) ";
									}else {
										$consulta .= " AND 1 = 0 ";
									}
								}
								if (isset($_GET["pb_semana"]) && $_GET["pb_semana"] != "" ){
									$semana = $_GET["pb_semana"];
									$consulta .= " AND semana = '$semana'";
								}
		  						if(isset($_GET["pb_tipo"]) && $_GET["pb_tipo"] != "" ){
									$tipo = $_GET["pb_tipo"];
									$consulta = $consulta." and Tipo_Complem = '".$tipo."' ";
		  						}
		  						if(isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" ){
									$municipio = $_GET["pb_municipio"];
									$consulta = $consulta." and s.cod_mun_sede = '".$municipio."' ";
		 	 					}
		  						if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] != "" ){
									$institucion = $_GET["pb_institucion"];
									$consulta = $consulta." and cod_inst = '".$institucion."' ";
		  						}
		  						if(isset($_GET["pb_sede"]) && $_GET["pb_sede"] != "" ){
									$sede = $_GET["pb_sede"];
									$consulta = $consulta." and s.cod_sede = '".$sede."' ";
		  						}
		  						if(isset($_GET["pb_sede"]) && $_GET["pb_sede"] == "" ){
		  							if ($_SESSION['perfil'] == "7") {
		  								$documentoCoordinador = $_SESSION['num_doc'];
		  								$codigoSedes = "";
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
    									$consulta .= " AND s.cod_sede IN ($codigoSedes) ";
		  							}
		  						}
		  						if(isset($_GET["pb_tipoDespacho"]) && $_GET["pb_tipoDespacho"] != "99" ){
									$tipoDespacho = $_GET["pb_tipoDespacho"];
									$consulta = $consulta." and TipoDespacho = ".$tipoDespacho." ";
		  						}
		  						if(isset($_GET["pb_ruta"]) && $_GET["pb_ruta"] != "" ){
									$ruta = $_GET["pb_ruta"];
									$consulta = $consulta." and s.cod_sede in (select cod_sede from rutasedes where IDRUTA = $ruta)";
		  						}
								// exit(var_dump($consulta));
		  						$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
					?>
					<hr>
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover selectableRows" id="box-table-movimientos" >
								<thead>
									<tr>
									  <th class="text-center">
										  <label for="seleccionarVarios">Todos</label>
										<input type="checkbox" class="i-checks" name="seleccionarVarios" id="seleccionarVarios">
									  </th>
									  <th class="text-center">Número</th>
									  <th class="text-center">Fecha</th>
									  <th class="text-center">Semana</th>
									  <th class="text-center">Dias</th>
									  <th class="text-center">Tipo Ración</th>
									  <th class="text-center">Variación</th>
									  <th class="text-center">Tipo Despacho</th>
									  <th class="text-center"> Municipio </th>
									  <th class="text-center">Bodega Origen</th>
									  <th class="text-center">Institución</th>
									  <th class="text-center"> Bodega Destino </th>
									  <th class="text-center">Estado</th>
									</tr>
			  					</thead>
			  					<tbody>
									<?php 
										if($resultado->num_rows >= 1){ 
											while($row = $resultado->fetch_assoc()) { ?>
						  						<tr>
													<td class="text-center">
														<input 	type="checkbox" 
																	class="i-checks despachos" 
																	value="<?php echo $row['Num_doc']; ?>" 
																	name="<?php echo $row['Num_doc']; ?>"
																	id="<?php echo $row['Num_doc']; ?>"<?php if($row['estado'] == 0){echo " disabled "; } ?> 
																	semana="<?php echo $row['Semana']; ?>" 
																	complemento="<?php echo $row['Tipo_Complem'];?>" 
																	tipo="<?php echo $row['tipodespacho'];?>" 
																	sede="<?php echo $row['cod_sede'];?>" 
																	nom_sede="<?php echo $row['bodegaDestino'];?>" 
																	estado="<?php echo $row['estado'];?>"/>
													</td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Num_doc']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['FechaHora_Elab']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Semana']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Dias']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Tipo_Complem']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['descVariacion']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['tipodespacho_nm']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Ciudad']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['bodegaOrigen']; ?></td>
													<td class="text-center" type ="hidden" > <?php echo $row['nom_inst']; ?> </td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['bodegaDestino']; ?></td>
													<td class="text-center" onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');">
							  							<?php
							  								$estado = $row['estado'];
							  								switch ($estado) {
																case 0:
																	echo "<span  class='label label-danger' > <i class='fas fa-times'></i> Eliminado</span>"; break;
																case 1:
																	echo "<span  class='label label-primary' > <i class='fas fa-check'></i> Enviado</span>"; break;
																case 2:
																	echo "<span  class='label label-warning' > <i class='fas fa-circle'></i> Pendiente</span>"; break;
																default:
																	echo $estado; break;
							  								}
							  							?>
													</td>
						  						</tr>
				  					<?php 
				  							}  // while
				  						} // if resultados	 
				  					?>
								</tbody>
								<tfoot>
						  			<tr>
				  						<th class="text-center"></th>
				  						<th class="text-center">Número</th>
				  						<th class="text-center">Fecha</th>
				  						<th class="text-center">Semana</th>
				  						<th class="text-center">Dias</th>
				  						<th class="text-center">Tipo Ración</th>
				  						<th class="text-center">Variación</th>
				  						<th class="text-center">Tipo Despacho</th>
				  						<th class="text-center">Municipio</th>
				  						<th class="text-center">Bodega Origen</th>
				  						<th class="text-center">Institución</th>
				  						<th class="text-center">Bodega Destino</th>
				  						<th class="text-center">Estado</th>
									</tr>
								</tfoot>
							</table>
						</div> <!-- table responsive -->

					<?php
							}// Termina el if que valida si la bandera continua igual a cero
						}// Termina el if que valida si se recibió el boton de busqueda del form de parametros.
					?>
		  			</form>
				</div><!-- /.ibox-content -->
	  		</div><!-- /.ibox float-e-margins -->
		</div><!-- /.col-lg-12 -->
  	</div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  	<div class="modal-dialog modal-sm">
    	<div class="modal-content">
      		<div class="modal-header text-info" style="padding: 15px;">
        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        		<h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
      		</div>
      		<div class="modal-body">
				<?php if($_SESSION['p_inventory'] != 0): ?>
					<p class="text-center">¿Esta seguro de eliminar?, si el estado es enviado, <strong> afectara el inventario de la bodega </strong></p>
				<?php else: ?>
					<p class="text-center"><strong> ¿Esta seguro de eliminar? </strong></p>
				<?php endif; ?>   		
      		</div>
      		<div class="modal-footer">
        		<input type="hidden" id="anno_eliminar">
        		<input type="hidden" id="mes_eliminar">
        		<input type="hidden" id="num_Doc_eliminar">
        		<button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
        		<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="deleteRemision();"><i class="fa fa-check"></i> Aceptar</button>
      		</div>
    	</div>
  	</div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<script src="<?= $baseUrl; ?>/modules/despachos/js/despachos.js?v=20200423"></script>
<script>
	$(document).ready(function(){
		var botonAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">';
		<?php if ($_SESSION['perfil'] == "0" || $permisos['despachos'] == "1" || $permisos['despachos'] == "2"): ?>
			<?php foreach ($formatos as $keyF => $valueF) { ?>
				<?php if ($_SESSION['perfil'] != '6' && $_SESSION['perfil'] != '7'): ?>
					botonAcciones += '<li><a href="#" onclick="<?= $valueF['route'] ?>"> <i class="<?= $valueF['icon'] ?>"></i> &nbsp <?= $valueF['name'] ?> </a></li>';
			 	<?php endif ?>
			<?php } ?> 
		
			<?php if($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"){ ?>
				botonAcciones += '<li class="divider"></li>';
				<?php if($_SESSION['p_inventory'] != 0): ?> 
					botonAcciones += '<li><a href="#" onclick="enviar_despacho()"><i class="fa fa-upload fa-lg"></i> &nbsp Enviar </a></li>';
				<?php endif ?>
				botonAcciones += '<li><a href="#" onclick="editar_despacho()"><i class="fas fa-pencil-alt fa-lg"></i> &nbsp Editar  </a></li>';
				botonAcciones += '<li><a href="#" onclick="despachos_por_sede_fecha_lote()"><i class="fas fa-pencil-alt fa-lg"></i> &nbsp Lotes y Fechas </a></li>';
				botonAcciones += '<li><a href="#" onclick="eliminar_despacho()"><i class="fa fa-trash fa-lg"></i> &nbsp Eliminar </a></li>';
			<?php } ?>
			botonAcciones += '</ul></div>';
			$('.containerBtn').html(botonAcciones);
		<?php endif ?>	
	});
</script>
<?php mysqli_close($Link); ?>

<form action="despacho_por_sede.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede" target="_blank">
  	<input type="hidden" name="despachoAnnoI" id="despachoAnnoI" value="">
  	<input type="hidden" name="despachoMesI" id="despachoMesI" value="">
  	<input type="hidden" name="paginasObservacionesI" id="paginasObservacionesI" value="">
  	<input type="hidden" name="despacho" id="despacho" value="">
</form>

<form action="despachos.php" id="parametrosBusqueda" method="get">	
  	<input type="hidden" id="pb_entrega" name="pb_entrega" value="">
  	<input type="hidden" id="pb_annoi" name="pb_annoi" value="">
  	<input type="hidden" id="pb_mesi" name="pb_mesi" value="">
  	<input type="hidden" id="pb_diai" name="pb_diai" value="">
  	<input type="hidden" id="pb_annof" name="pb_annof" value="">
  	<input type="hidden" id="pb_mesf" name="pb_mesf" value="">
  	<input type="hidden" id="pb_diaf" name="pb_diaf" value="">
  	<input type="hidden" id="pb_semana" name="pb_semana" value="">
  	<input type="hidden" id="pb_tipo" name="pb_tipo" value="">
  	<input type="hidden" id="pb_municipio" name="pb_municipio" value="">
  	<input type="hidden" id="pb_institucion" name="pb_institucion" value="">
  	<input type="hidden" id="pb_sede" name="pb_sede" value="">
  	<input type="hidden" id="pb_tipoDespacho" name="pb_tipoDespacho" value="">
  	<input type="hidden" id="pb_ruta" name="pb_ruta" value="">
  	<input type="hidden" id="pb_btnBuscar" name="pb_btnBuscar" value="">
</form>

</body>
</html>
