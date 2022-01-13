<?php
	include '../../config.php';
	require_once '../../db/conexion.php';

  	$periodo_actual = $_SESSION["periodoActual"];
    $municipio_defecto = $_SESSION["p_Municipio"];
    $departamento_operador = $_SESSION['p_CodDepartamento'];

    $c_municipio = "SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE ETC <> '1' ";
    if($departamento_operador != '') { $c_municipio .= " AND CodigoDANE LIKE '$departamento_operador%' "; }
    if ($municipio_defecto) { $c_municipio .= " AND CodigoDANE LIKE '$municipio_defecto%' "; }
    $c_municipio .= " ORDER BY ciudad ASC";
    $r_municipio = $Link->query($c_municipio) or die("Error al consultar los munnicipios. ". $Link->error);
    if($r_municipio->num_rows > 0){
      	while($row = $r_municipio->fetch_object()) {
      		$municipios[] = $row;
      	}
    }

    if (isset($municipio_defecto)) {
        $codigo_municipio = $municipio_defecto;
        $c_institucion = "SELECT DISTINCT cod_inst AS codigo, nom_inst AS nombre FROM sedes".$periodo_actual." WHERE cod_mun_sede = '".$codigo_municipio."' ORDER BY nom_inst;";
        $r_institucion = $Link->query($c_institucion) or die("Error al consultar instituciones ". $Link->error);

        if ($r_institucion->num_rows > 0) {
            while($instituto = $r_institucion->fetch_object()) {
                $instituciones[] = $instituto;
            }
        }
    }
?>
<style type="text/css">
	.select2-container--open {
      z-index: 9999999
  }
</style>

<div class="modal fade in" id="modal_crear_cronograma" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formulario_crear_cronograma">
		    <div class="modal-content">
		      	<div class="modal-header">
		        	<h4 class="modal-title">Creación Cronograma</h4>
		      	</div>
		      	<div class="modal-body">
		        	<div class="row">
		        		<div class="col-sm-4">
		        			<div class="form-group">
	        					<label for="municipio_modal">Municipio * </label>
	        					<select class="form-control select2" name="municipio" id="municipio_modal" style="width: 100%;" required="required">
	          						<option value="">Seleccione uno</option>
	          						<?php foreach ($municipios as $key => $municipio) { ?>
			                            <option value="<?= $municipio->codigoDANE; ?>"
			                            	<?= (isset($_POST["municipio"]) && $_POST["municipio"] == $municipio->codigoDANE || $municipio_defecto == $municipio->codigoDANE) ? "selected" : ""; ?>>
			                              	<?= $municipio->ciudad; ?>
			                            </option>
	          						<?php } ?>
	        					</select>
		        			</div>
      					</div>

      					<div class="col-sm-4">
      						<div class="form-group">
        						<label for="institucion_modal">Institución *</label>
      							<select class="form-control select2" name="institucion" id="institucion_modal" required="required" style="width: 100%;">
        								<option value="">Todas</option>
        								<?php if (isset($instituciones)) { ?>
                            <?php foreach ($instituciones as $key => $institucion) { ?>
                                <option value="<?= $institucion->codigo; ?>"
                                    <?= (isset($_POST["institucion"]) && $_POST["institucion"] == $institucion->codigo) ? "selected" : ""; ?>>
                                    <?= $institucion->nombre; ?>
                                </option>
                            <?php } ?>
                        <?php } ?>
      							</select>
      						</div>
  						  </div>

              			<div class="col-sm-4">
              				<div class="form-group">
        						<label for="sede_modal">Sede *</label>
    							<select class="form-control select2" name="sede" id="sede_modal" required="required" style="width: 100%;">
      								<option value="">Todas</option>
    							</select>
      						</div>
              			</div>
		        	</div>

		        	<div class="row">
		        		<div class="col-sm-4">
		        			<div class="form-group">
		        				<label for="value">Fecha desde</label>
		        				<input class="form-control" type="date" name="fecha_desde" id="fecha_desde">
		        			</div>
		        		</div>
		        		<div class="col-sm-4">
		        			<div class="form-group">
		        				<label for="value">Fecha hasta</label>
		        				<input class="form-control" type="date" name="fecha_hasta" id="fecha_hasta">
		        			</div>
		        		</div>
		        		<div class="col-sm-4">
		        			<div class="form-group">
		        				<label for="value">Mes *</label>
		        				<input class="form-control" type="number" name="mes" id="mes" min="1" max="12" required="required">
		        			</div>
		        		</div>
		        	</div>

		        	<div class="row">
		        		<div class="col-sm-4">
		        			<div class="form-group">
		        				<label for="value">Semana</label>
		        				<input class="form-control" type="number" name="semana" id="semana" min="1">
		        			</div>
		        		</div>
              			<div class="col-sm-4">
              				<div class="form-group">
        						<label for="horario">Horario</label>
    							<input class="form-control" type="text" name="horario" id="horario">
      						</div>
              			</div>
		        	</div>
		      	</div>


		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="guardar_cronograma"><i class="fa fa-check"></i> Guardar</button>
		      	</div>
			</div>
		</form>
	</div>
</div>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<script>
	$(document).ready(function() {
		$('.select2').select2();

		$(document).on('change', '#municipio_modal', function() { cargar_instituciones_modal(); });
    $(document).on('change', '#institucion_modal', function() { cargar_sedes_modal(); });

		$('#modal_crear_cronograma').modal('show');
	});


    function cargar_instituciones_modal()
    {
        var municipio = $('#municipio_modal').val();
        $('#institucion_modal').select2('val', '');

        $.ajax({
            url: 'functions/fn_obtener_institutos.php',
            type: 'POST',
            dataType: 'HTML',
            data: {
                'municipio': municipio
            },
        })
        .done(function(data) {
            $('#institucion_modal').html(data);
        })
        .fail(function(data) {
            console.log(data.responseText);
        });
    }

    function cargar_sedes_modal()
    {
        var institucion = $('#institucion_modal').val();
        $('#sede_modal').select2('val', '');

        $.ajax({
            url: 'functions/fn_obtener_sedes.php',
            type: 'POST',
            dataType: 'HTML',
            data: {
                'institucion': institucion
            },
        })
        .done(function(data) {
            $('#sede_modal').html(data);
        })
        .fail(function(data) {
            console.log(data.responseText);
        });
    }
</script>