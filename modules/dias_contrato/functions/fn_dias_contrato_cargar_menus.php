<?php
	require_once '../../../db/conexion.php';
	require_once '../../../config.php';
?>
	<option value="">Seleccione uno</option>
<?php
	// Variables post
	$ciclo = (isset($_POST['ciclo']) && $_POST['ciclo'] != '') ? mysqli_real_escape_string($Link, $_POST['ciclo']) : '';

	$pagina = (($ciclo - 1) * 5);
	$conMenu = "SELECT distinct Orden_Ciclo AS menu FROM productos". $_SESSION["periodoActual"] ." WHERE codigo LIKE '01%' AND nivel = '3' ORDER BY menu ASC LIMIT $pagina, 5;";
	$resMenu = $Link->query($conMenu);
	if($resMenu->num_rows > 0)
	{
		while ($regMenu = $resMenu->fetch_assoc())
		{
?>
	<option value="<?php echo $regMenu['menu']; ?>"><?php echo $regMenu['menu']; ?></option>
<?php
		}
	}
?>