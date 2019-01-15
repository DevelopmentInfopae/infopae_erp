<?php 

require_once '../../../config.php';
require_once '../../../db/conexion.php';

if (isset($_POST['idvalref'])) {
	$idvalref = $_POST['idvalref'];
} else {
	$idvalref = "";
}

$consulta = "SELECT G.DESCRIPCION AS nomGETA, T.CODIGO AS cod_comp, M.* FROM menu_valref_nutrientes AS M
	INNER JOIN grupo_etario AS G ON G.ID = M.Cod_Grupo_Etario
	INNER JOIN tipo_complemento AS T ON T.CODIGO = M.Cod_tipo_complemento
 WHERE M.id = '".$idvalref."'";
$resultado = $Link->query($consulta);
if ($resultado->num_rows > 0) {
	if ($datos = $resultado->fetch_assoc()) {
		$nomGETA = $datos['nomGETA'];
		$complemento = $datos['cod_comp'];
	}
}


$borrar = "DELETE FROM menu_valref_nutrientes WHERE id = '".$idvalref."'";
if ($Link->query($borrar)===true) {
	$sqlBitacora = "INSERT INTO bitacora (id, fecha, usuario, tipo_accion, observacion) VALUES ('', '".date('Y-m-d H:i:s')."', '".$_SESSION['idUsuario']."', '58', 'Actualizó los datos de los aportes calóricos y nutricionales para el complemento <strong>".$complemento."</strong> y grupo Etario <strong>".$nomGETA."</strong> ')";
	$Link->query($sqlBitacora);
	echo "1";
} else {
	echo " Error : ".$borrar;
}