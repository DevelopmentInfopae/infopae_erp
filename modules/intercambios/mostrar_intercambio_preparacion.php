<?php $periodoActual = $_SESSION['periodoActual']; ?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Parametros</h5>
				</div>
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">				
							<div class="row">
								<div class="col-sm-4 form-group">
									<label for="mes">Mes</label>
									<input type="text" class="form-control" name="mes" id="mes" value="<?= $mesNm ?>" readonly="readonly">	
								</div>
								
								<div class="col-sm-4 form-group">
									<label for="semana">Semana</label>
									<input type="text" class="form-control" name="semana" id="semana" value="<?= $semana ?>" readonly="readonly">	
								</div>

								<div class="col-sm-4 form-group">
									<label for="dia">Día</label>
									<input type="text" class="form-control" name="dia" id="dia" value="<?= $dia ?>" readonly="readonly">	
								</div>

								<div class="col-sm-4 form-group">
									<label for="tipoComplemento">Tipo de complemento</label>
									<input type="text" class="form-control" name="tipoComplemento" id="tipoComplemento" value="<?= $tipoComplemento ?>" readonly="readonly">	
								</div>

								<div class="col-sm-4 form-group">
									<label for="grupoEtario">Grupo etario</label>
									<input type="text" class="form-control" name="grupoEtario" id="grupoEtario" value="<?= $grupoEtario ?>" readonly="readonly">	
								</div>

								<div class="col-sm-4 form-group">
									<label for="variacion">Variación</label>
									<input type="text" class="form-control" name="variacion" id="variacion" value="<?= $variacion ?>" readonly="readonly">
								</div>

								<div class="col-sm-4 form-group">
									<label for="menu">Menú</label>
									<input type="text" class="form-control" name="menu" id="menu" value="<?= $menu ?>" readonly="readonly">					
								</div>

								<div class="col-sm-8 form-group">
									<label for="preparaciones">Preparaciones</label>
									<input type="text" class="form-control" name="preparaciones" id="preparaciones" value="<?= $producto ?>" readonly="readonly">	
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
	$consulta = " SELECT nmd.*, p.Descripcion AS Componente from novedades_menudet nmd LEFT JOIN productos$periodoActual p ON nmd.cod_producto = p.Codigo WHERE nmd.tipo = 0 AND nmd.id_novedad = $idNovedad ORDER BY nmd.id "; 
	$resultado = $Link->query($consulta) or die ('Unable to execute query - Leyendo novedad det '. mysqli_error($Link));
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Preparación Original</h5> 
				</div>
				<div class="ibox-content">
					<table class="table">
						<thead>
							<tr>
								<th>Preparación</th>
							</tr>
						</thead>
	                  	<tbody>
		                	<?php if($resultado->num_rows >= 1){ ?>
								<?php while($row = $resultado->fetch_assoc()) { ?>
			                        
			                        <tr>
			                            <td>
			                            	<input type="text" class="form-control" name="" id=""  value="<?= $row['Componente']; ?>" readonly>
			                            </td>
			                        </tr>
			                           
		                  		<?php } ?>
		                 	<?php } ?>
	                    </tbody>
	                </table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
	$consulta = " SELECT nmd.*, ftd.Componente FROM fichatecnica ft LEFT JOIN fichatecnicadet ftd ON ftd.IdFT = ft.Id LEFT JOIN novedades_menudet nmd ON nmd.cod_producto = ftd.codigo WHERE ft.Codigo = $codigo AND nmd.tipo = 1 AND nmd.id_novedad = $idNovedad ";
	$resultado = $Link->query($consulta) or die ('Unable to execute query - Leyendo novedad det '. mysqli_error($Link));
?>

<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Ajuste a la preparación</h5> 
				</div>
				<div class="ibox-content">
					<table class="table">
						<thead>
							<tr>
								<th>Preparación</th>
							</tr>
						</thead>
	                  	<tbody>
		                	<?php if($resultado->num_rows >= 1){ ?>
								<?php while($row = $resultado->fetch_assoc()) { ?>
			                        <tr>
			                            <td>
			                            	<input type="text" class="form-control" name="" id=""  value="<?= $row['Componente']; ?>" readonly>
			                            </td>
			                        </tr> 
		                  		<?php } ?>
		                 	<?php } ?>
	                    </tbody>
	                </table>
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
					<div class="row">
						<div class="col-sm-12">				
							<div class="row">
								<div class="col-sm-12 form-group">
									<label for="departamento">Fecha de vencimiento</label>
									<div class="input-group date">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span> 
										<input type="text" class="form-control" name="fechaVencimiento" id="fechaVencimiento"  value="<?= $fechaVencimiento ?>" readonly>
									</div>
								</div>
								<?php
									$columnas = 12;
									if($archivo != ""){
										$columnas = 6;
									}
								?>
								<div class="col-sm-<?= $columnas ?> form-group">
									<label for="observaciones">Observaciones</label>
									<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80" readonly=""><?= $observaciones ?></textarea>
								</div>
								<?php if($archivo != ""){ ?>
									<div class="col-sm-6 form-group">
										<label for="departamento">Archivo</label>
										<div style="text-align:center; box-sizing:border-box; padding:20px">
											<?php
											$url = $baseUrl."/".$archivo;
											$ext = substr($url,-3);
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
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>