<?php
include '../../header.php';

if ($permisos['archivos_globales'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php exit(); }

set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h1>Módulo de Archivos</h1>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Home</a>
			</li>
			<li><a href="index.php">Módulo de Archivos</a></li>
		</ol>
	</div>
  <div class="col-lg-4">
	  <div class="title-action">
		 <!--
		  <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
		  <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
		-->
		  <!-- <a href="<?php echo $baseUrl; ?>/modules/despachos/despacho_nuevo.php" target="_self" class="btn btn-primary"><i class="fa fa-truck"></i> Nuevo despacho </a> -->
	  </div>
  </div>
  	<div class="col-lg-12 debug">
  	</div>
</div>
<!-- /.row wrapper de la cabecera de la seccion -->













<div class="wrapper wrapper-content">
	<div class="row">
		<div class="col-lg-3">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<div class="file-manager">
						<h5>Tipo:</h5>
						<a href="#" class="file-control <?php if((isset($_GET['tipo']) && $_GET['tipo'] == 1) || !isset($_GET['tipo'])){ echo " active "; } ?>" value="1" >Todos</a>
						<a href="#" class="file-control <?php if(isset($_GET['tipo']) && $_GET['tipo'] == 2){ echo " active "; } ?>" value="2" >Imagenes</a>
						<a href="#" class="file-control <?php if(isset($_GET['tipo']) && $_GET['tipo'] == 3){ echo " active "; } ?>" value="3">PDF</a>
						<div class="hr-line-dashed"></div>
						
						
						<?php if( $_SESSION['perfil'] == "0" || $permisos['archivos_globales'] == "2"){ ?>
							<a class="btn btn-primary btn-block" href="#subirArchivos">Adjuntar Archivos</a>
							<div class="hr-line-dashed"></div>
						<?php } ?>




						
						<h5>Categoría</h5>
						<ul class="folder-list" style="padding: 0">
							<li><a href="#" class="category-control <?php if((isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 0) || !isset($_GET['categoriaFnd'])){ echo " active "; } ?>" value="0"><i class="fa fa-folder"></i> Todos</a></li>

							<?php
								$consulta2 = "select * from mod_archivos_categorias";
								$resultado2 = $Link->query($consulta2) or die ("No se puede realizar la consulta para mostrar el listado de las categorias. <br><br> $consulta2 <br><br> ". mysqli_error($Link));
								if($resultado2->num_rows >= 1){
									while($row2 = $resultado2->fetch_assoc()) { ?>
										<li><a href="#" class="category-control <?php if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == $row2['id'] ){ echo " active "; } ?>" value="<?= $row2['id'] ?>"><i class="fa fa-folder"></i> <?= $row2['categoria'] ?></a></li>
									<?php
									}
								}
							?>


							<?php if( $_SESSION['perfil'] == 0 || $permisos['archivos_globales'] == "2"){ ?>
								<li><a href="#editar-categorias"><i class="fa fa-pencil-square-o"></i> Editar Categorías</a></li>
							<?php } ?>




						</ul>
						<div class="row">
							<div class="col-sm-12 form-group">
								<label for="municipio">Municipio</label>
								<select class="form-control" name="municipioLateral" id="municipioLateral">
									<option value="">Todos</option>
								</select>
							</div>
							<div class="col-sm-12 form-group">
								<label for="institucion">Institución</label>
								<select class="form-control" name="institucionLateral" id="institucionLateral">
									<option value="">Todas</option>
								</select>
							</div>

							<div class="col-sm-12 form-group">
								<label for="sede">Sede</label>
								<select class="form-control" name="sedeLateral" id="sedeLateral">
									<option value="">Todas</option>
								</select>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>

				<!-- AREA DONDE SE MUESTRAN LOS ARCHIVOS -->
				<div class="col-lg-9 animated fadeInRight">
					<div class="row">
						<div class="col-lg-12">


							<?php
							$periodoActual = $_SESSION['periodoActual'];
							$consulta = " select * from mod_archivos where 1 = 1";
							if(isset($_GET['tipo']) && $_GET['tipo'] == 2){
								$consulta .= " and extension != 'pdf' ";
							}
							else if(isset($_GET['tipo']) && $_GET['tipo'] == 3){
								$consulta .= " and extension = 'pdf' ";
							}
							
							if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] != 0){
								$aux = $_GET['categoriaFnd'];
								$consulta .= " and categoria = $aux ";
							}

							if(isset($_GET['municipioFnd']) && $_GET['municipioFnd'] != ""){
								$aux = $_GET['municipioFnd'];
								$consulta .= " and cod_municipio = $aux ";
							}

							if(isset($_GET['institucionFnd']) && $_GET['institucionFnd'] != ""){
								$aux = $_GET['institucionFnd'];
								$consulta .= " and cod_inst = $aux ";
							}

							if(isset($_GET['sedeFnd']) && $_GET['sedeFnd'] != ""){
								$aux = $_GET['sedeFnd'];
								$consulta .= " and cod_sede = $aux ";
							}


							$consulta .= " order by fecha_carga desc ";

							$resultado = $Link->query($consulta) or die ("No se puede realizar la consulta para mostrar archivos. <br><br> $consulta <br><br> ". mysqli_error($Link));


						

							// var_dump($infopaeData);
							// foto_loader.php?file=gumball.pdf





							if($resultado->num_rows >= 1){
								while($row = $resultado->fetch_assoc()) { ?>
									<div class="file-box">
										<div class="file">
										<a href="file_loader.php?file=<?= $row['ruta']; ?>" target="_blank">
											<span class="corner"></span>
											<?php if($row['extension'] == 'pdf'){ ?>
												<div class="icon">
													<i class="fa fa-file-pdf-o"></i>
												</div>
											<?php }else{ ?>
												<div class="image">
													<img alt="image" class="img-responsive" src="file_loader.php?file=<?= $row['ruta']; ?>">
												</div>
											<?php } ?>


											<div class="file-name">
												<?php echo $row['nombre']; ?>.<?php echo $row['extension']; ?>
												<br/>
												<?php
													$aux = $row['fecha_carga'];
													$aux1 = date("h:i:s A d/m/Y", strtotime($aux));
												?>
												<small>Agregado: <?php echo $aux1 ?></small>
												<?php if( $_SESSION['perfil'] == 0 || $permisos['archivos_globales'] == "2"){ ?>
													<br><a href="" value="<?php echo $row['id']; ?>" class="btnBorrar"><small style="color: #ff7a7a;">Borrar</small></a>
												<?php } ?>
											</div>
										</a>
										</div>
									</div>
								<?php
								}// Termina el while
							}//Termina el if que valida que si existan resultados
							?>




					</div><!--/.col -->
					</div>
				</div>
				</div>






















<?php if( $_SESSION['perfil'] == 0 || $permisos['archivos_globales'] == "2"){ ?>
	<div class="wrapper wrapper-content">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Adjuntar Archivo</h5>
						<div class="ibox-tools">
							<a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
						</div>
					</div>
					<div class="ibox-content">
						<form class="" action="" method="post" name="formArchivos" id="formArchivos" enctype="multipart/form-data">
							<!-- <h2>Subir Archivo</h2> -->
							<!-- <input type="file" name="foto[]" id="foto" accept="image/jpeg" multiple > -->
							<div class="row" name="subirArchivos">

								<div class="col-sm-3 form-group">
									<label for="nombre">Nombre del documento</label>
									<input type="text" name="nombre" value="" id="nombre" class="form-control">
								</div>


								<div class="col-sm-3 form-group">
									<label for="municipio">Municipio</label>
									<select class="form-control" name="municipio" id="municipio">
										<option value="">Todos</option>
									</select>
								</div>

								<div class="col-sm-3 form-group">
									<label for="institucion">Institución</label>
									<select class="form-control" name="institucion" id="institucion">
										<option value="">Todas</option>
									</select>
								</div>

								<div class="col-sm-3 form-group">
									<label for="sede">Sede</label>
									<select class="form-control" name="sede" id="sede">
										<option value="">Todas</option>
									</select>
								</div>







								<div class="col-sm-3 form-group">
									<label for="categoria">Categoría</label>
									<select class="form-control" name="categoria" id="categoria">
										<option value="">Seleccione una</option>
										<?php
											$consulta2 = "select * from mod_archivos_categorias";
											$resultado2 = $Link->query($consulta2) or die ("No se puede realizar la consulta para mostrar el listado de las categorias. <br><br> $consulta2 <br><br> ". mysqli_error($Link));
											if($resultado2->num_rows >= 1){
												while($row2 = $resultado2->fetch_assoc()) { ?>
													<option value="<?= $row2['id'] ?>"><?= $row2['categoria'] ?></option>
												<?php
												}
											}
										?>
									</select>
								</div>




								<div class="col-sm-9 form-group">
									<label for="departamento">Archivo</label>
									<div class="fileinput fileinput-new input-group" data-provides="fileinput"> <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Elegir archivo</span><span class="fileinput-exists">Change</span><input type="file" name="foto[]" id="foto" accept="image/jpeg,image/gif,image/png,application/pdf"></span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a> </div>
								</div><!-- /.col -->
							</div>
							<div class="row">
								<div class="col-sm-3 form-group">
									<button type="button" name="btnSubirArchivo" id="btnSubirArchivo" class="btn btn-primary">Adjuntar archivo</button>
								</div><!-- /.col -->
							</div><!-- /.row -->
							<div class="row">
								<div class="col-lg-12">
									<div class="debugCarga">

									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>







	<div class="wrapper wrapper-content" id="editar-categorias">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins border-bottom">
					<div class="ibox-title">
						<h5>Categorías</h5>
						<div class="ibox-tools">
							<a class="collapse-link"> <i class="fa fa-chevron-up"></i> </a>
						</div>
					</div>
					<div class="ibox-content">



					<table class="table table-striped">
	<thead>
		<tr>
		<th scope="col">Categorías</th>
		<th scope="col">Acciones</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<input type="text" name="nuevaCategoria" value="" id="nuevaCategoria" placeholder="Digite el nombre para la nueva categoría" class="form-control">
			</td>
			<td>
				<button type="button" class="btn btn-primary nueva-categoria"><i class="fa fa-fw fa-check"></i></button>
			</td>
		</tr>
		
		<?php
			$consulta2 = "select * from mod_archivos_categorias where id > 1";
			$resultado2 = $Link->query($consulta2) or die ("No se puede realizar la consulta para mostrar el listado de las categorias. <br><br> $consulta2 <br><br> ". mysqli_error($Link));
			if($resultado2->num_rows >= 1){
				while($row2 = $resultado2->fetch_assoc()) { ?>
					<tr>
						<td>
							<input type="text" name="categoria-editar-<?= $row2['id'] ?>" value="<?= $row2['categoria'] ?>" id="categoria-editar-<?= $row2['id'] ?>" placeholder="Nueva Categoría" class="form-control">
						</td>
						<td>
							<button type="button" class="btn btn-primary categoria-editar" value="<?= $row2['id'] ?>"><i class="fa fa-fw fa-check"></i></button>
							<button type="button" class="btn btn-danger categoria-eliminar" value="<?= $row2['id'] ?>"><i class="fa fa-fw fa-trash"></i></button>
						</td>
					</tr>
				<?php
				}
			}
		?>








		
		






	</tbody>
	</table>







					</div>
				</div>
			</div>
		</div>
	</div>

	<div style="height:40px;"></div>

<?php } ?>














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

	<!-- Jasny -->
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>

	<!-- DROPZONE -->
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dropzone/dropzone.js"></script>

	<!-- CodeMirror -->
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/codemirror/codemirror.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/codemirror/mode/xml/xml.js"></script>

	<script src="<?php echo $baseUrl; ?>/modules/archivos/js/index_mod_archivos.js"></script>
	<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

	<!-- Page-Level Scripts -->
	<script type="text/javascript">
		$(document).ready(function() {
			$('select').select2({width: "100%"});
		});
	</script>





<?php mysqli_close($Link); ?>

<form action="index.php" name="mostrarArchivos" id="mostrarArchivos">
	<input type="hidden" name="municipioFnd" id="municipioFnd" value="<?php if(isset($_GET['municipioFnd'])){echo $_GET['municipioFnd'];} ?>">
	<input type="hidden" name="institucionFnd" id="institucionFnd" value="<?php if(isset($_GET['institucionFnd'])){echo $_GET['institucionFnd'];} ?>">
	<input type="hidden" name="sedeFnd" id="sedeFnd" value="<?php if(isset($_GET['sedeFnd'])){echo $_GET['sedeFnd'];} ?>">
	<input type="hidden" name="tipo" id="tipo" value="<?php if(isset($_GET['tipo'])){echo $_GET['tipo'];} ?>">
	<input type="hidden" name="categoriaFnd" id="categoriaFnd" value="<?php if(isset($_GET['categoriaFnd'])){echo $_GET['categoriaFnd'];} ?>">
</form>

</body>
</html>
