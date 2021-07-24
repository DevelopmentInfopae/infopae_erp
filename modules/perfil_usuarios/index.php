 <?php
    include '../../header.php';

    if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
      ?><script type="text/javascript">
          window.open('<?= $baseUrl ?>', '_self');
      </script>
    <?php exit(); }

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
					<div class="table-responsive">
					<table id="box-table" class="table table-striped table-hover selectableRows">
						<thead>
							<tr>
								<th>Identificador</th>
								<th>Nombre</th>
								<?php if ($_SESSION['perfil'] == "1" || $_SESSION['perfil'] == "0"): ?>
									<td align="center"><b>Acciones</b></td>
								<?php endif ?>	
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
								<?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) { ?>
									<td align="center">
										<div class="btn-group">
                           					<div class="dropdown">
                            					<button class="btn btn-primary btn-sm" type="button" id="accionesProducto" data-toggle="dropdown" aria-haspopup="true">
                             					 Acciones
                             	 					<span class="caret"></span>
                            					</button>
                           	 					<ul class="dropdown-menu pull-right" aria-labelledby="accionesProducto">
													<?php if ($dataRespuesta['id'] != "0"): ?>
														<li><a onclick="editarPermisos(<?= $dataRespuesta['id']; ?>)"><span class="fas fa-pencil-alt"></span>  Editar</a></li>
													<?php endif ?>
                            					</ul>
                          					</div>
                        				</div>
									</td>
								<?php } ?>	
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
								<?php if ($_SESSION['perfil'] == "1" || $_SESSION['perfil'] == "0"): ?>
									<td align="center"><b>Acciones</b></td>
								<?php endif ?>								
							</tr>
						</tfoot>
					</table>
					</div>
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- wrapper -->

<form method="Post" id="editarPerfilPermisos" action="editarPermisos.php" target="_blank" style="display: none;">
  <input type="hidden" name="id" id="id">
</form>

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