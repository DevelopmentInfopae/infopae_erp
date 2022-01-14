<?php
include '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];

if ($permisos['instituciones'] == "0") {
   	 ?><script type="text/javascript">
      	window.open('<?= $baseUrl ?>', '_self');
    </script>
<?php exit(); }

require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");


$codSede = '';
if(isset($_GET['sede'])){
    $codSede = $_GET['sede'];
}
$periodoActual = $_SESSION['periodoActual'];
$consulta = " select * from sedes$periodoActual where cod_sede = $codSede ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
	$row = $resultado->fetch_assoc();
	$nomInst = $row['nom_inst'];
	$nomSede = $row['nom_sede'];
}
?>






<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
    <div class="col-lg-8">

        <h1><?php echo $nomInst; ?></h1>
        <h2>Sede: <?php echo $nomSede; ?></h2>
        <!-- <h4><?php echo $codSede; ?></h4> -->
        <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl; ?>">Home</a>
          </li>
		  <li><a href="institucion.php">Institución</a></li>
		  <li><a href="sede.php?codSede=<?php echo $codSede; ?>&nomSede=<?php echo $nomSede; ?>&nomInst=<?php echo $nomInst; ?>">Sede</a></li>
          <li class="active">

              <strong>Archivos sede</strong>
          </li>
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
                        <?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
                        	<a class="btn btn-primary btn-block" href="#subirArchivos">Adjuntar Archivos</a>
							<div class="hr-line-dashed"></div>
                        <?php endif ?>
                        <h5>Categoría</h5>
                        <ul class="folder-list" style="padding: 0">
                            <li><a href="#" class="category-control <?php if((isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 0) || !isset($_GET['categoriaFnd'])){ echo " active "; } ?>" value="0"><i class="fa fa-folder"></i> Todos</a></li>


							<li><a href="#" class="category-control <?php if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 1){ echo " active "; } ?>" value="1"><i class="fa fa-folder"></i> Manipuladoras</a></li>





							<li><a href="#" class="category-control <?php if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 2){ echo " active "; } ?>" value="2"><i class="fa fa-folder"></i> Capacitaciones</a></li>



							<li><a href="#" class="category-control <?php if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 3){ echo " active "; } ?>" value="3"><i class="fa fa-folder"></i> Actas CAE</a></li>
							<li><a href="#" class="category-control <?php if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 4){ echo " active "; } ?>" value="4"><i class="fa fa-folder"></i> Dotación</a></li>
							<li><a href="#" class="category-control <?php if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 5){ echo " active "; } ?>" value="5"><i class="fa fa-folder"></i> Inventario Comedor</a></li>
							<li><a href="#" class="category-control <?php if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 6){ echo " active "; } ?>" value="6"><i class="fa fa-folder"></i> Evidencias</a></li>
							<li><a href="#" class="category-control <?php if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] == 7){ echo " active "; } ?>" value="7"><i class="fa fa-folder"></i> Documentación</a></li>




                        </ul>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
                <div class="col-lg-9 animated fadeInRight">
                    <div class="row">
                        <div class="col-lg-12">


							<?php
	                        $periodoActual = $_SESSION['periodoActual'];
	                        $consulta = " select * from archivos where cod_sede = '$codSede' ";
							if(isset($_GET['tipo']) && $_GET['tipo'] == 2){
								$consulta .= " and extension != 'pdf' ";
							}
							else if(isset($_GET['tipo']) && $_GET['tipo'] == 3){
								$consulta .= " and extension = 'pdf' ";
							}
							else if(isset($_GET['categoriaFnd']) && $_GET['categoriaFnd'] != 0){
								$aux = $_GET['categoriaFnd'];
								$consulta .= " and categoria = $aux ";
							}
	                        $consulta .= " order by fecha_carga desc ";

	                        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	                        if($resultado->num_rows >= 1){
	                            while($row = $resultado->fetch_assoc()) { ?>
									<div class="file-box">
										<div class="file">
										<a href="<?php echo $baseUrl.'/'.$row['ruta']; ?>" target="_blank">
										    <span class="corner"></span>
											<?php if($row['extension'] == 'pdf'){ ?>
												<div class="icon">
													<i class="fa fa-file-pdf-o"></i>
												</div>
											<?php }else{ ?>
												<div class="image">
		                                            <img alt="image" class="img-responsive" src="<?php echo $baseUrl.'/'.$row['ruta']; ?>">
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
												<br>
												<?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
													<a href="" value="<?php echo $row['id']; ?>" class="btnBorrar"><small style="color: #ff7a7a;">Borrar</small></a>
												<?php endif ?>
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




<?php if ($_SESSION['perfil'] == "0" || $permisos['instituciones'] == "2"): ?>
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
								<input type="hidden" name="sede" id="sede" value="<?php echo $codSede; ?>">
							</div><!-- /.col -->
				            <div class="col-sm-3 form-group">
				                <label for="categoria">Categoría</label>
				                <select class="form-control" name="categoria" id="categoria">
									<option value="">Seleccione una</option>
				                    <option value="1">Manipuladoras</option>
				                    <option value="2">Capacitaciones</option>
				                    <option value="3">Actas CAE</option>
				                    <option value="4">Dotación</option>
				                    <option value="5">Inventario Comedor</option>
				                    <option value="6">Evidencias</option>
				                    <option value="7">Documentación</option>
				                </select>
				            </div><!-- /.col -->
							<div class="col-sm-6 form-group">
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
<?php endif ?>
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

    <script src="<?php echo $baseUrl; ?>/modules/instituciones/js/sede_archivos.js"></script>

    <!-- Page-Level Scripts -->

<?php mysqli_close($Link); ?>

<form action="sede_archivos.php" name="mostrarArchivos" id="mostrarArchivos">
    <input type="hidden" name="sede" id="sede" value="<?php if(isset($_GET['sede'])){echo $_GET['sede'];} ?>">
    <input type="hidden" name="tipo" id="tipo" value="<?php if(isset($_GET['tipo'])){echo $_GET['tipo'];} ?>">
    <input type="hidden" name="categoriaFnd" id="categoriaFnd" value="<?php if(isset($_GET['categoriaFnd'])){echo $_GET['categoriaFnd'];} ?>">
</form>

</body>
</html>
