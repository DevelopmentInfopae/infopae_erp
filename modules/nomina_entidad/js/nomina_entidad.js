$(document).ready(function () {
	$(document).on('click', '#guardarNominaEntidad', function () { guardarNominaEntidad(); });
	$(document).on('click', '#actualizarNominaEntidad', function () { actualizarNominaEntidad(); });
	$(document).on('click', '.editarNominaEntidad', function () { editarNominaEntidad($(this).data('idnominaentidad'),$(this).data('tiponominaentidad'),$(this).data('entidadnominaentidad')); });
	$(document).on('click', '.confirmarEliminarNominaEntidad', function () { confirmarEliminarNominaEntidad($(this).data('idnominaentidad')); });
	$(document).on('click', '#eliminarNominaEntidad', function () { eliminarNominaEntidad(); });

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

$('#box-table').DataTable({
    buttons: [ {extend: 'excel', title: 'Nómina_Entidad', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } } ],
    dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    order: [[ 0, 'desc' ],[1,'asc']],
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


function guardarNominaEntidad()
{
	if($('#formCrearNominaEntidad').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_nomina_entidad_crear.php",
      data: $('#formCrearNominaEntidad').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data)
      {
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Creado",
            {
              onHidden : function(){ window.open('index.php', '_self'); }
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
      error: function(data)
      { console.log(data.responseText);
      	Command: toastr.error(
          "Al parecer existe un problema en el proceso.",
          "Error al crear",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function editarNominaEntidad(idnominaentidad, tiponominaentidad, entidadnominaentidad)
{
  var formulario = '<form id="formActualizarNominaEntidad">'+
                      '<div class="row">'+
                        '<div class="col-sm-12">'+

                            '<label for="Tipo">Tipo</label>'+
                            '<select class="form-control" name="tipo" id="tipo" required>'+
								              '<option value="1" '+ (tiponominaentidad == 1 ? "selected" : "") +'>Entidad Promotora de Salud (EPS)</option>'+	
								              '<option value="2" '+ (tiponominaentidad == 2 ? "selected" : "") +'>Administradoras de Fondos de Pensiones (AFP)</option>'+
								              '<option value="3" '+ (tiponominaentidad == 3 ? "selected" : "") +'>Administradoras de Riesgos Laborales (ARL)</option>'+
							 	              '<option value="4" '+ (tiponominaentidad == 4 ? "selected" : "") +'>Caja de compensación familiar (CAJA)</option>'+
							               '</select>'+
                            '<input type="hidden" name="idNominaEntidad" id="idNominaEntidad" value="'+ idnominaentidad +'">'+

                        '</div>'+
                        '<div class="col-sm-12">'+

                            '<label for="entidad">Entidad</label>'+
                            '<input type="text" class="form-control" name="entidad" id="entidad" value="'+ entidadnominaentidad +'" required>'+

                        '</div>'+
                      '</div>'+
                    '</form>';
  $('#ventanaFormulario .modal-body p').html(formulario);
  $('#ventanaFormulario'). modal('toggle');
}


function actualizarNominaEntidad()
{
	if($('#formActualizarNominaEntidad').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_nomina_entidad_actualizar.php",
      data: $('#formActualizarNominaEntidad').serialize(),
      dataType: 'json',
      beforeSend: function(){ $('#loader').fadeIn(); },
      success: function(data)
      {
        if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Actualizado",
            {
              onHidden : function(){ window.open('index.php', '_self'); }
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
      { console.log(data.responseText);
      	Command: toastr.error(
          'Al parecer existe un problema en el proceso.',
          "Error al actualizar",
          {
            onHidden : function(){ $('#loader').fadeOut(); }
          }
        );
      }
    });
  }
}

function confirmarEliminarNominaEntidad(idnominaentidad)
{
	$('#idAEliminar').val(idnominaentidad);
	$('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar la nómina entidad?');
	$('#ventanaConfirmar').	modal('toggle');
}

function eliminarNominaEntidad()
{
	$.ajax({
    type: "POST",
    url: "functions/fn_nomina_entidad_eliminar.php",
    data: { id: $('#idAEliminar').val() },
    dataType: 'json',
    beforeSend: function(){ $('#loader').fadeIn(); },
    success: function(data)
    {
      if(data.estado == 1){
          Command: toastr.success(
            data.mensaje,
            "Eliminado",
            {
              onHidden : function(){
                window.open('index.php', '_self');
              }
            }
          );
        } else {
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
    { console.log(data);
    	Command: toastr.error(
        data.responseText,
        "Error",
        {
          onHidden : function(){ $('#loader').fadeOut(); }
        }
      );
    }
  });
}
