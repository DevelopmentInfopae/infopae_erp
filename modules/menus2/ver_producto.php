<?php
$titulo = 'Ver';
require_once '../../header.php';

$periodoActual = $_SESSION['periodoActual'];

$options = array('g' => array('u' => 'Unidad', 'kg' => 'KiloGramo', 'lb' => 'Libra', 'g' => 'Gramos'), 'cc' => array('u' => 'Unidad', 'lt' => 'Litro', 'cc' => 'Centímetros cúbicos'), 'u' => array('u' => 'Unidad'));

if (isset($_REQUEST['idProducto'])) {
  $idProducto = $_REQUEST['idProducto'];
  $consultaDatosProducto = "select * from productos".$_SESSION['periodoActual']." where Id = ".$idProducto." AND nivel = 3";
  $resultadoDatosProducto = $Link->query($consultaDatosProducto) or die('Unable to execute query. '. mysqli_error($Link));
  if ($resultadoDatosProducto->num_rows > 0) {
    $Producto = $resultadoDatosProducto->fetch_assoc();

    if (substr($Producto['Codigo'], 0, 2) == "01") {
          $link = "index.php";
          $title = "Menú";
          $breadCumb = "menús";
        } else if (substr($Producto['Codigo'], 0, 2) == "02") {
          $link = "ver_preparaciones.php";
          $title = "Preparación";
          $breadCumb = "preparaciones";
        } else if (substr($Producto['Codigo'], 0, 2) == "03" || substr($Producto['Codigo'], 0, 2) == "04") {
          $link = "ver_alimentos.php";
          $title = "Alimento";
          $breadCumb = "alimentos";
        }
  ?>
<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $Producto['Codigo']." ".$title." ".$Producto['Descripcion']; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="<?php echo $baseUrl.'/modules/menus2/'.$link; ?>">Ver <?php echo $breadCumb; ?></a>
      </li>
      <li class="active">
        <strong><?php echo $title; ?></strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <div class="dropdown pull-right">
        <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">  Acciones <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
					<?php if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 0) { ?>
						<li><a onclick="editarProducto(<?php echo $_REQUEST['idProducto']; ?>)"><span class="fa fa-pencil"></span> Editar </a></li>
						<?php if ($Producto['Inactivo'] == 0): ?>
							<li><a data-toggle="modal" data-target="#modalEliminar"  data-codigo="<?php echo $Producto['Codigo']; ?>" data-tipocomplemento="<?php echo $Producto['Cod_Tipo_complemento']; ?>" data-ordenciclo="<?php echo $Producto['Orden_Ciclo']; ?>"><span class="fa fa-trash"></span> Eliminar </a></li>
						<?php else: ?>
							<li><a><span class="fa fa-ban"></span> Estado: <strong>Inactivo</strong></a></li>
						<?php endif ?>
					<?php } ?>



          <li><a href="#" ><span class="fa fa-file-excel-o"></span> Exportar </a></li>
        </ul>
      </div>
    </div><!-- /.title-action -->
  </div><!-- /.col -->
</div><!-- /.row -->


<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-content contentBackground">
          <form class="form" id="formEditarProducto" method="post"> <!-- FORMULARIO EDITAR -->
            <input type="hidden" name="IdProducto" value="<?php echo $idProducto ?>">
            <div class="col-lg-12 row">
              <h3>Datos del producto</h3>
              <div class="form-group col-sm-3">
                <label>Tipo de producto</label>
                <input type="hidden" name="tipoProducto" id="tipoProducto" value="<?php echo substr($Producto['Codigo'], 0, 2); ?>">
                <input type="text" class="form-control" value="<?php echo $Producto['TipodeProducto']; ?>" readonly>
              </div>
              <?php if (substr($Producto['Codigo'], 0, 2) != "01"): $style = "style='display:none;'"; else : $style=""; endif ?>
              <div class="form-group col-sm-3" id="divTipoComplemento" <?php echo $style; ?>>
                <label>Tipo de Complemento</label>
                <input type="text" class="form-control" value="<?php echo $Producto['Cod_Tipo_complemento']; ?>" readonly>
              </div>
              <?php if (substr($Producto['Codigo'], 0, 2) == "01"): $style = "style='display:none;'";  else : $style="";endif ?>
              <div class="form-group col-sm-3" id="divSubTipoProducto" <?php echo $style; ?>>
                <label>SubTipo de producto</label>
                  <?php
                  $consulta1= " SELECT * FROM productos".date('y')." where Codigo like '".substr($Producto['Codigo'], 0, 4)."%' AND nivel = 2";
                  $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($result1->num_rows > 0){
                    if($row1 = $result1->fetch_assoc()){
                      $subTipoProducto = $row1['Descripcion'];
                    }
                  }
                  ?>
                <input type="text" class="form-control" value="<?php echo $subTipoProducto; ?>" readonly>
              </div>
              <div class="form-group col-sm-3">
                <label>Descripción</label>
                <?php if (substr($Producto['Codigo'], 0, 2) == "01"): $readonly = "readonly"; else : $readonly=""; endif ?>
                <input type="text" name="descripcion" class="form-control" value="<?php echo $Producto['Descripcion'] ?>" readonly>
              </div>
              <div class="form-group col-sm-3">
                <label>Código</label>
                <input type="number" name="Codigo" class="form-control" value="<?php echo $Producto['Codigo'] ?>" readOnly>
              </div>
              <?php if ($Producto['Cod_Tipo_complemento'] != 0 && $Producto['Cod_Tipo_complemento'] != ""): ?>
                <div class="form-group col-sm-3">
                  <label>Tipo de complemento</label>
                  <input type="text" name="Cod_Tipo_complemento" class="form-control" value="<?php echo $Producto['Cod_Tipo_complemento'] ?>" readonly>
                </div>
              <?php endif ?>
              <?php if ($Producto['Cod_Grupo_Etario'] != 0): ?>
              <div class="form-group col-sm-3" id="divGrupoEtario"  >
                <label>Grupo Etario</label>
                  <?php
                  $consultaGrupoEtario = "select * from grupo_etario where ID = ".$Producto['Cod_Grupo_Etario'];
                  $resultadoGrupoEtario = $Link->query($consultaGrupoEtario);
                  if ($resultadoGrupoEtario->num_rows > 0) {
                    if ($row = $resultadoGrupoEtario->fetch_assoc()) {
                      $grupoEtario = $row['DESCRIPCION'];
                    }
                  }
                   ?>
                   <input type="text" class="form-control" value="<?php echo $grupoEtario ?>" readonly>
              </div>
              <?php endif ?>
              <?php if (substr($Producto['Codigo'], 0, 2) == "01" || substr($Producto['Codigo'], 0, 2) == "02"): ?>
                <div class="form-group col-sm-3" id="divVariacionMenu" >
                  <label>Variación menú</label>
                  <?php
                  $consulta1= " SELECT * FROM variacion_menu WHERE id = ".$Producto['cod_variacion_menu'];
                  $result1 = $Link->query($consulta1) or die ('Unable to execute query. '. mysqli_error($Link));
                  if($result1->num_rows > 0){
                    if($row1 = $result1->fetch_assoc()){
                      $variacionMenu = $row1['descripcion'];
                    }
                  } else {
                    $variacionMenu = "Sin registrar";
                  }
                  ?>
                  <input type="text" class="form-control" value="<?php echo $variacionMenu ?>" readonly>
                  </select>
                </div>
              <?php endif ?>
              <?php if ($Producto['Orden_Ciclo'] != 0): ?>
                <div class="form-group col-sm-3" id="divOrdenCiclo" >
                  <label>Orden Ciclo</label>
                  <input type="text" name="ordenCiclo" id="ordenCiclo" class="form-control" value="<?php echo $Producto['Orden_Ciclo'] ?>" min="0" step=".01" readOnly>
                </div>
              <?php endif ?>
              <?php if (substr($Producto['Codigo'], 0, 2) == "01"): ?>
                <input type="hidden" name="TipoDespacho" value="99">
              <?php elseif (substr($Producto['Codigo'], 0, 2) == "02" || substr($Producto['Codigo'], 0, 2) == "04"): ?>
                <input type="hidden" name="TipoDespacho" value="0">
              <?php else: ?>
              <div class="form-group col-sm-3">
                <label>Tipo de despacho</label>
                    <?php
                  $consultaTipoDespacho = "select * from tipo_despacho where Id = ".$Producto['TipoDespacho'];
                  $resultadoTipoDespacho = $Link->query($consultaTipoDespacho);
                  if ($resultadoTipoDespacho->num_rows > 0) {
                    if ($row = $resultadoTipoDespacho->fetch_assoc()) {
                      $tipoDespacho = $row['Descripcion'];
                    }
                  } else {
                    $tipoDespacho = "Sin registrar";
                  }
                   ?>
                  <input type="text" class="form-control" value="<?php echo $tipoDespacho ?>" readonly>
              </div>
              <?php endif ?>

              <hr class="col-sm-11">
              <?php
              if ($Producto['NombreUnidad1'] == "g" && $Producto['NombreUnidad3'] != "") {
                $UnidadMedida1 = "g";
                $style = "";
              } else if ($Producto['NombreUnidad1'] == "g" && $Producto['NombreUnidad3'] == ""){
                $UnidadMedida1 = "u";
                $style = "display:none;";
              } else if ($Producto['NombreUnidad1'] == "cc" && $Producto['NombreUnidad3'] != "") {
                $UnidadMedida1 = "cc";
                $style = "";
              } else if ($Producto['NombreUnidad1'] == "cc" && $Producto['NombreUnidad3'] == ""){
                $UnidadMedida1 = "u";
                $style = "display:none;";
              } else if ($Producto['NombreUnidad1'] == "u"){
                $UnidadMedida1 = "u";
                $style = "display:none;";
              }
              ?>
               <?php if ($Producto['NombreUnidad1'] != ""): ?>
                <div class="form-group col-sm-3">
                  <label>Unidad de medida</label>
                  <?php if (substr($Producto['Codigo'], 0, 2) == "01" || substr($Producto['Codigo'], 0, 2) == "02"): ?>
                    <input type="hidden" name="unidadMedida" value="u">
                    <input type="text" name="" value="Unidad" class="form-control" readonly>
                  <?php else:?>
                      <?php if ($Producto['NombreUnidad1'] == "u"): ?>
                      <input type="text" class="form-control" value="Unidad" readonly>
                      <?php elseif ($Producto['NombreUnidad1'] == "g"): ?>
                      <input type="text" class="form-control" value="Gramos" readonly>
                      <?php elseif ($Producto['NombreUnidad1'] == "cc"): ?>
                      <input type="text" class="form-control" value="Centímetros cúbicos" readonly>
                      <?php endif ?>
                  <?php endif ?>
                </div>
              <?php endif ?>
              <div  id="medidasPresentacion">
              <?php if ($Producto['NombreUnidad3'] == "gr" ||  $Producto['NombreUnidad3'] == "cc"): ?>
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
                    <input type="number" name="cantPresentacion[1]"  id="cantPresentacion" class="form-control" value="<?php echo $Producto['CantidadUnd2']*1000 ?>" min="0" readonly>
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
                    <input type="number" name="cantPresentacion[1]" id="cantPresentacion" class="form-control" value="<?php echo round(1/$Producto['CantidadUnd1']) ?>" min="0" readonly>
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
                    <input type="number" name="cantPresentacion[1]" id="cantPresentacion" class="form-control" value="<?php echo round(1/$Producto['CantidadUnd1']) ?>" min="0" readonly>
                  </div>
                </div>
              <?php endif ?>
              <?php if ($Producto['NombreUnidad3'] != ""): ?>
                <div id="medida_2">
                  <div class="form-group col-sm-3">
                    <label>Unidad de medida 2</label>
                    <input type="text" class="form-control" value="<?php echo $options[$UnidadMedida1][$UnidadMedida1] ?>" readonly>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 2</label>
                    <input type="number" name="cantPresentacion[2]" id="cantPresentacion2" class="form-control" value="<?php echo $Producto['CantidadUnd3']*1000 ?>" min="0" readonly>
                  </div>
                </div>
              <?php endif ?>
              <?php if ($Producto['NombreUnidad4'] != ""): ?>
                <div id="medida_3">
                  <div class="form-group col-sm-3">
                    <label>Unidad de medida 3</label>
                    <input type="text" class="form-control" value="<?php echo $options[$UnidadMedida1][$UnidadMedida1] ?>" readonly>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 3</label>
                    <input type="number" name="cantPresentacion[3]" id="cantPresentacion3" class="form-control" value="<?php echo $Producto['CantidadUnd4']*1000 ?>" min="0" readonly>
                  </div>
                </div>
              <?php endif ?>
              <?php if ($Producto['NombreUnidad5'] != ""): ?>
                <div id="medida_4">
                  <div class="form-group col-sm-3">
                    <label>Unidad de medida 4</label>
                    <input type="text" class="form-control" value="<?php echo $options[$UnidadMedida1][$UnidadMedida1] ?>" readonly>
                  </div>
                  <div class="form-group col-sm-3">
                    <label>Cantidad de medida 4</label>
                    <input type="number" name="cantPresentacion[4]" id="cantPresentacion4" class="form-control" value="<?php echo $Producto['CantidadUnd5']*1000 ?>" min="0" readonly>
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
              <h3>Productos ficha técnica</h3>
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
                    </tr>
                  </thead>
                  <tbody id="tbodyProductos">
                <?php while ($fichatecnicadet = $resultadoFichaTecnicaDet->fetch_assoc()) {
                  $cntFTD++;
                  ?>
                      <input type="hidden" name="IdFTDet[<?php echo $cntFTD ?>]" value="<?php echo $fichatecnicadet['Id'] ?>">
                          <tr id="filaProductoFichaTecnicaDet<?php echo $cntFTD; ?>" class="productoFichaTecnicaDet">
                            <td>
                                <input type="text" class="form-control" value="<?php echo $fichatecnicadet['Componente']; ?>" readonly>
                            </td>
                            <td class="datoPreparado" style="display: none;">
                              <input type="text" class="form-control" name="unidadMedidaProducto[<?php echo $cntFTD ?>]" id="unidadMedidaProducto<?php echo $cntFTD ?>"  value="<?php echo $fichatecnicadet['UnidadMedida']; ?>" readonly>
                            </td>
                            <td class="datoPreparado" style="display: none;">
                              <input type="number" min="0" class="form-control" name="cantidadProducto[<?php echo $cntFTD ?>]" id="cantidadProducto<?php echo $cntFTD ?>"  value="<?php echo $fichatecnicadet['Cantidad']; ?>" onchange="cambiarPesos(this, <?php echo $cntFTD ?>);" step=".01" readonly>
                            </td>
                            <td class="datoPreparado" style="display: none;">
                              <input type="number" min="0" class="form-control" name="pesoBrutoProducto[<?php echo $cntFTD ?>]" id="pesoBrutoProducto<?php echo $cntFTD ?>" value="<?php echo $fichatecnicadet['PesoBruto']; ?>" step=".01" readonly>
                            </td>
                            <td class="datoPreparado" style="display: none;">
                              <input type="number" min="0" class="form-control" name="pesoNetoProducto[<?php echo $cntFTD ?>]" id="pesoNetoProducto<?php echo $cntFTD ?>" value="<?php echo $fichatecnicadet['PesoNeto']; ?>" step=".01" readonly>
                            </td>
                          </tr>
                  <?php } ?>
                    </tbody>
                  </table>
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
                  <input type="number" name="kcalxg" class="form-control" value="<?php echo $calynut['kcalxg'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Kcal desde la grasa</label>
                  <input type="number" name="kcaldgrasa" class="form-control" value="<?php echo $calynut['kcaldgrasa'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Grasa saturada</label>
                  <input type="number" name="Grasa_Sat" class="form-control" value="<?php echo $calynut['Grasa_Sat'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Grasa poliinsaturada</label>
                  <input type="number" name="Grasa_poliins" class="form-control" value="<?php echo $calynut['Grasa_poliins'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Grasa monoinsaturada</label>
                  <input type="number" name="Grasa_monoins" class="form-control" value="<?php echo $calynut['Grasa_Monoins'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Grasa trans</label>
                  <input type="number" name="Grasa_Trans" class="form-control" value="<?php echo $calynut['Grasa_Trans'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Fibra dietaria</label>
                  <input type="number" name="Fibra_dietaria" class="form-control" value="<?php echo $calynut['Fibra_dietaria'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Azúcares</label>
                  <input type="number" name="Azucares" class="form-control" value="<?php echo $calynut['Azucares'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Proteínas</label>
                  <input type="number" name="Proteinas" class="form-control" value="<?php echo $calynut['Proteinas'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Colesterol</label>
                  <input type="number" name="Colesterol" class="form-control" value="<?php echo $calynut['Colesterol'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Sodio</label>
                  <input type="number" name="Sodio" class="form-control" value="<?php echo $calynut['Sodio'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Zinc</label>
                  <input type="number" name="Zinc" class="form-control" value="<?php echo $calynut['Zinc'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Calcio</label>
                  <input type="number" name="Calcio" class="form-control" value="<?php echo $calynut['Calcio'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Hierro</label>
                  <input type="number" name="Hierro" class="form-control" value="<?php echo $calynut['Hierro'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Vitamina A</label>
                  <input type="number" name="Vit_A" class="form-control" value="<?php echo $calynut['Vit_A'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Vitamina C</label>
                  <input type="number" name="Vit_C" class="form-control" value="<?php echo $calynut['Vit_C'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Vitamina B1</label>
                  <input type="number" name="Vit_B1" class="form-control" value="<?php echo $calynut['Vit_B1'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Vitamina B2</label>
                  <input type="number" name="Vit_B2" class="form-control" value="<?php echo $calynut['Vit_B2'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Vitamina B3</label>
                  <input type="number" name="Vit_B3" class="form-control" value="<?php echo $calynut['Vit_B3'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Ácido fólico</label>
                  <input type="number" name="Acido_Fol" class="form-control" value="<?php echo $calynut['Acido_Fol'] ?>" min="0" step=".01" readonly>
                </div>
                <div class="col-sm-2">
                  <label>Referencia TCA</label>
                  <input type="text" name="Referencia" class="form-control" value="<?php echo $calynut['Referencia'] ?>"  readonly>
                </div>
                <div class="col-sm-2">
                  <label>Código TCA</label>
                  <input type="text" name="cod_Referencia" class="form-control" value="<?php echo $calynut['cod_Referencia'] ?>"  readonly>
                </div>
                <hr class="col-sm-12">
              </div>
           <?php  } //Si el producto tiene aportes calorías y nutrientes
             ?>

          </form> <!-- FORMULARIO EDITAR -->
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

<!-- Modal -->
<div class="modal fade bs-example-modal-sm" id="modalEditando" tabindex="-1" role="dialog" aria-labelledby="modalEditandoLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalEditandoLabel">Actualizando datos...</h4>
      </div>
      <div class="modal-body">
        Los datos se están actualizando, por favor espere....
      </div>
    </div>
  </div>
</div>
<form method="Post" id="editar_producto" action="editar_producto.php" style="display: none;">
  <input type="hidden" name="idProducto" id="idProductoEditar">
</form>
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
    /*
    if ($('#IdFT').val() != null) {
      for (var i = <?php if(isset($cntFTD)){echo $cntFTD;} else {echo 0;} ?>; i > 0; i--) {
        obtenerProductos(i);
      }
    }*/

    if ($('#tipoProducto').val() == "01") {
      ocultarDatosDetPreparado(1);
    } else if ($('#tipoProducto').val() == "02") {
      ocultarDatosDetPreparado(2);
    }


</script>
<?php mysqli_close($Link); ?>

</body>
</html>
