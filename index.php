<?php
    $index = 1;
    include 'header.php';
?>

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Entrega de Complementos Alimentarios Contrato PAE</h5>
                    <div class="debug"></div>
                    <div class="pull-right">
                            <button type="button" class="btn btn-xs btn-primary m-l-xs" id="btnActualizarGrafica">Actualizar Datos</button>
                    </div>
                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="timeOption btn btn-xs btn-white active" value="1">Semana</button>
                            <button type="button" class="timeOption btn btn-xs btn-white" value="2">Mes</button>
                        </div>
                    </div>
                </div>
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
                </div>
            </div>
        </div>
    </div>

<?php
	// Bitacora / Actividades de usuarios
	$consulta = " SELECT b.*, ba.descripciones, u.nombre, u.foto FROM bitacora b left join bitacora_acciones ba on ba.id = b.tipo_accion left join usuarios u on b.usuario = u.id ORDER BY b.id DESC LIMIT 20 ";
	//echo $consulta;
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

	//Novidades
	$novedades = array();

	// Novedades de Priorización 1
	$consulta = " SELECT 1 as tipo, n.fecha_hora, n.observaciones, u.nombre, u.foto FROM novedades_priorizacion n LEFT JOIN usuarios u ON u.id = n.id_usuario LIMIT 20";
	$resultado2 = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	$indice = 0;
	$aux = '';
	if($resultado2->num_rows >= 1){
		while($row = $resultado2->fetch_assoc()){
			$aux = $row['fecha_hora'];
			if(isset($novedades[$aux])){
				$bandera = 0;
				while ($bandera == 0) {
                    //$aux = $row['fecha'].$indice;
					$aux = $indice;
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
	$consulta = " SELECT 2 as tipo, n.fecha_hora, n.observaciones, u.nombre, u.foto FROM novedades_focalizacion n LEFT JOIN usuarios u ON u.id = n.id_usuario";
	$resultado2 = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	$indice = 0;
	$aux = '';
	if($resultado2->num_rows >= 1){
		while($row = $resultado2->fetch_assoc()){
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
    <div class="col-lg-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Novedades</h5>
            </div>

            <div class="ibox-content inspinia-timeline">
			<?php
                foreach ($novedades as $novedad) {
				    $tipo = $novedad['tipo'];
				    if($tipo == 1) {
                        $tipo = 'fa-bank';
				        $tipoNm = 'Agrego novedad de priorización';
					} else if($tipo == 2) {
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
                    if(!is_url_exist($foto)) { $foto = $baseUrl."/img/no_image48.jpg"; }
			?>
				<div class="timeline-item">
                    <div class="row">
                        <div class="col-xs-1">
                            <i class="fa <?= $tipo; ?>"></i>
                        </div>
                        <div class="col-xs-11 /*content no-top-border*/">
							<a href="#" class="pull-left" style="margin-right: 10px">
								<img alt="image" style="width: 38px; height: 38px;" class="img-circle" src="<?php echo $foto; ?>">
							</a>
							<div class="media-body ">
                            <!-- <p class="m-b-xs"><strong><?= $nombre; ?></strong> <?= $tipoNm; ?></p> -->
                            <strong><?= $nombre; ?></strong> <?= $tipoNm; ?>

                            <?= $observaciones; ?>
                            <small class="text-muted"><?= $fecha; ?></small>
						</div>
                        </div>
                    </div>
                    <hr>
                </div>
			<?php
                }
            ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Actividades de usuarios</h5>
                <div class="ibox-tools"> </div>
            </div>
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
											<!-- <small class="pull-right">5m ago</small> -->
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
            </div>
        </div>
    </div>
</div><!-- /.wrapper -->
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
    <script src="js/index.js"></script>
</body>
</html>
