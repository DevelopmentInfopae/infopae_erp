<?php
require_once '../../header.php';
$titulo = 'Informes de alimentos.';

$periodoActual = $_SESSION['periodoActual'];
// $meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");
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
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
        <?php
        $opciones ="";
        $consultaTablas = "SELECT
                            table_name AS tabla
                          FROM
                            information_schema.tables
                          WHERE
                            table_schema = DATABASE() AND table_name like 'insumosmovdet%'";
        $resultadoTablas = $Link->query($consultaTablas);
        if ($resultadoTablas->num_rows > 0) {
          $cnt=0;
          while ($tabla = $resultadoTablas->fetch_assoc()) {
            $mes = str_replace("insumosmovdet", "", $tabla['tabla']);
            $mes = str_replace($_SESSION['periodoActual'], "", $mes);

            $nomMes = $meses[$mes];
            $opciones.= '<option value="'.$mes.'">'.$nomMes.'</option>';

            if ($cnt == 0) {
              $cnt++;
              $mesTablaInicio = $mes;
            }
          }
        } else {
          echo "<script>location.href='$baseUrl/modules/insumos/despacho_insumo.php';</script>";
        }
        ?>
          <form class="form row" id="formBuscar" method="POST">
            <div id="fechaDiasDespachos">
              <div class="form-group col-sm-3">
                <label>Desde</label>
                <div class="compositeDate">
                  <div class="nopadding">
                    <select name="mes_inicio" id="mes_inicio" class="form-control ">
                    <?php echo $opciones; ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="form-group col-sm-3">
                <label>Hasta</label>
                <div class="compositeDate">
                  <div class="nopadding">
                    <input type="text" id="nomMesFin" value="Espere..." class="form-control" readonly>
                    <input type="hidden" name="mes_fin" id="mes_fin" value="01">
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group col-sm-3">
              <label>Tipo documento</label>
              <select name="tipo_documento" id="tipo_documento" class="form-control">
                <option value="">Seleccione uno</option>
                <?php
                $consultarTipoDocumento = "SELECT * FROM tipomovimiento";
                $resultadoTipoDocumento = $Link->query($consultarTipoDocumento);
                if ($resultadoTipoDocumento->num_rows > 0) {
                  while ($tdoc = $resultadoTipoDocumento->fetch_assoc()) { ?>
                    <option value="<?php echo $tdoc['Movimiento'] ?>"><?php echo $tdoc['Movimiento'] ?></option>
                  <?php }
                }
                ?>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Proveedor/Responsable</label>
              <select name="proveedor" id="proveedor" class="form-control">
                <option value="">Seleccione uno</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Rutas</label>
              <select name="ruta_desp" id="ruta_desp" class="form-control">
                <option value="">Seleccione una</option>
                <?php
                $consultaRutas = "SELECT * FROM rutas";
                $resultadoRutas = $Link->query($consultaRutas);
                if ($resultadoRutas->num_rows > 0) {
                  while ($ruta = $resultadoRutas->fetch_assoc()) { ?>
                    <option value="<?php echo $ruta['ID']; ?>"><?php echo $ruta['Nombre']; ?></option>
                  <?php }
                }
                ?>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Municipio</label>
              <select class="form-control" name="municipio" id="municipio_desp">
                <option value="">Seleccione...</option>
                <?php
                  $consultarMunicipios = "SELECT
                                            ubicacion.CodigoDANE, ubicacion.Ciudad
                                          FROM insumosmov".$mes.$_SESSION['periodoActual']." AS denc
                                          INNER JOIN sedes".$_SESSION['periodoActual']." AS sede ON sede.cod_sede = denc.BodegaDestino
                                          INNER JOIN ubicacion ON ubicacion.CodigoDANE = sede.cod_mun_sede
                                          GROUP BY ubicacion.Ciudad;";
                  $resultadoMunicipios = $Link->query($consultarMunicipios);
                  if ($resultadoMunicipios->num_rows > 0) {
                    while ($municipios = $resultadoMunicipios->fetch_assoc()) { ?>
                      <option value="<?php echo $municipios['CodigoDANE'] ?>"><?php echo $municipios['Ciudad'] ?></option>
                <?php
                    }
                  }
                ?>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Institución</label>
              <select name="institucion_desp" id="institucion_desp" class="form-control">
                <option value="">Seleccione uno</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Sede</label>
              <select name="sede_desp" id="sede_desp" class="form-control">
                <option value="">Seleccione una</option>
              </select>
            </div>
            <input type="hidden" name="buscar" value="1">
          </form>
          <div class="col-sm-12">
            <button class="btn btn-primary" onclick="$('#formBuscar').submit();" id="btnBuscar"> <span class="fa fa-search"></span>  Buscar</button>
            <?php if (isset($_POST['buscar'])): ?>
              <button class="btn btn-primary" onclick="location.href='insumos_proveedor.php';" id="btnBuscar"> <span class="fa fa-times"></span>  Limpiar búsqueda</button>
            <?php endif ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <div class="table-responsive">
            <input type="hidden" name="tablaMes" id="tablaMes" value="<?php echo $mesTablaInicio; ?>">
            <table class="table" id="tablaTrazabilidad">
              <thead>
                <tr>
                  <th style="width: 8.33%;">Cod Alimento</th>
                  <th style="width: 17.65%;">Alimento</th>
                  <th style="width: 8.33%;">Cant Requerida</th>
                  <th style="width: 8.33%;">Cant Presentación</th>
                  <th style="width: 8.33%;">Cant Umedida 1</th>
                  <th style="width: 6%;">U medida</th>
                  <th style="width: 8.33%;">Cant Umedida 2</th>
                  <th style="width: 6%;">U medida</th>
                  <th style="width: 8.33%;">Cant Umedida 3</th>
                  <th style="width: 6%;">U medida</th>
                  <th style="width: 8.33%;">Cant Umedida 4</th>
                  <th style="width: 6%;">U medida</th>
                </tr>
              </thead>
              <tbody id="tBodyTrazabilidad">

              </tbody>
              <tfoot>
                <tr>
                  <th style="width: 8.33%;">Cod Alimento</th>
                  <th style="width: 17.65%;">Alimento</th>
                  <th style="width: 8.33%;">Cant Requerida</th>
                  <th style="width: 8.33%;">Cant Presentación</th>
                  <th style="width: 8.33%;">Cant Umedida 1</th>
                  <th style="width: 6%;">U medida</th>
                  <th style="width: 8.33%;">Cant Umedida 2</th>
                  <th style="width: 6%;">U medida</th>
                  <th style="width: 8.33%;">Cant Umedida 3</th>
                  <th style="width: 6%;">U medida</th>
                  <th style="width: 8.33%;">Cant Umedida 4</th>
                  <th style="width: 6%;">U medida</th>
                </tr>
              </tfoot>
            </table>
          </div>
          <?php
          if (!isset($_POST['buscar'])) { //Si no hay filtrado
            $numtabla = $mesTablaInicio.$_SESSION['periodoActual'];

            $consulta = "SELECT
                          pmovdet.CodigoProducto, pmovdet.Descripcion, SUM(pmovdet.Cantidad) AS Cantidad, pmovdet.CantUMedida, (SUM(pmovdet.CanTotalPresentacion)*1000) AS CantidadPresentacion, pmovdet.Umedida, SUM(pmovdet.CantU2) AS CantU2, SUM(pmovdet.CantU3) AS CantU3, SUM(pmovdet.CantU4) AS CantU4, SUM(pmovdet.CantU5) AS CantU5, P.NombreUnidad2 as Umedida2, P.NombreUnidad3 as Umedida3, P.NombreUnidad4 as Umedida4, P.NombreUnidad5 as Umedida5
                        FROM insumosmov$numtabla AS pmov
                        INNER JOIN insumosmovdet$numtabla as pmovdet ON pmovdet.Numero = pmov.Numero
                        INNER JOIN productos$periodoActual as P ON P.Codigo = pmovdet.CodigoProducto
                        GROUP BY pmovdet.CodigoProducto ORDER BY pmovdet.Descripcion ASC
                        LIMIT 2000;";
          } else if (isset($_POST['buscar'])) { //Si hay filtrado

            $numtabla = $_POST['mes_inicio'].$_SESSION['periodoActual']; //Número MesAño según mes escogido
            $condiciones = ""; //Donde se almacenan las condiciones según parámetros
            $inners="";//Donde se almacenan los INNERS necesarios para traer datos externos.

            if (isset($_POST['ruta_desp']) && $_POST['ruta_desp'] != "") { //SI SE ESCOGIÓ POR RUTA
              $consultaRutas = "SELECT *, inst.nom_inst, ubicacion.Ciudad
                                FROM rutasedes
                                INNER JOIN sedes".$_SESSION['periodoActual']." AS sede ON sede.cod_sede = rutasedes.cod_Sede
                                INNER JOIN instituciones AS inst ON inst.codigo_inst = sede.cod_inst
                                INNER JOIN ubicacion ON ubicacion.codigoDANE = inst.cod_mun
                                INNER JOIN parametros ON ubicacion.codigoDANE LIKE CONCAT( parametros.CodDepartamento, '%' )
                                AND rutasedes.IDRUTA = '".$_POST['ruta_desp']."' ORDER BY ubicacion.Ciudad";

              $resultadoRutasSedes = $Link->query($consultaRutas);
              if ($resultadoRutasSedes->num_rows > 0) {
                $condiciones.="AND (";
                while ($rutaSedes = $resultadoRutasSedes->fetch_assoc()) {
                  $condiciones.=" pmov.BodegaDestino = '".$rutaSedes['cod_sede']."' OR ";
                }

                $condiciones = trim($condiciones, ' OR');
                $condiciones.=")";
              }
            } else { //SI NO SE ESCOGIÓ RUTA
              if (isset($_POST['municipio']) && $_POST['municipio'] != "") { //Si el usuario especifica municipio, busca las sedes relacionadas que sean del municipio escogido
                $inners.=" INNER JOIN sedes".$_SESSION['periodoActual']." as sede ON sede.cod_sede = pmov.BodegaDestino ";
                $condiciones.=" AND sede.cod_mun_sede = '".$_POST['municipio']."' ";

                if (isset($_POST['institucion_desp']) && $_POST['institucion_desp'] != "") {
                  $inners.=" INNER JOIN instituciones as inst ON inst.codigo_inst = sede.cod_inst";
                  $condiciones.=" AND inst.codigo_inst = '".$_POST['institucion_desp']."' ";
                }

                if (isset($_POST['sede_desp']) && $_POST['sede_desp'] != "") {
                    $condiciones.=" AND sede.cod_sede = '".$_POST['sede_desp']."' ";
                }
              }
            }

            if (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] != "") { //Si el tipo de documento se especificó
              if ($_POST['proveedor'] != "") { //Si el proveedor se especificó, busca según las bodegas relacionadas
                $condiciones.=" AND pmov.Tipo = '".$_POST['tipo_documento']."' AND pmov.Nitcc = '".$_POST['proveedor']."' ";
              } else { //Si no especificó, trae todos los registros con el tipo de documento escogido
                $condiciones.=" AND pmov.Tipo = '".$_POST['tipo_documento']."' ";
              }
            }

            $consulta = "SELECT
                          pmovdet.CodigoProducto, pmovdet.Descripcion, SUM(pmovdet.Cantidad) AS Cantidad, pmovdet.CantUMedida, (SUM(pmovdet.CanTotalPresentacion)*1000) AS CantidadPresentacion, pmovdet.Umedida, SUM(pmovdet.CantU2) AS CantU2, SUM(pmovdet.CantU3) AS CantU3, SUM(pmovdet.CantU4) AS CantU4, SUM(pmovdet.CantU5) AS CantU5, P.NombreUnidad2 as Umedida2, P.NombreUnidad3 as Umedida3, P.NombreUnidad4 as Umedida4, P.NombreUnidad5 as Umedida5
                        FROM insumosmov$numtabla AS pmov
                        INNER JOIN insumosmovdet$numtabla as pmovdet ON pmovdet.Numero = pmov.Numero
                        INNER JOIN productos$periodoActual as P ON P.Codigo = pmovdet.CodigoProducto
                        $inners $condiciones
                        GROUP BY pmovdet.CodigoProducto ORDER BY pmovdet.Descripcion ASC
                        LIMIT 2000;";
          }

          echo $consulta;
          ?>
          <input type="hidden" name="consulta" id="consulta" value="<?php echo $consulta; ?>">
        </div>
      </div>
    </div>
  </div>
</div>

<form method="POST" action="editar_despacho_insumo.php" id="editar_despacho">
  <input type="hidden" name="id_despacho" id="id_despacho">
  <input type="hidden" name="mesTabla" id="mesTabla">
</form>

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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>

<!-- Section Scripts -->
<!-- <script src="<?php echo $baseUrl; ?>/modules/insumos/js/despachos.js"></script> -->
<script type="text/javascript">
  console.log('Aplicando Data Table');
  // dataset1 = $('#tablaTrazabilidad').DataTable({
  //   ajax: {
  //     method: 'POST',
  //     url: 'functions/fn_insumos_obtener_datos_tabla.php',
  //     data:{
  //       consulta: $('#consulta').val()
  //     }
  //   },
  //   columns:[
  //     { data: 'CodigoProducto'},
  //     { data: 'Descripcion'},
  //     { data: 'Cantidad'},
  //     { data: 'CantidadPresentacion'},
  //     { data: 'CantU2'},
  //     { data: 'Umedida2'},
  //     { data: 'CantU3'},
  //     { data: 'Umedida3'},
  //     { data: 'CantU4'},
  //     { data: 'Umedida4'},
  //     { data: 'CantU5'},
  //     { data: 'Umedida5'},
  //   ],
  //   pageLength: 25,
  //   responsive: true,
  //   dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
  //   buttons : [{extend:'excel', title:'Menus', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7,8,9,10,11]}}],
  //   oLanguage: {
  //     sLengthMenu: 'Mostrando _MENU_ registros por página',
  //     sZeroRecords: 'No se encontraron registros',
  //     sInfo: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
  //     sInfoEmpty: 'Mostrando 0 a 0 de 0 registros',
  //     sInfoFiltered: '(Filtrado desde _MAX_ registros)',
  //     sSearch:         'Buscar: ',
  //     oPaginate:{
  //       sFirst:    'Primero',
  //       sLast:     'Último',
  //       sNext:     'Siguiente',
  //       sPrevious: 'Anterior'
  //     }
  //   },
  //   "preDrawCallback": function( settings ) {
  //     $('#loader').fadeIn();
  //   }
  // }).on("draw", function(){
  //   $('#loader').fadeOut();
  //   $('.checkDespacho').iCheck({ checkboxClass: 'icheckbox_square-green' });
  //   $('#selecTodos').on('ifChecked', function(){ $('.checkDespacho').iCheck('check'); });
  //   $('#selecTodos').on('ifUnchecked', function(){ $('.checkDespacho').iCheck('uncheck'); });
  //   $('.checkDespacho').on('ifChecked', function(){ $('#sede_'+$(this).data('num')).prop('checked', true); });
  //   $('.checkDespacho').on('ifUnchecked', function(){ $('#sede_'+$(this).data('num')).prop('checked', false); });
  // });

  var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';
  $('.containerBtn').html(btnAcciones);

  <?php if (isset($_POST['buscar'])): ?>
    // Código para setear los campos del formulario de búsqueda con los parámetros especificados.

    $('#btnBuscar').prop('disabled', true);
    $('#formBuscar').find('input, textarea, button, select').prop('disabled',true);

    $('#mes_inicio').val('<?php echo $_POST['mes_inicio']; ?>').change();
    $('#mes_fin').val('<?php echo $_POST['mes_fin']; ?>').change();
  <?php if ($_POST['municipio'] != ""): ?>
      $('#municipio_desp').val('<?php echo $_POST['municipio']; ?>').change();
  <?php endif ?>
  <?php if ($_POST['tipo_documento'] != ""): ?>
      $('#tipo_documento').val('<?php echo $_POST['tipo_documento']; ?>').change();
  <?php endif ?>
  <?php if ($_POST['ruta_desp'] != ""): ?>
      $('#ruta_desp').val('<?php echo $_POST['ruta_desp']; ?>').change();
  <?php endif ?>
    setTimeout(function() {
    <?php if ($_POST['institucion_desp'] != ""): ?>
        $('#institucion_desp').val('<?php echo $_POST['institucion_desp']; ?>').change();
    <?php endif ?>
    }, 2200);

    setTimeout(function() {
      <?php if ($_POST['sede_desp'] != ""): ?>
          $('#sede_desp').val('<?php echo $_POST['sede_desp']; ?>').change();
      <?php endif ?>
      <?php if ($_POST['proveedor'] != ""): ?>
        $('#proveedor').val('<?php echo $_POST['proveedor']; ?>').change();;
      <?php endif ?>
      $('#btnBuscar').prop('disabled', false);
      $('#formBuscar').find('input, textarea, button, select').prop('disabled',false);
    }, 2800);

  <?php endif ?>
</script>

<?php mysqli_close($Link); ?>

</body>
</html>