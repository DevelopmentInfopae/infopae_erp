<?php
include '../../header.php';
// validaciones para poder entrar a este modulo 
if ($permisos['entrega_complementos'] == "0") {
?>	<script type="text/javascript">
    	window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php 
exit(); }
else {
?>	<script type="text/javascript">
      	const list = document.querySelector(".li_entrega_complementos");
      	list.className += " active ";
    </script>
<?php
}

require_once '../../db/conexion.php'; // establecemos la conexion
set_time_limit (0); // seteamos el limite de tiempo de ejecucion en todo el recurso
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];

$con_cod_muni = "SELECT CodMunicipio FROM parametros;";
$res_minicipio = $Link->query($con_cod_muni) or die(mysqli_error($Link));
if ($res_minicipio->num_rows > 0) {
    $codigoDANE = $res_minicipio->fetch_array();
}
$nameLabel = get_titles('entregaComplementos', 'certificadoInstitucion', $labels);
?>

<!-- setion title -->
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-8">
    	<h2><?= $nameLabel ?></h2>
      	<ol class="breadcrumb">
        	<li>
          		<a href="<?php echo $baseUrl; ?>">Inicio</a>
        	</li>
        	<li class="active">
          		<strong><?= $nameLabel ?></strong>
        	</li>
      	</ol>
  	</div>
  	<div class="col-lg-4">
    	<div class="title-action">
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <h2>Parámetros de Consulta</h2>
          <form class="col-lg-12" action="certificados_rector.php" name="formPlanillas" id="formPlanillas" method="post" target="_blank">
            <div class="row">

            	<div class="col-sm-4 form-group">
            		<label for="fechaInicial">Municipio</label>
            		<select class="form-control" name="municipio" id="municipio" required>
            			<option value="">Seleccione uno</option>
            			<?php              
                  $consulta = "SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE ETC = 0 ";
                  if($_SESSION['perfil'] == 6){
                    $rectorDocumento = $_SESSION['num_doc'];
                    $consulta = "SELECT ubicacion.ciudad as ciudad, ubicacion.codigoDANE from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.codigoDANE where cc_rector = $rectorDocumento";
                   }
            			$DepartamentoOperador = $_SESSION['p_CodDepartamento'];
            			if($DepartamentoOperador != ''){
            				$consulta = $consulta." and CodigoDANE like '$DepartamentoOperador%' ";
            			}
            			$consulta = $consulta." order by ciudad asc ";
            			$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
            			if($resultado->num_rows >= 1){
            				while($row = $resultado->fetch_assoc()) { ?>
            					<option value="<?php echo $row["codigoDANE"]; ?>"  <?php  if((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] == $row["codigoDANE"]) || ($codigoDANE["CodMunicipio"] == $row["codigoDANE"])){ echo " selected "; } ?> ><?php echo $row["ciudad"]; ?></option>
            					<?php
            				}// Termina el while
            			}//Termina el if que valida que si existan resultados
            			?>
            		</select>
            		<input type="hidden" name="municipioNm" id="municipioNm">
            	</div><!-- /.col -->

            	<div class="col-sm-4 form-group">
            		<label for="institucion">Institución</label>
            		<select class="form-control" name="institucion" id="institucion">
            			<option value="">Todas</option>
            			<?php
            			if(isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" || $codigoDANE["CodMunicipio"]){
            				$municipio = $_GET["pb_municipio"] = $codigoDANE["CodMunicipio"];
            				$consulta = " select distinct s.cod_inst, s.nom_inst from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
            				$consulta = $consulta." and s.cod_mun_sede = '$municipio' ";
            				$consulta = $consulta." order by s.nom_inst asc ";
            				$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
            				if($resultado->num_rows >= 1){
            					while($row = $resultado->fetch_assoc()) { ?>
            						<option value="<?php echo $row['cod_inst']; ?>" <?php if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?php echo $row['nom_inst']; ?></option>
            					<?php }// Termina el while
            				}//Termina el if que valida que si existan resultados
            			}
            			?>
            		</select>
            	</div><!-- /.col -->
            </div><!-- /.row -->

            <div class="row">
              <div class="col-sm-4 form-group">
                <label for="fechaInicial">Mes</label>
                <select name="mes" id="mes" class="form-control" required>
                  <option value="">Seleccione uno</option>
                  <option value="1" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 1) {echo " selected "; } ?>>Enero</option>
                  <option value="2" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 2) {echo " selected "; } ?>>Febrero</option>
                  <option value="3" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 3) {echo " selected "; } ?>>Marzo</option>
                  <option value="4" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 4) {echo " selected "; } ?>>Abril</option>
                  <option value="5" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 5) {echo " selected "; } ?>>Mayo</option>
                  <option value="6" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 6) {echo " selected "; } ?>>Junio</option>
                  <option value="7" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 7) {echo " selected "; } ?>>Julio</option>
                  <option value="8" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 8) {echo " selected "; } ?>>Agosto</option>
                  <option value="9" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 9) {echo " selected "; } ?>>Septiembre</option>
                  <option value="10" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 10) {echo " selected "; } ?>>Octubre</option>
                  <option value="11" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 11) {echo " selected "; } ?>>Noviembre</option>
                  <option value="12" <?php if (isset($_GET['pb_mes']) && $_GET['pb_mes'] == 12) {echo " selected "; } ?>>Diciembre</option>
                </select>
                <input type="hidden" name="mesConsulta" id="mesConsulta" value="<?php if (isset($_GET['pb_mes'])) { echo $_GET['pb_mes']; } ?>">
              </div><!-- /col -->

              <div class="col-sm-4 form-group">
                <label for="semana_inicial">Semana Inicial</label>
                <select class="form-control" name="semana_inicial" id="semana_inicial" required>
                  <option value="">Seleccione uno</option>
                </select>
                <input type="hidden" name="diaInicialSemanaInicial" id="diaInicialSemanaInicial">
                <input type="hidden" name="diaFinalSemanaInicial" id="diaFinalSemanaInicial">
              </div>

              <div class="col-sm-4   form-group">
                <label for="semana_final">Semana Final</label>
                <select class="form-control" name="semana_final" id="semana_final" required>
                  <option value="">Seleccione uno</option>
                </select>
                <input type="hidden" name="diaInicialSemanaFinal" id="diaInicialSemanaFinal">
                <input type="hidden" name="diaFinalSemanaFinal" id="diaFinalSemanaFinal">
              </div>
            </div><!-- /.row -->

            <div class="row">
              <div class="col-sm-12">
                <h3>Tipo de certificado</h3>
              </div>
            </div><!-- /.row -->

            <div class="row">
              <div class="col-sm-2 form-group">
            		<div class="i-checks"><label> <input type="radio" value="1" name="tipoPlanilla" required><i></i> Certificado Normal </label> </div>
            	</div><!-- /.col -->

            	<div class="col-sm-2 form-group">
            		<div class="i-checks"> <label> <input type="radio" value="2" name="tipoPlanilla" required><i></i> Certificado por días </label> </div>
              </div><!-- /.col -->
            </div><!-- /.row -->

            <div class="row">
              <div class="col-sm-3 form-group">
                <input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_GET['consultar']) && $_GET['consultar'] != '') {echo $_GET['consultar']; } ?>" >
                <button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong>Buscar</strong></button>
              </div>
            </div>
          </form>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<!-- iCheck -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script>
	$(document).ready(function () {
	  $('.i-checks').iCheck({
		  checkboxClass: 'icheckbox_square-green',
		  radioClass: 'iradio_square-green',
	  });
	});
</script>
<script src="<?php echo $baseUrl; ?>/modules/impresion_planillas/js/certificados.js"></script>

<?php mysqli_close($Link); ?>

</body>
</html>
