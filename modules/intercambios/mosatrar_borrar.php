				
<div class="ibox float-e-margins">
						<div class="ibox-content contentBackground">
							<div class="row">
								<div class="col-sm-4 form-group">
									<label for="municipio">Municipio</label>
									<input type="text" name="municipio" id="municipio" value="<?php echo $datosNovedad['Ciudad']; ?>" class="form-control" readonly>
								</div><!-- /.col -->
								<div class="col-sm-8 form-group">
									<label for="institucion">Institución</label>
									<input type="text" name="institucion" id="institucion" value="<?php echo $datosNovedad['nom_inst']; ?>" class="form-control" readonly>
								</div><!-- /.col -->
								<div class="col-sm-6 form-group">
									<label for="sede">Sede</label>
									<input type="text" name="sede" id="sede" value="<?php echo $datosNovedad['nom_sede']; ?>" class="form-control" readonly>
								</div><!-- /.col -->
								<div class="col-sm-3 form-group">
									<label for="mes">Mes</label>
									<input type="text" name="mes" id="mes" value="<?php echo $mesNm; ?>" class="form-control" readonly>
								</div><!-- /.col -->
								<div class="col-sm-3 form-group">
									<label for="semana">Semana</label>
									<input type="text" name="semana" id="semana" value="<?php echo $datosNovedad['Semana']; ?>" class="form-control" readonly>
								</div><!-- /.col -->
							</div><!-- -/.row -->
						</div><!-- /.ibox-content -->
					</div><!-- /.ibox float-e-margins -->



<div class="ibox float-e-margins priorizacionAction3">
						<div class="ibox-content contentBackground">
							<div class="table-responsive">
								<table width="100%" class="table table-striped table-bordered table-hover selectableRows">
									<thead>
										<tr>
											<th>Complemento Inicial</th>
											<th>Cant Total Inicial</th>
											<th>Grupo Etario 1</th>
											<th>Grupo Etario 2</th>
											<th>Grupo Etario 3</th>
										</tr>
									</thead>
									<tbody>
										<?php if($datosPriorizacion['APS'] > 0 ){ ?>
											<tr class="APSactual">
												<td> <input type="text" class="form-control" name="APSnm" id="APSnm" value="APS" readonly> </td>
												<td> <input type="text" class="form-control" name="APSactualTotal" id="APSactualTotal" value="<?php echo $datosPriorizacion['APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APSactual1" id="APSactual1" value="<?php echo $datosPriorizacion['Etario1_APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APSactual2" id="APSactual2" value="<?php echo $datosPriorizacion['Etario2_APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APSactual3" id="APSactual3" value="<?php echo $datosPriorizacion['Etario3_APS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>

										<?php if($datosPriorizacion['CAJMPS'] > 0 ){ ?>
											<tr class="CAJMPSactual">
												<td> <input type="text" class="form-control" name="CAJMPSnm" id="CAJMPSnm" value="CAJMPS" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJMPSactualTotal" id="CAJMPSactualTotal" value="<?php echo $datosPriorizacion['CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPSactual1" id="CAJMPSactual1" value="<?php echo $datosPriorizacion['Etario1_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPSactual2" id="CAJMPSactual2" value="<?php echo $datosPriorizacion['Etario2_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPSactual3" id="CAJMPSactual3" value="<?php echo $datosPriorizacion['Etario3_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>

										<?php if($datosPriorizacion['CAJMRI'] > 0 ){ ?>
											<tr class="CAJMRIactual">
												<td> <input type="text" class="form-control" name="CAJMRInm" id="CAJMRInm" value="CAJMRI" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJMRIactualTotal" id="CAJMRIactualTotal" value="<?php echo $datosPriorizacion['CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRIactual1" id="CAJMRIactual1" value="<?php echo $datosPriorizacion['Etario1_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRIactual2" id="CAJMRIactual2" value="<?php echo $datosPriorizacion['Etario2_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRIactual3" id="CAJMRIactual3" value="<?php echo $datosPriorizacion['Etario3_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php	} ?>

									</tbody>
								</table>
							</div><!-- /.table-responsive -->
						</div><!-- /.ibox-content -->
					</div><!-- /.ibox float-e-margins -->















<div class="ibox float-e-margins priorizacionAction3">
						<div class="ibox-content contentBackground">
							<div class="table-responsive">
								<table width="100%" class="table table-striped table-bordered table-hover selectableRows tablaNuevasCantidades">
									<thead>
										<tr>
											<th>Nuevo Complemento</th>
											<th>Nueva Cant Total</th>
											<th>Grupo Etario 1</th>
											<th>Grupo Etario 2</th>
											<th>Grupo Etario 3</th>
										</tr>
									</thead>
									<tbody>

										<?php if($datosNovedad['APS'] > 0 ){ ?>
											<tr class="APS">
												<td> <input type="text" class="form-control" name="APSnm" id="APSnm" value="APS" readonly> </td>
												<td> <input type="text" class="form-control" name="APSTotal" id="APSTotal" value="<?php echo $datosNovedad['APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APS1" id="APS1" value="<?php echo $datosNovedad['Etario1_APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APS2" id="APS2" value="<?php echo $datosNovedad['Etario2_APS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="APS3" id="APS3" value="<?php echo $datosNovedad['Etario3_APS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<?php if($datosNovedad['CAJMPS'] > 0 ){ ?>
											<tr class="CAJMPS">
												<td> <input type="text" class="form-control" name="CAJMPSnm" id="CAJMPSnm" value="CAJMPS" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJMPSTotal" id="CAJMPSTotal" value="<?php echo $datosNovedad['CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPS1" id="CAJMPS1" value="<?php echo $datosNovedad['Etario1_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPS2" id="CAJMPS2" value="<?php echo $datosNovedad['Etario2_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMPS3" id="CAJMPS3" value="<?php echo $datosNovedad['Etario3_CAJMPS']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<?php if($datosNovedad['CAJMRI'] > 0 ){ ?>
											<tr class="CAJMRI">
												<td> <input type="text" class="form-control" name="CAJMRInm" id="CAJMRInm" value="CAJMRI" readonly> </td>
												<td> <input type="text" class="form-control" name="CAJMRITotal" id="CAJMRITotal" value="<?php echo $datosNovedad['CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRI1" id="CAJMRI1" value="<?php echo $datosNovedad['Etario1_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRI2" id="CAJMRI2" value="<?php echo $datosNovedad['Etario2_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
												<td> <input type="text" class="form-control" name="CAJMRI3" id="CAJMRI3" value="<?php echo $datosNovedad['Etario3_CAJMRI']; ?>" readonly style="text-align:center;"> </td>
											</tr>
										<?php } ?>

										<tr class="total">
											<td> <input type="text" class="form-control" name="totalNm" id="totalNm" value="TOTAL" readonly> </td>
											<td> <input type="text" class="form-control" name="totalTotal" id="totalTotal" value="<?php echo $datosNovedad['APS'] + $datosNovedad['CAJMPS'] + $datosNovedad['CAJMRI']; ?>" readonly style="text-align:center;"> </td>
											<td> <input type="text" min="1" pattern="^[0-9]+" class="form-control" name="total1" id="total1" value="<?php echo $datosNovedad['Etario1_APS'] + $datosNovedad['Etario1_CAJMPS'] + $datosNovedad['Etario1_CAJMRI']; ?>" readonly  style="text-align:center;"> </td>
											<td> <input type="text" min="1" pattern="^[0-9]+" class="form-control" name="total2" id="total2" value="<?php echo $datosNovedad['Etario2_APS'] + $datosNovedad['Etario2_CAJMPS'] + $datosNovedad['Etario2_CAJMRI']; ?>" readonly  style="text-align:center;"> </td>
											<td> <input type="text" min="1" pattern="^[0-9]+" class="form-control" name="total3" id="total3" value="<?php echo $datosNovedad['Etario3_APS'] + $datosNovedad['Etario3_CAJMPS'] + $datosNovedad['Etario3_CAJMRI']; ?>" readonly  style="text-align:center;"> </td>
										</tr>

									</tbody>
								</table>
							</div><!-- /.table-responsive -->
						</div><!-- /.ibox-content -->
					</div><!-- /.ibox float-e-margins -->

















					<div class="ibox float-e-margins priorizacionAction3">
						<div class="ibox-content contentBackground">
							<div class="row">
								<div class="col-sm-6 form-group">
									<label for="observaciones">Observaciones</label>
									<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80" readonly><?php echo $datosNovedad['observaciones']; ?></textarea>
								</div><!-- /.col -->
								<div class="col-sm-6 form-group">
									<label for="departamento">Archivo</label>
									<div style="text-align:center; box-sizing:border-box; padding:20px">
										<?php
										$url = $baseUrl."/".$datosNovedad['arch_adjunto'];
										$ext = substr($url,-3);
										//var_dump($ext);
										?>


										<a href="<?php echo $url; ?>" target="_blank" style="color:#1ab394;">


											<?php
											if($ext == 'pdf'){
												echo "<i class=\"fa fa-file-pdf-o\" style=\"font-size:60px\"></i>";
											}else{
												echo "<i class=\"fa fa-file-image-o\" style=\"font-size:60px\"></i>";
											}
											 ?>



											<h3>Ver Archivo</h3>
										</a>
									</div>
								</div><!-- /.col -->
							</div><!-- -/.row -->
						</div><!-- /.ibox-content -->
					</div><!-- /.ibox float-e-margins -->
