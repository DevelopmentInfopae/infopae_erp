<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$codigoMenu = '';
$preparacionOriginal = "";

if(isset($_POST['menu']) && $_POST['menu'] != ''){
	$codigoMenu = mysqli_real_escape_string($Link, $_POST['menu']);
}

//var_dump($_POST);


// Consultando en ficha tecnicamediante el codigo para encontrar el id de la ficha tecnica.
$consulta = " SELECT Id FROM fichatecnica f WHERE f.Codigo = \"$codigoMenu\" ";
$resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$idFichaTecnicaMenu = $row["Id"];
}

$consultaFichaTecnica = " SELECT f.id as idFichaTecnica,fd.* FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = \"$idFichaTecnicaMenu\" ";

//$consultaFichaTecnica = " select * from fichatecnica where Codigo = \"$preparacion\" ";
//echo $consultaFichaTecnica;

$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
if ($resultadoFichaTecnica->num_rows > 0) {
        $cntFTD = 0;

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
								<!-- <thead> <tr> <th>Preparación</th> <th></th> </tr> </thead> -->
			                  	<tbody>
				                	<?php while ($fichatecnicadet = $resultadoFichaTecnica->fetch_assoc()) { 
				                  		$cntFTD++; ?>	              
				                        <tr id="filaProductoFichaTecnicaDet<?php echo $cntFTD; ?>" class="productoFichaTecnicaDet">
				                            <td>
				                            	<input type="text" class="form-control" name="" id=""  value="<?= $fichatecnicadet['Componente']; ?>" readonly>
				                            </td> 
				                        </tr>
				                  	<?php } ?>
			                    </tbody>
			                  </table>
							</div>
						</div>
					</div>
				</div>
			</div>


	<?php
	$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
	if ($resultadoFichaTecnica->num_rows > 0) {
        $cntFTD = 0;
		?>	

		<div class="wrapper wrapper-content  animated fadeInRight">
			<div class="row">
				<div class="col-sm-12">
					<div class="ibox">
						<div class="ibox-title">
							<h5>Ajuste a la preparación</h5> 
						</div>
						<div class="ibox-content">
							<table class="table tablaAjuste">
								<thead>
									<tr>
										<th>Preparación</th>
										<th></th>
									</tr>
								</thead>
				                <tbody>
			                		<?php while ($fichatecnicadet = $resultadoFichaTecnica->fetch_assoc()) { 
			                  			$cntFTD++; ?>			              
			                        	<tr id="filaProductoFichaTecnicaDet<?php echo $cntFTD; ?>" class="productoFichaTecnicaDet productoFichaTecnicaDetA productoAjuste<?= $cntFTD ?>" indice="<?= $cntFTD ?>">
			                            	<td>
			                            		<input type="hidden" name="IdFTDet[<?php echo $cntFTD ?>]" value="<?php echo $fichatecnicadet['Id'] ?>">
			                              		<select class="form-control producto" name="productoFichaTecnicaDet[<?php echo $cntFTD ?>]" id="productoFichaTecnicaDet<?php echo $cntFTD ?>" onchange="obtenerUnidadMedidaProducto(this, <?php echo $cntFTD ?>);" required>
			                                		<option value="<?php echo $fichatecnicadet['codigo']; ?>"><?php echo $fichatecnicadet['Componente']; ?></option>
			                              		</select>
			                            	</td>
			                            	<td>
			                                	<span class="btn btn-danger btn-sm btn-outline quitarProducto"  title='Eliminar de la composición'><span class="fa fa-trash"></span></span>
			                            	</td>
			                          	</tr>
			                  		<?php } ?>
				                </tbody>
			            	</table>

							<a class="btn btn-primary" onclick="anadirProducto();"><span class="fa fa-plus"></span></a>
							<a class="btn btn-primary" id="borrarProducto" onclick="borrarProducto();" style="display: none;"><span class="fa fa-minus"></span></a>
				                                    
							<div class="hr-line-dashed"></div>
							<div class="form-group row">
								<div class="col-sm-12">
									<button class="btn btn-primary btnGuardar" type="button">Guardar</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?> 
<?php } ?>