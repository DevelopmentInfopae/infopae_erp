<?php
  include '../../header.php';
  $titulo = 'Nómina Riesgos';
?>

<!-- header -->
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
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
  <div class="col-lg-4">
    <div class="title-action">
      <a href="#" class="btn btn-primary" id="guardarNominaRiesgos"><i class="fa fa-check"></i> Guardar </a>
    </div>
  </div>
</div>

<!-- formulario para la creacion de una nueva nomina riesgos -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formCrearNominaRiesgos">
            <div class="row">
              <div class="col-sm-12">
				<div class="form-group col-sm-6 col-md-4">
					<label for="tipo">Tipo</label>
					<input class="form-control" type="text" name="tipo" id="tipo" required>
				</div> 
				<div class="form-group col-sm-6 col-md-4">
					<label for="tipo">Porcentaje</label>
					<input class="form-control" type="number" name="porcentaje" id="porcentaje" required>
				</div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- tabla en la que se va a mostrar la informacion de la nomina riesgos  -->
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<table id="box-table" class="table table-striped table-hover selectableRows">
						<thead>
        					<tr>
	        					<th>Tipo</th>
				                <th>Porcentaje</th>
				                <th class="text-center">Acciones</th>
			              	</tr>
			            </thead>
			            <tbody>
			            	<?php 
			            	$consulta = "SELECT ID, Tipo, Porcentaje FROM nomina_riesgos ORDER BY Tipo";
			            	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
			            	if ($resultado->num_rows > 0) {
			            			while($registros = $resultado->fetch_assoc()){
			            	 ?>
			            	<tr>
			              		<td align="left"><?php echo $registros['Tipo'];?></td>
			              		<td align="left"><?php echo $registros['Porcentaje']. " %"; ?></td>
			              		<td align="center">
				                  	<div class="btn-group">
				                    	<div class="dropdown">
				                      		<button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				                        		Acciones <span class="caret"></span>
				                      		</button>
				                      		<ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
				                        		<li><a href="#" class="editarNominaRiesgos" data-idnominariesgos="<?php echo $registros['ID']; ?>" data-tiponominariesgos="<?php echo $registros['Tipo']; ?>" data-porcentajenominariesgos="<?php echo $registros['Porcentaje']; ?>"><i class="fas fa-pencil-alt "></i>  Editar</a></li>
                                    			<li><a href="#" class="confirmarEliminarNominaRiesgos" data-idnominariesgos="<?php echo $registros['ID']; ?>"><i class="fa fa-trash fa-lg"></i> Eliminar</a></li>
				                      		</ul>
				                    	</div>
				                  	</div>
				                </td>
			              	</tr>
			              	<?php 
			              		}
			              	}
			              	?> 
			            </tbody>
			            <tfoot>
			            	<tr>
	        					<th>Tipo</th>
				                <th>Porcentaje</th>
				                <th class="text-center">Acciones</th>
			              	</tr>
			            </tfoot>

					</table> <!-- table -->
				</div> <!-- contentBackground -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- fadeInRight -->


<!-- Ventana de formulario para actualizar el valor de nomina riesgos -->
<div class="modal inmodal fade" id="ventanaFormulario" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header text-info" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Formulario InfoPAE </h3>
      </div>
      <div class="modal-body">
          <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="actualizarNominaRiesgos"><i class="fa fa-check"></i> Si</button>
      </div>
    </div>
  </div>
</div>

<form action="nomina_riesgos_actualizar.php" method="post" name="formEditarNominaRiesgos" id="formEditarNominaRiesgos">
  <input type="hidden" name="idNominaRiesgos" id="idNominaRiesgos">
</form>

<!-- Ventana de confirmación para eliminar nomina riesgos -->
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
        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal" id="eliminarNominaRiesgos"><i class="fa fa-check"></i> Si</button>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script type="text/javascript">
  jQuery.extend(jQuery.validator.messages, { required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/nomina_riesgos/js/nomina_riesgos.js"></script>
<?php mysqli_close($Link); ?>

</body>
</html>