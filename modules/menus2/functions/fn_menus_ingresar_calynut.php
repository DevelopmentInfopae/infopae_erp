<?php 

require_once '../../../db/conexion.php';
require_once '../../../config.php';

if (isset($_POST['kcalxg'])) {
	$kcalxg = $_POST['kcalxg'];
} else {
	$kcalxg = "";
}

if (isset($_POST['kcaldgrasa'])) {
	$kcaldgrasa = $_POST['kcaldgrasa'];
} else {
	$kcaldgrasa = "";
}

if (isset($_POST['Grasa_Sat'])) {
	$Grasa_Sat = $_POST['Grasa_Sat'];
} else {
	$Grasa_Sat = "";
}

if (isset($_POST['Grasa_poliins'])) {
	$Grasa_poliins = $_POST['Grasa_poliins'];
} else {
	$Grasa_poliins = "";
}

if (isset($_POST['Grasa_Monoins'])) {
	$Grasa_Monoins = $_POST['Grasa_Monoins'];
} else {
	$Grasa_Monoins = "";
}

if (isset($_POST['Grasa_Trans'])) {
	$Grasa_Trans = $_POST['Grasa_Trans'];
} else {
	$Grasa_Trans = "";
}

if (isset($_POST['Fibra_dietaria'])) {
	$Fibra_dietaria = $_POST['Fibra_dietaria'];
} else {
	$Fibra_dietaria = "";
}

if (isset($_POST['Azucares'])) {
	$Azucares = $_POST['Azucares'];
} else {
	$Azucares = "";
}

if (isset($_POST['Proteinas'])) {
	$Proteinas = $_POST['Proteinas'];
} else {
	$Proteinas = "";
}

if (isset($_POST['Colesterol'])) {
	$Colesterol = $_POST['Colesterol'];
} else {
	$Colesterol = "";
}

if (isset($_POST['Sodio'])) {
	$Sodio = $_POST['Sodio'];
} else {
	$Sodio = "";
}

if (isset($_POST['Zinc'])) {
	$Zinc = $_POST['Zinc'];
} else {
	$Zinc = "";
}

if (isset($_POST['Calcio'])) {
	$Calcio = $_POST['Calcio'];
} else {
	$Calcio = "";
}

if (isset($_POST['Hierro'])) {
	$Hierro = $_POST['Hierro'];
} else {
	$Hierro = "";
}

if (isset($_POST['Vit_A'])) {
	$Vit_A = $_POST['Vit_A'];
} else {
	$Vit_A = "";
}

if (isset($_POST['Vit_C'])) {
	$Vit_C = $_POST['Vit_C'];
} else {
	$Vit_C = "";
}

if (isset($_POST['Vit_B1'])) {
	$Vit_B1 = $_POST['Vit_B1'];
} else {
	$Vit_B1 = "";
}

if (isset($_POST['Vit_B2'])) {
	$Vit_B2 = $_POST['Vit_B2'];
} else {
	$Vit_B2 = "";
}

if (isset($_POST['Vit_B3'])) {
	$Vit_B3 = $_POST['Vit_B3'];
} else {
	$Vit_B3 = "";
}

if (isset($_POST['Acido_Fol'])) {
	$Acido_Fol = $_POST['Acido_Fol'];
} else {
	$Acido_Fol = "";
}

if (isset($_POST['Referencia'])) {
	$Referencia = $_POST['Referencia'];
} else {
	$Referencia = "";
}

if (isset($_POST['cod_Referencia'])) {
	$cod_Referencia = $_POST['cod_Referencia'];
} else {
	$cod_Referencia = "";
}

if (isset($_POST['idProductoCalyNut'])) {
	$idProducto = $_POST['idProductoCalyNut'];
} else {
	$idProducto = "";
}



$consultaDescripcion = "select Descripcion from productos".$_SESSION['periodoActual']." where Codigo = ".$idProducto;
$resultadoDescripcion = $Link->query($consultaDescripcion) or die('Unable to execute query. '. mysqli_error($Link).$consultaDescripcion);
if ($resultadoDescripcion->num_rows > 0) {
  while ($row = $resultadoDescripcion->fetch_assoc()) {
    $Descripcion = $row['Descripcion'];
  }
} else {
	$Descripcion = "Error :".$consultaDescripcion;
}

$consultaGrupoAlimento = "select Descripcion from productos".$_SESSION['periodoActual']." where nivel = 2 AND Codigo = ".substr($idProducto, 0, 4);
$resultadoGrupoAlimento = $Link->query($consultaGrupoAlimento) or die('Unable to execute query. '. mysqli_error($Link).$consultaGrupoAlimento);
if ($resultadoGrupoAlimento->num_rows > 0) {
  while ($row = $resultadoGrupoAlimento->fetch_assoc()) {
    $grupo_alim = $row['Descripcion'];
  }
} else {
	$grupo_alim = "Error :".$consultaGrupoAlimento;
}

$sql = "insert into menu_aportes_calynut (id, cod_prod, nom_prod, grupo_alim, kcalxg, kcaldgrasa, Grasa_Sat, Grasa_poliins, Grasa_Monoins, Grasa_Trans, Fibra_dietaria, Azucares, Proteinas, Colesterol, Sodio, Zinc, Calcio, Hierro, Vit_A, Vit_C, Vit_B1, Vit_B2, Vit_B3, Acido_Fol, Referencia, cod_Referencia) values (NULL, '".$idProducto."', '".$Descripcion."', '".$grupo_alim."', '".$kcalxg."', '".$kcaldgrasa."', '".$Grasa_Sat."', '".$Grasa_poliins."', '".$Grasa_Monoins."', '".$Grasa_Trans."', '".$Fibra_dietaria."', '".$Azucares."', '".$Proteinas."', '".$Colesterol."', '".$Sodio."', '".$Zinc."', '".$Calcio."', '".$Hierro."', '".$Vit_A."', '".$Vit_C."', '".$Vit_B1."', '".$Vit_B2."', '".$Vit_B3."', '".$Acido_Fol."', '".$Referencia."', '".$cod_Referencia."')";

if ($Link->query($sql) === true) {
	echo "1";
} else {
	echo "Error : ".$sql;
}

 ?>