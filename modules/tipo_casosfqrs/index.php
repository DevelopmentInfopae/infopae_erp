<?php
  include '../../header.php';
  $titulo = 'Tipo Caso FQRS'; 
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
	  		<a href="#" class="btn btn-primary" id="crearTipoCasoFqrs"><i class="fa fa-plus"></i> Nuevo </a>
    	</div>
	</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content contentBackground">
					<table id="box-table" class="table table-striped table-hover selectableRows table-responsive">
						<thead>
							<tr>
								<th>Identificador</th>
								<th>Descripción</th>
								<th>Tipo</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$consulta = "SELECT ID, Descripcion, tipo FROM tipo_casosfqrs;";
							$respuesta = $Link->query($consulta); 
							if ($respuesta->num_rows > 0) {
							    while ($dataRespuesta = $respuesta->fetch_assoc()) {
							?>
							<tr>
								<td><?php echo $dataRespuesta['ID']; ?></td>
								<td><?php echo $dataRespuesta['Descripcion'] ?></td>
								<td><?php 
									$tipoString = '';
									if ($dataRespuesta['tipo'] == 'F') {
										$tipoString = 'Felicitaciones';
									}elseif ($dataRespuesta['tipo'] == 'Q') {
										$tipoString = 'Quejas';
									}elseif ($dataRespuesta['tipo'] == 'R') {
										$tipoString = 'Reclamos';
									}elseif ($dataRespuesta['tipo'] == 'S') {
										$tipoString = 'Solicitudes';
									}
									echo $tipoString; ?></td>
								  <td align="left">
                    <div class="btn-group">
                      <div class="dropdown">
                        <button class="btn btn-primary btn-sm" id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Acciones <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right" aria-labelledby="dLabel">
                          <li><a href="#" class="editarTipoCasoFqrs" data-idtipocasofqrs = <?php echo $dataRespuesta['ID']; ?> ><i class="fas fa-pencil-alt"></i> Editar</a></li>
                          <li><a data-toggle="modal" data-target="#modalEliminarTipoCasoFqrs"  data-idtipocasofqrs = <?php echo $dataRespuesta['ID']; ?> ><span class="fa fa-trash"></span>  Eliminar</a></li>
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
								<th>Identificador</th>
								<th>Descripción</th>
								<th>Tipo</th>
								<th>Acciones</th>
							</tr>
						</tfoot>
					</table> <!-- table -->
				</div> <!-- ibox-content -->
			</div> <!-- float-e-margins -->
		</div> <!-- col-lg-12 -->
	</div> <!-- row -->
</div> <!-- fadeInRight -->

<div id="contenedor_crear_tipo_caso_fqrs"></div>
<div id="contenedor_editar_tipo_caso_fqrs"></div>

<!-- modal eliminar -->
<input type="hidden" name="inputBaseUrl" id="inputBaseUrl" value="<?php echo $baseUrl; ?>">

<div class="modal inmodal fade" id="modalEliminarTipoCasoFqrs" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <span>¿Está seguro de borrar el tipo de caso FQRS?</span>
         <input type="hidden" name="idTipoCasoFqrs" id="idTipoCasoFqrs">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fas fa-times"></i> No</button>
       <button type="button" class="btn btn-primary btn-sm" onclick="eliminarTipoCasoFqrs()"><i class="fa fa-check"></i> Si </button>
     </div>
   </div>
 </div>
</div>

<?php include '../../footer.php'; ?>

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
	$(document).ready(function(){
		$(document).on('click', '#crearTipoCasoFqrs', function(){abrir_modal_crear_tipo_caso_fqrs(); });
		$(document).on('click', '#guardar_tipo_caso_fqrs', function() { guardar_tipo_caso_fqrs(); });
    $(document).on('click', '.editarTipoCasoFqrs', function(){abrir_modal_editar_tipo_caso_fqrs($(this).data('idtipocasofqrs')); });
    $(document).on('click', '#actualizar_tipo_caso_fqrs', function(){actualizar_tipo_caso_fqrs();})

		$('#box-table').DataTable({
		buttons: [ {extend: 'excel', title: 'Tipo Caso FQRS', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2] } } ],
    	dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    	order: [[ 0, 'asc'],[2,'asc']],
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
    	pageLength: 10,
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

  		toastr.options = {
		    "closeButton": true,
		    "debug": false,
		    "progressBar": true,
		    "preventDuplicates": false,
		    "positionClass": "toast-top-right",
		    "onclick": null,
		    "showDuration": "400",
		    "hideDuration": "1000",
		    "timeOut": "2000",
		    "extendedTimeOut": "1000",
		    "showEasing": "swing",
		    "hideEasing": "linear",
		    "showMethod": "fadeIn",
		    "hideMethod": "fadeOut"
		  }  
	});

function abrir_modal_crear_tipo_caso_fqrs(){
	$('#contenedor_crear_tipo_caso_fqrs').load($('#inputBaseUrl').val() +'/modules/tipo_casosfqrs/add.php');
}

function guardar_tipo_caso_fqrs(){
	if($('#formCrearTipoCasoFqrs').valid()){
    $('#loader').fadeIn();
  		$.ajax({
    		type: "post",
    		url: "functions/fn_tipo_casoFqrs_crear.php",
    		data: $('#formCrearTipoCasoFqrs').serialize(),
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
                  	window.open('index.php', '_self');                                    
              			}
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

function abrir_modal_editar_tipo_caso_fqrs(idTipoCaso){
  $('#contenedor_editar_tipo_caso_fqrs').load($('#inputBaseUrl').val() +'/modules/tipo_casosfqrs/edit.php?idTipoCaso='+idTipoCaso);
}	

function actualizar_tipo_caso_fqrs()
{
  if($('#formActualizarTipoCasoFqrs').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_tipo_casoFqrs_editar.php",
      data: $('#formActualizarTipoCasoFqrs').serialize(),
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

$('#modalEliminarTipoCasoFqrs').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idtipocasofqrs = button.data('idtipocasofqrs');
      // console.log(idparametromanipuladora);
      $('#idTipoCasoFqrs').val(idtipocasofqrs);
});

function eliminarTipoCasoFqrs(){
  $('#modalEliminarTipoCasoFqrs').modal('hide');
  $('#loader').fadeIn();
  var id = $('#idTipoCasoFqrs').val();
  $.ajax({
      type: "POST",
      url: "functions/fn_tipo_casoFqrs_eliminar.php",
      data: {"id" : id },
      dataType: 'json',
      success: function(data){
      console.log(data);
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