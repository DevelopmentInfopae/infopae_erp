<?php include 'header.php'; ?>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Entrega de Complementos Alimentarios Contrato PAE Santander 2018 </h5>
                    <div>
                        <?php
                        $institucionCodigo = '';
                        require_once 'db/conexion.php';
                        $Link = new mysqli($Hostname, $Username, $Password, $Database);
                        if ($Link->connect_errno) {
                            echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                        }
                        $Link->set_charset("utf8");
                        $rectorDocumento = $_SESSION['num_doc'];
                        // Quien consulta es el rector de la institución
                        $consulta = " select instituciones.*, ubicacion.Ciudad as municipio from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento limit 1  ";
                        //echo $consulta;
                        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                        if($resultado->num_rows >= 1){
                            $row = $resultado->fetch_assoc();
                            $institucionCodigo = $row['codigo_inst'];
                        }
                        ?>

                    </div>
                    <div class="pull-right">
                            <input type="hidden" id="codInst" name="codInst" value="<?php echo $institucionCodigo; ?>">
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

                        <ul class="stat-list" id="listaTotales">




                            </ul>
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </div>


		<?php
		require_once 'db/conexion.php';
		$Link = new mysqli($Hostname, $Username, $Password, $Database);
		if ($Link->connect_errno) {
		echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
		$Link->set_charset("utf8");

			// Bitacora / Actividades de usuarios
			$consulta = " SELECT b.*, ba.descripciones, u.nombre, u.foto FROM bitacora b left join bitacora_acciones ba on ba.id = b.tipo_accion left join usuarios u on b.usuario = u.id ORDER BY b.fecha DESC LIMIT 20 ";
			//echo $consulta;
			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));


			//Novidades
			$novedades = array();

			// Novedades de Priorización 1
			$consulta = " SELECT 1 as tipo, n.fecha_hora, n.observaciones, u.nombre, u.foto FROM novedades_priorizacion n LEFT JOIN usuarios u ON u.id = n.id_usuario limit 10 ";
			$resultado2 = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			$indice = 0;
			$aux = '';
			if($resultado2->num_rows >= 1){
				while($row = $resultado2->fetch_assoc()){
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
			$consulta = " SELECT 2 as tipo, n.fecha_hora, n.observaciones, u.nombre, u.foto FROM novedades_focalizacion n LEFT JOIN usuarios u ON u.id = n.id_usuario limit 10 ";
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
			//var_dump($novedades);



		?>













	    <div class="row">
	        <div class="col-lg-6">
	            <div class="ibox float-e-margins">
	                <div class="ibox-title">
	                    <h5>Novedades</h5>
	                    <!-- <span class="label label-primary">Meeting today</span> -->
	                    <div class="ibox-tools">
	                        <a class="collapse-link">
	                            <i class="fa fa-chevron-up"></i>
	                        </a>
	                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
	                            <i class="fa fa-wrench"></i>
	                        </a>
	                        <ul class="dropdown-menu dropdown-user">
	                            <li><a href="#">Config option 1</a>
	                            </li>
	                            <li><a href="#">Config option 2</a>
	                            </li>
	                        </ul>
	                        <a class="close-link">
	                            <i class="fa fa-times"></i>
	                        </a>
	                    </div>
	                </div>

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
	                                <!-- <small class="text-navy">2 hour ago</small> -->
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






	<!--

	                    <div class="timeline-item">
	                        <div class="row">
	                            <div class="col-xs-3 date">
	                                <i class="fa fa-file-text"></i>
	                                7:00 am
	                                <br/>
	                                <small class="text-navy">3 hour ago</small>
	                            </div>
	                            <div class="col-xs-7 content">
	                                <p class="m-b-xs"><strong>Send documents to Mike</strong></p>
	                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since.</p>
	                            </div>
	                        </div>
	                    </div> -->
	                    <!-- <div class="timeline-item">
	                        <div class="row">
	                            <div class="col-xs-3 date">
	                                <i class="fa fa-coffee"></i>
	                                8:00 am
	                                <br/>
	                            </div>
	                            <div class="col-xs-7 content">
	                                <p class="m-b-xs"><strong>Coffee Break</strong></p>
	                                <p>
	                                    Go to shop and find some products.
	                                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's.
	                                </p>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="timeline-item">
	                        <div class="row">
	                            <div class="col-xs-3 date">
	                                <i class="fa fa-phone"></i>
	                                11:00 am
	                                <br/>
	                                <small class="text-navy">21 hour ago</small>
	                            </div>
	                            <div class="col-xs-7 content">
	                                <p class="m-b-xs"><strong>Phone with Jeronimo</strong></p>
	                                <p>
	                                    Lorem Ipsum has been the industry's standard dummy text ever since.
	                                </p>
	                            </div>
	                        </div>
	                    </div> -->
	                </div>
	            </div>
	        </div>
	        <div class="col-lg-6">
	            <div class="ibox float-e-margins">
	                <div class="ibox-title">
	                    <h5>Actividades de usuarios</h5>
	                    <div class="ibox-tools">
	                        <!-- <span class="label label-warning-light pull-right">10 Messages</span> -->
	                       </div>
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



	                            <!-- <div class="feed-element">
	                                <a href="profile.html" class="pull-left">
	                                    <img alt="image" class="img-circle" src="theme/img/profile.jpg">
	                                </a>
	                                <div class="media-body ">
	                                    <small class="pull-right">5m ago</small>
	                                    <strong>Monica Smith</strong> posted a new blog. <br>
	                                    <small class="text-muted">Today 5:60 pm - 12.06.2014</small>

	                                </div>
	                            </div>

	                            <div class="feed-element">
	                                <a href="profile.html" class="pull-left">
	                                    <img alt="image" class="img-circle" src="theme/img/a2.jpg">
	                                </a>
	                                <div class="media-body ">
	                                    <small class="pull-right">2h ago</small>
	                                    <strong>Mark Johnson</strong> posted message on <strong>Monica Smith</strong> site. <br>
	                                    <small class="text-muted">Today 2:10 pm - 12.06.2014</small>
	                                </div>
	                            </div>
	                            <div class="feed-element">
	                                <a href="profile.html" class="pull-left">
	                                    <img alt="image" class="img-circle" src="theme/img/a3.jpg">
	                                </a>
	                                <div class="media-body ">
	                                    <small class="pull-right">2h ago</small>
	                                    <strong>Janet Rosowski</strong> add 1 photo on <strong>Monica Smith</strong>. <br>
	                                    <small class="text-muted">2 days ago at 8:30am</small>
	                                </div>
	                            </div>
	                            <div class="feed-element">
	                                <a href="profile.html" class="pull-left">
	                                    <img alt="image" class="img-circle" src="theme/img/a4.jpg">
	                                </a>
	                                <div class="media-body ">
	                                    <small class="pull-right text-navy">5h ago</small>
	                                    <strong>Chris Johnatan Overtunk</strong> started following <strong>Monica Smith</strong>. <br>
	                                    <small class="text-muted">Yesterday 1:21 pm - 11.06.2014</small>
	                                    <div class="actions">
	                                        <a class="btn btn-xs btn-white"><i class="fa fa-thumbs-up"></i> Like </a>
	                                        <a class="btn btn-xs btn-white"><i class="fa fa-heart"></i> Love</a>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="feed-element">
	                                <a href="profile.html" class="pull-left">
	                                    <img alt="image" class="img-circle" src="theme/img/a5.jpg">
	                                </a>
	                                <div class="media-body ">
	                                    <small class="pull-right">2h ago</small>
	                                    <strong>Kim Smith</strong> posted message on <strong>Monica Smith</strong> site. <br>
	                                    <small class="text-muted">Yesterday 5:20 pm - 12.06.2014</small>
	                                    <div class="well">
	                                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
	                                        Over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
	                                    </div>
	                                    <div class="pull-right">
	                                        <a class="btn btn-xs btn-white"><i class="fa fa-thumbs-up"></i> Like </a>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="feed-element">
	                                <a href="profile.html" class="pull-left">
	                                    <img alt="image" class="img-circle" src="theme/img/profile.jpg">
	                                </a>
	                                <div class="media-body ">
	                                    <small class="pull-right">23h ago</small>
	                                    <strong>Monica Smith</strong> love <strong>Kim Smith</strong>. <br>
	                                    <small class="text-muted">2 days ago at 2:30 am - 11.06.2014</small>
	                                </div>
	                            </div> -->
	                            <!-- <div class="feed-element">
	                                <a href="profile.html" class="pull-left">
	                                    <img alt="image" class="img-circle" src="theme/img/a7.jpg">
	                                </a>
	                                <div class="media-body ">
	                                    <small class="pull-right">46h ago</small>
	                                    <strong>Mike Loreipsum</strong> started following <strong>Monica Smith</strong>. <br>
	                                    <small class="text-muted">3 days ago at 7:58 pm - 10.06.2014</small>
	                                </div>
	                            </div> -->
	                        </div>

	                        <!-- <button class="btn btn-primary btn-block m-t"><i class="fa fa-arrow-down"></i> Show More</button> -->

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


    <script src="js/index_rector.js"></script>

    <script>
        $(document).ready(function() {
        });




    </script>


















  <script>
        $(document).ready(function() {






        });
    </script>
















</body>
</html>
