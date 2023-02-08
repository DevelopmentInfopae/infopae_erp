<?php 
include "../../header.php";

if ($permisos['informes'] == "0") {
?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }
	  else {
		?><script type="text/javascript">
		  const list = document.querySelector(".li_informes");
		  list.className += " active ";
		</script>
		<?php
		}
$consultaMeses = " SHOW TABLES LIKE 'entregas_res_%' ";
$respuestaMeses = $Link->query($consultaMeses) or die ('Error al consultar los meses ln 12');
if ($respuestaMeses->num_rows > 0) {
	while ($dataMeses = $respuestaMeses->fetch_assoc()) {
		$aux = (array_values($dataMeses));
		$meses[] = substr($aux[0], 13, -2);
	}
}

$mesesNom = [ 	"01" => "ENERO", 
					"02" => "FEBRERO", 
					"03" => "MARZO", 
					"04" => "ABRIL", 
					"05" => "MAYO", 
					"06" => "JUNIO",
					"07" => "JULIO",
					"08" => "AGOSTO",
					"09" => "SEPTIEMBRE",
					"10" => "OCTUBRE",
					"11" => "NOVIEMBRE",
					"12" => "DICIEMBRE" ];
					
$nameLabel = get_titles('informes', 'informeChip', $labels);
$titulo = $nameLabel;
?>

<div class="row wrapper wrapper-content	white-bg page-heading">
	<div class="col-md-6 col-sm-12">
		<h2><?= $titulo ?></h2>
		<ol class="breadcrumb">
			<li> <a href="<?= $baseUrl ?>">Home </a></li>
			<li class="active"><strong><?= $titulo ?></strong></li>
		</ol>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-12">
            <div class="ibox float-e-margins"> 
				<div class="ibox-content contentBackground"> 
                    <h2>Parámetros de Consulta</h2>
					<form name="formInformes" id="formInformes" method = "POST" >
                        <div class="row">
                            <div class="col-md-3 col-sm-12 form-group">
                                <label for="tipoInforme">Tipo de Informe*</label>
                                <select name="tipoInforme" id="tipoInforme" class="form-control" required='true'>
                                    <option value="">Seleccione</option>
                                    <option value="1">Informe de población</option>
                                    <option value="2">Formato 3 Ejecución de recursos</option>
                                    <option value="3">Formato</option>
                                </select>
                            </div><!--  col -->
							<div class="col-md-3 col-sm-12 form-group"> 
                                <label for="mes">Mes*</label>
                                <select class="form-control" name="mes" id="mes" required='true'>
									<!-- <option value=""> Seleccione... </option> -->
									<?php foreach ($meses as $key => $value): ?>
										<option value="<?= $value ?>" <?= !(isset($meses[$key+1])) ? 'selected' : '' ?>  > <?= $mesesNom[$value] ?> </option>
									<?php endforeach ?>
								</select>
                            </div><!--  col-md-3 -->
                        </div><!--  row -->
                        <div class="row">
							<div class="col-md-3 col-sm-12 form-group">
								<button class="btn btn-primary" name="btnBuscar" id="btnBuscar" value="1"><strong><i class="fa fa-search"></i> Consultar</strong></button>
							</div>
						</div> <!--  row -->
                    </form>     
                </div><!--  ibox-content -->
            <!-- </div> ibox -->
        </div> <!--  col-12 -->
    </div> <!--  row -->
</div> <!-- wrapper -->

<div class="wrapper wrapper-content animated fadeInRight rowTable" style="display: none;">
	<div class="row">
		<div class="col-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<div class="" id='table'>

					</div><!-- table-responsive -->
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div> <!-- col -->
	</div> <!-- row -->
</div> <!-- wrapper -->

<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
										

<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toggle/toggle.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<script src="<?= $baseUrl; ?>/modules/chip/js/chip.js"></script>

