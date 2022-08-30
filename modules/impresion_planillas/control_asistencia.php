<?php
   include '../../header.php';
   if ($permisos['entrega_complementos'] == "0") {
?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
   </script>
<?php 
   exit(); 
   }
   require_once '../../db/conexion.php';
   set_time_limit (0);
   ini_set('memory_limit','6000M');

   $periodoActual = $_SESSION['periodoActual'];
   $DepartamentoOperador = $_SESSION['p_CodDepartamento'];

   if($_SESSION['perfil'] == 6){
      $rectorDocumento = $_SESSION['num_doc'];
   }

   $consultaComplementos = " SELECT CODIGO FROM tipo_complemento ";
   $respuestaComplementos = $Link->query($consultaComplementos) or die ('Error al consultar los complementos ' . mysqli_error($Link));
   if ($respuestaComplementos->num_rows > 0) {
      while ($dataComplementos = $respuestaComplementos->fetch_assoc()) {
         $complementos[$dataComplementos['CODIGO']] = $dataComplementos['CODIGO'];
      }
   }
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
	<div class="col-lg-8">
		<h2>Control de asistencia</h2>
		<ol class="breadcrumb">
			<li>
				<a href="<?php echo $baseUrl; ?>">Home</a>
			</li>
			<li class="active">
				<strong>Control de asistencia</strong>
			</li>
		</ol>
	</div>
	<div class="col-lg-4">
		<div class="title-action">
	</div>
</div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
   <div class="row">
      <div class="col-lg-12">
         <div class="ibox float-e-margins">
            <div class="ibox-content contentBackground">
               <h2>Parámetros de Consulta</h2>
               <form class="col-lg-12" action="planillas_v2.php" name="form_planillas" id="form_planillas" method="post" target="_blank">
                  <div class="row">
                     <div class="col-md-3 col-sm-6 form-group">
                     <label for="municipio">Municipio</label>
                        <select class="form-control" name="municipio" id="municipio" required="required">
                           <option value="">Seleccione uno</option>
                           <?php
                              $limit = "";
                              $consulta = "SELECT DISTINCT CodigoDANE, ciudad FROM ubicacion WHERE ETC = 0";
                              if($_SESSION['perfil'] == 6){
                                 $consulta = "SELECT  ubicacion.ciudad as ciudad, 
                                                      ubicacion.CodigoDANE 
                                                   FROM instituciones 
                                                   LEFT JOIN ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE 
                                                   WHERE cc_rector = $rectorDocumento";
                                 $limit = " LIMIT 1 ";
                              }
                              if ($_SESSION['perfil'] == "7") {
                                 $documentoCoordinador = $_SESSION['num_doc'];
                                 $consulta = "SELECT  u.Ciudad as ciudad, 
                                                      u.CodigoDANE 
                                                   FROM instituciones i 
                                                   LEFT JOIN ubicacion u ON u.CodigoDANE = i.cod_mun 
                                                   LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst 
                                                   WHERE s.id_coordinador = $documentoCoordinador ";
                                 $limit = " LIMIT 1 ";
                              }
                              if($_SESSION['p_Municipio'] != '0') { $consulta = $consulta." and CodigoDANE = '".$_SESSION['p_Municipio']."' "; }
                              else if ($_SESSION['p_Municipio'] == 0) { $consulta = $consulta. " and CodigoDANE like '" .$_SESSION['p_CodDepartamento']. "%'"; }
                              $consulta = $consulta." order by ciudad asc $limit ";
                              $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                              if($resultado->num_rows > 0){
                                 while($row = $resultado->fetch_assoc()) {
                           ?>
                                    <option value="<?= $row["CodigoDANE"]; ?>" <?php if((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] == $row["CodigoDANE"]) || ($municipio_defecto["CodMunicipio"] == $row["CodigoDANE"])){ echo " selected "; } ?> ><?= $row["ciudad"]; ?></option>
                           <?php
                                 }
                              }
                           ?>
                        </select>
                        <input type="hidden" name="municipioNm" id="municipioNm" value="">
                     </div> <!-- form-group -->

                     <div class=" col-md-3 col-sm-6 form-group">
                        <label for="mes">Mes</label>
                        <?php 
                           $vsql="SELECT TABLE_NAME as mes 
                                    FROM information_schema.TABLES 
                                    WHERE  table_schema = '$Database' AND   TABLE_NAME LIKE 'entregas_res_%'"; 
                        ?>
                        <select class="form-control" name="mes" id="mes" required="required">
                           <option value="">Seleccione uno</option>
                           <?php
                              $result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
                                 while($row = $result->fetch_assoc()) {
                                    $aux = $row['mes'];
                                    $aux = substr($aux, 13, -2);
                           ?>
                                    <option value="<?php echo $aux; ?>" <?php if (isset($_POST['mesinicial']) && $_POST['mesinicial'] == $aux ) {echo " selected "; } ?>>
                           <?php
                                    switch ($aux) {
                                       case "01": echo "Enero"; break;
                                       case "02": echo "Febrero"; break;
                                       case "03": echo "Marzo"; break;
                                       case "04": echo "Abril"; break;
                                       case "05": echo "Mayo"; break;
                                       case "06": echo "Junio"; break;
                                       case "07": echo "Julio"; break;
                                       case "08": echo "Agosto"; break;
                                       case "09": echo "Septiembre"; break;
                                       case "10": echo "Octubre"; break;
                                       case "11": echo "Noviembre"; break;
                                       case "12": echo "Diciembre"; break;
                                    }
                           ?>
                           </option>
                           <?php 
                                 } 
                           ?>
                        </select>
                     </div> <!-- form-group -->

                     <div class="col-md-3 col-sm-6 form-group">
                        <label for="semana_inicial">Semana Inicial</label>
                        <select class="form-control" name="semana_inicial" id="semana_inicial" required="required">
                           <option value="">Seleccione uno</option>
                        </select>
                        <input type="hidden" name="diaInicialSemanaInicial" id="diaInicialSemanaInicial">
                        <input type="hidden" name="diaFinalSemanaInicial" id="diaFinalSemanaInicial">
                     </div> <!-- form-group -->

                     <div class="col-md-3 col-sm-6 form-group">
                        <label for="semana_final">Semana Final</label>
                        <select class="form-control" name="semana_final" id="semana_final" required="required">
                           <option value="">seleccione</option>
                        </select>
                        <input type="hidden" name="diaInicialSemanaFinal" id="diaInicialSemanaFinal">
                        <input type="hidden" name="diaFinalSemanaFinal" id="diaFinalSemanaFinal">
                     </div> <!-- form-group -->
                  </div> <!-- row -->
                  <div class="row">
                     <div class=" col-md-4 col-sm-6 form-group">
                        <label for="institucion">Institución</label>
                        <select class="form-control" name="institucion" id="institucion" >
                           <option value="">Todas</option>
                           <?php
                              if(isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" || $municipio_defecto["CodMunicipio"]) {
                                 $municipio = (isset($_GET["pb_municipio"])) ? $_GET["pb_municipio"] : $municipio_defecto["CodMunicipio"];             
                                 $consulta = " SELECT distinct s.cod_inst, 
                                                      s.nom_inst 
                                                   FROM sedes$periodoActual s 
                                                   LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede 
                                                   LEFT JOIN instituciones i ON s.cod_inst = i.codigo_inst
                                                   where 1=1 ";
                                 $consulta = $consulta." AND s.cod_mun_sede = '$municipio' ";
                                 if($_SESSION['perfil'] == 6){
                                    $consulta .= " AND cc_rector = $rectorDocumento ";
                                 } 
                                 $consulta = $consulta." ORDER BY s.nom_inst ASC ";
                                 $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                                 if($resultado->num_rows > 0) {
                                    while($row = $resultado->fetch_assoc()){ 
                           ?>
                                       <option value="<?php echo $row['cod_inst']; ?>" <?php if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?php echo $row['nom_inst']; ?></option>
                           <?php    
                                    }
                                 }
                              } 
                           ?>
                        </select>
                     </div>  <!-- form-group -->

                     <div class="col-md-4 col-sm-6 form-group">
                        <label for="sede">Sede</label>
                        <select class="form-control" name="sede" id="sede">
                           <option value="">Todas</option>
                           <?php
                              $institucion = '';
                              if( isset($_GET['pb_institucion']) && $_GET['pb_institucion'] != '' ){
                                 $institucion = $_GET['pb_institucion'];
                                 $consulta = " SELECT DISTINCT s.cod_sede, 
                                                      s.nom_sede 
                                                   FROM sedes$periodoActual s 
                                                   LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede 
                                                   WHERE 1=1 ";
                                 $consulta = $consulta."  AND s.cod_inst = '$institucion' ";
                                 $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                                 if($resultado->num_rows >= 1) {
                                    while($row = $resultado->fetch_assoc()) { 
                           ?>
                                       <option value="<?php echo $row['cod_sede']; ?>" <?php if(isset($_GET["pb_sede"]) && $_GET["pb_sede"] == $row['cod_sede'] ){ echo " selected "; }  ?> ><?php echo $row['nom_sede']; ?></option>
                           <?php 
                                    }// Termina el while
                                 }
                              } 
                           ?>
                        </select>
                     </div> <!-- form-group -->

                     <div class="col-md-4 col-sm-6 form-group">
                        <label for="tipo">Tipo Complemento</label>
                        <select class="form-control" name="tipo" id="tipo" required>
                           <option value="">Seleccione</option>
                           <?php foreach ($complementos as $key => $value) { ?>
                              <option value="<?= $key ?>"><?= $value ?></option>
                           <?php } ?>
                        </select>
                     </div> <!-- form-group -->
                  </div> <!-- row -->

                  <div class="row">
                     <div class="col-sm-12">
                        <h3>Parámetros de Consulta</h3>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12 form-group">
                        <div class="i-checks">
                           <label>
                              <input type="radio" value="1" name="tipoPlanilla" required="required"> Vacia  &nbsp;&nbsp;&nbsp;
                           </label>

                           <label>
                              <input type="radio" value="2" name="tipoPlanilla" required="required"> <i></i> Blanco  &nbsp;&nbsp;&nbsp;
                           </label>

                           <label>
                              <input type="radio" value="3" name="tipoPlanilla" required="required"> <i></i> Programada  &nbsp;&nbsp;&nbsp;
                           </label>

                           <label>
                              <input type="radio" value="4" name="tipoPlanilla" required="required"> <i></i> Diligenciada  &nbsp;&nbsp;&nbsp;
                           </label>

                           <label>
                              <input type="radio" value="5" name="tipoPlanilla" required="required"> <i></i> Novedades  &nbsp;&nbsp;&nbsp;
                           </label>

                           <label>
                              <input type="radio" value="7" name="tipoPlanilla" required="required"> <i></i> Novedades diligeciada  &nbsp;&nbsp;&nbsp;
                           </label>

                           <label>
                              <input type="radio" value="8" name="tipoPlanilla" required="required"> <i></i> Novedades programadas  &nbsp;&nbsp;&nbsp;
                           </label>

                           <label>
                              <input type="radio" value="6" name="tipoPlanilla" required="required"> <i></i> Suplentes  &nbsp;&nbsp;&nbsp;
                           </label>
                        </div>
                        <label for="tipoPlanilla" class="error"></label>
                     </div>
                  </div> <!-- row -->
                  <div class="row">
                     <div class="col-sm-3 form-group">
                        <button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong>Buscar</strong></button>
                     </div>
                  </div>
               </form>
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
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/validate/jquery.validate.min.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo $baseUrl; ?>/theme/js/inspinia.js"></script>
<script src="<?php echo $baseUrl; ?>/theme/js/plugins/pace/pace.min.js"></script>

<!-- Page-Level Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/impresion_planillas/js/control_asistencia.js"></script>

<?php mysqli_close($Link); ?>
</body>
</html>
