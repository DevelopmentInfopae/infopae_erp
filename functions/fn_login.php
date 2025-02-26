<?php
include '../config.php';
include("../db/conexion.php");

$user = mysqli_real_escape_string($Link, $_POST['user']);
$passencriptado = mysqli_real_escape_string($Link, $_POST['pass']);

if (isset($_SESSION["token_seguridad"]) && $token_seguridad != $_SESSION["token_seguridad"]) {
    echo "-2";
} else {
    $vlsql ="SELECT * FROM usuarios WHERE email='$user' AND clave='$passencriptado' AND Estado = 1 "; 
    if ($resultado = $Link->query($vlsql)) {
      	if ($resultado->num_rows >= 1) {
        	$row1 = $resultado->fetch_assoc();

        	$_SESSION["url"] = $baseUrl;
        	$_SESSION["autentificado"]="SI";
        	$_SESSION['foto']= $row1["foto"];
        	$_SESSION['login']= $row1["email"];
        	$_SESSION['id_usuario']= $row1["id"];
        	$_SESSION['idUsuario'] = $row1["id"];
        	$_SESSION['usuario']= $row1["nombre"];
        	$_SESSION['num_doc']= $row1["num_doc"];
        	$_SESSION['perfil']= $row1["id_perfil"];
        	$_SESSION['tipoUsuario']= $row1["Tipo_Usuario"];
        	$_SESSION["token_seguridad"] = $token_seguridad;

        	// Haciendo registro en la bitacora
        	$logIdUsr = $_SESSION['id_usuario'];
        	date_default_timezone_set('America/Bogota');
        	$fecha = date('Y-m-d H:i:s');
        	$consulta = " insert into bitacora (fecha, usuario, tipo_accion, observacion ) values ('$fecha','$logIdUsr',1,'Inicio Sesion') ";
        	$Link->query($consulta);
        	// Termina hacer registro en la bitacora

        	//Cargando parametros de la aplicación
        	$consulta = "SELECT * FROM parametros;";
        	$resultado = $Link->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

        	if($resultado->num_rows >= 1){
          		$row = $resultado->fetch_assoc();
        	}
        	$_SESSION['p_Operador'] = $row['Operador'];
        	$_SESSION['p_NumContrato'] = $row['NumContrato'];
        	$_SESSION['p_ano'] = $row['ano'];
        	$_SESSION['p_CodDepartamento'] = $row['CodDepartamento'];
        	$_SESSION['p_Nombre ETC'] = $row['NombreETC'];
        	$_SESSION['p_Logo ETC'] = $row['LogoETC'];
        	$_SESSION['p_Departamento'] = $row['Departamento'];
        	$_SESSION['p_Contrato'] = $row['NumContrato'];
        	$_SESSION["p_Municipio"] = $row["CodMunicipio"];
        	$_SESSION["p_nombre_representante_legal"] = $row["nombre_representante_legal"];
        	$_SESSION["p_documento_representante_legal"] = $row["documento_representante_legal"];
        	$_SESSION["p_telefono"] = $row["telefono"];
        	$_SESSION["p_inventory"] = $row["inventory"];
        	$_SESSION["p_redondeo_compra"] = $row["redondeo_compra"];
        	$_SESSION["p_tipo_redondeo_compra"] = $row["tipo_redondeo_compra"];
        	$_SESSION["p_rango_redondeo_compra"] = $row["rango_redondeo_compra"];
        	$_SESSION["p_redondeo_remision"] = $row["redondeo_remision"];
        	$_SESSION["p_tipo_redondeo_remision"] = $row["tipo_redondeo_remision"];
        	$_SESSION["p_rango_redondeo_remision"] = $row["rango_redondeo_remision"];
        	$_SESSION["p_gruposCalculos"] = $row["gruposCalculos"];
        	$_SESSION["p_complementoRemision"] = $row["complemento_remision"];
        	$_SESSION["p_dias_contrato"] = $row["diasAtencion"];
        	$_SESSION["p_valor_contrato"] = $row["ValorContrato"];
        	//Termina carga de parametros de la aplicación

        	$consultaGruposEtarios = " SELECT count(ID) as 'cantidad' FROM grupo_etario "; 
        	$resultadoGruposEtarios = $Link->query($consultaGruposEtarios);
        	if ($resultadoGruposEtarios->num_rows > 0) {
          		$dataGruposEtarios = $resultadoGruposEtarios->fetch_assoc();
        	}
        	$_SESSION['cant_gruposEtarios'] = $dataGruposEtarios['cantidad']; 

			$consultaDashboard = " SELECT dashboard FROM perfiles WHERE id = '"  .$row1["id_perfil"]. "'";
			$respuestaDashboard = $Link->query($consultaDashboard);
			if ($respuestaDashboard->num_rows > 0) {
				$dataDashboard = $respuestaDashboard->fetch_assoc();
				$dashboard = $dataDashboard['dashboard'];
				$consultaRutaDash = " SELECT ruta FROM dashboard WHERE id = '$dashboard'";
				$respuestaRutaDash = $Link->query($consultaRutaDash);
				if ($respuestaRutaDash->num_rows > 0 ) {
					$rutaDash = $respuestaRutaDash->fetch_assoc();
					$ruta = $rutaDash['ruta'];
					$rutaWin = substr($ruta, 1);
				}
			}
			$_SESSION['rutaDashboard'] = $ruta;
			$_SESSION['dashboard'] = $dataDashboard['dashboard'];

        	if($row1["nueva_clave"] == '' || $row1["nueva_clave"] < 1 ){
				$respuesta = [ 	'perfil' => 'nueva_clave',
								'dashboard' => $rutaWin
							];
			echo json_encode($respuesta);
          		// echo 'nueva_clave';
          		$_SESSION['nueva_clave'] = 'si';
        	}
        	else{
				$respuesta = [  'perfil' => $row1["id_perfil"],
								'dashboard' => $rutaWin
							];
				echo json_encode($respuesta);
          		// echo $row1["id_perfil"];
        	}
      	} 
		else {
        	// Usuario incorrecto, devolvemos 0
			$respuesta = [  'perfil' => '-1',
							'dashboard' => '-1'
						];
			echo json_encode($respuesta);
        	// echo("-1");
      	}
    } 
	else{
		$respuesta = [  'perfil' => '-1',
						'dashboard' => '-1'
					];
		echo json_encode($respuesta);
     	// echo("-1");
    }
}