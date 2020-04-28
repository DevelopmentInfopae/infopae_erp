<?php
  include '../../header.php';
  set_time_limit (0);
  ini_set('memory_limit','6000M');
  $periodoActual = $_SESSION['periodoActual'];
  require_once '../../db/conexion.php';
  $Link = new mysqli($Hostname, $Username, $Password, $Database);
  if ($Link->connect_errno) {
	  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }
  $Link->set_charset("utf8");

  $con_cod_muni = "SELECT CodMunicipio FROM parametros;";
  $res_minicipio = $Link->query($con_cod_muni) or die(mysqli_error($Link));
  if ($res_minicipio->num_rows > 0) {
	$codigoDANE = $res_minicipio->fetch_array();
  }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
			<div class="col-lg-8">
				<h2>Certificados por institución COVID19</h2>
				<ol class="breadcrumb">
					<li>
						<a href="<?php echo $baseUrl; ?>">Inicio</a>
					</li>
					<li class="active">
						<strong>Certificados por institución COVID19</strong>
					</li>
				</ol>
			</div>
			<div class="col-lg-4">
				<div class="title-action">
				   <!--
					<a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
					<a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
				  -->
					<!-- <a href="<?php echo $baseUrl; ?>/modules/despachos/despacho_nuevo.php" target="_self" class="btn btn-primary"><i class="fa fa-truck"></i> Nuevo despacho </a> -->
				</div>
			</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
	<div class="col-lg-12">
	  <div class="ibox float-e-margins">
		<div class="ibox-content contentBackground">
		  <h2>Parámetros de Consulta</h2>
		  <form class="col-lg-12" action="certificados_rector_covid19.php" name="formPlanillas" id="formPlanillas" method="post" target="_blank">
			<div class="row">
				<div class="col-sm-4 form-group">
					<label for="fechaInicial">Municipio</label>
					<select class="form-control" name="municipio" id="municipio" required>
						<option value="">Seleccione uno</option>
						<?php
						$consulta = "SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE ETC = 0 ";

						$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
						if($DepartamentoOperador != ''){
							$consulta = $consulta." and CodigoDANE like '$DepartamentoOperador%' ";
						}
						$consulta = $consulta." order by ciudad asc ";
						//echo $consulta;
						$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
						if($resultado->num_rows >= 1){
							while($row = $resultado->fetch_assoc()) { ?>
								<option value="<?php echo $row["codigoDANE"]; ?>"  <?php  if((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] == $row["codigoDANE"]) || ($codigoDANE["CodMunicipio"] == $row["codigoDANE"])){ echo " selected "; } ?> ><?php echo $row["ciudad"]; ?></option>
								<?php
							}// Termina el while
						}//Termina el if que valida que si existan resultados
						?>
					</select>
					<input type="hidden" name="municipioNm" id="municipioNm">
				</div><!-- /.col -->
				<div class="col-sm-4 form-group">
					<label for="institucion">Institución</label>
					<select class="form-control" name="institucion" id="institucion">
						<option value="">Todas</option>
						<?php
						if(isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" || $codigoDANE["CodMunicipio"]){
							$municipio = $_GET["pb_municipio"] = $codigoDANE["CodMunicipio"];
							$consulta = " select distinct s.cod_inst, s.nom_inst from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
							$consulta = $consulta." and s.cod_mun_sede = '$municipio' ";
							$consulta = $consulta." order by s.nom_inst asc ";
							$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
							if($resultado->num_rows >= 1){
								while($row = $resultado->fetch_assoc()) { ?>
									<option value="<?php echo $row['cod_inst']; ?>" <?php if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?php echo $row['nom_inst']; ?></option>
								<?php }// Termina el while
							}//Termina el if que valida que si existan resultados
						}
						?>
					</select>
				</div><!-- /.col -->

			</div><!-- /.row -->
			<div class="row">
			  <div class="col-sm-4 form-group">
				<label for="fechaInicial">Mes</label>
				<?php
				// if(!isset($_GET['pb_mes']) || $_GET['pb_mes'] == ''){
				//   $_GET['pb_mes'] = date("n");
				// }
				?>
				<select name="mes" id="mes" class="form-control" required>
				  <option value="">Seleccione uno</option>
				  <option value="1" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 1) {echo " selected "; } ?>>Enero</option>
				  <option value="2" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 2) {echo " selected "; } ?>>Febrero</option>
				  <option value="3" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 3) {echo " selected "; } ?>>Marzo</option>
				  <option value="4" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 4) {echo " selected "; } ?>>Abril</option>
				  <option value="5" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 5) {echo " selected "; } ?>>Mayo</option>
				  <option value="6" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 6) {echo " selected "; } ?>>Junio</option>
				  <option value="7" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 7) {echo " selected "; } ?>>Julio</option>
				  <option value="8" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 8) {echo " selected "; } ?>>Agosto</option>
				  <option value="9" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 9) {echo " selected "; } ?>>Septiembre</option>
				  <option value="10" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 10) {echo " selected "; } ?>>Octubre</option>
				  <option value="11" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 11) {echo " selected "; } ?>>Noviembre</option>
				  <option value="12" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 12) {echo " selected "; } ?>>Diciembre</option>
				</select>
				<input type="hidden" name="mesConsulta" id="mesConsulta" value="<?php if (isset($_GET['pb_mes'])) { echo $_GET['pb_mes']; } ?>">
			  </div><!-- /col -->

			  <div class="col-sm-4 form-group">
				<label for="semana_inicial">Semana Inicial</label>
				<select class="form-control" name="semana_inicial" id="semana_inicial" required>
				  <option value="">Seleccione uno</option>
				</select>
				<input type="hidden" name="diaInicialSemanaInicial" id="diaInicialSemanaInicial">
				<input type="hidden" name="diaFinalSemanaInicial" id="diaFinalSemanaInicial">
			  </div>

			  <div class="col-sm-4   form-group">
				<label for="semana_final">Semana Final</label>
				<select class="form-control" name="semana_final" id="semana_final" required>
				  <option value="">Seleccione uno</option>
				</select>
				<input type="hidden" name="diaInicialSemanaFinal" id="diaInicialSemanaFinal">
				<input type="hidden" name="diaFinalSemanaFinal" id="diaFinalSemanaFinal">
			  </div>
			</div><!-- /.row -->

			<div class="row">
			  <div class="col-sm-12">
				<h3>Tipo de certificado</h3>
			  </div>
			</div><!-- /.row -->

		
			 



<!-- <div class="i-checks"><label> <input type="radio" value="1" name="tipoPlanilla" required><i></i> Certificado Normal </label> </div> -->

<!-- <div class="col-sm-2 form-group"> <div class="i-checks"> <label> <input type="radio" value="2" name="tipoPlanilla" required><i></i> Certificado por días </label> </div> </div>  -->
				
				
				
				
				
				
	

			<div class="row">
				<div class="col-sm-3 form-group">
					 <input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_GET['consultar']) && $_GET['consultar'] != '') {echo $_GET['consultar']; } ?>" >
					 <button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong>Buscar</strong></button>
				</div>
			</div>

<?php
	//var_dump($_GET);
	$tablaMes = '';
	if(isset($_GET["pb_btnBuscar"]) && $_GET["pb_btnBuscar"] == 1){
	  if(isset($_GET["pb_mes"]) && $_GET["pb_mes"] != "" ){


		// Ajustado formato del mes inicial para hacer el llamado de la tabla con los registros para ese més.
		$mesnicial = $_GET["pb_mes"];

		if($mesnicial < 10){
		  $tablaMes = '0'.$mesnicial;
		}else{
		  $tablaMes = $mesnicial;
		}
	  }
	  $bandera = 0;
	  if($tablaMes == ''){
		$bandera++;
		echo "<br> <h3>Debe seleccionar el mes inicial.</h3> ";
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

		  if(isset($_GET["pb_diai"]) && $_GET["pb_diai"] != "" ){
			$diainicial = $_GET["pb_diai"];
			$consulta = $consulta." and DAYOFMONTH(de.FechaHora_Elab) >= ".$diainicial." ";
		  }

		  if(isset($_GET["pb_diaf"]) && $_GET["pb_diaf"] != "" ){
			$diafinal = $_GET["pb_diaf"];
			$consulta = $consulta." and DAYOFMONTH(de.FechaHora_Elab) <= ".$diafinal." ";
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






		  if(isset($_GET["pb_tipoDespacho"]) && $_GET["pb_tipoDespacho"] != "" ){
			$tipoDespacho = $_GET["pb_tipoDespacho"];
			$consulta = $consulta." and TipoDespacho = ".$tipoDespacho." ";
		  }

		  if(isset($_GET["pb_ruta"]) && $_GET["pb_ruta"] != "" ){
			$ruta = $_GET["pb_ruta"];
			$consulta = $consulta." and s.cod_sede in (select cod_sede from rutasedes where IDRUTA = $ruta)";
		  }







		  //Impromir la consulta que filtra los despachos
		  //echo "<br>$consulta<br><br><br>";

		  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));


		  //var_dump($resultado);


		  ?>















<hr>




				<div class="row">

				  <div class="col-xs-6 flexMid">
					<label for="seleccionarVarios">Seleccionar Todos</label>
					<input type="checkbox" name="seleccionarVarios" id="seleccionarVarios">
				  </div>

					<div class="col-xs-6">

							<div class="pull-right social-action dropdown">



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
								<button class="btn-white" title="Editar Despacho" onclick="editar_despacho()" type="button">
									<i class="fa fa-pencil"></i>
								</button>
								<button class="btn-white" title="Ingresar Lotes y Fechas de vencimiento" onclick="despachos_por_sede_fecha_lote()" type="button">
									<i class="fa fa-clock-o"></i>
								</button>
								<button class="btn-white" title="Eliminar Despacho" onclick="eliminar_despacho()" type="button">
									<i class="fa fa-trash"></i>
								</button>
							</div>
					  </div>

				</div>








						<div class="table-responsive">

















							<table class="table table-striped table-bordered table-hover selectableRows" id="box-table-movimientos" >
								<thead>
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
			  </thead>
			  <tbody>


				<?php if($resultado->num_rows >= 1){ while($row = $resultado->fetch_assoc()) { ?>
				  <tr>
					<td>

					  <input type="checkbox" class="despachos" value="<?php echo $row['Num_doc']; ?>" name="<?php echo $row['Num_doc']; ?>"id="<?php echo $row['Num_doc']; ?>"<?php if($row['estado'] == 0){echo " disabled "; } ?> />

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
		<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

		<!-- iCheck -->
		<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
				<script>
						$(document).ready(function () {
								$('.i-checks').iCheck({
										checkboxClass: 'icheckbox_square-green',
										radioClass: 'iradio_square-green',
								});
						});
				</script>




	<script src="<?php echo $baseUrl; ?>/modules/impresion_planillas/js/certificados.js"></script>



	<!-- Page-Level Scripts -->


<?php mysqli_close($Link); ?>

<form action="despacho_por_sede.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede">
  <input type="hidden" name="despachoAnnoI" id="despachoAnnoI" value="">
  <input type="hidden" name="despachoMesI" id="despachoMesI" value="">
  <input type="hidden" name="despacho" id="despacho" value="">
</form>

<form action="despachos.php" id="parametrosBusqueda" method="get">
  <input type="hidden" id="pb_annoi" name="pb_annoi" value="">
  <input type="hidden" id="pb_mes" name="pb_mes" value="">
  <input type="hidden" id="pb_diai" name="pb_diai" value="">
  <input type="hidden" id="pb_annof" name="pb_annof" value="">
  <input type="hidden" id="pb_mesf" name="pb_mesf" value="">
  <input type="hidden" id="pb_diaf" name="pb_diaf" value="">
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
