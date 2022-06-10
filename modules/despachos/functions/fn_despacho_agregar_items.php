<?php
include '../../../config.php';
require_once '../../../autentication.php';
require_once '../../../db/conexion.php';

$municipio = '';
$ruta = '';
$tipo = '';
$institucion = '';
$sede = '';
$consecutivo = 0;
$itemsActuales = array();

$dias = $_POST['dias'];
$mes = $_POST['mes'];
$semana = $_POST['semana'];
$municipio = $_POST['municipio'];
$ruta = $_POST['ruta'];
$tipo = $_POST['tipo'];
$institucion = $_POST['institucion'];
$sede = $_POST['sede'];
$consecutivo = $_POST['consecutivo'];

if(isset($_POST['itemsActuales'])){
   $itemsActuales = $_POST['itemsActuales'];
}

$periodoActual = $_SESSION['periodoActual'];

$consulta=" SELECT  DISTINCT s.cod_sede, 
                    u.Ciudad, 
                    s.nom_inst, 
                    s.nom_sede, ";
if ($tipo == 'APS') {
   $consulta .= "s.cod_variacion_menu AS cod_variacion_menu";
}else if ($tipo == 'CAJMPS') {
   $consulta .= "s.cod_variacion_menu_cajmps AS cod_variacion_menu";
}else if ($tipo == 'CAJMRI') {
   $consulta .= "s.cod_variacion_menu_cajmri AS cod_variacion_menu";
}else{
   $consulta .= "s.cod_variacion_menu AS cod_variacion_menu";
}

$consulta.= " FROM sedes$periodoActual s
              LEFT JOIN ubicacion u on s.cod_mun_sede = u.CodigoDANE and u.ETC = 0
              LEFT JOIN sedes_cobertura sc on s.cod_sede = sc.cod_sede
              WHERE 1=1 ";

if ($mes  != '') {
   $consulta = $consulta." AND sc.mes = '$mes' ";
}

if ($semana != '') {
   $consulta = $consulta." AND sc.semana = '$semana' ";
}

if($municipio != ''){
   $consulta = $consulta." AND s.cod_mun_sede = '$municipio' ";
}

if($tipo != ''){
   $consulta = $consulta." AND sc.$tipo > 0 ";
   $consulta = $consulta." AND (sc.Etario1_$tipo > 0 || sc.Etario2_$tipo > 0 || sc.Etario3_$tipo > 0 ) ";
}

if($institucion != ''){
   $consulta = $consulta." AND s.cod_inst = '$institucion' ";
}

if($sede != ''){
   $consulta = $consulta." AND s.cod_sede = '$sede' ";
}

if($ruta != ''){
   $consulta = $consulta." AND s.cod_sede IN (select cod_sede from rutasedes WHERE IDRUTA = $ruta) ";
}

if(count($itemsActuales) > 0){
   for ($i=0; $i < count($itemsActuales) ; $i++) {
      $aux = $itemsActuales[$i];
      $consulta = $consulta." and s.cod_sede != '$aux' ";
   }
}

if ($semana == '') {
   $ultimo_dia = end($dias);
   $consultaNumeroPriorizacion = " SELECT SEMANA FROM planilla_semanas WHERE MES = '$mes' AND DIA = '$ultimo_dia' ";
   $respuestaNumeroPriorizacion = $Link->query($consultaNumeroPriorizacion) or die ('Error al consultar la ultima semana priorizada');
   if ($respuestaNumeroPriorizacion->num_rows > 0) {
      $dataNumeroPriorizacion = $respuestaNumeroPriorizacion->fetch_assoc();
      $semanaBuscada = $dataNumeroPriorizacion['SEMANA'];
      $consulta = $consulta." AND sc.semana = '$semanaBuscada' ";
   }
}

// exit(var_dump($consulta)); 
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
   while($row = $resultado->fetch_assoc()) {
      $consecutivo++;
      ?>
      <tr>
         <td class="text-center"><input type="checkbox" class="i-checks" value="<?php echo $row['cod_sede']; ?>" data-variacion="<?php echo $row['cod_variacion_menu']; ?>" /></td>
         <td><input type="hidden" name="sede<?php echo $consecutivo; ?>" id="sede<?php echo $consecutivo; ?>" value="<?php echo $row['cod_sede']; ?>"><?php echo $row['Ciudad']; ?></td>
         <td><?php echo $row['nom_inst']; ?></td>
         <td><?php echo $row['nom_sede']; ?></td>
      </tr>
   <?php }// Termina el while
}//Termina el if que valida que si existan resultados
