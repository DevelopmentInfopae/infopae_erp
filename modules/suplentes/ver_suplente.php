<?php 
$titulo = 'Suplente';
require_once '../../header.php';

if ($permisos['titulares_derecho'] == "0") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

set_time_limit(0);
ini_set('memory_limit','6000M');
// exit(var_dump($_POST));
$periodoActual = $_SESSION['periodoActual'];
$id = (isset($_POST['id']) && $_POST['id'] != '') ? mysqli_real_escape_string($Link, $_POST['id']) : '';
$semana = (isset($_POST['semana']) && $_POST['semana'] != '') ? mysqli_real_escape_string($Link, $_POST['semana']) : '';
$suplente = [];  

$consultaSuplente = "SELECT 
                      td.nombre AS tipoDocumento, 
                      s.num_doc AS documento, 
                      CONCAT(s.nom1, ' ', s.nom2, ' ', s.ape1, ' ', s.ape2) AS nombre, 
                      s.genero, 
                      s.dir_res AS direccion, 
                      u.ciudad, 
                      s.telefono, 
                      e.nombre AS estrato, 
                      s.sisben, d.nombre AS discapacidad, 
                      et.descripcion AS etnia, 
                      pb.nombre AS poblacionVictima, 
                      s.nom_sede AS sede, 
                      s.nom_inst AS institucion, 
                      s.cod_grado AS grado, 
                      s.nom_grupo AS grupo, 
                      j.nombre AS jornada, 
                      s.edad, 
                      s.nom_acudiente AS nombreAcudiente, 
                      s.doc_acudiente AS documentoAcudiente, 
                      s.tel_acudiente AS telefonoAcudiente, 
                      s.parentesco_acudiente AS parentescoAcudiente 
                    FROM suplentes$semana AS s
                    LEFT JOIN tipodocumento AS td ON s.tipo_doc = td.id
                    LEFT JOIN ubicacion AS u ON s.cod_mun_nac = u.codigoDANE
                    LEFT JOIN estrato AS e ON s.cod_estrato = e.id
                    LEFT JOIN discapacidades AS d ON s.cod_discap = d.id
                    LEFT JOIN etnia AS et ON s.etnia = et.id
                    LEFT JOIN pobvictima AS pb ON s.cod_pob_victima = pb.id
                    LEFT JOIN jornada AS j ON s.cod_jorn_est = j.id
                    WHERE s.id = $id; 
  ";

$respuestaSuplente = $Link->query($consultaSuplente) or die ('Error al consultar la suplencia ' .mysqli_error($Link));
if ($respuestaSuplente->num_rows > 0) {
  $dataSuplente = $respuestaSuplente->fetch_assoc();
  $suplente = $dataSuplente;
}

// seccion para traer los datos de la tabla consumo de complementos alimentarios
$consultaMeses = "SELECT DISTINCT(mes) FROM planilla_semanas WHERE semana <=".$semana.";";
$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses de entrega' . mysqli_error($Link));
if ($respuestaMeses->num_rows > 0) {
  while ($dataMeses = $respuestaMeses->fetch_assoc()) {
    $meses[] = $dataMeses['mes'];
  }
}

foreach ($meses as $key => $mes) {
  $N = 1;
  $consultaSemanas = "SELECT DISTINCT(semana) FROM planilla_semanas WHERE mes = '".$mes."';"; 
  $respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar las semanas del mes ' . mysqli_error($Link));
  if ($respuestaSemanas->num_rows > 0) {
    while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
      $D = '';
      $DC = '';
      $dias = [];
      $numeroDiasSemana = 0;
      $consultaDias = "SELECT DISTINCT(dia) FROM planilla_semanas WHERE semana = '" .$dataSemanas['semana']. "';";
      $respuestaDias = $Link->query($consultaDias) or die ('Error al consultar los días de la semana ' . mysqli_error($Link));
      if ($respuestaDias->num_rows > 0) {
        while ($dataDias = $respuestaDias->fetch_assoc()) {
          $dias[] = $dataDias;
        }
        $numeroDiasSemana = count($dias);
        for ($i=0; $i < $numeroDiasSemana ; $i++) { 
          $D .= "D".$N.",";
          $N++;
          $DC .= "D".$N. "!= 0 OR ";
        }
        $D = substr($D, 0, -1);
        $DC = substr($DC, 0, -3);
      }
      $consultaEntregas = "SELECT tipo_complem, $D FROM entregas_res_$mes$periodoActual WHERE tipo = 'S' AND num_doc = '" .$suplente['documento']. "' AND ($DC);";
      // echo $consultaEntregas;
      $respuestaEntregas = $Link->query($consultaEntregas) or die ('Error al consultar el consumo de alimentos ' .mysqli_error($Link));
      if ($respuestaEntregas->num_rows > 0) {
        while ($dataEntregas = $respuestaEntregas->fetch_assoc()) {
          $consumos[$dataSemanas['semana']] = $dataEntregas;
        }
      }
    }
  }
}
// exit(var_dump($consumos));
?>

<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-8 col-sm-10">
    <h2>Suplentes</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?= $baseUrl; ?>">Home</a>
      </li>
      <li>
        <a href="<?= $baseUrl;?>/modules/suplentes/index.php">Suplentes</a>
      </li>
      <li class="active">
        <strong>Suplente</strong>
      </li>
    </ol>
  </div> <!-- col-lg-8 -->
  <div class="col-lg-4 col-sm-2">
    <div class="title-action">
      <?php if ($_SESSION['perfil'] == "0" || $permisos['titulares_derecho'] == "2") : ?>
      <div class="dropdown pull-right">
        <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">  Acciones <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
          <li><a  onclick="editar_suplente(<?php echo $id;?>, '<?php echo $semana; ?>')"><span class="fas fa-pencil-alt"></span> Editar </a>
          </li>
        </ul>
      </div><!--  dropdown -->
      <?php endif ?> 
    </div> <!-- title-action -->
  </div><!--  col-lg-4 -->
</div> <!-- page-heading -->

<div class="wrapper wrapper-content">
  <div class="row animated fadeInRight">
    <div class="col-lg-4 col-sm-12">
      <div class="row">

        <div class="ibox float-e-margins">
          <div class="ibox-title">
            <h4 style="text-align: center;"><strong><?php echo $suplente['nombre']; ?></strong></h4>
          </div>
          <div class="ibox-content no-padding border-left-right">
          <?php
            if($suplente['genero'] == 'M'){
              $ruta = 'img/ninno.jpg';
              $icono = 'fa-male';
              $sexo = 'Masculino';
            }else{
              $ruta = 'img/ninna.jpg';
              $icono = 'fa-female';
              $sexo = 'Femenino';
            }
          ?>
            <img alt="image" class="img-responsive" src="<?php echo $ruta; ?>">
          </div>

          <div class="ibox-content profile-content">

            <p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-id-card"></i></span><strong>Tipo Documento</strong></p>
            <p style="text-align: center;"> <?php echo $suplente['tipoDocumento']; ?> </p>

            <p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-id-card"></i></span><strong>Número Documento</strong></p>
            <p style="text-align: center;"> <?php echo $suplente['documento']; ?> </p>

            <p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa <?php echo $icono; ?>"></i></span><strong>Género</strong></p>
            <p style="text-align: center;"> <?php echo $sexo; ?> </p>

            <p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-map-marker"></i></span><strong>Dirección</strong></p>
            <p style="text-align: center;"> <?php echo $suplente['direccion']; ?> </p>

            <p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-map-marker"></i></span><strong>Ciudad</strong></p>
            <p style="text-align: center;"> <?php echo $suplente['ciudad']; ?> </p>

            <p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-phone"></i></span><strong>Teléfono</strong></p>
            <p style="text-align: center;"> <?php echo $suplente['telefono']; ?> </p>

            <p style="text-align: center;"> <span style="width: 20px; display: inline-block; text-align: center;"><i class="fa fa-child"></i></span><strong>Edad</strong></p>
            <p style="text-align: center;"> <?php echo $suplente['edad']; ?> </p>

          </div>
        </div> <!-- float-e-margins -->

      </div> <!-- row -->
    </div> <!-- col-lg-4 -->

    <div class="col-lg-8 col-sm-12">
      <div class="row">
        <div class="col-md-12">
          <?php if (isset($consumos) && !empty($consumos)) { ?>  

          <div class="ibox float-e-margins">
            <div class="ibox-title">
              <h5>Consumo de Complementos Alimetarios</h5>
              <div class="ibox-tools">
                <a class="collapse-link">
                  <i class="fa fa-chevron-up"></i>
                </a>
                <a class="close-link">
                  <i class="fa fa-times"></i>
                </a>
              </div> <!-- ibox-tools -->
            </div>  <!-- ibox-title -->
            <div class="ibox-content">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <th>Semana</th>
                    <th>Complemento</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                  </thead>
                  <tbody>
                    <?php foreach ($consumos as $semana => $value) { ?>
                      <tr>
                        <td><?= $semana; ?></td>
                        <?php foreach ($value as $key => $D) { ?>
                          <td><?php $consumio = ''; if($D == '1'){$consumio = 'X';} elseif($D == '0'){$consumio = '-';} else{$consumio = $D;} echo $consumio;?></td>
                        <?php } ?>
                      </tr>
                    <?php } ?>  
                  </tbody>
                  <tfoot>
                    <th>Semana</th>
                    <th>Complemento</th>
                    <th>Lunes</th>
                    <th>Martes</th>
                    <th>Miércoles</th>
                    <th>Jueves</th>
                    <th>Viernes</th>
                  </tfoot>
                </table>
              </div>  
            </div> <!-- ibox-content -->
          </div> <!-- float-e-margins -->

          <?php   } ?>

          <div class="ibox float-e-margins">
            <div class="ibox-title">
              <h5>Información Sociodemografica</h5>
              <div class="ibox-tools">
                <a class="collapse-link">
                  <i class="fa fa-chevron-up"></i>
                </a>
                <a class="close-link">
                  <i class="fa fa-times"></i>
                </a>
              </div> <!-- ibox-tools -->
            </div> <!-- ibox-title -->
            <div class="ibox-content">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>Descripción</th>
                      <th>Valor</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Estrato</td>
                      <td><?php echo $suplente['estrato']; ?></td>
                    </tr>
                    <tr>
                      <td>Discapacidad</td>
                      <td><?php echo $suplente['discapacidad']; ?></td>
                    </tr>
                    <tr>
                      <td>Etnia</td>
                      <td><?php echo $suplente['etnia']; ?></td>
                    </tr>
                    <tr>
                      <td>Población Victima</td>
                      <td><?php echo $suplente['poblacionVictima']; ?></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Descripción</th>
                      <th>Valor</th>
                    </tr>
                  </tfoot>
                </table><!--  table -->
              </div> <!-- table-responsive -->
            </div> <!-- ibox-content -->
          </div> <!-- float-e-margins -->

          <div class="ibox float-e-margins">
            <div class="ibox-title">
            <h5>Información Académica</h5>
              <div class="ibox-tools">
                <a class="collapse-link">
                  <i class="fa fa-chevron-up"></i>
                </a>
                <a class="close-link">
                  <i class="fa fa-times"></i>
                </a>
              </div> <!-- ibox-tools -->
            </div> <!-- ibox-title -->
            <div class="ibox-content">
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th>Descripción</th>
                      <th>Valor</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Institucion Educativa</td>
                      <td><?php echo $suplente['institucion']; ?></td>
                    </tr>
                    <tr>
                      <td>Sede Educativa</td>
                      <td><?php echo $suplente['sede']; ?></td>
                    </tr>
                    <tr>
                      <td>Grado</td>
                      <td><?php echo $suplente['grado']; ?></td>
                    </tr>
                    <tr>
                      <td>Grupo</td>
                      <td><?php echo $suplente['grupo']; ?></td>
                    </tr>
                    <tr>
                      <td>Jornada</td>
                      <td><?php echo $suplente['jornada']; ?></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Descripción</th>
                      <th>Valor</th>
                    </tr>
                  </tfoot>
                </table><!--  table -->
              </div> <!-- table-responsive -->
            </div> <!-- ibox-content -->
          </div> <!-- float-e-margins -->

          <div class="ibox float-e-margins">
            <div class="ibox-title">
            <h5>Información Acudiente</h5>
              <div class="ibox-tools">
                <a class="collapse-link">
                  <i class="fa fa-chevron-up"></i>
                </a>
                <a class="close-link">
                  <i class="fa fa-times"></i>
                </a>
              </div> <!-- ibox-tools -->
            </div> <!-- ibox-title -->
            <div class="ibox-content">
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>Descripción</th>
                    <th>Valor</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Nombre Acudiente</td>
                    <td><?php echo $suplente['nombreAcudiente']; ?></td>
                  </tr>
                  <tr>
                    <td>Documento Acudiente</td>
                    <td><?php echo $suplente['documentoAcudiente']; ?></td>
                  </tr>
                  <tr>
                    <td>Teléfono Acudiente</td>
                    <td><?php echo $suplente['telefonoAcudiente']; ?></td>
                  </tr>
                  <tr>
                    <td>Parentesco Acudiente</td>
                      <td><?php echo $suplente['parentescoAcudiente']; ?></td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Descripción</th>
                      <th>Valor</th>
                    </tr>
                  </tfoot>
                </table><!--  table -->
              </div> <!-- table-responsive -->
            </div> <!-- ibox-content -->
          </div> <!-- float-e-margins -->

        </div> <!-- col-md-12 -->
      </div> <!-- row -->
    </div> <!-- col-lg-8 -->

  </div> <!-- fadeInRight -->
</div> <!-- wrapper -->

<form id="formulario_editar_suplente" action="editar_suplente.php" method="post">
  <input type="hidden" name="id_suplente" id="id_suplente">
  <input type="hidden" name="semana" id="semana">
</form>

<?php include '../../footer.php'; ?>
    
<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>

<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/modules/suplentes/js/suplentes.js"></script>

<?php mysqli_close($Link); ?>
</body>
</html>
