<?php
  	require_once '../../../db/conexion.php';
  	require_once '../../../config.php';
    $descripcion = $_POST['descripcion'];
    $tipoProducto = $_POST['tipoProducto'];
    $subTipoProducto = $_POST['subTipoProducto'];
    $grupoEtario = $_POST['grupoEtario'];

	$condicionSubTipo = (isset($subTipoProducto) && $subTipoProducto != '') ? " AND Codigo LIKE '$subTipoProducto%' " : "";

    if ($tipoProducto == "02") {
      	$consulta = " SELECT * FROM productos".$_SESSION['periodoActual']." 
                        WHERE nivel = '3' $condicionSubTipo
						AND Descripcion LIKE '".$descripcion."%' AND Cod_Grupo_Etario = '".$grupoEtario."' AND inactivo = '0'";
    } else {
      $consulta = " SELECT * FROM productos".$_SESSION['periodoActual']." 
	  					WHERE nivel = '3' $condicionSubTipo
						AND Descripcion = '".$descripcion."' AND inactivo = '0'";
    }
  
	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));
  	if($resultado->num_rows > 0){
    	echo "1";
  	} else {
    	echo "0";
  	}