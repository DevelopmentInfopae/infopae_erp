<?php 
$titulo = 'Nuevo Movimiento';
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
    <h2>Nuevo Movimiento</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
        <a href="<?php echo $baseUrl; ?>/modules/movimientos/movimientos.php">Movimientos</a>
      </li>
      <li class="active">
        <strong>Nuevo Movimiento</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      -->
      <span class="btn btn-primary" onclick="guardar_movimiento(0)"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Guardar</span>
      <span class="btn btn-primary" onclick="guardar_movimiento(1)"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Aprobar y Guardar </span>  
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
 
          <form class="col-lg-12" action="" name="nuevoDocumento" id="nuevoDocumento" method="post">
            <input type="hidden" name="periodoActualCompleto" id="periodoActualCompleto" value="<?php echo $_SESSION["periodoActualCompleto"];   ?>">
            <input type="hidden" name="numeroOrigen" id="numeroOrigen" value="0" readonly="">
            <input type="hidden" name="docOrigen" id="docOrigen" value="Manual">
            

            <div class="row">

              <div class="col-sm-3 form-group">
                <label for="documento">Documento</label> 
                <select class="form-control" name="documento" id="documento" onchange="buscar_tipo_movimiento()">
                  <option value="">Seleccione uno</option>
                  <?php
                  $consulta = " select id,descripcion from documentos ";
                  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($resultado->num_rows >= 1){
                    while($row = $resultado->fetch_assoc()) { ?>
                      <option value="<?php echo $row["id"]; ?>" <?php  if (isset($_POST['documento']) && ($_POST['documento'] == $row["id"]) ) { echo ' selected '; } ?>   ><?php echo $row["descripcion"]; ?></option>
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
                </select>
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="numero">Numero</label> 
                <input class="form-control" type="text" name="numero" id="numero" value="1" readonly="">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="fecha">Fecha</label> 
                <input class="form-control" type="text" name="fecha" id="fecha" value="">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="bodegaOrigen">Bodega Origen</label> 
                <select name="bodegaOrigen" id="bodegaOrigen" class="form-control">
              <option value="">Seleccione una</option>
              <?php
              $consulta = " select * from bodegas ";
              $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
              if($resultado->num_rows >= 1){
                while($row = $resultado->fetch_assoc()) { ?>
                  <option value="<?php echo $row["ID"]; ?>" <?php  if (isset($_POST['bodegaOrigen']) && ($_POST['bodegaOrigen'] == $row["ID"]) ) { echo ' selected '; } ?>   ><?php echo $row["NOMBRE"]; ?></option>
                  <?php
                }// Termina el while
              }//Termina el if que valida que si existan resultados
              ?>
            </select>                
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="bodegaDestino">Bodega Destino</label> 
                <select name="bodegaDestino" id="bodegaDestino" class="form-control">
                  <option value="">Seleccione una</option>
                  <?php
                  $consulta = " select * from bodegas ";
                  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($resultado->num_rows >= 1){
                    while($row = $resultado->fetch_assoc()) { ?>
                      <option value="<?php echo $row["ID"]; ?>" <?php  if (isset($_POST['bodegaDestino']) && ($_POST['bodegaDestino'] == $row["ID"]) ) { echo ' selected '; } ?>   ><?php echo $row["NOMBRE"]; ?></option>
                      <?php
                    }// Termina el while
                  }//Termina el if que valida que si existan resultados
                  ?>
                </select>                
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="nitcc">NIT/CC</label> 
                <input class="form-control" type="text" name="nitcc" id="nitcc" value="" autocomplete="off">
                <div id="suggestionsn" class="suggestions"> <div id="suggestionsContenedorn" class="suggestionsContenedor"></div> </div>
           
              </div><!-- /.col --> 

           


              <div class="col-sm-3 form-group">
                <label for="nombre">Nombre Tercero</label> 
                <div class="input-group">
                  <input class="form-control" type="text" name="nombre" id="nombre" value="" autocomplete="off">
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
                      <option value="<?php echo $row["Nombre"]; ?>" <?php  if (isset($_POST['tipoTransporte']) && ($_POST['tipoTransporte'] == $row["Nombre"]) ) { echo ' selected '; } ?>   ><?php echo $row["Nombre"]; ?></option>
                      <?php
                    }// Termina el while
                  }//Termina el if que valida que si existan resultados
                  ?>

                </select>
              </div><!-- /.col -->  

              <div class="col-sm-6 form-group">
                <label for="conductor">Conductor</label> 
                <input type="text" name="conductor" id="conductor" value="" class="form-control">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="placa">Placa</label> 
                <input class="form-control" type="text" maxlength="6" name="placa" id="placa" value="" placeholder="ABC123">
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
                <textarea class="form-control" name="concepto" id="concepto" rows="3" cols="40"></textarea>
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
          <tr>
            <td class="consecutivo"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td>

            </td>
          </tr>



        </tbody>
        <tfoot>
          <tr>
            <td colspan="11" align="right"> Valor Total </td>
            <td><input type="text" name="valorTotal" id="valorTotal" value="0" readonly="" class="form-control"></td>
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

<script>
  $(document).ready( function () {

  dataset1 = $('#box-table-a').DataTable({
      bPaginate: false,
    order: [ 1, 'desc' ],
    pageLength: 25,
    responsive: true,
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
});
</script>
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