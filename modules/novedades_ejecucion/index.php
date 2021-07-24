<?php
  include '../../header.php';

  if ($permisos['novedades'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit();}

  $titulo = 'Novedades de focalización';

  $codigo_municipio = $_SESSION['p_Municipio'];
  $codigo_departamento = $_SESSION['p_CodDepartamento'];
?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?= $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li class="active">
        <strong><?= $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
			<?php if($_SESSION['perfil'] == "0" || $permisos['novedades'] == "2"){ ?>
				<a href="#" class="btn btn-primary" onclick="crearNovedadPriorizacion();"><i class="fa fa-plus"></i> Nuevo </a>
			<?php } ?>
		</div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-sm-12">
      <div class="ibox">
        <div class="ibox-content">
          <form class="form" method="post" name="formulario_buscar_novedades" id="formulario_buscar_novedades">
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="municipio">Municipio *</label>
                  <select class="form-control select2" name="municipio" id="municipio" required="required">
                    <option value="">seleccione</option>
                    <?php
                      $parametro_municipio = (! empty($codigo_municipio)) ? "AND CodigoDANE = '$codigo_municipio'" : "";
                      $consulta_municipios = "SELECT CodigoDANE AS codigo, Ciudad AS nombre FROM ubicacion WHERE CodigoDANE LIKE '$codigo_departamento%' $parametro_municipio ORDER BY Ciudad ASC;";
                      $respuesta_consulta_municipios = $Link->query($consulta_municipios) or die('Error al consultar municipios: '. $Link->error);
                      if (! empty($respuesta_consulta_municipios->num_rows))
                      {
                        while($municipio = $respuesta_consulta_municipios->fetch_object())
                        {
                          $codigo = $municipio->codigo;
                          $nombre = $municipio->nombre;
                          $seleccion = ($codigo == $codigo_municipio) ? 'selected' : '';
                          echo '<option value="'. $codigo .'" '. $seleccion .'>'. $nombre .'</option>';
                        }
                      }
                    ?>
                  </select>
                  <label class="error" style="display: none" for="municipio"></label>
                </div>
              </div>

              <div class="col-sm-4 form-group">
                <label for="institucion">Institución *</label>
                <select class="form-control select2" name="institucion" id="institucion" required="required">
                  <option value="">seleccione</option>
                  <?php
                    $consulta_instituciones = "SELECT codigo_inst AS codigo, nom_inst AS nombre FROM instituciones WHERE cod_mun = '". $codigo_municipio ."' ORDER BY nom_inst";
                    $respuesta_consulta_instituciones = $Link->query($consulta_instituciones) or die ('Error al consultar instituciones: '. $Link->error);
                    if(! empty($respuesta_consulta_instituciones->num_rows))
                    {
                      while($institucion = $respuesta_consulta_instituciones->fetch_object())
                      {
                        $codigo = $institucion->codigo;
                        $nombre = $institucion->nombre;
                        echo '<option value="'. $codigo .'">'. $nombre .'</option>';
                      }
                    }
                  ?>
                </select>
                <label class="error" style="display: none" for="institucion"></label>
              </div>

              <div class="col-sm-4 form-group">
                <label for="sede">Sede</label>
                <select class="form-control select2" name="sede" id="sede" required="required">
                  <option value="">seleccione</option>
                </select>
                <label class="error" style="display: none;" for="sede"></label>
              </div>

            </div>

            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label for="mes">Mes *</label>
                  <select class="form-control select2" name="mes" id="mes" required="required">
                    <option value="">seleccione</option>
                  </select>
                  <label class="error" style="display: none" for="mes"></label>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="form-group">
                  <label for="semana">Semana *</label>
                  <select class="form-control select2" name="semana" id="semana" required="required">
                    <option value="">seleccione</option>
                  </select>
                  <label class="error" style="display: none;" for="semana"></label>
                </div>
              </div>

              <div class="col-sm-4 form-group">
                <label for="tipo_complemento">Tipo complemento *</label>
                <select class="form-control select2" name="tipo_complemento" id="tipo_complemento" required="required">
                  <option value="">seleccione</option>
                </select>
                <label class="error" style="display: none;" for="tipo_complemento"></label>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12 form-group">
                <button class="btn btn-primary pull-right" type="button" id="boton_buscar_novedades" name="boton_buscar_novedades" value="1"><i class="fa fa-search"></i>  Buscar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-content">
          <table class="table table-striped table-hover selectableRows tabla_novedades_focalizacion">
            <thead>
              <tr>
                <th>ID</th>
                <th>Municipio</th>
                <th>Institución</th>
                <th>Sede</th>
                <th>Tipo Doc.</th>
                <th>Documento</th>
								<th>Titular</th>
                <th>Complemento</th>
                <th>Semana</th>
                <th>L</th>
                <th>M</th>
                <th>X</th>
                <th>J</th>
                <th>V</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
								<th>ID</th>
								<th>Municipio</th>
								<th>Institución</th>
								<th>Sede</th>
								<th>Tipo Doc.</th>
								<th>Documento</th>
								<th>Titular</th>
								<th>Complemento</th>
								<th>Semana</th>
								<th>L</th>
								<th>M</th>
								<th>X</th>
								<th>J</th>
								<th>V</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Información InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p class="text-center"></p>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="codigoACambiar">
        <input type="hidden" id="estadoACambiar">
        <button type="button" class="btn btn-primary btn-outline btn-sm" data-dismiss="modal" onclick="revertirEstado();">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="cambiarEstado();">Aceptar</button>
      </div>
    </div>
  </div>
</div>

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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/novedades_ejecucion/js/novedades_ejecucion.js"></script>

<form action="novedades_priorizacion_ver.php" method="post" name="formVerNovedad" id="formVerNovedad">
  <input type="hidden" name="idNovedad" id="idNovedad">
</form>

</body>
</html>
