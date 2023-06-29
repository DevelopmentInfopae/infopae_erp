<?php
  	include '../../header.php';
  	set_time_limit (0);
  	ini_set('memory_limit','6000M');
  	$periodoActual = $_SESSION['periodoActual'];

  	if ($permisos['inventario'] == "0" || $_SESSION['p_inventory'] == '0' ) {
?><script type="text/javascript">
  	window.open('<?= $baseUrl ?>', '_self');
</script>
<?php exit(); }

else {
?><script type="text/javascript">
        const list = document.querySelector(".li_inventario");
        list.className += " active ";
    </script>
<?php
}
$nameLabel = get_titles('inventario', 'inventario', $labels);
$sincronizacionInicial = false;
$consultaSincronizacion = " show tables like 'inventarios_bodegas_enc' ";
$result = $Link->query($consultaSincronizacion) or die ('Error al consultar existencia de tablas inventarios: '. mysqli_error($Link));
$existe = $result->num_rows;
if ($existe == 0) { $sincronizacionInicial = true ; } 

if ($sincronizacionInicial == false) {
    $consultaMunicipios = " SELECT DISTINCT(u.codigoDANE), u.Ciudad 
                            FROM ubicacion u 
                            INNER JOIN inventarios_bodegas_enc i ON i.municipio = u.codigoDANE 
                            ";
    if ($_SESSION['p_Municipio'] == 0) {
        $consultaMunicipios .= " WHERE u.codigoDANE LIKE '" .$_SESSION['p_CodDepartamento']. "%' AND ETC = 0 "; 
    }
    if ($_SESSION['p_Municipio'] != 0) {
        $consultaMunicipios .= " WHERE u.codigoDANE = '" .$_SESSION['p_Municipio']. "'"; 
    }
    // exit(var_dump($consultaMunicipios));
    $respuestaUbicacion = $Link->query($consultaMunicipios) or die ('Error al consultar los municipios LN 29');
    if ($respuestaUbicacion->num_rows > 0) {
        while ($dataUbicacion = $respuestaUbicacion->fetch_assoc()) {
            $municipios[$dataUbicacion['codigoDANE']] = $dataUbicacion['Ciudad'];
        }
    }

    $consultaBodegas = " SELECT b.ID AS cod_sede, 
                                b.NOMBRE AS nom_sede
                            FROM bodegas b
                            INNER JOIN inventarios_bodegas_enc i ON i.bodega = b.ID ";
    if (isset($_GET['municipio']) && $_GET['municipio'] != 0 ) {
        $consultaBodegas .= " WHERE municipio = '" .$_GET['municipio']. "'";
    } else{
        $consultaBodegas .= " WHERE RESPONSABLE != '' ";
    }
    $respuestaBodegas = $Link->query($consultaBodegas) or die ('Error al consultar las bodegas');
    if ($respuestaBodegas->num_rows > 0) {
        while ($dataBodegas = $respuestaBodegas->fetch_assoc()) {
            $bodegas[$dataBodegas['cod_sede']] = $dataBodegas['nom_sede']; 
        }
    }

    $consultaTipoAlimento = " SELECT Id, Descripcion FROM tipo_despacho ";
    $respuestaTipoAlimento = $Link->query($consultaTipoAlimento) or die ('Error al consultar el tipo de alimento Ln 49');
    if ($respuestaTipoAlimento->num_rows > 0) {
        while ($dataTipoAlimento = $respuestaTipoAlimento->fetch_assoc()) {
            $tipoAlimento[$dataTipoAlimento['Id']] = $dataTipoAlimento['Descripcion'];
        }
    }

    $consultaComplementos = " SELECT CODIGO FROM tipo_complemento WHERE ValorRacion > 0 ";
    $respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complementos Ln 55');
    if ($respuestaComplementos->num_rows > 0) {
        while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
            $complementos[] = $dataComplementos['CODIGO'];
        }
    }
    $consultaAlimentos = " SELECT   ( SELECT nombre FROM bodegas WHERE ID = e.bodega) AS nombre, 
                                    e.synchronization_date AS fecha_sinc,
                                    p.Codigo, 
                                    p.Descripcion, 
                                    p.NombreUnidad2,
                                    e.complemento,
                                    e.bodega,
                                    if(i.cantidad IS NULL, 0, i.cantidad) AS 'cantidad', 
                                    if(i.fecha_entrada IS NULL, 'N/A', i.fecha_entrada) AS 'fecha_entrada',
                                    if(i.fecha_salida IS NULL, 'N/A', i.fecha_salida) AS 'fecha_salida'
                                FROM productos$periodoActual p
                                LEFT JOIN inventarios_bodegas_det i ON p.codigo = i.codigo
                                INNER JOIN inventarios_bodegas_enc e ON e.id = i.id_bodega
                                WHERE p.TipodeProducto = 'Alimento' AND nivel = '3' ";
    if (!isset($_GET['bodega'])) {
        $consultaAlimentos .= " AND e.bodega = '1' ";
    }  
    if (isset($_GET['bodega']) && $_GET['bodega'] != '' ) {
        $consultaAlimentos .= " AND e.bodega = '" .$_GET['bodega']. "'";
    }                          
    if (isset($_GET['municipio']) && $_GET['municipio'] != '' ) {
        $consultaAlimentos .= " AND e.municipio = '" .$_GET['municipio']. "'";
    }     
    if ( (isset($_GET['municipio']) && $_GET['municipio'] == '') && (isset($_GET['bodega']) && $_GET['bodega'] == '') ) {
        $consultaAlimentos .= " AND e.bodega = '1' ";
    }     
    if (isset($_GET['complemento']) && $_GET['complemento'] != '' ) {
        $consultaAlimentos .= " AND e.complemento = '" .$_GET['complemento']. "'";
    }    
    if (isset($_GET['tipoAlimento']) && $_GET['tipoAlimento'] != '' && $_GET['tipoAlimento'] != 99  ) {
        $consultaAlimentos .= " AND p.tipoDespacho = '" .$_GET['tipoAlimento']. "'";
    }
    $consultaAlimentos .= " ORDER BY p.Descripcion ASC ";                              
    $respuestaAlimentos = $Link->query($consultaAlimentos) or die ('Error al consultar los alimentos LN 65');
    if ($respuestaAlimentos->num_rows > 0) {
        while ($dataAlimentos = $respuestaAlimentos->fetch_assoc()) {
            $alimentos[] = $dataAlimentos;
            $nameWarehouse = $dataAlimentos['nombre'];
            $sinc = $dataAlimentos['fecha_sinc'];
            $cod_bodega = $dataAlimentos['bodega'];
        }
    }

    $meses = array( '01' => "Enero", 
                    "02" => "Febrero", 
                    "03" => "Marzo", 
                    "04" => "Abril", 
                    "05" => "Mayo", 
                    "06" => "Junio", 
                    "07" => "Julio", 
                    "08" => "Agosto", 
                    "09" => "Septiembre", 
                    "10" => "Octubre", 
                    "11" => "Noviembre", 
                    "12" => "Diciembre");

    $consultam= "SHOW TABLES LIKE 'despachos_enc%' ";  
    $respuesta=$Link->query($consultam);
    if ($respuesta->num_rows > 0) {
        while ($data = $respuesta->fetch_assoc()) {
            $aux = array_values($data);
            $aux=substr(($aux[0]), 13, -2);
            $mess[]=$aux;   
        }
    }
}
?>

<?php if($sincronizacionInicial == true): ?>

    <style>
        .iniciarSinc{
            display: block;
            width: 70px;
            height: 70px;
            border-radius: 50%
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
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h2>Sincronización de bodegas</h2>
				</div>
				<div class="ibox-content contentBackground contentSinc">
                    <div class="col-lg-12">
                        <div class="col-md-11">
                            <br>
                            <div class="progress progress-striped active">
                                <div id='bar' class="progress-bar" role="progressbar"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                    style="width: 0%">
                                    <span class="sr-only">45% completado</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary btn-xs iniciarSinc"><b>Iniciar</b></button>
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div>


<?php else: ?>
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
        <div class="col-lg-4">
            <div class="title-action">
                <?php if ($_SESSION['perfil'] == "0" || $permisos['inventario'] == "2"): ?>
                    <a href="#" id="importarCantidades" class="btn btn-primary btn-outline" ><i class="fa fa-upload"></i> Importar cantidades</a>
                    <a href="#" id="descargarPlantillaProductos" class="btn btn-primary btn-outline " ><i class="fa fa-download"></i> Descarga plantilla</a>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
		    <div class="col-lg-12">
			    <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-sm-11">
                                <h2 style="display:inline;">Parámetros de Consulta</h2>
                            </div>
                            <div class="col-sm-1" id="father">
                                <div class="" id="loaderAjax">
                                    <i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i>
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>    
				    <div class="ibox-content contentBackground">
					    <form class="col-lg-12" action="index.php" name="formInventario" id="formInventario" method="GET" >
						    <div class="row">
                                <div class="col-md-3 col-sm-12 form-group">
                                    <label for="municipio">Municipio</label>
                                    <select name="municipio" id="municipio" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <?php
                                            foreach ($municipios as $key => $value) {
                                        ?>
                                                <option value="<?= $key ?>" <?= ( isset($_GET['municipio']) && $_GET['municipio'] == $key ) ? 'selected' : '' ?> > <?= $value ?> </option>
                                        <?php        
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-md-3 col-sm-12 form-group">
                                    <label for="bodega">Bodega</label>
                                    <select name="bodega" id="bodega" class="form-control" required >
                                        <option value="">Seleccione...</option>
                                        <?php
                                            foreach ($bodegas as $key => $value) { 
                                        ?>
                                                <option value="<?= $key ?>" <?= ( isset($_GET['bodega']) && $_GET['bodega'] == $key ) ? 'selected' : '' ?>><?= $value ?> </option>
                                        <?php
                                            }
                                        ?>
                                    </select>
                                </div>
                            
                                <?php
                                    if ($_SESSION['p_inventory'] == 2) {
                                ?>
                                        <div class="col-md-3 col-sm-12 form-group">
                                            <label for="complemento">Complemento</label>
                                            <select name="complemento" id="complemento" class="form-control">
                                                <option value="">Seleccione...</option>
                                                <?php
                                                    foreach ($complementos as $key => $value) {
                                                ?>
                                                        <option value="<?= $value ?>" <?= ( isset($_GET['complemento']) && $_GET['complemento'] == $value ) ? 'selected' : '' ?>><?= $value ?> </option>
                                                <?php        
                                                    }
                                                ?>
                                            </select>
                                        </div>    
                                <?php        
                                    }
                                ?>       

                                <div class="col-md-3 col-sm-12 form-group">
                                    <label for="tipoAlimento">Tipo de Alimento</label>
                                    <select name="tipoAlimento" id="tipoAlimento" class="form-control">
                                        <option value="">Seleccione...</option>
                                        <?php
                                            foreach ($tipoAlimento as $key => $value) {
                                        ?>
                                                <option value="<?= $key ?>" <?= ( isset($_GET['tipoAlimento']) && $_GET['tipoAlimento'] == $key ) ? 'selected' : '' ?>><?= $value ?> </option>

                                        <?php        
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div><!--  row -->
                            <div class="row">
                                <div class="col-sm-3 form-group">
                                    <button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong><i class="fa fa-search"></i> Buscar</strong></button>
                                </div>
                            </div>
                        </form>        
                    </div><!--  ibox-content -->
                </div><!--  ibox -->
            </div><!--  col-lg-12 -->
        </div><!--  row -->
    </div><!--  wrapper -->

    <?php
        if (isset($alimentos) && !empty($alimentos)) {
    ?>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins" id="contenedorInventarios" >
                            <div class="ibox-content contentBackground">
                                <div class="h3"><b>Bodega</b> - <?= $nameWarehouse ?></div>
                                <?php if(isset($sinc) && $sinc != '' && $sinc > '0000-00-00'): ?>
                                    <div class="h4"><b>Sincronización</b> - <label style = "color : #31C95F;"> <?= $sinc ?></label></div>
                                <?php endif; ?>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered selectableRows" id="box-table-inventarios" >
                                        <thead>
                                            <tr style="height: 4em;">
                                                <th class="text-left">Código</th>
                                                <th class="text-left">Descripción</th>
                                                <th class="text-center">Unidad Medida</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-center">Complemento</th>
                                                <th class="text-center">Fecha Ingreso</th>
                                                <th class="text-center">Fecha Salida</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <br>
                                        <tbody>
                                                <?php
                                                    foreach ($alimentos as $key => $value) {
                                                ?>
                                                        <tr>
                                                            <td class="text-left"><?= $value['Codigo'] ?></td>
                                                            <td class="text-left"><?= $value['Descripcion'] ?></td>
                                                            <td class="text-center"><?= $value['NombreUnidad2'] ?></td>
                                                            <td class="text-center"><?= number_format($value['cantidad'],2,',','.') ?></td>
                                                            <td class="text-center"><?= (isset($value['complemento']) && $value['complemento'] != '' ) ? $value['complemento'] : 'Todos'?></td>
                                                            <td class="text-center"><?= $value['fecha_entrada'] ?></td>
                                                            <td class="text-center"><?= $value['fecha_salida'] ?></td>
                                                            <td class="text-center">
                                                                <div class="btn-group">
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
                                                                            Acciones
                                                                            <span class="caret"></span>
                                                                        </button>
                                                                        <ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
                                                                            <?php if ($_SESSION['perfil'] == "0" || $permisos['inventarios'] == "2") { 
                                                                                $aux = $value['Codigo'];    
                                                                            ?>
                                                                                <li><a onclick="movimientos(<?= "'" .$aux."'" ?>, <?= $cod_bodega ?>, <?= "'" .$sinc. "'" ?> )"><span class="fa fa-file-pdf-o"></span>  Movimientos</a></li>                            
                                                                                <li><a onclick="sincProduct(<?= "'" .$aux."'" ?>, <?= $cod_bodega ?>, <?= "'" .$sinc. "'" ?> )"><span class="fa fa-refresh"></span>  Sincronizar Alimento</a></li>                            
                                                                            <?php } ?>
                                                                            <!-- <li><a onclick="#"><span class="fas fa-pencil-alt"></span>  Ver</a></li> -->
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                <?php
                                                    }
                                                ?>
                                        </tbody>
                                        <tfoot>
                                            <tr style="height: 4em;">
                                                <th class="text-left">Código</th>
                                                <th class="text-left">Descripción</th>
                                                <th class="text-center">Unidad Medida</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-center">Complemento</th>
                                                <th class="text-center">Fecha Ingreso</th>
                                                <th class="text-center">Fecha Salida</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>      
                            </div><!--  ibox-content -->
                        </div><!--  ibox -->
                    </div><!--  col-lg-12 -->
                </div><!--  row -->
            </div><!--  wrapper -->
    <?php    
        }else if(isset($_GET) && !empty($_GET) && empty($alimentos) ){ 
    ?>        
            <div class="h3 text-center">No existen alimentos registrados en esta bodega</div>
    <?php        
        }
    ?>

    <!-- Ventana formulari para la cargar el inventario -->
    <div class="modal inmodal fade" id="ventanaFormularioCargarCantidades" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-info" style="padding: 15px;">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h3><i class="fa fa-upload fa-lg" aria-hidden="true"></i> Importar Inventario  </h3>
                </div>
                <div class="modal-body">
                    <form action="" name="formSubirArchivoInventario" id="formSubirArchivoInventario">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="municipioImport">Municipio</label><br>
                                    <select style="width: 100%;" class="form-control" name="municipioImport" id="municipioImport" required>
                                        <option value="">Seleccione...</option>
                                        <?php
                                            foreach ($municipios as $key => $value) {
                                        ?>
                                                <option value="<?= $key ?>" > <?= $value ?> </option>
                                        <?php        
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bodegaImport">Bodega</label><br>
                                    <select style="width: 100%;" class="form-control" name="bodegaImport" id="bodegaImport" required>
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                            <?php
                                if ($_SESSION['p_inventory'] == 2) {
                            ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="complementoImport"> Complemento </label>
                                            <select style="width : 100%;" name="complementoImport" id="complementoImport" class="form-control">
                                                <option value="">Seleccione...</option>
                                                <?php
                                                    foreach ($complementos as $key => $value) {
                                                ?>
                                                        <option value="<?= $value ?>"><?= $value ?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                            <?php
                                }
                            ?>               
                        </div>
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="archivoInventario">Archivo</label>
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput">
                                            <i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span>
                                        </div>
                                        <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Seleccionar archivo</span><span class="fileinput-exists">Cambiar</span>
                                            <input type="file" name="archivoInventario" id="archivoInventario" accept=".csv, .xlsx" required>
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Borrar</a>
                                    </div>
                                    <label for="archivoInventario" class="error" style="display: none;"></label>
                                </div>
                                <label class="text-warning">Para mayor eficacia es mejor subir el archivo con extensión .CSV </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal"><span class="fas fa-times"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary btn-sm" id="subirArchivoInventario"><span class="fas fa-check"></span> Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventana formulari para la sincronizar el inventario -->
    <div class="modal inmodal fade" id="ventanaFormularioSincronizarCantidades" role="dialog" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-info" style="padding: 15px;">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h3><i class="fas fa-exchange-alt fa-lg" aria-hidden="true"></i> Sincronizar cantidades  </h3>
                </div>
                <div class="modal-body">
                    <form action="" name="formSincronizarCantidades" id="formSincronizarCantidades">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="municipioSincronizacion">Municipio*</label><br>
                                    <select style="width: 100%;" class="form-control" name="municipioSincronizacion" id="municipioSincronizacion" required >
                                        <option value="">Seleccione...</option>
                                        <?php
                                            foreach ($municipios as $key => $value) {
                                        ?>
                                                <option value="<?= $key ?>" > <?= $value ?> </option>
                                        <?php        
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bodegaSincronizacion">Bodega</label><br>
                                    <select style="width: 100%;" class="form-control" name="bodegaSincronizacion" id="bodegaSincronizacion" >
                                        <option value="">Seleccione...</option>
                                    </select>
                                </div>
                            </div>
                            <?php
                                if ($_SESSION['p_inventory'] == 2) {
                            ?>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="complementoSincronizacion"> Complemento </label>
                                            <select style="width : 100%;" name="complementoSincronizacion" id="complementoSincronizacion" class="form-control">
                                                <option value="">Seleccione...</option>
                                                <?php
                                                    foreach ($complementos as $key => $value) {
                                                ?>
                                                        <option value="<?= $value ?>"><?= $value ?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                            <?php
                                }else{
                            ?>
                                    <input type="hidden" name="complementoSincronizacion" id="complementoSincronizacion" value = ''>
                            <?php        
                                }
                            ?>               
                           <div class="col-md-4">
                                <label for="mesSincronizacion">Mes*</label>
                                <select  style="width : 100%;" name="mesSincronizacion" id="mesSincronizacion" required>
                                    <option value="">Seleccione</option>
                                    <?php
                                        foreach ($mess as $key => $value) {
                                    ?>
                                            <option value="<?= $value ?>"><?= $meses[$value] ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                           </div>

                           <div class="col-md-4">
                                <label for="semanaSincronizacion">Semana*</label>
                                <select style="width : 100%;" name="semanaSincronizacion" id="semanaSincronizacion" required>
                                    <option value="">Seleccione</option>
                                </select>
                           </div>

                           <div class="col-md-4">
                                <label for="diaSincronizacion">Día*</label>
                                <select style="width : 100%;" name="diaSincronizacion" id="diaSincronizacion" required>
                                    <option value="">Seleccione</option>
                                </select>
                           </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal"><span class="fas fa-times"></span> Cancelar</button>
                    <button type="button" class="btn btn-primary btn-sm" id="sincronizarCantidades"><span class="fas fa-check"></span> Aceptar</button>
                </div>
            </div>
        </div>
    </div>                            

<?php endif; ?>
<div id="contenedor_movimientos"></div>
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
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?= $baseUrl; ?>/modules/inventarios/js/inventarios.js"></script>


<script type="text/javascript">
    var buttonCommon = {
        exportOptions: {
            format: {
                body: function ( data, row, column, node ) {
                    return column === 3 ?
                    parseFloat(data.replace( ',', '.' )) :
                    data;
                }
            },
            columns: [ 0, 1, 2 ,3, 4, 5, 6]
        }
    };
    dataset1 = $('#box-table-inventarios').DataTable({
        pageLength: 25,
        responsive: true,
        order : [ 2 , 'asc' ],
        dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
        buttons : [
                    $.extend( 
                        true, {}, buttonCommon, {
                            extend: 'excel',
                            title:'Cantidades inventario', 
                            className:'btnExportarExcel'
                        } 
                    ),
                ],
        oLanguage: {
            sLengthMenu: 'Mostrando _MENU_ registros por página',
            sZeroRecords: 'No se encontraron registros',
            sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
            sInfoFiltered: '(Filtrado desde _MAX_ registros)',
            sSearch:         'Buscar: ',
            oPaginate:{
                sFirst:    'Primero',
                sLast:     'Último',
                sNext:     'Siguiente',
                sPrevious: 'Anterior'
            }
        }
    });
    var btnAcciones =   '<div class="dropdown pull-right" id="">' +
                            '<button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">'+
                                'Acciones <span class="caret"></span>'+
                            '</button>'+
                            '<ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">'+
                                '<li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li>'+
                                '<li><a href="#" id="sincronizar_bodegas"><i class="fa fa-refresh"></i> Sincronizar bodegas </a></li>'+
                                '<li><a href="#" id="cronjob"><i class="fas fa-exchange-alt"></i> Sincronizar alimentos </a></li>'+
                            '</ul>'+
                        '</div>';
    $('.containerBtn').html(btnAcciones);
    

  

</script>

