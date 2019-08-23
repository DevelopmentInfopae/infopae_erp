<?php
include '../../../config.php';
include  '../../../db/conexion.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$tablaAnno = $_SESSION['periodoActual'];

//var_dump($_POST);
//echo "<br><br>";
$nombre = '';
$documento = '';
$tipoDocumento = '';




$despacho = $_POST['despacho'];
$annoActual = $_SESSION['periodoActualCompleto'];
$tipo = '';
$tipoDespacho = '';
$semana = '';
$dias = '';
$items = array();
$menus = array();

// Son los menus que se van a mostrar en la planilla x,x,x,x,
$menusReg = array();


$menusEtarios = array();
$sedes = array();
$bodegaOrigen = '';
$usuario = '';
$login = '';
$tipoTransporte = '';
$conductor = '';
$placa = '';
$idUsuario = '';


if(isset($_POST['proveedorEmpleado']) && $_POST['proveedorEmpleado'] != ''){
  $documento = $_POST['proveedorEmpleado'];
}

if(isset($_POST['proveedorEmpleadoNm']) && $_POST['proveedorEmpleadoNm'] != ''){
  $nombre = $_POST['proveedorEmpleadoNm'];
}

if(isset($_POST['subtipoNm']) && $_POST['subtipoNm'] != ''){
  $tipoDocumento = $_POST['subtipoNm'];
}












if(isset($_POST['tipo']) && $_POST['tipo'] != ''){
  $tipo = $_POST['tipo'];
  $_SESSION['tipo'] = $tipo;
}


if(isset($_POST['tipoDespacho']) && $_POST['tipoDespacho'] != ''){
  $tipoDespacho = $_POST['tipoDespacho'];
  $_SESSION['tipoDespacho'] = $tipoDespacho;
}

if($tipoDespacho == ''){
  $tipoDespacho = 99;
}









if(isset($_POST['semana']) && $_POST['semana'] != ''){
  $semana = $_POST['semana'];
  $_SESSION['semana'] = $semana;
}

if(isset($_POST['dias']) && $_POST['dias'] != ''){
  $dias = $_POST['dias'];
  //echo "<br>vector de días<br>";
  //var_dump($dias);
}

if(isset($_POST['bodegaOrigen']) && $_POST['bodegaOrigen'] != ''){
  $bodegaOrigen = $_POST['bodegaOrigen'];
}

if(isset($_POST['tipoTransporte']) && $_POST['tipoTransporte'] != ''){
  $tipoTransporte = $_POST['tipoTransporte'];
}

if(isset($_POST['conductor']) && $_POST['conductor'] != ''){
  $conductor = $_POST['conductor'];
}

if(isset($_POST['placa']) && $_POST['placa'] != ''){
  $placa = $_POST['placa'];
}

if(isset($_POST['itemsDespacho']) && $_POST['itemsDespacho'] != ''){
  $sedes = $_POST['itemsDespacho'];
  $_SESSION['sedes'] = $sedes;
}

if(isset($_POST['itemsDespachoVariacion']) && $_POST['itemsDespachoVariacion'] != ''){
  $sedes_variacion = $_POST['itemsDespachoVariacion'][0];
}
  // exit(var_dump($sedes_variacion));

if(isset($_SESSION['usuario']) && $_SESSION['usuario'] != ''){
  $usuario = $_SESSION['usuario'];
}

if(isset($_SESSION['login']) && $_SESSION['login'] != ''){
  $login = $_SESSION['login'];
}

if(isset($_SESSION['id_usuario']) && $_SESSION['id_usuario'] != ''){
  $idUsuario = $_SESSION['id_usuario'];
}

$sedes = $_POST['itemsDespacho'];

//echo "<br>Array Sedes<br>";
//var_dump($sedes);


$Link = new mysqli($Hostname, $Username, $Password, $Database);
if ($Link->connect_errno) {
  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$Link->set_charset("utf8");

// Se van a buscar el mes y el año a partir de la tabla de planilla semana
$consulta = " select ano, mes, semana from planilla_semanas where semana = '$semana' limit 1 ";
$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  while($row = $resultado->fetch_assoc()){
    $semanaMes = $row['mes'];
    $semanaAnno = $row['ano'];
  }
}
$semanaAnno = substr($semanaAnno, -2);
$annoMes = $semanaMes.$semanaAnno;


//1. Armar array con los componentes
//Primera columna, la que trae los diferentes alimentos de los menu.

$consulta = " select ps.MENU, ps.NOMDIAS, ft.Nombre, p.Cod_Grupo_Etario, ft.Codigo AS codigo_menu, ftd.codigo, ftd.Componente, ftd.Cantidad, ftd.UnidadMedida from planilla_semanas ps inner join productos$tablaAnno p on ps.menu = p.orden_ciclo inner join fichatecnica ft on p.Codigo = ft.Codigo inner JOIN fichatecnicadet ftd on ftd.IdFT = ft.Id ";

  $consulta = $consulta." where ps.SEMANA = '$semana'
  and ft.Nombre IS NOT NULL
  and p.Cod_Tipo_complemento = '$tipo' AND p.cod_variacion_menu = '$sedes_variacion'";

  $diasDespacho = '';
  for ($i=0; $i < count($dias) ; $i++) {
    if($i == 0){ $consulta = $consulta." AND ( "; }
      else{
       $consulta = $consulta." OR ";
       $diasDespacho = $diasDespacho.',';
      }
      $diasDespacho = $diasDespacho.$dias[$i];

     $consulta = $consulta." ps.DIA = ".$dias[$i]." ";

  }
  if(count($dias) > 0){
    $consulta = $consulta." ) ";
  }


  $consulta = $consulta." order by ftd.codigo asc ";

//echo "<br><br>$consulta<br><br>";


$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
if($resultado->num_rows >= 1){
  $aux = 0;
  while($row = $resultado->fetch_assoc()) {
    $items[] = $row;
    $menus[] = $row['codigo_menu'];
    $menusReg[] = $row['MENU'];
  }// Termina el while
}//Termina el if que valida que si existan resultados
$resultado->close();




$menusReg = array_unique($menusReg);
sort($menusReg);
$menusReg = implode(",",$menusReg);
//var_dump($menus);
//echo "<br><br><br>";
//var_dump($items);



// Debemos buscar el codigo del alimento sin preparar para obtener el
// codigo que es y las unidades que le afectan
$itemsIngredientes = array();
for ($i=0; $i < count($items); $i++) {
   $item = $items[$i];
   $codigo = $item['codigo'];
   $consulta = " select ft.Codigo AS codigo_preparado, ftd.codigo, ftd.Componente,p.nombreunidad2 presentacion, p.cantidadund1 cantidadPresentacion, p.cantidadund2 factor, p.cantidadund3, p.cantidadund4, p.cantidadund5, m.grupo_alim, ftd.Cantidad, ftd.UnidadMedida, ftd.PesoNeto, ftd.PesoBruto

   from fichatecnica ft

   inner join fichatecnicadet ftd on ft.id=ftd.idft
   inner join productos$tablaAnno p on ftd.codigo=p.codigo
   inner join menu_aportes_calynut m on ftd.codigo=m.cod_prod

   where ft.codigo = $codigo  and ftd.tipo = 'Alimento' ";

   if($tipoDespacho != 99){
    //echo "<br>Tipo de despacho = $tipoDespacho<br>";
    $consulta = $consulta." and p.tipodespacho = $tipoDespacho ";
  }




   //echo "<br><br>".$consulta."<br><br>";
   $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
   if($resultado->num_rows >= 1){
      $ingredientes = 1;
      while($row = $resultado->fetch_assoc()){
         $item['codigoPreparado'] = $codigo;
         $item['codigo'] = $row['codigo'];
         $item['Componente'] = $row['Componente'];
         $item['presentacion'] = $row['presentacion'];
         $item['cantidadPresentacion'] = $row['cantidadPresentacion'];
         $item['factor'] = $row['factor'];
         $item['cantidadund3'] = $row['cantidadund3'];
         $item['cantidadund4'] = $row['cantidadund4'];
         $item['cantidadund5'] = $row['cantidadund5'];
         //IMPORTANTE los calculos se hacen con peso bruto con ecepcion de los RI
         if($tipo == 'CAJMRI'){$item['cantidad'] = $row['Cantidad']; }
         else{$item['cantidad'] = $row['PesoBruto']; }
         $item['grupo_alim'] = $row['grupo_alim'];
         $item['unidadMedida'] = $row['UnidadMedida'];
         $itemsIngredientes[] = $item;
         $ingredientes++;
      }
   }
  else{
    if($tipoDespacho == 99){
      // Si no se encontraron ingredientes vamos a mostrar un mensaje.
      echo "No se encontraron alimentos sin preparar para el codigo: $codigo";
      //echo " <div class='error'>No se encontraron alimentos sin preparar para el codigo: $codigo <br> $consulta </div> ";
      $bandera++;
      break;
    }
  }
 }
$items = $itemsIngredientes;


//echo "<br>";
//var_dump($items);
//echo "<br>";









// Se arma un array con las coverturas de las sedes para cada uno de los grupos etarios
// y al final se creara un array con los totales de las sedes.

$total1 = 0;
$total2 = 0;
$total3 = 0;
$totalTotal = 0;
for ($i=0; $i < count($sedes) ; $i++) {

  $auxSede = $sedes[$i];

  $consulta = " select cod_sede, Etario1_$tipo, Etario2_$tipo, Etario3_$tipo from sedes_cobertura where semana = '$semana' and cod_sede = $auxSede and Ano = $annoActual ";

  //echo "<br>Consulta que busca las coberturas de las diferentes sedes.<br>";
  //echo "<br><br>".$consulta."<br><br>";

  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){


    while($row = $resultado->fetch_assoc()) {
      $sedeCobertura['cod_sede'] = $row['cod_sede'];
      $aux1 = "Etario1_$tipo";
      $sedeCobertura['grupo1'] = $row[$aux1];
      $aux2 = "Etario2_$tipo";
      $sedeCobertura['grupo2'] = $row[$aux2];
      $aux3 = "Etario3_$tipo";
      $sedeCobertura['grupo3'] = $row[$aux3];
      $sedeCobertura['total'] = $row[$aux1] + $row[$aux2] + $row[$aux3];
      $sedesCobertura[] = $sedeCobertura;
      $total1 = $total1 + $row[$aux1];
      $total2 = $total2 + $row[$aux2];
      $total3 = $total3 + $row[$aux3];
      $totalTotal = $totalTotal +  $sedeCobertura['total'];
    }

  }
}

$totalesSedeCobertura  = array(
    "grupo1" => $total1,
    "grupo2" => $total2,
    "grupo3" => $total3,
    "total"  => $totalTotal
);


$_SESSION['sedesCobertura'] = $sedesCobertura;
$_SESSION['totalesSedeCobertura'] = $totalesSedeCobertura;
//echo "<br><br>SEDES COBERTURA<br><br>";
//var_dump($sedesCobertura);
//echo "<br><br>TOTAL SEDES COBERTURA<br><br>";
//var_dump($totalesSedeCobertura);
//echo "<br><br><br>";

// Se va a revisar que no ewxistan despachos despachados (1) o pendiente (2) para el mismo complemento, semana sede
$bandera = 0;
for ($i=0; $i < count($sedesCobertura) ; $i++) {
  $auxSede = $sedesCobertura[$i];
  $auxSede = $auxSede['cod_sede'];
  $consulta = " SELECT de.*,s.nom_sede
  FROM despachos_enc$annoMes de
  inner join sedes$tablaAnno s on s.cod_sede = de.cod_Sede
  INNER JOIN tipo_despacho td on de.TipoDespacho = td.Id
  WHERE de.Semana = '$semana' AND de.cod_sede = '$auxSede' AND de.Tipo_Complem = '$tipo' AND (de.Estado = 1 OR de.Estado = 2) AND de.Tipo_Doc = 'DES' AND de.Num_Doc != '$despacho' ";

  if($tipoDespacho != 99){
      $consulta = $consulta." and TipoDespacho = $tipoDespacho ";
   }





  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  if($resultado->num_rows >= 1){
    $bandera++;
    $row = $resultado->fetch_assoc();
    $nomSede = $row['nom_sede'];
    echo " Ya existe despacho para la sede $nomSede en la semana $semana de tipo $tipo ";
    break;
  }
}

if($bandera == 0){
  // Vamos a crear el Array de complementosCantidades para hacer las
  // inserciones con las cantidades precisas
  //var_dump($items);

  $item = $items[0];
  $complementoCantidades = array(
    "nomDias" => $item["NOMDIAS"],
    "codigoPreparado" => $item["codigoPreparado"],
    "codigo" => $item["codigo"],
    "Componente" => $item["Componente"],
    "presentacion" => $item["presentacion"],
    "cantidadPresentacion" => $item["cantidadPresentacion"],
    "grupo_alim" => $item["grupo_alim"],
    "cantidad" => $item["cantidad"],
    "unidadMedida" => $item["unidadMedida"],
    "factor" => $item["factor"],

    "cantidadund3" => $item['cantidadund3'],
    "cantidadund4" => $item['cantidadund4'],
    "cantidadund5" => $item['cantidadund5'],

    "cantidadGrupo1"  => 0,
    "cantidadGrupo2"  => 0,
    "cantidadGrupo3"  => 0,
    "grupo1" => 0,
    "grupo2" => 0,
    "grupo3" => 0,
    "total"  => 0,
    "grupo1_d1" => 0,
    "grupo1_d2" => 0,
    "grupo1_d3" => 0,
    "grupo1_d4" => 0,
    "grupo1_d5" => 0,
    "grupo2_d1" => 0,
    "grupo2_d2" => 0,
    "grupo2_d3" => 0,
    "grupo2_d4" => 0,
    "grupo2_d5" => 0,
    "grupo3_d1" => 0,
    "grupo3_d2" => 0,
    "grupo3_d3" => 0,
    "grupo3_d4" => 0,
    "grupo3_d5" => 0
  );
  if($item["Cod_Grupo_Etario"] == 1){
    $complementoCantidades["cantidadGrupo1"]++;
    $complementoCantidades["grupo1"] = $item['cantidad'] * $item['cantidadPresentacion'];
    //$totalesSedeCobertura
    $complementoCantidades["grupo2"] = 0;
    $complementoCantidades["grupo3"] = 0;
    $complementoCantidades["total"] = $complementoCantidades["grupo1"];

    $auxDias = $item["NOMDIAS"];
    switch ($auxDias) {
      case "lunes":
        $complementoCantidades["grupo1_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "martes":
        $complementoCantidades["grupo1_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "miércoles":
        $complementoCantidades["grupo1_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "jueves":
        $complementoCantidades["grupo1_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "viernes":
        $complementoCantidades["grupo1_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
    }
  }
  else if($item["Cod_Grupo_Etario"] == 2){
    $complementoCantidades["cantidadGrupo2"]++;
    $complementoCantidades["grupo1"] = 0;
    $complementoCantidades["grupo2"] = $item['cantidad'] * $item['cantidadPresentacion'];
    $complementoCantidades["grupo3"] = 0;
    $complementoCantidades["total"] = $complementoCantidades["grupo2"];

    $auxDias = $item["NOMDIAS"];
    switch ($auxDias) {
      case "lunes":
        $complementoCantidades["grupo2_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "martes":
        $complementoCantidades["grupo2_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "miércoles":
        $complementoCantidades["grupo2_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "jueves":
        $complementoCantidades["grupo2_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "viernes":
        $complementoCantidades["grupo2_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
    }
  }
  else if($item["Cod_Grupo_Etario"] == 3){
    $complementoCantidades["cantidadGrupo3"]++;
    $complementoCantidades["grupo1"] = 0;
    $complementoCantidades["grupo2"] = 0;
    $complementoCantidades["grupo3"] = $item['cantidad'] * $item['cantidadPresentacion'];
    $complementoCantidades["total"] = $complementoCantidades["grupo3"];

    $auxDias = $item["NOMDIAS"];
    switch ($auxDias){
      case "lunes":
        $complementoCantidades["grupo3_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "martes":
        $complementoCantidades["grupo3_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "miércoles":
        $complementoCantidades["grupo3_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "jueves":
        $complementoCantidades["grupo3_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
      case "viernes":
        $complementoCantidades["grupo3_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
        break;
    }
  }















// Agregando el primer complemento al Array de complementosCantidades, a
  // continuación se agregaran los demas elementos buscando cuales se repiten
  // para poder determinar las cantidades de cada complemento.
  $complementosCantidades[] = $complementoCantidades;

  for ($i=1; $i <  count($items); $i++) {
    $item = $items[$i];
    $encontrado = 0;
    for ($j=0; $j < count($complementosCantidades) ; $j++) {
      $complemento = $complementosCantidades[$j];
      //echo "<br>Comparando: ".$item["codigo"]." y ".$complemento['codigo']."<br>";
      if($item["codigo"] == $complemento['codigo']){
          $encontrado++;

          if($item["Cod_Grupo_Etario"] == 1){
            $complemento["cantidadGrupo1"]++;
            $complemento["grupo1"] = $complemento["grupo1"] + $item['cantidad'] * $item['cantidadPresentacion'];
            $complemento["total"] = $complemento["grupo1"] + $complemento["grupo2"] + $complemento["grupo3"];

            $auxDias = $item["NOMDIAS"];
            switch ($auxDias) {
              case "lunes":
                $complemento["grupo1_d1"] = $complemento["grupo1_d1"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "martes":
                $complemento["grupo1_d2"] = $complemento["grupo1_d2"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "miércoles":
                $complemento["grupo1_d3"] = $complemento["grupo1_d3"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "jueves":
                $complemento["grupo1_d4"] = $complemento["grupo1_d4"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "viernes":
                $complemento["grupo1_d5"] = $complemento["grupo1_d5"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
            }
          }
          else if($item["Cod_Grupo_Etario"] == 2){

            $complemento["cantidadGrupo2"]++;
            $complemento["grupo2"] = $complemento["grupo2"] + $item['cantidad'] * $item['cantidadPresentacion'];
            $complemento["total"] = $complemento["grupo1"] + $complemento["grupo2"] + $complemento["grupo3"];

            $auxDias = $item["NOMDIAS"];
            switch ($auxDias) {
              case "lunes":
                $complemento["grupo2_d1"] = $complemento["grupo2_d1"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "martes":
                $complemento["grupo2_d2"] = $complemento["grupo2_d2"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "miércoles":
                $complemento["grupo2_d3"] = $complemento["grupo2_d3"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "jueves":
                $complemento["grupo2_d4"] = $complemento["grupo2_d4"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "viernes":
                $complemento["grupo2_d5"] = $complemento["grupo2_d5"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
            }

          }
          else if($item["Cod_Grupo_Etario"] == 3){
            $complemento["cantidadGrupo3"]++;
            $complemento["grupo3"] = $complemento["grupo3"] + $item['cantidad'] * $item['cantidadPresentacion'];
            $complemento["total"] = $complemento["grupo1"] + $complemento["grupo2"] + $complemento["grupo3"];

            $auxDias = $item["NOMDIAS"];
            switch ($auxDias) {
              case "lunes":
                $complemento["grupo3_d1"] = $complemento["grupo3_d1"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "martes":
                $complemento["grupo3_d2"] = $complemento["grupo3_d2"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "miércoles":
                $complemento["grupo3_d3"] = $complemento["grupo3_d3"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "jueves":
                $complemento["grupo3_d4"] = $complemento["grupo3_d4"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
              case "viernes":
                $complemento["grupo3_d5"] = $complemento["grupo3_d5"] + $item['cantidad'] * $item['cantidadPresentacion'];
                break;
            }

          }

          $complementosCantidades[$j] = $complemento;
          break;
      }
    }
    if($encontrado == 0){
      //echo "<br>".$item["codigo"].": No Encontrado<br>";
      $complementoNuevo["nomDias"] = $item["NOMDIAS"];
      $complementoNuevo["codigoPreparado"] = $item["codigoPreparado"];
      $complementoNuevo["codigo"] = $item["codigo"];
      $complementoNuevo["Componente"] =$item["Componente"];
      $complementoNuevo["presentacion"] = $item["presentacion"];
      $complementoNuevo["cantidadPresentacion"] = $item["cantidadPresentacion"];
      $complementoNuevo["grupo_alim"] = $item["grupo_alim"];
      $complementoNuevo["cantidad"] = $item["cantidad"];
      $complementoNuevo["unidadMedida"] = $item["unidadMedida"];
      $complementoNuevo["factor"] = $item["factor"];

      $complementoNuevo["cantidadund3"] = $item["cantidadund3"];
      $complementoNuevo["cantidadund4"] = $item["cantidadund4"];
      $complementoNuevo["cantidadund5"] = $item["cantidadund5"];


      $complementoNuevo["cantidadGrupo1"] = 0;
      $complementoNuevo["cantidadGrupo2"] = 0;
      $complementoNuevo["cantidadGrupo3"] = 0;

      $complementoNuevo["grupo1_d1"] = 0;
      $complementoNuevo["grupo1_d2"] = 0;
      $complementoNuevo["grupo1_d3"] = 0;
      $complementoNuevo["grupo1_d4"] = 0;
      $complementoNuevo["grupo1_d5"] = 0;

      $complementoNuevo["grupo2_d1"] = 0;
      $complementoNuevo["grupo2_d2"] = 0;
      $complementoNuevo["grupo2_d3"] = 0;
      $complementoNuevo["grupo2_d4"] = 0;
      $complementoNuevo["grupo2_d5"] = 0;

      $complementoNuevo["grupo3_d1"] = 0;
      $complementoNuevo["grupo3_d2"] = 0;
      $complementoNuevo["grupo3_d3"] = 0;
      $complementoNuevo["grupo3_d4"] = 0;
      $complementoNuevo["grupo3_d5"] = 0;

      if($item["Cod_Grupo_Etario"] == 1){
        $complementoNuevo["cantidadGrupo1"]=1;
        $complementoNuevo["grupo1"] = $item['cantidad'] * $item['cantidadPresentacion'];
        $complementoNuevo["grupo2"] = 0;
        $complementoNuevo["grupo3"] = 0;
        $complementoNuevo["total"] = $complementoNuevo["grupo1"];

        $auxDias = $item["NOMDIAS"];
        switch ($auxDias) {
          case "lunes":
            $complementoNuevo["grupo1_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "martes":
            $complementoNuevo["grupo1_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "miércoles":
            $complementoNuevo["grupo1_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "jueves":
            $complementoNuevo["grupo1_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "viernes":
            $complementoNuevo["grupo1_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
        }
      }
      else if($item["Cod_Grupo_Etario"] == 2){
        $complementoNuevo["cantidadGrupo2"]=1;
        $complementoNuevo["grupo1"] = 0;
        $complementoNuevo["grupo2"] = $item['cantidad'] * $item['cantidadPresentacion'];
        $complementoNuevo["grupo3"] = 0;
        $complementoNuevo["total"] = $complementoNuevo["grupo2"];

        $auxDias = $item["NOMDIAS"];
        switch ($auxDias) {
          case "lunes":
            $complementoNuevo["grupo2_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "martes":
            $complementoNuevo["grupo2_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "miércoles":
            $complementoNuevo["grupo2_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "jueves":
            $complementoNuevo["grupo2_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "viernes":
            $complementoNuevo["grupo2_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
        }

      }
      else if($item["Cod_Grupo_Etario"] == 3){
        $complementoNuevo["cantidadGrupo3"]=1;
        $complementoNuevo["grupo1"] = 0;
        $complementoNuevo["grupo2"] = 0;
        $complementoNuevo["grupo3"] = $item['cantidad'] * $item['cantidadPresentacion'];
        $complementoNuevo["total"] = $complementoNuevo["grupo3"];

        $auxDias = $item["NOMDIAS"];
        switch ($auxDias) {
          case "lunes":
            $complementoNuevo["grupo3_d1"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "martes":
            $complementoNuevo["grupo3_d2"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "miércoles":
            $complementoNuevo["grupo3_d3"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "jueves":
            $complementoNuevo["grupo3_d4"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
          case "viernes":
            $complementoNuevo["grupo3_d5"] = $item['cantidad'] * $item['cantidadPresentacion'];
            break;
        }

      }
      $complementosCantidades[] = $complementoNuevo;
    }
  }// Termina el if externo el de los items



  //echo "<br>Con los datos complementarios<br>";
  //var_dump($complementosCantidades);







  $_SESSION['complementosCantidades'] = $complementosCantidades;




  /* Insertando movimiento */

  // Para la edición no necesitamos armar el array de consecutivos dado
  // que tenemos un despacho especifico sobre el cual actuar.

  /* Insertando en productosmov$tablaAnno */
  $annoActual = $_SESSION['periodoActual'];

  date_default_timezone_set('America/Bogota');
  $fecha = date("Y-m-d H:i:s");

  //echo "<br><br>Insertando en productosmov<br><br>";

  for ($i=0; $i < count($sedes); $i++){
    $bodegaDestino = $sedes[$i];
    //$consecutivo = $consecutivos[$i];



    //$consulta = " insert into productosmov$annoActual (Documento, Numero, BodegaOrigen, BodegaDestino, Aprobado, NombreResponsable, LoginResponsable, FechaMYSQL, TipoTransporte, Placa, ResponsableRecibe ) values ('DES', $consecutivo, $bodegaOrigen, $bodegaDestino, 1, '$usuario', '$login', '$fecha', $tipoTransporte, '$placa', '$conductor') ";


    $consulta = " update productosmov$annoMes set




  Nombre = '$nombre', Nitcc = '$documento', Tipo = '$tipoDocumento', BodegaOrigen = $bodegaOrigen, BodegaDestino = $bodegaDestino, NombreResponsable = '$usuario', LoginResponsable = '$login', FechaMYSQL = '$fecha' , TipoTransporte = $tipoTransporte, Placa = '$placa', ResponsableRecibe = '$conductor'where Documento = 'DES' and Numero = $despacho ";

    $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  }


  /* Insertando en productosmovdet$tablaAnno */

  // se va borrar los movimientos detallados para registrar los nuevos.
  $consulta = " delete from productosmovdet$annoMes where Documento = 'DES' and Numero = $despacho ";
  $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

  //echo "<br><br>Insertando en productosmovdet<br><br>";





  // INICIA LA INSERCIÓN EN PRODUCTOS MOV_DET
  $consulta = " insert into productosmovdet$annoMes (Documento, Numero, Item, CodigoProducto, Descripcion, Cantidad, BodegaOrigen,BodegaDestino ,Umedida, CantUmedida, Factor, cantu2, cantu3, cantu4, cantu5, cantotalpresentacion ) values ";
  $banderaPrimero = 0;
  for ($i=0; $i < count($sedesCobertura) ; $i++) {
    $auxItem = 1;

    for ($j=0; $j < count($complementosCantidades) ; $j++){

      $auxConsulta = '';
      //if($j > 0){ $auxConsulta = $auxConsulta." , "; }
      $auxConsulta = $auxConsulta." ( ";
      $auxAlimento = $complementosCantidades[$j];
      $codigo = $auxAlimento['codigo'];
      $sede = $sedesCobertura[$i];
      $bodegaDestino = $sede['cod_sede'];
      $componente = $auxAlimento['Componente'];
      $cantidad = ($auxAlimento["grupo1"] * $sede["grupo1"])+($auxAlimento["grupo2"] * $sede["grupo2"])+($auxAlimento["grupo3"] * $sede["grupo3"]);
      $unidad = $auxAlimento['unidadMedida'];
      $factor = $auxAlimento['factor'];
      $presentacion = $auxAlimento['presentacion'];
      include 'fn_despacho_generar_presentaciones.php';
      $auxConsulta = $auxConsulta." 'DES',$despacho,$auxItem,'$codigo','$componente',$cantidad,$bodegaOrigen,$bodegaDestino,'$presentacion',$cantidad,$factor, $necesario2, $necesario3, $necesario4, $necesario5, $tomadoTotal ";
      $auxItem++;
      $auxConsulta = $auxConsulta." ) ";

      if($cantidad > 0){
        //if($i > 0 && $j == 0){ $consulta = $consulta." , "; }
        if($banderaPrimero == 0){
          $banderaPrimero++;
        }else{
          $consulta = $consulta." , ";
        }
        $consulta = $consulta.$auxConsulta;
      }


    }
  }
  // TERMINA LA INSERCIÓN EN PRODUCTOS MOV_DET











  //echo "<br><br>".$consulta."<br><br>";
  $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));








  // Insertando en despachos_enc
  //echo "<br><br>Insertando en despachos_enc<br><br>";
  //$consulta = " insert into despachos_enc (Tipo_Doc, Num_Doc, FechaHora_Elab, Id_usuario, cod_Sede, Tipo_Complem, Semana, Cobertura, estado, concepto, Dias) values ";




  //echo "<br>".count($sedesCobertura);



  for ($i=0; $i < count($sedesCobertura); $i++) {
    if($i > 0){$consulta = $consulta." , "; }

    //echo "<br>".$i;



    //$consulta = $consulta." ( ";
    $sede = $sedesCobertura[$i];
    $grupo1 = $sede['grupo1'];
    $grupo2 = $sede['grupo2'];
    $grupo3 = $sede['grupo3'];
    //$consecutivo = $consecutivos[$i];




  // Se va a actualizar el array de total sedes cobertura, dependiendo de si tenemos menus
  // para esos grupos.

  $auxGrupo1 = 0;
  $auxGrupo2 = 0;
  $auxGrupo3 = 0;

  for ($j=0; $j < count($complementosCantidades) ; $j++) {
    $complemento = $complementosCantidades[$j];
    if($complemento['grupo1']>0){$auxGrupo1++; }
    if($complemento['grupo2']>0){$auxGrupo2++; }
    if($complemento['grupo3']>0){$auxGrupo3++; }
  }
  if($auxGrupo1 == 0){
    $sede['grupo1']=0;
    $sede['total'] = $sede['grupo1'] + $sede['grupo2'] + $sede['grupo3'];
  }
  if($auxGrupo2 == 0){
    $sede['grupo2']=0;
    $sede['total'] = $sede['grupo1'] + $sede['grupo2'] + $sede['grupo3'];
  }
  if($auxGrupo3 == 0){
    $sede['grupo3']=0;
    $sede['total'] = $sede['grupo1'] + $sede['grupo2'] + $sede['grupo3'];
  }

  $cobertura = $sede['total'];







    $sede = $sede['cod_sede'];

  /*
    $consulta = $consulta." 'DES',$consecutivo,'$fecha',$idUsuario,$sede,'$tipo','$semana',$cobertura,2,'', '$diasDespacho' ";
    $consulta = $consulta." ) ";

  */


    $consulta = "UPDATE despachos_enc$annoMes SET FechaHora_Elab = '$fecha', Id_usuario = $idUsuario, cod_Sede = $sede, Tipo_Complem = '$tipo', Semana = '$semana', Cobertura = $cobertura, Dias = '$diasDespacho', Menus = '$menusReg', Cobertura_G1 = '$grupo1', Cobertura_G2 = '$grupo2', Cobertura_G3 = '$grupo3' WHERE Tipo_Doc = 'DES' AND Num_Doc = $despacho";
  }

  $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));


  // Insertando en despachos_det

  // Se van a borrar los despacho detallados para poder actualizar los registros.
  $consulta = " delete from despachos_det$annoMes where Tipo_Doc = 'DES' and Num_Doc = $despacho ";
  $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));






  // INICIA CONSULTA DE INSERCIÓN EN DESPACHOS DET
  $consulta = " insert into despachos_det$annoMes (Tipo_Doc, Num_Doc, cod_Alimento, id_GrupoEtario, Cantidad, D1, D2, D3, D4, D5) values ";



  // banderaPrimero: es una variable bandera que nos controla si el primer elelemnto ya fue agregado y a partir de hay colocar las comas que separan los valores de cada inserción.
  $banderaPrimero = 0;

  for ($i=0; $i < count($sedesCobertura) ; $i++) {
    $sede = $sedesCobertura[$i];





    for ($j=0; $j < count($complementosCantidades) ; $j++){

      // Se toman los datos del alimento en una cadena auxiliar para despues
      // validar si la cantidad es mayor a cero y agregar a la cadena que va
      // a hacer la insersión
      $auxAlimento = $complementosCantidades[$j];
      $gruposRegistrar = 0;
      //include 'fn_despacho_generar_presentaciones.php';













      if($j > 0 &&( ($auxAlimento['grupo1']>0 && $sede["grupo1"]>0) || ($auxAlimento['grupo2']>0 && $sede["grupo2"]>0) || ($auxAlimento['grupo3']>0  && $sede["grupo3"]>0) ) ){
      //  $consulta = $consulta." , ";
      }

      if($auxAlimento['grupo1']>0 && $sede["grupo1"]>0){
        if($banderaPrimero == 0){
          $banderaPrimero++;
        }else{
          $consulta = $consulta." , ";
        }




        $gruposRegistrar++;
        $idGrupoEtario = 1;
        $consulta = $consulta." ( ";
        $codigo = $auxAlimento['codigo'];
        $componente = $auxAlimento['Componente'];
        $cantidad = $auxAlimento["grupo1"] * $sede["grupo1"];
        $unidad = $auxAlimento['unidadMedida'];


        $d1 = $auxAlimento["grupo1_d1"] * $sede["grupo1"];
        $d2 = $auxAlimento["grupo1_d2"] * $sede["grupo1"];
        $d3 = $auxAlimento["grupo1_d3"] * $sede["grupo1"];
        $d4 = $auxAlimento["grupo1_d4"] * $sede["grupo1"];
        $d5 = $auxAlimento["grupo1_d5"] * $sede["grupo1"];

        $consulta = $consulta." 'DES',$despacho, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5 ";
        $consulta = $consulta." ) ";

        }

      if($auxAlimento['grupo2']>0 && $sede["grupo2"]>0){


        if($banderaPrimero == 0){
          $banderaPrimero++;
        }else{
          $consulta = $consulta." , ";
        }


        $gruposRegistrar++;
        $idGrupoEtario = 2;
        $consulta = $consulta." ( ";
        $codigo = $auxAlimento['codigo'];
        $componente = $auxAlimento['Componente'];
        //$cantidad = $auxAlimento['grupo2']*$auxAlimento['cantidad'];
        $cantidad = $auxAlimento["grupo2"] * $sede["grupo2"];
        $unidad = $auxAlimento['unidadMedida'];


        $d1 = $auxAlimento["grupo2_d1"] * $sede["grupo2"];
        $d2 = $auxAlimento["grupo2_d2"] * $sede["grupo2"];
        $d3 = $auxAlimento["grupo2_d3"] * $sede["grupo2"];
        $d4 = $auxAlimento["grupo2_d4"] * $sede["grupo2"];
        $d5 = $auxAlimento["grupo2_d5"] * $sede["grupo2"];


        $consulta = $consulta." 'DES',$despacho, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5 ";
        $consulta = $consulta." ) ";

      }
      if($auxAlimento['grupo3']>0  && $sede["grupo3"]>0){
        //$consulta = $consulta."<br><br>j = $j y i = $i<br><br>";



        if($banderaPrimero == 0){
          $banderaPrimero++;
        }else{
          $consulta = $consulta." , ";
        }





        $gruposRegistrar++;
        $idGrupoEtario = 3;
        $consulta = $consulta." ( ";
        $codigo = $auxAlimento['codigo'];

        $componente = $auxAlimento['Componente'];
        //$cantidad = $auxAlimento['grupo3']*$auxAlimento['cantidad'];
        $cantidad = $auxAlimento["grupo3"] * $sede["grupo3"];
        $unidad = $auxAlimento['unidadMedida'];
        //$consecutivo = $consecutivos[$i];

        $d1 = $auxAlimento["grupo3_d1"] * $sede["grupo3"];
        $d2 = $auxAlimento["grupo3_d2"] * $sede["grupo3"];
        $d3 = $auxAlimento["grupo3_d3"] * $sede["grupo3"];
        $d4 = $auxAlimento["grupo3_d4"] * $sede["grupo3"];
        $d5 = $auxAlimento["grupo3_d5"] * $sede["grupo3"];


        $consulta = $consulta." 'DES',$despacho, '$codigo', $idGrupoEtario, $cantidad, $d1, $d2, $d3, $d4, $d5 ";
        $consulta = $consulta." ) ";
      }
    }// Termina el for de los alimentos
  }
  // TERMINA CONSULTA DE INSERCIÓN EN DESPACHOS DET


  $resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  $Link->close();



  echo "1";
}// Termina el if de bandera igual a cero
