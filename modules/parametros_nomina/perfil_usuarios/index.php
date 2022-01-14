 <?php
    include '../../header.php';
    $titulo = 'Perfil Usuarios'; 
?> 

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-12">
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
								<th>Nombre</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$consulta = "SELECT id, nombre FROM perfiles;";
							$respuesta = $Link->query($consulta) or die('Error al consultar los perfiles');
							if ($respuesta->num_rows > 0) {
								while ($dataRespuesta = $respuesta->fetch_assoc()) {
							?>
							<tr>
								<td><?php echo $dataRespuesta['id']; ?></td>
								<td><?php echo $dataRespuesta['nombre'] ?></td>
							</tr>
							<?php
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th>Identificador</th>
								<th>Nombre</th>
							</tr>
						</tfoot>
					</table>
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- wrapper -->

<?php include '../../footer.php'; ?>

<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/perfil_usuarios/js/perfil_usuarios.js"></script>