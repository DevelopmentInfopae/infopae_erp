<?php 
include "../../header.php";

if ($permisos['informes'] == "0") {
?>  <script type="text/javascript">
        window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php 
    exit(); 
}
else {
?>  <script type="text/javascript">
		const list = document.querySelector(".li_informes");
		list.className += " active ";
		const list2 = document.querySelector(".li_informeManipuladorasSede");
		list2.className += " active ";
	</script>
<?php
}

$consultaSemanas = " SELECT DISTINCT semana AS semana FROM sedes_cobertura ";
$respuestaSemanas = $Link->query($consultaSemanas) or die ('Error al consultar los meses ln 20');
if ($respuestaSemanas->num_rows > 0) {
	while ($dataSemanas = $respuestaSemanas->fetch_assoc()) {
		$semanas[] = $dataSemanas['semana'];
	}
}
					
$nameLabel = get_titles('informes', 'informeManipuladorasSede', $labels);
$titulo = $nameLabel;
?>

<div class="row wrapper wrapper-content	white-bg page-heading">
	<div class="col-md-6 col-sm-12">
		<h2><?= $titulo ?></h2>
		<ol class="breadcrumb">
			<li> <a href="<?= $baseUrl ?>">Inicio </a></li>
			<li class="active"><strong><?= $titulo ?></strong></li>
		</ol>
	</div>
</div>

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

<form name="formInformes" id="formInformes" method = "POST" >
    <input type="hidden" id='semana' name='semana' value=''>
</form>

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

<script src="<?= $baseUrl; ?>/modules/manipuladorasedes/js/manipuladorasedes.js"></script>