<?php 
$titulo = 'Nuevo menú de complementos alimentarios';
require_once '../../header.php'; 
$periodoActual = $_SESSION['periodoActual'];
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="index.php">Ver menús</a>
      </li>
      <li class="active">
        <strong><?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary" onclick="submitForm('formFichaTecnica');" id="segundoBtnSubmit" style="display: none;"><span class="fa fa-check"></span> Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true"><!-- COLLAPSE -->
            <div class="panel panel-default">
              <div class="panel-heading clearfix" role="tab" id="headingOne"> 
                <h4 class="panel-title"><span class="fa fa-file-text-o"></span>   Datos del menú
                  <a role="button" data-toggle="collapse" data-parent="#accordion" href="#producto" aria-expanded="true" aria-controls="producto" id="verDatosProducto" class="pull-right" style="color: #337ab7;">Editar</a>
                </h4>
              </div>
              <div id="producto" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                  <form class="form" id="formProducto" method="post">
                    <div class="form-group col-sm-3">
                      <label>Tipo de producto</label>
                      <select class="form-control" name="tipoProducto" id="tipoProducto" required>
                        <option value="01">Menú</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3" id="divTipoComplemento" style="display: none;">
                      <label>Tipo de Complemento</label>
                      <select class="form-control" name="tipoComplemento" id="tipoComplemento">
                        <option value="">Seleccione...</option>
                        <?php
                         $consulta1= " SELECT * FROM tipo_complemento";
                              $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                              if($result1){
                                while($row1 = $result1->fetch_assoc()){ ?>
                                  <option value="<?php echo $row1['CODIGO']; ?>">
                                    <?php echo $row1['CODIGO']; ?> (<?php echo $row1['DESCRIPCION']; ?> )</option>
                                  <?php
                                }
                              }
                        ?>
                      </select>
                    </div>
                    <div class="form-group col-sm-3" id="divSubTipoProducto" style="display: none;">
                      <label>SubTipo de producto</label>
                      <select class="form-control" name="subtipoProducto" id="subtipoProducto">
                        <option value="">Escoja tipo de producto.</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Descripción</label>
                      <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Describa el producto" required>
                      <em style="color: #cc5965; font-size: 13px; display: none;" id="existeDesc">Un producto con esta descripción ya existe.</em>
                    </div>
                    <div class="form-group col-sm-3" id="divTipoDespacho" style="display: none;">
                      <label>Tipo de despacho</label>
                      <select class="form-control" name="tipo_despacho" id="tipoDespacho">
                        <option value="">Seleccione...</option>
                      </select>
                    </div>
                    <div class="form-group col-sm-3" id="divGrupoEtario"  style="display: none;">
                      <label>Grupo Etario</label>
                      <select class="form-control" name="Cod_Grupo_Etario" id="Cod_Grupo_Etario">
                        <option value="">Seleccione...</option>
                        <?php 
                        $consultaGrupoEtario = "select * from grupo_etario";
                        $resultadoGrupoEtario = $Link->query($consultaGrupoEtario);
                        if ($resultadoGrupoEtario->num_rows > 0) {
                          while ($row = $resultadoGrupoEtario->fetch_assoc()) { ?>
                            <option value="<?php echo $row['ID'] ?>"><?php echo $row['DESCRIPCION'] ?></option>
                          <?php }
                        }
                         ?>
                      </select>
                    </div>
                    <div class="form-group col-sm-3" id="divVariacionMenu" style="display: none;">
                      <label>Variación menú</label>
                      <select class="form-control" name="variacionMenu" id="variacionMenu">
                        <option value="">Seleccione...</option>
                        <?php
                         $consulta1= " SELECT * FROM variacion_menu";
                              $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                              if($result1){
                                while($row1 = $result1->fetch_assoc()){ ?>
                                  <option value="<?php echo $row1['id']; ?>">
                                    <?php echo $row1['descripcion']; ?></option>
                                  <?php
                                }
                              }
                        ?>
                      </select>
                    </div>
                    <div class="form-group col-sm-3" id="divOrdenCiclo" style="display: none;">
                      <label>Orden ciclo</label>
                      <input type="text" name="ordenCiclo" id="ordenCiclo" class="form-control" readonly placeholder="Indique tipo, subtipo y grupo Etario.">
                    </div>
                    <hr class="col-sm-11">
                    <div class="form-group col-sm-12" id="gestionMedidas" style="display: none;">
                      <label>Añadir/borrar medida presentación</label><br>
                      <label class="btn btn-primary" onclick="anadirMedida();"><span class="fa fa-plus"></span></label>
                      <label class="btn btn-danger" onclick="quitarMedida();"><span class="fa fa-minus"></span></label>
                    </div>
                    <div id="medidasPresentacion">
                      <div class="form-group col-sm-3">
                        <label>Unidad de medida</label>
                        <input type="text" name="unidadMedida" value="u" style="display: none;">
                        <input type="text" name="" value="Unidad" class="form-control" readonly>
                      </div>
                      <div class="form-group col-sm-3" id="divUnidadMedidaPresentacion" style="display: none;">
                        <label>Unidad de medida de presentación 1</label>
                        <select name="unidadMedidaPresentacion[1]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                          <option value="">Seleccione unidad de medida.</option>
                        </select>
                      </div>
                      <div class="form-group col-sm-3" id="divCantPresentacion">
                        <label>Cantidad presentación 1</label>
                        <input type="number" min="0" name="cantPresentacion[1]" id="cantPresentacion" class="form-control" required>
                      </div>
                    </div>
                  </form>
                  <div class="form-group col-sm-12">
                      <button class="btn btn-primary" onclick="validarForm('formProducto', 'producto', 'fichaTecnica');" id="botonSiguiente">Siguiente</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-default" id="fichaTecnicaPanel">
              <div class="panel-heading" role="tab" id="headingTwo">
                <h4 class="panel-title"><span class="fa fa-list-alt"></span> Composición del menú
                  
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#fichaTecnica" aria-expanded="false" aria-controls="fichaTecnica" style="display: none;" id="verFichaTecnica"></a>
                </h4>
              </div>
              <div id="fichaTecnica" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                <div class="panel-body">
                  <form class="form" id="formFichaTecnica" method="post">
                    <input type="number" name="idProducto" id="idProducto" style="display: none;">
                    <input type="number" name="IdFT" id="IdFT" style="display: none;">
                    <input type="number" name="TipoProductoFT" id="TipoProductoFT" style="display: none;">
                    <div class="form-group col-sm-12">
                      <label>Seleccione preparaciones</label>
                      <table class="table" id="tablaProductosFichaTecnicaDet">
                        <thead>
                          <tr>
                            <th>Preparado</th>
                            <th class="datoPreparado" style="display: none;">Unidad de medida</th>
                            <th class="datoPreparado">Cantidad</th>
                            <th class="datoPreparado">Peso Bruto</th>
                            <th class="datoPreparado">Peso Neto</th>
                          </tr>
                        </thead>
                        <tbody id="tbodyProductos">
                          <tr class="productoFichaTecnicaDet">
                            <td>
                              <select class="form-control" name="productoFichaTecnicaDet[1]" id="productoFichaTecnicaDet1" onchange="obtenerUnidadMedidaProducto(this, 1);" style="width: 100%;" required>
                                <option value="">Cargando...</option>
                              </select>
                            </td>
                            <td class="datoPreparado">
                              <input type="text" class="form-control" name="unidadMedidaProducto[1]" id="unidadMedidaProducto1" readonly>
                            </td>
                            <td class="datoPreparado">
                              <input type="number" min="0" class="form-control" name="cantidadProducto[1]" id="cantidadProducto1" onchange="cambiarPesos(this, 1);" step=".0001">
                            </td>
                            <td class="datoPreparado">
                              <input type="number" min="0" class="form-control" name="pesoBrutoProducto[1]" id="pesoBrutoProducto1" step=".0001">
                            </td>
                            <td class="datoPreparado">
                              <input type="number" min="0" class="form-control" name="pesoNetoProducto[1]" id="pesoNetoProducto1" step=".0001">
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <a class="btn btn-primary" onclick="anadirProducto();"><span class="fa fa-plus"></span></a>
                      <a class="btn btn-primary" onclick="borrarProducto();"><span class="fa fa-minus"></span></a>
                    </div>
                  </form>
                  <div class="form-group col-sm-12">
                    <button class="btn btn-primary" onclick="submitForm('formFichaTecnica');"><span class="fa fa-check"></span>  Guardar</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-default" id="aportesCalyNutPanel" style="display: none;">
              <div class="panel-heading" role="tab" id="headingThree">
                <h4 class="panel-title"><span class="fa fa-bar-chart-o"></span>  Aportes nutritivos.
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#aportesCalyNut" aria-expanded="false" aria-controls="aportesCalyNut" id="verCalyNut">
                    ver
                  </a>
                </h4>
              </div>
              <div id="aportesCalyNut" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                <div class="panel-body">
                  <form class="form" id="formCalyNut" method="post">
                    <input type="number" name="idProductoCalyNut" id="idProductoCalyNut" style="display: none;">
                    <input type="number" name="tipoProductoCalyNut" id="tipoProductoCalyNut" style="display: none;">
                    <div class="form-group col-sm-3">
                      <label>Calorías (Kcal)</label>
                      <input type="number" name="kcalxg" id="kcalxg" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Kcal desde la grasa</label>
                      <input type="number" name="kcaldgrasa" id="kcaldgrasa" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Grasa saturada</label>
                      <input type="number" name="Grasa_Sat" id="Grasa_Sat" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Grasa poliinsaturada</label>
                      <input type="number" name="Grasa_poliins" id="Grasa_poliins" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Grasa monoinsaturada</label>
                      <input type="number" name="Grasa_Monoins" id="Grasa_Monoins" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Grasa trans</label>
                      <input type="number" name="Grasa_Trans" id="Grasa_Trans" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Fibra dietaria</label>
                      <input type="number" name="Fibra_dietaria" id="Fibra_dietaria" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Azúcares</label>
                      <input type="number" name="Azucares" id="Azucares" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Proteínas</label>
                      <input type="number" name="Proteinas" id="Proteinas" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Colesterol</label>
                      <input type="number" name="Colesterol" id="Colesterol" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Sodio</label>
                      <input type="number" name="Sodio" id="Sodio" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Zinc</label>
                      <input type="number" name="Zinc" id="Zinc" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Calcio</label>
                      <input type="number" name="Calcio" id="Calcio" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Hierro</label>
                      <input type="number" name="Hierro" id="Hierro" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Vitamina A</label>
                      <input type="number" name="Vit_A" id="Vit_A" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Vitamina C</label>
                      <input type="number" name="Vit_C" id="Vit_C" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Vitamina B1</label>
                      <input type="number" name="Vit_B1" id="Vit_B1" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Vitamina B2</label>
                      <input type="number" name="Vit_B2" id="Vit_B2" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Vitamina B3</label>
                      <input type="number" name="Vit_B3" id="Vit_B3" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Ácido Fólico</label>
                      <input type="number" name="Acido_Fol" id="Acido_Fol" class="form-control" min='0' required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Referencia</label>
                      <input type="text" name="Referencia" id="Referencia" class="form-control" required>
                    </div>
                    <div class="form-group col-sm-3">
                      <label>Código de referencia</label>
                      <input type="text" name="cod_Referencia" id="cod_Referencia" class="form-control" required>
                    </div>
                  </form>
                  <div class="form-group col-sm-12">
                    <button class="btn btn-primary" onclick="submitForm('formCalyNut');"><span class="fa fa-check"></span>  Guardar</button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- COLLAPSE -->
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
<script src="<?php echo $baseUrl; ?>/modules/menus2/js/menus.js"></script>

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


    $('.productoFichaTecnicaDet select').select2({
      width : "100%"
    });
</script>

<?php mysqli_close($Link); ?>

</body>
</html>