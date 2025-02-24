<?php
include '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];

$nomSede = (isset($_POST['nomSede'])) ? mysqli_real_escape_string($Link, $_POST['nomSede']) : '';
$codSede = (isset($_POST['codSede'])) ? mysqli_real_escape_string($Link, $_POST['codSede']) : '';
$nomInst = (isset($_POST['nomInst'])) ? mysqli_real_escape_string($Link, $_POST['nomInst']) : '';

$consulta = "SELECT s.*, u.nombre as coordinador FROM sedes$periodoActual s left join usuarios u on s.id_coordinador = u.id WHERE s.cod_sede = $codSede ";

$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$direccion = $row['direccion'];
	$telefonos = $row['telefonos'];
	$email = $row['email'];
	$fotoFachada = $row['url_foto'];
	$coordinador = $row['coordinador'];
}









?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h1><?php echo $nomInst; ?></h1>
        <h2>Sede: <?php echo $nomSede; ?></h2>
        <!-- <h4><?php echo $codSede; ?></h4> -->
        <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl.$_SESSION['rutaDashboard']; ?>">Home</a>
          </li>
          <li class="active">
              <strong>Sede</strong>
          </li>
        </ol>


    </div>
  <div class="col-lg-4">
      <div class="title-action">
		  	<a href="sede_archivos.php?sede=<?php echo $codSede;  ?>" class="btn btn-primary"><i class="fa fa-cloud"></i> Ver Archivos </a>
         <!--
          <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
          <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
	  -->
          <!-- <a href="<?php echo $baseUrl; ?>/modules/despachos/despacho_nuevo.php" target="_self" class="btn btn-primary"><i class="fa fa-truck"></i> Nuevo despacho </a> -->

      </div>
  </div>
</div>

<?php
$consulta = " select distinct semana from planilla_semanas ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	while($row = $resultado->fetch_assoc()){
		$aux = $row['semana'];
		$consulta2 = " show tables like 'focalizacion$aux' ";
		$resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
		if($resultado2->num_rows >= 1){
		 $semanas[] = $aux;
		}
	}
}
?>


<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
            	<div class="ibox-content">
            		<div class="row">
        				<div class="col-sm-7">
							<div class="fachadaSede">
								<?php if($fotoFachada == ''){ ?>
									<img src="<?php echo $baseUrl ?>/img/no_image.jpg" alt="Foto de sede">
								<?php }else{ ?>
									<img src="<?php echo $baseUrl ?>/<?php echo $fotoFachada; ?>" alt="Foto de sede">
								<?php } ?>
							</div>
	        				<p>
	        					<?php if($coordinador != ''){ ?>
	        					<strong>Coordinador: </strong><br>
	        					<?php echo $coordinador; ?><br>
	        					<?php } ?>
	        					<?php if($direccion != ''){ ?>
	        					<strong>Dirección: </strong><br>
	        					<?php echo $direccion; ?><br>
	        					<?php } ?>
	        					<?php if($telefonos != ''){ ?>
	        					<strong>Telefonos: </strong><br>
	        					<?php echo $telefonos; ?><br>
	        					<?php } ?>
	        					<?php if($email != ''){ ?>
								<strong>Correo electronico: </strong><br>
	        					<?php echo $email; ?><br>
	        					<?php } ?>
	        				</p>
        				</div>
        			</div>
            	</div>
            </div>
        </div>
    </div>
</div>


<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-content">
					<h2>Priorización</h2>
					<?php
					$priorizacion = array();
					$totalesPriorizacion = array();
					$totalesPriorizacion['APS'] = 0;
					$totalesPriorizacion['CAJMRI'] = 0;
					$totalesPriorizacion['CAJTRI'] = 0;
					$totalesPriorizacion['CAJMPS'] = 0;

					for ($i=1; $i <= 3 ; $i++) {
						$totalesPriorizacion['Etario'.$i.'_APS'] = 0;
						$totalesPriorizacion['Etario'.$i.'_CAJMRI'] = 0;
						$totalesPriorizacion['Etario'.$i.'_CAJTRI'] = 0;
						$totalesPriorizacion['Etario'.$i.'_CAJMPS'] = 0;
					}
					foreach ($semanas as $semana){
						$consulta = " select * from sedes_cobertura where semana = '$semana' and cod_sede = $codSede ";
						$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
						if($resultado->num_rows >= 1){
							while($row = $resultado->fetch_assoc()){
								if(isset($priorizacion['APS'][$semana]) && floatval($priorizacion['APS'][$semana]) > 0){
									$priorizacion['APS'][$semana] = $priorizacion['APS'][$semana] + $row['APS'];
								}else{
									$priorizacion['APS'][$semana] = $row['APS'];
								}
								if(isset($priorizacion['CAJMRI'][$semana]) && floatval($priorizacion['CAJMRI'][$semana]) > 0){
									$priorizacion['CAJMRI'][$semana] = $priorizacion['CAJMRI'][$semana] + $row['CAJMRI'];
								}else{
									$priorizacion['CAJMRI'][$semana] = $row['CAJMRI'];
								}
								if(isset($priorizacion['CAJTRI'][$semana]) && floatval($priorizacion['CAJTRI'][$semana]) > 0){
									$priorizacion['CAJTRI'][$semana] = $priorizacion['CAJTRI'][$semana] + $row['CAJTRI'];
								}else{
									//$priorizacion['CAJTRI'][$semana] = $row['CAJTRI'];
								}
								if(isset($priorizacion['CAJMPS'][$semana]) && floatval($priorizacion['CAJMPS'][$semana]) > 0){
									$priorizacion['CAJMPS'][$semana] = $priorizacion['CAJMPS'][$semana] + $row['CAJMPS'];
								}else{
									$priorizacion['CAJMPS'][$semana] = $row['CAJMPS'];
								}

								for ($i=1; $i <= 3 ; $i++) {
									if(isset($priorizacion['Etario'.$i.'_APS'][$semana]) && floatval($priorizacion['Etario'.$i.'_APS'][$semana]) > 0){
										$priorizacion['Etario'.$i.'_APS'][$semana] = $priorizacion['Etario'.$i.'_APS'][$semana] + $row['Etario'.$i.'_APS'];
									}else{
										$priorizacion['Etario'.$i.'_APS'][$semana] = $row['Etario'.$i.'_APS'];
									}

									if(isset($priorizacion['Etario'.$i.'_CAJMRI'][$semana]) && floatval($priorizacion['Etario'.$i.'_CAJMRI'][$semana]) > 0){
										$priorizacion['Etario'.$i.'_CAJMRI'][$semana] = $priorizacion['Etario'.$i.'_CAJMRI'][$semana] + $row['Etario'.$i.'_CAJMRI'];
									}else{
										$priorizacion['Etario'.$i.'_CAJMRI'][$semana] = $row['Etario'.$i.'_CAJMRI'];
									}

									if(isset($priorizacion['Etario'.$i.'_CAJTRI'][$semana]) && floatval($priorizacion['Etario'.$i.'_CAJTRI'][$semana]) > 0){
										$priorizacion['Etario'.$i.'_CAJTRI'][$semana] = $priorizacion['Etario'.$i.'_CAJTRI'][$semana] + $row['Etario'.$i.'_CAJTRI'];
									}else{
										//$priorizacion['Etario'.$i.'_CAJTRI'][$semana] = $row['Etario'.$i.'_CAJTRI'];
									}

									if(isset($priorizacion['Etario'.$i.'_CAJMPS'][$semana]) && floatval($priorizacion['Etario'.$i.'_CAJMPS'][$semana]) > 0){
										$priorizacion['Etario'.$i.'_CAJMPS'][$semana] = $priorizacion['Etario'.$i.'_CAJMPS'][$semana] + $row['Etario'.$i.'_CAJMPS'];
									}else{
										$priorizacion['Etario'.$i.'_CAJMPS'][$semana] = $row['Etario'.$i.'_CAJMPS'];
									}
								}

								$totalesPriorizacion['APS'] = $totalesPriorizacion['APS'] + $row['APS'];
								$totalesPriorizacion['CAJMRI'] = $totalesPriorizacion['CAJMRI'] + $row['CAJMRI'];
								//$totalesPriorizacion['CAJTRI'] = $totalesPriorizacion['CAJTRI'] + $row['CAJTRI'];
								$totalesPriorizacion['CAJMPS'] = $totalesPriorizacion['CAJMPS'] + $row['CAJMPS'];

								for ($i=1; $i <= 3 ; $i++) {
									$totalesPriorizacion['Etario'.$i.'_APS'] = $totalesPriorizacion['Etario'.$i.'_APS'] + $row['Etario'.$i.'_APS'];
									$totalesPriorizacion['Etario'.$i.'_CAJMRI'] = $totalesPriorizacion['Etario'.$i.'_CAJMRI'] + $row['Etario'.$i.'_CAJMRI'];
									//$totalesPriorizacion['Etario'.$i.'_CAJTRI'] = $totalesPriorizacion['Etario'.$i.'_CAJTRI'] + $row['Etario'.$i.'_CAJTRI'];
									$totalesPriorizacion['Etario'.$i.'_CAJMPS'] = $totalesPriorizacion['Etario'.$i.'_CAJMPS'] + $row['Etario'.$i.'_CAJMPS'];
								}
							}
						}
					}

					?>
					<div class="table-responsive">
						<table class="table table-striped table-hover dataTable-priorizacion">
							<thead>
								<tr>
									<th>Complemento</th>
									<?php
										foreach ($semanas as $semana){ ?>
											<th style="text-align:center;">Sem <?php echo $semana; ?></th>
										<?php }
									?>
									<th style="text-align:center;">Total</th>
								</tr>
							</thead>
							<tbody>
								<?php for ($i=1; $i <= 3 ; $i++) {  ?>
									<tr> <td>Etario <?php echo$i; ?> APS</td> <?php foreach ($semanas as $semana){ ?> <td style="text-align:center;"><?php echo $priorizacion['Etario'.$i.'_APS'][$semana]; ?></td> <?php } ?> <td style="text-align:center;"><?php echo $totalesPriorizacion['Etario'.$i.'_APS']; ?></td> </tr>
								<?php } ?>

								<tr> <th>Total APS</th> <?php foreach ($semanas as $semana){ ?> <th style="text-align:center;"><?php echo $priorizacion['APS'][$semana]; ?></th> <?php } ?> <th style="text-align:center;"><?php echo $totalesPriorizacion['APS']; ?></th> </tr>

								<?php for ($i=1; $i <= 3 ; $i++) {  ?>
									<tr> <td>Etario <?php echo$i; ?> CAJMRI</td> <?php foreach ($semanas as $semana){ ?> <td style="text-align:center;"><?php echo $priorizacion['Etario'.$i.'_CAJMRI'][$semana]; ?></td> <?php } ?> <td style="text-align:center;"><?php echo $totalesPriorizacion['Etario'.$i.'_CAJMRI']; ?></td> </tr>
								<?php } ?>

								<tr> <th>Total CAJMRI</th> <?php foreach ($semanas as $semana){ ?> <th style="text-align:center;"><?php echo $priorizacion['CAJMRI'][$semana]; ?></th> <?php } ?> <th style="text-align:center;"><?php echo $totalesPriorizacion['CAJMRI']; ?></th> </tr>

								<!-- <?php for ($i=1; $i <= 3 ; $i++) {  ?>
									<tr> <td>Etario <?php echo$i; ?> CAJTRI</td> <?php foreach ($semanas as $semana){ ?> <td style="text-align:center;"><?php echo $priorizacion['Etario'.$i.'_CAJTRI'][$semana]; ?></td> <?php } ?> <td style="text-align:center;"><?php echo $totalesPriorizacion['Etario'.$i.'_CAJTRI']; ?></td> </tr>
								<?php } ?> -->

								<!-- <tr> <th>Total CAJTRI</th> <?php foreach ($semanas as $semana){ ?> <th style="text-align:center;"><?php echo $priorizacion['CAJTRI'][$semana]; ?></th> <?php } ?> <th style="text-align:center;"><?php echo $totalesPriorizacion['CAJTRI']; ?></th> </tr> -->

								<?php for ($i=1; $i <= 3 ; $i++) {  ?>
									<tr> <td>Etario <?php echo$i; ?> CAJMPS</td> <?php foreach ($semanas as $semana){ ?> <td style="text-align:center;"><?php echo $priorizacion['Etario'.$i.'_CAJMPS'][$semana]; ?></td> <?php } ?> <td style="text-align:center;"><?php echo $totalesPriorizacion['Etario'.$i.'_CAJMPS']; ?></td> </tr>
								<?php } ?>

								<tr> <th>Total CAJMPS</th> <?php foreach ($semanas as $semana){ ?> <th style="text-align:center;"><?php echo $priorizacion['CAJMPS'][$semana]; ?></th> <?php } ?> <th style="text-align:center;"><?php echo $totalesPriorizacion['CAJMPS']; ?></th> </tr>

							</tbody>
							<tfoot>
								<tr>
									<th>Complemento</th>
									<?php
										foreach ($semanas as $semana){ ?>
											<th style="text-align:center;">Sem <?php echo $semana; ?></th>
										<?php }
									?>
									<th style="text-align:center;">Total</th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>







<div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox">
                        <div class="ibox-content">
							<h2>Focalización</h2>
                            <div class="clients-list">
                            <ul class="nav nav-tabs">
                                <!-- <span class="pull-right small text-muted">1406 Elements</span> -->


								<?php
								$auxIndice = 1;
								foreach ($semanas as $semana){
									?>
									<li class=" <?php if($auxIndice == 1){echo ' active '; } ?> "><a data-toggle="tab" href="#tab-<?php echo $semana; ?>"><i class="fa fa-child"></i> Sem <?php echo $semana; ?></a></li>
									<?php
									$auxIndice++;
								}
								?>






                            </ul>
                            <div class="tab-content">
								<?php
								$auxIndice = 1;
								foreach ($semanas as $semana){ ?>
									<div id="tab-<?php echo $semana; ?>" class="tab-pane <?php if($auxIndice == 1){echo ' active '; } ?>">
										<div style="overflow : hidden; height: 100%;">
											<div class="table-responsive">
												<table class="table table-striped table-hover dataTable-focalizacion">
													<thead>
														<tr>
															<th>Num doc</th>
															<th>Tipo doc</th>
															<th>Nombre</th>
															<th>Genero</th>
															<th>Grado</th>
															<th>Grupo</th>
															<th>Jornada</th>
															<th>Edad</th>
															<th>Tipo COMP</th>
														</tr>
													</thead>
													<tbody>
														<?php

														$consulta = " SELECT f.num_doc, t.Abreviatura AS tipo_doc, CONCAT(f.nom1, ' ', f.nom2, ' ', f.ape1, ' ', f.ape2) AS nombre, f.genero, g.nombre as grado, f.nom_grupo, jor.nombre as jornada, f.edad, f.Tipo_complemento FROM focalizacion$semana f LEFT JOIN tipodocumento t ON t.id = f.tipo_doc LEFT JOIN grados g ON g.id = f.cod_grado LEFT JOIN jornada jor ON jor.id = f.cod_jorn_est where cod_sede = '$codSede' order by f.nom1 asc ";

														$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
														if($resultado->num_rows >= 1){
															while($row = $resultado->fetch_assoc()){ ?>
															<tr class="titular" numDoc="<?php echo $row['num_doc']; ?>" tipoDoc="<?php echo $row['tipo_doc']; ?>" semana="<?php echo $semana; ?>" style="cursor:pointer" >
																<td><?php echo $row['num_doc']; ?></td>
																<td><?php echo $row['tipo_doc']; ?></td>
																<td><?php echo $row['nombre']; ?></td>
																<td style="text-align_center;"><?php echo $row['genero']; ?></td>
																<td><?php echo $row['grado']; ?></td>
																<td style="text-align:center;"><?php echo $row['nom_grupo']; ?></td>
																<td><?php echo $row['jornada']; ?></td>
																<td style="text-align:center;"><?php echo $row['edad']; ?></td>
																<td style="text-align:center;"><?php echo $row['Tipo_complemento']; ?></td>
															</tr>
															<?php }
														}
														?>
													</tbody>
													<tfoot>
														<tr>
															<th>Num doc</th>
															<th>Tipo doc</th>
															<th>Nombre</th>
															<th>Genero</th>
															<th>Grado</th>
															<th>Grupo</th>
															<th>Jornada</th>
															<th>Edad</th>
															<th>Tipo COMP</th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
									<?php
									$auxIndice++;
								}
								?>






















                            </div><!-- /.tab-content -->

						</div><!-- /.clients-list -->
                        </div>
                    </div><!-- /.ibox -->
                </div>

            </div>
        </div>








































<?php include '../../footer.php'; ?>

    <!-- Mainly scripts -->
    <script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>



    <!-- <script src="https://cdn.datatables.net/fixedcolumns/3.2.2/js/dataTables.fixedColumns.min.js"></script>
    <link href="https://cdn.datatables.net/fixedcolumns/3.2.2/css/fixedColumns.dataTables.min.css" rel="stylesheet"/> -->




    <script src="<?php echo $baseUrl; ?>/modules/instituciones/js/dataTables.fixedColumns.min.js"></script>
    <link href="<?php echo $baseUrl; ?>/modules/instituciones/css/fixedColumns.dataTables.min.css" rel="stylesheet"/>




    <!-- Custom and plugin javascript -->
    <script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>


    <script src="<?php echo $baseUrl; ?>/modules/instituciones/js/sede.js"></script>










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


    <!-- Page-Level Scripts -->
    <script>
//     fixedColumns:   {
//     leftColumns: 2//Le indico que deje fijas solo las 2 primeras columnas
// }
        $(document).ready(function(){
            $('.dataTable-focalizacion').DataTable({
				order: [[2,'asc']],
                scrollY:        "500px",
                scrollX:        true,
                scrollCollapse: true,
                paging:         false,
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [

                    {extend: 'excel', title: 'ExampleFile'}


                ],
                // fixedColumns:   {
                //     leftColumns: 1//Le indico que deje fijas solo las 2 primeras columnas
                // },

            });

        });

    </script>


<form action="<?php echo $baseUrl; ?>/modules/titulares_derecho/titular.php" method="GET" name="verTitular" id="verTitular">
	<input type="hidden" name="numDoc" id="numDoc">
	<input type="hidden" name="tipoDoc" id="tipoDoc">
	<input type="hidden" name="semana" id="semana">
	<input type="hidden" name="sede" id="sede" value="<?php echo $_GET['codSede']; ?>">
</form>







</body>
</html>
