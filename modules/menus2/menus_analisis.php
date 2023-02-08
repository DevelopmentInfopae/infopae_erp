<?php
$titulo = 'Menú';
require_once '../../header.php';
set_time_limit (0);
ini_set('memory_limit','6000M');

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

$periodoActual = $_SESSION['periodoActual'];
require_once '../../db/conexion.php';
$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");
?>



<?php
  $idProducto = $_POST['idProducto'];

  // echo "PRODUCTO : ".$idProducto;
  $titulo = $_POST['descripcion'];
  $codigoMenu = $_POST['codigo'];
  $annoActual = $_SESSION['periodoActual'];
  $consultaDatosProducto = "select * from productos".$_SESSION['periodoActual']." where Id = ".$idProducto." AND nivel = 3";
  $resultadoDatosProducto = $Link->query($consultaDatosProducto) or die('Unable to execute query. '. mysqli_error($Link).$consultaDatosProducto);
  if ($resultadoDatosProducto->num_rows > 0) {
    $Producto = $resultadoDatosProducto->fetch_assoc();
  }
?>


<div class="row wrapper wrapper-content border-bottom white-bg page-heading">
  <div class="col-lg-8">
    <h2><?php echo $codigoMenu.' '.$titulo; ?></h2>
    <ol class="breadcrumb">
      <li>
        <a href="<?php echo $baseUrl; ?>">Inicio</a>
      </li>
      <li>
        <a href="index.php">Ver menús</a>
      </li>
      <li class="active">
        <strong>Menú</strong>
      </li>
    </ol>
  </div><!-- /.col -->
  <div class="col-lg-4">
    <div class="title-action">
      <div class="dropdown pull-right">
        <button class="btn btn-primary" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true">  Acciones <span class="caret"></span>
        </button>
        <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
          <?php if ($_SESSION['perfil'] == "0" || $permisos['menus'] == "2") { ?>
            <li><a onclick="editarProducto(<?php echo $idProducto; ?>)"><span class="fas fa-pencil-alt"></span> Editar </a></li>
            <?php if ($Producto['Inactivo'] == 0): ?>
              <li><a data-toggle="modal" data-target="#modalEliminar"  data-codigo="<?php echo $Producto['Codigo']; ?>" data-tipocomplemento="<?php echo $Producto['Cod_Tipo_complemento']; ?>" data-ordenciclo="<?php echo $Producto['Orden_Ciclo']; ?>"><span class="fa fa-trash"></span> Eliminar </a></li>
            <?php else: ?>
              <li><a><span class="fa fa-ban"></span> Estado: <strong>Inactivo</strong></a></li>
            <?php endif ?>
          <?php } ?>
          <li><a  onclick="exportarMenu('<?php echo $Producto['Codigo']; ?>')"><span class="fa fa-file-excel-o"></span> Exportar </a></li>
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
<?php

      // echo "<br>Id del producto ".$idProducto."<br>";

// 1. Producto
      // 2. Subproductos
      // 3. Materias primas

      // 1. Producto
      $consultaProducto = "select id from fichatecnica where Codigo = '".$_POST['codigo']."'";

      //echo "<br>Consulta producto<br>".$consultaProducto."<br>";

      $result = $Link->query($consultaProducto) or die ('Unable to execute query. '. mysqli_error($Link));
      $row = $result->fetch_assoc();
      $idProducto = $row['id'];



      // 2. Subproductos
      $consultaSubProductos =  "SELECT f.id as idFichaTecnica,fd.* FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = '".$idProducto."' AND fd.Componente NOT LIKE '%CONTRAMUESTRA%' ";


      //$consultaSubProductos =  "SELECT f.id as idFichaTecnica,fd.* FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.id = fd.IdFT WHERE fd.IdFT = '".$idProducto."' ";


      //echo "<br>Consulta sub productos<br>".$consultaSubProductos."<br>";

      $result = $Link->query($consultaSubProductos) or die ('Unable to execute query. '. mysqli_error($Link));
      while ($row = $result->fetch_assoc()){
         $subProductos[] = $row;
      }

      // Cantidad de materias y grupo alimenticio del subproducto
      $consultaCantidadesGrupo = "SELECT idft, count(idFT) AS materias, max(cantidad), mac.grupo_alim FROM fichatecnicadet fd LEFT JOIN menu_aportes_calynut mac ON fd.codigo = mac.cod_prod WHERE fd.idFT IN (SELECT f.id FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = '".$idProducto."') AND mac.grupo_alim != \"Contramuestra\" GROUP BY idFT  ";

      //echo "<br>".$consultaCantidadesGrupo."<br>";

      $result = $Link->query($consultaCantidadesGrupo) or die ('Unable to execute query. '. mysqli_error($Link));
      while ($row = $result->fetch_assoc()){
         $cantidadesGrupo[] = $row;
      }

      // 3. Materias primas
      $consultaMateriasPrimas = " SELECT fd.*, mac.* FROM fichatecnicadet fd LEFT JOIN menu_aportes_calynut mac ON fd.codigo = mac.cod_prod WHERE fd.idFT IN (SELECT f.id FROM fichatecnica f LEFT JOIN fichatecnicadet fd ON f.Codigo = fd.codigo WHERE fd.IdFT = '".$idProducto."' ) AND mac.grupo_alim != \"Contramuestra\" ";

      // exit(var_dump($consultaMateriasPrimas));
      // "<br>".$consultaMateriasPrimas."<br>";

      $result = $Link->query($consultaMateriasPrimas) or die ('Unable to execute query. '. mysqli_error($Link));
      while ($row = $result->fetch_assoc()){
         $materiasPrimas[] = $row;
      }






      // Valores del menú para comparar con los totales
      // Debemos ir a la tabla de productos 16 encontrar el grupo etario y poder traer estosdatos.
      $consultaSubProductos =  "SELECT mvn.*
      FROM productos$annoActual  p
      left join menu_valref_nutrientes mvn on p.Cod_Grupo_Etario = mvn.Cod_Grupo_Etario and p.Cod_Tipo_complemento = mvn.Cod_tipo_complemento
      where p.Codigo = '$codigoMenu'";


      //echo "<br>Valores de referencia<br>";
      //echo "<br>".$consultaSubProductos."<br>";


      $result = $Link->query($consultaSubProductos) or die ('Unable to execute query. '. mysqli_error($Link));
      while ($row = $result->fetch_assoc()){
         $valoresMenu = $row;
      }
?>









<?php

   $tpesoNeto = 0;
   $tkcal = 0;
   $tkcaldgrasa =0;
   $tProteinas = 0;
   $tgrasas = 0;
   $tgrasaSaturada = 0;
   $tgrasaInsaturada = 0;
   $tgrasaMonoInsaturada = 0;



   $tgrasaTrans = 0;
   $tcarbohidratos = 0;
   $tFibra_dietaria = 0;
   $tAzucares = 0;
   $tColesterol = 0;
   $tSodio = 0;
   $tZinc = 0;
   $tCalcio = 0;
   $tHierro = 0;
   $tVit_A = 0;
   $tVit_C = 0;
   $tVit_B1 = 0;
   $tVit_B2 = 0;
   $tVit_B3 = 0;
   $tAcido_Fol = 0;
?>
<?php if(!isset($valoresMenu)){
    echo "<h2>No se encontraron valores de referencia para este menú.</h2>";
  }
  else{ ?>

  <div class="tablasMenu">
      <div class="tablaSencillaContenedor">









        <div class="row m-b-xl">
            <div class="col-xs-12">
              <div class="table-responsive">
        <table class="tablaSencilla table table-striped table-bordered table-hover">
          <thead>

            <tr>
              <th rowspan="2">GRUPO ALIMENTICIO</th>
              <th rowspan="2">CODIGO ALIMENTO</th>
              <th rowspan="2">ALIMENTO</th>
              <th rowspan="2">NOMBRE DE INGREDIENTE</th>
              <th rowspan="2">CÓDIGO</th>
              <th rowspan="2">PESO BRUTO<br>(g)</th>
              <th rowspan="2">PESO NETO<br>(g)</th>
              <th colspan="22" style="text-align: center">CALORÍAS Y NUTRIENTES</th>
            </tr>
            <tr>
              <th>CALORÍAS<br>(Kcal)</th>
              <th>Kcal DESDE LA GRASA</th>
              <th>PROTEÍNA<br>(g)</th>
              <th>GRASA<br>(g)</th>
              <th>GRASA SATURADA<br>(g)</th>
              <th>GRASA POLIINSATURADA<br>(g)</th>
              <th>GRASA MONOINSATURADA<br>(g)</th>

              <th>GRASA TRANS<br>(g)</th>
              <th>CARBOHIDRATOS<br>(g)</th>
              <th>FIBRA DIETARIA<br>(g)</th>
              <th>AZÚCARES <br>(g)</th>



              <th>COLESTEROL<br>(mg)</th>
              <th>SODIO<br>(g)</th>
              <th>ZINC<br>(g)</th>
              <th>CALCIO<br>(mg)</th>
              <th>HIERRO<br>(mg)</th>
              <th>VIT A<br>(mg)</th>
              <th>VIT C<br>(mg)</th>
              <th>VIT B1<br>(mg)</th>
              <th>VIT B2<br>(mg)</th>
              <th>VIT B3<br>(mg)</th>
              <th>ÁCIDO FÓLICO<br>(g)</th>

            </tr>
          </thead>
          <tbody>


            <?php

            for ($i=0; $i < count($subProductos) ; $i++) {




              $subproducto = $subProductos[$i]['idFichaTecnica'];


              $indice = 0;






              for ($j=0; $j < count($cantidadesGrupo) ; $j++) {
                if($cantidadesGrupo[$j]['idft'] == $subproducto){
                  $indice = $j;
                }
              }

              $materias = $cantidadesGrupo[$indice]['materias'];

              ?>
              <tr>



                <td rowspan="<?php echo $materias; ?>"> <?php echo $cantidadesGrupo[$indice]['grupo_alim']; ?></td>
                <td rowspan="<?php echo $materias; ?>" style="text-align: center"><?php echo $subProductos[$i]['codigo']; ?></td>
                <td rowspan="<?php echo $materias; ?>" ><?php echo $subProductos[$i]['Componente']; ?> </td>

                <?php




                unset($ingredientes);
                for ($l=0; $l < count($materiasPrimas) ; $l++) {
                  if($materiasPrimas[$l]['IdFT'] == $subproducto){
                    $ingredientes[] = $materiasPrimas[$l];
                  }
                }





                for ($k=0; $k < $materias ; $k++) {
                  $aux = 0;

                  if($k > 0){ ?> <tr> <?php } ?>
                    <td><?php echo $ingredientes[$k]['Componente']; ?></td>
                    <td style="text-align: center"><?php echo $ingredientes[$k]['codigo']; ?></td>
                    <td style="text-align: center"><?php echo $ingredientes[$k]['PesoBruto']; ?></td>
                    <td style="text-align: center"><?php echo $ingredientes[$k]['PesoNeto']; ?></td>



                    <td style="text-align: center">



                      <?php if (is_null($ingredientes[$k]['kcalxg']) ) {
                        echo '0'; $tkcal = $tkcal + 0;
                      }else{
                        $aux = $ingredientes[$k]['kcalxg'];
                        $aux = ($aux * $ingredientes[$k]['PesoNeto'])/100;
                        // Formula de las calorias ultima modificación 20160919
                        //$aux = $aux * 9;
                        //$aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux);
                        echo $aux;
                        $tkcal = $tkcal + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['kcaldgrasa']) ) {
                        echo '0'; $tkcaldgrasa = $tkcaldgrasa + 0;
                      }else{
                        $aux = $ingredientes[$k]['kcaldgrasa'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        $tkcaldgrasa = $tkcaldgrasa + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Proteinas']) ) {
                        echo '0'; $tProteinas = $tProteinas + 0;
                      }else{
                        $aux = $ingredientes[$k]['Proteinas'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        $tProteinas = $tProteinas + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php
                      $grasas = $ingredientes[$k]['Grasa_Sat'] + $ingredientes[$k]['Grasa_poliins'] + $ingredientes[$k]['Grasa_Monoins'] + $ingredientes[$k]['Grasa_Trans'];


                      $grasas = ($grasas / 100) * $ingredientes[$k]['Cantidad'];
                      $grasas = round($grasas, 1);
                      echo $grasas;
                      $tgrasas = $tgrasas + $grasas;
                      ?>
                    </td>

                    <td style="text-align: center">
                      <!-- GRASA SATURADA (g) -->
                      <?php if (is_null($ingredientes[$k]['Grasa_Sat']) ) {
                        echo '0'; $tgrasaSaturada = $tgrasaSaturada + 0;
                      }else{
                        $aux = $ingredientes[$k]['Grasa_Sat'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        $tgrasaSaturada = $tgrasaSaturada + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Grasa_poliins']) ) {
                        echo '0'; $tgrasaInsaturada = $tgrasaInsaturada + 0;
                      }else{
                        $aux = $ingredientes[$k]['Grasa_poliins'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        $tgrasaInsaturada = $tgrasaInsaturada + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Grasa_Monoins']) ) {
                        echo '0'; $tgrasaMonoInsaturada = $tgrasaMonoInsaturada + 0;
                      }else{
                        $aux = $ingredientes[$k]['Grasa_Monoins'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Grasa_Monoins'];
                        $tgrasaMonoInsaturada = $tgrasaMonoInsaturada + $aux;
                      } ?>
                    </td>





                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Grasa_Trans']) ) {
                        echo '0'; $tgrasaTrans = $tgrasaTrans + 0;
                      }else{
                        $aux = $ingredientes[$k]['Grasa_Trans'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Grasa_Trans'];
                        $tgrasaTrans = $tgrasaTrans + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <!-- CORBOHIDRATOS (g) -->
                      <?php

                      if (is_null($ingredientes[$k]['Fibra_dietaria']) || is_null($ingredientes[$k]['Azucares']) ) {
                        echo '0'; $tcarbohidratos = $tcarbohidratos + 0;
                      }else{
                        $aux = $ingredientes[$k]['Fibra_dietaria'] + $ingredientes[$k]['Azucares'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        $tcarbohidratos = $tcarbohidratos + $aux;
                      }


                      ?>
                    </td>

                    <td style="text-align: center">
                      <!-- Fibra dietaria -->
                      <?php if (is_null($ingredientes[$k]['Fibra_dietaria']) ) {
                        echo '0'; $tFibra_dietaria = $tFibra_dietaria + 0;
                      }else{
                        $aux = $ingredientes[$k]['Fibra_dietaria'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Fibra_dietaria'];
                        $tFibra_dietaria = $tFibra_dietaria + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Azucares']) ) {
                        echo '0'; $tAzucares = $tAzucares + 0;
                      }else{
                        $aux = $ingredientes[$k]['Azucares'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;

                        //echo $ingredientes[$k]['Azucares'];
                        $tAzucares = $tAzucares + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Colesterol']) ) {
                        echo '0'; $tColesterol = $tColesterol + 0;
                      }else{
                        $aux = $ingredientes[$k]['Colesterol'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Colesterol'];
                        $tColesterol = $tColesterol + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Sodio']) ) {
                        echo '0'; $tSodio = $tSodio + 0;
                      }else{
                        $aux = $ingredientes[$k]['Sodio'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Sodio'];
                        $tSodio = $tSodio + $aux;
                      } ?>
                    </td>


                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Zinc']) ) {
                        echo '0'; $tZinc = $tZinc + 0;
                      }else{
                        $aux = $ingredientes[$k]['Zinc'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Sodio'];
                        $tZinc = $tZinc + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Calcio']) ) {
                        echo '0'; $tCalcio = $tCalcio + 0;
                      }else{
                        $aux = $ingredientes[$k]['Calcio'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Calcio'];
                        $tCalcio = $tCalcio + $aux;
                      } ?>
                    </td>


                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Hierro']) ) {
                        echo '0'; $tHierro = $tHierro + 0;
                      }else{
                        $aux = $ingredientes[$k]['Hierro'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Hierro'];
                        $tHierro = $tHierro + $aux;
                      } ?>
                    </td>







                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Vit_A']) ) {
                        echo '0'; $tVit_A = $tVit_A + 0;
                      }else{
                        $aux = $ingredientes[$k]['Vit_A'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Vit_A'];
                        $tVit_A = $tVit_A + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Vit_C']) ) {
                        echo '0'; $tVit_C = $tVit_C + 0;
                      }else{
                        $aux = $ingredientes[$k]['Vit_C'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Vit_C'];
                        $tVit_C = $tVit_C + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Vit_B1']) ) {
                        echo '0'; $tVit_B1 = $tVit_B1 + 0;
                      }else{
                        $aux = $ingredientes[$k]['Vit_B1'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Vit_B1'];
                        $tVit_B1 = $tVit_B1 + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Vit_B2']) ) {
                        echo '0'; $tVit_B2 = $tVit_B2 + 0;
                      }else{
                        $aux = $ingredientes[$k]['Vit_B2'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Vit_B2'];
                        $tVit_B2 = $tVit_B2 + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Vit_B3']) ) {
                        echo '0'; $tVit_B3 = $tVit_B3 + 0;
                      }else{
                        $aux = $ingredientes[$k]['Vit_B3'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;
                        //echo $ingredientes[$k]['Vit_B3'];
                        $tVit_B3 = $tVit_B3 + $aux;
                      } ?>
                    </td>

                    <td style="text-align: center">
                      <?php if (is_null($ingredientes[$k]['Acido_Fol']) ) {
                        echo '0'; $tAcido_Fol = $tAcido_Fol + 0;
                      }else{
                        $aux = $ingredientes[$k]['Acido_Fol'];
                        $aux = ($aux / 100) * $ingredientes[$k]['Cantidad'];
                        $aux = round($aux, 1);
                        echo $aux;

                        //echo $ingredientes[$k]['Acido_Fol'];
                        $tAcido_Fol = $tAcido_Fol + $aux;
                      } ?>
                    </td>






                  </tr>
                  <?php } //Termina el for de k donde se imprimen los ingredientes del sub producto ?>

                  <?php } //Termina el for de la i que recorre los sub productos del menu ?>

                </tbody>

                <tfoot>
                  <tr>
                    <td colspan="7" style="text-align: center">TOTAL MENÚ</td>
                    <td style="text-align: center"> <?php echo $tkcal; ?></td>
                    <td style="text-align: center"> <?php echo $tkcaldgrasa; ?></td>
                    <td style="text-align: center"> <?php echo $tProteinas; ?></td>
                    <td style="text-align: center"> <?php echo $tgrasas; ?></td>
                    <td style="text-align: center"> <?php echo $tgrasaSaturada; ?></td>
                    <td style="text-align: center"> <?php echo $tgrasaInsaturada; ?></td>
                    <td style="text-align: center"> <?php echo $tgrasaMonoInsaturada; ?></td>
                    <td style="text-align: center"> <?php echo $tgrasaTrans; ?></td>
                    <td style="text-align: center"> <?php echo $tcarbohidratos; ?></td>
                    <td style="text-align: center"> <?php echo $tFibra_dietaria; ?></td>
                    <td style="text-align: center"> <?php echo $tAzucares; ?></td>
                    <td style="text-align: center"> <?php echo $tColesterol; ?></td>
                    <td style="text-align: center"> <?php echo $tSodio; ?></td>
                    <td style="text-align: center"> <?php echo $tZinc; ?></td>
                    <td style="text-align: center"> <?php echo $tCalcio; ?></td>
                    <td style="text-align: center"> <?php echo $tHierro; ?></td>
                    <td style="text-align: center"> <?php echo $tVit_A; ?></td>
                    <td style="text-align: center"> <?php echo $tVit_C; ?></td>
                    <td style="text-align: center"> <?php echo $tVit_B1; ?></td>
                    <td style="text-align: center"> <?php echo $tVit_B2; ?></td>
                    <td style="text-align: center"> <?php echo $tVit_B3; ?></td>
                    <td style="text-align: center"> <?php echo $tAcido_Fol; ?></td>
                  </tr>
                  <tr>
                    <td colspan="7" style="text-align: center">RECOMENDACIÓN DE INGESTA DE CALORIAS Y NUTRIENTES</td>
                    <td style="text-align: center"><?php echo $valoresMenu['kcalxg']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['kcaldgrasa']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Proteinas']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Grasa_Sat']+$valoresMenu['Grasa_poliins']+$valoresMenu['Grasa_Monoins']+$valoresMenu['Grasa_Trans']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Grasa_Sat']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Grasa_poliins']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Grasa_Monoins']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Grasa_Trans']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Fibra_dietaria'] + $valoresMenu['Azucares']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Fibra_dietaria']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Azucares']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Colesterol']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Zinc']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Sodio']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Calcio']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Hierro']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Vit_A']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Vit_C']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Vit_B1']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Vit_B2']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Vit_B3']; ?></td>
                    <td style="text-align: center"><?php echo $valoresMenu['Acido_Fol']; ?></td>
                  </tr>






                  <?php

                  if($valoresMenu['kcalxg'] != 0){$pkcal = number_format (($tkcal/$valoresMenu['kcalxg'])*100,2);}else{$pkcal = 0;}
                  if($valoresMenu['kcaldgrasa'] != 0){$pkcaldgrasa = number_format (($tkcaldgrasa/$valoresMenu['kcaldgrasa'])*100,2);}else{$pkcaldgrasa = 0;}
                  if($valoresMenu['Proteinas'] != 0){$pProteinas = number_format (($tProteinas/$valoresMenu['Proteinas'])*100,2);}else{$pProteinas = 0;}
                  if(($valoresMenu['Grasa_Sat']+$valoresMenu['Grasa_poliins']+$valoresMenu['Grasa_Monoins']+$valoresMenu['Grasa_Trans']) != 0){$pgrasas = number_format (($tgrasas/($valoresMenu['Grasa_Sat']+$valoresMenu['Grasa_poliins']+$valoresMenu['Grasa_Monoins']+$valoresMenu['Grasa_Trans']))*100,2);}else{$pgrasas = 0;}
                  if($valoresMenu['Grasa_Sat'] != 0){$pgrasaSaturada = number_format (($tgrasaSaturada/$valoresMenu['Grasa_Sat'])*100,2);}else{$pgrasaSaturada = 0;}
                  if($valoresMenu['Grasa_poliins'] != 0){$pgrasaInsaturada = number_format (($tgrasaInsaturada/$valoresMenu['Grasa_poliins'])*100,2);}else{$pgrasaInsaturada = 0;}
                  if($valoresMenu['Grasa_Monoins'] != 0){$pgrasaMonoInsaturada = number_format (($tgrasaMonoInsaturada/$valoresMenu['Grasa_Monoins'])*100,2);}else{$pgrasaMonoInsaturada = 0;}
                  if($valoresMenu['Grasa_Trans'] != 0){$pgrasaTrans = number_format (($tgrasaTrans/$valoresMenu['Grasa_Trans'])*100,2);}else{$pgrasaTrans = 0;}
                  if(($valoresMenu['Fibra_dietaria']+$valoresMenu['Azucares']) != 0){$pcarbohidratos = number_format (($tcarbohidratos/($valoresMenu['Fibra_dietaria']+$valoresMenu['Azucares']))*100,2);}else{$pcarbohidratos = 0;}
                  if($valoresMenu['Fibra_dietaria'] != 0){$pFibra = number_format (($tFibra_dietaria/$valoresMenu['Fibra_dietaria'])*100,2);}else{$pFibra = 0;}
                  if($valoresMenu['Azucares'] != 0){$pAzucares = number_format (($tAzucares/$valoresMenu['Azucares'])*100,2);}else{$pAzucares = 0;}
                  if($valoresMenu['Colesterol'] != 0){$pColesterol = number_format (($tColesterol/$valoresMenu['Colesterol'])*100,2);}else{$pColesterol = 0;}
                  if($valoresMenu['Sodio'] != 0){$pSodio = number_format (($tSodio/$valoresMenu['Sodio'])*100,2);}else{$pSodio = 0;}
                  if($valoresMenu['Zinc'] != 0){$pZinc = number_format (($tZinc/$valoresMenu['Zinc'])*100,2);}else{$pZinc = 0;}
                  if($valoresMenu['Calcio'] != 0){$pCalcio = number_format (($tCalcio/$valoresMenu['Calcio'])*100,2);}else{$pCalcio = 0;}
                  if($valoresMenu['Hierro'] != 0){$pHierro = number_format (($tHierro/$valoresMenu['Hierro'])*100,2);}else{$pHierro = 0;}
                  if($valoresMenu['Vit_A'] != 0){$pVit_A = number_format (($tVit_A/$valoresMenu['Vit_A'])*100,2);}else{$pVit_A = 0;}
                  if($valoresMenu['Vit_C'] != 0){$pVit_C = number_format (($tVit_C/$valoresMenu['Vit_C'])*100,2);}else{$pVit_C = 0;}
                  if($valoresMenu['Vit_B1'] != 0){$pVit_B1 = number_format (($tVit_B1/$valoresMenu['Vit_B1'])*100,2);}else{$pVit_B1 = 0;}
                  if($valoresMenu['Vit_B2'] != 0){$pVit_B2 = number_format (($tVit_B2/$valoresMenu['Vit_B2'])*100,2);}else{$pVit_B2 = 0;}
                  if($valoresMenu['Vit_B3'] != 0){$pVit_B3 = number_format (($tVit_B3/$valoresMenu['Vit_B3'])*100,2);}else{$pVit_B3 = 0;}
                  if($valoresMenu['Acido_Fol'] != 0){$pAcido_Fol = number_format (($tAcido_Fol/$valoresMenu['Acido_Fol'])*100,2);}else{$pAcido_Fol = 0;}


                  ?>









                  <tr>
                    <td colspan="7" style="text-align: center">% DE ADECUACIÓN</td>
                    <td style="text-align: center"><?php echo round($pkcal); ?>%</td>
                    <td style="text-align: center"><?php echo round($pkcaldgrasa, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pProteinas, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pgrasas, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pgrasaSaturada, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pgrasaInsaturada, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pgrasaMonoInsaturada, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pgrasaTrans, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pcarbohidratos, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pFibra, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pAzucares, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pColesterol, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pSodio, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pZinc, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pCalcio, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pHierro, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pVit_A, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pVit_C, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pVit_B1, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pVit_B2, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pVit_B3, 1); ?>%</td>
                    <td style="text-align: center"><?php echo round($pAcido_Fol, 1); ?>%</td>
                  </tr>


                </tfoot>

              </table>
                    </div><!-- /.table-responsive -->
            </div><!-- /.col -->
          </div><!--- /.row -->








            </div>

            <div class="tablaNutricionalContenedor">





      <div class="row" style="text-align: center;">

            <div class="col-xs-4" style="display:inline-block;">
              <div class="table-responsive">


              <table class="tablaNutricional table table-striped table-bordered table-hover" style="text-align: center">
                <thead>
                  <tr><th colspan="3" style="text-align: center; text-transform: uppercase;">Datos de Nutrición</th></tr>
                  <tr>
                    <td>Tamaño de Porción</td>
                    <td style="text-align: center"><?php echo $tpesoNeto; ?> g</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>Calorías (energía)</td>
                    <td style="text-align: center"><?php echo $tkcal; ?> kcal</td>
                    <td style="text-align: center"><?php echo number_format ($tkcal*4.186,2); ?> kj</td>

                  </tr>
                  <tr>
                    <td>Calorías desde la grasa</td>
                    <td style="text-align: center"><?php echo $tkcaldgrasa; ?> kcal</td>
                    <td style="text-align: center"><?php echo number_format ($tkcaldgrasa*4.186,2); ?> kj</td>

                  </tr>
                  <tr class="separadorNutricional"><td></td> <td></td> <td></td></tr>
                  <tr> <td><strong>Cantidad por porción</strong></td> <td style="text-align: center"><strong>g</strong></td> <td style="text-align: center"><strong>% VD</strong></td> </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>Grasa Total</strong></td>
                    <td style="text-align: center"><?php echo $tgrasas; ?></td>
                    <td style="text-align: center"><?php echo $pgrasas; ?>%</td>
                  </tr>
                  <tr>
                    <td class="subIndice"><strong>Grasa Saturada</strong></td>
                    <td style="text-align: center"><?php echo $tgrasaSaturada; ?></td>
                    <td style="text-align: center"><?php echo $pgrasaSaturada; ?>%</td>
                  </tr>
                  <tr>
                    <td class="subIndice"><strong>Grasa Poliinsaturada</strong></td>
                    <td style="text-align: center"><?php echo $tgrasaInsaturada; ?></td>
                    <td style="text-align: center"><?php echo $pgrasaInsaturada; ?>%</td>
                  </tr>

                  <tr>
                    <td class="subIndice"><strong>Grasa Mono Insaturada</strong></td>
                    <td style="text-align: center"><?php echo $tgrasaMonoInsaturada; ?></td>
                    <td style="text-align: center"><?php echo $pgrasaMonoInsaturada; ?>%</td>
                  </tr>
                  <tr>
                    <td class="subIndice"><strong>Grasa Trans</strong></td>
                    <td style="text-align: center"><?php echo $tgrasaTrans; ?></td>
                    <td style="text-align: center"><?php echo $pgrasaTrans; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Colesterol</strong></td>
                    <td style="text-align: center"><?php echo $tColesterol; ?></td>
                    <td style="text-align: center"><?php echo $pColesterol; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Sodio</strong></td>
                    <td style="text-align: center"><?php echo $tSodio; ?></td>
                    <td style="text-align: center"><?php echo $pSodio; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Zinc</strong></td>
                    <td style="text-align: center"><?php echo $tZinc; ?></td>
                    <td style="text-align: center"><?php echo $pZinc; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Carbohidrato</strong></td>
                    <td style="text-align: center"><?php echo $tcarbohidratos; ?></td>
                    <td style="text-align: center"><?php echo $pcarbohidratos; ?>%</td>
                  </tr>
                  <tr>
                    <td class="subIndice"><strong>Fibra Dietaría</strong></td>
                    <td style="text-align: center"><?php echo $tFibra_dietaria; ?></td>
                    <td style="text-align: center"><?php echo $pFibra; ?>%</td>
                  </tr>
                  <tr>
                    <td class="subIndice"><strong>Azúcares</strong></td>
                    <td style="text-align: center"><?php echo $tAzucares; ?></td>
                    <td style="text-align: center"><?php echo $pAzucares; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Proteína</strong></td>
                    <td style="text-align: center"><?php echo $tProteinas; ?></td>
                    <td style="text-align: center"><?php echo $pProteinas; ?>%</td>
                  </tr>

                  <tr class="separadorNutricional"><td></td> <td></td> <td></td></tr>

                  <tr>
                    <td><strong>Calcio</strong></td>
                    <td style="text-align: center"><?php echo $tCalcio; ?></td>
                    <td style="text-align: center"><?php echo $pCalcio; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Hierro</strong></td>
                    <td style="text-align: center"><?php echo $tHierro; ?></td>
                    <td style="text-align: center"><?php echo $pHierro; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Vitamina A</strong></td>
                    <td style="text-align: center"><?php echo $tVit_A; ?></td>
                    <td style="text-align: center"><?php echo $pVit_A; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Vitamina C</strong></td>
                    <td style="text-align: center"><?php echo $tVit_C; ?></td>
                    <td style="text-align: center"><?php echo $pVit_C; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Vitamina B1</strong></td>
                    <td style="text-align: center"><?php echo $tVit_B1; ?></td>
                    <td style="text-align: center"><?php echo $pVit_B1; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Vitamina B2</strong></td>
                    <td style="text-align: center"><?php echo $tVit_B2; ?></td>
                    <td style="text-align: center"><?php echo $pVit_B2; ?>%</td>
                  </tr>

                  <tr>
                    <td><strong>Vitamina B3</strong></td>
                    <td style="text-align: center"><?php echo $tVit_B3; ?></td>
                    <td style="text-align: center"><?php echo $pVit_B3; ?>%</td>
                  </tr>




                  <tr>
                    <td><strong>Ácido Fólico</strong></td>
                    <td style="text-align: center"><?php echo $tAcido_Fol; ?></td>
                    <td style="text-align: center"><?php echo $pAcido_Fol; ?>%</td>
                  </tr>



                  <tr><td colspan="3">*Los porcentajes de valores diarios están basados en una dieta de <?php echo $valoresMenu['kcalxg']; ?> calorías. Sus valores dietarios pueden ser mayores o menores dependiendo de sus necesidades calóricas</td></tr>

                </tbody>






              </table>
                               </div><!-- /.table-responsive -->
            </div><!-- /.col -->
          </div><!--- /.row -->

            </div><!-- Termina el div tablaNutricionalContenedor -->

          </div><!-- Termina el div de tablas menu -->


  <?php } ?>








          <div class="row">
            <div class="col-xs-12">
              <div class="table-responsive">
              </div><!-- /.table-responsive -->
            </div><!-- /.col -->
          </div><!--- /.row -->





        </div><!-- /.ibox-content -->
      </div><!-- /.ibox float-e-margins -->
    </div><!-- /.col-lg-12 -->
  </div><!-- /.row -->
</div><!-- /.wrapper wrapper-content animated fadeInRight -->
<form method="Post" id="editar_producto" action="editar_producto.php" style="display: none;">
  <input type="hidden" name="idProducto" id="idProductoEditar">
</form>
<form method="Post" id="exportar_menu" action="exportar_menu.php" target="_blank" style="display: none;">
  <input type="hidden" name="idProducto" id="idProductoExportar">
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

<!-- Scripts sección del modulo -->
<script src="<?php echo $baseUrl; ?>/modules/menus2/js/menus.js"></script>
<!-- Page-Level Scripts -->

<?php mysqli_close($Link); ?>

</body>
</html>