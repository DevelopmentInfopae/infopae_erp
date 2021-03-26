$(document).ready(function () {
	CargarTablas();
	$(document).on('click', '#crearValores', function () { crearValores(); });
	$(document).on('click', '#guardarManipuladoraValoresNomina', function () { guardarManipuladoraValoresNomina(false); });
	$(document).on('click', '#guardarManipuladoraValoresNominaContinuar', function () { guardarManipuladoraValoresNomina(true); });
	$(document).on('click', '.editarValorManipuladoraNomina', function () {editarValorManipuladoraNomina($(this).data('idvalormanipuladora')); });
	$(document).on('click', '#actualizarManipuladoraValorNomina', function () { actualizarManipuladoraValorNomina(false); });
	$(document).on('click', '#actualizarManipuladoraValorNominaContinuar', function () { actualizarManipuladoraValorNomina(true); });
	

	// Configuración inicial del plugin toastr.
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

function crearValores()
{
	window.open('manipuladoras_valores_nomina_crear.php', '_self');
}

function editarValorManipuladoraNomina(idValorManipuladora)
{
	// console.log(idValorManipuladora);
  	$('#formEditarManipuladoraValorNomina #idValorManipuladora').val(idValorManipuladora);
	$('#formEditarManipuladoraValorNomina').submit();
}

function CargarTablas(){
	$('#loader').fadeIn();
	$.ajax({
		url: 'functions/fn_tabla_manipuladoras_valores_nomina.php',
		type: 'POST',
		success:function(data){
			// console.log(data);
			data = JSON.parse(data);
					$('#tHeadValores').html(data['thead']);
					$('#tBodyValores').html(data['tbody']);
					$('#tFootValores').html(data['tfoot']);
					dataset1 = $('#box-table').DataTable({
					    order: [ 0, 'asc' ],
					    pageLength: 25,
					    responsive: true,
					    dom : 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
					    buttons : [ {extend: 'excel', title: 'Manipuladoras valores nomina', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } }],
					    oLanguage: {
					      sLengthMenu: 'Mostrando _MENU_ registros por página',
					      sZeroRecords: 'No se encontraron registros',
					      sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
					      sInfoFiltered: '(Filtrado desde _MAX_ registros)',
					      sSearch:         'Buscar: ',
					      sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
					      oPaginate:{
					        sFirst:    'Primero',
					        sLast:     'Último',
					        sNext:     'Siguiente',
					        sPrevious: 'Anterior'
					      }
					    }
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
			$('#loader').fadeOut();	    
		}
	})
}

function guardarManipuladoraValoresNomina(continuar)
{
	if($('#formCrearManipuladoraValorNomina').valid()){
    $.ajax({
      	type: "post",
      	url: "functions/fn_manipuladoras_valores_nomina_crear.php",
      	data: $('#formCrearManipuladoraValorNomina').serialize(),
      	dataType: 'json',
      	beforeSend: function(){ $('#loader').fadeIn(); },
      	success: function(data) { 
	      	// console.log(data);
	        if(data.estado == 1){
	          Command: toastr.success(
	            data.mensaje,
	            "Creado",
	            {
	            onHidden : function(){
	                if(continuar){
	                  	$("#formCrearManipuladoraValorNomina")[0].reset();
	                  	$('#loader').fadeOut();
	                  	window.open('index.php', '_self');
	                }else{
	                  	window.open('index.php', '_self');
	                }
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

function actualizarManipuladoraValorNomina(continuar)
{
	if($('#formActualizarManipuladoraValorNomina').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_manipuladoras_valores_nomina_actualizar.php",
      data: $('#formActualizarManipuladoraValorNomina').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data){
      	// console.log(data);
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Editado",
            {
              onHidden : function(){
                if(continuar){
                  $('#loader').fadeOut();
                  window.open('index.php', '_self');
                }else{
                  window.open('index.php', '_self');
                }
              }
            }
          );
        }
        else
        {
          Command: toastr.warning(
            data.mensaje,
            "Error al editar",
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
          "Error al crear",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

$('#modalEliminarValorManipuladora').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idvalormanipuladora = button.data('idvalormanipuladora');
      $('#idValorManipuladora').val(idvalormanipuladora);
});

function eliminarManipuladoraValorNomina(){
	$('#modalEliminarValorManipuladora').modal('hide');
	$('#loader').fadeIn();
	var id = $('#idValorManipuladora').val();
 	$.ajax({
  		type: "POST",
  		url: "functions/fn_menus_eliminar_manipuladora_valor_nomina.php",
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
	          "Error al crear",
	        {
	           onHidden : function(){ $('#loader').fadeOut(); }
	        }
	        );
	    }
	});
}

