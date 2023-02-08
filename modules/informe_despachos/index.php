<?php
    require_once '../../header.php';

    if ($permisos['informes'] == "0") {
        ?><script type="text/javascript">
              window.open('<?= $baseUrl ?>', '_self');
          </script>
        <?php exit(); }
              else {
                ?><script type="text/javascript">
                  const list = document.querySelector(".li_informes");
                  list.className += " active ";
                </script>
                <?php
                }
    $periodoActual = $_SESSION['periodoActual'];
    $cantGrupoEtario = $_SESSION['cant_gruposEtarios'];
    $titulo = 'Informe de despacho por día';

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

    $consultam= "SHOW TABLES LIKE 'despachos_enc%' ";  

 
    $respuesta=$Link->query($consultam);

    if ($respuesta->num_rows > 0) {
        while ($data = $respuesta->fetch_assoc()) {
        $aux = array_values($data);
        $aux=substr(($aux[0]), 13, -2);
        $mess[]=$aux;   
    }
}

$ultimoMes = '';
$consultaTablas = " SELECT
                        table_name AS tabla
                    FROM
                        information_schema.tables
                    WHERE
                      table_schema = DATABASE() AND table_name like 'despachos_enc%'
                    ORDER BY table_name DESC LIMIT 1 ";
$resultadoTablas = $Link->query($consultaTablas);
    if ($resultadoTablas->num_rows > 0) {
        while ($data = $resultadoTablas->fetch_assoc()) {
            $mes = str_replace("despachos_enc", "", $data['tabla']);
            $mes = str_replace($_SESSION['periodoActual'], "", $mes);
            $mesTablaF = $mes;
            $tabla = $mes.$_SESSION['periodoActual'];
            $ultimoMes = $mes;
        }
    }

    $nameLabel = get_titles('informes', 'informeDespachodia', $labels);
    $titulo = $nameLabel;
?>


<style type="text/css">
    .select2-container--open {
        z-index: 9999999
    }
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
                <form class="form row" id="formBuscar" method="POST">
                    <div class="form-group col-md-3 col-sm-6">
                        <label>Municipio</label>
                        <select class="form-control selectMunicipio" name="municipio" id="municipio" style="width: 100%;">
                            <option value="">Seleccionar</option>
                            <?php
                                $codigo_departamento = $_SESSION['p_CodDepartamento'];
                                $codigo_municipio = $_SESSION["p_Municipio"];
                                $condicion_municipio = ($codigo_municipio == 0) ? " AND CodigoDANE LIKE '$codigo_departamento%' " : " AND CodigoDANE = '$codigo_municipio' ";
                                $consultarMunicipios =   "SELECT
                                                            CodigoDANE, Ciudad
                                                        FROM
                                                            ubicacion
                                                        WHERE 1=1
                                                            $condicion_municipio
                                                        GROUP BY ubicacion.Ciudad ";
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
                        <label>Institución</label>
                            <select name="institucion" id="institucion" class="form-control selectInstitucion" style="width: 100%;">
                                <option value="">Seleccionar</option>
                                    <?php
                                        $consultarInstituciones =   "SELECT 
                                                                        codigo_inst, nom_inst 
                                                                    FROM
                                                                        instituciones";
                                                                    $resultadoInstituciones = $Link->query($consultarInstituciones);
                                        if ($resultadoInstituciones->num_rows > 0) {
                                            while ($instituciones = $resultadoInstituciones->fetch_assoc()) { ?>
                                                <option value="<?= $instituciones['codigo_inst']?>" <?= (isset($_POST['institucion']) && $_POST['institucion'] == $instituciones['codigo_inst']) ? 'selected' : ''?> > <?= $instituciones['nom_inst'] ?> </option>
                                    <?php }
                                       }
                                    ?>
                            </select>
                    </div>
 
                    <div class="form-group col-md-3 col-sm-6">
                        <label>Sede</label>
                        <select class="form-control selectSede" name="sede" id="sede" style="width: 100%;">
                            <option value="">Seleccionar</option>
                                <?php
                                $instituciones = $_POST['institucion'];
                                $consultarSedes =   "SELECT 
                                                        cod_sede, nom_sede 
                                                    FROM
                                                        sedes$periodoActual where cod_inst='$instituciones'";
                                $resultadoSedes = $Link->query($consultarSedes);
                                
                                if ($resultadoSedes->num_rows > 0) {
                                    while ($sedes = $resultadoSedes->fetch_assoc()) { ?>
                                        <option value="<?= $sedes['cod_sede']?>" <?= (isset($_POST['sede']) && $_POST['sede'] == $sedes['cod_sede']) ? 'selected' : ''?> > <?= $sedes['nom_sede'] ?> </option>
                                <?php }
                                    }
                                ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-sm-6">
                        <label>Complemento</label>
                            <select class="form-control selectComplem" name="complemento" id="complemento" style="width: 100%;">
                                <option value="">Seleccionar</option>
                                <?php 
                                    $consultarComplemento =     "SELECT     
                                                                    CODIGO, DESCRIPCION
                                                                from
                                                                    tipo_complemento";
                                   $resultadoComplemento = $Link->query($consultarComplemento);
                                    if ($resultadoComplemento->num_rows > 0) {
                                        while ($compl = $resultadoComplemento->fetch_assoc()) { ?>
                                            <option value="<?= $compl['CODIGO']?>" <?= (isset($_POST['complemento']) && $_POST['complemento'] == $compl['CODIGO']) ? 'selected' : ''?> > <?= $compl['CODIGO']?> </option>
                                <?php }
                                   } 
                                ?>
                            </select>
                    </div>

                    <div class="form-group col-md-3 col-sm-6">
                        <label>Rutas</label>
                        <select class="form-control selectRuta" name="ruta" id="ruta" style="width: 100%;">
                            <option value="">Seleccionar</option>
                                <?php 
                                    $consultarRutas =   "SELECT
                                                            ID, Nombre
                                                        from
                                                            rutas";
                                    $resultadoRutas = $Link->query($consultarRutas);
                                    if ($resultadoRutas->num_rows > 0) {
                                        while ($ruta = $resultadoRutas->fetch_assoc()) {  ?>
                                            <option value="<?= $ruta['ID']?>" <?= (isset($_POST['ruta']) && $_POST['ruta'] == $ruta['ID']) ? 'selected' : ''?> > <?= $ruta['Nombre'] ?> </option>
                                <?php }
                                    }
                                ?>
                        </select>
                    </div>

                    <div class="form-group col-md-3 col-sm-6">
                        <label>Mes</label>
                            <select class="form-control selectMes" name="mes" id="mes" style="width: 100%;">
                                <!-- <?php foreach ($meses as $keyMeses => $valueMeses): ?> -->
                                <?php if (isset($meses[$mes]) && $bandera == 0): ?>
                                    <?= ( $mes == $keyMeses ) ? $bandera = 1 : $bandera = 0 ?>
                                        <option value="<?= $keyMeses ?>" <?= ($keyMeses == $mes) ? 'selected' : ''?> > <?= $valueMeses ?> </option>      
                                    <?php endif ?>
                                <!-- <?php endforeach ?> -->
                            </select>
                    </div>
   
                  <!-- <input type="hidden" name="buscar" value="1"> -->
                    <div class="form-group col-md-3 col-sm-6">
                        <label>Semana</label>
                        <select class="form-control selectSemana" name="semana" id="semana" style="width: 100%;">
                            <option value="">Seleccionar</option>
                                <?php 
                                    $consultarSemanas =     " SELECT DISTINCT 
                                                                SEMANA
                                                            FROM 
                                                                planilla_semanas
                                                            where 
                                                                MES_DESPACHO ='$mes' ";
                                    $resultadoSemanas = $Link->query($consultarSemanas);

                                    if ($resultadoSemanas->num_rows > 0) {
                                        while ($semanas = $resultadoSemanas->fetch_assoc()) { ?>
                                            <option value="<?= $semanas['SEMANA']?>" <?= (isset($_POST['semana']) && $_POST['semana'] == $semanas['SEMANA']) ? 'selected' : ''?> > <?= $semanas['SEMANA'] ?> </option>
                                <?php }
                                    }
                                ?>
                        </select> 
                    </div>
         
                    <div class="form-group col-md-3 col-sm-6">
                        <label>Dias</label>
                            <select class="form-control selectDia" name="dia" id="dia" style="width: 100%;">
                                <option value="">Seleccionar</option>
                                    <?php 
                                        $condicionSemana = '';
                                        if (isset($_POST['semana']) && $_POST['semana'] != '' ) {
                                            $condicionSemana = " AND SEMANA_DESPACHO = '" .$_POST['semana']. "'";
                                        }
                                        $consultarDias =    " SELECT DISTINCT 
                                                                DIA
                                                            FROM 
                                                                planilla_semanas
                                                            WHERE 1=1  $condicionSemana
                                                                ";
                                        $resultadoDias = $Link->query($consultarDias);
                                        if ($resultadoDias->num_rows > 0) {
                                            while ($dias = $resultadoDias->fetch_assoc()) { ?>
                                                <option value="<?= $dias['DIA']?>" <?= (isset($_POST['dia']) && $_POST['dia'] == $dias['DIA']) ? 'selected' : ''?> > <?= $dias['DIA'] ?> </option>
                                    <?php }
                                        }
                                    ?>
                            </select>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary"  id="btnBuscar" name="btnBuscar" value="1"> <span class="fa fa-search"></span>  Buscar</button>
                                <?php if (isset($_POST['btnBuscar'])): ?>
                                    <button class="btn btn-primary" type="reset" onclick="location.href='index.php';" id="btnBuscar2"> <span class="fa fa-times"></span>  Limpiar búsqueda</button>
                                <?php endif ?>
                        </div>
                    </div>
                </form>
            </div> <!-- contentBackground -->
         </div> <!-- float-e-margins -->
      </div> <!-- col-lg-12 -->
   </div> <!-- row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content contentBackground">
                    <div class="table-responsive">
                        <?php
                            if (!isset($_POST['btnBuscar'])) { //Si no hay filtrado
                        ?>
                            <table class="table" id="tabladespachodia">
                            <thead>
                                <tr>
                                    <th>Ciudad</th>
                                    <th>Código Institución</th>
                                    <th>Nombre Institución</th>
                                    <th>Código Sede</th>
                                    <th>Nombre Sede</th>
                                    <th>Tipo Complemento</th>
                                    <th>Semanas</th>
                                    <th>Dias</th>
                                    <th>Cobertura</th>
                                    <?php  
                                        for ($i=1; $i <=$cantGrupoEtario ; $i++) { 
                                    ?>
                                        <th>Cobertura<?= $i; ?></th>
                                    <?php       
                                      }
                                    ?>

                                    <?php 
                                        $consultaDiasMes = " SELECT DISTINCT(DIA) FROM planilla_semanas WHERE MES = '" .$ultimoMes. "'";
                                        $respuestaDiaMes = $Link->query($consultaDiasMes);
                                        if ($respuestaDiaMes->num_rows > 0) {
                                            while ($dataDias = $respuestaDiaMes->fetch_assoc()) {
                                                $diasR[] = $dataDias;
                                            }
                                        }
                                  
                                        foreach ($diasR as $keyD => $diaR) {

                                    ?>
                                            <th>D<?= $diaR['DIA']; ?></th>
                                    <?php 
                                        }
                                    ?>
                                </tr>
                            </thead>
                                <tbody id="tBodydesdia"> </tbody>
                                <tfoot>
                                    <tr>
                                       <th>Ciudad</th>
                                       <th>Código Institución</th>
                                       <th>Nombre Institución</th>
                                       <th>Código Sede</th>
                                       <th>Nombre Sede</th>
                                       <th>Tipo Complemento</th>
                                       <th>Semanas</th>
                                       <th>Dias</th>
                                       <th>Cobertura</th>
                                       <?php  
                                          for ($i=1; $i <=$cantGrupoEtario ; $i++) { 
                                       ?>
                                             <th>Cobertura<?= $i; ?></th>
                                       <?php       
                                          }
                                       ?>
                                       <?php 
                                          foreach ($diasR as $keyD => $diaR) {
                                          
                                       ?>
                                             <th>D<?= $diaR['DIA']; ?></th>
                                       <?php 
                                          }
                                       ?>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php         
                                $consulta =     " SELECT u.Ciudad,
                                                s.cod_inst,
                                                s.nom_inst, 
                                                d.cod_Sede, 
                                                s.nom_sede, 
                                                d.Tipo_Complem, 
                                                d.Semana, 
                                                d.Dias, 
                                                d.Cobertura, ";  

                                                for ($i=1; $i <=$cantGrupoEtario ; $i++) { 
                                                    $consulta .= " d.Cobertura_G".$i.", ";
                                                } 

                                                foreach ($diasR as $keyD => $diaR) {
                                                    $aux = $diaR['DIA'];
                                                    $consulta .= " if(dias like '%$aux%', cobertura, 0) AS 'D$aux', "; 
                                                }
                                                $consulta = trim($consulta,', ');

                                                $consulta .= " 
                                                    FROM 
                                                        despachos_enc$tabla d
                                                    join 
                                                        sedes$periodoActual s on d.cod_sede = s.cod_sede 
                                                    inner join 
                                                        ubicacion u on (s.cod_mun_sede=u.`CodigoDANE`) 
                                                    where
                                                        d.Estado != 0";

                            } else if (isset($_POST['btnBuscar'])) { //Si hay filtrado
                            ?>    
                                <table class="table" id="tabladespachodia">
                                    <thead>
                                        <tr>
                                            <th>Ciudad</th>
                                            <th>Código Institución</th>
                                            <th>Nombre Institución</th>
                                            <th>Código Sede</th>
                                            <th>Nombre Sede</th>
                                            <th>Tipo Complemento</th>
                                            <th>Semanas</th>
                                            <th>Dias</th>
                                            <th>Cobertura</th>
                                            <?php  
                                                for ($i=1; $i <=$cantGrupoEtario ; $i++) { 
                                            ?>
                                                    <th>Cobertura<?= $i; ?></th>
                                            <?php       
                                                }
                                            ?>

                                            <?php 
                                                $consultaDiasMes = " SELECT DISTINCT(DIA) 
                                                                        FROM planilla_semanas WHERE MES_DESPACHO = '" .$_POST['mes']. "'";
                                                if (isset($_POST['semana']) && $_POST['semana'] != '') {
                                                    $consultaDiasMes .= " AND SEMANA_DESPACHO = '" .$_POST['semana']. "'";
                                                    if (isset($_POST['dia']) && $_POST['dia'] != '' ) {
                                                        $consultaDiasMes .= " AND DIA = '" .$_POST['dia']. "'";
                                                    }
                                                }
                                                $respuestaDiaMes = $Link->query($consultaDiasMes);
                                                if ($respuestaDiaMes->num_rows > 0) {
                                                    while ($dataDias = $respuestaDiaMes->fetch_assoc()) {
                                                        $diasR[] = $dataDias;
                                                    }
                                                }
                                  
                                                foreach ($diasR as $keyD => $diaR) {
                                            ?>
                                                    <th>D<?= $diaR['DIA']; ?></th>
                                            <?php 
                                                }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody id="tBodydesdia"> </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Ciudad</th>
                                            <th>Código Institución</th>
                                            <th>Nombre Institución</th>
                                            <th>Código Sede</th>
                                            <th>Nombre Sede</th>
                                            <th>Tipo Complemento</th>
                                            <th>Semanas</th>
                                            <th>Dias</th>
                                            <th>Cobertura</th>
                                            <?php  
                                                for ($i=1; $i <=$cantGrupoEtario ; $i++) { 
                                            ?>
                                                    <th>Cobertura<?= $i; ?></th>
                                            <?php       
                                                }
                                            ?>
                                            <?php 
                                                foreach ($diasR as $keyD => $diaR) {
                                          
                                            ?>
                                                    <th>D<?= $diaR['DIA']; ?></th>
                                            <?php 
                                                }
                                            ?>
                                        </tr>
                                    </tfoot>
                                </table>
                                <?php 
                                    $tabla = "despachos_enc".$_POST['mes'].$_SESSION['periodoActual'];
                                       $consulta =  " SELECT u.Ciudad,
                                                    s.cod_inst,
                                                    s.nom_inst, 
                                                    d.cod_Sede, 
                                                    s.nom_sede, 
                                                    d.Tipo_Complem, 
                                                    d.Semana, 
                                                    d.Dias, 
                                                    d.Cobertura, ";  

                                                    for ($i=1; $i <=$cantGrupoEtario ; $i++) { 
                                                        $consulta .= " d.Cobertura_G".$i.", ";
                                                    } 

                                                    foreach ($diasR as $keyD => $diaR) {
                                                        $aux = $diaR['DIA'];
                                                        $consulta .= " if(dias like '%$aux%', cobertura, 0) AS 'D$aux', "; 
                                                    }
                                                    $consulta = trim($consulta,', ');

                                                    $consulta .= " 
                                                FROM 
                                                        $tabla d
                                                INNER JOIN 
                                                        sedes$periodoActual s on d.cod_sede = s.cod_sede 
                                                INNER JOIN 
                                                        ubicacion u on (s.cod_mun_sede=u.`CodigoDANE`) 
                                                WHERE
                                                        d.Estado != 0";

                                    if (isset($_POST['municipio']) && $_POST['municipio'] != '') {
                                        $consulta .= " AND u.CodigoDANE = " .$_POST['municipio'];
                                    }

                                    if (isset($_POST['institucion']) && $_POST['institucion'] != '') {
                                        $consulta .= " AND s.cod_inst = " .$_POST['institucion'];
                                    }

                                    if (isset($_POST['sede']) && $_POST['sede'] != '') {
                                        $consulta .= " AND d.cod_Sede = " .$_POST['sede'];
                                    }

                                    if (isset($_POST['complemento']) && $_POST['complemento'] != '') {
                                        $consulta .= " AND d.Tipo_Complem= '" .$_POST['complemento']."'";
                                    }

                                    if (isset($_POST['ruta']) && $_POST['ruta'] != '') {
                                        $consulta .= " AND d.cod_Sede IN (Select rs.cod_Sede FROM rutasedes rs WHERE rs.idruta = " .$_POST['ruta'] .')';
                                    }
        
                                    if (isset($_POST['semana']) && $_POST['semana'] != '') {
                                        $consulta .= " AND d.semana = '" .$_POST['semana']. "'";
                                    }

                                    if (isset($_POST['dia']) && $_POST['dia'] != '') {
                                        $consulta .= " AND d.Dias LIKE '%".$_POST['dia']."%'";
                                    }
                                } 
                                // exit(var_dump($consulta));
                            ?>

                        <input type="hidden" name="consulta" id="consulta" value="<?php echo $consulta; ?>">
                    </div> <!-- table-responsive -->
                </div><!-- /.ibox-content contentBackground -->
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
<script src="<?= $baseUrl; ?>/modules/informe_despachos/js/informe_despacho.js"></script>

<script type="text/javascript">
   // console.log('im here');
   var buttonCommon = {
      exportOptions: {
         format: {
            body: function ( data, row, column, node ) {
               return column === 7 ?
               data.replace( ',', ' ' ) :
               data;

            }
         }
      }
   };

   dataset1 = $('#tabladespachodia').DataTable({
      ajax: {
         method: 'POST',
         url: 'functions/fn_despachodia_obtener_datos_despacho.php',
         data:{
            consulta: $('#consulta').val()
           }
      },
      columns:[
         { data: 'Ciudad'},
         { data: 'cod_inst'},
         { data: 'nom_inst'}, 
         { data: 'cod_Sede'},
         { data: 'nom_sede'},
         { data: 'Tipo_Complem'},
         { data: 'Semana'},
         { data: 'Dias'},
         { data: 'Cobertura'},
      <?php  
         for ($i=1; $i <=$cantGrupoEtario ; $i++) { 
      ?>
            { data: 'Cobertura_G<?= $i; ?>'},
      <?php       
         }
      ?>
      <?php 
         foreach ($diasR as $keyD => $diaR) {
            $aux = $diaR['DIA'];                   
      ?>
            {data : 'D<?=$aux?>'},
      <?php 
         }
      ?>
      ],

      pageLength: 25,
      responsive: true, 
      dom : '<"html5buttons" B>lr<"containerBtn"><"inputFiltro"f>tip',
      buttons : [
        $.extend( true, {}, buttonCommon, {
            extend: 'excel',
            title:'Informe Despachos Diario', 
            className:'btnExportarExcel'
         } ),
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


   <?php if (isset($_POST['buscar'])): ?>
        $('#btnBuscar').prop('disabled', true);

        $('#municipio').change(function(){
            $('#complemento').removeAttr('disabled');
        });

        setTimeout(function() {
            <?php if ($_POST['municipio'] != ""): ?>
                $('#municipio').val('<?php echo $_POST['municipio']; ?>').change();
            <?php endif ?>
                
            <?php if (isset($_POST['institucion']) != ""): ?>
                $('#institucion').val('<?php echo $_POST['institucion']; ?>').change();
                $("#ruta").prop('disabled', true);
            <?php endif ?>

            <?php if ($_POST['sede'] != ""): ?>
                $('#sede').val('<?php echo $_POST['sede']; ?>').change();
                $("#ruta").prop('disabled', true);
            <?php endif ?>

            <?php if ($_POST['ruta'] != ""): ?>
                $('#ruta').val('<?php echo $_POST['ruta']; ?>').change();
                $("#municipio").prop('disabled', true);
            <?php endif ?>

            $('#btnBuscar').prop('disabled', false);
            $('#formBuscar').find('input, textarea, button, select').prop('disabled',false);
        }, 3500);
    <?php endif ?>

 </script>

<?php mysqli_close($Link); ?>

</body>
</html>