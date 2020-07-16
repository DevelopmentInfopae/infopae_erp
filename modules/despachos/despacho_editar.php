<?php
include '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$paginasObservaciones = 1;
// $Link = new mysqli($Hostname, $Username, $Password, $Database);
// if ($Link->connect_errno) {
//     echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
// }
// $Link->set_charset("utf8");
?>






<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
            <div class="col-lg-8">
                <h2>Editar Despacho</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo $baseUrl; ?>">Inicio</a>
                    </li>
                    <li class="active">
                        <strong>Editar Despacho</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-4">
                <div class="title-action">
                    <a href="#" onclick="actualizarDespacho()" target="_self" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Guardar Cambios </a>
                </div>
            </div>
</div>


<?php
$mesAnno = '';
        if( isset($_POST['despachoAnnoI']) && isset($_POST['despachoMesI']) && isset($_POST['despacho']) ){
          // Se va a recuperar el mes y el año para las tablaMesAnno
          $mes = $_POST['despachoMesI'];
          if($mes < 10){
            $mes = '0'.$mes;
          }
          $mes = trim($mes);
          $anno = $_POST['despachoAnnoI'];
          $anno = substr($anno, -2);
          $anno = trim($anno);
          $mesAnno = $mes.$anno;
          $_POST = array_slice($_POST, 3);
          $_POST = array_values($_POST);
        }else{
          // Se va a recuperar el mes y el año para las tablaMesAnno
          $mes = $_POST['mesiConsulta'];
          if($mes < 10){
            $mes = '0'.$mes;
          }
          $mes = trim($mes);
          $anno = $_POST['annoi'];
          $anno = substr($anno, -2);
          $anno = trim($anno);
          $mesAnno = $mes.$anno;





          $corteDeVariables = 16;
          if(isset($_POST['seleccionarVarios'])){
            $corteDeVariables++;
          }
          if(isset($_POST['informeRuta'])){
            $corteDeVariables++;
          }
          if(isset($_POST['ruta'])){
            $corteDeVariables++;
          }
          if(isset($_POST['rutaNm'])){
            $corteDeVariables++;
          }

          if(isset($_POST['paginasObservaciones'])){
            $paginasObservaciones = $_POST['paginasObservaciones'];
            $corteDeVariables++;
          }

          $_POST = array_slice($_POST, $corteDeVariables);
          $_POST = array_values($_POST);
        }


        // Se va a recibir el numero d edespacho para hacer la consulta y traer los
        // encabezados del despacho.
        $claves = array_keys($_POST);
        $aux = $claves[0];
        $despacho = $_POST[$aux];
        // var_dump($_POST);
        $consulta = " select * from despachos_enc$mesAnno where Num_Doc = $despacho ";
        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){
          $row = $resultado->fetch_assoc();
          $semana = $row['Semana'];
          $tipo = $row['Tipo_Complem'];
          $tipoDespacho = $row['TipoDespacho'];
          $diasDespacho = $row['Dias'];
          $sede = $row['cod_Sede'];
          $data_sede = [];

          $consDataSede = "SELECT * FROM sedes".$_SESSION['periodoActual']." WHERE cod_sede = ".$sede;
          $resDataSede = $Link->query($consDataSede);
          if ($resDataSede->num_rows > 0) {
            $data_sede = $resDataSede->fetch_assoc();
          }

        }

        // Segunda consulta para traer los datos almacenados en productosmov 16 que complementan los
        // parametros del despacho

        $consulta = " select * from productosmov$mesAnno pm where pm.Numero = $despacho and pm.Documento='DES' ";

        // echo "<br>Segunda consulta para traer los datos almacenados en productosmov 16 que complementan los parametros del despacho<br>$consulta<br>";





        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        if($resultado->num_rows >= 1){
          $row = $resultado->fetch_assoc();
          //var_dump($row);
          $bodegaOrigen = $row['BodegaOrigen'];
          $bodegaDestino = $row['BodegaDestino'];
          $tipoTransporte = $row['TipoTransporte'];
          $placa = $row['Placa'];
          $conductor = $row['ResponsableRecibe'];
          $proveedorEmpleadoNm = $row['Nombre'];
          $proveedorEmpleado = $row['Nitcc'];
          $tipoMovimiento = $row['Tipo'];
        }

        //Tercera consulta para traer los datos de la sede.
        $consulta = " SELECT u.Ciudad, s.nom_inst, s.nom_sede, s.cod_mun_sede, s.cod_inst, s.cod_sede
        FROM sedes$periodoActual  s
        inner join ubicacion u on s.cod_mun_sede = u.CodigoDANE and u.ETC = 0
        WHERE s.cod_sede = $bodegaDestino ";

        //echo "<br>Tercera consulta para traer los datos de la sede<br>$consulta<br>";

        $resultado = $Link->query($consulta) or die ('Unable to execute query para traer los datos de la sede '. mysqli_error($Link));
        if($resultado->num_rows >= 1){
          $row = $resultado->fetch_assoc();
          $municipio = $row['cod_mun_sede'];
          $municipioNm = $row['Ciudad'];
          $institucion = $row['cod_inst'];
          $institucionNm = $row['nom_inst'];
          $sedeNm = $row['nom_sede'];
        }
?>



<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="col-lg-12" action="despachos.php" name="formDespachos" id="formDespachos" method="post">




            <!-- Parametros admicionales para la edición de despachos -->

            <input type="hidden" name="despacho" id="despacho" value="<?php echo $despacho; ?>">
            <input type="hidden" name="proveedorEmpleadoInicial" id="proveedorEmpleadoInicial" value="<?php echo $proveedorEmpleado; ?>">
            <input type="hidden" name="municipioInicial" id="municipioInicial" value="<?php echo $municipio; ?>">
            <input type="hidden" name="institucionInicial" id="institucionInicial" value="<?php echo $institucion; ?>">
            <input type="hidden" name="sedeInicial" id="sedeInicial" value="<?php echo $sede; ?>">













            <div class="row">
              <div class="col-sm-3 form-group">
                <label for="subtipo">Tipo de despacho</label>
                <!-- Planilla semanas -->
                <select class="form-control" name="subtipo" id="subtipo">
                  <option value="">Seleccione uno</option>
                  <?php
                  $consulta = " select * from tipomovimiento where Documento = 'DES' ";
                  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($resultado->num_rows >= 1){
                    while($row = $resultado->fetch_assoc()) { ?>
                      <option value="<?php echo $row["Id"]; ?>" <?php  if (isset($_POST['subtipo']) && ($_POST['subtipo'] == $row["Id"]) ) { echo ' selected '; } ?> <?php if($tipoMovimiento == $row["Movimiento"] ){ echo " selected ";} ?>   ><?php echo $row["Movimiento"]; ?></option>
                      <?php
                    }// Termina el while
                  }//Termina el if que valida que si existan resultados
                  ?>
                </select>
                <input type="hidden" id="subtipoNm" name="subtipoNm" value="">
              </div><!-- /.col -->
              <div class="col-sm-3 form-group">
                <label for="subtipo">Proveedor / Empleado</label>
                <select class="form-control" name="proveedorEmpleado" id="proveedorEmpleado">
                  <option value="">Seleccione uno</option>
                </select>
                <input type="hidden" id="proveedorEmpleadoNm" name="proveedorEmpleadoNm" value="">
              </div><!-- /.col -->


              <div class="col-sm-3 form-group">
                <label for="subtipo">Semana</label>
                <select class="form-control" name="semanaReferencia" id="semanaReferencia" disabled="disabled">
                <option value="">Seleccione una</option>
                <?php
                $consulta = " select DISTINCT SEMANA from planilla_semanas ";
                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($resultado->num_rows >= 1){
                  while($row = $resultado->fetch_assoc()) { ?>
                    <option value="<?php echo $row["SEMANA"]; ?>" <?php  if (isset($semana) && ($semana == $row["SEMANA"]) ) { echo ' selected '; } ?>   ><?php echo $row["SEMANA"]; ?></option>
                    <?php
                  }// Termina el while
                }//Termina el if que valida que si existan resultados
                ?>
                </select>
                <input type="hidden" name="semana" id="semana" value="<?php  if (isset($semana)){ echo $semana; } ?>">
              </div><!-- /.col -->




              <div class="col-sm-3 form-group">
                <input type="hidden" name="diasDespacho" id="diasDespacho" value="<?php echo $diasDespacho; ?>">
                <label for="dias">Días</label>
                <!-- Planilla semanas -->
                <div id="dias">
                </div>
              </div><!-- /.col -->


              <div class="col-sm-3 form-group">
                <label for="tipoRacion">Tipo Ración</label>
                <select class="form-control" name="tipoRacion" id="tipoRacion">
                <option value="">Seleccione una</option>
                <?php
                $consulta = " select DISTINCT CODIGO from tipo_complemento ";
                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($resultado->num_rows >= 1){
                  while($row = $resultado->fetch_assoc()) { ?>
                    <option value="<?php echo $row["CODIGO"]; ?>" <?php  if (isset($tipo) && ($tipo == $row["CODIGO"]) ) { echo ' selected '; } ?>   ><?php echo $row["CODIGO"]; ?></option>
                    <?php
                  }// Termina el while
                }//Termina el if que valida que si existan resultados
                ?>
              </select>
              </div><!-- /.col -->
              <div class="col-sm-3 form-group">
                <label for="tipoDespacho">Tipo Despacho</label>
                <!-- Tipo Complemento - Codigo -->
                <select class="form-control" name="tipoDespacho" id="tipoDespacho">
                  <option value="">Seleccione una</option>
                                 <?php

               $consulta = " select * from tipo_despacho where 1=1 order by Descripcion asc ";

               $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

               if($resultado->num_rows >= 1){

                 while($row = $resultado->fetch_assoc()) { ?>

                   <option value="<?php echo $row["Id"]; ?>" <?php  if (isset($tipoDespacho) && ($tipoDespacho == $row["Id"]) ) { echo ' selected '; } ?>  ><?php echo $row["Descripcion"]; ?></option>

                   <?php

                 }// Termina el while

               }//Termina el if que valida que si existan resultados

               ?>
                </select>
              </div><!-- /.col -->
              <div class="col-sm-3 form-group">
                <label for="municipio">Municipio</label>
                <!-- Tipo Complemento - Codigo -->
                <select class="form-control" name="municipio" id="municipio">
                <option value="">Seleccione uno</option>
                </select>
              </div><!-- /.col -->
              <div class="col-sm-3 form-group">
                <label for="institucion">Institución</label>
                <!-- Tipo Complemento - Codigo -->
                <select class="form-control" name="institucion" id="institucion">
                  <option value="">Todos</option>
                </select>
              </div><!-- /.col -->
              <div class="col-sm-3 form-group">
                <label for="sede">Sede</label>
                <!-- Tipo Complemento - Codigo -->
                <select class="form-control" name="sede" id="sede">
                  <option value="">Todos</option>
                </select>
              </div><!-- /.col -->
              <div class="col-sm-3 form-group">
                <label for="ruta">Buscar Sedes x Ruta</label>
                <!-- Tipo Complemento - Codigo -->
                <select class="form-control" name="ruta" id="ruta">
                  <option value="">Seleccione una</option>
                  <?php
              $consulta = " select * from rutas order by nombre asc ";
              $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
              if($resultado->num_rows >= 1){
                while($row = $resultado->fetch_assoc()) { ?>
                  <option value="<?php echo $row["ID"]; ?>" ><?php echo $row["Nombre"]; ?></option>
                  <?php
                }// Termina el while
              }//Termina el if que valida que si existan resultados
              ?>
                </select>
              </div><!-- /.col -->
            </div><!-- -/.row -->
            <div class="row">
              <div class="col-sm-3 form-group">
                <button type="button" id="btnAgregar" class="botonParametro btn btn-primary">+</button>
                <button type="button" id="btnQuitar" class="botonParametro btn btn-primary">-</button>
              </div><!-- /.col -->
            </div><!-- -/.row -->




            <div class="row">
              <div class="col-sm-6 form-group">
                <input type="checkbox" name="selectVarios" id="selectVarios" value="">
                <label for="selectVarios">Seleccionar Todos</label>
              </div><!-- /.col -->
            </div>


            <div class="table-responsive">

              <table width="100%" id="box-table-a" class="table table-striped table-bordered table-hover selectableRows" >
            <thead>
              <tr>
                <th></th>
                <th>Municipio</th>
                <th>Institución</th>
                <th>Sede</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input type="checkbox" value="<?php echo $sede; ?>" data-variacion="<?= $data_sede['cod_variacion_menu'] ?>"></td>
                <td><input type="hidden" name="sede1" id="sede1" value="<?php echo $sede; ?>"><?php echo $municipioNm; ?></td>
                <td><?php echo $institucionNm; ?></td>
                <td><?php echo $sedeNm; ?></td>
              </tr>
            </tbody>
          </table>

            </div><!-- /.table-responsive -->




        <hr>

        <div class="row">
          <div class="col-sm-3 form-group">
              <label for="bodegaOrigen">Bodega Origen</label>
              <select class="form-control" name="bodegaOrigen" id="bodegaOrigen">
                <option value="">-</option>
                <?php
                $consulta = " select ID, NOMBRE from bodegas order by NOMBRE asc ";
                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($resultado->num_rows >= 1){
                  while($row = $resultado->fetch_assoc()) { ?>
                    <option value="<?php echo $row["ID"]; ?>" <?php  if (isset($bodegaOrigen) && ($bodegaOrigen == $row["ID"]) ) { echo ' selected '; } ?>   ><?php echo $row["NOMBRE"]; ?></option>
                    <?php
                  }// Termina el while
                }//Termina el if que valida que si existan resultados
                ?>
              </select>
          </div><!-- /.col -->
          <div class="col-sm-3 form-group">
            <label for="bodegaDestino">Bodega Destino</label>
            <input type="text" name="bodegaDestino" id="bodegaDestino" value="Bodega asignada a sede" readonly="readonly" class="form-control">
          </div><!-- /.col -->
        </div><!-- -/.row -->

        <div class="row">
          <div class="col-sm-3 form-group">
              <label for="tipoTransporte">Tipo Transporte</label>
              <select class="form-control" name="tipoTransporte" id="tipoTransporte">
                <option value="">-</option>
                <?php
                $consulta = " select * from tipovehiculo order by nombre asc ";
                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($resultado->num_rows >= 1){
                  while($row = $resultado->fetch_assoc()) { ?>
                    <option value="<?php echo $row["Id"]; ?>" <?php  if (isset($tipoTransporte) && ($tipoTransporte == $row["Id"]) ) { echo ' selected '; } ?>   ><?php echo $row["Nombre"]; ?></option>
                    <?php
                  }// Termina el while
                }//Termina el if que valida que si existan resultados
                ?>
              </select>
          </div><!-- /.col -->
          <div class="col-sm-3 form-group">
            <label for="placa">Placa</label>
            <!-- Planilla tipoTransportes -->
            <input type="text" name="placa" id="placa" value="<?php echo $placa; ?>" class="form-control">
          </div><!-- /.col -->
          <div class="col-sm-3 form-group">
            <label for="conductor">Nombre Conductor</label>
            <!-- Planilla tipoTransportes -->
            <input type="text" name="conductor" id="conductor" value="<?php echo $conductor; ?>" class="form-control">
          </div><!-- /.col -->
        </div><!-- -/.row -->











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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>


    <script src="<?php echo $baseUrl; ?>/modules/despachos/js/despacho_nuevo.js"></script>
    <script src="<?php echo $baseUrl; ?>/modules/despachos/js/despacho_editar.js"></script>








</body>
</html>
