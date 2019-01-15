<?php
  require_once 'autenticacion_carga_de_archivos.php';
  //error_reporting(E_ALL);
  error_reporting(E_ERROR | E_PARSE);
  require_once 'db/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Carga de Archivos</title>
	<LINK REL="SHORTCUT ICON" HREF="favicon.ico" />
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">



<link rel="stylesheet" type="text/css" href="css/estilos.css">


</head>
<body class="superior">


 <header>
    <div class="logotipo">
      <img src="imagenes/logo.jpg" alt="">
    </div>
    <div class="enbezados">

      <h1>Carga de Archivos</h1>
        <nav>
          Bienvenido: <?php echo utf8_encode($_SESSION['usuario']); ?> | <a href="cambiar_clave.php">Cambiar clave</a> | <a href="cerrar_sesion.php">Cerrar Sesión</a>
        </nav>

    </div>
  </header>



<article>

<?php
	$dispositivo="";
	$dispositivo = $_POST["dispositivo"];


	if ($dispositivo < 10) {
		$dispositivo = '00'.$dispositivo;
	}
	else if ($dispositivo < 100) {
		$dispositivo = '0'.$dispositivo;
	}

	//echo "<br>Dispositivo: ".$dispositivo."<br>";




	$ext_permitidas = array("kq","KQ");
	$bandera=0;

	if (isset($_FILES['archivo'])) {
    	$archivo = $_FILES['archivo'];
	    //$rand=rand();
	    //$nombre=$rand.$_FILES['archivo']['name'];
	    $nombre=$_FILES['archivo']['name'];



	    //Obtiene la extension del archivo
	    if (!(strpos($nombre,".")===false)) {
		    $file_ext = explode(".",$nombre);

		    $longitudArray = count($file_ext);
		    $longitudArray--;

		    $file_ext = $file_ext[$longitudArray];


		    $nombreSinExt = str_replace('.'.$file_ext, '', $nombre);











		}


		// Validación de la extensión del archivo
	    if (isset($file_ext)) {
	        if (!in_array($file_ext,$ext_permitidas)) {

	        	echo '<h2 class="errorCarga">El archivo es de una extención diferente a la requerida .KQ<h2>';
	            $bandera++;

	            ?>

				<form name="carga_de_archivos" id="carga_de_archivos" action="carga_de_archivos.php"> <button type="submmit"><< Volver a intentar la carga del Archivo</button> </form> <?php
	        }
	    }
	    // Fin Validación de la extensión del archivo


	    // Validación del nombre del archivo
	    if ( isset($nombreSinExt) && $bandera == 0 ) {
	        if ($nombreSinExt != "BAK") {
	        	echo '<h2 class="errorCarga">El nombre de archivo es diferente al esperado "BAK.KQ"<h2>';
	            $bandera++;
	            ?>
				<form name="carga_de_archivos" id="carga_de_archivos" action="carga_de_archivos.php"> <button type="submmit"><< Volver a intentar la carga del Archivo</button> </form> <?php
	        }
	    }
	    // Fin Validación del nombre del archivo


	    // Revisando el consecutivo de nombres
	    if($bandera==0){




        if (!file_exists("usb_bak/".$dispositivo)) {
          mkdir("usb_bak/".$dispositivo, 0777, true);
        }








	    	if(file_exists("usb_bak/".$dispositivo."/$nombre")){
	    		$bandera++;
	    		$contador=1;

	    		while($bandera > 0){
	    			$nombre = $nombreSinExt.$contador.".".$file_ext;
	    			if(file_exists("usb_bak/".$dispositivo."/$nombre")){
	    				$contador++;
	    			}
	    			else{
	    				$bandera = 0;
	    			}
	    		}

	    	}


	    }
	    // Fin Revizando el consecutivo de nombres






  		if($bandera==0){

  			//if (move_uploaded_file($archivo['tmp_name'], "$nombre")) {
			if (move_uploaded_file($archivo['tmp_name'], "usb_bak/".$dispositivo."/$nombre")) {
	    		?>
					<h2><?php echo "El Archivo se ha cargado con Exito."; ?></h2>


					<?php
						$mysqli = new mysqli($Hostname , $Username,   $Password,  $Database);
						if ($mysqli->connect_errno) {
						  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
						}
						// Haciendo registro en el log
		                $logIdUsr = $_SESSION['id_usuario'];
		                date_default_timezone_set('America/Bogota');
		                $fecha = date('Y-m-d H:i:s');

		                $consulta = " insert into log (id_usuario,fecha,descripcion)
		                values ('$logIdUsr','$fecha','Termino de cargar un archivo') ";
		                //echo '<br>Consulta para el log: '.$consulta;

		                $mysqli->query($consulta);
		                mysqli_close($mysqli);
            			// Termina hacer registro en el log
 					?>









	    		<?php
     		}
     		else {
        		?>
					<h2><?php echo "Se ha presentado un error con la carga del archivo."; ?></h2>
	    		<?php
    		}
        }
	}// Fin del if que recibe un tipo de dato archivo.
	else{
		?>
			<h2><?php echo "No se ha recibido archivo para cargar."; ?></h2>
		<?php
	}
?>



</article>

</body>
</html>
