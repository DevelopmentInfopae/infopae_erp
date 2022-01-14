<?php
include '../../header.php';

if ($permisos['entrega_complementos'] == "0") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

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
      <!-- <h2><?php echo $institucionNombre; ?></h2> -->
      <!-- <h4><?php echo $institucionCodigo; ?></h4> -->
      <h2>Ver registros biométricos</h2>
      <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl; ?>">Home</a>
          </li>
          <li class="active">
              <strong>Ver registros biométricos</strong>
          </li>
      </ol>
  </div>
  <div class="col-lg-4">
      <div class="title-action">
         <!--
          <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
          <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
        -->
          <!-- <a href="<?php echo $baseUrl; ?>/modules/despachos/despacho_nuevo.php" target="_self" class="btn btn-primary"><i class="fa fa-truck"></i> Nuevo despacho </a> -->
      </div>
  </div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->





<div class="wrapper wrapper-content  animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-content">
          <!-- <h3>Seleccione la semana de focalización</h3> -->
          <?php
          $consulta = " select distinct semana from planilla_semanas ";
          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
          if($resultado->num_rows >= 1){
            while($row = $resultado->fetch_assoc()){
              $aux = $row['semana'];
              $consulta2 = " show tables like 'focalizacion$aux' ";
              $resultado2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
              if($resultado2->num_rows >= 1){
               $semanas[] = $aux;
              }
            }
          }
          //var_dump($semanas);
          ?>
          <div class="row">
            <div class="col-sm-12">
              <form class="" action="" id="formSedes" name="formSedes" method="get">
                <div class="row">


									<div class="col-sm-3 form-group">
							      <label for="institucion">Semana</label>
							      <select class="form-control" name="semana" id="semana" required>
							        <option value="">Seleccionar</option>
							        <?php
							          $consulta = " select distinct semana from planilla_semanas ";
							          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
							            if($resultado->num_rows >= 1){
							              while($row = $resultado->fetch_assoc()) { ?>
							                <option value="<?php echo $row['semana']; ?>" <?php if(isset($_GET["semana"]) && $_GET["semana"] == $row['semana'] ){ echo " selected "; }  ?> > <?php echo $row['semana']; ?></option>
							              <?php }// Termina el while
							            }//Termina el if que valida que si existan resultados
							        ?>
							      </select>
							    </div><!-- /.col -->





									<div class="col-sm-2 form-group">
        <label for="fechaInicial">Municipio</label>
        <select class="form-control" name="municipio" id="municipio" required>
    <option value="">Seleccione uno</option>
    <?php
    $consulta = " select DISTINCT codigoDANE, ciudad from ubicacion where 1=1 ";

    $DepartamentoOperador = $_SESSION['p_CodDepartamento'];
    if($DepartamentoOperador != ''){
      $consulta = $consulta." and CodigoDANE like '$DepartamentoOperador%' ";
    }
    $consulta = $consulta." order by ciudad asc ";
    //echo $consulta;






    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
    <option value="<?php echo $row["codigoDANE"]; ?>"  <?php  if(isset($_GET["municipio"]) && $_GET["municipio"] == $row["codigoDANE"] ){ echo " selected "; } ?> ><?php echo $row["ciudad"]; ?></option>
    <?php
    }// Termina el while
    }//Termina el if que valida que si existan resultados
    ?>
    </select>
    </div><!-- /.col -->






    <div class="col-sm-3 form-group">
        <label for="institucion">Institución</label>
        <select class="form-control" name="institucion" id="institucion">
    <option value="">Todas</option>
    <?php
    if(isset($_GET["municipio"]) && $_GET["municipio"] != "" ){
        $municipio = $_GET["municipio"];
        $consulta = " select distinct s.cod_inst, s.nom_inst from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
        $consulta = $consulta." and s.cod_mun_sede = '$municipio' ";

        $consulta = $consulta." order by s.nom_inst asc ";



          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
          if($resultado->num_rows >= 1){
            while($row = $resultado->fetch_assoc()) { ?>
              <option value="<?php echo $row['cod_inst']; ?>" <?php if(isset($_GET["institucion"]) && $_GET["institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?php echo $row['nom_inst']; ?></option>
            <?php }// Termina el while
          }//Termina el if que valida que si existan resultados
    }
    ?>
    </select>
    </div><!-- /.col -->

     <div class="col-sm-3 form-group">
        <label for="sede">Sede</label>
        <select class="form-control" name="sede" id="sede">
    <option value="">Todas</option>
    <?php
    if(isset($_GET["institucion"]) && $_GET["institucion"] != "" ){
        $institucion = $_GET["institucion"];
        $consulta = " select distinct s.cod_sede, s.nom_sede from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
        $consulta = $consulta." and s.cod_inst = '$institucion' ";

        $consulta = $consulta." order by s.nom_sede asc ";

        //echo $consulta;



          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
          if($resultado->num_rows >= 1){
            while($row = $resultado->fetch_assoc()) { ?>
              <option value="<?php echo $row['cod_sede']; ?>" <?php if(isset($_GET["sede"]) && $_GET["sede"] == $row['cod_sede'] ){ echo " selected "; }  ?> > <?php echo $row['nom_sede']; ?></option>
            <?php }// Termina el while
          }//Termina el if que valida que si existan resultados
    }
    ?>
    </select>
    </div><!-- /.col -->

























                  <div class="col-sm-3">
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-3 form-group">
                    <button class="btn btn-primary" type="button" id="btnBuscar">Buscar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>













<?php
if(isset($_GET["municipio"]) && $_GET['municipio'] != ''){ ?>

<div class="wrapper wrapper-content  animated fadeInRight">
      <div class="row">
          <div class="col-sm-12">
              <div class="ibox">
                  <div class="ibox-content">
                      <h2>Registros</h2>
                      <div class="row">
                        <div class="col-sm-12">
                        <div><?php
                          $municipio = 0;
                          $institucion = 0;
                          $sede = 0;
                          $semana = 0;

                          $focalizacionInicial = '01';
                          $anno = 0;
                          $mes = 0;
                          $inicio = 0;
                          $fin = 0;

                          //var_dump($_GET);

                          if(isset($_GET['municipio']) && $_GET['municipio'] != ''){
                            $municipio = $_GET['municipio'];
                          }

                          if(isset($_GET['institucion']) && $_GET['institucion'] != ''){
                            $institucion = $_GET['institucion'];
                          }

                          if(isset($_GET['sede']) && $_GET['sede'] != ''){
                            $sede = $_GET['sede'];
                          }

                          if(isset($_GET['semana']) && $_GET['semana'] != ''){
                            $semana = $_GET['semana'];
                          }



                          if($semana != 0){
														$focalizacionInicial = $semana;
                            // Consulta para buscar el día inicial y final de la semana
                            $consulta = " SELECT p.ANO, p.MES, p.SEMANA, min(p.DIA) as inicio, max(p.DIA) as fin FROM planilla_semanas p WHERE semana = '$semana' group by p.SEMANA ";
                            //echo "<br>".$consulta."<br><br><br>";
                            $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                            if($resultado->num_rows >= 1){
                              $row = $resultado->fetch_assoc();
                              //var_dump($row);
                              $anno = $row['ANO'];
                              $mes = $row['MES'];
                              $inicio = $row['inicio'];
                              $fin = $row['fin'];
                            }
                          }

                          // Consulta para la busqueda
													$banderaTablaFocalizacion = 0;
													$consulta = "SHOW TABLES LIKE 'focalizacion$focalizacionInicial'";
													if ($resultado = $Link->query($consulta) ) {
        										if(!$resultado->num_rows >= 1) {
															$banderaTablaFocalizacion ++;
														}
													}












                          $consulta = " SELECT br.* , s.nom_inst, s.nom_sede, f.nom1, f.nom2, f.ape1, f.ape2 FROM biometria_reg br left join biometria b on br.dispositivo_id = b.id_dispositivo and br.usr_dispositivo_id = b.id_bioest left join sedes$periodoActual s on b.cod_sede = s.cod_sede left join focalizacion$focalizacionInicial f on b.tipo_doc = f.tipo_doc and b.num_doc = f.num_doc COLLATE utf8_unicode_ci WHERE 1 = 1 ";
                          if($municipio != 0){
                            $consulta .= " and s.cod_mun_sede = $municipio ";
                          }
                          if($institucion != 0){
                            $consulta .= " and s.cod_inst = $institucion ";
                          }
                          if($sede != 0){
                            $consulta .= " and s.cod_sede = $sede ";
                          }
                          if($semana != 0){
                            $consulta .= " AND DATE(fecha) BETWEEN DATE('$anno-$mes-$inicio') AND DATE('$anno-$mes-$fin')  ";
                            // $consulta .= " and fecha BETWEEN '$anno-$mes-$inicio' AND '$anno-$mes-$fin' ";

                          }

                          //echo "<br>".$consulta."<br><br><br>";



                        ?></div>
                        <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-sedes" >
                    <thead>
                    <tr>
                        <th>Id Dispositivo</th>
                        <th>Id Usr Disp</th>
                        <th>Fecha</th>
                        <th>Institución</th>
                        <th>Sede</th>
                        <th>Titular</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
												if($banderaTablaFocalizacion == 0){


                        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                        if($resultado->num_rows >= 1){
                            while($row = $resultado->fetch_assoc()) { ?>
                                <tr>
                                  <td><?php
                                  $aux = '';
                                  $aux .=  $row["dispositivo_id"];
                                  echo $aux;
                                  ?></td>
                                  <td><?php
                                  $aux = '';
                                  $aux .=  $row["usr_dispositivo_id"];
                                  echo $aux;
                                  ?></td>
                                  <td><?php
                                  $aux = '';
                                  $aux .=  $row["fecha"];
                                  echo $aux;
                                  ?></td>
                                  <td><?php
                                  $aux = '';
                                  $aux .=  $row["nom_inst"];
                                  echo $aux;
                                  ?></td>
                                  <td><?php
                                  $aux = '';
                                  $aux .=  $row["nom_sede"];
                                  echo $aux;
                                  ?></td>
                                  <td><?php
                                  $aux = '';
                                  $aux .=  $row["nom1"];
                                  $aux .= " ";
                                  $aux .=  $row["nom2"];
                                  $aux .=  $row["ape1"];
                                  $aux .= " ";
                                  $aux .=  $row["ape2"];
                                  echo $aux;
                                  ?></td>
                                </tr>
                            <?php
                            }// Termina el while
                        }//Termina el if que valida que si existan resultados
											}?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Id Dispositivo</th>
                        <th>Id Usr Disp</th>
                        <th>Fecha</th>
                        <th>Institución</th>
                        <th>Sede</th>
                        <th>Titular</th>
                    </tr>
                    </tfoot>
                    </table>
                        </div>
                        <!-- Termina table responsive -->
                        </div>
                      </div>
                  </div>
              </div>
          </div>

      </div><!-- /.row -->
  </div>

<?php } ?>










































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
		<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>


    <script src="<?php echo $baseUrl; ?>/modules/registros_biometricos/js/registros_biometricos.js"></script>



    <!-- Page-Level Scripts -->


<?php mysqli_close($Link); ?>




    <!-- Page-Level Scripts -->
    <script>
        $(document).ready(function(){
            $('.dataTables-sedes').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                  oLanguage: {
      sLengthMenu: 'Mostrando _MENU_ registros',
      sZeroRecords: 'No se encontraron registros',
      sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
      sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
      sInfoFiltered: '(Filtrado desde _MAX_ registros)',
      sSearch:         'Buscar: ',
      oPaginate:{
        sFirst:    'Primero',
        sLast:     'Último',
        sNext:     'Siguiente',
        sPrevious: 'Anterior'
      }
    },
                buttons: [ {extend: 'excel', title: 'ExampleFile'} ]

            });

        });

    </script>

</body>
</html>
