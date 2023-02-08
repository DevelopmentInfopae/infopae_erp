<?php 
include "header.php"; 
require_once 'db/conexion.php';

?><script type="text/javascript">
const list = document.querySelector(".li_inicio");
list.className += " active ";
</script>
<?php

?>
<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Entrega de Complementos Alimentarios Contrato PAE</h5>
					<?php  
					$codigoSedes = "";
					$documentoCoordinador = $_SESSION['num_doc'];
					$periodoActual = $_SESSION['periodoActual'];
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
					// var_dump($codigoSedes);
					?>
					<div class="pull-right">
                        <input type="hidden" id="codSede" name="codSede" value="<?php echo $codigoSedes; ?>">
                        <button type="button" class="btn btn-xs btn-primary m-l-xs" id="btnActualizarGrafica">Actualizar Datos</button>
                    </div>
                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="timeOption btn btn-xs btn-white active" value="1">Semana</button>
                            <button type="button" class="timeOption btn btn-xs btn-white" value="2">Mes</button>
                        </div>
                    </div>
				</div> <!-- ibox-title -->		
			</div> <!-- float-e-margins -->
			<div class="ibox-content">
                <div class="row">
	                <div class="col-lg-9">
	                    <div class="flot-chart">
	                        <div class="flot-chart-content" id="flot-dashboard-chart"></div>
	                    </div>
	                </div>
	               	<div class="col-lg-3">
	                    <ul class="stat-list" id="listaTotales"></ul>
	                </div>
                </div>
            </div> <!-- ibox-content -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->

<?php 
	// actividad de usuarios
	$consultaActividades = " SELECT b.*, ba.descripciones, 
									u.nombre, 
									u.foto 
								FROM bitacora b 
								left join bitacora_acciones ba on ba.id = b.tipo_accion 
								left join usuarios u on b.usuario = u.id 
								WHERE b.usuario = ".$_SESSION['id_usuario']." 
								ORDER BY b.fecha DESC 
								LIMIT 20 ";
	$resultado = $Link->query($consultaActividades) or die ('Error al consultar la bitacora '. mysqli_error($Link));

	$novedades = [];

	// Novedades de Priorización 1
	$codigosSedes = substr($codigoSedes, 0, -1);
	$consultaNovedadesPriorizacion = " SELECT 1 as tipo, 
											n.fecha_hora, 
											n.observaciones, 
											u.nombre, 
											u.foto 
										FROM novedades_priorizacion n 
										LEFT JOIN usuarios u ON u.id = n.id_usuario 
										WHERE n.cod_sede IN ($codigosSedes)
										limit 10 ";
	$resultadoNovedadesPriorizacion = $Link->query($consultaNovedadesPriorizacion) or die ('Error al consultar las novedades de priorización. '. mysqli_error($Link));
	$indice = 0;
	$aux = '';
	if($resultadoNovedadesPriorizacion->num_rows >= 1){
		while($row = $resultadoNovedadesPriorizacion->fetch_assoc()){
			$aux = $row['fecha_hora'];
			if(isset($novedades[$aux])){
				$bandera = 0;
				while ($bandera == 0) {
					$aux = $row['fecha'].$indice;
					if(isset($novedades[$aux])){
						$indice++;
					}
					else{
						$indice = 0;
						$bandera = 1;
					}
				}
			}
			$novedades[$aux] = $row;
			$aux = '';
		}
	}

	// Novedades de Focalización 2
	$consultaNovedadesFocalizacion = " SELECT 2 as tipo, 
											n.fecha_hora, 
											n.observaciones, 
											u.nombre, 
											u.foto 
										FROM novedades_focalizacion n 
										LEFT JOIN usuarios u ON u.id = n.id_usuario 
										WHERE n.cod_sede IN ($codigosSedes)
										limit 10;";
	$resultadoNovedadesFocalizacion = $Link->query($consultaNovedadesFocalizacion) or die ('Error al consultar las novedades de focalización. '. mysqli_error($Link));
	$indice = 0;
	$aux = '';
	if($resultadoNovedadesFocalizacion->num_rows >= 1){
		while($row = $resultadoNovedadesFocalizacion->fetch_assoc()){
			$aux = $row['fecha_hora'];
			if(isset($novedades[$aux])){
				$bandera = 0;
				while ($bandera == 0) {
					$aux = $row['fecha_hora'].$indice;
					if(isset($novedades[$aux])){
						$indice++;
					}
					else{
						$indice = 0;
						$bandera = 1;
					}
				}
			}
		$novedades[$aux] = $row;
		$aux = '';
		}
	}
	krsort($novedades);				
?>
	<div class="row">
		<div class="col-lg-6 col-sm-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5>Novedades</h5>
	                <div class="ibox-tools">
	                    <a class="collapse-link">
	                        <i class="fa fa-chevron-up"></i>
	                    </a>
	                    <a class="close-link">
	                        <i class="fa fa-times"></i>
	                    </a>
	                </div>
				</div> <!-- ibox-title -->
				<div class="ibox-content inspinia-timeline">
					<?php foreach ($novedades as $novedad) {
						$tipo = $novedad['tipo'];
						if($tipo == 1){
							$tipo = 'fa-bank';
							$tipoNm = 'Agrego novedad de priorización';
						}else if($tipo == 2){
							$tipo = 'fa-child';
							$tipoNm = 'Agrego novedad de focalización';
						}
						$fecha = $novedad['fecha_hora'];
						$fecha = date("d/m/Y h:i:s a", strtotime($fecha));
						$nombre = $novedad['nombre'];
						$observaciones = $novedad['observaciones'];
						$aux = $novedad['foto'];
						$aux = substr( $aux, 5);
						$foto = $baseUrl.$aux;
						if(!is_url_exist($foto)){
							$foto = $baseUrl."/img/no_image48.jpg";
						}
					?>
					<div class="timeline-item">
	                    <div class="row">
	                        <div class="col-xs-3 date">
								<?php  ?>
	                                <i class="fa <?php echo $tipo; ?>"></i>
	                                <?php echo $fecha; ?>
	                                <br/> 
	                        </div>
	                        <div class="col-xs-7 content no-top-border">
								<a href="#" class="pull-left">
									<img alt="image" style="width: 38px; height: 38px;" lass="img-circle" src="<?php echo $foto; ?>">
								</a>
								<div class="media-body ">
			                        <p class="m-b-xs"><strong><?php echo $nombre; ?></strong> <?php echo $tipoNm; ?></p>
			                        <p><?php echo $observaciones; ?></p>
								</div>
	                        </div>
	                    </div>
	                </div>
					<?php } ?>
				</div> <!-- ibox-content -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-6 -->
		<div class="col-lg-6 col-sm-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
	                <h5>Actividades de usuarios</h5>
	                <div class="ibox-tools">
	                    <div class="ibox-tools">
	                       	<a class="collapse-link">
	                            <i class="fa fa-chevron-up"></i>
	                        </a>
	                        <a class="close-link">
	                            <i class="fa fa-times"></i>
	                        </a>
	                    </div>
	                </div>
	            </div> <!-- ibox-title -->
	            <div class="ibox-content">
	                <div>
	                    <div class="feed-activity-list">
							<?php
								if($resultado->num_rows >= 1){
									while($row = $resultado->fetch_assoc()){
										$aux = $row['foto'];
										$aux = substr( $aux, 5);
										$foto = $baseUrl.$aux;
										if(!is_url_exist($foto)){
											$foto = $baseUrl."/img/no_image48.jpg";
										}
										$fecha = $row['fecha'];
										$fecha = date("d/m/Y h:i:s a", strtotime($fecha));
							?>
							<div class="feed-element">
								<a href="#" class="pull-left">
									<img alt="image" style="width: 38px; height: auto;" class="img-circle" src="<?php echo $foto; ?>">
								</a>
								<div class="media-body ">
									<strong><?php echo $row['nombre']; ?></strong> <?php echo $row['descripciones']; ?>. <br>
										<?php echo $row['observacion']; ?>. <br>
									<small class="text-muted"><?php echo $fecha; ?></small>
								</div>
							</div>
							<?php
									}
								}
								?>
	                    </div>
	                </div>
	            </div> <!-- ibox-content -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-6 -->
	</div> <!-- row -->
</div> <!-- wrapper-content -->
	
<?php include 'footer.php'; ?>

    <!-- Mainly scripts -->
    <script src="theme/js/jquery-3.1.1.min.js"></script>
    <script src="theme/js/bootstrap.min.js"></script>
    <script src="theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="theme/js/inspinia.js"></script>
    <script src="theme/js/plugins/pace/pace.min.js"></script>

    <!-- jQuery UI -->
    <script src="theme/js/plugins/jquery-ui/jquery-ui.min.js"></script>

    <!-- Flot -->
    <script src="theme/js/plugins/flot/jquery.flot.js"></script>
    <script src="theme/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
    <script src="theme/js/plugins/flot/jquery.flot.spline.js"></script>
    <script src="theme/js/plugins/flot/jquery.flot.resize.js"></script>
    <script src="theme/js/plugins/flot/jquery.flot.pie.js"></script>
    <script src="theme/js/plugins/flot/jquery.flot.symbol.js"></script>
    <script src="theme/js/plugins/flot/jquery.flot.time.js"></script>
    <script src="js/index_coordinador.js"></script>

</body>
</html>