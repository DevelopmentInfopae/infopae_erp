<?php
  include '../../header.php';
  $titulo = 'Parámetros Manipuladoras'; 
?>

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
	  		<a href="#" class="btn btn-primary" id="crearParametrosManipuladoras"><i class="fa fa-plus"></i> Nuevo </a>
    	</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<table id="box-table" class="table table-striped table-hover selectableRows">
						<thead>
			            	<tr>
                    			<th>Tipo Complemento</th>
                    			<th>Cantidad Manipuladoras</th>
                    			<th>Límite Inferior</th>
                   				<th>Límite superior</th>
                  				<th align="center">Acciones</th>
                  			</tr>
			          </thead>
			          <tbody>
			          	<?php 
			          		$consultaTabla = "SELECT ID, tipo_complem, cant_manipuladora, limite_inferior, limite_superior FROM parametros_manipuladoras ORDER BY tipo_complem;";
			          		$respuesta = $Link->query($consultaTabla);
			          		if ($respuesta->num_rows > 0) {
			          			while ($dataRespueta = $respuesta->fetch_assoc()) {
			          	?>
			          		<tr>
			          			<td><?php echo $dataRespueta['tipo_complem']; ?></td>
			          			<td><?php echo $dataRespueta['cant_manipuladora']; ?></td>
			          			<td><?php echo $dataRespueta['limite_inferior']; ?></td>
			          			<td><?php echo $dataRespueta['limite_superior']; ?></td>
			          			<td align="left">
                      				<div class="btn-group">
                        				<div class="dropdown">
                         	 				<button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Acciones <span class="caret"></span>
                          					</button>
                          					<ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                            					<li><a href="#" class="editarParametrosManipuladoras" data-idparametromanipuladora = <?php echo $dataRespueta['ID']; ?> ><i class="fas fa-pencil-alt"></i> Editar</a></li>
                            					<li><a data-toggle="modal" data-target="#modalEliminarParametrosManipuladoras"  data-idparametromanipuladora = <?php echo $dataRespueta['ID']; ?> ><span class="fa fa-trash"></span>  Eliminar</a></li>
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
			          	    	<th>Tipo Complemento</th>
                    			<th>Cantidad Manipuladoras</th>
                    			<th>Límite Inferior</th>
                   				<th>Límite superior</th>
                  				<th align="center">Acciones</th>
                  			</tr>
			          </tfoot>
					</table> <!-- box-table -->
				</div> <!-- contentBackground -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- fadeInRight -->

<!-- Button trigger modal -->
<input type="hidden" name="inputBaseUrl" id="inputBaseUrl" value="<?php echo $baseUrl; ?>">

<div class="modal inmodal fade" id="modalEliminarParametrosManipuladoras" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar el parámetro manipuladora?</span>
         <input type="hidden" name="idParametroManipuladoraEliminar" id="idParametroManipuladoraEliminar">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarParametroManipuladora()"><i class="fa fa-check"></i> Si </button>
     </div>
   </div>
 </div>
</div>

<div id="contenedor_crear_parametros_manipuladoras"></div>
<div id="contenedor_editar_parametros_manipuladoras"></div>


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
    jQuery.extend(jQuery.validator.messages, {step: "Por favor ingresa un numero entero", required: "Este campo es obligatorio.", remote: "Por favor, rellena este campo.", email: "Por favor, escribe una dirección de correo válida", url: "Por favor, escribe una URL válida.", date: "Por favor, escribe una fecha válida.", dateISO: "Por favor, escribe una fecha (ISO) válida.", number: "Por favor, escribe un número entero válido.", digits: "Por favor, escribe sólo dígitos.", creditcard: "Por favor, escribe un número de tarjeta válido.", equalTo: "Por favor, escribe el mismo valor de nuevo.", accept: "Por favor, escribe un valor con una extensión aceptada.", maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."), minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."), rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."), range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."), max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."), min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.") });
</script>

<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click', '#crearParametrosManipuladoras', function() { abrir_modal_crear_parametros_manipuladoras(); });
		$(document).on('click', '#guardar_parametros_manipuladoras', function() { guardar_parametros_manipuladoras(); });
    $(document).on('click', '.editarParametrosManipuladoras', function() { abrir_modal_editar_parametros_manipuladoras($(this).data('idparametromanipuladora')); });
    $(document).on('click', '#editar_parametros_manipuladoras', function() { actualizarParametrosManipuladora(); });


		$('#box-table').DataTable({
			buttons: [ {extend: 'excel', title: 'Parámetros Manipuladoras', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3] } } ],
    		dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    		order: [[ 0, 'asc'],[ 1, 'asc' ]],
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
    		pageLength: 25,
    		responsive: true,
		});

		var botonAcciones = '<div class="dropdown pull-right">'+
                      			'<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                        			'Acciones <span class="caret"></span>'+
                      			'</button>'+
                      			'<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                        			'<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                      			'</ul>'+
                    		'</div>';
  		$('.containerBtn').html(botonAcciones);
});

function abrir_modal_crear_parametros_manipuladoras(){
  	$('#contenedor_crear_parametros_manipuladoras').load($('#inputBaseUrl').val() +'/modules/parametros_manipuladoras/add.php');
}

function guardar_parametros_manipuladoras(){
	if($('#formCrearParametrosManipuladoras').valid()){
  		$.ajax({
    		type: "post",
    		url: "functions/fn_parametros_manipuladoras_crear.php",
    		data: $('#formCrearParametrosManipuladoras').serialize(),
    		dataType: 'json',
    		beforeSend: function(){ $('#loader').fadeIn(); },
    		success: function(data) { 
            console.log(data);
      			if(data.estado == 1){
        			Command: toastr.success(
        			data.mensaje,
        			"Creado",
          				{
          				onHidden : function(){
            			$('#loader').fadeOut();
            			window.open('index.php', '_self');}
          				}
        			);
      			}
      			else
      			{
        			Command: toastr.warning(
        			data.mensaje,
        			"Error al crear",
          				{
            			onHidden : function(){ $('#loader').fadeOut(); }
          				}
        			);
      			}
    		},
    		error: function(data) {
      			console.log(data.responseText);
      			Command: toastr.error(
      			'Al parecer existe un error en el proceso',
      			"Error al crear",
        			{
          			onHidden : function(){ $('#loader').fadeOut(); }
        			}
     			);
    		}
    	});
  	}
}

function abrir_modal_editar_parametros_manipuladoras(valor_id)
{
    $('#contenedor_editar_parametros_manipuladoras').load($('#inputBaseUrl').val() +'/modules/parametros_manipuladoras/edit.php?idParametroManipuladora='+valor_id);
}

function actualizarParametrosManipuladora()
{
  if($('#formActualizarParametrosManipuladora').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_parametros_manipuladora_editar.php",
      data: $('#formActualizarParametrosManipuladora').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
        console.log(data);
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Actualizado",
            {
              onHidden : function(){window.open('index.php', '_self');}
            }
          );
        }
        else
        {
          Command: toastr.warning(
            data.mensaje,
            "Error al actualizar",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
      },
      error: function(data)
      {
        console.log(data.responseText);
        Command: toastr.error(
          'Al parecer existe un error en el proceso',
          "Error al actualizar",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

$('#modalEliminarParametrosManipuladoras').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idparametromanipuladora = button.data('idparametromanipuladora');
      // console.log(idparametromanipuladora);
      $('#idParametroManipuladoraEliminar').val(idparametromanipuladora);
});

function eliminarParametroManipuladora(){
  $('#modalEliminarParametrosManipuladoras').modal('hide');
  $('#loader').fadeIn();
  var id = $('#idParametroManipuladoraEliminar').val();
  $.ajax({
      type: "POST",
      url: "functions/fn_parametros_manipuladora_eliminar.php",
      data: {"id" : id },
      dataType: 'json',
      success: function(data){
      // console.log(data);
        if(data.estado == 1){
            Command: toastr.success(
            data.mensaje,
            "Eliminado",
            {
              onHidden : function(){
                  $('#loader').fadeOut();
                  window.open('index.php', '_self');                                    
              }
            }
          );
        }
        else
        {
        Command: toastr.warning(
            data.mensaje,
            "Error al eliminar",
            {
              onHidden : function(){ $('#loader').fadeOut(); }
            }
          );
        }
    },
    error: function(data)
      {
          console.log(data.responseText);
          Command: toastr.error(
            'Al parecer existe un error en el proceso',
            "Error al eliminar",
          {
             onHidden : function(){ $('#loader').fadeOut(); }
          }
          );
      }
  });
}

</script>