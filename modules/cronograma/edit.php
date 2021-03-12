<?php
	include '../../config.php';
	require_once '../../db/conexion.php';

    $cronograma_id = $_GET["cronograma_id"];
  	$periodo_actual = $_SESSION["periodoActual"];
    $municipio_defecto = $_SESSION["p_Municipio"];
    $departamento_operador = $_SESSION['p_CodDepartamento'];

    $c_cronograma = "SELECT c.id, c.mes, c.semana, s.cod_mun_sede AS codigo_municipio, s.cod_inst AS codigo_institucion, c.cod_sede AS codigo_sede, s.nom_sede AS nombre_sede, c.fecha_desde, c.fecha_hasta, c.horario FROM cronograma c INNER JOIN sedes".$periodo_actual." s ON s.cod_sede = c.cod_sede WHERE c.id='".$cronograma_id."';";
    $r_cronograma = $Link->query($c_cronograma) or die("Error al consultar el cronograma por id: ". $Link->error);
    if ($r_cronograma->num_rows > 0) {
    	$cronograma = $r_cronograma->fetch_object();
    }

    $c_municipio = "SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE ETC <> '1' ";
    if($departamento_operador != ''){
      $c_municipio .= " AND CodigoDANE LIKE '$departamento_operador%' ";
    }
    $c_municipio .= " ORDER BY ciudad ASC";
    $r_municipio = $Link->query($c_municipio) or die("Error al consultar los municipios. ". $Link->error);
    if($r_municipio->num_rows > 0){
      	while($row = $r_municipio->fetch_object()) {
      		$municipios[] = $row;
      	}
    }

    if (isset($cronograma->codigo_municipio) && !empty($cronograma->codigo_municipio)) {
        $c_institucion = "SELECT DISTINCT cod_inst AS codigo, nom_inst AS nombre FROM sedes".$periodo_actual." WHERE cod_mun_sede = '".$cronograma->codigo_municipio."' ORDER BY nom_inst;";
        $r_institucion = $Link->query($c_institucion) or die("Error al consultar instituciones ". $Link->error);

        if ($r_institucion->num_rows > 0) {
            while($instituto = $r_institucion->fetch_object()) {
                $instituciones[] = $instituto;
            }
        }
    }

    if (isset($cronograma->codigo_institucion) && !empty($cronograma->codigo_institucion)) {
        $c_sedes = "SELECT DISTINCT cod_sede AS codigo, nom_sede AS nombre FROM sedes".$periodo_actual." WHERE cod_inst = '".$cronograma->codigo_institucion."';";
        $r_sedes = $Link->query($c_sedes) or die("Error al consultar municipios ". $link->error);

        if ($r_sedes->num_rows > 0) {
            while($sede = $r_sedes->fetch_object()) {
                $sedes[] = $sede;
            }
        }
    }
?>
<style type="text/css">
	.select2-container--open {
	    z-index: 9999999
	}
</style>

<div class="modal fade in" id="modal_editar_cronograma" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form action="" method="post" id="formulario_editar_cronograma">
			<input type="hidden" name="cronograma_id" id="cronograma_id" value="<?= $cronograma_id; ?>">
		    <div class="modal-content">
		      	<div class="modal-header">
		        	<h4 class="modal-title">Creación Cronograma</h4>
		      	</div>
		      	<div class="modal-body">
		        	<div class="row">
		        		<div class="col-sm-4">
		        			<div class="form-group">
	        					<label for="municipio_modal_editar">Municipio * </label>
	        					<select class="form-control select2" name="municipio" id="municipio_modal_editar" style="width: 100%;" disabled="disabled">
	          						<option value="">Seleccione uno</option>
	          						<?php foreach ($municipios as $key => $municipio) { ?>
			                            <option value="<?= $municipio->codigoDANE; ?>" <?= (isset($cronograma->codigo_municipio) && $cronograma->codigo_municipio == $municipio->codigoDANE || $municipio_defecto == $municipio->codigoDANE) ? "selected" : ""; ?>>
			                              	<?= $municipio->ciudad; ?>
			                            </option>
	          						<?php } ?>
	        					</select>
		        			</div>
      					</div>

      					<div class="col-sm-4">
      						<div class="form-group">
        						<label for="institucion_modal_editar">Institución *</label>
    							<select class="form-control select2" name="institucion" id="institucion_modal_editar" disabled="disabled" style="width: 100%;">
      								<option value="">Seleccione</option>
      								<?php if (isset($instituciones)) { ?>
                                        <?php foreach ($instituciones as $key => $institucion) { ?>
                                            <option value="<?= $institucion->codigo; ?>" <?= (isset($cronograma->codigo_institucion) && $cronograma->codigo_institucion == $institucion->codigo) ? "selected" : ""; ?>>
                                                <?= $institucion->nombre; ?>
                                            </option>
                                        <?php } ?>
                                    <?php } ?>
    							</select>
      						</div>
  						</div>

              			<div class="col-sm-4">
              				<div class="form-group">
        						<label for="sede_modal_editar">Sede *</label>
    							<select class="form-control select2" name="sede" id="sede_modal_editar" disabled="disabled" style="width: 100%;">
      								<option value="">Todas</option>
      								<?php if (isset($sedes)) { ?>
                                        <?php foreach ($sedes as $key => $sede) { ?>
                                            <option value="<?= $sede->codigo; ?>"
                                                <?= (isset($cronograma->codigo_sede) && $cronograma->codigo_sede == $sede->codigo) ? "selected" : ""; ?>>
                                                <?= $sede->nombre; ?>
                                            </option>
                                        <?php } ?>
                                    <?php } ?>
    							</select>
      						</div>
              			</div>
		        	</div>

		        	<div class="row">
		        		<div class="col-sm-4">
		        			<div class="form-group">
		        				<label for="value">Fecha desde</label>
		        				<input class="form-control" type="date" name="fecha_desde" id="fecha_desde_modal_editar" value="<?= $cronograma->fecha_desde; ?>" max="<?= $cronograma->fecha_hasta; ?>">
		        			</div>
		        		</div>
		        		<div class="col-sm-4">
		        			<div class="form-group">
		        				<label for="value">Fecha hasta</label>
		        				<input class="form-control" type="date" name="fecha_hasta" id="fecha_hasta_modal_editar" value="<?= $cronograma->fecha_hasta; ?>" min="<?= $cronograma->fecha_desde; ?>">
		        			</div>
		        		</div>
		        		<div class="col-sm-4">
		        			<div class="form-group">
		        				<label for="value">Mes *</label>
		        				<input class="form-control" type="number" name="mes" id="mes" min="1" max="12" required="required" value="<?= $cronograma->mes; ?>" disabled="disabled">
		        			</div>
		        		</div>
		        	</div>

		        	<div class="row">
		        		<div class="col-sm-4">
		        			<div class="form-group">
		        				<label for="value">Semana</label>
		        				<input class="form-control" type="number" name="semana" id="semana" min="1" value="<?= $cronograma->semana; ?>">
		        			</div>
		        		</div>
              			<div class="col-sm-4">
              				<div class="form-group">
        						<label for="horario">Horario</label>
    							<input class="form-control" type="text" name="horario" id="horario" value="<?= $cronograma->horario; ?>">
      						</div>
              			</div>
		        	</div>
		      	</div>


		      	<div class="modal-footer">
		        	<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		        	<button type="button" class="btn btn-primary" id="editar_cronograma"><i class="fa fa-check"></i> Guardar</button>
		      	</div>
			</div>
		</form>
	</div>
</div>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<script>
	$(document).ready(function() {
		$('.select2').select2();

    $(document).on('change', '#municipio_modal_editar', function() { cargar_instituciones_modal(); });
    $(document).on('change', '#institucion_modal_editar', function() { cargar_sedes_modal(); });

		$('#modal_editar_cronograma').modal('show');
	});

  function cargar_instituciones_modal()
    {
        var municipio = $('#municipio_modal_editar').val();
        $('#institucion_modal_editar').select2('val', '');

        $.ajax({
            url: 'functions/fn_obtener_institutos.php',
            type: 'POST',
            dataType: 'HTML',
            data: {
                'municipio': municipio
            },
        })
        .done(function(data) {
            $('#institucion_modal_editar').html(data);
        })
        .fail(function(data) {
            console.log(data.responseText);
        });
    }

    function cargar_sedes_modal()
    {
        var institucion = $('#institucion_modal_editar').val();
        $('#sede_modal_editar').select2('val', '');

        $.ajax({
            url: 'functions/fn_obtener_sedes.php',
            type: 'POST',
            dataType: 'HTML',
            data: {
                'institucion': institucion
            },
        })
        .done(function(data) {
            $('#sede_modal_editar').html(data);
        })
        .fail(function(data) {
            console.log(data.responseText);
        });
    }
</script>