<?php
require_once '../../../db/conexion.php';
require_once '../../../config.php';

$periodoActual = $_SESSION['periodoActual'];

$fecha = date('Y-m-d H:i:s');
$carpeta = 'upload/novedades/menu/';
$carpetaFisica = '../../../upload/novedades/menu/';
$nuevoId = 0;

//var_dump($_POST);
//var_dump($_FILES);
//var_dump($_SESSION);

$usuario = $_SESSION['id_usuario'];
$mes = '';
$semana = '';
$tipoComplemento = '';
$grupoEtario = '';
$fechaVencimiento = '';
$observaciones = '';
$rutaArchivo = '';


if(isset($_POST['mes']) && $_POST['mes'] != ''){
	$mes = mysqli_real_escape_string($Link, $_POST['mes']);
}
if(isset($_POST['semana']) && $_POST['semana'] != ''){
  $semana = mysqli_real_escape_string($Link, $_POST['semana']);
}
if(isset($_POST['tipoComplemento']) && $_POST['tipoComplemento'] != ''){
  $tipoComplemento = mysqli_real_escape_string($Link, $_POST['tipoComplemento']);
}
if(isset($_POST['grupoEtario']) && $_POST['grupoEtario'] != ''){
  $grupoEtario = mysqli_real_escape_string($Link, $_POST['grupoEtario']);
}
if(isset($_POST['fechaVencimiento']) && $_POST['fechaVencimiento'] != ''){
  $fechaVencimiento = mysqli_real_escape_string($Link, $_POST['fechaVencimiento']);
  $fechaVencimiento = str_replace('/', '-', $fechaVencimiento);
  $fechaVencimiento = date("Y-m-d", strtotime($fechaVencimiento));
}
if(isset($_POST['observaciones']) && $_POST['observaciones'] != ''){
  $observaciones = mysqli_real_escape_string($Link, $_POST['observaciones']);
}


// Respuesta en caso de error
$resultadoAJAX = array(
	"estado" => 2,
	"message" => "Error al hacer el registro.",
);


// Registro del encabezdo de la novedad
$query = " insert into novedades_menu (mes, semana, tipo_complem, cod_grupo_etario,  observaciones, url_archivo, fecha_registro, estado, fecha_vencimiento, id_usuario, tipo_intercambio) values (\"$mes\", \"$semana\", \"$tipoComplemento\", \"$grupoEtario\", \"$observaciones\", \"\", \"$fecha\", \"1\", \"$fechaVencimiento\", \"$usuario\", \"3\") ";

//echo "<br><br>$query<br><br>";
$result = $Link->query($query) or die ('Insertando novedad'. mysqli_error($Link)); 
$nuevoId = $Link->insert_id;

/* Tratamiento del archivo */
if (!file_exists($carpetaFisica)) {
    mkdir($carpetaFisica, 0777);
}
if (isset($_FILES["foto"])) {
		$file = $_FILES["foto"];
		$tipo = $file["type"];
		$size = $file["size"];
		$nombre = $file["name"];
		$ruta_provisional = $file["tmp_name"];
		$ext = pathinfo($nombre, PATHINFO_EXTENSION);
		if($nuevoId > 0){
			$nombre = $nuevoId.'.'.$ext;
			$src = $carpetaFisica.$nombre;
			$srcw = $carpeta.$nombre;
			if(move_uploaded_file($ruta_provisional, $src)){
				$query = " update novedades_menu set url_archivo = \"$srcw\" where id = \"$nuevoId\" ";
				// echo "<br><br> $query <br><br>";
				$Link->query($query) or die ('Error actualizando la URL del archivo de novedad de menú'. mysqli_error($Link));
			}
		}	
	
}
/* Termina tratamiento del archivo */

// Para poder hacer el registro del intercambio de los menus debo saber el orden_ciclo de los menus que escogi
$menus = $_POST['menu'];
//var_dump($menus);
$menusConsulta = array();
foreach ($menus as $menu) {
	$menusConsulta[] = $menu['codigo'];
}
unset($menus);
//var_dump($menusConsulta);
$menusConsulta = implode( ", ", $menusConsulta );

// Buscando los registros originales
$query = " SELECT 0 AS tipo, \"$nuevoId\" as novedad, p.Codigo, p.Orden_Ciclo FROM planilla_semanas ps LEFT JOIN productos$periodoActual p ON ps.MENU = p.Orden_Ciclo WHERE ps.MES = \"$mes\" AND ps.SEMANA = \"$semana\" AND p.Cod_Tipo_complemento = \"$tipoComplemento\" AND p.Cod_Grupo_Etario = \"$grupoEtario\" AND p.Codigo LIKE \"01%\" AND p.Nivel = 3 ";
$query .= " union SELECT 0 AS tipo, \"$nuevoId\" as novedad, p2.Codigo, p2.Orden_Ciclo FROM productos$periodoActual p2 where p2.Codigo IN ($menusConsulta)  ";
//echo $query;
$result = $Link->query($query) or die ('Error al cargar los registros originales.'. mysqli_error($Link));
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()){
		$menusOriginales[ $row["Orden_Ciclo"]] = $row;
	}
}
$menusSeleccionados =  $_POST['menu'];

$menusConCambios = array();

//var_dump( $menusOriginales );
//var_dump( $menusSeleccionados );
foreach ($menusOriginales as $menuOriginal) {
	$auxCodigo = $menuOriginal['Codigo'];
	if(isset($menusSeleccionados[$auxCodigo])){
		$menuSeleccionado = $menusSeleccionados[$auxCodigo];
		if($menuOriginal['Orden_Ciclo'] == $menuSeleccionado['ordenCiclo']){
			$menuOriginal['Orden_Ciclo_Destino'] = $menuOriginal['Orden_Ciclo'];
			$menusConCambios[$menuOriginal['Orden_Ciclo']] = $menuOriginal;
		}
		else{
			$menuOriginal['Orden_Ciclo_Destino'] =	$menuSeleccionado['ordenCiclo'];
			$menuDeIntercambio = $menusOriginales[$menuSeleccionado['ordenCiclo']];
			$menuDeIntercambio['Orden_Ciclo_Destino'] = $menuOriginal['Orden_Ciclo'];
			$menusConCambios[$menuOriginal['Orden_Ciclo']] = $menuOriginal;
			$menusConCambios[$menuDeIntercambio['Orden_Ciclo']] = $menuDeIntercambio;
		}
	}
}
//var_dump( $menusConCambios );

// Inserción del detalle de la novedad
$aux = 0;
$queryCambioEnMenu = "";
$query = " INSERT INTO novedades_menudet (tipo, id_novedad, cod_producto, orden_ciclo) values ";
foreach ($menusConCambios as $menu) {
	$codigo = mysqli_real_escape_string($Link, $menu['Codigo']);
	$ordenCiclo = mysqli_real_escape_string($Link, $menu['Orden_Ciclo']);
	$ordenCicloDestino = mysqli_real_escape_string($Link, $menu['Orden_Ciclo_Destino']);
	if($aux > 0){$query .= " , "; }
	$query .= " (\"0\", \"$nuevoId\", \"$codigo\", \"$ordenCiclo\") , ";
	$query .= " (\"1\", \"$nuevoId\", \"$codigo\", \"$ordenCicloDestino\") ";
	$aux++;
	$queryCambioEnMenu .= " update productos$periodoActual set Orden_Ciclo = \"$ordenCicloDestino\" where Codigo = \"$codigo\"; ";
}
//echo $query;
$Link->query($query) or die ('Error inserción de registros originales en el detalle de la novedad'. mysqli_error($Link));

$result = $Link->multi_query($queryCambioEnMenu) or die ('Update error'. mysqli_error($Link));
if($result){
	$resultadoAJAX = array(
		"estado" => 1,
		"message" => "El registro se ha realizado con éxito.",
	  );
} 
echo json_encode($resultadoAJAX);