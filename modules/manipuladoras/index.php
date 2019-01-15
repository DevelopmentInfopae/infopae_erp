<?php
$titulo = 'Subir Archivo';
include 'header.php';
$periodoActual = $_SESSION['periodoActual'];
require_once 'db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2>Carga de Archivos</h2> 
		<ol class="breadcrumb">
			<li> <a href="<?php echo $baseUrl; ?>">Home</a> </li>
			<li class="active"> <strong>Carga de archivos</strong> </li> 
		</ol>
	</div>
	<div class="col-lg-2">

	</div>
</div>

<div class="wrapper wrapper-content">
	<div class="row animated fadeInRight">
		<div class="col-md-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h3>Carga de archivos</h3>
				</div>
				<div class="ibox-content">

					<?php 
					$idDispositivo = "";
			        $annoActual = $_SESSION['periodoActual'];
			        $idUsuario = $_SESSION['id_usuario'];
			        $consulta = "select dis.* , sed.nom_sede from dispositivos  dis join sedes".$annoActual." sed on sed.cod_sede = dis.cod_sede where id_usuario = $idUsuario";   

			        //echo $consulta;
			        $result = $Link->query($consulta) or die(mysqli_error($Link));
			        $Link->close();
			        if($result->num_rows <= 0){
			          echo "<h2>El usuario no tiene dispositivo asignado!</h2>";
			        }else {
			        ?>
						<form class="" action="" method="post" name="formArchivos" id="formArchivos" enctype="multipart/form-data">
							<div class="row">
								<div class="col-md-3 form-group">	
									<label for="dispositivo">Dispositivo</label>
									<select name="dispositivo" id="dispositivo" class="form-control">
										<option value="">Seleccione uno</option>
            							<?php while ($row = $result->fetch_assoc()) { ?> <option value="<?php echo $row["id"]; ?>"><?php echo $row["id"]." - ".$row["num_serial"]." - ".$row["nom_sede"]; ?></option> <?php } ?> </select>
								</div>

								<div class="col-sm-6 form-group">
									<label for="departamento">Archivo</label>
									<div class="fileinput fileinput-new input-group" data-provides="fileinput"> <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div> <span class="input-group-addon btn btn-default btn-file"><span class="fileinput-new">Elegir archivo</span><span class="fileinput-exists">Change</span><input type="file" name="archivo" id="archivo" accept=".kq"></span> <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remover</a> </div>
								</div><!-- /.col -->	
							</div>
							<div class="row">
								<div class="col-md-3 form-group">
									<input type="hidden" id="ruta" name="ruta" value="<?php echo $nodeUrl; ?>">
									<button type="button" name="btnEnviar" id="btnEnviar" class="btn btn-primary">Enviar</button>	
								</div>
							</div>		
						</form>
					<?php } ?>
					<div class="row"> <div class="col-md-12 form-group debug"></div> </div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include 'footer.php'; ?>

<!-- Mainly scripts -->
<script src="theme/js/jquery-3.1.1.min.js"></script>
<script src="theme/js/bootstrap.min.js"></script>
<script src="theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="theme/js/inspinia.js"></script>
<script src="theme/js/plugins/pace/pace.min.js"></script>







<!-- Jasny -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>

<!-- DROPZONE -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dropzone/dropzone.js"></script>

<!-- CodeMirror -->
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/codemirror/codemirror.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/codemirror/mode/xml/xml.js"></script>





<script src="js/subir_archivo.js"></script>

<!-- <script src="<?php echo $baseUrl; ?>/modules/instituciones/js/instituciones.js"></script> -->
<!-- Page-Level Scripts -->
</body>
</html>