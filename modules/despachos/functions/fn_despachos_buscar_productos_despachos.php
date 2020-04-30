<option value="">Seleccione</option>
<?php
  include '../../../config.php';
  require_once '../../../autentication.php';
  require_once '../../../db/conexion.php';

  $periodo_actual = $_SESSION['periodoActual'];

  $mes = (isset($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : "";
  $semana = (isset($_POST['semana'])) ? $Link->real_escape_string($_POST['semana']) : "";
  $tipo_complemento = (isset($_POST['tipo_complemento'])) ? $Link->real_escape_string($_POST['tipo_complemento']) : "";

  $condicion_sede = (isset($_POST["sede"]) && ! empty($_POST['sede'])) ? " AND d.cod_Sede = '".$_POST["sede"]."'" : "";
  $condicion_municipio = (isset($_POST["municipio"]) && ! empty($_POST['municipio'])) ? " AND s.cod_mun_sede = '".$_POST["municipio"]."'" : "";
  $condicion_intitucion = (isset($_POST["institucion"]) && ! empty($_POST['institucion'])) ? " AND s.cod_inst = '".$_POST["institucion"]."'" : "";

  $consulta = "SELECT
                DISTINCT
                p.CodigoProducto AS codigo,
                p.Descripcion As descripcion
              FROM
                productosmovdet$mes$periodo_actual p
                    INNER JOIN
                despachos_enc$mes$periodo_actual d ON (p.numero = d.Num_Doc)
                    INNER JOIN
                sedes$periodo_actual s ON (p.BodegaDestino = s.cod_sede)
              WHERE
                d.Semana = '$semana'
                AND d.Tipo_complem = '$tipo_complemento'
                $condicion_municipio
                $condicion_intitucion
                $condicion_sede
              ORDER BY descripcion";
  $respuesta = $Link->query($consulta) or die ('Error al cargar los complementos: '. $Link->error);
  if($respuesta->num_rows > 0)
  {
    $productos_array = [];

    while($producto = $respuesta->fetch_object())
    {
      $productos_array[$producto->codigo] = $producto->descripcion;
    }

    foreach ($productos_array as $clave => $producto)
    {
?>
      <option value="<?= $clave; ?>"><?= $producto; ?></option>
<?php
    }
  }
