<?php 
$titulo = 'Editar Movimiento';
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
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Editar Movimiento</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
        <a href="<?php echo $baseUrl; ?>/modules/movimientos/movimientos.php">Movimientos</a>
      </li>
      <li class="active">
        <strong>Editar Movimiento</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      -->
      <span class="btn btn-primary" onclick="guardar_cambio_movimiento(0)"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Guardar</span>
      <span class="btn btn-primary" onclick="guardar_cambio_movimiento(1)"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Aprobar y Guardar </span>  
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">

<?php
  //var_dump($_POST);
  $id = $_POST['formEditarId'];
  $tabla = $_POST['formEditarTabla'];

  $consulta = " select * from productosmov$tabla where id = $id and Aprobado != 1 ";
  //echo $consulta;
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
    //var_dump($row);
  }//Termina el if que valida que si existan resultados

  $documento = htmlspecialchars($row['Documento']);
  $documentoTipo = $documento;
  $tipoDocumento = htmlspecialchars($row['Documento']);
  $numero = htmlspecialchars($row['Numero']);
  $concepto = htmlspecialchars($row['Concepto']);
  $tipo = htmlspecialchars($row['Tipo']);
  $fecha = htmlspecialchars($row['FechaMYSQL']);
  $fecha = date_create($fecha);
  $fecha = date_format($fecha, 'd/m/Y h:i:s a');
  $bodegaOrigen = htmlspecialchars($row['BodegaOrigen']);
  $bodegaDestino = htmlspecialchars($row['BodegaDestino']);
  $nitcc = htmlspecialchars($row['Nitcc']);
  $nombre = htmlspecialchars($row['Nombre']);
  $tipoTransporte = htmlspecialchars($row['TipoTransporte']);
  $responsable = htmlspecialchars($row['ResponsableRecibe']);
  $placa = htmlspecialchars($row['Placa']);
  $total = htmlspecialchars($row['ValorTotal']);

  $consulta = " select Id from documentos where Tipo = '$documento' ";
  //echo $consulta;
  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $row = $resultado->fetch_assoc();
    $documento = $row['Id'];
  }//Termina el if que valida que si existan resultados
?>














 
          <form class="col-lg-12" action="" name="nuevoDocumento" id="nuevoDocumento" method="post">
            <input type="hidden" name="periodoActualCompleto" id="periodoActualCompleto" value="<?php echo $_SESSION["periodoActualCompleto"];   ?>">
            <input type="hidden" name="idMovimiento" id="idMovimiento" value="<?php echo $id; ?>">
            <input type="hidden" name="numeroOrigen" id="numeroOrigen" value="0" readonly="">
            <input type="hidden" name="docOrigen" id="docOrigen" value="Manual">
   
            

            <div class="row">

              <div class="col-sm-3 form-group">
                <label for="documento">Documento</label> 
                <input type="hidden" name="documento" id="documento" value="<?php echo $documento; ?>">
                <select class="form-control" name="documentoNm" id="documentoNm" onchange="buscar_tipo_movimiento()">
                  <option value="">Seleccione uno</option>
                  <?php
              $consulta = " select id,descripcion from documentos ";
              $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
              if($resultado->num_rows >= 1){
                while($row = $resultado->fetch_assoc()) { ?>
                  <option value="<?php echo $row["id"]; ?>" <?php  if ((isset($_POST['documento']) && ($_POST['documento'] == $row["id"]) || $documento == $row["id"]) ) { echo ' selected '; } ?>   ><?php echo $row["descripcion"]; ?></option>
                  <?php
                }// Termina el while
              }//Termina el if que valida que si existan resultados
              ?>
                </select>
              </div><!-- /.col --> 


              <div class="col-sm-3 form-group">
                <label for="tipo">Tipo</label> 
                <select class="form-control" name="tipo" id="tipo">
              <option value="">Seleccione uno</option>
              <?php
                $consulta = " SELECT * FROM tipomovimiento WHERE Documento IN (SELECT Tipo FROM documentos WHERE Id = $documento) ";
                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($resultado->num_rows >= 1){
                  while($row = $resultado->fetch_assoc()) { ?>
                    <option value="<?php echo $row["Id"]; ?>" <?php  if ($tipo == $row["Movimiento"]){ echo ' selected '; } ?>   ><?php echo $row["Movimiento"]; ?></option>
                    <?php
                  }// Termina el while
                }//Termina el if que valida que si existan resultados
              ?>
            </select>
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="numero">Numero</label> 
                <input class="form-control" type="text" name="numero" id="numero" readonly="" value="<?php echo $numero; ?>" readonly="" style="text-align:center;">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="fecha">Fecha</label> 
                <input class="form-control" type="text" name="fecha" id="fecha" value="<?php echo $fecha; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="bodegaOrigen">Bodega Origen</label> 
                <select name="bodegaOrigen" id="bodegaOrigen" class="form-control bodegaOrigen" <?php if($documento == 3){ ?>disabled="disabled"<?php } ?>>
                  <option value="">Seleccione una</option>
                  <?php
                  $consulta = " select * from bodegas ";
                  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($resultado->num_rows >= 1){
                    while($row = $resultado->fetch_assoc()) { ?>
                      <option value="<?php echo $row["ID"]; ?>" <?php
                      if($bodegaOrigen == $row["ID"]){ echo ' selected '; } ?>><?php echo $row["NOMBRE"]; ?></option>
                      <?php
                    }// Termina el while
                  }//Termina el if que valida que si existan resultados
                  ?>
                </select>           
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="bodegaDestino">Bodega Destino</label> 
                <select name="bodegaDestino" id="bodegaDestino" class="form-control bodegaDestino" <?php if($documento == 9){ ?>disabled="disabled"<?php } ?>>
                  <option value="">Seleccione una</option>
                  <?php
                  $consulta = " select * from bodegas ";
                  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($resultado->num_rows >= 1){
                    while($row = $resultado->fetch_assoc()) { ?>
                      <option value="<?php echo $row["ID"]; ?>" <?php  if( $bodegaDestino == $row["ID"] ) { echo ' selected '; } ?>   ><?php echo $row["NOMBRE"]; ?></option>
                      <?php
                    }// Termina el while
                  }//Termina el if que valida que si existan resultados
                  ?>
                </select>           
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="nitcc">NIT/CC</label> 
                <input class="form-control" type="text" name="nitcc" id="nitcc" value="<?php echo $nitcc; ?>" autocomplete="off">
                <div id="suggestionsn" class="suggestions"> <div id="suggestionsContenedorn" class="suggestionsContenedor"></div> </div>
           
              </div><!-- /.col --> 

           


              <div class="col-sm-3 form-group">
                <label for="nombre">Nombre Tercero</label> 
                <div class="input-group">
                  <input class="form-control" type="text" name="nombre" id="nombre" value="<?php echo $nombre; ?>" autocomplete="off">
                  <div id="suggestions" class="suggestions"> 
                    <div id="suggestionsContenedor" class="suggestionsContenedor"></div> 
                  </div>
                  <span class="input-group-btn"> 
                    <button type="button" name="button" class="btn btn-info" onclick="listado(1)"> 
                      <i class="fa fa-search" aria-hidden="true"></i> 
                    </button>      
                  </span>
                </div>
              </div><!-- /.col --> 













              <div class="col-sm-3 form-group">
                <label for="tipoTransporte">Tipo Transporte</label> 
                <select class="form-control" name="tipoTransporte" id="tipoTransporte">

                  <option value="">Seleccione una</option>
                  <?php
                  $consulta = " select * from tipovehiculo ";
                  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($resultado->num_rows >= 1){
                    while($row = $resultado->fetch_assoc()) { ?>
                      <option value="<?php echo $row["Nombre"]; ?>" <?php if($tipoTransporte == $row['Nombre']){echo " selected ";} ?>><?php echo $row["Nombre"]; ?></option>
                      <?php
                    }// Termina el while
                  }//Termina el if que valida que si existan resultados
                  ?>










                </select>
              </div><!-- /.col -->  

              <div class="col-sm-6 form-group">
                <label for="conductor">Conductor</label> 
                <input type="text" name="conductor" id="conductor" value="<?php echo $responsable; ?>" class="form-control">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="placa">Placa</label> 
                <input class="form-control" type="text" maxlength="6" name="placa" id="placa" value="<?php echo $placa; ?>" placeholder="ABC123">
              </div><!-- /.col --> 
            </div><!-- /.row -->
            
            
            
            <div class="row">

              <div class="col-sm-3 form-group">
                <label for="codigoProducto">Código del Porducto / Codigo de Barras</label>
                <input class="form-control" type="text" name="codigoProducto" id="codigoProducto" value="" autocomplete="off">
                <input type="hidden" name="codigoBarras" id="codigoBarras" value="">
                <div id="suggestionsCP" class="suggestions"> <div id="suggestionsContenedorCP" class="suggestionsContenedor"></div> </div>
              </div><!-- /.col --> 

              <div class="col-sm-9 form-group">
                <label for="nombre">Producto</label> 
                <div class="input-group">
                  <input type="text" name="descripcionProducto" id="descripcionProducto" value="" class="form-control">
                <div id="suggestionsCB" class="suggestions"> <div id="suggestionsContenedorCB" class="suggestionsContenedor"></div> </div>
                  <span class="input-group-btn"> 
                    <button type="button" name="button" class="btn btn-info" onclick="listado(2)">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </button>

                    <button type="button" name="button" class="btn btn-primary" onclick="agregar_item_grilla()">Agregar producto</button>
                  </span>
                </div>
              </div><!-- /.col --> 

            </div><!-- /.row -->


            <div class="row">
              <div class="col-sm-12 form-group">
                <label for="concepti">Concepto</label> 
                <textarea class="form-control" name="concepto" id="concepto" rows="3" cols="40"><?php echo $concepto; ?></textarea>
              </div><!-- /.col --> 
            </div><!-- /.row -->



            <div class="row">
              <div class="col-sm-12 form-group">
                <div class="table-responsive">
                  <table width="100%" id="box-table-a" class="table table-striped table-bordered table-hover selectableRows">
        <thead>
          <tr>
          <th> </th>
          <th> Código </th>
          <th> Descripción </th>
          <th> B. Oríg </th>
          <th> B. Dest </th>
          <th> U. Medida </th>
          <th> Cant. U. Medida </th>
          <th> Costo Unitario </th>
          <th> Costo Total </th>
          <th> Lote </th>
          <th> Fecha V. </th>
          <th> Acciones </th>
        </tr>
        </thead>
        <tbody>
          <?php
            $item = 1;
            $consultaItems = " select * from productosmovdet$tabla where Documento = '$documentoTipo' and Numero = '$numero' ";
            $resultadoItems = $Link->query($consultaItems) or die ('Unable to execute query. '. mysqli_error($Link));
            if($resultadoItems->num_rows >= 1){
              while($row = $resultadoItems->fetch_assoc()) {
                $item = $row['Item'];
                $codigoProducto = $row['CodigoProducto'];
                $descripcion = $row['Descripcion'];
                $bodegaOrigen = $row['BodegaOrigen'];
                $bodegaDestino = $row['BodegaDestino'];
                $unidad = $row['Umedida'];
                $cantUMedida = $row['CantUmedida'];
                $uMedida = $row['Umedida'];
                $factor = $row['Factor'];
                $cantidad = $row['Cantidad'];
                $fechaVencimiento = $row['FechaVencimiento'];
                $fechaVencimiento=date("d/m/Y",strtotime($fechaVencimiento));
                $lote = $row['Lote'];
                $costoTotal = $row['CostoTotal'];
                $costoUnitario = $costoTotal / $cantidad;
                ?>
                <tr>
                  <td class="consecutivo"><?php echo $item; ?></td>







                  <td>
                    <input type="hidden" name="item" id="item" value="<?php echo $item; ?>">
                    <?php //var_dump($_POST); ?>
                    <input type="text" name="codigo<?php echo $item; ?>" id="codigo<?php echo $item; ?>" value="<?php echo $codigoProducto; ?>" readonly="" class="form-control codigoItem campoNoVisible">
                  </td>



                  <td>
                    <input type="hidden" name="descripcion<?php echo $item; ?>" id="descripcion<?php echo $item; ?>" value="<?php echo $descripcion; ?>">
                    <?php echo $descripcion; ?>
                  </td>






                  <td>
                    <select name="bodegaOrigen<?php echo $item; ?>" id="bodegaOrigen<?php echo $item; ?>" class="form-control bodegaOrigen" <?php if($documento == 3){ ?>disabled="disabled"<?php } ?>>
                      <option value="">Seleccione una</option>
                      <?php
                      $consulta = " select * from bodegas ";
                      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                      if($resultado->num_rows >= 1){
                        while($row = $resultado->fetch_assoc()) { ?>
                          <option value="<?php echo $row["ID"]; ?>" <?php  if (isset($bodegaOrigen) && ($bodegaOrigen == $row["ID"]) ) { echo ' selected '; } ?>   ><?php echo $row["NOMBRE"]; ?></option>
                          <?php
                        }// Termina el while
                      }//Termina el if que valida que si existan resultados
                      ?>
                    </select>
                  </td>

                  <td>
                    <select name="bodegaDestino<?php echo $item; ?>" id="bodegaDestino<?php echo $item; ?>" class="form-control bodegaDestino" <?php if($documento == 9){ ?>disabled="disabled"<?php } ?>>
                      <option value="">Seleccione una</option>
                      <?php
                      $consulta = " select * from bodegas ";
                      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                      if($resultado->num_rows >= 1){
                        while($row = $resultado->fetch_assoc()) { ?>
                          <option value="<?php echo $row["ID"]; ?>" <?php  if (isset($bodegaDestino) && ($bodegaDestino == $row["ID"]) ) { echo ' selected '; } ?>   ><?php echo $row["NOMBRE"]; ?></option>
                          <?php
                        }// Termina el while
                      }//Termina el if que valida que si existan resultados
                      ?>
                    </select>
                  </td>

                  <td>


                    <select class="form-control unidadesM" name="unidades<?php echo $item; ?>" id="unidades<?php echo $item; ?>" onchange="buscar_cant_unidad('<?php echo $item; ?>','<?php echo $codigoProducto;; ?>')">
                      <option value="">Seleccione una</option>
                      <?php
                      $periodoActual = $_SESSION['periodoActual'];
                      $consulta = "   SELECT
                            NombreUnidad1 as unidad, CantidadUnd1 as cantidad, 1 as consecutivoUnidad
                        FROM
                            productos$periodoActual
                        WHERE
                            Codigo = $codigoProducto
                        UNION SELECT
                            NombreUnidad2 as unidad, CantidadUnd2 as cantidad, 2 as consecutivoUnidad
                        FROM
                            productos$periodoActual
                        WHERE
                            Codigo = $codigoProducto
                        UNION SELECT
                            NombreUnidad3 as unidad, CantidadUnd3 as cantidad, 3 as consecutivoUnidad
                        FROM
                            productos$periodoActual
                        WHERE
                            Codigo = $codigoProducto
                            UNION SELECT
                            NombreUnidad4 as unidad, CantidadUnd4 as cantidad, 4 as consecutivoUnidad
                        FROM
                            productos$periodoActual
                        WHERE
                            Codigo = $codigoProducto
                            UNION SELECT
                            NombreUnidad5 as unidad, CantidadUnd5 as cantidad, 5 as consecutivoUnidad
                        FROM
                            productos$periodoActual
                        WHERE
                            Codigo = $codigoProducto ";
                      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                      if($resultado->num_rows >= 1){
                        while($row = $resultado->fetch_assoc()) {
                          if($row["unidad"] != ''){?>

                            <option value="<?php echo $row["consecutivoUnidad"]; ?>" <?php if($unidad == $row['unidad']){ echo ' selected ';} ?>><?php echo $row["unidad"]; ?></option>
                        <?php  }



                        }// Termina el while
                      }//Termina el if que valida que si existan resultados
                      ?>
                    </select>
                  </td>


                  <td>
                    <input class="form-control unidades txtAlignCenter" type="text" name="unidades<?php echo $item; ?>" id="unidades<?php echo $item; ?>" value="<?php echo number_format($cantidad,2); ?>">
                    <input type="hidden" name="unidadesnm<?php echo $item; ?>" id="unidadesnm<?php echo $item; ?>" value="<?php echo $uMedida; ?>">
                    <input type="hidden" name="factor<?php echo $item; ?>" id="factor<?php echo $item; ?>" value="<?php echo $factor; ?>">
                  </td>







                  <td>
                    <input class="form-control costounitario" type="text" name="costoUnitario<?php echo $item; ?>" id="costoUnitario<?php echo $item; ?>" value="<?php echo $costoUnitario; ?>" class="txtAlignCenter">
                  </td>

                    <td>
                      <input class="form-control costototal" type="text" name="costoTotal<?php echo $item; ?>" id="costoTotal<?php echo $item; ?>" value="<?php echo $costoTotal; ?>" readonly="" class="campoNoVisible txtAlignCenter">
                    </td>


                    <td> <input type="text" name="lote<?php echo $item; ?>" id="lote<?php echo $item; ?>" value="<?php echo $lote; ?>" class="form-control txtAlignCenter loteItem"> </td>
                    <td> <input type="text" class="form-control datepick txtAlignCenter fechaItem" name="fechaV<?php echo $item; ?>" id="fechaV<?php echo $item; ?>" value = "<?php echo $fechaVencimiento; ?>"> </td>




                    <td align="center">
                      <span class="quitarItem"><i class="fa fa-minus-circle" aria-hidden="true"></i></span>
                    </td>


                </tr>


                <?php
                $item++;
              }// Termina el while
            }//Termina el if que valida que si existan resultados
            ?>



        </tbody>
        <tfoot>
          <tr>
            <td colspan="11" align="right"> Valor Total </td>
            <td><input type="text" name="valorTotal" id="valorTotal" value="<?php echo $total; ?>" readonly="" class="form-control" style="text-align:right;background-color:transparent;border:none;"></td>
          </tr>
        </tfoot>
      </table>
                  
                </div><!-- /.table-responsive -->
               </div><!-- /.col --> 

            </div><!-- /.row -->





              
            
            

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

<!-- Data picker -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>

<!-- Date picker en español -->
<script src="<?php echo $baseUrl; ?>/js/bootstrap-datepicker.es.js"></script>

<!-- Scripts sección del modulo -->
<script src="<?php echo $baseUrl; ?>/modules/movimientos/js/movimiento_nuevo.js"></script>

<!-- Page-Level Scripts -->
<script type="text/javascript"> $(document).ready( function () { reiniciarTabla(); }); </script>
<script type="text/javascript">
$(document).ready(function() {

  $('#fecha').each(function(){
    $(this).removeClass("hasDatepicker");
    $(this).datepicker({ 
    format: 'dd/mm/yyyy',
    todayHighlight: 'true',
    autoclose: 'true' 
  });
  });

});
</script>
<script type="text/javascript">
  //Función que vijila el no salirse de la pagina sin guardar
  $(window).bind('beforeunload', function(){
    return 'Está a punto de salir sin guardar el movimiento actual, todos los campos diligenciados se perderán';
  });
  $('#contactform').submit(function(){
    $(window).unbind('beforeunload');
    return false;
  });
</script>



<?php mysqli_close($Link); ?>

  <div class="listadoFondo">
            <div class="listadoContenedor">
              <div class="listadoCuerpo">
              </div><!-- /.listadoCuerpo -->
            </div><!-- /.listadoContenedor -->
          </div><!-- /.listadoFondo -->


</body>
</html>