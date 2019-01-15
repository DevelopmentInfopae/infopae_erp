<?php 
$titulo = 'Movimientos';
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
    <h2>Movimientos</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong>Movimientos</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      -->
      <a href="<?php echo $baseUrl; ?>/modules/movimientos/movimiento_nuevo.php" target="_self" class="btn btn-primary"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> Nuevo Movimiento </a>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <h2>Parámetros de Consulta</h2>
          <form class="col-lg-12" action="movimientos.php" name="formMovimientos" id="formMovimientos" method="post">
            <div class="row">
              <div class="col-sm-4 form-group">
                                    <label for="fechaInicial">Fecha Inicial</label>  
                                    <div class="row compositeDate">
                                        <div class="col-sm-4 nopadding"> 
                                            <select name="annoi" id="annoi" class="form-control">
                                                <option value="<?php echo $_SESSION['periodoActual']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
                                            </select>  
                                        </div><!-- /.col-sm-4 -->   
                                        <div class="col-sm-5 nopadding">
                                            <?php
                                                if(!isset($_GET['pb_mesi']) || $_GET['pb_mesi'] == ''){
                                                    $_GET['pb_mesi'] = date("n");
                                                }
                                            ?>
                                            <select name="mesi" id="mesi" onchange="mesFinal();" class="form-control">
                                                <option value="">mm</option>
                                                <option value="1" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 1) {echo " selected "; } ?>>Enero</option>
                                                <option value="2" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 2) {echo " selected "; } ?>>Febrero</option>
                                                <option value="3" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 3) {echo " selected "; } ?>>Marzo</option>
                                                <option value="4" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 4) {echo " selected "; } ?>>Abril</option>
                                                <option value="5" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 5) {echo " selected "; } ?>>Mayo</option>
                                                <option value="6" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 6) {echo " selected "; } ?>>Junio</option>
                                                <option value="7" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 7) {echo " selected "; } ?>>Julio</option>
                                                <option value="8" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 8) {echo " selected "; } ?>>Agosto</option>
                                                <option value="9" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 9) {echo " selected "; } ?>>Septiembre</option>
                                                <option value="10" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 10) {echo " selected "; } ?>>Octubre</option>
                                                <option value="11" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 11) {echo " selected "; } ?>>Noviembre</option>
                                                <option value="12" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 12) {echo " selected "; } ?>>Diciembre</option>
                                            </select>
                                            <input type="hidden" name="mesiConsulta" id="mesiConsulta" value="<?php if (isset($_GET['pb_mesi'])) { echo $_GET['pb_mesi']; } ?>">
                                        </div><!-- /.col --> 


                                        <div class="col-md-3 nopadding"> 





   
                                            <select name="diai" id="diai" class="form-control">
                           <option value="">dd</option>


                           <option value="1" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 1) {echo " selected "; } ?>>01</option>


                           <option value="2" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 2) {
                              echo " selected ";
                           } ?>>02</option>
                           <option value="3" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 3) {
                              echo " selected ";
                           } ?>>03</option>
                           <option value="4" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 4) {
                              echo " selected ";
                           } ?>>04</option>
                           <option value="5" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 5) {
                              echo " selected ";
                           } ?>>05</option>
                           <option value="6" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 6) {
                              echo " selected ";
                           } ?>>06</option>
                           <option value="7" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 7) {
                              echo " selected ";
                           } ?>>07</option>
                           <option value="8" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 8) {
                              echo " selected ";
                           } ?>>08</option>
                           <option value="9" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 9) {
                              echo " selected ";
                           } ?>>09</option>
                           <option value="10" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 10) {
                              echo " selected ";
                           } ?>>10</option>
                           <option value="11" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 11) {
                              echo " selected ";
                           } ?>>11</option>
                           <option value="12" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 12) {
                              echo " selected ";
                           } ?>>12</option>
                           <option value="13" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 13) {
                              echo " selected ";
                           } ?>>13</option>
                           <option value="14" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 14) {
                              echo " selected ";
                           } ?>>14</option>
                           <option value="15" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 15) {
                              echo " selected ";
                           } ?>>15</option>
                           <option value="16" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 16) {
                              echo " selected ";
                           } ?>>16</option>
                           <option value="17" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 17) {
                              echo " selected ";
                           } ?>>17</option>
                           <option value="18" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 18) {
                              echo " selected ";
                           } ?>>18</option>
                           <option value="19" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 19) {
                              echo " selected ";
                           } ?>>19</option>
                           <option value="20" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 20) {
                              echo " selected ";
                           } ?>>20</option>
                           <option value="21" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 21) {
                              echo " selected ";
                           } ?>>21</option>
                           <option value="22" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 22) {
                              echo " selected ";
                           } ?>>22</option>
                           <option value="23" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 23) {
                              echo " selected ";
                           } ?>>23</option>
                           <option value="24" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 24) {
                              echo " selected ";
                           } ?>>24</option>
                           <option value="25" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 25) {
                              echo " selected ";
                           } ?>>25</option>
                           <option value="26" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 26) {
                              echo " selected ";
                           } ?>>26</option>
                           <option value="27" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 27) {
                              echo " selected ";
                           } ?>>27</option>
                           <option value="28" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 28) {
                              echo " selected ";
                           } ?>>28</option>
                           <option value="29" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 29) {
                              echo " selected ";
                           } ?>>29</option>
                           <option value="30" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 30) {
                              echo " selected ";
                           } ?>>30</option>
                           <option value="31" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 31) {
                              echo " selected ";
                           } ?>>31</option>
                       </select>  
                                            </div><!-- /.col-sm-4 --> 
                                        </div><!-- /.row -->                          
                                    </div><!-- /Fecha inicial -->



<div class="col-sm-4 form-group">
    <label for="fechaInicial">Fecha Final</label>  
    <div class="row compositeDate">
        <div class="col-sm-4 form-group nopadding">
            <select name="annof" id="annof" class="form-control">
                <option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
          </select>
        </div>
        <div class="col-sm-5 form-group nopadding">
            <input type="text" name="mesfText" id="mesfText" value="mm" readonly="readonly" class="form-control">
            <input type="hidden" name="mesf" id="mesf" value="">
        </div>
        <div class="col-sm-3 form-group nopadding">
            <select name="diaf" id="diaf" class="form-control">
                           <option value="">dd</option>


                           <option value="1" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 1) {echo " selected "; } ?>>01</option>


                           <option value="2" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 2) {
                              echo " selected ";
                           } ?>>02</option>
                           <option value="3" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 3) {
                              echo " selected ";
                           } ?>>03</option>
                           <option value="4" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 4) {
                              echo " selected ";
                           } ?>>04</option>
                           <option value="5" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 5) {
                              echo " selected ";
                           } ?>>05</option>
                           <option value="6" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 6) {
                              echo " selected ";
                           } ?>>06</option>
                           <option value="7" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 7) {
                              echo " selected ";
                           } ?>>07</option>
                           <option value="8" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 8) {
                              echo " selected ";
                           } ?>>08</option>
                           <option value="9" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 9) {
                              echo " selected ";
                           } ?>>09</option>
                           <option value="10" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 10) {
                              echo " selected ";
                           } ?>>10</option>
                           <option value="11" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 11) {
                              echo " selected ";
                           } ?>>11</option>
                           <option value="12" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 12) {
                              echo " selected ";
                           } ?>>12</option>
                           <option value="13" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 13) {
                              echo " selected ";
                           } ?>>13</option>
                           <option value="14" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 14) {
                              echo " selected ";
                           } ?>>14</option>
                           <option value="15" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 15) {
                              echo " selected ";
                           } ?>>15</option>
                           <option value="16" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 16) {
                              echo " selected ";
                           } ?>>16</option>
                           <option value="17" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 17) {
                              echo " selected ";
                           } ?>>17</option>
                           <option value="18" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 18) {
                              echo " selected ";
                           } ?>>18</option>
                           <option value="19" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 19) {
                              echo " selected ";
                           } ?>>19</option>
                           <option value="20" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 20) {
                              echo " selected ";
                           } ?>>20</option>
                           <option value="21" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 21) {
                              echo " selected ";
                           } ?>>21</option>
                           <option value="22" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 22) {
                              echo " selected ";
                           } ?>>22</option>
                           <option value="23" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 23) {
                              echo " selected ";
                           } ?>>23</option>
                           <option value="24" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 24) {
                              echo " selected ";
                           } ?>>24</option>
                           <option value="25" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 25) {
                              echo " selected ";
                           } ?>>25</option>
                           <option value="26" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 26) {
                              echo " selected ";
                           } ?>>26</option>
                           <option value="27" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 27) {
                              echo " selected ";
                           } ?>>27</option>
                           <option value="28" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 28) {
                              echo " selected ";
                           } ?>>28</option>
                           <option value="29" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 29) {
                              echo " selected ";
                           } ?>>29</option>
                           <option value="30" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 30) {
                              echo " selected ";
                           } ?>>30</option>
                           <option value="31" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 31) {
                              echo " selected ";
                           } ?>>31</option>
                       </select>
        </div>
    </div>
</div><!-- /Fecha final -->

                         
            </div><!-- /.row -->


            <div class="row">
              <div class="col-sm-3 form-group">
                <input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_GET['consultar']) && $_GET['consultar'] != '') {echo $_GET['consultar']; } ?>" > 
                <button class="btn btn-primary" type="submit" id="btnBuscar" name="btnBuscar" value="1" ><strong>Buscar</strong></button>
              </div>
            </div><!-- /.row -->  
          </form>


          <?php  if( (isset($_POST['mesi']) && $_POST['mesi']=='') ||(isset($_POST['mesi']) && $_POST['mesi'] <= 1) ){
            echo "<h2>Debe seleccionar un mes inicial.</h2>";
          } ?>


          <?php 



  if( isset($_POST['btnBuscar']) && $_POST['btnBuscar'] == 1 && $_POST['mesi'] >= 1 ){
    if($_POST['mesi'] < 10){
      $tablaMesAnno = '0'.$_POST['mesi'].$_POST['annoi'];
    }else{
      $tablaMesAnno = $_POST['mesi'].$_POST['annoi'];
    }
    //echo "<br><br>".$tablaMesAnno."<br><br>";

    // Validar la existencia de la tabla de productosmov + el periodo seleccionado por el usuario Ej: productosmov0606
    $banderaTabla = 0;
    $consulta = " show tables like 'productosmov$tablaMesAnno' ";
    //echo "<br><br>".$consulta."<br><br>";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
      $banderaTabla = 1;
    }
    if($banderaTabla == 1){
      //$tablaMesAnno = $_SESSION['periodoActual'];

      $consulta = " SELECT pm.id,pm.Documento, pm.NumCompra, d.descripcion, pm.Numero, pm.Tipo, pm.ValorTotal, pm.Nombre, pm.TipoTransporte, pm.Placa, pm.FechaMYSQL, pm.Anulado, pm.Aprobado FROM productosmov$tablaMesAnno pm left join documentos d on pm.Documento = d.Tipo where 1=1 ";

      if(isset($_POST["diai"]) && $_POST["diai"] != "" ){
        $diainicial = $_POST["diai"];
        $consulta = $consulta." and DAYOFMONTH(pm.FechaMYSQL) >= ".$diainicial." ";
      }

      if(isset($_POST["diaf"]) && $_POST["diaf"] != "" ){
        $diafinal = $_POST["diaf"];
        $consulta = $consulta." and DAYOFMONTH(pm.FechaMYSQL) <= ".$diafinal." ";
      }

      $consulta = $consulta." order by pm.Id desc  ";

      //echo "<br><br>".$consulta."<br><br>";

      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
      ?>


<div class="row"> <div class="col-xs-12"> <hr> </div> </div>


<div class="row">
  <div class="col-xs-12">
<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover selectableRows" id="box-table-movimientos" >
        <thead>
          <tr>
            <th>Id</th>
            <th>Tipo documento</th>
            <th>Documento</th>
            <th> Número de compra </th>
            <th> Valor </th>
            <th>Tercero</th>
            <th> Transporte </th>
            <th> Placa </th>
            <th>Fecha</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php

          if($resultado->num_rows >= 1){
            while($row = $resultado->fetch_assoc()) { ?>

              <tr>
                <td align="center"> <?php echo $row['id']; ?> </td>
                <td><?php echo $row['descripcion']; ?></td>
                <td><?php echo $row['Tipo']; ?></td>
                <td style="text-align:center;"><?php echo $row['NumCompra']; ?></td>
                <td style="text-align:right;"><?php
                $ValorTotal = $row['ValorTotal'];
                setlocale(LC_MONETARY, 'es_CO');
                echo "$ ".number_format($ValorTotal, 2, ".", ",");
                ?></td>
                <td><?php echo $row['Nombre']; ?></td>
                <td><?php echo $row['TipoTransporte']; ?></td>
                <td style="text-align:center;"><?php echo $row['Placa']; ?></td>
                <td align="center">
                  <?php

                  $date = date_create($row['FechaMYSQL']);
                  echo date_format($date, 'd/m/Y h:i:s a');
                  ?>
                </td>
                <td align="center">
                  <?php
                  /*
                  Convención para los estados de un movimiento
                  1 = Pendiente
                  2 = Aprobado
                  3 = Anulado
                  */
                  $estado = '1';
                  if($row['Anulado'] == 1){
                    echo "Anulado";
                    $estado = 3;
                  }
                  else if($row['Aprobado'] == 1){
                    echo "Aprobado";
                    $estado = 2;
                  } else{
                    echo "Pendiente";
                    $estado = 1;
                  }
                  ?>
                  <input type="hidden" class="indice" name="indice<?php echo $row['id']; ?>" id="indice<?php echo $row['id']; ?>" value="<?php echo $row['id']; ?>">
                  <input type="hidden" class="estado" name="estado<?php echo $row['id']; ?>" id="estado<?php echo $row['id']; ?>" value="<?php echo $estado; ?>">
                </td>
              </tr>


              <?php } } ?>



            </tbody>
          </table>
</div>
</div>
</div>






      <?php } } ?>










        
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







<form class="" name="formEditar" id="formEditar" action="movimiento_editar.php" method="post">
    <input type="hidden" name="formEditarId" id="formEditarId" value="">
    <input type="hidden" name="formEditarEstado" id="formEditarEstado" value="">
    <input type="hidden" name="formEditarTabla" id="formEditarTabla" value="<?php //echo $tablaMesAnno; ?>">
</form>

<form class="" name="formVer" id="formVer" action="movimiento_ver.php" method="post">
    <input type="hidden" name="formVerId" id="formVerId" value="">
    <input type="hidden" name="formVerEstado" id="formVerEstado" value="">
    <input type="hidden" name="formVerTabla" id="formVerTabla" value="<?php //echo $tablaMesAnno; ?>">
</form>












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
<script src="<?php echo $baseUrl; ?>/modules/movimientos/js/movimientos.js"></script>


<!-- Page-Level Scripts -->

<?php mysqli_close($Link); ?>

</body>
</html>