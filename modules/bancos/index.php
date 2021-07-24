<?php 
$titulo = 'Bancos';
require_once '../../header.php';

if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    ?><script type="text/javascript">
        window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php exit(); }

$periodoActual = $_SESSION['periodoActual'];
?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-md-12">
    	<h2><?php echo $titulo; ?></h2>
    	<ol class="breadcrumb">
	      	<li>
	        	<a href="<?php echo $baseUrl; ?>">Inicio</a>
	      	</li>
	      	<li class="active">
	        	<strong><?php echo $titulo; ?></strong>
	      	</li>
    	</ol>
  	</div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  	<div class="row">
    	<div class="col-lg-12">
      		<div class="ibox float-e-margins">
        		<div class="ibox-content contentBackground">
        			<table class="table selectableRows table-hover table-striped" id="box-table">
			            <thead id="tHeadBancos">
			              
			            </thead>
			            <tbody id="tBodyBancos">
			              
			            </tbody>
			            <tfoot id="tFootBancos">
			              
			            </tfoot>
          			</table>
        		</div>
    		</div>
		</div>
	</div>
</div>

<?php include '../../footer.php'; ?>
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
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jvectormap/jquery-jvectormap-co-merc.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/bancos/js/bancos.js"></script>


<script type="text/javascript">

</script>


</body>
</html>