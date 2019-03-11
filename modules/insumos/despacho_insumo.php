<?php 
$titulo = 'Nuevo despacho';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];
$meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="index.php">Ver insumos</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary guardar" onclick="submitDespacho(1);" id="segundoBtnSubmit" style=""><span class="fa fa-check"></span> Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form row" id="formDespachoInsumo">
            <div class="form-group col-sm-3">
              <label>Tipo de despacho</label>
              <select name="tipo_despacho" id="tipo_despacho" class="form-control" required>
                <option value="">Seleccione...</option>
                <?php 

                $consultaTipoDesp = "SELECT * FROM tipomovimiento WHERE Documento = 'DESI'";
                $resultadoTipoDesp = $Link->query($consultaTipoDesp);
                if ($resultadoTipoDesp->num_rows > 0) {
                  while ($tipoDesp = $resultadoTipoDesp->fetch_assoc()) { ?>
                    <option value="<?php echo $tipoDesp['Id'] ?>"><?php echo $tipoDesp['Movimiento'] ?></option>
                 <?php  }
                }

                 ?>
              </select>
              <input type="hidden" name="nomTipoMov" id="nomTipoMov">
            </div>
            <div class="form-group col-sm-3">
              <label>Proveedor / Empleado</label>
              <select name="proveedor" id="proveedor" class="form-control" required>
                <option value="">Seleccione...</option>
                
              </select>
              <input type="hidden" name="nombre_proveedor" id="nombre_proveedor">
            </div>
            <div class="form-group col-sm-3">
              <label>Municipio</label>
              <select name="municipio_desp" id="municipio_desp" class="form-control" required>
                <option value="">Seleccione...</option>
              <?php 
              $consultaMunicipios = "SELECT DISTINCT
                                        ubicacion.CodigoDANE, ubicacion.Ciudad
                                    FROM
                                        ubicacion,
                                        parametros
                                    WHERE
                                        ubicacion.ETC = 0 
                                        AND ubicacion.CodigoDane LIKE CONCAT(parametros.CodDepartamento, '%')
                                        AND EXISTS( SELECT DISTINCT
                                            cod_mun
                                        FROM
                                            instituciones
                                        WHERE
                                            cod_mun = ubicacion.CodigoDANE)
                                    ORDER BY ubicacion.Ciudad ASC";
              $resultadoMunicipios = $Link->query($consultaMunicipios);
              if ($resultadoMunicipios->num_rows > 0) {
                while ($municipio = $resultadoMunicipios->fetch_assoc()) { ?>
                  <option value="<?php echo $municipio['CodigoDANE']; ?>"><?php echo ucfirst(mb_strtolower($municipio['Ciudad'])); ?></option>
                <?php }
              }
               ?>  
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Institución</label>
              <select name="institucion_desp" id="institucion_desp" class="form-control select2">
                <option value="">Seleccione municipio</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Sede</label>
              <select name="sede_desp" id="sede_desp" class="form-control select2">
                <option value="">Seleccione institución</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Rutas</label>
              <select name="ruta_desp" id="ruta_desp" class="form-control select2">
                <option value="">Seleccione...</option>
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
            <div class="col-sm-12">
              <span class="btn btn-primary" onclick="añadirSedes()"><i class="fa fa-plus"></i> </span>
              <span class="btn btn-primary" onclick="eliminarSedes()"><i class="fa fa-minus"></i> </span>
            </div>
            <div class="col-sm-12">
              <br>
              <p>Debe seleccionar la sede a la que se le realizará el despacho.</p>
              <div class="radio">
                <label><input type="checkbox" name="seleccionar_todos" id="seleccionar_todos" onclick="seleccionarTodos(this)"> Seleccionar todos</label>
              </div>
              <table class="table">
                <thead>
                  <tr>
                    <th></th>
                    <th>Municipio</th>
                    <th>Institución</th>
                    <th>Sede</th>
                  </tr>
                </thead>
                <tbody id="tbodySedesDespachos">
                  
                </tbody>
                <tfoot>
                  <tr>
                    <th></th>
                    <th>Municipio</th>
                    <th>Institución</th>
                    <th>Sede</th>
                  </tr>
                </tfoot>
              </table>
              <div class="alert alert-danger" role="alert" id="errDespachos" style="display: none;">
                
              </div>
            </div>

            <hr class="col-sm-11">

            <div class="productos col-sm-12">
              <h3>Productos a despachar</h3><br>
              <span class="btn btn-primary" onclick="anadirProducto()"><i class="fa fa-plus"></i></span>
              <span class="btn btn-primary" onclick="borrarProducto()"><i class="fa fa-minus"></i></span>
              <br>
              <br>
              <div class="row" id="productosDesp">

              </div>
            </div>

            <hr class="col-sm-11">

            <div class="form-group col-sm-3">
              <label>Meses a despachar</label>
              <select name="meses_despachar[]" id="meses_despachar" class="form-control" multiple required>
              <?php 
              $consultaMesesPlanillaDias = "SELECT mes FROM planilla_dias WHERE ano = '".$_SESSION['periodoActualCompleto']."'"; 
              $resultadoMesesPlanillaDias = $Link->query($consultaMesesPlanillaDias);
              if ($resultadoMesesPlanillaDias->num_rows > 0) {
                while ($MPD = $resultadoMesesPlanillaDias->fetch_assoc()) { ?>
                  <option value="<?php echo $MPD['mes']; ?>"><?php echo $meses[$MPD['mes']]; ?></option>
                <?php }
              } else { ?>
                  <option value="">No hay meses registrados.</option>
              <?php }
              ?>   
              </select>
            </div>

            <div class="form-group col-sm-3">
              <label>Bodega Origen</label>
              <select name="bodega_origen" id="bodega_origen" class="form-control" required>
                <option value="">Seleccione proveedor/empleado</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Bodega Destino</label>
              <input type="text" class="form-control" value="Bodega asignada a sede" readonly>
            </div>

            <hr class="col-sm-11">

            <div class="col-sm-3">
              <label>Tipo de transporte</label>
              <select name="tipo_transporte" class="form-control" required>
                <option value="">Seleccione...</option>
                <?php 
                $consultaTipoTrans = "SELECT * FROM tipovehiculo";
                $resultadoTipoTrans = $Link->query($consultaTipoTrans);
                if ($resultadoTipoTrans->num_rows > 0) {
                  while ($tipoTrans = $resultadoTipoTrans->fetch_assoc()) { ?>
                    <option value="<?php echo $tipoTrans['Id'] ?>"><?php echo $tipoTrans['Nombre'] ?></option>
                  <?php }
                }
                 ?>
              </select>
            </div>
            <div class="col-sm-3">
              <label>Placa</label>
              <input type="text" name="placa_vehiculo" class="form-control">
            </div>
            <div class="col-sm-3">
              <label>Conductor</label>
              <input type="text" name="conductor" class="form-control">
            </div>
          </form>
          <div class="col-sm-12" style="padding: 3% 0% 2% 0%;">
            <button class="btn btn-primary guardar" onclick="submitDespacho(1);"><span class="fa fa-check"></span> Guardar</button>
          </div>
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/insumos/js/insumos.js"></script>

<script type="text/javascript">/*
  console.log('Aplicando Data Table');
  dataset1 = $('#box-table').DataTable({
    order: [ 0, 'asc' ],
    pageLength: 25,
    responsive: true,
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
    }
    });*/
    $('#tipoProducto').change();


  $('.select2').select2({
    width: "resolve"
  });
</script>

<?php mysqli_close($Link); ?>

</body>
</html>