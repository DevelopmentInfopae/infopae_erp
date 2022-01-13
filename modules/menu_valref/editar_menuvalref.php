<?php 
$titulo = 'Editar aportes calóricos y nutricionales';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];

if ($permisos['menus'] == "0") {
  ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

if ($_SESSION['perfil'] == "0" || $permisos['menus'] == "2") {} else { echo "<script>location.href='$baseUrl';</script>"; } 

if(isset($_POST['idvalref'])){$idvalref = $_POST['idvalref'];} else {echo "<script>alert('Error al obtener datos de aportes calóricos y nutricionales');location.href='index.php';</script>";}

$consulta = "SELECT * FROM menu_valref_nutrientes WHERE id = '".$idvalref."'";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
  $menuValRef = $resultado->fetch_assoc();
} else {
  echo "<script>alert('Error al obtener datos de aportes calóricos y nutricionales');location.href='index.php';";
}


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
        <button class="btn btn-primary submitValrefEditar"><span class="fa fa-check"></span>  Guardar</button>
      </div>
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form row" id="formValRef">
            <input type="hidden" name="idvalref" value="<?php echo $idvalref; ?>">
            <div class="form-group col-sm-3">
              <label>Tipo de complemento</label>
              <?php 
                $consTipoComplemento = "SELECT * FROM tipo_complemento WHERE CODIGO = '".$menuValRef['Cod_tipo_complemento']."'";
                $resTipoComplemento = $Link->query($consTipoComplemento);
                if ($resTipoComplemento->num_rows > 0) {
                  if ($tipoComplemento = $resTipoComplemento->fetch_assoc()) { 
                    ?>
                    <input type="text" name="" value="<?php echo $tipoComplemento['CODIGO']; ?>" class="form-control" readonly>
                    <input type="hidden" name="complemento" value="<?php echo $tipoComplemento['CODIGO']; ?>">
                  <?php }
                }
                 ?>
            </div>
            <div class="form-group col-sm-3">
              <label>Grupo etario</label>
              <?php 
                $consGrupoEtario = "SELECT * FROM grupo_etario WHERE ID = '".$menuValRef['Cod_Grupo_Etario']."'";
                $resGrupoEtario = $Link->query($consGrupoEtario);
                if ($resGrupoEtario->num_rows > 0) {
                  if ($grupoEtario = $resGrupoEtario->fetch_assoc()) {
                    ?>
                    <input type="text" name="" value="<?php echo $grupoEtario['DESCRIPCION']; ?>" class="form-control" readonly>
                    <input type="hidden" name="grupoEtario" value="<?php echo $grupoEtario['ID']; ?>">
                    <input type="hidden" name="nomGETA" value="<?php echo $grupoEtario['DESCRIPCION']; ?>">
                    <?php }
                }
                 ?>
            </div>
            <hr class="col-sm-11">
            <div class="form-group col-sm-3">
              <label>Calorías (kcal)</label>
              <input type="number" name="kcalxg" class="form-control" value="<?php echo $menuValRef['kcalxg'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Kcal desde la grasa</label>
              <input type="number" name="kcaldgrasa" class="form-control" value="<?php echo $menuValRef['kcaldgrasa'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Grasa saturada</label>
              <input type="number" name="Grasa_Sat" class="form-control" value="<?php echo $menuValRef['Grasa_Sat'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Grasa poliinsaturada</label>
              <input type="number" name="Grasa_poliins" class="form-control" value="<?php echo $menuValRef['Grasa_poliins'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Grasa monoinsaturada</label>
              <input type="number" name="Grasa_monoins" class="form-control" value="<?php echo $menuValRef['Grasa_Monoins'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Grasa trans</label>
              <input type="number" name="Grasa_Trans" class="form-control" value="<?php echo $menuValRef['Grasa_Trans'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Fibra dietaria</label>
              <input type="number" name="Fibra_dietaria" class="form-control" value="<?php echo $menuValRef['Fibra_dietaria'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Azúcares</label>
              <input type="number" name="Azucares" class="form-control" value="<?php echo $menuValRef['Azucares'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Proteínas</label>
              <input type="number" name="Proteinas" class="form-control" value="<?php echo $menuValRef['Proteinas'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Colesterol</label>
              <input type="number" name="Colesterol" class="form-control" value="<?php echo $menuValRef['Colesterol'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Sodio</label>
              <input type="number" name="Sodio" class="form-control" value="<?php echo $menuValRef['Sodio'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Zinc</label>
              <input type="number" name="Zinc" class="form-control" value="<?php echo $menuValRef['Zinc'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Calcio</label>
              <input type="number" name="Calcio" class="form-control" value="<?php echo $menuValRef['Calcio'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Hierro</label>
              <input type="number" name="Hierro" class="form-control" value="<?php echo $menuValRef['Hierro'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina A</label>
              <input type="number" name="Vit_A" class="form-control" value="<?php echo $menuValRef['Vit_A'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina C</label>
              <input type="number" name="Vit_C" class="form-control" value="<?php echo $menuValRef['Vit_C'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina B1</label>
              <input type="number" name="Vit_B1" class="form-control" value="<?php echo $menuValRef['Vit_B1'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina B2</label>
              <input type="number" name="Vit_B2" class="form-control" value="<?php echo $menuValRef['Vit_B2'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Vitamina B3</label>
              <input type="number" name="Vit_B3" class="form-control" value="<?php echo $menuValRef['Vit_B3'] ?>" min="0" step=".01" required>
            </div>
            <div class="form-group col-sm-3">
              <label>Ácido fólico</label>
              <input type="number" name="Acido_Fol" class="form-control" value="<?php echo $menuValRef['Acido_Fol'] ?>" min="0" step=".01" required>
            </div>
          </form>
            <div class="col-sm-12">
              <button class="btn btn-primary submitValrefEditar"><span class="fa fa-check"></span>  Guardar</button>
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