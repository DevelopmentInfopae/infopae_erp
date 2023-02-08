<?php 

require_once '../../header.php'; 

if ($permisos['menus'] == "0") {
  ?><script type="text/javascript">
      window.open('<?= $baseUrl ?>', '_self');
  </script>
<?php exit(); }
else {
  ?><script type="text/javascript">
      const list = document.querySelector(".li_menus");
      list.className += " active ";
  </script>
  <?php
  }

if ($_SESSION['perfil'] == "0" || $permisos['menus'] == "2") {} else { echo "<script>location.href='$baseUrl';</script>"; } 
$periodoActual = $_SESSION['periodoActual'];

$options = array('g' => array('u' => 'Unidad', 'kg' => 'KiloGramo', 'lb' => 'Libra', 'g' => 'Gramos'), 'cc' => array('u' => 'Unidad', 'lt' => 'Litro', 'cc' => 'Centímetros cúbicos'), 'u' => array('u' => 'Unidad'));

?>
<?php if (isset($_REQUEST['idProducto'])) { 
  $idProducto = $_REQUEST['idProducto'];
  $consultaDatosProducto = "select * from productos".date('y')." where Id = ".$idProducto." AND nivel = 3";
  $resultadoDatosProducto = $Link->query($consultaDatosProducto) or die('Unable to execute query. '. mysqli_error($Link));
  if ($resultadoDatosProducto->num_rows > 0) {
    $Producto = $resultadoDatosProducto->fetch_assoc();

    if (substr($Producto['Codigo'], 0, 2) == "01") {
          $nameLabel = get_titles('menus', 'menus', $labels);
          $titulo = $nameLabel. ' - Editar';
          $link = "index.php";
          $title = "Menú";
          $breadCumb = "menús";
        } else if (substr($Producto['Codigo'], 0, 2) == "02") {
          $nameLabel = get_titles('menus', 'preparaciones', $labels);
          $titulo = $nameLabel. ' - Editar';
          $link = "ver_preparaciones.php";
          $title = "Preparación";
          $breadCumb = "preparaciones";
        } else if (substr($Producto['Codigo'], 0, 2) == "03" || substr($Producto['Codigo'], 0, 2) == "04") {
          $nameLabel = get_titles('menus', 'alimentos', $labels);
          $titulo = $nameLabel. ' - Editar';
          $link = "ver_alimentos.php";
          $title = $titulo;
          $breadCumb = "alimentos";
        }
  ?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2> <?php echo $titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="<?php echo $baseUrl.'/modules/menus2/'.$link; ?>">Ver <?php echo $nameLabel; ?></a>
      </li>
      <li class="active">
        <strong> <?php echo $titulo; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <button class="btn btn-primary" onclick="submitForm('formEditarProducto');"><span class="fa fa-check"></span>  Guardar</button>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form" id="formEditarProducto" method="post"> <!-- FORMULARIO EDITAR -->
            <input type="hidden" name="IdProducto" id="IdProducto" value="<?php echo $idProducto ?>">
            <div class="col-lg-12 row">
              <h3>Datos del <?php echo strtolower($title); ?></h3>
              <div class="form-group col-sm-3">
                <label>Tipo de producto</label>
                <select class="form-control" name="tipoProducto" id="tipoProducto" required>
                  <option value="<?php echo substr($Producto['Codigo'], 0, 2); ?>"><?php echo $Producto['TipodeProducto']; ?></option>
                </select>
              </div>
              <?php if (substr($Producto['Codigo'], 0, 2) != "01"): $style = "style='display:none;'"; else : $style=""; endif ?>
              <div class="form-group col-sm-3" id="divTipoComplemento" <?php echo $style; ?>>
                <label>Tipo de Complemento</label>
                <select class="form-control" name="tipoComplemento" id="tipoComplemento">
                  <option value="">Seleccione...</option>
                  <?php
                  $consulta1= " SELECT * FROM tipo_complemento";
                  $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($result1->num_rows > 0){
                    while($row1 = $result1->fetch_assoc()){ 
                      if ($Producto['Cod_Tipo_complemento'] == $row1['CODIGO']) {
                        $selected = "selected='true'";
                      } else {
                        $selected = "";
                      }
                      ?>
                      <option value="<?php echo $row1['CODIGO']; ?>" <?php echo $selected; ?>>
                        <?php echo $row1['CODIGO']; ?> (<?php echo $row1['DESCRIPCION']; ?> )</option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>
              <?php if (substr($Producto['Codigo'], 0, 2) == "01"): $style = "style='display:none;'";  else : $style="";endif ?>
              <div class="form-group col-sm-3" id="divSubTipoProducto" <?php echo $style; ?>>
                <label>SubTipo de producto</label>
                <select class="form-control" name="subtipoProducto" id="subtipoProducto" required>
                  <?php
                  $consulta1= " SELECT * FROM productos".$_SESSION['periodoActual']." where Codigo like '".substr($Producto['Codigo'], 0, 2)."%' AND nivel = 2";
                  $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($result1->num_rows > 0){
                    while($row1 = $result1->fetch_assoc()){ 
                      if (substr($Producto['Codigo'], 0, 4) == $row1['Codigo']) {
                        $selected = "selected='true'";
                      } else {
                        $selected = "";
                      }
                      ?>
                      <option value="<?php echo $row1['Codigo']; ?>" <?php echo $selected; ?>><?php echo $row1['Descripcion']; ?></option>
                      <?php
                    }
                  }
                  ?>
                </select>
              </div>
              <div class="form-group col-sm-3">
                <label>Descripción</label>
                <?php if (substr($Producto['Codigo'], 0, 2) == "01"): $readonly = "readonly"; else : $readonly=""; endif ?>
                <input type="text" name="descripcion" class="form-control" value="<?php echo $Producto['Descripcion'] ?>" <?php echo $readonly; ?>>
              </div>
              <div class="form-group col-sm-3">
                <label>Código</label>
                <input type="number" name="Codigo" class="form-control" value="<?php echo $Producto['Codigo'] ?>" readOnly>
              </div>
              <?php if ($Producto['Cod_Tipo_complemento'] != 0 && $Producto['Cod_Tipo_complemento'] != ""): ?>
                <div class="form-group col-sm-3">
                  <label>Tipo de complemento</label>
                  <input type="text" name="Cod_Tipo_complemento" class="form-control" value="<?php echo $Producto['Cod_Tipo_complemento'] ?>">
                </div>
              <?php endif ?>
              <?php if (substr($Producto['Codigo'], 0, 2) == "01" || substr($Producto['Codigo'], 0, 2) == "02"): ?>
                <div class="form-group col-sm-3" id="divGrupoEtario"  >
                  <label>Grupo Etario</label>
                  <select class="form-control" name="Cod_Grupo_Etario" id="Cod_Grupo_Etario">
                    <?php if ($Producto['Cod_Grupo_Etario'] == 0): ?>
                      <option value="">Seleccione...</option>
                    <?php endif ?>
                    <?php 
                    $consultaGrupoEtario = "select * from grupo_etario";
                    $resultadoGrupoEtario = $Link->query($consultaGrupoEtario);
                    if ($resultadoGrupoEtario->num_rows > 0) {
                      while ($row = $resultadoGrupoEtario->fetch_assoc()) {
                        if ($Producto['Cod_Grupo_Etario'] == $row['ID']) {
                            $selected = "selected='true'";
                          } else {
                            $selected = "";
                          } 
                        ?>
                        <option value="<?php echo $row['ID'] ?>" <?php echo $selected; ?>><?php echo $row['DESCRIPCION'] ?></option>
                      <?php }
                    }
                     ?>
                  </select>
                </div>
              <?php endif ?>
              <?php if (substr($Producto['Codigo'], 0, 2) == "01" || substr($Producto['Codigo'], 0, 2) == "02"): ?>
                <div class="form-group col-sm-3" id="divVariacionMenu" >
                  <?= $Producto['cod_variacion_menu'] ?>
                  <label>Variación menú</label>
                  <select class="form-control" name="variacionMenu" id="variacionMenu" required>
                    <option value="">Seleccione...</option>
                    <?php
                    $consulta1= " SELECT * FROM variacion_menu";
                    $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                    if($result1->num_rows > 0){
                      while($row1 = $result1->fetch_assoc()){
                      if ($Producto['cod_variacion_menu'] == $row1['id']) {
                        $selected = "selected='true'";
                      } else {
                        $selected = "";
                      } ?>
                        <option value="<?php echo $row1['id']; ?>" <?php echo $selected; ?>><?php echo $row1['descripcion']; ?></option>
                        <?php
                      }
                    }
                    ?>
                  </select>
                </div>
              <?php endif ?>
              <?php if ($Producto['Orden_Ciclo'] != 0): ?>
                <div class="form-group col-sm-3" id="divOrdenCiclo" >
                  <label>Orden Ciclo</label>
                  <input type="text" name="ordenCiclo" id="ordenCiclo" class="form-control" value="<?php echo $Producto['Orden_Ciclo'] ?>" min="0" step=".0001" readOnly>
                </div>
              <?php endif ?>
              <?php if (substr($Producto['Codigo'], 0, 2) == "01"): ?>
                <input type="hidden" name="TipoDespacho" value="99">
              <?php elseif (substr($Producto['Codigo'], 0, 2) == "02" || substr($Producto['Codigo'], 0, 2) == "04"): ?>
                <input type="hidden" name="TipoDespacho" value="0">
              <?php else: ?>
              <div class="form-group col-sm-3">
                <label>Tipo de despacho</label>
                <select class="form-control" name="TipoDespacho" >

                    <?php 
                  $consultaTipoDespacho = "select * from tipo_despacho";
                  $resultadoTipoDespacho = $Link->query($consultaTipoDespacho);
                  if ($resultadoTipoDespacho->num_rows > 0) {
                    $selected = "";
                    $cnt = 0;
                    while ($row = $resultadoTipoDespacho->fetch_assoc()) { 
                      if ($Producto['TipoDespacho'] == $row['Id'] && $Producto['TipoDespacho'] != 0) {
                         $selected = "selected='true'";
                      } else if ($Producto['TipoDespacho'] != $row['Id'] && $Producto['TipoDespacho'] != 0) {
                        $selected = "";
                      } else if ($Producto['TipoDespacho'] == 0 && $cnt == 0){
                        $selected = "";
                        echo "<option value=''>Sin registrar</option>";
                      }
                      ?>
                      <option value="<?php echo $row['Id'] ?>" <?php echo $selected; ?>><?php echo $row['Descripcion'] ?></option>
                    <?php $cnt++; }
                  }
                   ?>
                  </select>
              </div>
              <?php endif ?>
              
              
              <hr class="col-sm-11">
              <?php 
              if ($Producto['NombreUnidad1'] == "g" && $Producto['NombreUnidad3'] != "") {
                $UnidadMedida1 = "g";
                $CantUnd1 = round(1/$Producto['CantidadUnd1']);
                $style = "";
              } else if ($Producto['NombreUnidad1'] == "g" && $Producto['NombreUnidad3'] == ""){
                $UnidadMedida1 = "u";
                $style = "display:none;";
                if ($Producto['NombreUnidad1'] == "g" && $Producto['NombreUnidad2'] != "") {

                  if (!isset($options['g'][$Producto['NombreUnidad2']])) {
                    $UnidadMedida1 = $Producto['NombreUnidad1'];
                    $CantUnd1 = round(1/$Producto['CantidadUnd1']);
                    $style = "";
                  } else {
                    $UnidadMedida1 = $Producto['NombreUnidad2'];
                    $CantUnd1 = $Producto['CantidadUnd2'];
                  }
                  
                }
              } else if ($Producto['NombreUnidad1'] == "cc" && $Producto['NombreUnidad3'] != "") {
                $UnidadMedida1 = "cc";
                $CantUnd1 = round(1/$Producto['CantidadUnd1']);
                $style = "";
              } else if ($Producto['NombreUnidad1'] == "cc" && $Producto['NombreUnidad3'] == ""){
                $UnidadMedida1 = "u";
                $style = "display:none;";
                if ($Producto['NombreUnidad1'] == "cc" && $Producto['NombreUnidad2'] != "") {
                  if (!isset($options['cc'][$Producto['NombreUnidad2']])) {
                    $UnidadMedida1 = $Producto['NombreUnidad1'];
                    $CantUnd1 = round(1/$Producto['CantidadUnd1']);
                    $style = "";
                  } else {
                    $UnidadMedida1 = $Producto['NombreUnidad2'];
                    $CantUnd1 = $Producto['CantidadUnd2'];
                  }
                }
              } else if ($Producto['NombreUnidad1'] == "u"){
                $UnidadMedida1 = "u";
                $style = "display:none;";
              }
              ?>
                <div class="form-group col-sm-12" id="gestionMedidas" style="<?php echo $style; ?>">
                  <label>Añadir/borrar medida presentación</label><br>
                  <label class="btn btn-primary" onclick="anadirMedida();"><span class="fa fa-plus"></span></label>
                  <label class="btn btn-danger" onclick="quitarMedida();"><span class="fa fa-minus"></span></label>
                </div>
               <?php if ($Producto['NombreUnidad1'] != ""): ?>
                <div class="form-group col-sm-3">
                  <label>Unidad de medida</label>
                  <?php if (substr($Producto['Codigo'], 0, 2) == "01" || substr($Producto['Codigo'], 0, 2) == "02"): ?>
                    <input type="hidden" name="unidadMedida" value="u">
                    <input type="text" name="" value="Unidad" class="form-control" readonly>
                  <?php else:?>
                    <select name="unidadMedida" id="unidadMedida" class="form-control">
                      <?php if ($Producto['NombreUnidad1'] == "u"): ?>
                      <option value="u">Unidad</option>
                      <option value="g">Gramos</option>
                      <option value="cc">Centímetros cúbicos</option>
                      <?php elseif ($Producto['NombreUnidad1'] == "g"): ?>
                      <option value="g">Gramos</option>
                      <option value="u">Unidad</option>
                      <option value="cc">Centímetros cúbicos</option>
                      <?php elseif ($Producto['NombreUnidad1'] == "cc"): ?>
                      <option value="cc">Centímetros cúbicos</option>
                      <option value="g">Gramos</option>
                      <option value="u">Unidad</option>
                      <?php endif ?>
                    </select>
                  <?php endif ?>
                </div>
              <?php endif ?>
              <div  id="medidasPresentacion">
              <?php if ($Producto['NombreUnidad3'] == "gr" ||  $Producto['NombreUnidad3'] == "cc" || $Producto['NombreUnidad2'] != ""): ?>
                <div>
                  <div class="form-group col-sm-3">
                    <label>Unidad de medida 1</label>
                    <select name="unidadMedidaPresentacion[1]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion" >
                      <?php 
                      foreach ($options[$Producto['NombreUnidad1']] as $indice => $valor) { 
                        if ($indice == $UnidadMedida1 ) { ?>
                          <option value="<?php echo $indice ?>" selected="true"><?php echo $valor; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $indice ?>"><?php echo $valor; ?></option>
                        <?php }
                      }
                       ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 1</label>
                    <input type="number" name="cantPresentacion[1]"  id="cantPresentacion" class="form-control" value="<?php echo $CantUnd1 ?>" min="0">
                  </div>
                </div>
              <?php elseif(($Producto['NombreUnidad1'] == "gr" || $Producto['NombreUnidad1'] == "cc") && $Producto['NombreUnidad2'] == "u"): ?>
                <div>
                  <div class="form-group col-sm-3" style="display: none;"  id="divUnidadMedidaPresentacion">
                    <label>Unidad de medida 1</label>
                    <select name="unidadMedidaPresentacion[1]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                      <?php 
                      foreach ($options[$Producto['NombreUnidad1']] as $indice => $valor) { 
                        if ($indice == $UnidadMedida1 ) { ?>
                          <option value="<?php echo $indice ?>" selected="true"><?php echo $valor; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $indice ?>"><?php echo $valor; ?></option>
                        <?php }
                      }
                       ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 1</label>
                    <input type="number" name="cantPresentacion[1]" id="cantPresentacion" class="form-control" value="<?php echo round(1/$Producto['CantidadUnd1']) ?>" min="0">
                  </div>
                </div>
              <?php else: ?>
                <div>
                  <div class="form-group col-sm-3" style="display: none;"  id="divUnidadMedidaPresentacion">
                    <label>Unidad de medida 1</label>
                    <select name="unidadMedidaPresentacion[1]" id="unidadMedidaPresentacion" class="form-control unidadMedidaPresentacion">
                      <?php 
                      foreach ($options[$Producto['NombreUnidad1']] as $indice => $valor) { 
                        if ($indice == $UnidadMedida1 ) { ?>
                          <option value="<?php echo $indice ?>" selected="true"><?php echo $valor; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $indice ?>"><?php echo $valor; ?></option>
                        <?php }
                      }
                       ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 1</label>
                    <input type="number" name="cantPresentacion[1]" id="cantPresentacion" class="form-control" value="<?php echo round(1/$Producto['CantidadUnd1']) ?>" min="0">
                  </div>
                </div>
              <?php endif ?>
              <?php if ($Producto['NombreUnidad3'] != ""): ?>
                <div id="medida_2">
                  <div class="form-group col-sm-3">
                    <label>Unidad de medida 2</label>
                    <select name="unidadMedidaPresentacion[2]" id="unidadMedidaPresentacion2" class="form-control unidadMedidaPresentacion" >
                      <?php 
                      foreach ($options[$Producto['NombreUnidad1']] as $indice => $valor) { 
                        if ($indice == $UnidadMedida1 ) { ?>
                          <option value="<?php echo $indice ?>" selected="true"><?php echo $valor; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $indice ?>"><?php echo $valor; ?></option>
                        <?php }
                      }
                       ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 2</label>
                    <input type="number" name="cantPresentacion[2]" id="cantPresentacion2" class="form-control" value="<?php echo $Producto['CantidadUnd3']*1000 ?>" min="0">
                  </div>
                </div>
              <?php endif ?>
              <?php if ($Producto['NombreUnidad4'] != ""): ?>
                <div id="medida_3">
                  <div class="form-group col-sm-3">
                    <label>Unidad de medida 3</label>
                    <select name="unidadMedidaPresentacion[3]" id="unidadMedidaPresentacion3" class="form-control unidadMedidaPresentacion" >
                      <?php 
                      foreach ($options[$Producto['NombreUnidad1']] as $indice => $valor) { 
                        if ($indice == $UnidadMedida1 ) { ?>
                          <option value="<?php echo $indice ?>" selected="true"><?php echo $valor; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $indice ?>"><?php echo $valor; ?></option>
                        <?php }
                      }
                       ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 3</label>
                    <input type="number" name="cantPresentacion[3]" id="cantPresentacion3" class="form-control" value="<?php echo $Producto['CantidadUnd4']*1000 ?>" min="0">
                  </div>
                </div>
              <?php endif ?>
              <?php if ($Producto['NombreUnidad5'] != ""): ?>
                <div id="medida_4">
                  <div class="form-group col-sm-3">
                    <label>Unidad de medida 4</label>
                    <select name="unidadMedidaPresentacion[4]" id="unidadMedidaPresentacion4" class="form-control unidadMedidaPresentacion" >
                      <?php 
                      foreach ($options[$Producto['NombreUnidad1']] as $indice => $valor) { 
                        if ($indice == $UnidadMedida1 ) { ?>
                          <option value="<?php echo $indice ?>" selected="true"><?php echo $valor; ?></option>
                        <?php } else { ?>
                          <option value="<?php echo $indice ?>"><?php echo $valor; ?></option>
                        <?php }
                      }
                       ?>
                    </select>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 4</label>
                    <input type="number" name="cantPresentacion[4]" id="cantPresentacion4" class="form-control" value="<?php echo $Producto['CantidadUnd5']*1000 ?>" min="0">
                  </div>
                </div>
              <?php endif ?>
              </div> <!-- DIV UNIDADES DE MEDIDA -->
              <hr class="col-sm-12">
            </div>
            <?php 
              $consultaFichaTecnica = "select * from fichatecnica where Codigo = ".$Producto['Codigo'];
              $resultadoFichaTecnica = $Link->query($consultaFichaTecnica);

              if ($resultadoFichaTecnica->num_rows > 0) {
                $fichaTecnica = $resultadoFichaTecnica->fetch_assoc();
            ?>
            <input type="hidden" name="IdFT" id="IdFT" value="<?php echo $fichaTecnica['Id'] ?>">
            <div class="col-lg-12 row">
              <!--<h3>Ficha técnica</h3>
              <div class="form-group col-sm-3">
                <label>Id ficha técnica</label>
                <input type="number" name="codigo" class="form-control" value="<?php echo $fichaTecnica['Id']; ?>">
              </div>
              <hr class="col-sm-12">
            </div>
            <div class="col-lg-12 row"> -->
              <h3>Composición del <?php echo $title; ?></h3>
              <?php 
              $consultaFichaTecnicaDet = "select * from fichatecnicadet where IdFT = ".$fichaTecnica['Id'];
              $resultadoFichaTecnicaDet = $Link->query($consultaFichaTecnicaDet);
              if ($resultadoFichaTecnicaDet->num_rows > 0) {
                $cntFTD = 0; ?>
                <div class="form-group col-sm-12">
                <label>Seleccione productos</label>
                <table class="table" id="tablaProductosFichaTecnicaDet">
                  <thead>
                    <tr>
                      <th>Producto</th>
                      <th class="datoPreparado" style="display: none;">Unidad de medida</th>
                      <th class="datoPreparado" style="display: none;">Cantidad</th>
                      <th class="datoPreparado" style="display: none;">Peso Bruto</th>
                      <th class="datoPreparado" style="display: none;">Peso Neto</th>
                      <?php if (substr($Producto['Codigo'], 0, 2) != "04"): ?>
                      <th></th>
                      <?php endif ?>
                    </tr>
                  </thead>
                  <tbody id="tbodyProductos">
                <?php while ($fichatecnicadet = $resultadoFichaTecnicaDet->fetch_assoc()) { 
                  $cntFTD++;
                  ?>
                      <input type="hidden" name="IdFTDet[<?php echo $cntFTD ?>]" value="<?php echo $fichatecnicadet['Id'] ?>">
                          <tr id="filaProductoFichaTecnicaDet<?php echo $cntFTD; ?>" class="productoFichaTecnicaDet">
                            <td>
                              <select class="form-control" name="productoFichaTecnicaDet[<?php echo $cntFTD ?>]" id="productoFichaTecnicaDet<?php echo $cntFTD ?>" onchange="obtenerUnidadMedidaProducto(this, <?php echo $cntFTD ?>);" required>
                                <option value="<?php echo $fichatecnicadet['codigo']; ?>"><?php echo $fichatecnicadet['Componente']; ?></option>
                              </select>
                            </td>
                            <td class="datoPreparado" style="display: none;">
                              <input type="text" class="form-control" name="unidadMedidaProducto[<?php echo $cntFTD ?>]" id="unidadMedidaProducto<?php echo $cntFTD ?>"  value="<?php echo $fichatecnicadet['UnidadMedida']; ?>" readonly>
                            </td>
                            <td class="datoPreparado" style="display: none;">
                              <input type="number" min="0" class="form-control" name="cantidadProducto[<?php echo $cntFTD ?>]" id="cantidadProducto<?php echo $cntFTD ?>"  value="<?php echo $fichatecnicadet['Cantidad']; ?>" onchange="cambiarPesos(this, <?php echo $cntFTD ?>);" step=".0001">
                            </td>
                            <td class="datoPreparado" style="display: none;">
                              <input type="number" min="0" class="form-control" name="pesoBrutoProducto[<?php echo $cntFTD ?>]" id="pesoBrutoProducto<?php echo $cntFTD ?>" value="<?php echo $fichatecnicadet['PesoBruto']; ?>" step=".0001">
                            </td>
                            <td class="datoPreparado" style="display: none;">
                              <input type="number" min="0" class="form-control" name="pesoNetoProducto[<?php echo $cntFTD ?>]" id="pesoNetoProducto<?php echo $cntFTD ?>" value="<?php echo $fichatecnicadet['PesoNeto']; ?>" step=".0001">
                            </td>
                            <?php if (substr($Producto['Codigo'], 0, 2) != "04"): ?>
                            <td>
                              <span class="btn btn-danger btn-sm btn-outline"  data-toggle="modal" data-target="#modalEliminarFTDet" data-idftd="<?php echo $fichatecnicadet['Id']; ?>" data-numftd="<?php echo $cntFTD; ?>" data-idproducto="<?php echo $Producto['Codigo']; ?>" title='Eliminar de la composición'><span class="fa fa-trash"></span></span>
                            </td>
                            <?php endif ?>
                          </tr>
                  <?php } ?>
                    </tbody>
                  </table>
                  <a class="btn btn-primary" onclick="anadirProducto();"><span class="fa fa-plus"></span></a>
                  <a class="btn btn-primary" id="borrarProducto" onclick="borrarProducto();" style="display: none;"><span class="fa fa-minus"></span></a>
                </div>
                <?php } else { //Si el producto no tiene ningún componente en fichatecnicadet ?>
                  <div class="form-group col-sm-12">
                    <table class="table" id="tablaProductosFichaTecnicaDet">
                      <thead>
                        <tr>
                          <th>Producto</th>
                          <th class="datoPreparado" style="display: none;">Unidad de medida</th>
                          <th class="datoPreparado" style="display: none;">Cantidad</th>
                          <th class="datoPreparado" style="display: none;">Peso Bruto</th>
                          <th class="datoPreparado" style="display: none;">Peso Neto</th>
                        </tr>
                      </thead>
                      <tbody id="tbodyProductos">

                      </tbody>
                    </table>
                    <a class="btn btn-primary" onclick="anadirProducto();"><span class="fa fa-plus"></span></a>
                    <a class="btn btn-primary" onclick="borrarProducto();"><span class="fa fa-minus"></span></a>
                  </div>
               <?php }?>
               <hr class="col-sm-12">
            </div>
          <?php }//Si el producto tiene ficha tecnica ?>
           <?php 
            $consultaCalyNut = "select * from menu_aportes_calynut where cod_prod = ".$Producto['Codigo'];
            $resultadoCalyNut = $Link->query($consultaCalyNut);
            if ($resultadoCalyNut->num_rows > 0) { 
              $calynut = $resultadoCalyNut->fetch_assoc();
            ?>
            <input type="hidden" name="IdCalyNut" value="<?php echo $calynut['id'] ?>">
              <div class="col-lg-12 row">
                <h3>Aportes Calorías y Nutrientes</h3>
                <div class="col-sm-2">
                  <label>Calorías (kcal)</label>
                  <input type="number" name="kcalxg" class="form-control" value="<?php echo $calynut['kcalxg'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Kcal desde la grasa</label>
                  <input type="number" name="kcaldgrasa" class="form-control" value="<?php echo $calynut['kcaldgrasa'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Grasa saturada</label>
                  <input type="number" name="Grasa_Sat" class="form-control" value="<?php echo $calynut['Grasa_Sat'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Grasa poliinsaturada</label>
                  <input type="number" name="Grasa_poliins" class="form-control" value="<?php echo $calynut['Grasa_poliins'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Grasa monoinsaturada</label>
                  <input type="number" name="Grasa_monoins" class="form-control" value="<?php echo $calynut['Grasa_Monoins'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Grasa trans</label>
                  <input type="number" name="Grasa_Trans" class="form-control" value="<?php echo $calynut['Grasa_Trans'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Fibra dietaria</label>
                  <input type="number" name="Fibra_dietaria" class="form-control" value="<?php echo $calynut['Fibra_dietaria'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Azúcares</label>
                  <input type="number" name="Azucares" class="form-control" value="<?php echo $calynut['Azucares'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Proteínas</label>
                  <input type="number" name="Proteinas" class="form-control" value="<?php echo $calynut['Proteinas'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Colesterol</label>
                  <input type="number" name="Colesterol" class="form-control" value="<?php echo $calynut['Colesterol'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Sodio</label>
                  <input type="number" name="Sodio" class="form-control" value="<?php echo $calynut['Sodio'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Zinc</label>
                  <input type="number" name="Zinc" class="form-control" value="<?php echo $calynut['Zinc'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Calcio</label>
                  <input type="number" name="Calcio" class="form-control" value="<?php echo $calynut['Calcio'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Hierro</label>
                  <input type="number" name="Hierro" class="form-control" value="<?php echo $calynut['Hierro'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Vitamina A</label>
                  <input type="number" name="Vit_A" class="form-control" value="<?php echo $calynut['Vit_A'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Vitamina C</label>
                  <input type="number" name="Vit_C" class="form-control" value="<?php echo $calynut['Vit_C'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Vitamina B1</label>
                  <input type="number" name="Vit_B1" class="form-control" value="<?php echo $calynut['Vit_B1'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Vitamina B2</label>
                  <input type="number" name="Vit_B2" class="form-control" value="<?php echo $calynut['Vit_B2'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Vitamina B3</label>
                  <input type="number" name="Vit_B3" class="form-control" value="<?php echo $calynut['Vit_B3'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Ácido fólico</label>
                  <input type="number" name="Acido_Fol" class="form-control" value="<?php echo $calynut['Acido_Fol'] ?>" min="0" step=".0001">
                </div>
                <div class="col-sm-2">
                  <label>Referencia TCA</label>
                  <input type="text" name="Referencia" class="form-control" value="<?php echo $calynut['Referencia'] ?>" >
                </div>
                <div class="col-sm-2">
                  <label>Código TCA</label>
                  <input type="text" name="cod_Referencia" class="form-control" value="<?php echo $calynut['cod_Referencia'] ?>" >
                </div>
                <hr class="col-sm-12">
              </div>
           <?php  } //Si el producto tiene aportes calorías y nutrientes
             ?>
          </form> <!-- FORMULARIO EDITAR -->

<?php if (substr($Producto['Codigo'], 0, 2) == "01"): ?>
<form method="Post" id="ver_producto" action="menus_analisis.php" style="display: none;">
  <input type="hidden" name="descripcion" id="descripcion" value="<?php echo $Producto['Descripcion']; ?>">
  <input type="hidden" name="codigo" id="codigo" value="<?php echo $Producto['Codigo']; ?>">
  <input type="hidden" name="idProducto" id="idProducto" value="<?php echo $idProducto; ?>">
</form>
<?php else: ?>
<form method="Post" id="ver_producto" action="ver_producto.php" style="display: none;">
  <input type="hidden" name="idProducto" value="<?php echo $idProducto; ?>">
</form>
<?php endif ?>

          <div class="form-group">
                <button class="btn btn-primary" onclick="submitForm('formEditarProducto');"><span class="fa fa-check"></span>  Guardar</button>
          </div>
          <?php } else { //Si el producto no es nivel 3 ?>
              <div class="col-sm-12">
                <h1>El producto es inválido.</h1>
              </div>
          <?php }
        } else { //Si no se indicó el id del producto ?>
            <div class="col-sm-12">
              <h1>No se ha definido producto.</h1>
            </div>
          <?php } ?>
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
<script src="<?php echo $baseUrl; ?>/modules/menus2/js/menus.js?v=20191209"></script>

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

    if ($('#IdFT').val() != null && $('#tipoProducto').val() != "04") {
      for (var i = <?php if(isset($cntFTD)){echo $cntFTD;} else {echo 0;} ?>; i > 0; i--) {
        obtenerProductos(i);
      }
    //$('#tipoProducto').change();
    //ocultarDatosDetPreparado($('#tipoProducto').val());
    }

    if ($('#tipoProducto').val() == "01") {
      ocultarDatosDetPreparado(1);
    } else if ($('#tipoProducto').val() == "02") {
      ocultarDatosDetPreparado(2);
    }

    $('.productoFichaTecnicaDet select').select2({
      width : "100%"
    });

    console.log(numProducto);
</script>
<?php mysqli_close($Link); ?>

</body>
</html>