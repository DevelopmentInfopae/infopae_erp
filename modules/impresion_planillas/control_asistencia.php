<?php
  include '../../header.php';
  require_once '../../db/conexion.php';
  set_time_limit (0);
  ini_set('memory_limit','6000M');

  $periodoActual = $_SESSION['periodoActual'];
  $DepartamentoOperador = $_SESSION['p_CodDepartamento'];

  if($_SESSION['perfil'] == 6){
    $rectorDocumento = $_SESSION['num_doc'];
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
                <div class="col-sm-3 form-group">
                  <label for="municipio">Municipio</label>
                  <select class="form-control" name="municipio" id="municipio" required="required">
                    <option value="">Seleccione uno</option>
                    <?php
                      $limit = "";
                      $consulta = "SELECT DISTINCT CodigoDANE, ciudad FROM ubicacion WHERE ETC = 0";
                      if($_SESSION['perfil'] == 6){
                        $consulta = "SELECT ubicacion.ciudad as ciudad, ubicacion.CodigoDANE from instituciones left join ubicacion on instituciones.cod_mun = ubicacion.CodigoDANE where cc_rector = $rectorDocumento";
                        $limit = " LIMIT 1 ";
                       }
                       if ($_SESSION['perfil'] == "7") {
                        $documentoCoordinador = $_SESSION['num_doc'];
                        $consulta = "SELECT u.Ciudad as ciudad, u.CodigoDANE FROM instituciones i LEFT JOIN ubicacion u ON u.CodigoDANE = i.cod_mun LEFT JOIN sedes$periodoActual s ON s.cod_inst = i.codigo_inst WHERE s.id_coordinador = $documentoCoordinador ";
                        $limit = " LIMIT 1 ";
                       }

                      if($_SESSION['p_Municipio'] != '') { $consulta = $consulta." and CodigoDANE = '".$_SESSION['p_Municipio']."' "; }
                      $consulta = $consulta." order by ciudad asc $limit ";
                      // var_dump($consulta);

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
                </div>

                <div class="col-sm-3 form-group">
                  <label for="mes">Mes</label>
                  <?php $vsql="SELECT TABLE_NAME as mes FROM information_schema.TABLES WHERE  table_schema = '$Database' AND   TABLE_NAME LIKE 'entregas_res_%'"; ?>
                  <select class="form-control" name="mes" id="mes" required="required">
                    <option value="">Seleccione uno</option>
                    <?php
                      $result = $Link->query($vsql) or die ('Unable to execute query. '. mysqli_error($Link));
                      while($row = $result->fetch_assoc())
                      {
                        $aux = $row['mes'];
                        $aux = substr($aux, 13, -2);
                    ?>
                    <option value="<?php echo $aux; ?>" <?php if (isset($_POST['mesinicial']) && $_POST['mesinicial'] == $aux ) {echo " selected "; } ?>>
                      <?php
                        switch ($aux)
                        {
                          case "01":
                            echo "Enero";
                          break;
                          case "02":
                            echo "Febrero";
                          break;
                          case "03":
                            echo "Marzo";
                          break;
                          case "04":
                            echo "Abril";
                          break;
                          case "05":
                            echo "Mayo";
                          break;
                          case "06":
                            echo "Junio";
                          break;
                          case "07":
                            echo "Julio";
                          break;
                          case "08":
                            echo "Agosto";
                          break;
                          case "09":
                            echo "Septiembre";
                          break;
                          case "10":
                            echo "Octubre";
                          break;
                          case "11":
                            echo "Noviembre";
                          break;
                          case "12":
                            echo "Diciembre";
                          break;
                        }
                      ?>
                    </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-sm-3 form-group">
                  <label for="semana_inicial">Semana Inicial</label>
                  <select class="form-control" name="semana_inicial" id="semana_inicial" required="required">
                    <option value="">Seleccione uno</option>
                  </select>
                  <input type="hidden" name="diaInicialSemanaInicial" id="diaInicialSemanaInicial">
                  <input type="hidden" name="diaFinalSemanaInicial" id="diaFinalSemanaInicial">
                </div>

                <div class="col-sm-3 form-group">
                  <label for="semana_final">Semana Final</label>
                  <select class="form-control" name="semana_final" id="semana_final" required="required">
                    <option value="">seleccione</option>
                  </select>
                  <input type="hidden" name="diaInicialSemanaFinal" id="diaInicialSemanaFinal">
                  <input type="hidden" name="diaFinalSemanaFinal" id="diaFinalSemanaFinal">
                </div>
              </div>
              <div class="row">
                <div class="col-sm-4 form-group">
                  <label for="institucion">Institución</label>
                  <select class="form-control" name="institucion" id="institucion" required="required">
                    <option value="">Todas</option>
                    <?php
                      if(isset($_GET["pb_municipio"]) && $_GET["pb_municipio"] != "" || $municipio_defecto["CodMunicipio"])
                      {
                        $municipio = (isset($_GET["pb_municipio"])) ? $_GET["pb_municipio"] : $municipio_defecto["CodMunicipio"];
                        
                        
                        
                        
                        
                        
                        $consulta = " SELECT distinct s.cod_inst, s.nom_inst from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede 
                        LEFT JOIN instituciones i ON s.cod_inst = i.codigo_inst
                        where 1=1 ";
                        $consulta = $consulta." and s.cod_mun_sede = '$municipio' ";



                        if($_SESSION['perfil'] == 6){
       

                          $consulta .= " and cc_rector = $rectorDocumento ";
    
    
    
                          } 


                        $consulta = $consulta." order by s.nom_inst asc ";


                        //echo "<br><br>$consulta<br><br>";









                        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                        if($resultado->num_rows > 0)
                        {
                          while($row = $resultado->fetch_assoc())
                          { ?>
                            <option value="<?php echo $row['cod_inst']; ?>" <?php if(isset($_GET["pb_institucion"]) && $_GET["pb_institucion"] == $row['cod_inst'] ){ echo " selected "; }  ?> > <?php echo $row['nom_inst']; ?></option>
                    <?php }
                        }
                      } ?>
                  </select>
                </div>

                <div class="col-sm-4 form-group">
                  <label for="sede">Sede</label>
                  <select class="form-control" name="sede" id="sede">
                    <option value="">Todas</option>
                      <?php
                        $institucion = '';
                        if( isset($_GET['pb_institucion']) && $_GET['pb_institucion'] != '' )
                        {
                          $institucion = $_GET['pb_institucion'];
                          $consulta = " select distinct s.cod_sede, s.nom_sede from sedes$periodoActual s left join sedes_cobertura sc on s.cod_sede = sc.cod_sede where 1=1 ";
                          $consulta = $consulta."  and s.cod_inst = '$institucion' ";
                          $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                          if($resultado->num_rows >= 1)
                          {
                            while($row = $resultado->fetch_assoc())
                            { ?>
                              <option value="<?php echo $row['cod_sede']; ?>" <?php if(isset($_GET["pb_sede"]) && $_GET["pb_sede"] == $row['cod_sede'] ){ echo " selected "; }  ?> ><?php echo $row['nom_sede']; ?></option>
                      <?php }// Termina el while
                          }
                        } ?>
                  </select>
                </div>

                <div class="col-sm-4 form-group">
                  <label for="tipo">Tipo Complemento</label>
                  <select class="form-control" name="tipo" id="tipo" required>
                    <option value="">seleccione</option>
                  </select>
                </div>
              </div>
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
              </div>
              <div class="row">
                <div class="col-sm-3 form-group">
                  <button class="btn btn-primary" type="button" id="btnBuscar" name="btnBuscar" value="1" ><strong>Buscar</strong></button>
                </div>
              </div>
              <?php
                $tablaMes = '';
                if(isset($_GET["pb_btnBuscar"]) && $_GET["pb_btnBuscar"] == 1)
                {
                  if(isset($_GET["pb_mes"]) && $_GET["pb_mes"] != "" )
                  {
                    // Ajustado formato del mes inicial para hacer el llamado de la tabla con los registros para ese més.
                    $mesnicial = $_GET["pb_mes"];

                    if($mesnicial < 10) { $tablaMes = '0'.$mesnicial; }
                    else { $tablaMes = $mesnicial; }
                  }

                  $bandera = 0;

                  if($tablaMes == '')
                  {
                    $bandera++;
                    echo "<br> <h3>Debe seleccionar el mes inicial.</h3> ";
                  }
                  else
                  {
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

                  if($bandera == 0)
                  {
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
                      productosmov$tablaMes$tablaAnno pm ON pm.Numero = de.Num_doc AND pm.Documento = 'DES'
                      LEFT JOIN
                      bodegas b ON b.ID = pm.BodegaOrigen
                      LEFT JOIN tipo_despacho td ON td.Id = de.tipodespacho
                      WHERE 1=1;";

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

                      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
              ?>
              <hr>
              <div class="row">
                <div class="col-xs-6 flexMid">
                  <label for="seleccionarVarios">Seleccionar Todos</label>
                  <input type="checkbox" name="seleccionarVarios" id="seleccionarVarios">
                </div>
                <div class="col-xs-6">
                  <div class="pull-right social-action dropdown">
                    <button data-toggle="dropdown" class="dropdown-toggle btn-white" title="Generar Planilla"> <i class="fa fa-file-pdf-o"></i> </button>
                    <ul class="dropdown-menu m-t-xs">
                      <li><a href="#" onclick="despachos_por_sede()">Individual</a></li>
                      <li><a href="#" onclick="despachos_kardex()">Kardex</a></li>
                      <li><a href="#" onclick="despachos_kardex2()">Kardex 2</a></li>
                      <li><a href="#" onclick="despachos_mixta()">Mixta</a></li>
                      <li><a href="#" onclick="despachos_consolidado()">Consolidado</a></li>
                      <li><a href="#" onclick="despachos_agrupados()">Agrupado</a></li>
                    </ul>
                    <button class="btn-white" title="Editar Despacho" onclick="editar_despacho()" type="button"> <i class="fa fa-pencil"></i> </button>
                    <button class="btn-white" title="Ingresar Lotes y Fechas de vencimiento" onclick="despachos_por_sede_fecha_lote()" type="button"> <i class="fa fa-clock-o"></i> </button>
                    <button class="btn-white" title="Eliminar Despacho" onclick="eliminar_despacho()" type="button"> <i class="fa fa-trash"></i> </button>
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
                    <?php
                      if($resultado->num_rows >= 1)
                      {
                        while($row = $resultado->fetch_assoc())
                        { ?>
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
                            switch ($estado)
                            {
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
                    <?php
                      }
                    } ?>
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
                  }
                } ?>
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
