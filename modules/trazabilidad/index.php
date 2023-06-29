<?php
   require_once '../../header.php';
   if ($permisos['informes'] == "0") {
?>    <script type="text/javascript">
         window.open('<?= $baseUrl ?>', '_self');
      </script>
<?php exit(); }
	  else {
		?><script type="text/javascript">
		  const list = document.querySelector(".li_informes");
		  list.className += " active ";
        const list2 = document.querySelector(".li_trazabilidad");
		  list2.className += " active ";
		</script>
	  <?php
	  }
   $periodoActual = $_SESSION['periodoActual'];
   // $titulo = 'Trazabilidad de alimentos';
   $meses = array('01' => "Enero", 
                  "02" => "Febrero", 
                  "03" => "Marzo", 
                  "04" => "Abril", 
                  "05" => "Mayo", 
                  "06" => "Junio", 
                  "07" => "Julio", 
                  "08" => "Agosto", 
                  "09" => "Septiembre", 
                  "10" => "Octubre", 
                  "11" => "Noviembre", 
                  "12" => "Diciembre");

   $entregaNom = array( '1' => 'PRIMERA', 
                        '2' => 'SEGUNDA', 
                        '3' => 'TERCERA', 
                        '4' => 'CUARTA', 
                        '5' => 'QUINTA', 
                        '6' => 'SEXTA', 
                        '7' => 'SÉPTIMA', 
                        '8' => 'OCTAVA', 
                        '9' => 'NOVENA', 
                        '10' => 'DÉCIMA', 
                        '11' => 'UNDÉCIMA', 
                        '12' => 'DUODÉCIMA' );

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
   $bandera = 0;

   // meses de despachos creados
   $consultaTablas = "SELECT
                       table_name AS tabla
                      FROM
                       information_schema.tables
                      WHERE
                       table_schema = DATABASE() AND table_name like 'despachos_enc%'";
   $resultadoTablas = $Link->query($consultaTablas);
   if ($resultadoTablas->num_rows > 0) {
      while ($tabla = $resultadoTablas->fetch_assoc()) {
         $mes = str_replace("despachos_enc", "", $tabla['tabla']);
         $mes = str_replace($_SESSION['periodoActual'], "", $mes);
         $mesesTablas[$mes] = $mes;
      }
   } 

   // meses de ordenes creadas
   $consultaTablas = "SELECT
                        table_name AS tabla
                     FROM
                        information_schema.tables
                     WHERE
                        table_schema = DATABASE() AND table_name like 'orden_compra_enc%'";
   $resultadoTablas = $Link->query($consultaTablas);
   if ($resultadoTablas->num_rows > 0) {
      while ($tabla = $resultadoTablas->fetch_assoc()) {
         $mes = str_replace("orden_compra_enc", "", $tabla['tabla']);
         $mes = str_replace($_SESSION['periodoActual'], "", $mes);
         $mesesTablas[$mes] = $mes;
      }
   }    

   $opciones ="";
   $banderaPrimerMes = 0;
   foreach ($mesesTablas as $key => $value) {
      $nomMes = $meses[$value];
      $opciones.= '<option value="'.$value.'">'.$value.'</option>';
      $mesTablaInicio = $value;
      if ($banderaPrimerMes == 0) {
         $primerMes = $value;
         $banderaPrimerMes = 1;
      }
   }

   // echo $primerMes;
   $month     = $_SESSION['periodoActualCompleto'].'-'.$mesTablaInicio ;
   $aux         = date('Y-m-d', strtotime("{$month} + 1 month"));
   $last_day = date('Y-m-d', strtotime("{$aux} - 1 day"));
   
   $nameLabel = get_titles('informes', 'trazabilidad', $labels);
   $titulo = $nameLabel;
?>

<style type="text/css">
   .select2-container--open {
      z-index: 9999999
  }
</style>

<!-- Ventana de formulario de exportación para la plantilla de trazabilidad -->
<div class="modal inmodal fade" id="ventana_formulario_exportar_plantilla_trazabilidad"  role="dialog" style="display: none;" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header text-info" style="padding: 15px;">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
            <h3 id="titulo"><i class="fa fa-upload fa-lg" aria-hidden="true"></i> Exportar Trazabilidad  </h3>
         </div>
         <div class="modal-body">
            <form action="" name="formulario_exportar_plantilla_trazabilidad" id="formulario_exportar_plantilla_trazabilidad">
               <div class="row">
                  <div class="col-md-4 col-sm-12">
                     <div class="form-group">
                        <input type="hidden" name="tipoFormato" id="tipoFormato">
                        <label for="mes_exportar">Mes</label>
                        <select class="form-control selectMesExportar" name="mes_exportar" id="mes_exportar" required style="width: 100%;">
                           <option value="">Selección</option>
                           <?php
                              $consultaMes = "SHOW TABLES LIKE 'despachos_enc%'";
                              $resultadoMes = $Link->query($consultaMes);
                              if($resultadoMes->num_rows > 0){
                                 while($registros = $resultadoMes->fetch_assoc()) {
                                    $dataRegistros[] = array_values($registros);
                                 } 
                              }
                           ?>
                           <?php foreach ($dataRegistros as $key => $value): 
                              $mesesNumero = substr($value[0], 13, -2); 
                           ?>
                              <option id="<?= $mesesNumero ?>" value="<?= $mesesNumero; ?>"><?= $meses[$mesesNumero]; ?></option>
                           <?php endforeach ?>    
                        </select>
                     </div>
                  </div> <!-- col -->
                  <div class="col-md-4 col-sm-12">
                     <div class="form-group">
                        <label for="semana_exportar">Semana inicial 
                           <!-- <i class="fa fa-question-circle" style="cursor: pointer;" data-toggle="tooltip" data-placement="right" title=" Semana en la que se generará la plantilla o el informe, en el caso del informe es opcional "></i>  -->
                        </label>
                        <select class="form-control selectSemanaExportar" name="semana_exportar" id="semana_exportar" required style="width: 100%;">
                           <option value="">Selección</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-4 col-sm-12">
                     <div class="form-group">
                        <label for="semana_exportar_final">Semana final</label>
                        <select class="form-control selectSemanaExportarFinal" name="semana_exportar_final" id="semana_exportar_final" required style="width: 100%;">
                           <option value="">Selección</option>
                        </select>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-outline btn-sm" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary btn-sm" id="exportar_plantillaTrazabilidad">Aceptar</button>
         </div>
      </div> <!-- modal-content -->
   </div> <!-- modal-dialog -->
</div> <!-- modal -->

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
            <div class="ibox-title">
               <div class="row">
                  <div class="col-sm-11">
                     <h2 style="display:inline;">Parámetros de Consulta</h2>
                  </div>
                  <div class="col-sm-1" id="father">
                     <div class="" id="loaderAjax">
                        <i class="fas fa-spinner fa-pulse fa-3x fa-fw"></i>
                        <span class="sr-only">Loading...</span>
                     </div>
                  </div>
               </div>
            </div>  
            <div class="ibox-content contentBackground">
               <form class="form row" id="formBuscar" method="POST">
                  <?php if ($tipoBusqueda == 1): ?>
                     <div class="form-group col-md-3 col-sm-6">
                        <label>Fecha de </label>
                        <div class="row compositeDate">
                           <select name="fecha_de" id="fecha_de" class="form-control selectFechaDe " style="width: 100%;">
                              <option value="1">Elaboración documento</option>
                              <option value="2">Días Remisiones</option>
                              <option value="3">Días Ordenes</option>
                           </select>
                        </div>
                     </div> 
                     <div id="fechaDiasDespachos" style="display: none;">
                        <div class="form-group col-md-3 col-sm-6">
                           <label>Desde</label>
                           <div class="row compositeDate">
                              <div class="col-sm-8 nopadding">
                                 <select name="mes_inicio" id="mes_inicio" class="form-control selectMesInicio " style="width: 100%;">
                                    <?php foreach ($meses as $keyMeses => $valueMeses): ?>
                                       <?php if (isset($meses[$mes]) && $bandera == 0): ?>
                                          <?= ( $mes == $keyMeses ) ? $bandera = 1 : $bandera = 0 ?>
                                          <option value="<?= $keyMeses ?>" <?= ($keyMeses == $mes) ? 'selected' : ''?> > <?= $valueMeses ?> </option>
                                       <?php endif ?>
                                    <?php endforeach ?>
                                 </select>
                              </div>
                              <div class="col-sm-4 nopadding">
                                 <select name="dia_inicio" id="dia_inicio" class="form-control selectDiaInicio">
                                    <option value="">dd</option>
                                    <?php for ($i=1; $i <= 31; $i++) { ?>
                                       <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                           <label>Hasta</label>
                           <div class="row compositeDate">
                              <div class="col-sm-8 nopadding">
                                 <select name="mes_fin" id="mes_fin" class="mesFin" style="width: 100%;" >
                                    <?php foreach ($meses as $keyMeses => $valueMeses): ?>
                                       <option value="<?= $keyMeses ?>" <?= ($keyMeses == $mes) ? 'selected' : ''?> > <?= $valueMeses ?> </option>
                                    <?php endforeach ?>
                                 </select>
                                 <!-- <input type="text" id="nomMesFin" value="Espere..." class="form-control" readonly> -->
                                 <input type="hidden" name="mes_fin" id="mes_fin" value="01">
                              </div>
                              <div class="col-sm-4 nopadding">
                                 <select name="dia_fin" id="dia_fin" class="form-control selectDiaFin" style="width: 100%;">
                                    <option value="">dd</option>
                                    <?php for ($i=1; $i <= 31; $i++) { ?>
                                       <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </div> <!-- diasDespachados -->

                     <div id="fechaElaboracion">
                        <div class="form-group col-md-3 col-sm-6">
                           <label>Desde</label>
                           <input   type="text" 
                                    name="fecha_inicio_elaboracion" 
                                    id="fecha_inicio_elaboracion" 
                                    value="<?php echo date('Y')."-".$primerMes.""."-01" ?>"
                                    data-date-format="yyyy-mm-dd"  
                                    class="form-control datepicker_inicio text-center" max = "<?= $last_day ?>">
                        </div>
                        <div class="form-group col-md-3 col-sm-6">
                           <label>Hasta</label>
                           <?php $mesSiguiente = date('m')+1; ?>
                           <input   type="text" 
                                    name="fecha_fin_elaboracion" 
                                    id="fecha_fin_elaboracion" 
                                    data-date-format="yyyy-mm-dd" 
                                    value="" 
                                    class="form-control datepicker_fin text-center" >
                        </div>
                     </div>
                  <?php endif ?>
                  <?php if ($tipoBusqueda == "2"): ?>
                     <div class="col-md-3 col-sm-6 ">
                        <label for="numeroEntrega">Número Entrega</label>
                        <select id="numeroEntrega" name="numeroEntrega" class="form-control" style="width: 100%;">
                           <?php foreach ($numeroEntrega as $mes => $entrega): ?>
                              <option value="<?= $mes; ?>"><?= $entregaNom[$entrega]; ?></option>
                           <?php endforeach ?>
                        </select>
                     </div> <!-- col -->
                  <?php endif ?>

                  <div class="form-group col-md-3 col-sm-6">
                     <label>Municipio</label>
                     <select class="form-control selectMunicipio" name="municipio" id="municipio" style="width: 100%;">
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

                  <div class="form-group col-md-3 col-sm-6">
                     <label>Tipo documento</label>
                     <select name="tipo_documento" id="tipo_documento" class="form-control selectTipoDocumento" style="width: 100%;">
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

                  <div class="form-group col-md-3 col-sm-6">
                     <label>Proveedor/Responsable</label>
                     <select name="proveedor" id="proveedor" class="form-control selectProveedor" style="width: 100%;">
                        <option value="">Seleccione tipo documento</option>
                     </select>
                  </div>
            
                  <div class="form-group col-md-3 col-sm-6" id="divsubFiltro" style="display : none">
                     <label>Filtrar por  </label>
                     <select name="tipo_filtro" id="tipo_filtro" class="form-control selectTipoFiltro " style="width: 100%;">
                        <option value="" >Seleccione...</option>
                        <option value="1">Bodega</option>
                        <option value="2">Conductor</option>
                        <option value="3">Producto</option>
                        <option value="4">Grupo etario</option>
                        <option value="5">Tipo complemento</option>
                        <!-- <option value="6">Fecha vencimiento</option> -->
                     </select>
                  </div>

                  <div id="divBodegas" style="display: none;">
                     <div class="form-group col-md-3 col-sm-6">
                        <label>Tipo bodega  </label>
                        <select name="tipo_bodega" id="tipo_bodega" class="form-control selectTipoBodega" style="width: 100%;">
                           <option value="">Todas</option>
                           <option value="1">Bodega origen</option>
                           <option value="2">Bodega destino</option>
                        </select>
                     </div>
                     <div class="form-group col-md-3 col-sm-6">
                        <label>Bodegas  </label>
                        <select name="bodegas" id="bodegas" class="form-control selectBodegas" style="width: 100%;"> </select>
                     </div>
                  </div>

                  <div class="form-group col-md-3 col-sm-6" id="divConductores" style="display: none;">
                     <label>Conductor </label>
                     <select name="conductor" id="conductor" class="form-control selectCoductores" style="width: 100%;"> </select>
                  </div>

                  <div id="divProductos" style="display: none;">
                     <div class="form-group col-md-3 col-sm-6">
                        <label>Producto </label>
                        <select name="producto" id="producto" class="form-control selectProductos" style="width: 100%;">
                           <option value="">Cargando...</option>
                        </select>
                     </div>
                     <div class="form-group col-sm-3 radio">
                        <label>Ver por </label>
                        <div class="radio" >
                           <label><input type="checkbox" name="totales" id="totales" value="1" > Totales</label>
                        </div>
                     </div>
                  </div>

                  <div class="form-group col-md-3 col-sm-6" id="divGrupoEtario" style="display: none;">
                     <label>Grupo Etario</label>
                     <select name="grupo_etario" id="grupo_etario" class="form-control selectGrupoEtario" style="width: 100%;"> </select>
                  </div>

                  <div class="form-group col-md-3 col-sm-6" id="divTipoComplemento" style="display: none;">
                     <label>Tipo de complemento</label>
                     <select name="tipo_complemento" id="tipo_complemento" class="form-control selectTipoComplemento" style="width: 100%;">
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
                     <div class="form-group col-md-3 col-sm-6">
                        <label>Desde : </label>
                        <input type="date" name="fechavto_desde" class="form-control">
                     </div>
                     <div class="form-group col-md-3 col-sm-6">
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
                        <button class="btn btn-danger" onclick="limpiarFormulario()" id="btnBuscar"> <span class="fa fa-times"></span>  Limpiar búsqueda</button>
                     <?php endif ?>
                  </div>
               </div>
            </div> <!-- contentBackground -->
         </div> <!-- float-e-margins -->
      </div> <!-- col-lg-12 -->
   </div> <!-- row -->

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
                           <th>Fecha Sacrificio</th>
                           <th>Fecha Empaque</th>
                           <th>Codigo Interno</th>
                           <th>Observacion</th>
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
                           <th>Fecha Sacrificio</th>
                           <th>Fecha Empaque</th>
                           <th>Codigo Interno</th>
                           <th>Observacion</th>
                        </tr>
                     </tfoot>
                  </table>
               </div> <!-- table-responsive -->
               <?php
                  if (!isset($_POST['buscar'])) { //Si no hay filtrado
                     $numtabla = $mesTablaInicio.$_SESSION['periodoActual'];
                     $consulta = "SELECT  pmov.Tipo, 
                                          pmov.Numero, 
                                          pmov.FechaMYSQL, 
                                          pmov.fecha_despacho, 
                                          pmov.Nombre as Proveedor, 
                                          pmovdet.Descripcion, 
                                          pmovdet.Umedida, 
                                          FORMAT(pmovdet.Cantidad, 4) as Cantidad, 
                                          bodegas.NOMBRE as nomBodegaOrigen, 
                                          IF(pmov.TipoTransporte = '', '', (SELECT Nombre FROM tipovehiculo WHERE Id = pmov.TipoTransporte)) AS TipoTransporte,  
                                          pmov.Placa, 
                                          pmov.ResponsableRecibe, 
                                          pmovdet.Lote, 
                                          pmovdet.FechaVencimiento, 
                                          pmovdet.Marca, 
                                          pmovdet.fecha_sacrificio, 
                                          pmovdet.fecha_empaque, 
                                          pmovdet.codigo_interno, 
                                          pmovdet.observacion,
                                          (SELECT NOMBRE FROM bodegas WHERE ID = pmovdet.BodegaDestino) AS  nomBodegaDestino
                                       FROM productosmov$numtabla AS pmov
                                       INNER JOIN productosmovdet$numtabla AS pmovdet ON pmov.Numero = pmovdet.Numero
                                       INNER JOIN bodegas ON bodegas.ID = pmovdet.BodegaOrigen
                                       LIMIT 2000;";
                  } else if (isset($_POST['buscar'])) { //Si hay filtrado
                     $condicionFvto = "";
                     $condiciones = "";
                     $datos=" pmov.Tipo, 
                              pmov.Numero, 
                              pmov.FechaMYSQL, 
                              pmov.fecha_despacho, 
                              pmov.Nombre as Proveedor, 
                              pmovdet.Descripcion, 
                              pmovdet.Umedida, ";
                              ($_POST['tipo_filtro'] == 4 ) ? $datos.= " FORMAT(pmovdet.Cantidad, 4) as Cantidad, " : $datos.= " FORMAT(pmovdet.Cantidad, 4) as Cantidad, ";
                     $datos.= "bodegas.NOMBRE as nomBodegaOrigen, 
                              IF(pmov.TipoTransporte = '', '', (SELECT Nombre FROM tipovehiculo WHERE Id = pmov.TipoTransporte)) AS TipoTransporte, 
                              pmov.Placa, 
                              pmov.ResponsableRecibe, 
                              pmovdet.Lote, 
                              pmovdet.FechaVencimiento, 
                              pmovdet.Marca, 
                              pmovdet.fecha_sacrificio, 
                              pmovdet.fecha_empaque, 
                              pmovdet.codigo_interno, 
                              pmovdet.observacion,
                              (SELECT NOMBRE FROM bodegas WHERE ID = pmovdet.BodegaDestino) AS  nomBodegaDestino ";

                     if (isset($_POST['fecha_de']) && $_POST['fecha_de'] != "") {
                        $fecha_de = $_POST['fecha_de'];
                        if ($fecha_de == 1) {
                           $mes = date("m", strtotime($_POST['fecha_inicio_elaboracion']));
                           $numtabla = $mes.$_SESSION['periodoActual'];
                           $condiciones.=" AND pmov.FechaMYSQL > '".$_POST['fecha_inicio_elaboracion']." 00:00:00' AND pmov.FechaMYSQL < '".$_POST['fecha_fin_elaboracion']." 00:00:00' ";
                        } else if ($fecha_de == 2) { //Si el tipo de búsqueda es por días despachados
                           $numtabla = $_POST['mes_inicio'].$_SESSION['periodoActual'];
                           if ($_POST['dia_inicio'] != "" && $_POST['dia_fin'] != "" ) { //Si los días indicados son diferente a vacío, busca por los días despachados tomado del campo Dias en la tabla despachos_enc
                              $cnt2 = 0;
                              $condiciones.=" AND (";
                              for ($i=$_POST['dia_inicio']; $i <= $_POST['dia_fin'] ; $i++) {
                                 if ($cnt2 > 0) {
                                    $condiciones.=" OR ";
                                 }
                                 $auxi = ($i < 10) ? '0'.$i : $i;
                                 $condiciones.= " pmov.Numero IN (SELECT Num_Doc FROM despachos_enc$numtabla denc WHERE denc.Dias LIKE '".$auxi."' OR denc.Dias LIKE '".$auxi.",%' OR denc.Dias like '%,".$auxi.",%' OR denc.Dias like '%,".$auxi."' )";
                                 $cnt2++;
                              }
                              $condiciones.=") ";
                           }
                        } else if ($fecha_de == 3) {
                           $numtabla = $_POST['mes_inicio'].$_SESSION['periodoActual'];
                           if ($_POST['dia_inicio'] != "" && $_POST['dia_fin'] != "" ) { //Si los días indicados son diferente a vacío, busca por los días despachados tomado del campo Dias en la tabla despachos_enc
                              $cnt2 = 0;
                              $condiciones.=" AND (";
                              for ($i=$_POST['dia_inicio']; $i <= $_POST['dia_fin'] ; $i++) {
                                 if ($cnt2 > 0) {
                                    $condiciones.=" OR ";
                                 }
                                 $auxi = ($i < 10) ? '0'.$i : $i;
                                 $condiciones.= " pmov.Numero IN (SELECT Num_Doc FROM orden_compra_enc$numtabla denc WHERE denc.Dias LIKE '".$auxi."' OR denc.Dias LIKE '".$auxi.",%' OR denc.Dias like '%,".$auxi.",%' OR denc.Dias like '%,".$auxi."' )";
                                 $cnt2++;
                              }
                              $condiciones.=") ";
                           }
                        }
                     }
                     // echo "$condiciones";
                     if ($tipoBusqueda == "2" && $_POST['numeroEntrega'] != "") {
                        $numtabla = $_POST['numeroEntrega'].$_SESSION['periodoActual'];
                        $mesEntrega = $_POST['numeroEntrega'];
                        $mesEntregaAnterior = $mesEntrega - 1;
                        $mesEntregaSiguiente = $mesEntrega + 1;  
                        $mesEntregaAnterior = "0".$mesEntregaAnterior;
                        $mesEntregaSiguiente = "0".$mesEntregaSiguiente;
                        $condiciones .= " AND pmov.Numero IN (SELECT Num_Doc FROM despachos_enc$numtabla denc) AND ( MONTH(denc.FechaHora_Elab) = '" .$_POST['numeroEntrega']. "' OR MONTH(denc.FechaHora_Elab) = '" .$mesEntregaAnterior."' OR  MONTH(denc.FechaHora_Elab) = '".$mesEntregaSiguiente."' )";
                     }

                     if (isset($_POST['tipo_documento']) && $_POST['tipo_documento'] != "") { //Si el tipo de documento se especificó
                        if ($_POST['proveedor'] != "") { //Si el proveedor se especificó, busca según las bodegas relacionadas
                           $condiciones.=" AND pmov.Tipo = '".$_POST['tipo_documento']."' AND pmov.Nitcc = '".$_POST['proveedor']."' ";
                        } else { //Si no especificó, trae todos los registros con el tipo de documento escogido
                           $condiciones.=" AND pmov.Tipo = '".$_POST['tipo_documento']."' ";
                        }
                     }

                     if (isset($_POST['municipio']) && $_POST['municipio'] != "") {
                        $condiciones.=" AND bodegas.ciudad = '".$_POST['municipio']."' ";
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
                                    $datos ="   '".$txtTotales."' AS Tipo, 
                                                '".$txtTotales."' AS Numero, 
                                                '".$txtTotales."' AS FechaMYSQL, 
                                                '".$txtTotales."' AS fecha_despacho,
                                                '".$txtTotales."' AS Proveedor, 
                                                pmovdet.Descripcion, 
                                                pmovdet.Umedida, 
                                                SUM(pmovdet.Cantidad) AS Cantidad,  
                                                '".$txtTotales."' AS nomBodegaOrigen, 
                                                '".$txtTotales."' AS nomBodegaDestino,  
                                                '".$txtTotales."' AS TipoTransporte, 
                                                '".$txtTotales."' AS Placa, 
                                                '".$txtTotales."' AS ResponsableRecibe, 
                                                '".$txtTotales."' AS Lote, 
                                                '".$txtTotales."' AS FechaVencimiento, 
                                                '".$txtTotales."' AS Marca,
                                                '".$txtTotales."' AS fecha_sacrificio, 
                                                '".$txtTotales."' AS fecha_empaque, 
                                                '".$txtTotales."' AS codigo_interno, 
                                                '".$txtTotales."' AS observacion  ";
                                 } else { //Si hay criterios, muestra los resultados agrupados
                                    $datos=" pmov.Tipo, 
                                             pmov.Numero, 
                                             pmov.FechaMYSQL, 
                                             pmov.fecha_despacho, 
                                             pmov.Nombre as Proveedor, 
                                             pmovdet.Descripcion, 
                                             pmovdet.Umedida, 
                                             FORMAT(SUM(pmovdet.Cantidad), 4) as Cantidad, 
                                             bodegas.NOMBRE as nomBodegaOrigen, 
                                             (SELECT NOMBRE FROM bodegas WHERE ID = pmovdet.BodegaDestino) AS  nomBodegaDestino, 
                                             IF(pmov.TipoTransporte = '', '', (SELECT Nombre FROM tipovehiculo WHERE Id = pmov.TipoTransporte)) AS TipoTransporte,  
                                             pmov.Placa, 
                                             pmov.ResponsableRecibe, 
                                             pmovdet.Lote, 
                                             pmovdet.FechaVencimiento, 
                                             pmovdet.Marca, 
                                             pmovdet.fecha_sacrificio, 
                                             pmovdet.fecha_empaque, 
                                             pmovdet.codigo_interno, 
                                             pmovdet.observacion  ";
                                 }
                                 $condiciones.=" AND pmovdet.CodigoProducto = '".$_POST['producto']."' GROUP BY pmovdet.CodigoProducto ";
                              } else { // Si no se especificó ver por totales, muestra cada uno de los despachos del producto
                                 $condiciones.=" AND pmovdet.CodigoProducto = '".$_POST['producto']."' ";
                              }
                           } 
                        } else if ($filtro == 4) {
                           if (isset($_POST['fecha_de']) && $_POST['fecha_de'] != "") {
                              $fecha_de = $_POST['fecha_de'];
                              if ($fecha_de == 2) { //Si el tipo de búsqueda es por días despachados
                                 $numtabla = $_POST['mes_inicio'].$_SESSION['periodoActual'];
                                 $condiciones.=" AND (";
                                 $condiciones.= " pmovdet.Numero IN (SELECT Num_Doc FROM despachos_det$numtabla denc WHERE denc.Id_GrupoEtario = '" .$_POST['grupo_etario']. "' )";
                                 $condiciones.=") ";
                              } else if ($fecha_de == 3) {
                                 $numtabla = $_POST['mes_inicio'].$_SESSION['periodoActual'];
                                 $condiciones.=" AND (";
                                 $condiciones.= " pmovdet.Numero IN (SELECT Num_Doc FROM orden_compra_det$numtabla denc WHERE denc.Id_GrupoEtario = '" .$_POST['grupo_etario']. "' )";
                                 $condiciones.=") ";
                              }
                           }
                        } else if ($filtro == 5) {
                           if (isset($_POST['tipo_complemento']) && $_POST['tipo_complemento'] != "") {
                              $condiciones.=" AND pmov.Complemento = '".$_POST['tipo_complemento']."' ";
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

                     $consulta = "SELECT $datos
                                    FROM productosmov$numtabla AS pmov
                                    INNER JOIN productosmovdet$numtabla AS pmovdet ON pmov.Numero = pmovdet.Numero $condicionFvto
                                    INNER JOIN bodegas ON bodegas.ID = pmovdet.BodegaOrigen
                                    WHERE 1 = 1 $condiciones
                                    LIMIT 2000; ";     
                  }
                  // echo "$consulta";  
               ?>
               <input type="hidden" name="consulta" id="consulta" value="<?php echo $consulta; ?>">
            </div><!-- /.ibox-content -->
         </div><!-- /.ibox float-e-margins -->
      </div><!-- /.col-lg-12 -->
   </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<?php include '../../footer.php'; ?>

<!-- Mainly scripts -->
<script src="<?= $baseUrl; ?>/theme/js/jquery-3.1.1.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?= $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/jasny/jasny-bootstrap.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/toastr/toastr.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/iCheck/icheck.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/steps/jquery.steps.min.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/datapicker/bootstrap-datepicker.js"></script>
<script src="<?= $baseUrl; ?>/theme/js/plugins/select2/select2.full.min.js"></script>

<!-- Section Scripts -->
<script src="<?= $baseUrl; ?>/modules/trazabilidad/js/trazabilidad.js"></script>

<script type="text/javascript">

   var buttonCommon = {
      exportOptions: {
         format: {
            body: function ( data, row, column, node ) {
               console.log(data)
               return column === 7 ?
               data.replace( ',', '.' ) :
               data;
            }
         }
      }
   };

   dataset1 = $('#tablaTrazabilidad').DataTable({
      ajax: {
         method: 'POST',
         url: 'functions/fn_trazabilidad_obtener_datos_tabla.php',
         data:{
            consulta: $('#consulta').val()
         }
      },
      // columnWidth:200,
      columns:[
         { data: 'Tipo', resize:false },
         { data: 'Numero'},
         { data: 'FechaMYSQL'},
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
         { data: 'fecha_sacrificio'},
         { data: 'fecha_empaque'},
         { data: 'codigo_interno'},
         { data: 'observacion'},
      ],
      pageLength: 25,
      responsive: true,
      dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
      buttons : [
         $.extend( true, {}, buttonCommon, {
            extend: 'excel',
            title:'Trazabilidad_alimentos', 
            className:'btnExportarExcel', 
            exportOptions: {columns : [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19]}
         } ),
         $.extend( true, {}, buttonCommon, {
            extend: 'excelHtml5'
         } ),
         $.extend( true, {}, buttonCommon, {
            extend: 'pdfHtml5'
         } )
      ],
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
         var btnAcciones = '<div class="dropdown pull-right" id=""> ' +
                              '<button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button>'+
                              '<ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">'+
                                 '<li><a onclick="$(\'.btnExportarExcel\').click()"><span class="fa fa-file-excel-o"></span> Exportar Tabla </a></li>'+
                                 '<li><a href="#" id="descargarPlantillaTrazabilidadRutas"><i class="fa fa-download"></i> Descargar Plantilla Rutas</a></li>'+
                                 '<li><a href="#" id="descargarPlantillaTrazabilidadDetalle"><i class="fa fa-download"></i> Descargar Plantilla Detalle</a></li>'+
                                 '<li><a href="#" id="informeTrazabilidadRutas"><i class="fa fa-file-excel-o"></i>  Informe Trazabilidad Rutas</a></li>'+
                                 '<li><a href="#" id="informeTrazabilidadDetalle"><i class="fa fa-file-excel-o"></i>  Informe Trazabilidad Detalle</a></li>'+
                              '</ul>'+
                           '</div>';
         $('.containerBtn').html(btnAcciones);
         $('#loader').fadeOut();
      },
      preDrawCallback: function( settings ) {
         $('#loader').fadeIn();
      }
   }).on("draw", function(){ 
      $('#loader').fadeOut();
   });

</script>

<?php mysqli_close($Link); ?>

</body>
</html>