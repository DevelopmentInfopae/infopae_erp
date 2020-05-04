<?php
	include '../../header.php';
	$titulo = 'Nómina';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li>
      	<a href="<?php echo $baseUrl . '/modules/nomina'; ?>">Empleados</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div>
  <div class="col-lg-4">
    <div class="title-action">
      <a href="nueva_nomina.php" class="btn btn-primary"><i class="fa fa-plus"></i> Nueva </a>
    </div>
  </div>
</div>

<!-- Table de nominas -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <table id="tablaNomina" class="table table-striped table-hover selectableRows">
            <thead>
              <tr>
                <th style="width: 7.69%">Fecha</th>
                <th style="width: 7.69%">Número</th>
                <th style="width: 7.69%">Mes</th>
                <th style="width: 7.69%">Periodo</th>
                <th style="width: 7.69%">Empleado</th>
                <th style="width: 7.69%">Documento</th>
                <th style="width: 7.69%">Tipo</th>
                <th style="width: 7.69%">Municipio</th>
                <th style="width: 7.69%">Sede</th>
                <th style="width: 7.69%">Complemento</th>
                <th style="width: 7.69%">Total devengado</th>
                <th style="width: 7.69%">Total deducido</th>
                <th style="width: 7.69%">Valor pagado</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <th style="width: 7.69%">Fecha</th>
                <th style="width: 7.69%">Número</th>
                <th style="width: 7.69%">Mes</th>
                <th style="width: 7.69%">Periodo</th>
                <th style="width: 7.69%">Empleado</th>
                <th style="width: 7.69%">Documento</th>
                <th style="width: 7.69%">Tipo</th>
                <th style="width: 7.69%">Municipio</th>
                <th style="width: 7.69%">Sede</th>
                <th style="width: 7.69%">Complemento</th>
                <th style="width: 7.69%">Total devengado</th>
                <th style="width: 7.69%">Total deducido</th>
                <th style="width: 7.69%">Valor pagado</th>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?php echo $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/nomina/js/nomina.js"></script>
<script>
    $(document).ready(function(){
        $('#tablaNomina').DataTable({
          ajax: {
            method: 'post',
            url: 'functions/fn_nomina_listar.php'
          },
          columns: [

            {data : 'Fecha'},
            {data : 'numero'},
            {data : 'mes', "mRender" : function ( data, type, full ) 
                {
                  return meses_texto[data];
                }
            },
            {data : 'periodo'},
            {data : 'nombre'},
            {data : 'Nitcc'},
            {data : 'tipo', "mRender" : function ( data, type, full ) 
                {
                  if (data == 1) {
                    return 'Empleado(a)';
                  } else if (data == 2) {
                    return 'Manipulador(a)';
                  } else if (data == 3) {
                    return 'Contratista';
                  } else if (data == 4) {
                    return 'Transportador';
                  } else  {
                    return data;
                  }
                }
            },
            {data : 'ciudad'},
            {data : 'nom_sede'},
            {data : 'tipo_complem'},
            {data : 'total_devengados'},
            {data : 'tota_deducidos'},
            {data : 'total_pagado'},
          ],
          fnRowCallback: function (nRow, aData, iDisplayIndex)
          {
              // nRow.setAttribute('data-idempleado', aData['idEmpleado']);
              return nRow;
          },
          buttons: [ {extend: 'excel', title: 'Empleados', className: 'btnExportarExcel', exportOptions: { columns: [ 0, 1, 2, 3, 4, 5 ] } } ],
          dom: 'lr<"containerBtn"><"inputFiltro"f>tip<"clear"><"html5buttons"B>',
          order: [ 1, 'asc' ],
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
          preDrawCallback: function() {
            $('#loader').fadeIn();
          }
        }).on('draw', function () { $('#loader').fadeOut(); });

        var botonAcciones = '<div class="dropdown pull-right">'+
                            '<button class="btn btn-primary btn-sm btn-outline" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">'+
                              'Acciones <span class="caret"></span>'+
                            '</button>'+
                            '<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">'+
                              '<li><a tabindex="0" aria-controls="box-table" href="#" onclick="$(\'.btnExportarExcel\').click();"><i class="fa fa-file-excel-o"></i> Exportar </a></li>'+
                              '<ul>'+
                            '</ul>'+
                          '</div>';
        $('.containerBtn').html(botonAcciones);
    });

</script>