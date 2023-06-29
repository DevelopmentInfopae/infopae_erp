<?php
  include '../../header.php';

  if ($permisos['orden_compra'] == "0") {
?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

else {
    ?><script type="text/javascript">
      const list = document.querySelector(".li_orden_compra");
      list.className += " active ";
    </script>
  <?php
  }
  	set_time_limit (0);
  	ini_set('memory_limit','6000M');
  	$periodoActual = $_SESSION['periodoActual'];
  	require_once '../../db/conexion.php';

  	$arrayDetalle = [ 'lunes_1' => 'D1',  'martes_1' => 'D2',  'miércoles_1' => 'D3',  'jueves_1' => 'D4',  'viernes_1' => 'D5',
  							'lunes_2' => 'D6',  'martes_2' => 'D7',  'miércoles_2' => 'D8',  'jueves_2' => 'D9',  'viernes_2' => 'D10',
  							'lunes_3' => 'D11', 'martes_3' => 'D12', 'miércoles_3' => 'D13', 'jueves_3' => 'D14', 'viernes_3' => 'D15',
  							'lunes_4' => 'D16', 'martes_4' => 'D17', 'miércoles_4' => 'D18', 'jueves_4' => 'D19', 'viernes_4' => 'D20',
  							'lunes_5' => 'D21', 'martes_5' => 'D22', 'miércoles_5' => 'D23', 'jueves_5' => 'D24', 'viernes_5' => 'D25'
  ];

  $nameLabel = get_titles('ordenCompra', 'ordenCompra', $labels);
?>

<style type="text/css">
   .select2-container--open {
      z-index: 9999999
  }
</style>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-md-6 col-lg-8">
		<h2><?= $nameLabel; ?></h2>
		<ol class="breadcrumb">
		  	<li>
				<a href="<?php echo $baseUrl; ?>">Inicio</a>
		  	</li>
		  	<li class="active">
				<strong><?= $nameLabel ?></strong>
		  	</li>
		</ol>
	</div>
	<div class="col-md-6 col-lg-4">
		<div class="title-action">
			<?php if($_SESSION['perfil'] == "0" || $permisos['orden_compra'] == "2"){ ?>
				<a href="<?= $baseUrl; ?>/modules/ordenes_de_compra/orden_de_compra_nueva.php" target="_self" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a>
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
					<form class="col-lg-12" action="ordenes_de_compra.php" name="formDespachos" id="formDespachos" method="post" target="_blank">
						<div class="row">

 							<div class=" col-sm-6 col-md-3 form-group">
								<label for="fechaInicial">Fecha Inicial</label>
								<div class="row compositeDate">
									<div class="col-sm-4 col-md-4 nopadding">
										<select name="annoi" id="annoi" class="form-control annoInicial">
											<option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
										</select>
									</div>

									<div class="col-sm-4 col-md-5 nopadding">
										<?php if(!isset($_GET['pb_mesi']) || $_GET['pb_mesi'] == ''){ $_GET['pb_mesi'] = date("n"); } ?>
										<select name="mesi" id="mesi" onchange="mesFinal();" class="form-control mesInicial">
											<!-- <option value="">mm</option> -->
											<option value="1" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 1) ? " selected " : "" ?> > Enero</option>
											<option value="2" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 2) ? " selected " : "" ?> > Febrero</option>
											<option value="3" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 3) ? " selected " : "" ?> > Marzo</option>
											<option value="4" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 4) ? " selected " : "" ?> > Abril</option>
											<option value="5" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 5) ? " selected " : "" ?> > Mayo</option>
											<option value="6" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 6) ? " selected " : "" ?> > Junio</option>
											<option value="7" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 7) ? " selected " : "" ?> > Julio</option>
											<option value="8" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 8) ? " selected " : "" ?> > Agosto</option>
											<option value="9" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 9) ? " selected " : "" ?> > Septiembre</option>
											<option value="10" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 10) ? " selected " : "" ?> > Octubre</option>
											<option value="11" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 11) ? " selected " : "" ?> > Noviembre</option>
											<option value="12" <?= (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 12) ? " selected " : "" ?> > Diciembre</option>
										</select>
										<input type="hidden" name="mesiConsulta" id="mesiConsulta" value="<?php if (isset($_GET['pb_mesi'])) { echo $_GET['pb_mesi']; } ?>">
									</div>

									<div class="col-sm-4 col-md-3 nopadding">
										<select name="diai" id="diai" class="form-control diaInicial">
											<option value="0">dd</option>
											<option value="1" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 1) ? " selected " : "" ?> > 01 </option>
											<option value="2" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 2) ? " selected " : "" ?> > 02 </option>
											<option value="3" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 3) ? " selected " : "" ?> > 03 </option>
											<option value="4" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 4) ? " selected " : "" ?> > 04 </option>
											<option value="5" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 5) ? " selected " : "" ?> > 05 </option>
											<option value="6" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 6) ? " selected " : "" ?> > 06 </option>
											<option value="7" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 7) ? " selected " : "" ?> > 07 </option>
											<option value="8" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 8) ? " selected " : "" ?> > 08 </option>
											<option value="9" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 9) ? " selected " : "" ?> > 09 </option>
											<option value="10" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 10) ? " selected " : "" ?> > 10 </option>
											<option value="11" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 11) ? " selected " : "" ?> > 11 </option>
											<option value="12" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 12) ? " selected " : "" ?> > 12 </option>
											<option value="13" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 13) ? " selected " : "" ?> > 13 </option>
											<option value="14" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 14) ? " selected " : "" ?> > 14 </option>
											<option value="15" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 15) ? " selected " : "" ?> > 15 </option>
											<option value="16" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 16) ? " selected " : "" ?> > 16 </option>
											<option value="17" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 17) ? " selected " : "" ?> > 17 </option>
											<option value="18" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 18) ? " selected " : "" ?> > 18 </option>
											<option value="19" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 19) ? " selected " : "" ?> > 19 </option>
											<option value="20" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 20) ? " selected " : "" ?> > 20 </option>
											<option value="21" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 21) ? " selected " : "" ?> > 21 </option>
											<option value="22" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 22) ? " selected " : "" ?> > 22 </option>
											<option value="23" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 23) ? " selected " : "" ?> > 23 </option>
											<option value="24" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 24) ? " selected " : "" ?> > 24 </option>
											<option value="25" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 25) ? " selected " : "" ?> > 25 </option>
											<option value="26" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 26) ? " selected " : "" ?> > 26 </option>
											<option value="27" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 27) ? " selected " : "" ?> > 27 </option>
											<option value="28" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 28) ? " selected " : "" ?> > 28 </option>
											<option value="29" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 29) ? " selected " : "" ?> > 29 </option>
											<option value="30" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 30) ? " selected " : "" ?> > 30 </option>
											<option value="31" <?= (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 31) ? " selected " : "" ?> > 31 </option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-sm-12 col-md-3 form-group">
								<label for="fechaInicial">Fecha Final</label>
								<div class="row compositeDate">
									<div class="col-sm-6 col-md-4 nopadding">
										<select name="annof" id="annof" class="form-control annoFinal">
											<option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
									  </select>
									</div>
									<div class="col-sm-5 nopadding">
									<select name="mesfText" id="mesfText" class="form-control select2 mesfText">
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
									<div class="col-sm-6 col-md-3 nopadding">
										<select name="diaf" id="diaf" class="form-control diaFinal">
											<option value="0">dd</option>
											<option value="1" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 1) ? " selected " : "" ?> > 01 </option>
											<option value="2" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 2) ? " selected " : "" ?> > 02 </option>
											<option value="3" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 3) ? " selected " : "" ?> > 03 </option>
											<option value="4" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 4) ? " selected " : "" ?> > 04 </option>
											<option value="5" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 5) ? " selected " : "" ?> > 05 </option>
											<option value="6" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 6) ? " selected " : "" ?> > 06 </option>
											<option value="7" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 7) ? " selected " : "" ?> > 07 </option>
											<option value="8" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 8) ? " selected " : "" ?> > 08 </option>
											<option value="9" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 9) ? " selected " : "" ?> > 09 </option>
											<option value="10" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 10) ? " selected " : "" ?> > 10 </option>
											<option value="11" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 11) ? " selected " : "" ?> > 11 </option>
											<option value="12" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 12) ? " selected " : "" ?> > 12 </option>
											<option value="13" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 13) ? " selected " : "" ?> > 13 </option>
											<option value="14" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 14) ? " selected " : "" ?> > 14 </option>
											<option value="15" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 15) ? " selected " : "" ?> > 15 </option>
											<option value="16" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 16) ? " selected " : "" ?> > 16 </option>
											<option value="17" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 17) ? " selected " : "" ?> > 17 </option>
											<option value="18" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 18) ? " selected " : "" ?> > 18 </option>
											<option value="19" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 19) ? " selected " : "" ?> > 19 </option>
											<option value="20" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 20) ? " selected " : "" ?> > 20 </option>
											<option value="21" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 21) ? " selected " : "" ?> > 21 </option>
											<option value="22" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 22) ? " selected " : "" ?> > 22 </option>
											<option value="23" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 23) ? " selected " : "" ?> > 23 </option>
											<option value="24" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 24) ? " selected " : "" ?> > 24 </option>
											<option value="25" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 25) ? " selected " : "" ?> > 25 </option>
											<option value="26" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 26) ? " selected " : "" ?> > 26 </option>
											<option value="27" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 27) ? " selected " : "" ?> > 27 </option>
											<option value="28" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 28) ? " selected " : "" ?> > 28 </option>
											<option value="29" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 29) ? " selected " : "" ?> > 29 </option>
											<option value="30" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 30) ? " selected " : "" ?> > 30 </option>
											<option value="31" <?= (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 31) ? " selected " : "" ?> > 31 </option>
										</select>
									</div>
								</div>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="semana">Semana</label>
								<select name="semana" id="semana" class="form-control semana">
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
								<select class="form-control tipoComplemento" name="tipo" id="tipo">
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
						</div>

						<div class="row">
							<div class="col-sm-6 col-md-3 form-group">
								<label for="municipio">Municipio</label>
								<select class="form-control municipio" name="municipio" id="municipio">
								  	<option value="">Seleccione uno</option>
								  	<?php
										$consulta = " SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE 1=1 and ETC = 0 ";
										$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
										if($DepartamentoOperador != ''){
									  		$consulta = $consulta." and CodigoDANE like '$DepartamentoOperador%' ";
										}
										$consulta = $consulta." order by ciudad asc ";
										$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
										if($resultado->num_rows >= 1){
									  		while($row = $resultado->fetch_assoc()) {
								  	?>
									  			<option value="<?= $row["codigoDANE"]; ?>" <?php if((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] == $row["codigoDANE"]) || ($municipio_defecto["CodMunicipio"] == $row["codigoDANE"])){ echo " selected "; }?>><?= $row["ciudad"]; ?></option>
								  	<?php
									  		}// Termina el while
										}//Termina el if que valida que si existan resultados
								  	?>
								</select>
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="institucion">Institución</label>
								<select class="form-control institucion" name="institucion" id="institucion">
								  	<option value="">Todas</option>
								  	<?php
										if ((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" ) || $municipio_defecto["CodMunicipio"] != "") {
									  		$municipio = (isset($_GET["pb_municipio"])) ? $_GET["pb_municipio"] : $municipio_defecto["CodMunicipio"];
									  		$consulta = "SELECT DISTINCT s.cod_inst, s.nom_inst FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc ON s.cod_sede = sc.cod_sede WHERE 1=1";
									  		$consulta = $consulta." AND s.cod_mun_sede = '$municipio'";
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
							</div>

							<div class="col-sm-6 col-md-3 form-group">
								<label for="sede">sede</label>
								<select class="form-control sede" name="sede" id="sede">
								  	<option value="">Todas</option>
								  	<?php
										$institucion = '';
										if( isset($_GET['pb_institucion']) && $_GET['pb_institucion'] != '' ){
									  		$institucion = $_GET['pb_institucion'];
									  		$consulta = " select distinct s.cod_sede, s.nom_sede from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
									  		$consulta = $consulta."  and s.cod_inst = '$institucion' ";
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

							<div class="col-sm-6 col-md-3 form-group">
								<label for="tipoDespacho">Tipo Alimento</label>
								<select class="form-control alimento" name="tipoDespacho" id="tipoDespacho">
								  	<!-- <option value="">Todos</option> -->
								  	<?php
										$consulta = " SELECT * FROM tipo_despacho WHERE id != 4 ORDER BY Id DESC ";
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

							<div class="col-sm-6 col-md-3 form-group">
								<label for="ruta">Ruta</label>
								<select class="form-control ruta" name="ruta" id="ruta">
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
							<div class="col-sm-4   form-group">

							</div>
						</div>

						<div class="row">
						  	<div class="col-sm-3 form-group">
								<input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_GET['consultar']) && $_GET['consultar'] != '') {echo $_GET['consultar']; } ?>" >
								<button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong><i class="fa fa-search"></i> Buscar</strong></button>
						  	</div>
						</div>

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
	  							$bandera = 0;
	  							if($tablaMes == ''){
									$bandera++;
									echo "<br> <h3>Debe seleccionar el mes inicial.</h3> ";
	  							}else{
									$tablaAnno = $_SESSION['periodoActual'];
		  							$consulta = " show tables like 'orden_compra_det$tablaMes$tablaAnno' ";
		  							$result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
		  							$existe = $result->num_rows;
		  							if($existe <= 0){
										$bandera++;
									echo "<br> <h3>No se encontraron registros para este periodo.</h3> ";
		  							}
	  							}
	  							if($bandera == 0){
	  								$concatenada = '';
									$consulta = " SELECT de.Num_OCO, 
																de.FechaHora_Elab, 
																de.Semana, 
																de.Dias, 
																de.Menus, 
																de.rutaMunicipio,
																de.Tipo_Complem, 
																vm.descripcion AS descVariacion,
																(SELECT Descripcion FROM tipo_despacho WHERE id = de.tipodespacho) AS tipodespacho_nm, 
																de.estado,
																p.Nitcc AS Nitcc,
																p.Nombrecomercial AS Nombrecomercial,
																de.bodega AS bodegaId,
																(SELECT NOMBRE FROM bodegas WHERE ID = de.bodega LIMIT 1) AS bodega
															FROM orden_compra_enc$tablaMes$tablaAnno de 
															LEFT JOIN sedes$tablaAnno s ON s.cod_sede = de.cod_Sede 
															LEFT JOIN ubicacion u ON u.codigoDANE = s.cod_mun_sede AND u.ETC = 0 
															LEFT JOIN variacion_menu vm on vm.id = de.cod_variacion_menu
															LEFT JOIN proveedores p on p.Nitcc = de.proveedor ";

									if ( (isset($_GET['pb_diai']) && $_GET['pb_diai'] > 0) || (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] > 0) ) {
										$consulta .= " INNER JOIN orden_compra_det$tablaMes$tablaAnno det ON de.Num_Doc = det.Num_Doc WHERE 1 = 1 ";
									}else{
										$consulta .= " WHERE 1 = 1 ";
									}						

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
											$consulta .= " AND det.$columna > 0 ";
										}else{
											echo "<br> <h3>No se encontraron registros para este periodo.</h3> ";
											exit();
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
											$consulta .= " OR det.$columna > 0 ";
										}else {
											echo "<br> <h3>No se encontraron registros para este periodo.</h3> ";
											exit();
										}
									}

								 	if (isset($_GET["pb_semana"]) && $_GET["pb_semana"] != ""){
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

				  					if(isset($_GET["pb_tipoDespacho"]) && $_GET["pb_tipoDespacho"] != "99" ){
										$tipoDespacho = $_GET["pb_tipoDespacho"];
										$consulta = $consulta." and TipoDespacho = ".$tipoDespacho." ";
				  					}

				  					if(isset($_GET["pb_ruta"]) && $_GET["pb_ruta"] != "" ){
										$ruta = $_GET["pb_ruta"];
										$consulta = $consulta." and s.cod_sede in (select cod_sede from rutasedes where IDRUTA = $ruta)";
				  					}
				  					
				 	 				$consulta = $consulta." GROUP BY(de.Num_OCO) ";
				 	 				// exit(var_dump($consulta));
				 	 				if(isset($_GET["pb_diai"]) && $_GET["pb_diai"] > 0 ){
										$consulta = $consulta." ORDER BY Dias ";
									}
				  					// $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
						?>
						<input type="hidden" name="consulta" id="consulta" value="<?php echo $consulta; ?>">
				</div><!-- /.ibox-content -->
	  		</div><!-- /.ibox float-e-margins -->
		</div><!-- /.col-lg-12 -->
  	</div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<!-- form -->		
						<div class="table-responsive">
							<table class="table table-striped table-bordered table-hover selectableRows" id="box-table-movimientos" >
								<thead>
									<tr style="height: 4em;">
										<th class="text-center">
											<label for="seleccionarVarios"></label>
											<input type="checkbox" class="i-checks" name="seleccionarVarios" id="seleccionarVarios">
										</th>
										<th class="text-center">Número</th>
										<th class="text-center">Fecha</th>
										<th class="text-center">Semana</th>
										<th class="text-center">Días</th>
										<th class="text-center">Menús</th>
										<th class="text-center">Ruta o Municipio</th>
										<th class="text-center">Tipo Ración</th>
										<th class="text-center">Variación</th>
										<th class="text-center">Tipo Alimento</th>
										<th class="text-center">Bodega</th>
										<th class="text-center">Estado</th>
										<th class="text-center">Documento Proveedor</th>
										<th class="text-center">Nombre Proveedor</th>
									</tr>
								</thead>
								<br>
								<tbody id="tbodyOrdenes">
									<tr></tr>
								</tbody>
								<tfoot>
								  	<tr style="height: 4em;">
						  				<th></th>
										<th>Número</th>
										<th>Fecha</th>
										<th>Semana</th>
										<th>Días</th>
										<th>Menús</th>
										<th>Ruta o Municipio</th>
										<th>Tipo Ración</th>
										<th>Variación</th>
										<th>Tipo Alimento</th>
										<th>Bodega</th>
										<th>Estado</th>
										<th>Documento Proveedor</th>
										<th>Nombre Proveedor</th>
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

<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  	<div class="modal-dialog modal-sm">
    	<div class="modal-content">
      		<div class="modal-header text-info" style="padding: 15px;">
        		<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        		<h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
      		</div>
      		<div class="modal-body">
				<?php if($_SESSION['p_inventory'] != 0): ?>
					<p class="text-center">¿Esta seguro de eliminar la orden?, si la orden esta recibida, <strong> afectara el inventario de la bodega </strong></p>
				<?php else: ?>
					<p class="text-center"><strong> ¿Esta seguro de eliminar la orden? </strong></p>
				<?php endif; ?>		
      		</div>
      		<div class="modal-footer">
        		<input type="hidden" id="anno_eliminar">
        		<input type="hidden" id="mes_eliminar">
        		<input type="hidden" id="num_oco_eliminar">
        		<input type="hidden" id="estado_eliminar">
        		<button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal ">Cancelar</button>
        		<button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="deleteOrden();">Aceptar</button>
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
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/modules/ordenes_de_compra/js/ordenes_de_compra.js"></script>
<script>
	$(document).ready(function(){
		<?php if ($_SESSION['perfil'] == "0" || $permisos['orden_compra'] == "1" || $permisos['orden_compra'] == "2"): ?>
			var botonAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">';			
			botonAcciones += '<li> <a href="#" onclick="despachoPorSede()"> <i class="fa fa-file-pdf-o fa-lg"></i> &nbsp Individual</a> </li>';
			botonAcciones += '<li> <a href="#" onclick="ordenesConsolidado()"> <i class="fa fa-file-pdf-o fa-lg"></i> &nbsp Consolidado</a> </li>';
			botonAcciones += '<li class="divider"></li>';
			<?php if ($_SESSION['perfil'] == "0" || $permisos['orden_compra'] == "2"): ?>
				botonAcciones += '<li> <a href="#" onclick="editar_orden()"> <i class="fas fa-pencil-alt fa-lg"></i> &nbsp Editar Orden</a> </li>';
				botonAcciones += '<li> <a href="#" onclick="eliminar_orden()"> <i class="fa fa-trash fa-lg"></i>&nbsp Eliminar Orden</a> </li>';
				<?php if ($_SESSION['p_inventory'] != "0" && $permisos['orden_compra'] == "2"): ?>
				botonAcciones += '<li class="divider"></li>';
				botonAcciones += '<li> <a href="#" onclick="recibir_orden()"> <i class="fa fa-download fa-lg"></i>&nbsp Recibir Orden</a> </li>';
				<?php endif ?>
			<?php endif ?>
			botonAcciones += '</ul></div>';
			$('.containerBtn').html(botonAcciones);
			<?php endif ?>
		});
</script>

<?php mysqli_close($Link); ?>

<form action="formato_orden_compra.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede" target="_blank">
  <input type="hidden" name="AnnoI" id="AnnoI" value="">
  <input type="hidden" name="MesI" id="MesI" value="">
  <input type="hidden" name="imprimirMesI" id="imprimirMesI" value="">
  <input type="hidden" name="ordenCompra" id="ordenCompra" value="">
</form>


<form action="formato_orden_compra_consolidado.php" method="post" name="formOrdenesConsolidado" id="formOrdenesConsolidado" target="_blank">
  <input type="hidden" name="AnnoIC" id="AnnoIC" value="">
  <input type="hidden" name="MesIC" id="MesIC" value="">
  <input type="hidden" name="imprimirMesIC" id="imprimirMesIC" value="">
  <input type="hidden" name="ordenesCompra" id="ordenesCompra" value="">
</form>

<form action="orden_de_compra_editar.php" method="post"  id="ordenCompraEditar" target="_self">
  <input type="hidden" name="Num_oco" id="Num_oco" value="">
  <input type="hidden" name="mesi" id="mesi" value="">
</form>

<form action="ordenes_de_compra.php" id="parametrosBusqueda" method="get">
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
