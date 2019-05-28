<option value="">seleccione</option>
<?php
	require_once '../../../config.php';
	require_once '../../../db/conexion.php';

	$sede = (isset($_POST['sede']) && ! empty($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : '';
	$semana = (isset($_POST['semana']) && ! empty($_POST['semana'])) ? $Link->real_escape_string($_POST["semana"]) : '';
	$institucion = (isset($_POST['institucion']) && ! empty($_POST['institucion'])) ? $Link->real_escape_string($_POST['institucion']) : '';

	//Revisar si la tabla existencia
	$consulta_focalizacion = "SHOW TABLES LIKE 'focalizacion$semana'";
	$respuesta_consulta_focalizado = $Link->query($consulta_focalizacion) or die('Error al consultar la focalización: '. $Link->error);
	if(! empty($respuesta_consulta_focalizado->num_rows))
	{
		$parametro_sedes = (! empty($sede)) ? "AND cod_sede = '$sede'" : "";
		$parametro_institucion = (! empty($institucion)) ? "AND cod_inst = '$institucion'" : "";
		$consulta_complementos = "SELECT distinct f.Tipo_complemento AS tipo_complemento FROM focalizacion$semana f WHERE 1 = 1 $parametro_institucion $parametro_sedes ORDER BY Tipo_complemento ASC";
		$respuesta_consulta_complementos = $Link->query($consulta_complementos) or die('Error al consultar los complementos: '. $Link->error);
		if(! empty($respuesta_consulta_complementos->num_rows))
		{
			while($complementos = $respuesta_consulta_complementos->fetch_object())
			{
				$tipo_complemento = $complementos->tipo_complemento;
				echo '<option value="'. $tipo_complemento .'">'. $tipo_complemento .'</option>';
			}
		}
	}
