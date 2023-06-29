<?php 
	include '../../header.php';

	if ($permisos['configuracion'] == "0" || $permisos['configuracion'] == "1") {
    	?><script type="text/javascript">
      		window.open('<?= $baseUrl ?>', '_self');
    	</script>
  	<?php exit(); }
	  	  else {
			?><script type="text/javascript">
			  const list = document.querySelector(".li_configuracion");
			  list.className += " active ";
			  const list2 = document.querySelector(".li_usuarios");
			  list2.className += " active ";
			</script>
			<?php
			}

	$titulo = "Editar Usuario";

	$idUsuario = $_POST["codigoUsuario"];
	$consulta = "SELECT id, nombre, direccion, cod_mun, telefono, IFNULL(foto, '') AS foto, email, id_perfil, num_doc, Tipo_Usuario, estado, clave,
								IFNULL(
	                (SELECT DISTINCT pro.Nitcc FROM proveedores pro WHERE usu.num_doc = pro.Nitcc ), 
	                IFNULL(
	                  (SELECT DISTINCT ubod.USUARIO FROM usuarios_bodegas ubod WHERE usu.id = ubod.USUARIO ),
	                  IFNULL(
	                    (SELECT DISTINCT emp.Nitcc FROM empleados emp WHERE usu.num_doc = emp.Nitcc ),
	                          (SELECT DISTINCT dis.id_usuario FROM dispositivos dis WHERE usu.num_doc = dis.id_usuario )
	                  )
	                )
	              ) AS 'usuarioAsociado'
							FROM usuarios usu
							WHERE id = '$idUsuario';";
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
	if($resultado){
		$row = $resultado->fetch_assoc();
	}
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
      	<a href="<?php echo $baseUrl . '/modules/usuarios'; ?>">Usuario</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" onclick="actualizarUsuario();"><i class="fa fa-check"></i> Guardar </a>
      <div class="btn-group">
				<div class="dropdown pull-right">
	        <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">
	          Acciones 
	          <span class="caret"></span>
	        </button>
	        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
	          <li><a onclick="restaurarContrasena(<?php echo $idUsuario; ?>);"><i class="fa fa-retweet"></i> Restaurar Contraseña</a></li>
			    	<li><a onclick="confirmarEliminarUsuario(<?php echo $idUsuario; ?>);"><i class="fa fa-trash"></i> Eliminar </a></li>
	        </ul>
	       </div>
			</div>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formActualizarUsuario" action="functions/fn_usuarios_actualizar.php" method="post">
          	<div class="row">
          		<div class="col-sm-3 col-lg-2 text-center">
        				<div class="form-group">
									<div class="fileinput fileinput-new" data-provides="fileinput">
									  <div class="fileinput-preview thumbnail img-circle" data-trigger="fileinput" style="width: 150px; height: 150px; padding: 0px;">
									  	<img class="img-responsive" <?php if ($row['foto'] != "") { ?> src="<?php echo $row['foto']; ?>" <?php } ?> alt="">
									  </div>
									  <div class="text-center">
									    <span class="btn btn-default btn-file">
								    		<span class="fileinput-new">seleccionar</span>
								    		<span class="fileinput-exists">Cambiar</span>
									    	<input type="file" name="foto" id="foto" accept="image/jpg, image/jpeg, image/png">
									    </span>
									    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
									  </div>
									</div>
									<input type="hidden" nombre="fotoCargada" id="fotoCargada" value=" <?php echo $row['foto']; ?> ">
								</div>
        			</div>
        			<div class="col-sm-9 col-lg-10">
        				<div class="row">
	        				<div class="form-group col-sm-6 col-md-4">
		                <label for="numeroDocumento">Número documento</label>
		                <input type="text" class="form-control" name="numeroDocumento" id="numeroDocumento" value="<?php echo $row['num_doc']; ?>" required>
		                <input type="hidden" name="id" id="id" value="<?php echo $idUsuario; ?>">
		              </div>

		              <div class="form-group col-sm-6 col-md-4">
		                <label for="nombre">Nombres y Apellidos</label>
		                <input type="text" class="form-control" name="nombre" id="nombre" value="<?php echo $row['nombre'] ?>" required>
		              </div>

									<div class="form-group col-sm-6 col-md-4">
		                <label for="email">Email</label>
		                <input type="email" class="form-control" name="email" id="email" value="<?php echo $row['email'] ?>" required>
		              </div>
        				</div>
								<div class="row">
									<div class="form-group col-sm-6 col-md-4">
		                <label for="telefono">Teléfono</label>
		                <input type="text" class="form-control" name="telefono" id="telefono" value="<?php echo $row['telefono'] ?>">
		              </div>
		              <div class="form-group col-sm-6 col-md-4">
		                <label for="direccion">Dirección</label>
		                <input type="text" class="form-control" name="direccion" id="direccion" value="<?php echo $row['direccion'] ?>">
		              </div>
		              
		              <div class="form-group col-sm-6 col-md-4">
		                <label for="municipio">Municipio </label>
		                <select class="form-control" name="municipio" id="municipio" required>
		                	<option value="">Seleccione uno</option>
		                	<?php
		                    $codigoCiudad = $_SESSION['ciudad'];
		                    $consulta1= " SELECT DISTINCT CodigoDANE, Ciudad FROM ubicacion where CodigoDANE LIKE '$codigoCiudad%' order by ciudad asc; ";
		                    $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
		                    if($result1){
		                      while($row1 = $result1->fetch_assoc()){
		                  ?>
		                        <option value="<?php echo $row1['CodigoDANE']; ?>" <?php if(isset($row['cod_mun']) && $row['cod_mun'] == $row1['CodigoDANE']){ echo ' selected '; } ?>>
		                          <?php echo $row1['Ciudad']; ?>
		                        </option>
		                  <?php
		                      }
		                    }
		                  ?>
		                </select>
		              </div>
								</div>
								<div class="row">
									
	              <div class="form-group col-sm-6 col-md-4">
	                <label for="perfil">Perfil</label>
	                <select class="form-control" name="perfil" id="perfil" required>
	                	<option value="">Seleccione uno</option>
	                	<?php
	                    $consulta2= " SELECT id, nombre FROM perfiles order by id asc; ";
	                    $result2 = $Link->query($consulta2) or die ('Unable to execute query. '. mysqli_error($Link));
	                    if($result2){
	                      while($row2 = $result2->fetch_assoc()){
	                  ?>
	                        <option value="<?php echo $row2['id']; ?>" <?php if(isset($row['id_perfil']) && $row['id_perfil'] == $row2['id']){ echo ' selected '; } ?>>
	                          <?php echo $row2['nombre']; ?>
	                        </option>
	                  <?php
	                      }
	                    }
	                  ?>
	                </select>
	              </div>

	              <div class="form-group col-sm-6 col-md-4">
	                <label for="tipoUsuario">Tipo de usuario</label>
	                <input type="text" class="form-control" name="tipoUsuario" id="tipoUsuario" value="<?php echo $row['Tipo_Usuario'] ?>" required>
	              </div>
	              
	              <div class="form-group col-sm-6 col-md-4">
	                <label for="tipoUsuario">Estado</label>
	                <select class="form-control" name="estado" id="estado">
	                	<option value="1" <?php echo ($row["estado"] == "1") ? "selected" : ""; ?> >Activo</option>
	                	<option value="0" <?php echo ($row["estado"] == "0") ? "selected" : ""; ?> >Inactivo</option>
	                </select>
	              </div>
								</div>
        			</div>
          	</div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Ventana modal para confirmar -->
<div class="modal inmodal fade" id="ventanaConfirmar" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p class="text-center"></p>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="idAEliminar">
        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" onclick="eliminarUsuario()">Si</button>
      </div>
    </div>
  </div>
</div> 

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/usuarios/js/usuarios.js"></script>
<script type="text/javascript">
	jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
	
	$('.fileinput').fileinput();
</script>
<?php mysqli_close($Link); ?>

</body>
</html>