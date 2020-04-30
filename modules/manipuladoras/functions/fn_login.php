<?php
  include '../config.php';
  $periodoActualCompleto = $_SESSION['periodoActualCompleto'];



    include("../db/conexion.php");
    $mysqli = new mysqli($Hostname , $Username,   $Password,  $Database);
    if ($mysqli->connect_errno) {
        echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
    $mysqli->set_charset("utf8");

    $user = mysqli_real_escape_string($mysqli, $_POST['user']);
    $passencriptado = mysqli_real_escape_string($mysqli, $_POST['pass']);
    $periodoActual = substr($periodoActualCompleto, 2);

    $vlsql ="select * FROM usuarios WHERE email='".$user."' AND clave='".$passencriptado."'";







    /* Consultas de selección que devuelven un conjunto de resultados */
    if ($resultado = $mysqli->query($vlsql)) {
        if($resultado->num_rows >= 1){
            $row1 = $resultado->fetch_assoc();
            $_SESSION["autentificado"]="SI";
            $_SESSION['usuario']= $row1["nombre"];
            $_SESSION['num_doc']= $row1["num_doc"];
            $_SESSION['tipoUsuario']= $row1["Tipo_Usuario"];
            $_SESSION['login']= $row1["email"];
            $_SESSION['perfil']= $row1["id_perfil"];
            $_SESSION['id_usuario']= $row1["id"];
            $_SESSION['periodoActual'] = $periodoActual;
            $_SESSION['periodoActualCompleto'] = $periodoActualCompleto;



            // Haciendo registro en la bitacora
                $logIdUsr = $_SESSION['id_usuario'];
                date_default_timezone_set('America/Bogota');
                $fecha = date('Y-m-d H:i:s');

                $consulta = " insert into bitacora (fecha, usuario, tipo_accion, observacion ) values ('$fecha','$logIdUsr',1,'Inicio Sesion') ";
                //echo '<br>Consulta para la bitacora: '.$consulta;
                $mysqli->query($consulta);
            // Termina hacer registro en la bitacora



            //Cargando parametros de la aplicación
            $consulta = " select * from parametros ";
            $resultado = $mysqli ->query($consulta) or die ('Unable to execute query. '. mysqli_error($Link));

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
            

















            if($row1["nueva_clave"] == '' || $row1["nueva_clave"] < 1 ){
                echo 'nueva_clave';
                $_SESSION['nueva_clave'] = 'si';
            }
            else{
                echo $row1["id_perfil"];
            }








        }
        else{
            // Usuario incorrecto, devolvemos 0
            echo("0");
        }
        /* liberar el conjunto de resultados */
        $resultado->close();
    }
    else{
        echo("0");
    }
    mysqli_close($mysqli);
