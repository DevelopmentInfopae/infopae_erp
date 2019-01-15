<?php 
$titulo = 'Nuevos aportes calóricos y nutricionales';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];
?>

<style type="text/css">

</style>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="index.php">Aportes calóricos y nutricionales</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
      <div class="title-action">
        <button class="btn btn-primary submitValref"><span class="fa fa-check"></span>  Guardar</button>
      </div>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form row" id="formValRef">
            <div class="form-group col-sm-3">
              <label>Tipo de complemento</label>
              <select class="form-control" name="complemento" id="complemento" required>
                <option value="">Seleccione...</option>
                <?php 
                $consTipoComplemento = "SELECT * FROM tipo_complemento";
                $resTipoComplemento = $Link->query($consTipoComplemento);
                if ($resTipoComplemento->num_rows > 0) {
                  while ($tipoComplemento = $resTipoComplemento->fetch_assoc()) { ?>
                    <option value="<?php echo $tipoComplemento['CODIGO'] ?>"><?php echo $tipoComplemento['CODIGO'] ?></option>
                  <?php }
                }
                 ?>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Grupo etario</label>
              <select class="form-control" name="grupoEtario" id="grupoEtario" required>
                <option value="">Seleccione...</option>
                <?php 
                $consGrupoEtario = "SELECT * FROM grupo_etario";
                $resGrupoEtario = $Link->query($consGrupoEtario);
                if ($resGrupoEtario->num_rows > 0) {
                  while ($grupoEtario = $resGrupoEtario->fetch_assoc()) { ?>
                    <option value="<?php echo $grupoEtario['ID'] ?>"><?php echo $grupoEtario['DESCRIPCION'] ?></option>
                  <?php }
                }
                 ?>
              </select>
              <input type="hidden" name="nomGETA" id="nomGETA">
            </div>
            <hr class="col-sm-11">
            <div class="form-group col-sm-3">
              <label>Calorías (Kcal)</label>
              <input type="number" name="kcalxg" id="kcalxg" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Kcal desde la grasa</label>
              <input type="number" name="kcaldgrasa" id="kcaldgrasa" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Grasa saturada</label>
              <input type="number" name="Grasa_Sat" id="Grasa_Sat" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Grasa poliinsaturada</label>
              <input type="number" name="Grasa_poliins" id="Grasa_poliins" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Grasa monoinsaturada</label>
              <input type="number" name="Grasa_Monoins" id="Grasa_Monoins" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Grasa trans</label>
              <input type="number" name="Grasa_Trans" id="Grasa_Trans" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Fibra dietaria</label>
              <input type="number" name="Fibra_dietaria" id="Fibra_dietaria" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Azúcares</label>
              <input type="number" name="Azucares" id="Azucares" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Proteínas</label>
              <input type="number" name="Proteinas" id="Proteinas" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Colesterol</label>
              <input type="number" name="Colesterol" id="Colesterol" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Sodio</label>
              <input type="number" name="Sodio" id="Sodio" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Zinc</label>
              <input type="number" name="Zinc" id="Zinc" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Calcio</label>
              <input type="number" name="Calcio" id="Calcio" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Hierro</label>
              <input type="number" name="Hierro" id="Hierro" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina A</label>
              <input type="number" name="Vit_A" id="Vit_A" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina C</label>
              <input type="number" name="Vit_C" id="Vit_C" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina B1</label>
              <input type="number" name="Vit_B1" id="Vit_B1" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina B2</label>
              <input type="number" name="Vit_B2" id="Vit_B2" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina B3</label>
              <input type="number" name="Vit_B3" id="Vit_B3" class="form-control" min='0' required>
            </div>
            <div class="form-group col-sm-3">
              <label>Ácido Fólico</label>
              <input type="number" name="Acido_Fol" id="Acido_Fol" class="form-control" min='0' required>
            </div>
          </form>
            <div class="col-sm-12">
              <button class="btn btn-primary submitValref"><span class="fa fa-check"></span>  Guardar</button>
            </div>
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<form method="Post" id="ver_infraestructura" action="ver_infraestructura.php" style="display: none;">
  <input type="hidden" name="idinfraestructura" id="idinfraestructuraver">
</form>
<form method="Post" id="editar_infraestructura" action="editar_infraestructura.php" style="display: none;">
  <input type="hidden" name="idinfraestructura" id="idinfraestructuraeditar">
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/menu_valref/js/menu_valref.js"></script>

<?php mysqli_close($Link); ?>

</body>
</html>