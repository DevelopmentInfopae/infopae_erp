<?php
  include '../../header.php';
  $titulo = 'Manipuladoras valores nomina';
?>

<!-- Button trigger modal -->
<input type="hidden" name="inputBaseUrl" id="inputBaseUrl" value="<?php echo $baseUrl; ?>">

<div class="modal inmodal fade" id="modalEliminarValorManipuladora" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar el valor manipuladora nomina?</span>
         <input type="hidden" name="idValorManipuladora" id="idValorManipuladora">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarManipuladoraValorNomina()">Si</button>
     </div>
   </div>
 </div>
</div>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div> 
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="crearValores"><i class="fa fa-plus"></i> Nuevo </a>
    </div>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  	<div class="row">
    	<div class="col-lg-12">
      		<div class="ibox float-e-margins">
        		<div class="ibox-content contentBackground">
        			<table id="box-table" class="table table-striped table-hover selectableRows">
        				<thead id="tHeadValores">
			              
			            </thead>
			            <tbody id="tBodyValores">
			              
			            </tbody>
			            <tfoot id="tFootValores">
			              
			            </tfoot>
        			</table>	
        		</div>
    		</div>
		</div>
	</div>
</div>

<form action="manipuladora_valor_nomina_editar.php" method="post" name="formEditarManipuladoraValorNomina" id="formEditarManipuladoraValorNomina">
  <input type="hidden" name="idValorManipuladora" id="idValorManipuladora">
</form>


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
<script src="<?php echo $baseUrl; ?>/modules/manipuladoras_valores_nomina/js/manipuladoras_valores_nomina.js"></script>

</body>
</html>