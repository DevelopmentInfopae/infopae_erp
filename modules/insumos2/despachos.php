<?php 
$titulo = "Despachos Insumos";
require_once "../../header.php";

if ($permisos['despachos'] == "0") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

$periodoActual = $_SESSION['periodoActual'];
$codigoMunicipio = $_SESSION['p_Municipio'];
$codigoDepartamento = $_SESSION['p_CodDepartamento'];

$meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$consultaTablaInsumos = "SELECT
							table_name AS tabla
						FROM
							information_schema.tables
						WHERE
							table_schema = DATABASE() AND table_name like 'insumosmovdet%'";
							
$respuestaTablaInsumos = $Link->query($consultaTablaInsumos) or die ('Error al consultar los meses despachados ' . mysqli_error($Link));
if ($respuestaTablaInsumos->num_rows > 0) {
	while ($dataTablaInsumos = $respuestaTablaInsumos->fetch_assoc()) {
		$tabla = $dataTablaInsumos['tabla'];
		$mes[] = substr($tabla, 13, -2);
	}	
}else {
	echo "<script>location.href='$baseUrl/modules/insumos2/despacho_insumo.php';</script>";
}							

$consultaTipoMovimiento = " SELECT id, Movimiento FROM tipomovimiento ";
$respuestaTipoMovimiento = $Link->query($consultaTipoMovimiento) or die ('Error al consultar el tipo de movimiento' . mysqli_error($Link));
if ($respuestaTipoMovimiento->num_rows > 0) {
	while ($dataTipoMovimiento = $respuestaTipoMovimiento->fetch_assoc()) {
		$tipoMovimiento[$dataTipoMovimiento['id']] = $dataTipoMovimiento['Movimiento'];
	}
}

$consultaRutas = " SELECT ID, Nombre FROM rutas ";
$respuestaRutas = $Link->query($consultaRutas) or die ('Error al consultar las rutas ' . mysqli_error($Link));
if ($respuestaRutas->num_rows > 0) {
	while ($dataRutas = $respuestaRutas->fetch_assoc()) {
		$rutas[$dataRutas['ID']] = $dataRutas['Nombre'];
	}
}

if ($codigoMunicipio == "0") {
	$consultaMunicipio = " SELECT CodigoDANE, Ciudad FROM ubicacion WHERE CodigoDANE LIKE '$codigoDepartamento%' ";
}else if ($codigoMunicipio != "0") {
	$consultaMunicipio = " SELECT CodigoDANE, Ciudad FROM ubicacion WHERE CodigoDANE = '$codigoMunicipio' ";
}
$respuestaMunicipio = $Link->query($consultaMunicipio) or die ('Error al consultar los municipios ' . mysqli_error($Link));
if ($respuestaMunicipio->num_rows > 0 ) {
	while ($dataMunicipios = $respuestaMunicipio->fetch_assoc()) {
		$municipios[$dataMunicipios['CodigoDANE']] = $dataMunicipios['Ciudad'];
	}
}

// resultado de la busqueda _POST
if (isset($_POST['buscar']) && ($_POST['buscar'] != "")) {
	$mesPost = $_POST['mes_inicio'];
	$tipoDocumentoPost = $_POST['tipoDocumento'];
	$responsablePost = $_POST['responsable'];
	$rutaPost = $_POST['rutas'];
	$municipioPost = (isset($_POST['municipio']) ? $_POST['municipio'] : "");
	$institucionPost = (isset($_POST['institucion']) ? $_POST['institucion'] : "");
	$sedePost = (isset($_POST['sede']) ? $_POST['sede'] : "");
	$imprimirMesPost = $_POST['imprimirMes'];
	$observacionesPost = $_POST['observaciones'];
	$buscarPost = $_POST['buscar'];
	
	$condiciones = "";
	$inners = "";

	if ($tipoDocumentoPost != "") {	
		$inners.= " INNER JOIN tipomovimiento tim ON tim.Movimiento = ins.Tipo ";
		$condiciones .= " AND tim.id = $tipoDocumentoPost ";
		if ($responsablePost != "") {
			$consultaResponsable = " SELECT Nitcc FROM empleados WHERE Nitcc = $responsablePost ";
			$respuestaResponsable = $Link->query($consultaResponsable) or die ('Error al consultar responsable ' . mysqli_error($Link));
			if ($respuestaResponsable->num_rows > 0) {
				$inners .= " INNER JOIN empleados emp ON ins.Nitcc = emp.Nitcc ";
				$condiciones .= " AND emp.Nitcc = $responsablePost ";
			}else{
				$consultaResponsable = " SELECT Nitcc FROM proveedores WHERE Nitcc = $responsablePost ";
				$respuestaResponsable = $Link->query($consultaResponsable) or die ('Error al consultar responsable ' . mysqli_error($Link));
				if ($respuestaResponsable->num_rows > 0) {
					$inners .= " INNER JOIN proveedores pro ON ins.Nitcc = pro.Nitcc ";
					$condiciones .= " AND pro.Nitcc = $responsablePost ";
				}
			}
		}
	}

	if ($rutaPost != "") {
		$inners .= " INNER JOIN rutasedes rut ON ins.BodegaDestino = rut.cod_sede ";
		$condiciones .= " AND rut.IDRUTA = $rutaPost "; 
	}else if ($municipioPost != "") {
		$condiciones .= " AND ubi.CodigoDANE = $municipioPost ";
		if ($institucionPost != "") {
			$condiciones .= " AND sed.cod_inst = $institucionPost ";
			if ($sedePost != "") {
				$condiciones .= " AND sed.cod_sede = $sedePost ";
			}
		}
	}

	$consulta = " SELECT  ins.Id AS id, 
						ins.Tipo AS tipo, 
						ins.Numero AS numero, 
						ubi.Ciudad AS ciudad, 
						ins.fechaMYSQL AS fecha, 
						bod.NOMBRE AS nombre, 
						sed.nom_sede AS nomSede,
						sed.nom_inst AS nomInst,
						sed.cod_sede AS codigoSede,
						sed.cod_inst AS codigoInst,
						ins.Complemento AS complemento, " .
						(isset($mesPost) ? $mesPost . ' AS mesDespacho ' : '') ."
				FROM insumosmov$mesPost$periodoActual ins	
				INNER JOIN sedes$periodoActual sed ON sed.cod_sede = ins.BodegaDestino
				INNER JOIN ubicacion ubi ON sed.cod_mun_sede = ubi.CodigoDANE
				INNER JOIN bodegas bod ON ins.BodegaOrigen = bod.ID
				$inners 		
				WHERE 1 = 1	
				$condiciones 
				ORDER BY ubi.Ciudad, sed.nom_inst, sed.cod_sede
				";
}
// exit(var_dump($consulta));
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2><?= $titulo?></h2>
		<ol class="breadcrumb">
			<li>
				<a href="<?= $baseUrl; ?>">Inicio</a>
			</li>
			<li class="active">
				<strong><?= $titulo; ?></strong>
			</li>
		</ol>
	</div> <!-- col-lg-8 -->
	<div class="col-lg-4">
		<?php if ($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"): ?>
			<div class="title-action">
				<button class="btn btn-primary" onclick="window.location.href = '<?php echo $baseUrl; ?>/modules/insumos2/despacho_insumo.php';"><span class="fa fa-plus"></span> Nuevo</button>
			</div>
		<?php endif ?>
	</div>
</div> <!-- wrapper-content -->

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
		 		 	<form class="form row" id="formBuscar" method="POST">
		 		 		<div id="mesDespachos">
			  				<div class="form-group col-lg-3 col-sm-6">
			  					<label for="desde">Desde</label>
			  					<div class="nopadding">
			  						<select name="mes_inicio" id="mes_inicio" class="form-control">
										<?php foreach ($mes as $key => $value):?>
											<option value="<?= $value; ?>" <?= (isset($mesPost) && $mesPost == $value) ? "selected" : "" ?> > <?= $meses[$value];?> </option>
										<?php endforeach ?>
									</select>
			  					</div>
		 		 			</div> <!-- col-lg-3 -->
		 		 			<div class="form-group col-lg-3 col-sm-6">
		 		 				<label for="hasta">Hasta</label>
			  					<div class="nopadding">
			 		 				<select name="mes_fin" id="mes_fin" class="form-control" disabled>
			 		 					<?php foreach ($mes as $key => $value): ?>
			 		 						<option value="<?= $value; ?>"> <?= $meses[$value];?> </option>
			 		 					<?php endforeach ?>
			 		 				</select>
		 		 				</div>
		 		 			</div> <!-- col-lg-3 -->
		 		 		</div>
		 		 		<div class="form-group col-lg-3 col-sm-6">
		 		 			<label for="tipoDocumento"> Tipo Documento</label>
		 		 			<select name="tipoDocumento" id="tipoDocumento" class="form-control">
		 		 				<option value="">Seleccione...</option>
		 		 				<?php foreach ($tipoMovimiento as $key => $value): ?>
		 		 					<option value="<?= $key; ?>" <?= (isset($tipoDocumentoPost) && $tipoDocumentoPost == $key) ? "selected" : "" ?> ><?= $value; ?></option>
		 		 				<?php endforeach ?>
		 		 			</select>
		 		 		</div> <!-- col-lg-3 --> 
		 		 		<div class="form-group col-lg-3 col-sm-6">
		 		 			<label for="responsable "> Proveedor / Responsable</label>
		 		 			<select name="responsable" id="responsable" class="form-control">
		 		 				<option value="<?= (isset($responsablePost) && $responsablePost != '') ? $responsablePost : '' ?>">Seleccione...</option>
		 		 			</select>
		 		 		</div> <!-- col-lg-3 -->
		 		 		<div class="form-group col-lg-3 col-sm-6">
		 		 			<label for="rutas">Rutas</label>
		 		 			<select name="rutas" id="rutas" class="form-control">
		 		 				<option value="">Seleccione...</option>
		 		 				<?php foreach ($rutas as $id => $nombre): ?>
		 		 					<option value="<?= $id; ?>" <?= (isset($rutaPost) && $rutaPost == $id) ? "selected" : "" ?> ><?= $nombre; ?></option>
		 		 				<?php endforeach ?>
		 		 			</select>
		 		 		</div> <!-- col-lg-3 -->
		 		 		<div class="form-group col-lg-3 col-sm-6">
		 		 			<label for="municipio">Municipio</label>
		 		 			<select name="municipio" id="municipio" class="form-control">
		 		 				<option value="">Seleccione...</option>
		 		 				<?php foreach ($municipios as $codigo => $ciudad): ?>
		 		 					<option value="<?= $codigo; ?>" <?= ($codigoMunicipio == $codigo) ? "selected" : ""?>> <?= $ciudad; ?> </option>
		 		 				<?php endforeach ?>
		 		 			</select>
		 		 		</div> <!-- col-lg-3 -->
		 		 		<div class="form-group col-lg-3 col-sm-6">
		 		 			<label for="institucion">Institución</label>
		 		 			<select class="form-control select2" name="institucion" id="institucion">
		 		 				<option value="<?= (isset($institucionPost) && $institucionPost != '') ? $institucionPost : '' ?>">Seleccione...</option>
		 		 			</select>
		 		 		</div> <!-- col-lg-3 -->
		 		 		<div class="form-group col-lg-3 col-sm-6">
		 		 			<label for="sedes">Sede</label>
		 		 			<select class="form-control select2" name="sede" id="sede">
		 		 				<option value="">Seleccione...</option>
		 		 			</select>
		 		 		</div> <!-- col-lg-3 -->
		 		 		<div class="form-group col-lg-3 col-sm-6">
							<label for="nombreMes">Imprimir nombre del mes</label>
							<div>
								<input type="checkbox" name="imprimirMes" id="imprimirMes" checked>
							</div>
						</div> <!-- col-lg-3 -->
						<div class="form-group col-lg-12">
							<label for="observaciones">Observaciones</label>
							<textarea class="form-control" name="observaciones" id="observaciones" cols="30" rows="5"></textarea>
						</div> <!-- col-lg-12 -->	
						<div class="col-sm-12">
			  				<button class="btn btn-primary" onclick="$('#formBuscar').submit();" id="btnBuscar"> <span class="fa fa-search"></span>  Buscar</button>
			  				<?php if (isset($_POST['buscar'])): ?>
								<button class="btn btn-primary" onclick="location.href='despachos.php';" id="btnBuscar"> <span class="fa fa-times"></span>  Limpiar búsqueda</button>
			  				<?php endif ?>
						</div>
						<input type="hidden" name="buscar" value="1">
		 		 	</form> <!-- form -->
				</div> <!-- ibox-content -->
			</div><!-- float-e-margins -->
		</div><!--  col-lg-12 -->
	</div><!-- row -->
</div> <!-- wrapper-content -->

<?php if (isset($_POST['buscar']) && $_POST['buscar'] != ""): ?>
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<div class="table-responsive">
					<label>
						<input type="checkbox" class="i-checks" name="selecTodos" id="selecTodos"> Seleccionar todos
					</label>
					<form id="formDespachos" method="POST" target="_blank">
					<input type="hidden" name="tablaMesInicio" id="tablaMesInicio" value="<?= $mesPost; ?>">
					<input type="hidden" name="tablaMesFin" id="tablaMesFin" value="<?= $mesPost; ?>">
					<input type="hidden" name="despachos_seleccionados" id="despachos_seleccionados">
					<input type="hidden" name="paginasObservaciones" id="paginasObservaciones" value="<?= $observacionesPost ?>">
					<input type="hidden" name="mesImprimir" id="mesImprimir">
					<input type="hidden" name="despachosEliminar" id="despachosEliminar">
					<input type="hidden" name="ruta" id="ruta">
					<table class="table" id="tablaInsumos">
						<thead>
							<tr>
								<th></th>
								<th>Tipo Documento</th>
								<th>Número</th>
								<th>Municipio</th>
								<th>Institución</th>
								<th>Fecha / Hora</th>
								<th>Nombre Bodega Origen</th>
								<th>Nombre Bodega Destino</th>
								<th>Complemento</th>
							</tr>
						</thead>
						<tbody id="tBodyInsumos">
							
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th>Tipo Documento</th>
								<th>Número</th>
								<th>Municipio</th>
								<th>Institución</th>
								<th>Fecha / Hora</th>
								<th>Nombre Bodega Origen</th>
								<th>Nombre Bodega Destino</th>
								<th>Complemento</th>
							</tr>
						</tfoot>
					</table>
					</form>
					<input type="hidden" name="consulta" id="consulta" value="<?php echo $consulta; ?>">
					</div> <!-- table-responsive -->
				</div> <!-- contentBackground -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
<?php endif ?>

<?php include '../../footer.php'; ?>

 <!-- formulario para editar el despacho -->
<form method="POST" action="editar_despacho_insumo.php" id="editar_despacho">
  	<input type="hidden" name="id_despacho" id="id_despacho">
  	<input type="hidden" name="mesTabla" id="mesTabla">
</form>

<!-- modal eliminar despacho -->
<div class="modal inmodal fade" id="modalEliminarDespachos" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 	<div class="modal-dialog modal-sm">
   		<div class="modal-content">
	 		<div class="modal-header text-info" id="tipoCabecera" style="padding: 15px;">
	   			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
	   			<h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
	 		</div>
	 		<div class="modal-body" style="text-align: center;">
		 		<h3>¿Está seguro de eliminar los despachos seleccionados?</h3>
	 		</div>
	 		<div class="modal-footer">
	   			<button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
	   			<button type="button" class="btn btn-primary btn-sm" id="tipoBoton" onclick="eliminarDespachos();">Si</button>
	 		</div>
   		</div>
 	</div>
</div>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>

<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/modules/insumos2/js/despachos.js"></script>

<script type="text/javascript">
	dataset1 = $('#tablaInsumos').DataTable({
		ajax: {
			method: 'POST',
			url: 'functions/fn_insumos_obtener_datos_tabla.php',
			data:{
		  		consulta: $('#consulta').val()
			}
	  	},
		columns:[
			{ data: 'id'},
			{ data: 'tipo'},
			{ data: 'numero'},
			{ data: 'ciudad'},
			{ data: 'nomInst'},
			{ data: 'fecha'},
			{ data: 'nombre'},
			{ data: 'nomSede'},
			{ data: 'complemento' }
	  	],
	  	columnDefs: [
			{
			// "targets": [7],
			"visible": false,
			"searchable": false
			}
		],
		// order: [[ 3, 'asc'], [ 4, 'asc' ], [7, 'asc']],
		pageLength: 25,
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "TODO"] ],
		responsive: true,
		dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
		buttons : [{extend:'excel', title:'Menus', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6]}}],
		oLanguage: {
	  		sLengthMenu: 'Mostrando _MENU_ registros por página',
	  		sZeroRecords: 'No se encontraron registros',
	  		sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
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
		"preDrawCallback": function( settings ) {
			$('#loader').fadeIn();
	  	},
		'fnRowCallback': function (nRow, aData, iDisplayIndex) {
			return nRow;
		},
	}).on("draw", function(){ $('#loader').fadeOut();
  	$('.checkDespacho').iCheck({
	 	checkboxClass: 'icheckbox_square-green '
	});

	$('#selecTodos').on('ifChecked', function(){
	  	$('.checkDespacho').iCheck('check');
	});

	$('#selecTodos').on('ifUnchecked', function(){
	  	$('.checkDespacho').iCheck('uncheck');
	});

	$('.checkDespacho').on('ifChecked', function(){
	  	$('#sede_'+$(this).data('num')).prop('checked', true);
	});

	$('.checkDespacho').on('ifUnchecked', function(){
	  	$('#sede_'+$(this).data('num')).prop('checked', false);
	});
	var btnAcciones = 	'<div class="dropdown pull-right" id="">'+
					  		'<button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button>'+
							'<ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">'+
						  		'<li><a onclick="informeDespachos(1);"><span class="fa fa-file-excel-o"></span> Individual </a></li>'+
						  		'<li><a onclick="informeDespachos2(1);"><span class="fa fa-file-excel-o"></span> Individual 2 </a></li>'+
						  		'<li><a onclick="informeDespachos2Vertical(1);"><span class="fa fa-file-excel-o"></span> Individual Vertical </a></li>'+
						  		'<li><a onclick="informeDespachosVertical2(1);"><span class="fa fa-file-excel-o"></span> Individual Vertical 2</a></li>'+
						  		'<li><a onclick="informeDespachosInstitucion(1);"><span class="fa fa-file-excel-o"></span> Institución </a></li>'+
						  		'<li><a onclick="informeConsolidadoVertical(1);"><span class="fa fa-file-excel-o"></span> Consolidado Vertical </a></li>'+
						  		'<li><a onclick="informeDespachosConsolidado(1);"><span class="fa fa-file-excel-o"></span> Consolidado </a></li>'+
						  		<?php if ($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"): ?>
						  			'<li><a onclick="editarDespacho();"><span class="fas fa-pencil-alt"></span> Editar </a></li>'+
						  			'<li><a data-toggle="modal" data-target="#modalEliminarDespachos"><span class="fa fa-trash"></span> Eliminar </a></li>'+
						  			'<li><a onclick=";"><span class="fa fa-clock-o"></span> Lote y Fec. Venc. </a></li>'+
						 	 	<?php endif ?>
							'</ul>'+
						'</div>';

  	$('.containerBtn').html(btnAcciones);
  	
  	$(document).on('ifChecked', '.checkDespacho', function(){
		var despachos = "";
		$('.checkDespacho').each(function(index, val){
			if ($(this).iCheck('data')[0].checked) {
				despachos += $(this).data('iddespacho')+"_"+$(this).data('mesdespacho')+", ";
			}
		});
		$('#despachos_seleccionados').val('').val(despachos);
	});

	$(document).on('ifUnchecked', '.checkDespacho', function(){
		var despachos = "";
		$('.checkDespacho').each(function(index, val){
			if ($(this).iCheck('data')[0].checked) {
				despachos += $(this).data('iddespacho')+"_"+$(this).data('mesdespacho')+", ";
			}
		});
		$('#despachos_seleccionados').val('').val(despachos);
	});


	$(document).on('ifChecked', '.checkDespacho', function(){
		var despachosEliminar = "";
		$('.checkDespacho').each(function(index, val){
			if ($(this).iCheck('data')[0].checked) {
				despachosEliminar += $(this).data('iddespacho')+", ";
			}
		});
		$('#despachosEliminar').val('').val(despachosEliminar);
	});

	$(document).on('ifUnchecked', '.checkDespacho', function(){
		var despachosEliminar = "";
		$('.checkDespacho').each(function(index, val){
			if ($(this).iCheck('data')[0].checked) {
				despachosEliminar += $(this).data('iddespacho')+", ";
			}
		});
		$('#despachosEliminar').val('').val(despachosEliminar);
	});

});;
</script>
</body>
</html>

