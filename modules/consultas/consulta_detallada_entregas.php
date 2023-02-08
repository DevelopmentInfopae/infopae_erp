<?php
$titulo = 'Consulta resumida de entregas';
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
if ($permisos['entrega_complementos'] == "0") {
  ?><script type="text/javascript">
        window.open('<?= $baseUrl ?>', '_self');
     </script>
<?php exit(); }
 else {
  ?><script type="text/javascript">
    const list = document.querySelector(".li_entrega_complementos");
    list.className += " active ";
  </script>
<?php
}
$nameLabel = get_titles('entregaComplementos', 'consultaResumida', $labels);

?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?= $nameLabel ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong><?= $nameLabel ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      <a href="#" onclick="actualizarDespacho()" target="_self" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar Cambios </a>
      -->
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form action="consulta_detallada_entregas.php" method="post" name="parametros" id="parametros">
            <?php
              //Se va a capturar el año actual para sacar la subcadena del periodo
              //que permitira saber que tablas consultar de acuerdo al año.

              if (isset($_POST['annoinicial']) && $_POST['annoinicial']!= '') {
                $annoactual = $_POST['annoinicial'];
              }
              else{
                $annoactual = $_SESSION['periodoActualCompleto'];
              }
              $_SESSION['annoactual'] = $annoactual;


              $periodoactual = substr($annoactual, 2, 2);

              $mesinicial = 'mm';
              if(isset($_POST["mesinicial"]) && $_POST["mesinicial"] != "" ){
                $mesinicial = $_POST["mesinicial"];
                // if($mesinicial < 10){
                //   $mesinicial = '0'.$mesinicial;
                // }
              }
              $mesinicialConsulta = $mesinicial;

              $_SESSION['mesinicialConsulta'] = $mesinicialConsulta;
              $departamento = $_SESSION['p_CodDepartamento'];
            ?>


            <div class="row">

              <div class="col-sm-3 form-group">
                <label for="departamento">Departamento</label>
                <?php
                  $consulta = "select distinct departamento from ubicacion where ETC = 0 and codigodane like '$departamento%' order by departamento asc";
                  $result = $Link->query($consulta);
                ?>
                <select class="form-control" name="departamento" id="departamento" onchange="buscar_municipios();">
                  <?php while($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $row["departamento"]; ?>"><?php echo $row["departamento"]; ?></option>
                  <?php } ?>
                </select>
              </div><!-- /.col -->

              <div class="col-sm-3 form-group">
                <label for="municipio">Municipio</label>
                <select id="municipio" name="municipio" onchange="buscar_instituciones();" class="form-control">
                  <?php
            if (isset($departamento) && $departamento != "") {

              $vsql = "select distinct ciudad, codigodane from ubicacion where ETC = 0 and codigodane like '$departamento%' order by ciudad asc";

              $result = $Link->query($vsql);

            ?>
              <option value="">TODOS</option>
            <?php
                while($row = $result->fetch_assoc()) {  ?>
                  <option value="<?php echo $row["codigodane"]; ?>"


                  <?php if (isset($_POST['municipio']) && ($_POST['municipio'] == $row["codigodane"]) ) {
                    echo ' selected ';
                  }  ?>
                  ><?php echo $row["ciudad"]; ?></option>
          <?php }
          } else{?> <option value="">TODOS</option> <?php }  ?>

            </select>


              </div><!-- /.col -->


              <div class="col-sm-3 form-group">
                <label for="departamento">Institución</label>
                <!-- onchange="buscar_sedes();" -->
                <select id="institucion" name="institucion" onchange="buscar_sedes();" class="form-control">
                             <?php
                          if (isset($_POST["municipio"]) && $_POST["municipio"] != "") {

                            $municipio = $_POST["municipio"];

                            $vsql = "select distinct cod_inst, nom_inst from sedes".$periodoactual." where cod_mun_sede = '$municipio' order by nom_inst asc";
                            $result = $Link->query($vsql);



                          ?>
                            <option value="">TODOS</option>
                          <?php
                               while($row = $result->fetch_assoc()) {  ?>

                          <option value="<?php echo $row["cod_inst"]; ?>" <?php if (isset($_POST['institucion']) && ($_POST['institucion'] == $row["cod_inst"]) ) {
                            echo 'selected';
                          }  ?>   ><?php echo $row["nom_inst"]; ?></option>

                              <?php }}

                          else{?> <option value="">TODOS</option> <?php }  ?>
                </select>
              </div><!-- /.col -->



              <div class="col-sm-3 form-group">
                <label for="sede">Sede</label>
                <select id="sede" name="sede" onchange="buscar_estudiantes();" class="form-control">
             <?php
            if (isset($_POST["institucion"]) && $_POST["institucion"] != "") {

              $institucion = $_POST["institucion"];

              $vsql = "select distinct cod_sede, nom_sede from sedes".$periodoactual." where cod_inst = '$institucion' order by nom_sede asc";

                $result = $Link->query($vsql);




                ?>
                <option value="">TODOS</option>

                <?php




                while($row = $result->fetch_assoc()) {  ?>

            <option value="<?php echo $row["cod_sede"]; ?>" <?php  if (isset($_POST['sede']) && ($_POST['sede'] == $row["cod_sede"]) ) {
              echo ' selected ';
            } ?>   ><?php echo utf8_encode($row["nom_sede"]); ?></option>

                <?php }
}

 else{?> <option value="">TODOS</option> <?php }  ?>
            </select>
              </div><!-- /.col -->



<div class="col-sm-3 form-group">
  <label for="">Fecha Inicial</label>
   <div class="row compositeDate">
     <div class="col-sm-4 nopadding">
       <?php
 $periodoActualCompleto = $_SESSION['periodoActualCompleto'];
 ?>
                  <select name="annoinicial" id="annoinicial" onchange="actualizaranno();" class="form-control">



                    <option value="<?php echo $periodoActualCompleto; ?>" selected="selected"> <?php echo $periodoActualCompleto; ?> </option>



                  </select>
     </div><!-- /.col -->
    <div class="col-sm-4 nopadding">
      <select name="mesinicial" id="mesinicial" onchange="actualizarmes();" class="form-control">
        <option value="">mm</option>


                      <?php
                      $vsql="select distinct mes from planilla_dias";
                      $result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
                      while($row = $result->fetch_assoc()) { ?>


                      <option value="<?php echo $row['mes']; ?>" <?php if (isset($_POST['mesinicial']) && $_POST['mesinicial'] == $row['mes'] ) {echo " selected "; } ?>><?php


                      $aux = $row['mes'];

                      switch ($aux) {
                        case "01":
                          echo "Enero";
                          break;
                        case "02":
                          echo "Febrero";
                          break;
                        case "03":
                          echo "Marzo";
                          break;
                          case "04":
                          echo "Abril";
                          break;
                          case "05":
                          echo "Mayo";
                          break;
                          case "06":
                          echo "Junio";
                          break;
                          case "07":
                          echo "Julio";
                          break;
                          case "08":
                          echo "Agosto";
                          break;
                          case "09":
                          echo "Septiembre";
                          break;
                          case "10":
                          echo "Octubre";
                          break;
                          case "11":
                          echo "Noviembre";
                          break;
                          case "12":
                          echo "Diciembre";
                          break;
                      }







                       ?></option>

                     <?php  } ?>







                       </select>
                     </div><!-- /.col -->



<div class="col-sm-4 nopadding">
  <select class="form-control" name="diainicial" id="diainicial">
    <option value="">dd</option>
    <option value="1" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 1) {echo " selected "; } ?>>01</option>
    <option value="2" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 2) {
      echo " selected ";
    } ?>>02</option>
    <option value="3" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 3) {
      echo " selected ";
    } ?>>03</option>
    <option value="4" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 4) {
      echo " selected ";
    } ?>>04</option>
    <option value="5" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 5) {
      echo " selected ";
    } ?>>05</option>
    <option value="6" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 6) {
      echo " selected ";
    } ?>>06</option>
    <option value="7" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 7) {
      echo " selected ";
    } ?>>07</option>
    <option value="8" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 8) {
      echo " selected ";
    } ?>>08</option>
    <option value="9" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 9) {
      echo " selected ";
    } ?>>09</option>
    <option value="10" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 10) {
      echo " selected ";
    } ?>>10</option>
    <option value="11" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 11) {
      echo " selected ";
    } ?>>11</option>
    <option value="12" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 12) {
      echo " selected ";
    } ?>>12</option>
    <option value="13" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 13) {
      echo " selected ";
    } ?>>13</option>
    <option value="14" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 14) {
      echo " selected ";
    } ?>>14</option>
    <option value="15" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 15) {
      echo " selected ";
    } ?>>15</option>
    <option value="16" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 16) {
      echo " selected ";
    } ?>>16</option>
    <option value="17" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 17) {
      echo " selected ";
    } ?>>17</option>
    <option value="18" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 18) {
      echo " selected ";
    } ?>>18</option>
    <option value="19" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 19) {
      echo " selected ";
    } ?>>19</option>
    <option value="20" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 20) {
      echo " selected ";
    } ?>>20</option>
    <option value="21" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 21) {
      echo " selected ";
    } ?>>21</option>
    <option value="22" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 22) {
      echo " selected ";
    } ?>>22</option>
    <option value="23" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 23) {
      echo " selected ";
    } ?>>23</option>
    <option value="24" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 24) {
      echo " selected ";
    } ?>>24</option>
    <option value="25" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 25) {
      echo " selected ";
    } ?>>25</option>
    <option value="26" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 26) {
      echo " selected ";
    } ?>>26</option>
    <option value="27" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 27) {
      echo " selected ";
    } ?>>27</option>
    <option value="28" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 28) {
      echo " selected ";
    } ?>>28</option>
    <option value="29" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 29) {
      echo " selected ";
    } ?>>29</option>
    <option value="30" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 30) {
      echo " selected ";
    } ?>>30</option>
    <option value="31" <?php if (isset($_POST['diainicial']) && $_POST['diainicial'] == 31) {
      echo " selected ";
    } ?>>31</option>
  </select>
</div><!-- /.col -->







</div><!-- /.row compositeDate -->
</div><!-- /.col -->




  <div class="col-sm-3 form-group">
    <label for="">Fecha Final</label>
    <div class="row compositeDate">
      <div class="col-sm-4 nopadding">
        <input class="form-control" type="text" name="annofinal"  id="annofinal" readonly="readonly" value="<?php echo $annoactual; ?>" >
      </div><!-- /.col -->
      <div class="col-sm-4 nopadding">
        <input class="form-control" type="text" name="mesfinalnm"  id="mesfinalnm" readonly="readonly" value="<?php


               $aux = $mesinicial;




                      switch ($aux) {
                        case "mm":
                          echo "mm";
                          break;
                        case "01":
                          echo "Enero";
                          break;
                        case "02":
                          echo "Febrero";
                          break;
                        case "03":
                          echo "Marzo";
                          break;
                          case "04":
                          echo "Abril";
                          break;
                          case "05":
                          echo "Mayo";
                          break;
                          case "06":
                          echo "Junio";
                          break;
                          case "07":
                          echo "Julio";
                          break;
                          case "08":
                          echo "Agosto";
                          break;
                          case "09":
                          echo "Septiembre";
                          break;
                          case "10":
                          echo "Octubre";
                          break;
                          case "11":
                          echo "Noviembre";
                          break;
                          case "12":
                          echo "Diciembre";
                          break;
                      }






              ?>">
              <input type="hidden" name="mesfinal"  id="mesfinal" readonly="readonly" value="<?php echo $mesinicial; ?>">
      </div><!-- /.col -->
      <div class="col-sm-4 nopadding">
        <select name="diafinal" id="diafinal" class="form-control">
                  <option value="">dd</option>
                  <option value="1" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 1) {echo " selected "; } ?> >01</option>
                  <option value="2" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 2) {echo " selected "; } ?> >02</option>
                  <option value="3" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 3) {echo " selected "; } ?> >03</option>
                  <option value="4" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 4) {echo " selected "; } ?> >04</option>
                  <option value="5" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 5) {echo " selected "; } ?> >05</option>
                  <option value="6" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 6) {echo " selected "; } ?> >06</option>
                  <option value="7" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 7) {echo " selected "; } ?> >07</option>
                  <option value="8" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 8) {echo " selected "; } ?> >08</option>
                  <option value="9" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 9) {echo " selected "; } ?> >09</option>
                  <option value="10" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 10) {echo " selected "; } ?> >10</option>
                  <option value="11" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 11) {echo " selected "; } ?> >11</option>
                  <option value="12" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 12) {echo " selected "; } ?> >12</option>
                  <option value="13" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 13) {echo " selected "; } ?> >13</option>
                  <option value="14" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 14) {echo " selected "; } ?> >14</option>
                  <option value="15" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 15) {echo " selected "; } ?> >15</option>
                  <option value="16" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 16) {echo " selected "; } ?> >16</option>
                  <option value="17" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 17) {echo " selected "; } ?> >17</option>
                  <option value="18" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 18) {echo " selected "; } ?> >18</option>
                  <option value="19" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 19) {echo " selected "; } ?> >19</option>
                  <option value="20" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 20) {echo " selected "; } ?> >20</option>
                  <option value="21" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 21) {echo " selected "; } ?> >21</option>
                  <option value="22" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 22) {echo " selected "; } ?> >22</option>
                  <option value="23" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 23) {echo " selected "; } ?> >23</option>
                  <option value="24" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 24) {echo " selected "; } ?> >24</option>
                  <option value="25" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 25) {echo " selected "; } ?> >25</option>
                  <option value="26" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 26) {echo " selected "; } ?> >26</option>
                  <option value="27" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 27) {echo " selected "; } ?> >27</option>
                  <option value="28" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 28) {echo " selected "; } ?> >28</option>
                  <option value="29" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 29) {echo " selected "; } ?> >29</option>
                  <option value="30" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 30) {echo " selected "; } ?> >30</option>
                  <option value="31" <?php if (isset($_POST['diafinal']) && $_POST['diafinal'] == 31) {echo " selected "; } ?> >31</option>
              </select>
      </div><!-- /.col -->
    </div><!-- /.row compositeDate -->
  </div><!-- /.col -->
</div><!-- /.row -->


















<div class="row">
  <div class="col-sm-5 form-group">
    <div class="row">
      <div class="col-sm-6 nopadding">
        <input type="checkbox" name="detallar" id="detallar" <?php if (isset($_POST['detallar'])) {
                echo ' checked ';
              } ?> />
              Detallar estudiantes
      </div><!-- .col -->
      <div class="col-sm-6 nopadding">
        <input type="checkbox" name="graficar" id="graficar" <?php if (isset($_POST['graficar'])) {
          echo ' checked ';
        } ?> />
        Graficar segmentación de estudiantes
      </div><!-- .col -->
    </div><!-- /.row -->
  </div><!-- /.col -->
</div><!-- /.row -->

  <div class="row">
    <div class="col-sm-3 form-group">
      <input type="hidden" id="municipio_nm" name="municipio_nm" value="">
      <input type="hidden" id="institucion_nm" name="institucion_nm" value="">
      <input type="hidden" id="sede_nm" name="sede_nm" value="">
      <input type="hidden" id="estudiante_nm" name="estudiante_nm" value="">
      <input type="hidden" id="resultado" name="resultado" value="mostrar">
      <button type="button" name="enviar" id="enviar" onclick="enviarForm();" class="btn btn-primary">Buscar</button>
    </div><!-- /.col -->
  </div><!-- /.row -->
</form>

<?php //var_dump($_POST); ?>
<?php
  if (count($_POST)>0) {
    // $mes = $_POST['mesinicial'];
    // $annoinicial = $_POST['annoinicial'];
    // $municipio = $_POST['municipio'];
    // $institucion = $_POST['institucion'];
    // $sede = $_POST['sede'];
    //
    // $dias="select * from planilla_dias where ano = '$annoinicial' and mes = '$mes'";
    // $result = $Link->query($dias) or die ('Unable to execute query. '. mysqli_error($Link));
    // $rowDias = $result->fetch_assoc();
    //
    // $annoinicial = substr($annoinicial, 2, 2);
    include 'det_resumen.php';
    if (isset($_POST['graficar'])) { include 'det_grafica.php'; }
    if (isset($_POST['detallar'])) {include 'det_titulares.php'; }

  }// Termina el if que valida que se reciban los parametros post
?>

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

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/consultas/js/select_anidados.js"></script>

<!-- Page-Level Scripts -->
<?php  if (count($_POST)>0) { ?>
  <script type="text/javascript">
  $(document).ready( function () {
                  $('#box-table-dr').DataTable({
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



                  var anchoTabla = $('#box-table-dr').width();


              });
   </script>

  <?php if (isset($_POST['graficar'])) { ?>
    <!-- ChartJS-->
    <script src="<?php echo $baseUrl; ?>/theme/js/plugins/chartJs/Chart.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/modules/consultas/js/consultas_graficos_detallado.js"></script> <?php
  } if (isset($_POST['detallar'])) { ?>
    <script type="text/javascript">
      $(document).ready( function () {
        $('#box-table-d').DataTable({
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
    <?php
  }
} ?>















<?php mysqli_close($Link); ?>

</body>
</html>
