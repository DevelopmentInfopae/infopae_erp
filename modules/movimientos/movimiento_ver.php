<?php 
$titulo = 'Ver Movimiento';
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









<?php  
        //var_dump($_POST);
        //var_dump($_SESSION);
        
        
        $tablaMesAnno = 0;
        if(isset($_POST['formVerTabla'])){
          $tablaMesAnno = $_POST['formVerTabla'];
          $_SESSION['tablaMesAnno'] = $tablaMesAnno;
        }else if(isset($_SESSION['tablaMesAnno'])){
          $tablaMesAnno = $_SESSION['tablaMesAnno'];
        }

        $id = 0;
        if(isset($_POST['formVerId'])){
          $id = $_POST['formVerId'];
          $_SESSION['idMovimiento'] = $id;
        }else if(isset($_SESSION['idMovimiento'])){
          $id = $_SESSION['idMovimiento'];
        }

    



        $consulta = " select * from productosmov$tablaMesAnno where id = $id and Aprobado = 1 ";
        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){
          $row = $resultado->fetch_assoc();
          //var_dump($row);
        }//Termina el if que valida que si existan resultados

        $documento = htmlspecialchars($row['Documento']);
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
        $aprobado = htmlspecialchars($row['Aprobado']);
        $anulado = htmlspecialchars($row['Anulado']);



        // Buscando el nombre del tipo de documento
        $consulta = " select Descripcion from documentos where Tipo = '$documento' ";
        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){
          $row = $resultado->fetch_assoc();
          $documento = $row['Descripcion'];
        }
        // Termina buscando el nimbre del tipo de documento

        // Buscando la bodega origen
        if($bodegaOrigen == 0 || $bodegaOrigen == ''){
          $bodegaOrigen = '';
        }
        else{
          $consulta = " select NOMBRE from bodegas where ID = '$bodegaOrigen' ";
          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
          if($resultado->num_rows >= 1){
            $row = $resultado->fetch_assoc();
            $bodegaOrigen = $row['NOMBRE'];
          }
        }
        // Termina de buscar la bodega origen

        // Buscando la bodega destino
        if($bodegaDestino == 0 || $bodegaDestino == ''){
          $bodegaDestino = '';
        }
        else{
          $consulta = " select NOMBRE from bodegas where ID = '$bodegaDestino' ";
          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
          if($resultado->num_rows >= 1){
            $row = $resultado->fetch_assoc();
            $bodegaDestino = $row['NOMBRE'];
          }
        }
        // Termina de buscar la bodega destino
?>





<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Visualizar Movimiento</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
        <a href="<?php echo $baseUrl; ?>/modules/movimientos/movimientos.php">Movimientos</a>
      </li>
      <li class="active">
        <strong>Visualizar Movimiento</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      -->


      <?php if($anulado == 0){ ?>
      <a href="" target="_self" class="btn btn-primary" onclick="anular_movimiento(<?php echo $id; ?>,'<?php echo $tablaMesAnno; ?>')"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Anular Movimiento </a>
      <?php } ?>
    
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->



















<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          
      <?php
      if($anulado != 0){
        echo '<h2>Estado: Anulado</h2>';
      }
      else{
        echo '<h2>Estado: Aprobado</h2>';
      }
      ?>










          <form class="col-lg-12" action="" name="nuevoDocumento" id="nuevoDocumento" method="post">
            <input type="hidden" name="numeroOrigen" id="numeroOrigen" value="0" readonly="">
            <input type="hidden" name="docOrigen" id="docOrigen" value="Manual">
            

            <div class="row">

              <div class="col-sm-3 form-group">
                <label for="documento">Documento</label> 
                <input class="form-control" type="input" name="documento" id="documento" readonly value="<?php echo $documento; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="tipo">Tipo</label> 
                <input class="form-control" type="input" name="tipo" id="tipo" readonly value="<?php echo $tipo; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="numero">Numero</label> 
                <input class="form-control" type="input" name="numero" id="numero" readonly value="<?php echo $numero; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="fecha">Fecha</label> 
                <input class="form-control" type="input" name="fecha" id="fecha" readonly value="<?php echo $fecha; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="bodegaOrigen">Bodega Origen</label> 
                <input class="form-control" type="input" name="bodegaOrigen" id="bodegaOrigen" readonly value="<?php echo $bodegaOrigen; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="bodegaDestino">Bodega Destino</label> 
                <input class="form-control" type="input" name="bodegaDestino" id="bodegaDestino" readonly value="<?php echo $bodegaDestino; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="nitcc">NIT/CC</label> 
                <input class="form-control" type="input" name="nitcc" id="nitcc" readonly value="<?php echo $nitcc; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="nombre">nombre Tercero</label> 
                <input class="form-control" type="input" name="nombre" id="nombre" readonly value="<?php echo $nombre; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="tipoTransporte">Tipo Transporte</label> 
                <input class="form-control" type="input" name="tipoTransporte" id="tipoTransporte" readonly value="<?php echo $tipoTransporte; ?>">
              </div><!-- /.col -->  

              <div class="col-sm-3 form-group">
                <label for="conductor">Conductor</label> 
                <input class="form-control" type="input" name="conductor" id="conductor" readonly value="<?php echo $responsable; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-3 form-group">
                <label for="placa">Placa</label> 
                <input class="form-control" type="input" name="placa" id="placa" readonly value="<?php echo $placa; ?>">
              </div><!-- /.col --> 

              <div class="col-sm-12 form-group">
                <label for="concepto">Concepto</label> 
                <textarea class="form-control" name="concepto" id="concepto" rows="8" cols="40" readonly="readonly"><?php echo $concepto; ?></textarea>
              </div><!-- /.col --> 
            
            </div><!-- /.row -->
            <div class="row">
              <div class="col-sm-12 form-group">


      
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover selectableRows" id="box-table-a">
        <thead>
          <tr>
          <th> </th>
          <th> Código </th>
          <th>
            Descripción
          </th>
          <th>
            B. Oríg
          </th>
          <th>
            B. Dest
          </th>
          <th>
            U. Medida
          </th>
          <th>
            Cant. U. Medida
          </th>
          <th>
            Unidades
          </th>
          <th>
            Lote
          </th>
          <th>
            Fecha V.
          </th>
          <th>
            Costo Unitario
          </th>
          <th>
            Costo Total
          </th>
          </tr>
        </thead>
        <tbody>
          <?php
            //$tipoDocumento
            //$numero
            $consulta = " select p.*,(select NOMBRE from bodegas where ID = p.BodegaOrigen) as bodegaOrigen_nm,(select NOMBRE from bodegas where ID = p.BodegaDestino) as bodegaDestino_nm from productosmovdet$tablaMesAnno p where p.Documento = '$tipoDocumento' and p.Numero = '$numero' ";
            $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
            if($resultado->num_rows >= 1){
              while($row = $resultado->fetch_assoc()){ ?>
                <tr>
                  <td style="text-align:center;"><?php echo $row['Item']; ?></td>
                  <td style="text-align:center;"><?php echo $row['CodigoProducto']; ?></td>
                  <td><?php echo $row['Descripcion']; ?></td>
                  <td><?php echo $row['bodegaOrigen_nm']; ?></td>
                  <td><?php echo $row['bodegaDestino_nm']; ?></td>
                  <td style="text-align:center;"><?php echo $row['Umedida']; ?></td>
                  <td style="text-align:center;"><?php
                    $aux = $row['CantUmedida'];
                    $aux = number_format($aux,2);
                    echo $aux;
                  ?></td>
                  <td style="text-align:center;"><?php
                    $aux = $row['Cantidad'];
                    $aux = number_format($aux,2);
                    echo $aux;
                  ?></td>
                  <td style="text-align:center;"><?php echo $row['Lote']; ?></td>
                  <td style="text-align:center;"><?php
                    $fecha = htmlspecialchars($row['FechaVencimiento']);
                    $fecha = date_create($fecha);
                    $fecha = date_format($fecha, 'd/m/Y');
                    echo $fecha;
                  ?></td>
                  <td style="text-align:right;">$ <?php echo $row['ValorUnitario']; ?></td>
                  <td style="text-align:right;">$ <?php echo $row['CostoTotal']; ?></td>

                </tr>
              <?php }
            }//Termina el if que valida que si existan resultados
          ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="11" align="right"> Valor Total</td>
            <td><input style="background-color:transparent;border:none;text-align:right;" type="text" name="valorTotal" id="valorTotal" value="$ <?php echo $total; ?>" readonly="" style="border:none;text-align:right;"></td>
          </tr>
        </tfoot>
      </table>
    </div><!-- /.table-responsive -->
  </div><!-- /.col --> 
</div><!-- /.row -->
</form>


          <div class="listadoFondo">
            <div class="listadoContenedor">
              <div class="listadoCuerpo">
              </div><!-- /.listadoCuerpo -->
            </div><!-- /.listadoContenedor -->
          </div><!-- /.listadoFondo -->
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

<!-- Scripts sección del modulo -->
<script src="<?php echo $baseUrl; ?>/modules/movimientos/js/movimiento_ver.js"></script>


<!-- Page-Level Scripts -->

<?php mysqli_close($Link); ?>

</body>
</html>