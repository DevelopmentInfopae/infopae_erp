<?php
	include '../../header.php';
	$titulo = "Asignar bodegas";

  $con_cod_muni = "SELECT CodMunicipio FROM parametros;";
  $res_minicipio = $Link->query($con_cod_muni) or die(mysqli_error($Link));
  if ($res_minicipio->num_rows > 0) {
    $codigoDANE = $res_minicipio->fetch_array();
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
      	<a href="<?php echo $baseUrl . '/modules/bodegas/index.php'; ?>">Bodegas</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!-- <a class="btn btn-primary" id="guardarUsuarioBodega"><i class="fa fa-check"></i> Guardar </a> -->
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formAsignarUsuariosBodega">
          	<div class="row">
        			<div class="col-xs-12">
        				<div class="row">

	        				<div class="form-group col-sm-3">
		                <label for="usuario">Usuario</label>
		                <select class="form-control" name="usuario" id="usuario" required>
                      <option value="">Seleccione uno</option>
                      <?php
                        $consulta1 = "SELECT id AS idUsuario, nombre AS nombreUsuario FROM usuarios WHERE id_perfil = '2' AND (Tipo_Usuario = 'Proveedor' OR Tipo_Usuario = 'Empleado');";
                        $resultado1 = $Link->query($consulta1) or die (mysqli_error($Link));
                        if($resultado1){
                          while($registros1 = $resultado1->fetch_assoc()){
                      ?>
                            <option value="<?php echo $registros1['idUsuario']; ?>">
                              <?php echo $registros1['nombreUsuario']; ?>
                            </option>
                      <?php
                          }
                        }
                      ?>
                    </select>
		              </div>

		              <div class="form-group col-sm-3">
		                <label for="bodegaSalida">Bodega salida</label>
		                <select class="form-control" name="bodegaSalida" id="bodegaSalida" required>
                      <option value="">Seleccione uno</option>
                      <?php
                        $consulta2 = "SELECT ID AS codigoBodega, NOMBRE AS nombreBodega FROM bodegas WHERE ID NOT IN (SELECT cod_sede FROM sedes".$_SESSION['periodoActual'].");";
                        $resultado2 = $Link->query($consulta2) or die (mysqli_error($Link));
                        if($resultado2){
                          while($registros2 = $resultado2->fetch_assoc()){
                      ?>
                            <option value="<?php echo $registros2['codigoBodega']; ?>">
                              <?php echo $registros2['nombreBodega']; ?>
                            </option>
                      <?php
                          }
                        }
                      ?>
                    </select>
		              </div>

                  <div class="form-group col-sm-3">
                    <label for="municipio">Municipio</label>
                    <select class="form-control" name="municipio" id="municipio">
                      <option value="">Seleccionar todo</option>
                      <?php
                        $consulta = " SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE 1=1 ";

                        $DepartamentoOperador = $_SESSION['p_CodDepartamento'];
                        if($DepartamentoOperador != ''){
                          $consulta = $consulta." AND CodigoDANE LIKE '$DepartamentoOperador%' ";
                        }
                        $consulta = $consulta." ORDER BY ciudad ASC ";
                        $resultado = $Link->query($consulta);
                        if($resultado->num_rows > 0){
                          while($row = $resultado->fetch_assoc()) {
                            $selected = (isset($codigoDANE["CodMunicipio"]) && $codigoDANE["CodMunicipio"] == $row["codigoDANE"] ) ? " selected " : "";
                            echo '<option value="' . $row["codigoDANE"] . '" ' . $selected . '>
                                    ' . $row["ciudad"] .
                                  '</option>';
                          }
                        }
                      ?>
                    </select>
                  </div>

									<div class="form-group col-sm-3">
		                <label for="bodegaEntrada">Bodega Entrada</label>
		                <select class="form-control" name="bodegaEntrada" id="bodegaEntrada">
                      <option value="">Seleccionar todo</option>
                      <?php
                        $consulta = "SELECT ID AS codigoBodega, NOMBRE AS nombreBodega FROM bodegas WHERE CIUDAD = '".$codigoDANE["CodMunicipio"]."'";
                        $resultado = $Link->query($consulta);
                        if ($resultado->num_rows > 0)
                        {
                          while($registros = $resultado->fetch_assoc())
                          {
                      ?>
                      <option value="<?php echo $registros['codigoBodega']; ?>"><?php echo $registros['nombreBodega']; ?></option>
                      <?php
                          }
                        }
                      ?>
                    </select>
		              </div>

        				</div>

        			</div>
          	</div>
          	<div class="row">
          		<div class="col-lg-3 col-lg-3">
                <a class="btn btn-primary"  id="asignarUsuarioBodega"><i class="fa fa-plus "></i></a>
          			<a class="btn btn-primary"  id="eliminarUsuarioBodega"><i class="fa fa-minus "></i></a>
          		</div>
          	</div>
          </form>

          <hr>
          <br>

          <h3>Bodegas de usuario</h3>
          <form id="formEliminarUsuarioBodega">
            <table id="tablaUsuariosBodegas" class="table table-striped table-hover selectableRows">
              <thead>
                <tr>
                  <th class="text-center"><input type="checkbox" name="usuariosBodegas" id="usuariosBodegas"></th>
                  <th>Nombre</th>
                  <th>Bodega entrada</th>
                  <th>Bodega salida</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
              <tfoot>
                <tr>
                  <th></th>
                  <th>Nombre</th>
                  <th>Bodega entrada</th>
                  <th>Bodega salida</th>
                </tr>
              </tfoot>
            </table>
          </form>
        </div>
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

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/modules/bodegas/js/bodegas.js"></script>

<!-- Section Scripts -->
<script type="text/javascript">
  jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });

</script>
<?php mysqli_close($Link); ?>

</body>
</html>