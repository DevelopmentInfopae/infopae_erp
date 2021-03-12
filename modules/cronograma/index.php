<?php
  	include '../../header.php';
  	set_time_limit (0);
  	ini_set('memory_limit','6000M');

  	$titulo = "Cronograma";

  	$periodo_actual = $_SESSION["periodoActual"];
    $municipio_defecto = $_SESSION["p_Municipio"];
    $departamento_operador = $_SESSION['p_CodDepartamento'];

    $c_municipio = "SELECT DISTINCT codigoDANE, ciudad FROM ubicacion WHERE ETC <> '1' ";
    if($departamento_operador != ''){
      $c_municipio .= " AND CodigoDANE LIKE '$departamento_operador%' ";
    }
    if ($municipio_defecto) {
      $c_municipio .= " AND CodigoDANE LIKE '$municipio_defecto%' ";
    }
    $c_municipio .= " ORDER BY ciudad ASC";
    $r_municipio = $Link->query($c_municipio) or die("Error al consultar los munnicipios. ". $Link->error);
    if($r_municipio->num_rows > 0){
      	while($row = $r_municipio->fetch_object()) {
      		$municipios[] = $row;
      	}
    }

    if (isset($_POST["municipio"]) && !empty($_POST["municipio"]) || isset($municipio_defecto)) {
        $codigo_municipio = (!empty($_POST["municipio"])) ? $_POST["municipio"] : $municipio_defecto;
        $c_institucion = "SELECT DISTINCT cod_inst AS codigo, nom_inst AS nombre FROM sedes".$periodo_actual." WHERE cod_mun_sede = '".$codigo_municipio."' ORDER BY nom_inst;";
        $r_institucion = $Link->query($c_institucion) or die("Error al consultar instituciones ". $Link->error);

        if ($r_institucion->num_rows > 0) {
            while($instituto = $r_institucion->fetch_object()) {
                $instituciones[] = $instituto;
            }
        }
    }

    if (isset($_POST["institucion"]) && !empty($_POST["institucion"])) {
        $c_sedes = "SELECT DISTINCT cod_sede AS codigo, nom_sede AS nombre FROM sedes".$periodo_actual." WHERE cod_inst = '".$_POST["institucion"]."';";
        $r_sedes = $Link->query($c_sedes) or die("Error al consultar municipios ". $link->error);

        if ($r_sedes->num_rows > 0) {
            while($sede = $r_sedes->fetch_object()) {
                $sedes[] = $sede;
            }
        }
    }

    $c_cronogramas = "SELECT c.id, c.mes, c.semana, s.nom_inst AS nombre_institucion, c.cod_sede AS codigo_sede, s.nom_sede AS nombre_sede, c.fecha_desde, c.fecha_hasta, c.horario FROM cronograma c INNER JOIN sedes".$periodo_actual." s ON s.cod_sede = c.cod_sede";
    if (isset($_POST["municipio"]) && !empty($_POST["municipio"])) { $c_cronogramas.=" WHERE s.cod_mun_sede = '".$_POST["municipio"]."'"; }
    if (isset($_POST["institucion"]) && !empty($_POST["institucion"])) { $c_cronogramas.=" AND s.cod_inst = '".$_POST["institucion"]."'"; }
    if (isset($_POST["sede"]) && !empty($_POST["sede"])) { $c_cronogramas.=" AND c.cod_sede = '".$_POST["sede"]."'"; }

    $r_cronogramas = $Link->query($c_cronogramas) or die("Error al consultar el listado de cronograma: ". $Link->error);
    if ($r_cronogramas->num_rows > 0) {
        while($registro_cronograma = $r_cronogramas->fetch_object()) {
            $cronogramas[] = $registro_cronograma;
        }
    }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  	<div class="col-lg-8">
      	<h2><?= $titulo; ?></h2>
      	<ol class="breadcrumb">
          	<li>
              	<a href="<? $baseUrl; ?>">Inicio</a>
          	</li>
          	<li class="active">
              	<strong><?= $titulo; ?></strong>
          	</li>
      </ol>
  	</div>
  	<div class="col-lg-4">
      	<div class="title-action">
        	<?php if($_SESSION["perfil"] == 1 || $_SESSION["perfil"] == 0) { ?>
          		<a href="#" class="btn btn-primary" id="crear_cronograma"><i class="fa fa-plus"></i> Nuevo</a>
        	<?php } ?>
      	</div>
  	</div>
</div>

<div class="wrapper wrapper-content  animated fadeInRight">
  	<div class="row">
    	<div class="col-sm-12">
      		<div class="ibox">
        		<div class="ibox-content">
          			<div class="row">
            			<div class="col-sm-12">
              				<form action="#" id="formSedes" name="formSedes" method="post">
                				<div class="row">
                  					<div class="col-sm-3 form-group">
                    					<label for="municipio">Municipio</label>
                    					<select class="form-control select2" name="municipio" id="municipio" required>
                      						<option value="">Seleccione uno</option>
                      						<?php foreach ($municipios as $key => $municipio) { ?>
					                            <option value="<?= $municipio->codigoDANE; ?>"
					                            	<?= (isset($_POST["municipio"]) && $_POST["municipio"] == $municipio->codigoDANE || $municipio_defecto == $municipio->codigoDANE) ? "selected" : ""; ?>>
					                              	<?= $municipio->ciudad; ?>
					                            </option>
                      						<?php } ?>
                    					</select>
                  					</div>

                  					<div class="col-sm-3">
                  						<div class="form-group">
                    						<label for="institucion">Institución</label>
                							<select class="form-control select2" name="institucion" id="institucion">
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

		                  			<div class="col-sm-3">
		                  				<div class="form-group">
                    						<label for="sede">Sede</label>
                							<select class="form-control select2" name="sede" id="sede">
                  								<option value="">Todas</option>
                                                <?php if (isset($sedes)) { ?>
                                                    <?php foreach ($sedes as $key => $sede) { ?>
                                                        <option value="<?= $sede->codigo; ?>"
                                                            <?= (isset($_POST["sede"]) && $_POST["sede"] == $sede->codigo) ? "selected" : ""; ?>>
                                                            <?= $sede->nombre; ?>
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                							</select>
                  						</div>
		                  			</div>

                              		<div class="col-sm-3 form-group">
                                		<button class="btn btn-primary" type="submit" id="btnBuscar" style="margin-top: 24px;"> <i class="fa fa-search"></i> Buscar</button>
                              		</div>
	                  			</div>
	                  		</form>
                		</div>
            		</div>
        		</div>
      		</div>
		</div>
  	</div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-content">
                    <table class="table table-striped table-hover" id="tabla_cronograma">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th>Semana</th>
                                <th>Nombre Institución</th>
                                <th>Código Sede</th>
                                <th>Nombre Sede</th>
                                <th>Fecha desde</th>
                                <th>Fecha hasta</th>
                                <th>Horario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($cronogramas)) { ?>
                                <?php foreach ($cronogramas as $key => $cronograma): ?>
                                    <tr>
                                        <td><?= $cronograma->mes; ?></td>
                                        <td><?= $cronograma->semana; ?></td>
                                        <td><?= $cronograma->nombre_institucion; ?></td>
                                        <td><?= $cronograma->codigo_sede; ?></td>
                                        <td><?= $cronograma->nombre_sede; ?></td>
                                        <td><?= $cronograma->fecha_desde; ?></td>
                                        <td><?= $cronograma->fecha_hasta; ?></td>
                                        <td><?= $cronograma->horario; ?></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <div class="dropdown pull-right">
                                                    <button class="btn btn-primary btn-sm" type="button" id="dropDownMenu1" data-toggle="dropdown"  aria-haspopup="true">
                                                        Acciones <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu pull-right" aria-labelledby="dropDownMenu1">
                                                        <li>
                                                            <a href="#" class="editar_cronograma" data-cronograma_id="<?= $cronograma->id; ?>"><i class="fa fa-pencil-square-o fa-lg"></i> Editar</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="contenedor_crear_cronograma"></div>
<div id="contenedor_editar_cronograma"></div>

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
<!-- <script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script> -->
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<script>
	$(document).ready(function() {
		$('.select2').select2();

		$(document).on('change', '#municipio', function() { cargar_instituciones(); });
		$(document).on('change', '#institucion', function() { cargar_sedes(); });
        $(document).on('click', '#crear_cronograma', function() { abrir_modal_crear_cronograma(); });
        $(document).on('click', '.editar_cronograma', function() { abrir_modal_editar_cronograma($(this).data('cronograma_id')); });

        // $(document).on('click', '#save_payment_button', function() { save_payment(); });
        $(document).on('change', '#fecha_desde', function() { $('#fecha_hasta').prop('min', $('#fecha_desde').val()) });
        $(document).on('change', '#fecha_hasta', function() { $('#fecha_desde').prop('max', $('#fecha_hasta').val()) });
        // $(document).on('change', '#municipio_modal', function() { cargar_instituciones_modal(); });
        // $(document).on('change', '#institucion_modal', function() { cargar_sedes_modal(); });
        $(document).on('click', '#guardar_cronograma', function() { guardar_cronograma(); });

        // $(document).on('change', '#municipio_modal', function() { cargar_instituciones('_modal_editar'); });
        // $(document).on('change', '#institucion_modal', function() { cargar_sedes('_modal_editar'); });
        $(document).on('change', '#fecha_desde_modal_editar', function() { $('#fecha_hasta_modal_editar').prop('min', $('#fecha_desde_modal_editar').val()) });
        $(document).on('change', '#fecha_hasta_modal_editar', function() { $('#fecha_desde_modal_editar').prop('max', $('#fecha_hasta_modal_editar').val()) });
        $(document).on('click', '#editar_cronograma', function() { editar_cronograma(); });

        $('#tabla_cronograma').DataTable({
            pageLength: 25,
            responsive: true,
            dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
            oLanguage: {
                sLengthMenu: 'Mostrando _MENU_ registros',
                sZeroRecords: 'No se encontraron registros',
                sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros ',
                sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
                sInfoFiltered: '(Filtrado desde _MAX_ registros)',
                sSearch:         'Buscar: ',
                oPaginate:{
                    sFirst:    'Primero',
                    sLast:     'Último',
                    sNext:     'Siguiente',
                    sPrevious: 'Anterior'
                }
            },
            buttons: [ {extend: 'excel', title: 'Instituciones', className: "btnExportarExcel"} ]
        });

        load_button_dataTables();
	});

    function load_button_dataTables()
    {
        var botonAcciones = '<div class="dropdown pull-right">'+
                                '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true">'+
                                    'Acciones <span class="caret"></span>'+
                                '</button>'+
                                '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu2">'+
                                    '<li>'+
                                        '<a class="fileinput fileinput-new" data-provides="fileinput">'+
                                            '<span class="btn-file">'+
                                                '<i class="fa fa-upload"></i>'+
                                                '<span class="fileinput-new"> Importar</span>'+
                                                '<span class="fileinput-exists">Cambiar</span>'+
                                                '<input type="file" name="archivo" id="archivo" onchange="if(!this.value.length) return false; importar_plantilla();" accept=".csv, .xlsx">'+
                                            '</span>'+
                                            '<span class="fileinput-filename center-block"></span>'+
                                            '<span href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</span>'+
                                        '</a>'+
                                    '</li>'+
                                    '<li class="divider"></li>'+
                                    '<li>'+
                                        '<a href="'+ $('#inputBaseUrl').val() +'/modules/cronograma/functions/fn_cronograma_exportar_plantilla.php" target="_blank"><i class="fa fa-download"></i> Descarga Plantilla .CSV </a>'+
                                    '</li>'+
                                '</ul>'+
                            '</div>';
      $('.containerBtn').html(botonAcciones);
    }

    function abrir_modal_crear_cronograma()
    {
        $('#contenedor_crear_cronograma').load($('#inputBaseUrl').val() +'/modules/cronograma/add.php?departamento_defecto=<?= $departamento_operador; ?>');
    }

    function abrir_modal_editar_cronograma(cronograma_id)
    {
        $('#contenedor_editar_cronograma').load($('#inputBaseUrl').val() +'/modules/cronograma/edit.php?cronograma_id='+cronograma_id+'&departamento_defecto=<?= $departamento_operador; ?>');
    }

	function cargar_instituciones(tipo = '')
    {
        var municipio = $('#municipio'+tipo).val();
        $('#institucion'+tipo).select2('val', '');

        $.ajax({
            url: 'functions/fn_obtener_institutos.php',
            type: 'POST',
            dataType: 'HTML',
            data: {
                'municipio': municipio
            },
        })
        .done(function(data) {
            $('#institucion'+tipo).html(data);
        })
        .fail(function(data) {
            console.log(data.responseText);
        });
    }

	function cargar_sedes(tipo = '')
    {
        var institucion = $('#institucion'+tipo).val();
        $('#sede'+tipo).select2('val', '');

        $.ajax({
            url: 'functions/fn_obtener_sedes.php',
            type: 'POST',
            dataType: 'HTML',
            data: {
                'institucion': institucion
            },
        })
        .done(function(data) {
            $('#sede'+tipo).html(data);
        })
        .fail(function(data) {
            console.log(data.responseText);
        });
    }

    function importar_plantilla(){
        var formData = new FormData();
        formData.append('archivo', $('#archivo')[0].files[0]);

        $.ajax({
            url: "functions/fn_cronograma_importar_plantilla.php",
            type: "POST",
            contentType: false,
            processData: false,
            data: formData,
            dataType: 'JSON',
            beforeSend: function() { $('#loader').fadeIn(); },
            success: function(data) {
                if(data.estado == '1') {
                    if (data.log != '') {
                        Command: toastr.error('Los siguientes registros no fueron almacenados: <br>'+data.log, "¡Error!", { onHidden : function(){ $('#loader').fadeOut(); }});
                    }

                    Command: toastr.success(data.mensaje, "¡Correcto!", { onHidden : function(){ $('#loader').fadeOut(); location.reload(); }});
                } else {
                    Command: toastr.error(data.mensaje, "¡Error!", { onHidden : function(){ $('#loader').fadeOut(); }});

                    if (data.log != '') {
                        Command: toastr.error('Los siguientes registros no fueron almacenados: <br>'+data.log, "Error al procesar", { onHidden : function(){ $('#loader').fadeOut(); location.reload(); }});
                    }
                }
            },
            error: function(data){
                Command: toastr.error("Existe un error con el archivo. Por favor verifique los datos suministrados. Posiblemente los códigos de sedes se encuentran duplicados.", "¡Error!", { onHidden : function(){ $('#loader').fadeOut(); console.log(data.responseText) }});
            }
        });
    }

    function guardar_cronograma()
    {
        if (validar_formulario_guardar()) {
            $.ajax({
                url: 'functions/fn_cronograma_crear.php',
                type: 'POST',
                dataType: 'JSON',
                data: $("#formulario_crear_cronograma").serialize(),
            })
            .done(function(data) {
                if (data.response == 1) {
                    Command: toastr.success(data.message, '¡Correcto!', {onHidden: function() { $('#modal_crear_cronograma').modal('hide'); location.reload(); }});
                } else {
                    Command: toastr.error(data.message, '¡Error!');
                }
            })
            .fail(function(data) {
                Command: toastr.error('Al parecer existe un error. Por favor comuníquese con el adminitrador del sistema.', '¡Error!', {onHidden: function() { console.log(data.responseText); }});
            });
        }
    }

    function validar_formulario_guardar()
    {
        if ($('#sede_modal').val() == '') {
            Command: toastr.error('El campo Sede es obligatorio.', 'Mensaje de validación', {onHidden: function() { $('#sede_modal').focus(); }});
            return false;
        }

        if ($('#mes').val() == '') {
            Command: toastr.error('El campo Mes es obligatorio.', 'Mensaje de validación', {onHidden: function() { $('#mes').focus(); }});
            return false;
        } else if ($('#mes').val() == 0) {
            Command: toastr.error('El campo Mes debe ser mayor a 0.', 'Mensaje de validación', {onHidden: function() { $('#mes').focus(); }});
            return false;
        }

        if ($('#semana').val() <= 0) {
            Command: toastr.error('El campo Semana debe ser mayor a 0.', 'Mensaje de validación', {onHidden: function() { $('#semana').focus(); }});
            return false;
        }

        return true;
    }

    function editar_cronograma()
    {
        if (validar_formulario_editar()) {
            $.ajax({
                url: 'functions/fn_cronograma_editar.php',
                type: 'POST',
                dataType: 'JSON',
                data: $("#formulario_editar_cronograma").serialize(),
            })
            .done(function(data) {
                if (data.response == 1) {
                    Command: toastr.success(data.message, '¡Correcto!', {onHidden: function() { $('#modal_editar_cronograma').modal('hide'); location.reload(); }});
                } else {
                    Command: toastr.error(data.message, '¡Error!');
                }
            })
            .fail(function(data) {
                Command: toastr.error('Al parecer existe un error. Por favor comuníquese con el adminitrador del sistema.', '¡Error!', {onHidden: function() { console.log(data.responseText); }});
            });
        }
    }

    function validar_formulario_editar()
    {
        if ($('#semana').val() <= 0) {
            Command: toastr.error('El campo Semana debe ser mayor a 0.', 'Mensaje de validación', {onHidden: function() { $('#semana').focus(); }});
            return false;
        }

        return true;
    }
</script>