<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

if (isset($_POST['complemento'])) {
	$complemento = $_POST['complemento'];
} else {
	$complemento = "";
}

if (isset($_POST['grupoEtario'])) {
	$grupoEtario = $_POST['grupoEtario'];
} else {
	$grupoEtario = "";
}

if (isset($_POST['nomGETA'])) {
	$nomGETA = $_POST['nomGETA'];
} else {
	$nomGETA = "";
}

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


$insertar = "INSERT INTO menu_valref_nutrientes (id, kcalxg, kcaldgrasa, Grasa_Sat, Grasa_poliins, Grasa_Monoins, Grasa_Trans, Fibra_dietaria, Azucares, Proteinas, Colesterol, Sodio, Zinc, Calcio, Hierro, Vit_A, Vit_C, Vit_B1, Vit_B2, Vit_B3, Acido_Fol, Cod_Grupo_Etario, Cod_tipo_complemento) VALUES ('', '".$kcalxg."', '".$kcaldgrasa."', '".$Grasa_Sat."', '".$Grasa_poliins."', '".$Grasa_Monoins."', '".$Grasa_Trans."', '".$Fibra_dietaria."', '".$Azucares."', '".$Proteinas."', '".$Colesterol."', '".$Sodio."', '".$Zinc."', '".$Calcio."', '".$Hierro."', '".$Vit_A."', '".$Vit_C."', '".$Vit_B1."', '".$Vit_B2."', '".$Vit_B3."', '".$Acido_Fol."', '".$grupoEtario."', '".$complemento."')";

if ($Link->query($insertar)===true) {
	$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '56', 'Registró aportes calóricos y nutricionales para el complemento <strong>".$complemento."</strong> y grupo Etario <strong>".$nomGETA."</strong> ')";
	$Link->query($sqlBitacora);
	echo "1";
} else {
	echo "Error : ".$insertar;
}