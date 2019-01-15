<?php
  //require_once 'autentication.php';
  error_reporting(E_ALL);
  require_once 'db/conexion.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<title>Carga de archivos</title>
<LINK REL="SHORTCUT ICON" HREF="favicon.ico" />
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">



<link rel="stylesheet" type="text/css" href="theme/css/style.css">

<script src="js/jquery.js"></script>




  </head>
  <body class="superior">


<header>
    <div class="logotipo">
      <img src="img/logo.png" alt="">
    </div>
    <div class="enbezados">
      <h1>Carga de Archivos</h1>
        <nav>
          Bienvenido: <?php echo utf8_encode($_SESSION['usuario']); ?> |
          <a href="carga_de_archivos.php">Inicio</a> |

<!--
          <a href="cambiar_clave.php">Cambiar clave</a> |
          <a href="manuales_manipuladora/fabricante.pdf" target="_blank">Manual del Fabricante</a> |
    -->

          <a href="manuales_manipuladora/usuario.pdf" target="_blank">Manual de Usuario</a> |
          <a href="cerrar_sesion.php">Cerrar Sesión</a>
        </nav>
    </div>
  </header>



    <article>








      <?php
        $idDispositivo = "";
        $annoActual = $_SESSION['periodoActual'];

        //print_r($_SESSION);
        $idUsuario = $_SESSION['id_usuario'];



        $consulta = "select dis.* , sed.nom_sede from dispositivos  dis join sedes".$annoActual." sed on sed.cod_sede = dis.cod_sede where id_usuario = $idUsuario";


        //echo $consulta;


        $Link = new mysqli($Hostname, $Username, $Password, $Database);
        $Link->set_charset("utf8");
        $result = $Link->query($consulta) or die(mysqli_error($Link));
        $Link->close();

        if($result->num_rows <= 0){
          echo "El usuario no tiene dispositivo asignado!";
        }else {
      ?>
        <div>




          <!--
          <p>
            <strong>Id de Dispositivo:</strong> <?php echo $row["id"]; ?>
            <br>
            <strong>Referencia:</strong> <?php echo $row["referencia"]; ?>
            <br>
            <strong>Serial:</strong> <?php echo $row["num_serial"]; ?>
            <br>
            <strong>Sede:</strong> <?php echo $row["nom_sede"]; ?>
          </p>

          -->





        </div>
        <br>
        <form action="fn_carga_archivos.php" method="post" enctype="multipart/form-data" name="formarchivo" id="formarchivo">


          <label for="dispositivo">Dispositivo:</label><br>
          <select name="dispositivo" id="dispositivo">
            <option value="">Selecciones Uno</option>




            <?php
              while ($row = $result->fetch_assoc()) { ?>

              <option value="<?php echo $row["id"]; ?>"><?php echo $row["id"]." - ".$row["num_serial"]." - ".$row["nom_sede"]; ?></option>

              <?php }




            ?>












          </select>
          <br><br><br>




          <input type="file" name="archivo" id="archivo" accept=".kq" />
          <br />
          <br />
          <br />

          <button type="button" name="enviar" onclick="enviararchivo();">Enviar</button>

        </form>
        <script>
          function enviararchivo(){
            var bandera = 0;
            if($('#dispositivo').val() == ''){
              bandera++;
              alert('Debe seleccionar un dispositivo');
              $('#dispositivo').focus();
            }
            else if($('#archivo').val() == ''){
              bandera++;
              alert('Debe seleccionar un archivo .KQ');
              $('#archivo').focus();
            }

            if(bandera == 0){
              $('#formarchivo').submit();
            }

          }
        </script>



      <?php } ?> <!-- Final del if que valida que el usuario tenga asociado un dispositivo -->
















    </article>
  </body>
</html>
