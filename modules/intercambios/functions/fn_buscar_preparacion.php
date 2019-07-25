<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$preparacion = '';
$preparacionOriginal = "";

if(isset($_POST['preparacion']) && $_POST['preparacion'] != ''){
		$preparacion = mysqli_real_escape_string($Link, $_POST['preparacion']);
}

//var_dump($_POST);

$consultaFichaTecnica = " select * from fichatecnica where Codigo = \"$preparacion\" ";
//echo $consultaFichaTecnica;
$resultadoFichaTecnica = $Link->query($consultaFichaTecnica);
if ($resultadoFichaTecnica->num_rows > 0) {
	$fichaTecnica = $resultadoFichaTecnica->fetch_assoc();
    $consultaFichaTecnicaDet = "select * from fichatecnicadet where IdFT = ".$fichaTecnica['Id'];
    //echo $consultaFichaTecnicaDet;
    $resultadoFichaTecnicaDet = $Link->query($consultaFichaTecnicaDet);
    if ($resultadoFichaTecnicaDet->num_rows > 0) {
        $cntFTD = 0;
        $preparacionOriginal .= "";
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
										<th>Producto</th>
										<th class="text-center">Unidad de medida</th>
										<!-- <th class="text-center">Cantidad</th> -->
										<th class="text-center">Peso Bruto</th>
										<th class="text-center">Peso Neto</th>
									</tr>
								</thead>
			                  	<tbody>
			                	<?php while ($fichatecnicadet = $resultadoFichaTecnicaDet->fetch_assoc()) { 
			                  		$cntFTD++; ?>
			              
			                        <tr id="filaProductoFichaTecnicaDet<?php echo $cntFTD; ?>" class="productoFichaTecnicaDet">
			                            <td>
			                            	<input type="text" class="form-control" name="" id=""  value="<?= $fichatecnicadet['Componente']; ?>" readonly>
			                            </td> 
			                            <td class="datoPreparado">
			                              <input type="text" class="form-control text-center" name="unidadMedidaProducto[<?php echo $cntFTD ?>]" id=""  value="<?php echo $fichatecnicadet['UnidadMedida']; ?>" readonly>
			                            </td>



			       <!--                      <td class="datoPreparado">
			                              <input type="number" min="0" class="form-control text-center" name="" id=""  value="<?php //echo $fichatecnicadet['Cantidad']; ?>" onchange="cambiarPesos(this, <?php //echo $cntFTD ?>);" step=".0001" readonly>
			                            </td> -->



			                            <td class="datoPreparado">
			                              <input type="number" min="0" class="form-control text-center" name="" id="" value="<?php echo $fichatecnicadet['PesoBruto']; ?>" step=".0001" readonly>
			                            </td>
			                            <td class="datoPreparado">
			                            	<input type="number" min="0" class="form-control text-center" name="" id="" value="<?php echo $fichatecnicadet['PesoNeto']; ?>" step=".0001" readonly> 
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

		
	<?php } ?> 

	<?php
	    $consultaFichaTecnicaDet = "select * from fichatecnicadet where IdFT = ".$fichaTecnica['Id'];
    //echo $consultaFichaTecnicaDet;
    $resultadoFichaTecnicaDet = $Link->query($consultaFichaTecnicaDet);
    if ($resultadoFichaTecnicaDet->num_rows > 0) {
        $cntFTD = 0;
        $preparacionOriginal .= "";
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
										<th>Producto</th>
										<th class="datoPreparado">Unidad de medida</th>
										<!-- <th class="datoPreparado">Cantidad</th> -->
										<th class="datoPreparado">Peso Bruto</th>
										<th class="datoPreparado">Peso Neto</th>
										<th></th>
									</tr>
								</thead>
			                  	<tbody>
			                	<?php while ($fichatecnicadet = $resultadoFichaTecnicaDet->fetch_assoc()) { 
			                  		$cntFTD++; ?>
			              
			                        <tr id="filaProductoFichaTecnicaDet<?php echo $cntFTD; ?>" class="productoFichaTecnicaDet productoFichaTecnicaDetA productoAjuste<?= $cntFTD ?>" indice="<?= $cntFTD ?>">
			                            <td>
			                            	<input type="hidden" name="IdFTDet[<?php echo $cntFTD ?>]" value="<?php echo $fichatecnicadet['Id'] ?>">
			                              <select class="form-control producto" name="productoFichaTecnicaDet[<?php echo $cntFTD ?>]" id="productoFichaTecnicaDet<?php echo $cntFTD ?>" onchange="obtenerUnidadMedidaProducto(this, <?php echo $cntFTD ?>);" required>
			                                <option value="<?php echo $fichatecnicadet['codigo']; ?>"><?php echo $fichatecnicadet['Componente']; ?></option>
			                              </select>
			                            </td>
			                            <td class="datoPreparado">
			                              <input type="text" class="form-control text-center unidad" name="unidadMedidaProducto[<?php echo $cntFTD ?>]" id="unidadMedidaProducto<?php echo $cntFTD ?>"  value="<?php echo $fichatecnicadet['UnidadMedida']; ?>" readonly >
			                            </td>

			                            <!-- <td class="datoPreparado">
			                              <input type="number" min="0" class="form-control text-center" name="cantidadProducto[<?php //echo $cntFTD ?>]" id="cantidadProducto<?php //echo $cntFTD ?>"  value="<?php //echo $fichatecnicadet['Cantidad']; ?>" onchange="cambiarPesos(this, <?php //echo $cntFTD ?>);" step=".0001">
			                            </td> -->

			                            <td class="datoPreparado">
			                              <input type="number" min="0" class="form-control text-center pesoBruto" name="pesoBrutoProducto[<?php echo $cntFTD ?>]" id="pesoBrutoProducto<?php echo $cntFTD ?>" value="<?php echo $fichatecnicadet['PesoBruto']; ?>" step=".0001" required>
			                            </td>
			                            <td class="datoPreparado">
			                              <input type="number" min="0" class="form-control text-center pesoNeto" name="pesoNetoProducto[<?php echo $cntFTD ?>]" id="pesoNetoProducto<?php echo $cntFTD ?>" value="<?php echo $fichatecnicadet['PesoNeto']; ?>" step=".0001" required>
			                            </td>
			                            
			                            <td>
			                              

			                              <span class="btn btn-danger btn-sm btn-outline quitarProducto"  title='Eliminar de la composición'><span class="fa fa-trash"></span></span>


			                            </td>
			                           
			                          </tr>
			                  <?php } ?>
			                    </tbody>
			                </table>
			                <a class="btn btn-primary" onclick="anadirProducto();"><span class="fa fa-plus"></span></a>
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


										<div class="col-sm-6 form-group">
											<label for="departamento">Fecha de vencimiento</label>
											<div class="input-group date">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span> <input name="fechaVencimiento>" id="fechaVencimiento" type="text" class="form-control inputFechaVencimiento datepick" value="" required>
											</div>
										</div>


										<div class="col-sm-6 form-group">
											<label for="departamento">Archivo</label>
											<div class="fileinput fileinput-new input-group" data-provides="fileinput"> 
												<div class="form-control" data-trigger="fileinput">
													<i class="glyphicon glyphicon-file fileinput-exists"></i> 
													<span class="fileinput-filename"></span>
												</div> 
												<span class="input-group-addon btn btn-default btn-file">
													<span class="fileinput-new">Elegir archivo</span>
													<span class="fileinput-exists">Change</span>
													<input type="file" name="foto" id="foto" accept="image/jpeg,image/gif,image/png,application/pdf">
												</span> 
												<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a> 
											</div>
										</div>

										<div class="col-sm-12 form-group">
											<label for="observaciones">Observaciones</label>
											<textarea name="observaciones" id="observaciones" class="form-control" rows="8" cols="80"></textarea>
										</div>

									</div>
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
			</div>
		</div>





















	<?php } ?> 

<?php } ?>

















































<?php




// $opciones = "<option value=\"\">Seleccione uno</option>";

// $consulta = " select distinct(SEMANA) as semana from planilla_semanas where mes = \"$mes\" order by semana asc ";
// // echo $consulta;

// $resultado = $Link->query($consulta) or die ('No se pudieron cargar los muunicipios. '. mysqli_error($Link));
// if($resultado->num_rows >= 1){
// 	$respuesta = 1;
// 	while($row = $resultado->fetch_assoc()){
// 		$id = $row["semana"];
// 		$valor = $row["semana"];

// 		$opciones .= "<option value=\"$id\"";
// 		$opciones .= ">";
// 		$opciones .= "$valor</option>";
// 	}
// }if($resultado){
// 	$resultadoAJAX = array(
// 		"estado" => 1,
// 		"mensaje" => "Se ha cargado con exito.",
// 		"opciones" => $opciones
// 	);
// }else{
// 	$resultadoAJAX = array(
// 		"estado" => 0,
// 		"mensaje" => "Se ha presentado un error."
// 	);
// }
// echo json_encode($resultadoAJAX);