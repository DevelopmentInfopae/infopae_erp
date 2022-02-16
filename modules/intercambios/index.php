		<?php
			include '../../header.php';
			 if ($permisos['novedades'] == "0") {
    			?><script type="text/javascript">
      				window.open('<?= $baseUrl ?>', '_self');
    			</script>
  			<?php exit();}
			$titulo = 'Usuarios';
		?>

		<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
			<div class="col-lg-8">
				<h2>Novedades de menú</h2>
				<ol class="breadcrumb">
					<li>
						<a href="<?php echo $baseUrl; ?>">Inicio</a>
					</li>
					<li class="active">
						<strong>Novedades de menú</strong>
					</li>
				</ol>
			</div>
		</div>

		<!-- Seccion de filtros -->
		<div class="wrapper wrapper-content animated fadeInRight">
			<div class="row">
				<div class="col-lg-12">
					<div class="ibox float-e-margins">
						<div class="ibox-content contentBackground">
							<table class="table table-striped table-hover selectableRows dataTablesNovedadesPriorizacion">
								<thead>
									<tr>
										<th>Mes</th>
										<th>Semana</th>
										<th>Tipo de novedad</th>
										<th>Menú</th>
										<th>Variación</th>
										<th>Tipo de complemento</th>
										<th>Grupo etario</th>
										<th>Fecha registro</th>
										<th>Fecha vencimiento</th>
										<th>Estado</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<th>Mes</th>
										<th>Semana</th>
										<th>Tipo de novedad</th>
										<th>Menú</th>
										<th>Variación</th>
										<th>Tipo de complemento</th>
										<th>Grupo etario</th>
										<th>Fecha registro</th>
										<th>Fecha vencimiento</th>
										<th>Estado</th>
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
		
		<input type="hidden" id="opcion" value="<?= $permisos['novedades'] ?>">

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
		<script src="<?php echo $baseUrl; ?>/modules/intercambios/js/novedades_menu.js"></script>

		<form action="novedades_menu_ver.php" method="post" name="formVerNovedad" id="formVerNovedad">
			<input type="hidden" name="idNovedad" id="idNovedad">
		</form>
	</body>
</html>