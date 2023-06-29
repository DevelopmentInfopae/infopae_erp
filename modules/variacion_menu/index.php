<?php
    include '../../header.php';

    if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
      ?><script type="text/javascript">
          window.open('<?= $baseUrl ?>', '_self');
      </script>
    <?php exit(); }

else {
  ?><script type="text/javascript">
    const list = document.querySelector(".li_configuracion");
    list.className += " active ";
    const list2 = document.querySelector(".li_variacionMenu");
    list2.className += " active ";
  </script>
  <?php
  }

    $titulo = 'Variación Menú'; 
    $nameLabel = get_titles('configuracion', 'variacionMenu', $labels);
    $titulo = $nameLabel;
?> 

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div> 
  <div class="col-lg-4">
      <div class="title-action">
        <a href="#" class="btn btn-primary" id="crearVariacionMenu"><i class="fa fa-plus"></i> Nuevo </a>
      </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table id="box-table" class="table table-striped table-hover selectableRows table-responsive">
            <thead>
              <tr>
                <th>Identificador</th>
                <th>Descripción</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
            <?php  
            $consulta = "SELECT id, descripcion FROM variacion_menu;";
            $respuesta = $Link->query($consulta) or die('Error al consultar la tabla variacion menu' . mysqli_error($consulta));
            // exit(var_dump($respuesta));
            if ($respuesta->num_rows > 0) {
              while ($dataRespuesta = $respuesta->fetch_assoc()) {
            ?>
            <tr>
              <td><?php echo $dataRespuesta['id']; ?></td>
              <td><?php echo $dataRespuesta['descripcion']; ?></td>
              <td align="left">
                <div class="btn-group">
                  <div class="dropdown">
                    <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Acciones <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                      <li><a href="#" class="editarVariacionMenu" data-idvariacionmenu = <?php echo $dataRespuesta['id']; ?> ><i class="fas fa-pencil-alt"></i> Editar</a></li>
                      <li><a data-toggle="modal" data-target="#modalEliminarVariacionMenu"  data-idvariacionmenu = <?php echo $dataRespuesta['id']; ?> ><span class="fa fa-trash"></span>  Eliminar</a></li>
                    </ul>
                  </div>
                </div>
              </td>
            </tr>
            <?php
              }
            }
            ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Identificador</th>
                <th>Descripción</th>
                <th>Acciones</th>
              </tr>
            </tfoot>
          </table> <!-- table -->
        </div> <!-- ibox-content -->
      </div> <!-- ibox -->
    </div> <!-- .col-lg-12 -->
  </div> <!-- row -->
</div> <!-- wrapper -->

<div id="contenedor_crear_variacion_menu"></div>
<div id="contenedor_editar_variacion_menu"></div>

<!-- modal eliminar -->
<input type="hidden" name="inputBaseUrl" id="inputBaseUrl" value="<?php echo $baseUrl; ?>">

<div class="modal inmodal fade" id="modalEliminarVariacionMenu" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header text-info" style="padding: 15px;">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
          <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
        </div>
        <div class="modal-body" style="text-align: center;">
            <span>¿Está seguro de borrar la variación menú?</span>
            <input type="hidden" name="idVariacionMenu" id="idVariacionMenu">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="eliminarVariacionMenu()"><i class="fa fa-check"></i> Si </button>
        </div>
      </div>
  </div>
</div>

<?php include '../../footer.php'; ?>

<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/variacion_menu/js/variacion_menu.js"></script>