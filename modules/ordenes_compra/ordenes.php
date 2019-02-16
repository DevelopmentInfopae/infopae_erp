<?php
  include '../../header.php';
  set_time_limit (0);
  ini_set('memory_limit','6000M');
  $periodoActual = $_SESSION['periodoActual'];
  require_once '../../db/conexion.php';
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
      <h2>Órdenes de compra</h2>
      <ol class="breadcrumb">
          <li>
              <a href="<?php echo $baseUrl; ?>">Home</a>
          </li>
          <li class="active">
              <strong>Órdenes de compra</strong>
          </li>
      </ol>
  </div>
	<div class="col-lg-4">
		<div class="title-action">
			<?php if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){ ?>
				<a href="<?php echo $baseUrl; ?>/modules/despachos/despacho_nuevo.php" target="_self" class="btn btn-primary">Nuevo</a>
			<?php } ?>
		</div>
	</div>
</div>


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <h2>Parámetros de Consulta</h2>
          <form class="col-lg-12" action="despachos.php" name="formDespachos" id="formDespachos" method="post" target="_blank">
            <div class="row">

                                <div class="col-sm-4 form-group">
                                    <label for="fechaInicial">Fecha Inicial</label>
                                    <div class="row compositeDate">
                                        <div class="col-sm-4 nopadding">
                                            <select name="annoi" id="annoi" class="form-control">
                                                <option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
                                            </select>
                                        </div><!-- /.col-sm-4 -->
                                        <div class="col-sm-5 nopadding">
                                            <?php
                                                if(!isset($_GET['pb_mesi']) || $_GET['pb_mesi'] == ''){
                                                    $_GET['pb_mesi'] = date("n");
                                                }
                                            ?>
                                            <select name="mesi" id="mesi" onchange="mesFinal();" class="form-control">
                                                <option value="">mm</option>
                                                <option value="1" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 1) {echo " selected "; } ?>>Enero</option>
                                                <option value="2" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 2) {echo " selected "; } ?>>Febrero</option>
                                                <option value="3" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 3) {echo " selected "; } ?>>Marzo</option>
                                                <option value="4" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 4) {echo " selected "; } ?>>Abril</option>
                                                <option value="5" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 5) {echo " selected "; } ?>>Mayo</option>
                                                <option value="6" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 6) {echo " selected "; } ?>>Junio</option>
                                                <option value="7" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 7) {echo " selected "; } ?>>Julio</option>
                                                <option value="8" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 8) {echo " selected "; } ?>>Agosto</option>
                                                <option value="9" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 9) {echo " selected "; } ?>>Septiembre</option>
                                                <option value="10" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 10) {echo " selected "; } ?>>Octubre</option>
                                                <option value="11" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 11) {echo " selected "; } ?>>Noviembre</option>
                                                <option value="12" <?php if (isset($_GET['pb_mesi']) && $_GET['pb_mesi'] == 12) {echo " selected "; } ?>>Diciembre</option>
                                            </select>
                                            <input type="hidden" name="mesiConsulta" id="mesiConsulta" value="<?php if (isset($_GET['pb_mesi'])) { echo $_GET['pb_mesi']; } ?>">
                                        </div><!-- /.col -->


                                        <div class="col-md-3 nopadding">






                                            <select name="diai" id="diai" class="form-control">
                           <option value="">dd</option>


                           <option value="1" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 1) {echo " selected "; } ?>>01</option>


                           <option value="2" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 2) {
                              echo " selected ";
                           } ?>>02</option>
                           <option value="3" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 3) {
                              echo " selected ";
                           } ?>>03</option>
                           <option value="4" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 4) {
                              echo " selected ";
                           } ?>>04</option>
                           <option value="5" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 5) {
                              echo " selected ";
                           } ?>>05</option>
                           <option value="6" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 6) {
                              echo " selected ";
                           } ?>>06</option>
                           <option value="7" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 7) {
                              echo " selected ";
                           } ?>>07</option>
                           <option value="8" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 8) {
                              echo " selected ";
                           } ?>>08</option>
                           <option value="9" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 9) {
                              echo " selected ";
                           } ?>>09</option>
                           <option value="10" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 10) {
                              echo " selected ";
                           } ?>>10</option>
                           <option value="11" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 11) {
                              echo " selected ";
                           } ?>>11</option>
                           <option value="12" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 12) {
                              echo " selected ";
                           } ?>>12</option>
                           <option value="13" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 13) {
                              echo " selected ";
                           } ?>>13</option>
                           <option value="14" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 14) {
                              echo " selected ";
                           } ?>>14</option>
                           <option value="15" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 15) {
                              echo " selected ";
                           } ?>>15</option>
                           <option value="16" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 16) {
                              echo " selected ";
                           } ?>>16</option>
                           <option value="17" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 17) {
                              echo " selected ";
                           } ?>>17</option>
                           <option value="18" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 18) {
                              echo " selected ";
                           } ?>>18</option>
                           <option value="19" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 19) {
                              echo " selected ";
                           } ?>>19</option>
                           <option value="20" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 20) {
                              echo " selected ";
                           } ?>>20</option>
                           <option value="21" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 21) {
                              echo " selected ";
                           } ?>>21</option>
                           <option value="22" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 22) {
                              echo " selected ";
                           } ?>>22</option>
                           <option value="23" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 23) {
                              echo " selected ";
                           } ?>>23</option>
                           <option value="24" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 24) {
                              echo " selected ";
                           } ?>>24</option>
                           <option value="25" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 25) {
                              echo " selected ";
                           } ?>>25</option>
                           <option value="26" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 26) {
                              echo " selected ";
                           } ?>>26</option>
                           <option value="27" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 27) {
                              echo " selected ";
                           } ?>>27</option>
                           <option value="28" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 28) {
                              echo " selected ";
                           } ?>>28</option>
                           <option value="29" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 29) {
                              echo " selected ";
                           } ?>>29</option>
                           <option value="30" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 30) {
                              echo " selected ";
                           } ?>>30</option>
                           <option value="31" <?php if (isset($_GET['pb_diai']) && $_GET['pb_diai'] == 31) {
                              echo " selected ";
                           } ?>>31</option>
                       </select>
                                            </div><!-- /.col-sm-4 -->
                                        </div><!-- /.row -->
                                    </div><!-- /Fecha inicial -->



<div class="col-sm-4 form-group">
    <label for="fechaInicial">Fecha Final</label>
    <div class="row compositeDate">
        <div class="col-sm-4 form-group nopadding">
            <select name="annof" id="annof" class="form-control">
                <option value="<?php echo $_SESSION['periodoActualCompleto']; ?>"><?php echo $_SESSION['periodoActualCompleto']; ?></option>
          </select>
        </div>
        <div class="col-sm-5 form-group nopadding">
            <input type="text" name="mesfText" id="mesfText" value="mm" readonly="readonly" class="form-control">
            <input type="hidden" name="mesf" id="mesf" value="">
        </div>
        <div class="col-sm-3 form-group nopadding">
            <select name="diaf" id="diaf" class="form-control">
                           <option value="">dd</option>


                           <option value="1" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 1) {echo " selected "; } ?>>01</option>


                           <option value="2" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 2) {
                              echo " selected ";
                           } ?>>02</option>
                           <option value="3" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 3) {
                              echo " selected ";
                           } ?>>03</option>
                           <option value="4" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 4) {
                              echo " selected ";
                           } ?>>04</option>
                           <option value="5" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 5) {
                              echo " selected ";
                           } ?>>05</option>
                           <option value="6" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 6) {
                              echo " selected ";
                           } ?>>06</option>
                           <option value="7" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 7) {
                              echo " selected ";
                           } ?>>07</option>
                           <option value="8" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 8) {
                              echo " selected ";
                           } ?>>08</option>
                           <option value="9" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 9) {
                              echo " selected ";
                           } ?>>09</option>
                           <option value="10" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 10) {
                              echo " selected ";
                           } ?>>10</option>
                           <option value="11" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 11) {
                              echo " selected ";
                           } ?>>11</option>
                           <option value="12" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 12) {
                              echo " selected ";
                           } ?>>12</option>
                           <option value="13" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 13) {
                              echo " selected ";
                           } ?>>13</option>
                           <option value="14" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 14) {
                              echo " selected ";
                           } ?>>14</option>
                           <option value="15" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 15) {
                              echo " selected ";
                           } ?>>15</option>
                           <option value="16" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 16) {
                              echo " selected ";
                           } ?>>16</option>
                           <option value="17" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 17) {
                              echo " selected ";
                           } ?>>17</option>
                           <option value="18" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 18) {
                              echo " selected ";
                           } ?>>18</option>
                           <option value="19" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 19) {
                              echo " selected ";
                           } ?>>19</option>
                           <option value="20" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 20) {
                              echo " selected ";
                           } ?>>20</option>
                           <option value="21" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 21) {
                              echo " selected ";
                           } ?>>21</option>
                           <option value="22" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 22) {
                              echo " selected ";
                           } ?>>22</option>
                           <option value="23" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 23) {
                              echo " selected ";
                           } ?>>23</option>
                           <option value="24" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 24) {
                              echo " selected ";
                           } ?>>24</option>
                           <option value="25" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 25) {
                              echo " selected ";
                           } ?>>25</option>
                           <option value="26" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 26) {
                              echo " selected ";
                           } ?>>26</option>
                           <option value="27" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 27) {
                              echo " selected ";
                           } ?>>27</option>
                           <option value="28" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 28) {
                              echo " selected ";
                           } ?>>28</option>
                           <option value="29" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 29) {
                              echo " selected ";
                           } ?>>29</option>
                           <option value="30" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 30) {
                              echo " selected ";
                           } ?>>30</option>
                           <option value="31" <?php if (isset($_GET['pb_diaf']) && $_GET['pb_diaf'] == 31) {
                              echo " selected ";
                           } ?>>31</option>
                       </select>
        </div>
    </div>
</div><!-- /Fecha final -->

<div class="col-sm-4 form-group">
    <label for="tipo">Tipo Complemento</label>
    <select class="form-control" name="tipo" id="tipo">
    <option value="">Seleccione una</option>
    <?php
                $consulta = " select DISTINCT CODIGO from tipo_complemento ";
                $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                if($resultado->num_rows >= 1){
                  while($row = $resultado->fetch_assoc()) { ?>
                    <option value="<?php echo $row["CODIGO"]; ?>" <?php  if (isset($_GET['pb_tipo']) && ($_GET['pb_tipo'] == $row["CODIGO"]) ) { echo ' selected '; } ?>   ><?php echo $row["CODIGO"]; ?></option>
                    <?php
                  }// Termina el while
                }//Termina el if que valida que si existan resultados

                ?>
</select>
</div>
            </div><!-- /.row -->














<!-- Segunda Fila de parametros -->
<div class="row">
    <div class="col-sm-2 form-group">
        <label for="fechaInicial">Municipio</label>
        <select class="form-control" name="municipio" id="municipio">
    <option value="">Seleccione uno</option>
    <?php
    $consulta = " select DISTINCT codigoDANE, ciudad from ubicacion where 1=1 and ETC = 0 ";

    $DepartamentoOperador = $_SESSION['p_CodDepartamento'];
    if($DepartamentoOperador != ''){
      $consulta = $consulta." and CodigoDANE like '$DepartamentoOperador%' ";
    }
    $consulta = $consulta." order by ciudad asc ";
    //echo $consulta;






    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
    <option value="<?php echo $row["codigoDANE"]; ?>"  <?php  if((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] == $row["codigoDANE"]) || ($municipio_defecto["CodMunicipio"] == $row["codigoDANE"])){ echo " selected "; } ?> ><?php echo $row["ciudad"]; ?></option>
    <?php
    }// Termina el while
    }//Termina el if que valida que si existan resultados
    ?>
    </select>
    </div><!-- /.col -->






    <div class="col-sm-3 form-group">
        <label for="institucion">Institución</label>
        <select class="form-control" name="institucion" id="institucion">
            <option value="">Todas</option>
            <?php
                if ((isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" ) || $municipio_defecto["CodMunicipio"] != "") {
                    $municipio = (isset($_GET["pb_municipio"])) ? $_GET["pb_municipio"] : $municipio_defecto["CodMunicipio"];
                    $consulta = "SELECT DISTINCT s.cod_inst, s.nom_inst FROM sedes$periodoActual s LEFT JOIN sedes_cobertura sc ON s.cod_sede = sc.cod_sede WHERE 1=1";
                    $consulta = $consulta." AND s.cod_mun_sede = '$municipio'";
                    $consulta = $consulta." ORDER BY s.nom_inst ASC";
                    echo $consulta;

                    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                    if($resultado->num_rows >= 1){
                        while($row = $resultado->fetch_assoc()) {
            ?>
                            <option value="<?php echo $row['cod_inst']; ?>" <?php if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?php echo $row['nom_inst']; ?></option>
            <?php
                        }
                    }
                }

            ?>
        </select>
    </div>

    <div class="col-sm-3 form-group">
        <label for="sede">sede</label>
        <select class="form-control" name="sede" id="sede">
    <option value="">Todas</option>




 <?php
  $institucion = '';
  if( isset($_GET['pb_institucion']) && $_GET['pb_institucion'] != '' ){
    $institucion = $_GET['pb_institucion'];
    $consulta = " select distinct s.cod_sede, s.nom_sede from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
    $consulta = $consulta."  and s.cod_inst = '$institucion' ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
      <option value="<?php echo $row['cod_sede']; ?>" <?php if(isset($_GET["pb_sede"]) && $_GET["pb_sede"] == $row['cod_sede'] ){ echo " selected "; }  ?> ><?php echo $row['nom_sede']; ?></option>
    <?php }// Termina el while
    }//Termina el if que valida que si existan resultados
  }
  ?>





    </select>
    </div><!-- /.col -->




    <div class="col-sm-2 form-group">
    <label for="tipoDespacho">Tipo Despacho</label>
    <!-- Tipo Complemento - Codigo -->
    <select class="form-control" name="tipoDespacho" id="tipoDespacho">
    <option value="">Todos</option>
    <?php
    $consulta = " select * from tipo_despacho where id != 4 order by Descripcion asc ";
    $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    if($resultado->num_rows >= 1){
    while($row = $resultado->fetch_assoc()) { ?>
    <option value="<?php echo $row["Id"]; ?>"  <?php  if(isset($_GET["pb_tipoDespacho"]) && $_GET["pb_tipoDespacho"] == $row["Id"] ){ echo " selected "; } ?> ><?php echo $row["Descripcion"]; ?></option>
    <?php
    }// Termina el while
    }//Termina el if que valida que si existan resultados
    ?>
    </select>
    </div><!-- /.col -->


    <div class="col-sm-2 form-group">
    <label for="ruta">Ruta</label>
    <select class="form-control" name="ruta" id="ruta">
          <option value="">Todos</option>
          <?php
            $consulta = " select * from rutas order by nombre asc ";
            $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
            if($resultado->num_rows >= 1){
              while($row = $resultado->fetch_assoc()) { ?>
                <option value="<?php echo $row["ID"]; ?>"  <?php  if(isset($_GET["pb_ruta"]) && $_GET["pb_ruta"] == $row["ID"] ){ echo " selected ";} ?> ><?php echo $row["Nombre"]; ?></option>
                <?php
              }// Termina el while
            }//Termina el if que valida que si existan resultados
          ?>
        </select>
        <input type="hidden" name="rutaNm" id="rutaNm" value="">
    </div><!-- /.col -->






</div>
<!-- /Segunda Fila de parametros -->



















                                <div class="row">
                                    <div class="col-sm-3 form-group">
                                         <input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_GET['consultar']) && $_GET['consultar'] != '') {echo $_GET['consultar']; } ?>" >
                                         <button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong>Buscar</strong></button>
                                    </div>
                                </div>

















<?php
    //var_dump($_GET);
    $tablaMes = '';
    if(isset($_GET["pb_btnBuscar"]) && $_GET["pb_btnBuscar"] == 1){
      if(isset($_GET["pb_mesi"]) && $_GET["pb_mesi"] != "" ){


        // Ajustado formato del mes inicial para hacer el llamado de la tabla con los registros para ese més.
        $mesinicial = $_GET["pb_mesi"];

        if($mesinicial < 10){
          $tablaMes = '0'.$mesinicial;
        }else{
          $tablaMes = $mesinicial;
        }
      }
      $bandera = 0;
      if($tablaMes == ''){
        $bandera++;
        echo "<br> <h3>Debe seleccionar el mes inicial.</h3> ";
      }else{
        $tablaAnno = $_SESSION['periodoActual'];
        $consulta = " show tables like 'productosmov$tablaMes$tablaAnno' ";
        $result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
        $existe = $result->num_rows;
        if($existe > 0){
          $consulta = " show tables like 'despachos_enc$tablaMes$tablaAnno' ";
          $result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
          $existe = $result->num_rows;
          if($existe <= 0){
            $bandera++;
            echo "<br> <h3>No se encontraron registros para este periodo.</h3> ";
          }
        }else{
          $bandera++;
          echo "<br> <h3>No se encontraron registros para este periodo.</h3> ";
        }
      }
      if($bandera == 0){
        ?>



<?php
          $consulta = " SELECT
          s.cod_mun_sede,
          s.cod_inst,
          s.cod_sede,
          de.Num_doc,
          de.FechaHora_Elab,
          de.Semana,
          de.Dias,
          de.Tipo_Complem,
          de.tipodespacho,
          td.Descripcion as tipodespacho_nm,

          de.estado,
          u.Ciudad,
          b.NOMBRE AS bodegaOrigen,
          s.nom_sede AS bodegaDestino
          FROM
          despachos_enc$tablaMes$tablaAnno de
          LEFT JOIN
          sedes$tablaAnno s ON s.cod_sede = de.cod_Sede
          LEFT JOIN
          ubicacion u ON u.codigoDANE = s.cod_mun_sede and u.ETC = 0
          LEFT JOIN
          productosmov$tablaMes$tablaAnno pm ON pm.Numero = de.Num_doc
          AND pm.Documento = 'DES'
          LEFT JOIN
          bodegas b ON b.ID = pm.BodegaOrigen

          LEFT JOIN tipo_despacho td ON td.Id = de.tipodespacho

          where 1=1
           ";

          if(isset($_GET["pb_diai"]) && $_GET["pb_diai"] != "" ){
            $diainicial = $_GET["pb_diai"];
            $consulta = $consulta." and DAYOFMONTH(de.FechaHora_Elab) >= ".$diainicial." ";
          }

          if(isset($_GET["pb_diaf"]) && $_GET["pb_diaf"] != "" ){
            $diafinal = $_GET["pb_diaf"];
            $consulta = $consulta." and DAYOFMONTH(de.FechaHora_Elab) <= ".$diafinal." ";
          }


          if(isset($_GET["pb_tipo"]) && $_GET["pb_tipo"] != "" ){
            $tipo = $_GET["pb_tipo"];
            $consulta = $consulta." and Tipo_Complem = '".$tipo."' ";
          }

          if(isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" ){
            $municipio = $_GET["pb_municipio"];
            $consulta = $consulta." and s.cod_mun_sede = '".$municipio."' ";
          }

          if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] != "" ){
            $institucion = $_GET["pb_institucion"];
            $consulta = $consulta." and cod_inst = '".$institucion."' ";
          }

          if(isset($_GET["pb_sede"]) && $_GET["pb_sede"] != "" ){
            $sede = $_GET["pb_sede"];
            $consulta = $consulta." and s.cod_sede = '".$sede."' ";
          }






          if(isset($_GET["pb_tipoDespacho"]) && $_GET["pb_tipoDespacho"] != "" ){
            $tipoDespacho = $_GET["pb_tipoDespacho"];
            $consulta = $consulta." and TipoDespacho = ".$tipoDespacho." ";
          }

          if(isset($_GET["pb_ruta"]) && $_GET["pb_ruta"] != "" ){
            $ruta = $_GET["pb_ruta"];
            $consulta = $consulta." and s.cod_sede in (select cod_sede from rutasedes where IDRUTA = $ruta)";
          }







          //Impromir la consulta que filtra los despachos
          //echo "<br>$consulta<br><br><br>";

          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));


          //var_dump($resultado);


          ?>















<hr>
                <div class="row">

                  <div class="col-xs-6 flexMid">
                    <label for="seleccionarVarios">Seleccionar Todos</label>
                    <input type="checkbox" name="seleccionarVarios" id="seleccionarVarios">
                  </div>

                    <div class="col-xs-6">

                            <div class="pull-right dropdown">

                                <!-- <button data-toggle="dropdown" class="dropdown-toggle btn-white" title="Generar Planilla">
                                    <i class="fa fa-file-pdf-o"></i>
                                </button>
                                <ul class="dropdown-menu m-t-xs">
                                    <li><a href="#" onclick="despachos_por_sede()">Individual</a></li>
                                    <li><a href="#" onclick="despachos_kardex()">Kardex</a></li>
                                    <li><a href="#" onclick="despachos_kardex2()">Kardex 2</a></li>
                                    <li><a href="#" onclick="despachos_mixta()">Mixta</a></li>
                                    <li><a href="#" onclick="despachos_consolidado()">Consolidado</a></li>
                                    <li><a href="#" onclick="despachos_agrupados()">Agrupado</a></li>
                                </ul> -->

                               <div class="dropdown pull-right" id="">
                                <button class="btn btn-primary btn-sm btn-outline" type="button" id="accionesTabla" data-toggle="dropdown" aria-haspopup="true">Acciones<span class="caret"></span></button>
                                <ul class="dropdown-menu pull-right" aria-labelledby="accionesTabla">
                                  <li><a href="#" onclick="despachos_por_sede()">Individual</a></li>
                                  <li><a href="#" onclick="despachos_kardex()">Kardex</a></li>
                                  <li><a href="#" onclick="despachos_kardex_multiple()">Kardex Múltiple</a></li>
                                  <!-- <li><a href="#" onclick="despachos_kardex2()">Kardex 2</a></li> -->
                                  <!-- <li><a href="#" onclick="despachos_mixta()">Mixta</a></li> -->
                                  <li><a href="#" onclick="despachos_consolidado()">Consolidado</a></li>
                                  <li><a href="#" onclick="despachos_agrupados()">Agrupado</a></li>
                                  <?php if($_SESSION['perfil'] == 0 || $_SESSION['perfil'] == 1){ ?>
                                    <li>
                                      <a href="#" onclick="editar_despacho()">Editar Despacho</a>
                                    </li>
                                    <li>
                                      <a href="#" onclick="despachos_por_sede_fecha_lote()">Ingresar Lotes y Fechas de vencimiento</a>
                                    </li>
                                    <li>
                                      <a href="#" onclick="eliminar_despacho()">Eliminar Despacho</a>
                                    </li>
                                  <?php } ?>
                                </ul>
                              </div>


                            </div>
                      </div>

                </div>








                        <div class="table-responsive">

















                            <table class="table table-striped table-bordered table-hover selectableRows" id="box-table-movimientos" >
                                <thead>
                <tr>
                  <th></th>
                  <th>Número</th>
                  <th>Fecha</th>
                  <th>Semana</th>
                  <th>Dias</th>
                  <th>Tipo Ración</th>
                  <th>Tipo Despacho</th>
                  <th> Municipio </th>
                  <th>Bodega Origen</th>
                  <th> Bodega Destino </th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>


                <?php if($resultado->num_rows >= 1){ while($row = $resultado->fetch_assoc()) { ?>
                  <tr>
                    <td>

                      <input type="checkbox" class="despachos" value="<?php echo $row['Num_doc']; ?>" name="<?php echo $row['Num_doc']; ?>"id="<?php echo $row['Num_doc']; ?>"<?php if($row['estado'] == 0){echo " disabled "; } ?> />

                    </td>






                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Num_doc']; ?></td>
                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['FechaHora_Elab']; ?></td>



                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" >
                      <?php echo $row['Semana']; ?>
                      <input class="soloJs" type="hidden" name="semana_<?php echo $row['Num_doc']; ?>" id="semana_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['Semana']; ?>">
                    </td>

                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" >
                      <?php echo $row['Dias']; ?>
                    </td>


                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" >
                      <?php echo $row['Tipo_Complem']; ?>
                      <input class="soloJs" type="hidden" name="tipo_<?php echo $row['Num_doc']; ?>" id="tipo_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['Tipo_Complem']; ?>">
                    </td>

                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" >
                      <?php echo $row['tipodespacho_nm']; ?>
                      <input class="soloJs" type="hidden" name="tipodespacho_<?php echo $row['Num_doc']; ?>" id="tipodespacho_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['tipodespacho']; ?>">

                      <input class="soloJs" type="hidden" name="cod_sede_<?php echo $row['Num_doc']; ?>" id="cod_sede_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['cod_sede']; ?>">

                    </td>


                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['Ciudad']; ?></td>
                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['bodegaOrigen']; ?></td>
                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');" ><?php echo $row['bodegaDestino']; ?></td>

                    <td onclick="despachoPorSede('<?php echo $row['Num_doc']; ?>');">
                      <?php
                      $estado = $row['estado'];
                      switch ($estado) {
                        case 0:
                        echo "Eliminado";
                        break;
                        case 1:
                        echo "Despachado";
                        break;
                        case 2:
                        echo "Pendiente";
                        break;
                        default:
                        echo $estado;
                        break;
                      }
                      ?>


                      <input class="soloJs" type="hidden" name="estado_<?php echo $row['Num_doc']; ?>" id="estado_<?php echo $row['Num_doc']; ?>" value="<?php echo $row['estado']; ?>">
                    </td>

                  </tr>
                  <?php } } ?>



                </tbody>

                <tfoot>
                          <tr>
                  <th></th>
                  <th>Número</th>
                  <th>Fecha</th>
                  <th>Semana</th>
                  <th>Dias</th>
                  <th>Tipo Ración</th>
                  <th>Tipo Despacho</th>
                  <th> Municipio </th>
                  <th>Bodega Origen</th>
                  <th> Bodega Destino </th>
                  <th>Estado</th>
                </tr>
                </tfoot>
                            </table>
                        </div>






















<?php
    }// Termina el if que valida si la bandera continua igual a cero
}// Termina el if que valida si se recibió el boton de busqueda del form de parametros.
?>






          </form>
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


    <script src="<?php echo $baseUrl; ?>/modules/ordenes_compra/js/ordenes.js"></script>



    <!-- Page-Level Scripts -->


<?php mysqli_close($Link); ?>

<form action="despacho_por_sede.php" method="post" name="formDespachoPorSede" id="formDespachoPorSede" target="_blank">
  <input type="hidden" name="despachoAnnoI" id="despachoAnnoI" value="">
  <input type="hidden" name="despachoMesI" id="despachoMesI" value="">
  <input type="hidden" name="despacho" id="despacho" value="">
</form>

<form action="despachos.php" id="parametrosBusqueda" method="get">
  <input type="hidden" id="pb_annoi" name="pb_annoi" value="">
  <input type="hidden" id="pb_mesi" name="pb_mesi" value="">
  <input type="hidden" id="pb_diai" name="pb_diai" value="">
  <input type="hidden" id="pb_annof" name="pb_annof" value="">
  <input type="hidden" id="pb_mesf" name="pb_mesf" value="">
  <input type="hidden" id="pb_diaf" name="pb_diaf" value="">
  <input type="hidden" id="pb_tipo" name="pb_tipo" value="">
  <input type="hidden" id="pb_municipio" name="pb_municipio" value="">
  <input type="hidden" id="pb_institucion" name="pb_institucion" value="">
  <input type="hidden" id="pb_sede" name="pb_sede" value="">
  <input type="hidden" id="pb_tipoDespacho" name="pb_tipoDespacho" value="">
  <input type="hidden" id="pb_ruta" name="pb_ruta" value="">
  <input type="hidden" id="pb_btnBuscar" name="pb_btnBuscar" value="">
</form>

</body>
</html>
