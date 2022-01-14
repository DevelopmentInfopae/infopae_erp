<?php
// if(isset($_POST['id_despacho'])){} else { echo "<script>alert('No ha indicado despacho.');location.href='';</script>"; }

$titulo = 'Editar despacho';
require_once '../../header.php';

if ($permisos['despachos'] == "0") {
  ?><script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }

$periodoActual = $_SESSION['periodoActual'];
$meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");

$iddespacho = $_POST['id_despacho'];
$mesTabla = $_POST['mesTabla'];
// $iddespacho = "3";
// $mesTabla = "01";
$tablaNom = "insumosmov".$mesTabla.$_SESSION['periodoActual'];
$tablaNom2 = "insumosmovdet".$mesTabla.$_SESSION['periodoActual'];
$sede = "sedes".$_SESSION['periodoActual'];

$consDatosDespacho = "SELECT instituciones.nom_inst, sede.nom_sede, ubicacion.Ciudad, insmov.* FROM $tablaNom as insmov
  INNER JOIN $sede AS sede ON sede.cod_sede = insmov.BodegaDestino
  INNER JOIN instituciones ON instituciones.codigo_inst = sede.cod_inst
  INNER JOIN ubicacion ON ubicacion.CodigoDANE = sede.cod_mun_sede
WHERE insmov.Id = '".$iddespacho."'";
$resDatosDespacho = $Link->query($consDatosDespacho);
$DatosDespacho = [];
if ($resDatosDespacho->num_rows > 0) {
  $DatosDespacho = $resDatosDespacho->fetch_assoc();
} else {
  // echo "<script>alert('No existe despacho con el ID especificado.');location.href='';</script>";
  echo $consDatosDespacho;
}
?>

<?php if ($_SESSION['perfil'] == "0" || $permisos['despachos'] == "2"): ?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="despachos.php">Ver despachos de insumos</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary guardar" onclick="submitDespacho(2);" id="segundoBtnSubmit" style=""><span class="fa fa-check"></span> Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form row" id="formDespachoInsumo">
            <input type="hidden" name="idDespacho" value="<?php echo $iddespacho; ?>">
            <input type="hidden" name="numDoc" value="<?php echo $DatosDespacho['Numero']; ?>">
            <input type="checkbox" name="sede[]" value="<?php echo $DatosDespacho['BodegaDestino']; ?>" checked='true' style='display: none;'>
            <div class="form-group col-sm-3">
              <label>Tipo de despacho</label>
              <select name="tipo_despacho" id="tipo_despacho" class="form-control" required>
                <option value="">Seleccione...</option>
                <?php

                $consultaTipoDesp = "SELECT * FROM tipomovimiento WHERE Documento = 'DESI'";
                $resultadoTipoDesp = $Link->query($consultaTipoDesp);
                if ($resultadoTipoDesp->num_rows > 0) {
                  while ($tipoDesp = $resultadoTipoDesp->fetch_assoc()) {

                    if ($tipoDesp['Movimiento'] == $DatosDespacho['Tipo']) {
                      $selected = "selected='true'";
                    } else {
                      $selected = "";
                    }

                    ?>
                    <option value="<?php echo $tipoDesp['Id'] ?>" <?php echo $selected; ?>><?php echo $tipoDesp['Movimiento'] ?></option>
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
            <!-- <div class="form-group col-sm-3">
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
            </div> -->
<!--             <div class="form-group col-sm-3">
              <label>Institución</label>
              <select name="institucion_desp" id="institucion_desp" class="form-control">
                <option value="">Seleccione municipio</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Sede</label>
              <select name="sede_desp" id="sede_desp" class="form-control">
                <option value="">Seleccione institución</option>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Rutas</label>
              <select name="ruta_desp" id="ruta_desp" class="form-control">
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
            </div> -->
           <!--  <div class="col-sm-12">
              <span class="btn btn-primary" onclick="añadirSedes()"><i class="fa fa-plus"></i> </span>
              <span class="btn btn-primary" onclick="eliminarSedes()"><i class="fa fa-minus"></i> </span>
            </div> -->
            <div class="col-sm-12">
              <br>
              <!-- <p>Debe seleccionar la sede a la que se le realizará el despacho.</p>
              <div class="radio">
                <label><input type="checkbox" name="seleccionar_todos" id="seleccionar_todos" onclick="seleccionarTodos(this)"> Seleccionar todos</label>
              </div> -->
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
                      <tr>
                        <td><!-- <input type="checkbox" onclick="return false;" name="sede[]" class="checkInst " value="<?php echo $DetDespacho['BodegaDestino']; ?>" checked> -->
                          <!-- <button class="btn btn-danger btn-outline btn-sm" type='button' data-codsede="<?php echo $DetDespacho['BodegaDestino']; ?>" data-numdoc="<?php echo $DatosDespacho['Numero']; ?>" title='Eliminar sede del despacho' data-toggle="modal" data-target="#modalEliminarSedeDespacho"><span class="fa fa-trash"></span></button> -->
                          <input type="hidden" name="">
                        </td>
                        <td><?php echo $DatosDespacho['Ciudad']; ?></td>
                        <td><?php echo $DatosDespacho['nom_inst']; ?></td>
                        <td><?php echo $DatosDespacho['nom_sede']; ?></td>
                      </tr>
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

            <?php
            $productos = [];
            $consulta = "SELECT * FROM productos".$_SESSION['periodoActual']." WHERE Codigo LIKE '05%' AND Nivel = '3'";
            $resultado = $Link->query($consulta);
            if ($resultado->num_rows > 0) {
              while ($datProd = $resultado->fetch_assoc()) {
                $productos[] = $datProd;
              }
            }
            ?>

            <div class="productos col-sm-12">
              <h3>Productos a despachar</h3><br>
              <span class="btn btn-primary" onclick="anadirProducto()"><i class="fa fa-plus"></i></span>
              <span class="btn btn-primary" onclick="borrarProducto()"><i class="fa fa-minus"></i></span>
              <br>
              <br>
              <div class="row" id="productosDesp">

                <?php
                  $consDetDespacho = "SELECT * FROM $tablaNom2 WHERE Numero = '".$DatosDespacho['Numero']."'";
                  $resDetDespacho = $Link->query($consDetDespacho);
                  $num = 0;
                  if ($resDetDespacho->num_rows > 0) {
                    while ($DetDespacho = $resDetDespacho->fetch_assoc()) {
                      $num++;
                      ?>
                      <div class="col-sm-3 row" id="producto_<?php echo $num; ?>">
                          <div class="col-sm-2">
                            <button class="btn btn-danger btn-outline btn-sm" type="button" data-iddet="<?php echo $DetDespacho['Id']; ?>" data-numdoc="<?php echo $DatosDespacho['Numero']; ?>" data-mestabla="<?php echo $mesTabla; ?>" data-numdet="<?php echo $num; ?>" title='Eliminar producto del despacho' data-toggle="modal" data-target="#modalEliminarProductoDespacho"><span class="fa fa-trash"></span></button>
                          </div>
                          <div class="col-sm-10">
                            <select class="form-control productodesp" onchange="validaProductos(this, '<?php echo $num; ?>')" name="productoDespacho[]" id="producto_<?php echo $num; ?>"
                              required>
                              <?php foreach ($productos as $producto) {
                                if ($producto['Codigo'] == $DetDespacho['CodigoProducto']) { ?>
                                  <option value="<?php echo $producto['Codigo'] ?>" <?php echo $selected; ?>><?php echo $producto['Descripcion'] ?></option>
                                <?php }
                                ?>
                              <?php } ?>
                            </select>
                          </div>
                          <input type="hidden" name="DescInsumo[]" id="descIns_<?php echo $num; ?>" value="<?php echo $DetDespacho['Descripcion']; ?>">
                          <input type="hidden" name="idDetDespacho[]" id="idDetDespacho_<?php echo $num; ?>" value="<?php echo $DetDespacho['Id']; ?>">
                      </div>
                    <?php }
                  }
                 ?>
              </div>
            </div>

            <hr class="col-sm-11">

            <div class="form-group col-sm-3">
              <label>Tipo complemento</label>
              <input type="text" class="form-control" name="tipo_complemento" id="tipo_complemento" value="<?= $DatosDespacho['Complemento'] == "Total cobertura" ? "ALL" : $DatosDespacho['Complemento'] ?>" readonly>
            </div>

            <div class="form-group col-sm-3">
              <label>Meses a despachar</label>
              <input type="text" value="<?php echo $meses[$mesTabla]; ?>" class="form-control" readonly>
              <input type="hidden" name="meses_despachar[]" id="meses_despachar" value="<?php echo $mesTabla; ?>">
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
                <?php
                $consultaTipoTrans = "SELECT * FROM tipovehiculo";
                $resultadoTipoTrans = $Link->query($consultaTipoTrans);
                if ($resultadoTipoTrans->num_rows > 0) {
                  while ($tipoTrans = $resultadoTipoTrans->fetch_assoc()) {

                    if ($tipoTrans['Id'] == $DatosDespacho['TipoTransporte']) {
                      $selected = "seleted=true";
                    } else {
                      $selected = "";
                    }

                   ?>
                    <option value="<?php echo $tipoTrans['Id'] ?>" <?php echo $selected; ?>><?php echo $tipoTrans['Nombre'] ?></option>
                  <?php }
                }
                 ?>
              </select>
            </div>
            <div class="col-sm-3">
              <label>Placa</label>
              <input type="text" name="placa_vehiculo" class="form-control" value="<?php echo $DatosDespacho['Placa']; ?>">
            </div>
            <div class="col-sm-3">
              <label>Conductor</label>
              <input type="text" name="conductor" class="form-control"  value="<?php echo $DatosDespacho['ResponsableRecibe']; ?>">
            </div>
          </form>
          <div class="col-sm-12" style="padding: 3% 0% 2% 0%;">
            <button class="btn btn-primary guardar" onclick="submitDespacho(2);"><span class="fa fa-check"></span> Guardar</button>
          </div>
        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

<div class="modal inmodal fade" id="modalEliminarProductoDespacho" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
 <div class="modal-dialog modal-sm">
   <div class="modal-content">
     <div class="modal-header text-info" id="tipoCabecera" style="padding: 15px;">
       <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Cerrar</span></button>
       <h3><i class="fa fa-question-circle fa-lg" aria-hidden="true"></i> Confirmación InfoPAE </h3>
     </div>
     <div class="modal-body" style="text-align: center;">
         <h3>¿Está seguro de eliminar el producto del despacho?</h3>
         <p id="mensajeConfirm" style="display: none;"><b>¡Atención! </b> Eliminará el último producto del despacho, por lo que el despacho se eliminará también.</p>
         <input type="hidden" name="num_det" id="num_det">
         <input type="hidden" name="id_det_despacho" id="id_det_despacho">
         <input type="hidden" name="numdoc_eliminar_det" id="numdoc_eliminar_det">
         <input type="hidden" name="mes_tabla_eliminar_det" id="mes_tabla_eliminar_det">
     </div>
     <div class="modal-footer">
       <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">No</button>
       <button type="button" class="btn btn-primary btn-sm" id="tipoBoton" onclick="eliminarProductoDespacho()">Si</button>
     </div>
   </div>
 </div>
</div>
<?php else: ?>
  <script type="text/javascript">
    window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php endif ?>

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
    $('#tipo_despacho').change();

    setTimeout(function() {
      $('#proveedor').val('<?php echo $DatosDespacho['Nitcc'] ?>').change();
    }, 800);

</script>

<?php mysqli_close($Link); ?>

</body>
</html>