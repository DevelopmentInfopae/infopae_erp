$(document).ready(function () {
	$(document).on('click', '#guardarNominaRiesgos', function () { guardarNominaRiesgos(); });
	$(document).on('click', '#actualizarNominaRiesgos', function () { actualizarNominaRiesgos(); });
	$(document).on('click', '.editarNominaRiesgos', function () { editarNominaRiesgos($(this).data('idnominariesgos'),$(this).data('tiponominariesgos'),$(this).data('porcentajenominariesgos')); });
	$(document).on('click', '.confirmarEliminarNominaRiesgos', function () { confirmarEliminarNominaRiesgos($(this).data('idnominariesgos')); });
	$(document).on('click', '#eliminarNominaRiesgos', function () { eliminarNominaRiesgos(); });

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
    buttons: [ {extend: 'excel', title: 'Nómina_riesgos', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1] } } ],
    dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
    order: [ 0, 'asc' ],
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


function guardarNominaRiesgos()
{
    if($('#formCrearNominaRiesgos').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_nomina_riesgos_crear.php",
      data: $('#formCrearNominaRiesgos').serialize(),
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

function editarNominaRiesgos(idnominariesgos, tiponominariesgos, porcentajenominariesgos)
{
  var formulario = '<form id="formActualizarNominaRiesgos">'+
                      '<div class="row">'+
                        '<div class="col-sm-12">'+

                            '<label for="Tipo">Tipo</label>'+
                            '<input type="text" class="form-control" name="tipo" id="tipo" maxlength="20" value="'+ tiponominariesgos +'" required>'+
                            '<input type="hidden" name="idNominaRiesgos" id="idNominaRiesgos" value="'+ idnominariesgos +'">'+

                        '</div>'+
                        '<div class="col-sm-12">'+

                            '<label for="porcentaje">Porcentaje</label>'+
                            '<input type="number" class="form-control" name="porcentaje" id="porcentaje" maxlength="20" value="'+ porcentajenominariesgos +'" required>'+

                        '</div>'+
                      '</div>'+
                    '</form>';
  $('#ventanaFormulario .modal-body p').html(formulario);
  $('#ventanaFormulario'). modal('toggle');
}


function actualizarNominaRiesgos()
{
    if($('#formActualizarNominaRiesgos').valid()){
    $.ajax({
      type: "post",
      url: "functions/fn_nomina_riesgos_actualizar.php",
      data: $('#formActualizarNominaRiesgos').serialize(),
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

function confirmarEliminarNominaRiesgos(idnominariesgos)
{
    $('#idAEliminar').val(idnominariesgos);
    $('#ventanaConfirmar .modal-body p').html('¿Está seguro de eliminar la nómina riesgos?');
    $('#ventanaConfirmar'). modal('toggle');
}

function eliminarNominaRiesgos()
{
    $.ajax({
    type: "POST",
    url: "functions/fn_nomina_riesgos_eliminar.php",
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



