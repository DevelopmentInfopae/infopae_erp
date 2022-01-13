<?php
  $titulo = 'Trazabilidad de alimentos';
  $meses = array('01' => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre");
  require_once '../../header.php';

  if ($permisos['informes'] == "0") {
    ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
    </script>
  <?php exit(); }

  $periodoActual = $_SESSION['periodoActual'];

   $entregaNom = array('1' => 'PRIMERA', '2' => 'SEGUNDA', '3' => 'TERCERA', '4' => 'CUARTA', '5' => 'QUINTA', '6' => 'SEXTA', '7' => 'SÉPTIMA', '8' => 'OCTAVA', '9' => 'NOVENA', '10' => 'DÉCIMA', '11' => 'UNDÉCIMA', '12' => 'DUODÉCIMA' );
  $consultaTipoBusqueda = " SELECT tipo_busqueda FROM parametros ";
  $respuestaTipoBusqueda = $Link->query($consultaTipoBusqueda) or die ('Error al consultar el tipo de busqueda ' . mysqli_error($Link));
  if ($respuestaTipoBusqueda->num_rows > 0) {
    $dataTipoBusqueda = $respuestaTipoBusqueda->fetch_assoc();
    $tipoBusqueda = $dataTipoBusqueda['tipo_busqueda'];
    if ($tipoBusqueda == "2") {
      $consultaNumeroEntrega = " SELECT mes, NumeroEntrega FROM planilla_dias ";
      $respuestaNumeroEntrega = $Link->query($consultaNumeroEntrega) or die ('Error al consultar el numero de la entrega ' . mysqli_error($Link));
      if ($respuestaNumeroEntrega->num_rows > 0) {
        while ($dataNumeroEntrega = $respuestaNumeroEntrega->fetch_assoc()) {
          $numeroEntrega[$dataNumeroEntrega['mes']] = $dataNumeroEntrega['NumeroEntrega'];
        }
      }
    }
  }
?>

<style type="text/css">

</style>
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
                                   table_schema = DATABASE() AND table_name like 'despachos_enc%'";
          $resultadoTablas = $Link->query($consultaTablas);
          if ($resultadoTablas->num_rows > 0) {
            $cnt=0;
            while ($tabla = $resultadoTablas->fetch_assoc()) {
              $mes = str_replace("despachos_enc", "", $tabla['tabla']);
              $mes = str_replace($_SESSION['periodoActual'], "", $mes);

              $nomMes = $meses[$mes];
              $opciones.= '<option value="'.$mes.'">'.$nomMes.'</option>';

              if ($cnt == 0) {
                  $cnt++;
                  $mesTablaInicio = $mes;
              }
             }
          }
          ?>
          <form class="form row" id="formBuscar" method="POST">
            <?php if ($tipoBusqueda == 1): ?>
              <div class="form-group col-sm-2">
                <label>Fecha de </label>
                <div class="row compositeDate">
                  <select name="fecha_de" id="fecha_de" class="form-control ">
                    <option value="1">Elaboración documento</option>
                    <option value="2">Días despachados</option>
                  </select>
                </div>
              </div>
              <div id="fechaDiasDespachos" style="display: none;">
                <div class="form-group col-sm-2">
                  <label>Desde</label>
                  <div class="row compositeDate">
                    <div class="col-sm-8 nopadding">
                      <select name="mes_inicio" id="mes_inicio" class="form-control ">
                        <?php echo $opciones; ?>
                      </select>
                    </div>
                    <div class="col-sm-4 nopadding">
                      <select name="dia_inicio" id="dia_inicio" class="form-control">
                        <option value="">dd</option>
                        <?php for ($i=1; $i <= 31; $i++) { ?>
                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group col-sm-2">
                  <label>Hasta</label>
                  <div class="row compositeDate">
                    <div class="col-sm-8 nopadding">
                      <input type="text" id="nomMesFin" value="Espere..." class="form-control" readonly>
                      <input type="hidden" name="mes_fin" id="mes_fin" value="01">
                    </div>
                    <div class="col-sm-4 nopadding">
                      <select name="dia_fin" id="dia_fin" class="form-control">
                        <option value="">dd</option>
                        <?php for ($i=1; $i <= 31; $i++) { ?>
                          <option value="<?php echo $i ?>"><?php echo $i ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div id="fechaElaboracion">
                <div class="form-group col-sm-2">
                  <label>Desde</label>
                  <input type="text" name="fecha_inicio_elaboracion" id="fecha_inicio_elaboracion" value="<?php echo date('Y-m')."-01" ?>"data-date-format="yyyy-mm-dd"  class="form-control datepicker">
                </div>
                <div class="form-group col-sm-2">
                  <label>Hasta</label>
                  <?php $mesSiguiente = date('m')+1; ?>
                  <input type="text" name="fecha_fin_elaboracion" id="fecha_fin_elaboracion" data-date-format="yyyy-mm-dd" value="" class="form-control datepicker">
                </div>
              </div>
            <?php endif ?>
            <?php if ($tipoBusqueda == "2"): ?>
              <div class="col-lg-3 col-sm-6- col-xs-12">
                <label for="numeroEntrega">Número Entrega</label>
                <select id="numeroEntrega" name="numeroEntrega" class="form-control">
                  <?php foreach ($numeroEntrega as $mes => $entrega): ?>
                    <option value="<?= $mes; ?>"><?= $entregaNom[$entrega]; ?></option>
                  <?php endforeach ?>
                </select>
              </div> <!-- col -->
            <?php endif ?>

            <div class="form-group col-sm-3">
              <label>Municipio</label>
              <select class="form-control" name="municipio" id="municipio">
                <option value="">Seleccionar</option>
                <?php
                  $codigo_departamento = $_SESSION['p_CodDepartamento'];
                  $codigo_municipio = $_SESSION["p_Municipio"];
                  $condicion_municipio = (! empty($codigo_municipio)) ? "AND CodigoDANE = '$codigo_municipio'": "";

                  $consultarMunicipios = "SELECT
                                              CodigoDANE, Ciudad
                                          FROM
                                              ubicacion
                                          WHERE
                                              CodigoDANE LIKE '$codigo_departamento%'
                                              $condicion_municipio
                                          GROUP BY ubicacion.Ciudad;";
                  $resultadoMunicipios = $Link->query($consultarMunicipios);
                  if ($resultadoMunicipios->num_rows > 0) {
                    while ($municipios = $resultadoMunicipios->fetch_assoc()) { ?>
                      <option value="<?= $municipios['CodigoDANE'] ?>" <?= (isset($_POST['municipio']) && $_POST['municipio'] == $municipios['CodigoDANE']) ? 'selected':'';  ?>><?= $municipios['Ciudad'] ?></option>
                    <?php }
                  }
                ?>
              </select>
            </div>
            <div class="form-group col-sm-3">
              <label>Tipo documento</label>
              <select name="tipo_documento" id="tipo_documento" class="form-control">
                <option value="">Seleccione...</option>
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
                <option value="">Seleccione tipo documento</option>
              </select>
            </div>
            <div class="form-group col-sm-2">
              <label>Filtrar por  </label>
              <select name="tipo_filtro" id="tipo_filtro" class="form-control">
                <option value="">Seleccione...</option>
                <option value="1">Bodega</option>
                <option value="2">Conductor</option>
                <option value="3">Producto</option>
                <option value="4">Grupo etario</option>
                <option value="5">Tipo complemento</option>
                <option value="6">Fecha vencimiento</option>
              </select>
            </div>
            <div id="divBodegas" style="display: none;">
              <div class="form-group col-sm-2">
                <label>Tipo bodega  </label>
                <select name="tipo_bodega" id="tipo_bodega" class="form-control">
                  <option value="">Todas</option>
                  <option value="1">Bodega origen</option>
                  <option value="2">Bodega destino</option>
                </select>
              </div>
              <div class="form-group col-sm-2">
                <label>Bodegas  </label>
                <select name="bodegas" id="bodegas" class="form-control">
                </select>
              </div>
            </div>
            <div class="form-group col-sm-3" id="divConductores" style="display: none;">
              <label>Conductor</label>
              <select name="conductor" id="conductor" class="form-control">

              </select>
            </div>
            <div id="divProductos" style="display: none;">
              <div class="form-group col-sm-3">
                <label>Producto</label>
                <select name="producto" id="producto" class="form-control">
                  <option value="">Cargando...</option>
                </select>
              </div>
              <div class="form-group col-sm-3">
                <label>Ver por  </label>
                <div class="radio">
                <label><input type="checkbox" name="totales" id="totales" value="1" <?php if(isset($_POST['totales']) && $_POST['totales'] = "1") { ?> checked <?php } ?> required> Totales</label>
                </div>
              </div>
            </div>
            <div class="form-group col-sm-3" id="divGrupoEtario" style="display: none;">
              <label>Grupo Etario</label>
              <select name="grupo_etario" id="grupo_etario" class="form-control">

              </select>
            </div>
            <div class="form-group col-sm-3" id="divTipoComplemento" style="display: none;">
              <label>Tipo de complemento</label>
              <select name="tipo_complemento" id="tipo_complemento" class="form-control">
                <?php
                $consultaTipoComplemento = "SELECT * FROM tipo_complemento";
                $resultadoTipoComplemento = $Link->query($consultaTipoComplemento);
                if ($resultadoTipoComplemento->num_rows > 0) {
                  while ($tipoComplemento = $resultadoTipoComplemento->fetch_assoc()) { ?>
                    <option value="<?php echo $tipoComplemento['CODIGO']; ?>"><?php echo $tipoComplemento['CODIGO']; ?></option>
                  <?php }
                }
                 ?>
              </select>
            </div>
            <div id="divFechaVencimiento" style="display: none;">
              <div class="form-group col-sm-2">
                <label>Desde : </label>
                <input type="date" name="fechavto_desde" class="form-control">
              </div>
              <div class="form-group col-sm-2">
                <label>Desde : </label>
                <input type="date" name="fechavto_hasta" class="form-control">
              </div>
            </div>
            <input type="hidden" name="buscar" value="1">
          </form>
          <div class="row">
            <div class="col-sm-12">
              <button class="btn btn-primary" onclick="$('#formBuscar').submit();" id="btnBuscar"> <span class="fa fa-search"></span>  Buscar</button>
              <?php if (isset($_POST['buscar'])): ?>
                <button class="btn btn-primary" onclick="location.href='index.php';" id="btnBuscar"> <span class="fa fa-times"></span>  Limpiar búsqueda</button>
              <?php endif ?>
            </div>
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
             <table class="table" id="tablaTrazabilidad">
                <thead>
                  <tr>
                    <th>Tipo Doc</th>
                    <th>Número</th>
                    <th>Fecha / Hora Elab</th>
                    <th>Fecha Despacho</th>
                    <th>Responsable / Proveedor</th>
                    <th>Nombre Producto / Alimento</th>
                    <th>Unidad Medida</th>
                    <th>Cantidad</th>
                    <th>Nombre Bodega Origen</th>
                    <th>Nombre Bodega Destino</th>
                    <th>Tipo Transp</th>
                    <th>Placa</th>
                    <th>Conductor</th>
                    <th>Lote</th>
                    <th>Fecha Vence</th>
                    <th>Marca</th>
                  </tr>
                </thead>
                <tbody id="tBodyTrazabilidad">

                </tbody>
                <tfoot>
                  <tr>
                    <th>Tipo Doc</th>
                    <th>Número</th>
                    <th>Fecha / Hora Elab</th>
                    <th>Fecha Despacho</th>
                    <th>Responsable / Proveedor</th>
                    <th>Nombre Producto / Alimento</th>
                    <th>Unidad Medida</th>
                    <th>Cantidad</th>
                    <th>Nombre Bodega Origen</th>
                    <th>Nombre Bodega Destino</th>
                    <th>Tipo Transp</th>
                    <th>Placa</th>
                    <th>Conductor</th>
                    <th>Lote</th>
                    <th>Fecha Vence</th>
                    <th>Marca</th>
                  </tr>
                </tfoot>
              </table>
          </div>
          <?php
            if (!isset($_POST['buscar'])) { //Si no hay filtrado
              $numtabla = $mesTablaInicio.$_SESSION['periodoActual'];

              $consulta = "SELECT
                  pmov.Tipo, pmov.Numero, pmov.FechaMYSQL, denc.FechaHora_Elab, pmov.fecha_despacho, pmov.Nombre as Proveedor, pmovdet.Descripcion, pmovdet.Umedida, FORMAT(pmovdet.Cantidad, 4) as Cantidad, bodegas.NOMBRE as nomBodegaOrigen, b2.NOMBRE as nomBodegaDestino, tipovehiculo.Nombre as TipoTransporte, pmov.Placa, pmov.ResponsableRecibe, pmovdet.Lote, pmovdet.FechaVencimiento, pmovdet.Marca
                  FROM productosmov$numtabla AS pmov
                    INNER JOIN productosmovdet$numtabla AS pmovdet ON pmov.Numero = pmovdet.Numero
                    INNER JOIN bodegas ON bodegas.ID = pmovdet.BodegaOrigen
                    INNER JOIN bodegas as b2 ON b2.ID = pmovdet.BodegaDestino
                    INNER JOIN tipovehiculo ON tipovehiculo.Id = pmov.TipoTransporte
                    INNER JOIN despachos_enc$numtabla as denc ON denc.Num_Doc = pmov.Numero
                  LIMIT 200;";
            } else if (isset($_POST['buscar'])) { //Si hay filtrado
              $condicionFvto = "";
              $inners="";
              $condiciones = "";
              $datos=" pmov.Tipo, pmov.Numero, pmov.FechaMYSQL, denc.FechaHora_Elab,  pmov.fecha_despacho, pmov.Nombre as Proveedor, pmovdet.Descripcion, pmovdet.Umedida, FORMAT(pmovdet.Cantidad, 4) as Cantidad, bodegas.NOMBRE as nomBodegaOrigen, b2.NOMBRE as nomBodegaDestino, tipovehiculo.Nombre as TipoTransporte, pmov.Placa, pmov.ResponsableRecibe, pmovdet.Lote, pmovdet.FechaVencimiento, pmovdet.Marca";

              if (isset($_POST['fecha_de']) && $_POST['fecha_de'] != "") {
                $fecha_de = $_POST['fecha_de'];

                if ($fecha_de == 1) {
                  $mes = date("m", strtotime($_POST['fecha_inicio_elaboracion']));
                  $numtabla = $mes.$_SESSION['periodoActual'];
                  $condiciones.=" AND denc.FechaHora_Elab > '".$_POST['fecha_inicio_elaboracion']." 00:00:00' AND denc.FechaHora_Elab < '".$_POST['fecha_fin_elaboracion']." 00:00:00' ";

                } else if ($fecha_de == 2) { //Si el tipo de búsqueda es por días despachados
                  $numtabla = $_POST['mes_inicio'].$_SESSION['periodoActual'];
                  $bnd_inner_denc = 1;

                  if ($_POST['dia_inicio'] != "" && $_POST['dia_fin'] != "" ) { //Si los días indicados son diferente a vacío, busca por los días despachados tomado del campo Dias en la tabla despachos_enc
                    $cnt2 = 0;
                    $condiciones.=" AND (";

                    for ($i=$_POST['dia_inicio']; $i <= $_POST['dia_fin'] ; $i++) {
                      if ($cnt2 > 0) {
                        $condiciones.=" OR ";
                      }
                      $condiciones.="denc.Dias LIKE '".$i."' OR denc.Dias LIKE '".$i.",%' OR denc.Dias like '%,".$i.",%' OR denc.Dias like '%,".$i."'";
                      $cnt2++;
                    }
                    $condiciones.=") ";
                  }
                }
              }

              if ($tipoBusqueda == "2" && $_POST['numeroEntrega'] != "") {
                $numtabla = $_POST['numeroEntrega'].$_SESSION['periodoActual'];
                $mesEntrega = $_POST['numeroEntrega'];
                $mesEntregaAnterior = $mesEntrega - 1;
                $mesEntregaSiguiente = $mesEntrega + 1;  
                $mesEntregaAnterior = "0".$mesEntregaAnterior;
                $mesEntregaSiguiente = "0".$mesEntregaSiguiente;
                $condiciones .= " AND ( MONTH(denc.FechaHora_Elab) = '" .$_POST['numeroEntrega']. "' OR MONTH(denc.FechaHora_Elab) = '" .$mesEntregaAnterior."' OR  MONTH(denc.FechaHora_Elab) = '".$mesEntregaSiguiente."' )";
              }

              if (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] != "") { //Si el tipo de documento se especificó
                if ($_POST['proveedor'] != "") { //Si el proveedor se especificó, busca según las bodegas relacionadas
                  $condiciones.=" AND pmov.Tipo = '".$_POST['tipo_documento']."' AND pmov.Nitcc = '".$_POST['proveedor']."' ";
                } else { //Si no especificó, trae todos los registros con el tipo de documento escogido
                  $condiciones.=" AND pmov.Tipo = '".$_POST['tipo_documento']."' ";
                }
              }

              if (isset($_POST['municipio']) && $_POST['municipio'] != "") {
                if (!isset($bnd_inner_denc) && $_POST['fecha_de'] != "2") {
                  $bnd_inner_denc = 1;
                }
                $inners.=" INNER JOIN sedes".$_SESSION['periodoActual']." as sede ON sede.cod_sede = denc.cod_Sede ";
                $condiciones.=" AND sede.cod_mun_sede = '".$_POST['municipio']."' ";
              }

              if (isset($_POST['tipo_filtro']) && $_POST['tipo_filtro'] != "") {
              $filtro = $_POST['tipo_filtro'];

                if ($filtro == 1) { //Valor escogido en tipo de filtro

                    if (isset($_POST['bodegas']) && $_POST['bodegas'] != "") { //Si el filtro de búsqueda está por bodegas

                      if ($_POST['tipo_bodega'] == 1) { //Si eligió buscar por la bodega de Origen
                        $condiciones.=" AND pmovdet.BodegaOrigen = '".$_POST['bodegas']."' ";
                      } else if ($_POST['tipo_bodega'] == 2) { //Si eligió buscar por la bodega de Destino
                        $condiciones.=" AND pmovdet.BodegaDestino = '".$_POST['bodegas']."' ";
                      } else if ($_POST['tipo_bodega'] == "") { //Si eligió buscar por las dos bodegas (Origen y Destino)
                        $condiciones.=" AND (pmovdet.BodegaOrigen = '".$_POST['bodegas']."' OR pmovdet.BodegaDestino = '".$_POST['bodegas']."')";
                      }
                    }
                } else if ($filtro == 2) {

                    if (isset($_POST['conductor']) && $_POST['conductor'] != "") { //Si el filtro de búsqueda está por conductor
                      $condiciones.=" AND pmov.ResponsableRecibe = '".$_POST['conductor']."' ";
                    }
                } else if ($filtro == 3) {

                    if (isset($_POST['producto']) && $_POST['producto'] != "") { //Si especificó filtro por producto
                        if (isset($_POST['totales'])) { //Si especificó ver por totales, suma las cantidades despachadas

                          if ($condiciones == "") { //Si no hay otros criterios especificados, muestra sólo valores Nombre de producto, Factor, Unidad medida y Cantidad
                            $txtTotales = "--";
                            $datos =" '".$txtTotales."' as Tipo, '".$txtTotales."' as Numero, '".$txtTotales."' as FechaMYSQL, '".$txtTotales."' as FechaHora_Elab, '".$txtTotales."' as Proveedor, pmovdet.Descripcion, pmovdet.Umedida, FORMAT(SUM(pmovdet.Cantidad), 4) as Cantidad,  '".$txtTotales."' as nomBodegaOrigen, '".$txtTotales."' as nomBodegaDestino,  '".$txtTotales."' as TipoTransporte, '".$txtTotales."' as Placa, '".$txtTotales."' as ResponsableRecibe, pmovdet.Lote, pmovdet.FechaVencimiento, pmovdet.Marca ";
                          } else { //Si hay criterios, muestra los resultados agrupados
                            $datos=" pmov.Tipo, pmov.Numero, pmov.FechaMYSQL, denc.FechaHora_Elab, pmov.fecha_despacho, pmov.Nombre as Proveedor, pmovdet.Descripcion, pmovdet.Umedida, FORMAT(SUM(pmovdet.Cantidad), 4) as Cantidad, bodegas.NOMBRE as nomBodegaOrigen, b2.NOMBRE as nomBodegaDestino, tipovehiculo.Nombre as TipoTransporte, pmov.Placa, pmov.ResponsableRecibe, pmovdet.Lote, pmovdet.FechaVencimiento, pmovdet.Marca  ";
                          }

                          $condiciones.=" AND pmovdet.CodigoProducto = '".$_POST['producto']."' GROUP BY pmovdet.CodigoProducto ";

                        } else { // Si no se especificó ver por totales, muestra cada uno de los despachos del producto
                          $condiciones.=" AND pmovdet.CodigoProducto = '".$_POST['producto']."' ";
                        }
                    }
                } else if ($filtro == 4) {

                  if (isset($_POST['grupo_etario']) && $_POST['grupo_etario'] != "") {
                    $inners.= " INNER JOIN despachos_det$numtabla as dent ON dent.Num_Doc = pmov.Numero AND dent.cod_Alimento = pmovdet.CodigoProducto";
                    $condiciones.=" AND dent.Id_GrupoEtario = '".$_POST['grupo_etario']."' ";
                  }
                } else if ($filtro == 5) {

                  if (isset($_POST['tipo_complemento']) && $_POST['tipo_complemento'] != "") {

                    // if (!isset($bnd_inner_denc)) { //Si 'fecha de' es por elaboración de documento Traemos datos de tabla despachos enc
                    //   $inners.= " INNER JOIN despachos_enc$numtabla as denc ON denc.Num_Doc = pmov.Numero ";
                    // }
                    $condiciones.=" AND denc.Tipo_Complem = '".$_POST['tipo_complemento']."' ";
                  }
                } else if ($filtro == 6) {

                  $condicionFvto = "";

                  if ($_POST['fechavto_desde'] != "" || $_POST['fechavto_hasta'] != "") {

                    $condicionFvto.=" AND pmovdet.FechaVencimiento >= '".$_POST['fechavto_desde']."' AND pmovdet.FechaVencimiento <= '".$_POST['fechavto_hasta']."'";

                  } else {

                    if ($_POST['fechavto_desde'] != "") {
                        $condicionFvto.=" AND pmovdet.FechaVencimiento >= '".$_POST['fechavto_desde']."'";
                    } else if ($_POST['fechavto_hasta'] != "") {
                        $condicionFvto.=" AND pmovdet.FechaVencimiento <= '".$_POST['fechavto_hasta']."'";
                    }

                  }
                }
              }

              $consulta = "SELECT
                                $datos
                            FROM
                              productosmov$numtabla AS pmov
                                INNER JOIN productosmovdet$numtabla AS pmovdet ON pmov.Numero = pmovdet.Numero $condicionFvto
                                INNER JOIN bodegas ON bodegas.ID = pmovdet.BodegaOrigen
                                INNER JOIN bodegas as b2 ON b2.ID = pmovdet.BodegaDestino
                                INNER JOIN tipovehiculo ON tipovehiculo.Id = pmov.TipoTransporte
                                INNER JOIN despachos_enc$numtabla as denc ON denc.Num_Doc = pmov.Numero
                                $inners WHERE 1 = 1 $condiciones
                            LIMIT 2000;";
          }?>
          <input type="hidden" name="consulta" id="consulta" value="<?php echo $consulta; ?>">
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/trazabilidad/js/trazabilidad.js"></script>

  <script type="text/javascript">
  console.log('Aplicando Data Table');
  dataset1 = $('#tablaTrazabilidad').DataTable({
    ajax: {
        method: 'POST',
        url: 'functions/fn_trazabilidad_obtener_datos_tabla.php',
        data:{
          consulta: $('#consulta').val()
        }
      },
    columns:[
        { data: 'Tipo'},
        { data: 'Numero'},
        { data: 'FechaHora_Elab'},
        { data: 'fecha_despacho'},
        { data: 'Proveedor'},
        { data: 'Descripcion'},
        { data: 'Umedida'},
        { data: 'Cantidad'},
        { data: 'nomBodegaOrigen'},
        { data: 'nomBodegaDestino'},
        { data: 'TipoTransporte'},
        { data: 'Placa'},
        { data: 'ResponsableRecibe'},
        { data: 'Lote'},
        { data: 'FechaVencimiento'},
        { data: 'Marca'},
      ],
    pageLength: 25,
    responsive: true,
    dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
    buttons : [{extend:'excel', title:'Trazabilidad_alimentos', className:'btnExportarExcel', exportOptions: {columns : [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14]}}],
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
    initComplete: function() {
      var btnAcciones = '<div class="dropdown pull-right" id=""><button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button><ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla"><li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar </a></li></ul></div>';
      $('.containerBtn').html(btnAcciones);
      $('#loader').fadeOut();
    },
    preDrawCallback: function( settings ) {
        $('#loader').fadeIn();
      }
    }).on("draw", function(){ $('#loader').fadeOut();});

  <?php if (isset($_POST['buscar'])): ?>
    $('#btnBuscar').prop('disabled', true);
    $('#formBuscar').find('input, textarea, button, select').prop('disabled',true);
    <?php if ($tipoBusqueda == "1"): ?>
      $('#fecha_de').val('<?php echo $_POST['fecha_de']; ?>').change();
      $('#mes_inicio').val('<?php echo $_POST['mes_inicio']; ?>').change();
      $('#mes_fin').val('<?php echo $_POST['mes_fin']; ?>').change();
    <?php endif ?>
    <?php if ($tipoBusqueda == "2"): ?>
      $('#numeroEntrega').val('<?= $_POST['numeroEntrega']; ?>')
    <?php endif ?>

    <?php if ($_POST['tipo_documento'] != ""): ?>
      $('#tipo_documento').val('<?php echo $_POST['tipo_documento']; ?>').change();
    <?php endif ?>
    <?php if ($_POST['tipo_filtro'] != ""): ?>
      $('#tipo_filtro').val('<?php echo $_POST['tipo_filtro']; ?>').change();
    <?php endif ?>
    <?php if (isset($_POST['dia_inicio']) && $_POST['dia_inicio'] != ""): ?>
      $('#dia_inicio').val('<?php echo $_POST['dia_inicio']; ?>').change();
    <?php endif ?>
    <?php if (isset($_POST['dia_fin']) && $_POST['dia_fin'] != ""): ?>
      $('#dia_fin').val('<?php echo $_POST['dia_fin']; ?>').change();
    <?php endif ?>
    setTimeout(function() {
      <?php if ($_POST['municipio'] != ""): ?>
        $('#municipio').val('<?php echo $_POST['municipio']; ?>').change();
      <?php endif ?>
      <?php if ($_POST['proveedor'] != ""): ?>
        $('#proveedor').val('<?php echo $_POST['proveedor']; ?>').change();;
      <?php endif ?>

      <?php if (isset($_POST['conductor']) && $_POST['conductor'] != ""): ?>
        $('#conductor').val('<?php echo $_POST['conductor']; ?>').change();
      <?php endif ?>

      <?php if ($_POST['tipo_bodega'] != ""): ?>
        $('#tipo_bodega').val('<?php echo $_POST['tipo_bodega']; ?>').change();
      <?php endif ?>
      <?php if (isset($_POST['bodegas']) && $_POST['bodegas'] != ""): ?>
        $('#bodegas').val('<?php echo $_POST['bodegas']; ?>').change();
      <?php endif ?>

      <?php if ($_POST['producto'] != ""): ?>
        $('#producto').val('<?php echo $_POST['producto']; ?>').change();
      <?php endif ?>

      <?php if (isset($_POST['grupo_etario']) && $_POST['grupo_etario'] != ""): ?>
        $('#grupo_etario').val('<?php echo $_POST['grupo_etario']; ?>').change();
      <?php endif ?>

      <?php if ($_POST['tipo_complemento'] != ""): ?>
        $('#tipo_complemento').val('<?php echo $_POST['tipo_complemento']; ?>').change();
      <?php endif ?>
      $('#btnBuscar').prop('disabled', false);
      $('#formBuscar').find('input, textarea, button, select').prop('disabled',false);
    }, 3500);

  <?php endif ?>

</script>

<?php mysqli_close($Link); ?>

</body>
</html>