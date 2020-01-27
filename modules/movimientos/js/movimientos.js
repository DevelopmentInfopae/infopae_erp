var cantidadDetallados = 0;

$(document).ready( function () {


  var mes = $('#mesi').val();
  var mesText = $("#mesi option[value='"+mes+"']").text()
  $('#mesfText').val(mesText);
  $('#mesf').val(mes);



  dataset1 = $('#box-table-movimientos').DataTable({
          order: [[ 1, 'asc' ]],
          oLanguage: {
            sLengthMenu: 'Mostrando _MENU_ registros por página',
            sZeroRecords: 'No se encontraron registros',
            sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
            sInfoFiltered: '(Filtrado desde _MAX_ registros)',
            sSearch:         'Buscar: ',
        oPaginate: {
          sFirst:    'Primero',
          sLast:     'Último',
          sNext:     'Siguiente',
          sPrevious: 'Anterior'
        }
    }
    });
  // Fin Funcionamiento del report

  var anchoTabla = $('#box-table-movimientos').width();
  var anchoTabla = anchoTabla-8;
  $('.fg-toolbar').css({ 'width': anchoTabla });
  $( window ).resize(function(){ var anchoTabla = $('#box-table-movimientos').width(); var anchoTabla = anchoTabla-8; $('.fg-toolbar').css({ 'width': anchoTabla }); });

  //Cuando se hace click a un movimiento
  $('#box-table-movimientos tbody tr').click(function(){
    console.log('Click en un movimiento');
    var indice = $(this).find( ".indice" ).val();
    var estado = $(this).find( ".estado" ).val();
    console.log("Indice = "+indice+"  estado = "+estado);
    /*
      Convención para los estados de un movimiento
      1 = Pendiente
      2 = Aprobado
      3 = Anulado
    */
    if(estado == 1){
        $('#formEditarId').val(indice);
        $('#formEditarEstado').val(estado);
        var aux = $('#mesi').val();
        aux = aux + $('#annoi').val();
        console.log(aux);
        $('#formEditarTabla').val(aux);
        $('#formEditar').submit();
    }

    // Si el estado es igual a 2 significa que esta aprobado, por lo tanto debo poder ver pero no editar.
    else if(estado == 2 || estado == 3){
        $('#formVerId').val(indice);
        $('#formVerEstado').val(estado);
        var aux = $('#mesi').val();
        aux = aux + $('#annoi').val();
        console.log(aux);
        $('#formVerTabla').val(aux);
        $('#formVer').submit();
    }
  });

});



function mesFinal(){
  var mes = $('#mesi').val();
  var mesText = $("#mesi option[value='"+mes+"']").text()
  $('#mesfText').val(mesText);
  $('#mesf').val(mes);
}
