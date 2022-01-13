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

  require_once '../../db/conexion.php';

   $entregaNom = array('1' => 'PRIMERA', '2' => 'SEGUNDA', '3' => 'TERCERA', '4' => 'CUARTA', '5' => 'QUINTA', '6' => 'SEXTA', '7' => 'SÉPTIMA', '8' => 'OCTAVA', '9' => 'NOVENA', '10' => 'DÉCIMA', '11' => 'UNDÉCIMA', '12' => 'DUODÉCIMA' );
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
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-md-6 col-lg-8">
		<h2>Despachos</h2>
		<ol class="breadcrumb">
		  <li>
			<a href="<?php echo $baseUrl; ?>">Home</a>
		  </li>
		  <li class="active">
			<strong>Despachos</strong>
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
								<label for="fechaInicial">Fecha Inicial</label>
								<div class="row compositeDate">
									<div class="col-sm-4 nopadding">
										<select name="annoi" id="annoi" class="form-control">
											<option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
										</select>
									</div>

									<div class="col-sm-5 nopadding">
										<?php if(!isset($_GET['pb_mesi']) || $_GET['pb_mesi'] == ''){ $_GET['pb_mesi'] = date("n"); } ?>
										<select name="mesi" id="mesi" onchange="mesFinal();" class="form-control">
											<option value="">mm</option>
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
										<select name="diai" id="diai" class="form-control">
											<option value="">dd</option>
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
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="fechaInicial">Fecha Final</label>
								<div class="row compositeDate">
									<div class="col-sm-4 form-group nopadding">
										<select name="annof" id="annof" class="form-control">
											<option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
									 	 </select>
									</div>
									<div class="col-sm-5 form-group nopadding">
										<input type="text" name="mesfText" id="mesfText" value="mm" readonly="readonly" class="form-control">
										<input type="hidden" name="mesf" id="mesf" value="">
									</div>
									<div class="col-sm-3 form-group nopadding">
										<select name="diaf" id="diaf" class="form-control">
											<option value="">dd</option>
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
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="semana">Semana</label>
								<select name="semana" id="semana" class="form-control">
									<option value="">Seleccione uno</option>
									<?php
										$mes = ($_GET['pb_mesi'] > 9) ? $_GET['pb_mesi'] : "0". $_GET['pb_mesi'];
										$res_sem = $Link->query("SELECT DISTINCT SEMANA FROM planilla_semanas WHERE MES = '$mes'") or die (mysqli_error($Link));
										if ($res_sem->num_rows > 0) {
											while ($reg_sem = $res_sem->fetch_assoc()) {
									?>
												<option value="<?= $reg_sem["SEMANA"]; ?>" <?php if (isset($_GET["pb_semana"]) && $_GET["pb_semana"] == $reg_sem["SEMANA"]) { echo "selected"; }?>><?= "SEMANA ". $reg_sem["SEMANA"]; ?></option>
											<?php
												}
										}
										?>
								</select>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
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
							</div>
						</div>

						<div class="row">
							<div class="col-sm-4 col-md-2 form-group">
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
							</div>

							<div class="col-sm-4 col-md-3 form-group">
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
							</div>

							<div class="col-sm-4 col-md-3 form-group">
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

								  $consulta = " select distinct s.cod_sede, s.nom_sede from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
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
							</div>

							<div class="col-sm-4 col-md-2 form-group">
								<label for="tipoDespacho">Tipo Alimento</label>
								<select class="form-control" name="tipoDespacho" id="tipoDespacho">
							  <option value="">Todos</option>
							  <?php
								$consulta = " select * from tipo_despacho where id != 4 order by Descripcion asc ";
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
							</div>

							<div class="col-sm-4 col-md-2 form-group">
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
							</div>
						</div>

						<div class="row">
							<div class="col-sm-2 form-group">
								<label for="paginasObservaciones">Páginas de observaciones</label>
								<input type="number" name="paginasObservaciones" id="paginasObservaciones" value="1" class="form-control">
							</div>

							<div class="col-sm-4   form-group">
								<label for="semana_final">Imprimir nombre del mes</label>
								<div>
									<input type="checkbox" name="imprimirMes" id="imprimirMes" checked>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-12 form-group">
								<label for="observaciones">Observaciones</label>
								<textarea class="form-control" name="observaciones" id="observaciones" cols="30" rows="5"></textarea>
							</div>
						</div>
												
						<div class="row">
							<div class="col-sm-3 form-group">
								<input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_GET['consultar']) && $_GET['consultar'] != '') {echo $_GET['consultar']; } ?>" >
								<button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong><i class="fa fa-search"></i> Buscar</strong></button>
							</div>
						</div>
					<?php endif ?>
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

								  		$consulta = " select distinct s.cod_sede, s.nom_sede from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
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
									$consulta = " select * from tipo_despacho where id != 4 order by Descripcion asc ";
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

							<div class="col-lg-3 col-md-6 col-sm-12 form-group">
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

							<div class="col-lg-3 col-md-6 col-sm-12 form-group">
								<label for="paginasObservaciones">Páginas de observaciones</label>
								<input type="number" name="paginasObservaciones" id="paginasObservaciones" value="1" class="form-control">
							</div> <!-- col -->
						</div> <!-- row -->	
						<div class="row">
							<div class="col-sm-12 form-group">
								<label for="semana_final">Imprimir nombre del mes</label>
								<div>
									<input type="checkbox" name="imprimirMes" id="imprimirMes" checked>
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
// exit(var_dump($_GET));
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
		?>



<?php
		  $consulta = " SELECT
		  s.cod_mun_sede,
		  s.cod_inst,
		  s.cod_sede,
		  de.Num_doc,
		  de.FechaHora_Elab,
		  de.Semana,
		  de.Dias,
		  de.Tipo_Complem,
		  de.tipodespacho,
		  td.Descripcion as tipodespacho_nm,
		  de.estado,
		  u.Ciudad,
		  b.NOMBRE AS bodegaOrigen,
		  s.nom_sede AS bodegaDestino
		  FROM
		  despachos_enc$tablaMes$tablaAnno de
		  LEFT JOIN
		  sedes$tablaAnno s ON s.cod_sede = de.cod_Sede
		  LEFT JOIN
		  ubicacion u ON u.codigoDANE = s.cod_mun_sede and u.ETC = 0
		  LEFT JOIN
		  productosmov$tablaMes$tablaAnno pm ON pm.Numero = de.Num_doc
		  AND pm.Documento = 'DES'
		  LEFT JOIN
		  bodegas b ON b.ID = pm.BodegaOrigen

		  LEFT JOIN tipo_despacho td ON td.Id = de.tipodespacho

		  where 1=1
		   ";

			if (isset($_GET["pb_semana"]) && $_GET["pb_semana"] == "") {
				if(isset($_GET["pb_diai"]) && $_GET["pb_diai"] != "" ){
					$diainicial = $_GET["pb_diai"];
					$consulta = $consulta." and DAYOFMONTH(de.FechaHora_Elab) >= ".$diainicial." ";
				}

				if(isset($_GET["pb_diaf"]) && $_GET["pb_diaf"] != "" ){
					$diafinal = $_GET["pb_diaf"];
					$consulta = $consulta." and DAYOFMONTH(de.FechaHora_Elab) <= ".$diafinal." ";
				}
			} else {
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

		  if(isset($_GET["pb_tipoDespacho"]) && $_GET["pb_tipoDespacho"] != "" ){
			$tipoDespacho = $_GET["pb_tipoDespacho"];
			$consulta = $consulta." and TipoDespacho = ".$tipoDespacho." ";
		  }

		  if(isset($_GET["pb_ruta"]) && $_GET["pb_ruta"] != "" ){
			$ruta = $_GET["pb_ruta"];
			$consulta = $consulta." and s.cod_sede in (select cod_sede from rutasedes where IDRUTA = $ruta)";
		  }

		  // var_dump($consulta);
		  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
?>















<hr>
				<!-- <div class="row">

				  <div class="col-xs-6 flexMid">
					<label for="seleccionarVarios">Seleccionar Todos</label>
					<input type="checkbox" class="i-checks" name="seleccionarVarios" id="seleccionarVarios">
				  </div>

					<div class="col-xs-6">

							<div class="pull-right dropdown">

								<button data-toggle="dropdown" class="dropdown-toggle btn-white" title="Generar Planilla">
									<i class="fa fa-file-pdf-o"></i>
								</button>
								<ul class="dropdown-menu m-t-xs">
									<li><a href="#" onclick="despachos_por_sede()">Individual</a></li>
									<li><a href="#" onclick="despachos_kardex()">Kardex</a></li>
									<li><a href="#" onclick="despachos_kardex2()">Kardex 2</a></li>
									<li><a href="#" onclick="despachos_mixta()">Mixta</a></li>
									<li><a href="#" onclick="despachos_consolidado()">Consolidado</a></li>
									<li><a href="#" onclick="despachos_agrupados()">Agrupado</a></li>
								</ul>

							   <div class="dropdown pull-right" id="">
								<button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button>
								<ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">
								  <li><a href="#" onclick="despachos_por_sede()">Individual</a></li>
								  <li><a href="#" onclick="despachos_kardex()">Kardex</a></li>
								  <li><a href="#" onclick="despachos_kardex_multiple()">Kardex Múltiple</a></li>
								  <li><a href="#" onclick="despachos_consolidado()">Consolidado</a></li>
								  <li><a href="#" onclick="despachos_agrupados()">Agrupado</a></li>
								  <?php if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){ ?>
									<li>
									  <a href="#" onclick="editar_despacho()">Editar Despacho</a>
									</li>
									<li>
									  <a href="#" onclick="despachos_por_sede_fecha_lote()">Ingresar Lotes y Fechas de vencimiento</a>
									</li>
									<li>
									  <a href="#" onclick="eliminar_despacho()">Eliminar Despacho</a>
									</li>
								  <?php } ?>
								</ul>
							  </div>


							</div>
					  </div>

				</div> -->








						<div class="table-responsive">

















							<table class="table table-striped table-bordered table-hover selectableRows" id="box-table-movimientos" >
								<thead>
				<tr>
				  <th class="text-center">
					  <label for="seleccionarVarios">Todos</label>
					<input type="checkbox" class="i-checks" name="seleccionarVarios" id="seleccionarVarios">
				  </th>
				  <th>Número</th>
				  <th>Fecha</th>
				  <th>Semana</th>
				  <th>Dias</th>
				  <th>Tipo Ración</th>
				  <th>Tipo Despacho</th>
				  <th> Municipio </th>
				  <th>Bodega Origen</th>
				  <th> Bodega Destino </th>
				  <th>Estado</th>
				</tr>
			  </thead>
			  <tbody>


				<?php if($resultado->num_rows >= 1){ while($row = $resultado->fetch_assoc()) { ?>
				  <tr>
					<td class="text-center">

						<input type="checkbox" class="i-checks despachos" value="<?php echo $row['Num_doc']; ?>" name="<?php echo $row['Num_doc']; ?>"id="<?php echo $row['Num_doc']; ?>"<?php if($row['estado'] == 0){echo " disabled "; } ?> semana="<?php echo $row['Semana']; ?>" complemento="<?php echo $row['Tipo_Complem'];?>" tipo="<?php echo $row['tipodespacho'];?>" sede="<?php echo $row['cod_sede'];?>" estado="<?php echo $row['estado'];?>"/>







					</td>






					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Num_doc']; ?></td>
					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['FechaHora_Elab']; ?></td>



					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" >
					  <?php echo $row['Semana']; ?>
					  <input class="soloJs" type="hidden" name="semana_<?php echo $row['Num_doc']; ?>" id="semana_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['Semana']; ?>">
					</td>

					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" >
					  <?php echo $row['Dias']; ?>
					</td>


					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" >
					  <?php echo $row['Tipo_Complem']; ?>
					  <input class="soloJs" type="hidden" name="tipo_<?php echo $row['Num_doc']; ?>" id="tipo_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['Tipo_Complem']; ?>">
					</td>

					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" >
					  <?php echo $row['tipodespacho_nm']; ?>
					  <input class="soloJs" type="hidden" name="tipodespacho_<?php echo $row['Num_doc']; ?>" id="tipodespacho_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['tipodespacho']; ?>">

					  <input class="soloJs" type="hidden" name="cod_sede_<?php echo $row['Num_doc']; ?>" id="cod_sede_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['cod_sede']; ?>">

					</td>


					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Ciudad']; ?></td>
					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['bodegaOrigen']; ?></td>
					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['bodegaDestino']; ?></td>

					<td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');">
					  <?php
					  $estado = $row['estado'];
					  switch ($estado) {
						case 0:
						echo "Eliminado";
						break;
						case 1:
						echo "Despachado";
						break;
						case 2:
						echo "Pendiente";
						break;
						default:
						echo $estado;
						break;
					  }
					  ?>


					  <input class="soloJs" type="hidden" name="estado_<?php echo $row['Num_doc']; ?>" id="estado_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['estado']; ?>">
					</td>

				  </tr>
				  <?php } } ?>



				</tbody>

				<tfoot>
						  <tr>
				  <th></th>
				  <th>Número</th>
				  <th>Fecha</th>
				  <th>Semana</th>
				  <th>Dias</th>
				  <th>Tipo Ración</th>
				  <th>Tipo Despacho</th>
				  <th> Municipio </th>
				  <th>Bodega Origen</th>
				  <th> Bodega Destino </th>
				  <th>Estado</th>
				</tr>
				</tfoot>
							</table>
						</div>






















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

<?php include '../../footer.php'; ?>

	<!-- Mainly scripts -->
	<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

	<!-- Custom and plugin javascript -->
	<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>


	<script src="<?php echo $baseUrl; ?>/modules/despachos/js/despachos.js?v=20200423"></script>
	<script>
		$(document).ready(function(){
			<?php if ($_SESSION['perfil'] == "0" || $permisos['despachos'] == "1" || $permisos['despachos'] == "2"): ?>
				var botonAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">';
					botonAcciones += '<li><a href="#" onclick="despachos_por_sede()">Individual</a></li>';
					<?php if ($_SESSION['perfil'] != '6' && $_SESSION['perfil'] != '7'): ?>
						botonAcciones += '<li><a href="#" onclick="despachos_por_sede_vertical()">Individual Vertical</a></li>';
						botonAcciones += '<li><a href="#" onclick="despachos_kardex()">Kardex</a></li>';
					<?php endif ?>
					botonAcciones += '<li><a href="#" onclick="despachos_kardex_multiple()">Kardex Múltiple</a></li>';
					<?php if ($_SESSION['perfil'] != '6' && $_SESSION['perfil'] != '7'): ?>
						botonAcciones += '<li><a href="#" onclick="despachos_consolidado()">Consolidado</a></li>';
					<?php endif ?>
					botonAcciones += '<li><a href="#" onclick="despachos_consolidado_x_sede()">Consolidado x Sedes</a></li>';
					<?php if ($_SESSION['perfil'] != '6' && $_SESSION['perfil'] != '7'): ?>
						botonAcciones += '<li><a href="#" onclick="despachos_consolidado_vertical()">Consolidado Vertical</a></li>';
					<?php endif ?>
			
					// Menu para COVID
					botonAcciones += '<li><a href="#" onclick="covid19_despachos_consolidado_ri()">Entrega Raciones COVID-19 RI</a></li>';
					botonAcciones += '<li><a href="#" onclick="covid19_despachos_consolidado()">Entrega Raciones COVID-19</a></li>';
					<?php if ($_SESSION['perfil'] != '6' && $_SESSION['perfil'] != '7'): ?>
						botonAcciones += '<li><a href="#" onclick="despachos_agrupados()">Agrupado</a></li>';
					<?php endif ?>
					<?php if($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"){ ?>
						botonAcciones += '<li><a href="#" onclick="editar_despacho()">Editar Despacho</a></li>';
						botonAcciones += '<li><a href="#" onclick="despachos_por_sede_fecha_lote()">Ingresar Lotes y Fechas de vencimiento</a></li>';
						botonAcciones += '<li><a href="#" onclick="eliminar_despacho()">Eliminar Despacho</a></li>';
					<?php } ?>
					botonAcciones += '</ul></div>';

				$('.containerBtn').html(botonAcciones);
			<?php endif ?>
			

		});
	</script>


	<!-- Page-Level Scripts -->


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
