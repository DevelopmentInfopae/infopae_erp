<?php 
$titulo = 'Trazabilidad';
include '../../header.php'; 
set_time_limit (0);
ini_set('memory_limit','6000M');
$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>

<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2>Trazabilidad</h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Home</a>
      </li>
      <li class="active">
        <strong>Trazabilidad</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <!--
      <a href="#" class="btn btn-white"><i class="fa fa-pencil"></i> Edit </a>
      <a href="#" class="btn btn-white"><i class="fa fa-check "></i> Save </a>
      <a href="#" onclick="actualizarDespacho()" target="_self" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar Cambios </a>
      -->
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form id="formTrazabilidad" action="" method="post">
            <div class="row">            
              <?php include "trazabilidad_fecha_inicio.php"; ?>
              <?php include "trazabilidad_fecha_fin.php"; ?>
              
              <div class="col-sm-3 form-group">
                <label for="tipoDocumento">Tipo Documento</label>
                <select class="form-control" name="tipoDocumento" id="tipoDocumento" onchange="buscarProveedorResponsable();">
                  <option value="">Seleccione uno</option>

                  <?php
                   $consulta1= " select Id, Movimiento from tipomovimiento order by Movimiento asc ";
                        $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                        if($result1){
                          while($row1 = $result1->fetch_assoc()){ ?>
                            <option value="<?php echo $row1['Movimiento']; ?>"
                            <?php if(isset($_POST['tipoDocumento']) && $_POST['tipoDocumento'] == $row1['Movimiento']){ echo ' selected '; } ?>
                            ><?php echo $row1['Movimiento']; ?></option><?php
                          }
                        }
                  ?>
                </select>  
              </div><!-- /.col -->

              <div class="col-sm-3 form-group">
                <label for="proveedor">Proveedor/Responsable</label>
                <select class="form-control" name="proveedor" id="proveedor" onchange="buscarBodegas();">
                  <option value="">Seleccione uno</option>



                  <?php
                    if(isset($_POST['tipoDocumento']) && $_POST['tipoDocumento'] != ''){
                      $proveedor = '';
                      if(isset($_POST['proveedor']) && $_POST['proveedor'] != ''){
                        $proveedor = $_POST['proveedor'];
                        //echo $proveedor;
                      }
                      $tipoDocumento = $_POST['tipoDocumento'];
                      $tipotercero = '';

                      $consulta = " select TipoTercero from tipomovimiento where Movimiento = '$tipoDocumento' ";

                      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                      if($resultado->num_rows >= 1){
                        while($row = $resultado->fetch_assoc()) {
                          $tipotercero = $row['TipoTercero'];
                        }
                      }
                      if($tipotercero != ''){
                        if($tipotercero == 'Proveedor'){
                          $consulta = " select Nitcc, Nombrecomercial as Nombre from proveedores  order by Nombre asc";
                        }elseif($tipotercero == 'Empleado'){
                          $consulta = " select Nitcc, Nombre from empleados order by Nombre asc ";
                        }
                        $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                        if($resultado->num_rows >= 1){
                          while($row = $resultado->fetch_assoc()) { ?>
                            <option value="<?php echo $row['Nitcc']; ?>"  <?php if($proveedor == $row['Nitcc']){ echo " selected ";} ?> ><?php echo $row['Nombre']; ?></option>
                            <?php }// Termina el while
                          }//Termina el if que valida que si existan resultados
                        }

                    }
                    ?>
                </select>  
              </div><!-- /.col -->
            </div><!-- /.row -->


              





            <div class="row">
              <div class="col-sm-3 form-group">
                <label for="producto">Producto / Alimento</label>
                <select class="form-control" name="producto" id="producto">
                  <option value="">Todos</option>
                  <?php
                    $periodoActual = $_SESSION['periodoActual'];
                    $consulta1= " select Codigo, Descripcion from productos$periodoActual  where TipodeProducto = 'Alimento' order by Descripcion asc ";

                    $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                    if($result1){
                      while($row1 = $result1->fetch_assoc()){ ?>
                        <option value="<?php echo $row1['Codigo']; ?>"
                        <?php if(isset($_POST['producto']) && $_POST['producto'] == $row1['Codigo']){ echo ' selected '; } ?>
                        ><?php echo $row1['Descripcion']; ?></option><?php
                      }
                    }
                  ?>
                </select>
              </div><!-- /.col -->

              <div class="col-sm-3 form-group">
                <label for="placa">Tipo de Vehículo</label>
                <select class="form-control" name="vehiculo" id="vehiculo">
                  <option value="">Todos</option>
                    <?php
                      $consulta1= " select * from tipovehiculo order by Nombre asc ";
                      $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                      if($result1){
                        while($row1 = $result1->fetch_assoc()){ ?>
                          <option value="<?php echo $row1['Id']; ?>"
                          <?php if(isset($_POST['vehiculo']) && $_POST['vehiculo'] == $row1['Id']){ echo ' selected '; } ?>
                          ><?php echo $row1['Nombre']; ?></option><?php
                        }
                      }
                  ?>


                </select>
              </div><!-- /.col -->

              <div class="col-sm-3 form-group">
                <label for="bodegao">Bodega Origen</label>
                <select class="form-control" name="bodegao" id="bodegao">
                  <option value="">Todos</option>
                  <?php
                    if(isset($_POST['proveedor']) && $_POST['proveedor'] != '' ){
                      $proveedor = $_POST['proveedor'];
                      $bodegao = 0;
                      if(isset($_POST['bodegao']) && $_POST['bodegao'] != '' ){
                        $bodegao = $_POST['bodegao'];
                      }

                      $consulta = " select distinct ub.COD_BODEGA_SALIDA as codigo, b.NOMBRE as nombre from usuarios u
                      inner join usuarios_bodegas ub on u.id = ub.USUARIO
                      inner join bodegas b on b.ID = ub.COD_BODEGA_SALIDA
                      where u.num_doc = '$proveedor' ";

                      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                      if($resultado->num_rows >= 1){
                        while($row = $resultado->fetch_assoc()) { ?>
                          <option value="<?php echo $row['codigo']; ?>" <?php if($bodegao == $row['codigo']){ echo " selected "; } ?> ><?php echo $row['nombre']; ?></option>
                          <?php }// Termina el while
                        }//Termina el if que valida que si existan resultados
                    }
                    ?>
                </select>
              </div><!-- /.col -->

              <div class="col-sm-3 form-group">
                <label for="bodegad">Bodega Destino</label>
                <select class="form-control" name="bodegad" id="bodegad">
                  <option value="">Todos</option>
                  <?php
                    //if(isset($_POST['proveedor']) && $_POST['proveedor'] != '' ){
                      //$proveedor = $_POST['proveedor'];
                      //$bodegao = 0;
                      if(isset($_POST['bodegad']) && $_POST['bodegad'] != '' ){
                        $bodegad = $_POST['bodegad'];
                      }

                      //$consulta = " select distinct ub.COD_BODEGA_ENTRADA as codigo, b.NOMBRE as nombre from usuarios u inner join usuarios_bodegas ub on u.id = ub.USUARIO inner join bodegas b on b.ID = ub.COD_BODEGA_ENTRADA where u.num_doc = '$proveedor' ";

                      //$consulta = " select distinct ub.COD_BODEGA_ENTRADA as codigo, b.NOMBRE as nombre from usuarios u inner join usuarios_bodegas ub on u.id = ub.usuario inner join bodegas b on ub.COD_BODEGA_ENTRADA = b.ID order by b.NOMBRE asc  ";

                      $consulta = " select distinct ub.COD_BODEGA_ENTRADA as codigo, b.NOMBRE as nombre from usuarios u
                      inner join usuarios_bodegas ub on u.id = ub.USUARIO
                      inner join bodegas b on b.ID = ub.COD_BODEGA_ENTRADA
                      where u.num_doc = '$proveedor' ";

                      $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
                      if($resultado->num_rows >= 1){
                        while($row = $resultado->fetch_assoc()) { ?>
                          <option class ="mayusculas" value="<?php echo $row['codigo']; ?>" <?php if($bodegad == $row['codigo']){ echo " selected "; } ?> ><?php echo $row['nombre']; ?></option>
                          <?php }// Termina el while
                        }//Termina el if que valida que si existan resultados
                    //}
                  ?>

                </select>
              </div><!-- /.col -->
              
            </div><!-- /.row -->

            <div class="row">
              <div class="col-sm-3 form-group">
                    <input type="hidden" id="consultar" name="consultar" value="<?php if (isset($_POST['consultar']) && $_POST['consultar'] != '') {
      echo $_POST['consultar'];
    } ?>" >
    <button onclick="consultarTrazabilidad();" type="button">Buscar</button>
              </div><!-- /.col -->
            </div><!-- /.row -->
              

          </form>

              <?php //var_dump($_POST); ?>
              <?php if(isset($_POST['consultar']) && $_POST['consultar'] == 1){ ?>
          <div class="row">
            <div class="col-sm-12 form-group">
                <h2>Resultados de la Consulta</h2><br/>



  <?php

  if(isset($_POST["mesi"]) && $_POST["mesi"] != "" ){

    // Ajustado formato del mes inicial para hacer el llamado de la tabla con los registros para ese més.
    $mesinicial = $_POST["mesi"];

    if($mesinicial < 10){
      $tablaMes = '0'.$mesinicial;
    }else{
      $tablaMes = $mesinicial;
    }
  }

$bandera = 0;
if($tablaMes == ''){
  $bandera++;
  echo " <h3>Debe seleccionar el mes inicial.</h3> ";
}else if(!isset($_POST["tipoDocumento"]) || $_POST["tipoDocumento"] == "" ){
  $bandera++;
  echo " <h3>Debe seleccionar el tipo de documento.</h3> ";
}else if( !isset($_POST["proveedor"]) || $_POST["proveedor"] == "" ){
  $bandera++;
  echo " <h3>Debe seleccionar Proveedor/Responsable</h3> ";
}else{



  $tablaAnno = $_SESSION['periodoActual'];




  $consulta = " show tables like 'productosmov$tablaMes$tablaAnno' ";
 // echo '<br>'.$consulta.'<br>';
  $result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  $existe = $result->num_rows;
  if($existe > 0){
    $consulta = " show tables like 'productosmovdet$tablaMes$tablaAnno' ";
    //echo '<br>'.$consulta.'<br>';
    $result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
    $existe = $result->num_rows;
    if($existe <= 0){
      $bandera++;
      echo " <h3>No se encontraron registros para este periodo.</h3> ";
    }
  }else{
    $bandera++;
    echo " <h3>No se encontraron registros para este periodo.</h3> ";
  }
}

if($bandera == 0){





  //$consulta="SELECT pd.id, pd.Documento, pd.Numero, p.FechaMYSQL, p.Nitcc, p.Nombre, p.DocOrigen, p.Numero as Numerop, pd.CodigoProducto, pd.Descripcion, pd.Umedida, pd.Factor, pd.CantBodOrg,pd.CantBodDest, pd.BodegaOrigen, pd.BodegaDestino, pd.Lote, pd.FechaVencimiento, p.TipoTransporte, p.Placa, p.ResponsableRecibe,b.NOMBRE as nombreBodegaOrigen, tv.Nombre as nombreTipoTransporte FROM productosmovdet$tablaMes$tablaAnno pd LEFT JOIN productosmov$tablaMes$tablaAnno p ON p.Numero = pd.Numero AND p.Documento = pd.Documento inner join bodegas b on pd.BodegaOrigen = b.id inner join tipovehiculo tv on tv.Id = p.TipoTransporte WHERE 1 = 1";



//  $consulta = " SELECT pd.id, pd.Documento, pd.Numero, p.FechaMYSQL, p.Nitcc, p.Nombre, p.DocOrigen, p.Numero as Numerop, pd.CodigoProducto, pd.Descripcion, pd.Umedida, pd.Factor, pd.CantBodOrg,pd.CantBodDest, pd.BodegaOrigen, pd.BodegaDestino, pd.Lote, pd.FechaVencimiento, p.TipoTransporte, p.Placa, p.ResponsableRecibe,coalesce(b.NOMBRE,'') as nombreBodegaOrigen,coalesce(b2.NOMBRE,'') as nombreBodegaDestino, tv.Nombre as nombreTipoTransporte FROM productosmovdet$tablaMes$tablaAnno pd left JOIN productosmov$tablaMes$tablaAnno p ON pd.Numero = p.Numero AND pd.Documento = p.Documento left join bodegas b on pd.BodegaOrigen = b.id left join bodegas b2 on pd.bodegadestino=b2.id inner join tipovehiculo tv on p.TipoTransporte= tv.Id WHERE ";







  $consulta = " SELECT pd.id, pd.Documento, pd.Numero, p.FechaMYSQL, p.Nitcc, p.Nombre, p.DocOrigen, p.Numero as Numerop, pd.CodigoProducto, pd.Descripcion, pd.Umedida,pd.cantidad, pd.Factor,pd.BodegaOrigen, pd.BodegaDestino, pd.Lote, pd.FechaVencimiento, p.TipoTransporte, p.Placa, p.ResponsableRecibe,coalesce(b.NOMBRE,'') as nombreBodegaOrigen, coalesce(b2.NOMBRE,'') as nombreBodegaDestino, tv.Nombre as nombreTipoTransporte FROM productosmovdet$tablaMes$tablaAnno pd left JOIN productosmov$tablaMes$tablaAnno p ON pd.Numero = p.Numero AND pd.Documento = p.Documento left join bodegas b on pd.BodegaOrigen = b.id left join bodegas b2 on pd.bodegadestino=b2.id left join tipovehiculo tv on p.TipoTransporte= tv.nombre WHERE ";








  if(isset($_POST["annoi"]) && $_POST["annoi"] != "" ){
    $annoinicial = $_POST["annoi"];
    $consulta = $consulta." YEAR(p.FechaMYSQL) >= ".$annoinicial." ";
  }

  if(isset($_POST["mesi"]) && $_POST["mesi"] != "" ){
    $mesinicial = $_POST["mesi"];
    $consulta = $consulta." and MONTH(p.FechaMYSQL) >= ".$mesinicial." ";
  }

  if(isset($_POST["diai"]) && $_POST["diai"] != "" ){
    $diainicial = $_POST["diai"];
    $consulta = $consulta." and DAYOFMONTH(p.FechaMYSQL) >= ".$diainicial." ";
  }

  if(isset($_POST["annof"]) && $_POST["annof"] != "" ){
    $annofinal = $_POST["annof"];
    $consulta = $consulta." and YEAR(p.FechaMYSQL) <= ".$annoinicial." ";
  }

  if(isset($_POST["mesf"]) && $_POST["mesf"] != "" ){
    $mesfinal = $_POST["mesf"];
    $consulta = $consulta." and MONTH(p.FechaMYSQL) <= ".$mesfinal." ";
  }

  if(isset($_POST["diaf"]) && $_POST["diaf"] != "" ){
    $diafinal = $_POST["diaf"];
    $consulta = $consulta." and DAYOFMONTH(p.FechaMYSQL) <= ".$diafinal." ";
  }

  if(isset($_POST["tipoDocumento"]) && $_POST["tipoDocumento"] != ""){
    $consulta = $consulta." AND p.Tipo = '".$_POST['tipoDocumento']."' ";
  }

  if(isset($_POST["proveedor"]) && $_POST["proveedor"] != ""){
    $consulta = $consulta." AND p.Nitcc = '".$_POST['proveedor']."' ";
  }

  if(isset($_POST["producto"]) && $_POST["producto"] != ""){
    $consulta = $consulta." AND pd.CodigoProducto = ".$_POST['producto']." ";
  }

  if(isset($_POST["vehiculo"]) && $_POST["vehiculo"] != ""){
    $consulta = $consulta." AND tv.id = '".$_POST['vehiculo']."' ";
  }

  if(isset($_POST["bodegao"]) && $_POST["bodegao"] != ""){
    $consulta = $consulta." AND p.BodegaOrigen = ".$_POST['bodegao']." ";
  }

  if(isset($_POST["bodegad"]) && $_POST["bodegad"] != ""){
    $consulta = $consulta." AND p.BodegaDestino = ".$_POST['bodegad']." ";
  }

   //$consulta = $consulta." limit 10 ";
   //var_dump($_POST);
   // echo "<br><br>".$consulta."<br><br>";
   //echo '<br>'.$consulta.'<br>';








  $result = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
?>





            </div><!-- /.col -->
          </div><!-- /.row -->

<div class="row">
  <div class="col-sm-12 form-group">
    <div class="table-responsive">
      <table width="100%" id="box-table" class="table table-striped table-bordered table-hover">
    <thead>
      <tr>
        <th>Tipo Doc</th>
        <th>Número</th>
        <th>Fecha / Hora</th>
        <th>Nit / CC</th>
        <th>Responsable / Proveedor</th>
        <th>Doc Origen</th>
        <th>Número</th>
        <th>Código Producto / Alimento</th>
        <th>Nombre Producto / Alimento</th>
        <th>Unidad Medida</th>
        <th>Factor</th>
        <th>Cantidad</th>
        <th>Bodega Origen</th>
        <th>Nombre Bodega Origen</th>
        <th>Bodega Destino</th>
        <th>Nombre Bodega Destino</th>
        <th>Lote</th>
        <th>Fecha Vence</th>
        <th>Tipo Transp</th>
        <th>Placa</th>
        <th>Conductor</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()){ ?>
      <tr>
        <td align="center"><?php
        switch ($row['Documento']) {
          case "CO":
            echo "Compra";
            break;
          case "EN":
            echo "Entrada";
            break;
          case "SA":
            echo "Salida";
            break;
          case "TR":
            echo "Traslado";
          case "DES":
            echo "Despacho";
            break;
        }?></td>
        <td align="center"><?php echo $row['Numero']; ?></td>
        <td align="center"><?php echo $row['FechaMYSQL']; ?></td>
        <td align="center"><?php echo $row['Nitcc']; ?></td>
        <td><?php echo $row['Nombre']; ?></td>
        <td><?php echo $row['DocOrigen']; ?></td>
        <td align="center"><?php echo $row['Numerop']; ?></td>
        <td align="center"><?php echo $row['CodigoProducto']; ?></td>
        <td><?php echo $row['Descripcion']; ?></td>




        <td align="center"><?php echo strtolower ($row['Umedida']); ?></td>
        <td align="center"><?php echo $row['Factor']; ?></td>
        <td align="center"><?php echo $row['cantidad']; ?></td>










<!--
        <td align="center"><?php if($row['Documento'] == "CO" || $row['Documento'] == "EN" ){echo $row['CantBodDest']; }else{ echo $row['CantBodOrg'];} ?></td>
-->








        <td align="center"><?php echo $row['BodegaOrigen']; ?></td>
        <td class="mayusculas"> <?php echo $row['nombreBodegaOrigen']; ?> </td>
        <td align="center"><?php echo $row['BodegaDestino']; ?></td>
        <td class="mayusculas"> <?php echo $row['nombreBodegaDestino']; ?> </td>
        <td align="center"><?php echo $row['Lote']; ?></td>
        <td align="center"><?php echo $row['FechaVencimiento']; ?></td>
        <td align="center" class="mayusculas"><?php echo $row['nombreTipoTransporte']; ?></td>
        <td align="center"><?php echo $row['Placa']; ?></td>
        <td class="mayusculas"><?php echo $row['ResponsableRecibe']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
    </div><!-- /.table-responsive -->    
  </div><!-- /.col -->
</div><!-- /.row -->







              <?php 
            }//Termina el if de bandera
            }// Termina el if de si se recibieron parametros post 
            ?>





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

<!-- Section Scripts -->
<script src="<?php echo $baseUrl; ?>/modules/trazabilidad/js/trazabilidad.js"></script>

<!-- Page-Level Scripts -->
<?php if(isset($_POST['consultar']) && $_POST['consultar'] == 1){ ?>
<?php if($bandera == 0){ ?>

<script>
  console.log('Aplicando Data Table');
  dataset1 = $('#box-table').DataTable({
    order: [ 1, 'desc' ],
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
    });
</script>
<?php } ?>
<?php } ?>

<?php mysqli_close($Link); ?>

</body>
</html>