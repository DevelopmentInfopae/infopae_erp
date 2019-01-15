<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  $data = [];
  $usuario = (isset($_POST['usuario']) && $_POST['usuario'] != '') ? mysqli_real_escape_string($Link, $_POST['usuario']) : '';

  $consulta = "SELECT
                usub.ID AS idUsuarioBodega,
                usu.nombre AS nombreUsuario,
                bent.NOMBRE AS bodegaEntrada,
                bsal.NOMBRE AS bodegaSalida
              FROM
                usuarios_bodegas usub
                    INNER JOIN
                bodegas bent ON bent.ID = usub.COD_BODEGA_ENTRADA
                    INNER JOIN
                bodegas bsal ON bsal.ID = usub.COD_BODEGA_SALIDA
                INNER JOIN usuarios usu ON usu.id = usub.USUARIO
              WHERE
                usub.USUARIO = '$usuario';";
  $resultado = $Link->query($consulta);
  if ($resultado->num_rows > 0)
  {
  	while($registros = $resultado->fetch_assoc())
  	{
      $registros['input'] = '<input type="checkbox" class="usuarioBodega" name="usuarioBodega[]" id="usuarioBodega" value="'. $registros['idUsuarioBodega'] .'">';
  		$data[] = $registros;
  	}
  }

  $salida = [
  	'sEcho' => 1,
  	'iTotalRecords' => count($data),
  	'iTotalDisplayRecords' => count($data),
  	'aaData' => $data
  ];

  echo json_encode($salida);