<?php
include '../../header.php'; 

if ($permisos['informes'] == "0") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

set_time_limit (0);
ini_set('memory_limit','6000M');
date_default_timezone_set ( 'America/Bogota' );

$titulo = "Bitácora Usuarios";
$periodo_actual = $_SESSION["periodoActual"];
$perfilUsuario = $_SESSION["perfil"];
$idUsuario = $_SESSION['idUsuario'];
$usuarios = [];
$acciones = [];
$respuestas = [];

$consultaUsuarios = "SELECT id_perfil, nombre, id FROM usuarios;";
$respuestaConsultaUsuarios = $Link->query($consultaUsuarios) or die ('Error al consultar los usuarios ' . mysqli_error($Link));
if ($respuestaConsultaUsuarios->num_rows > 0) {
	while ($dataRespuestaUsuarios = $respuestaConsultaUsuarios->fetch_assoc()) {
		$usuarios[] = $dataRespuestaUsuarios;
	}
}

$consultaBitacoraAcciones = "SELECT id, descripciones FROM bitacora_acciones;";
$respuestaBitacoraAcciones = $Link->query($consultaBitacoraAcciones) or die('Error al consultar el tipo de acción ' . mysqli_error($Link));
if ($respuestaBitacoraAcciones->num_rows > 0) {
	while ($dataRespuestaUsuarios = $respuestaBitacoraAcciones->fetch_assoc()) {
		$acciones[] = $dataRespuestaUsuarios;
	}
}

$consultaBitacora = "SELECT b.fecha, b.usuario, b.tipo_accion, b.observacion FROM bitacora AS b INNER JOIN bitacora_acciones as ba ON b.tipo_accion = ba.id";
if (isset($_POST["fechaInicial"]) && !empty($_POST["fechaInicial"])) { $consultaBitacora.=" WHERE b.fecha >= '".$_POST["fechaInicial"]." 00:00:00'"; }
if (isset($_POST["fechaFinal"]) && !empty($_POST["fechaFinal"])) { $consultaBitacora.=" AND b.fecha <= '".$_POST["fechaFinal"]." 23:59:59'"; }
if ($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1) {
	if (isset($_POST["usuario"]) && !empty($_POST["usuario"])) { $consultaBitacora.=" AND b.usuario = '".$_POST["usuario"]."'"; }
}
if ($_SESSION['perfil'] != 0 && $_SESSION['perfil'] != 1) {
	$consultaBitacora.=" AND b.usuario = '".$idUsuario."'";
}
if (isset($_POST["tipoAccion"]) && !empty($_POST["tipoAccion"])) { $consultaBitacora.=" AND b.tipo_accion = '".$_POST["tipoAccion"]."'"; }
$consultaBitacora .= " order by fecha desc limit 100;";

$respuestaBitacora = $Link->query($consultaBitacora) or die('Error al consultar la bitacora ' .mysqli_error($Link));
if ($respuestaBitacora->num_rows > 0) {
	while ($dataRespuestaBitacora = $respuestaBitacora->fetch_assoc()) {
		$respuestas[] = $dataRespuestaBitacora;
	}
}
// exit(var_dump($consultaBitacora));
?>

<style type="text/css">
	table.dataTable.dataTable_width_auto {
  	width: auto;
	}
</style>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-12">
		<h2><?= $titulo; ?></h2>
      	<ol class="breadcrumb">
          	<li>
              	<a href="<? $baseUrl; ?>">Inicio</a>
          	</li>
          	<li class="active">
              	<strong><?= $titulo; ?></strong>
          	</li>
      </ol>
	</div> <!-- col-lg-12 -->
</div><!--  row wrapper -->

<!-- seccion parametros de busqueda -->
<div class="wrapper wrapper-content  animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox">
				<div class="ibox-content">
					<div class="row">
						<div class="col-sm-12">
							<form action="#" id="formBitacora" name="formBitacora" method="post">
								<div class="row">
									<div class="col-sm-3 form-group">
										<label for="fechaInicial">Fecha Inicial</label>
										<input class="form-control" type="date" name="fechaInicial" id="fechaInicial" required max="<?php 
											$year = date('Y');
											$month = date('m'); 
											$day = date('d');	
                                            echo $year.'-'.$month.'-'.$day;?>">
									</div>
									<div class="col-sm-3 form-group">
										<label for="fechaFinal">Fecha Final</label>
										<input class="form-control" type="date" name="fechaFinal" id="fechaFinal" required max="<?php 
											$year = date('Y');
											$month = date('m'); 
											$day = date('d');	
                                            echo $year.'-'.$month.'-'.$day;?>">
									</div>

								<?php if ($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1) { ?>
									<div class="col-sm-3 form-group">
										<label for="usuario">Usuario</label>
										<select class="form-control select2" id="usuario" name="usuario" >
											<option value="">Seleccione Uno</option>
											<?php foreach ($usuarios as $key => $usuario) { ?>
					                            <option value="<?= $usuario['id']; ?>"
					                            	<?= (isset($_POST["usuario"]) && $_POST["usuario"] == $usuario['id'] ) ? "selected" : ""; ?>>
					                              	<?= $usuario['nombre']; ?>
					                            </option>
                      						<?php } ?>
										</select>
									</div>
								<?php	} ?>
									<div class="col-sm-3 form-group">
										<label for="tipoAccion">Tipo Acción</label>
										<select class="form-control select2" id="tipoAccion" name="tipoAccion" >
											<option value="">Seleccione Uno</option>
											<?php foreach ($acciones as $key => $accion) { ?>
												<option value="<?= $accion['id']; ?>"
													<?= (isset($_POST["tipoAccion"]) && $_POST["tipoAccion"] == $accion['id']) ? "selected" : ""; ?>>
													<?= $accion['descripciones']; ?>									
													</option>
											<?php }?>
										</select>
									</div>
								</div><!-- row -->
								<div class="row">
									<div class="col-sm form-group">
										<button class="btn btn-primary" type="submit" name="buscar" id="buscar" style="float: right; margin-right: 20px;"><i class="fa fa-search"></i> Buscar</button>
									</div>
								</div>
							</form> <!-- form -->
						</div> <!-- col-sm-12 -->
					</div><!-- row -->
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div> <!-- col-lg-12 -->

		<!-- inicio seccion tabla -->
		<div class="col-lg-12">
			<div class="ibox">
				<div class="ibox-content">
					<table class="table table-striped table-hover" id="tabla_bitacora">
						<thead>
							<tr>
								<th>Fecha/Hora</th>
								<th>Usuario</th>
								<th>Tipo Acción</th>
								<th>Observación</th>
							</tr>
						</thead>
						<tbody>
							<?php if (isset($respuestas)) { ?>
								<?php foreach ($respuestas as $key => $respuesta): ?>
									<tr>
										<td><?= $respuesta['fecha']; ?></td>
										<td><?php 
											$usuarioString = '';
											foreach ($usuarios as $key => $usuario) {
												if ($respuesta['usuario'] == $usuario['id']) {
													$usuarioString = $usuario['nombre'];
												}
											}
											echo $usuarioString;
											?>
										</td>
										<td><?php
										$accionString = '';
										foreach ($acciones as $key => $accion) {
												if ($respuesta['tipo_accion'] == $accion['id']) {
													$accionString = $accion['descripciones'];
												}
											}	
											echo $accionString; 
											?>
										</td>
										<td><?= $respuesta['observacion']?></td>
									</tr>
								<?php endforeach ?>
							<?php } ?>		
						</tbody>
						<tfoot>
							<tr>
								<th>Fecha/Hora</th>
								<th>Usuario</th>
								<th>Tipo Acción</th>
								<th>Observación</th>
							</tr>
						</tfoot>
					</table>
				</div> <!-- ibox-content -->
			</div> <!-- ibox -->
		</div><!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- wrapper -->

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/modules/bitacora_usuarios/js/bitacora_usuarios.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>