<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// se capturan los datos que vienen en el formulario
$complemento = (isset($_POST['complemento']) && $_POST['complemento'] != '') ? mysqli_real_escape_string($Link, $_POST['complemento']) : '';
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$limiteInferior = (isset($_POST['limiteInferior']) && $_POST['limiteInferior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteInferior']) : '';
$limiteSuperior = (isset($_POST['limiteSuperior']) && $_POST['limiteSuperior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteSuperior']) : '';
$valor = (isset($_POST['valor']) && $_POST['valor'] != '') ? mysqli_real_escape_string($Link, $_POST['valor']) : '';

if ($limiteInferior > $limiteSuperior) {
      $respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'El limite inferor no puede ser mayor al limite superior'
      ];
      exit (json_encode($respuestaAJAX));
}

// vamos a validar que un complemento no pueda tener mas de dos valores ya que solo son dos tipos de pago
$consultaComplemento = "SELECT tipo_complem, tipo FROM manipuladoras_valoresnomina WHERE tipo_complem = '".$complemento."';";
$resComplemento = $Link->query($consultaComplemento) or die('Error al consultar tipo de complemento: '. mysqli_error($Link));
// exit(var_dump($consultaComplemento));
	if($resComplemento->num_rows == 2) {
	    $respuestaAJAX = [
	     'estado' => 0,
	     'mensaje' => 'El código de Complemento ya tiene registro en los dos tipos de pago'
	    ];
	    exit (json_encode($respuestaAJAX));
	}
  if($resComplemento->num_rows > 0) {
   	$mensaje = '';
		while($dataComplementos = $resComplemento->fetch_assoc()) { 
      // exit(var_dump($dataComplementos));
			if ($dataComplementos['tipo'] == '1' && $tipo == '1') {
				$mensaje = 'El codigo de complemento ya tiene registrado el tipo de pago por día';
        $respuestaAJAX = [
        'estado' => 0,
        'mensaje' => $mensaje
        ];
        exit (json_encode($respuestaAJAX));
			}
			elseif ($dataComplementos['tipo'] == '2' && $tipo == '2') {
				$mensaje = 'El codigo de complemento ya tiene registrado el tipo de pago por titular';
        $respuestaAJAX = [
        'estado' => 0,
        'mensaje' => $mensaje
        ];
        exit (json_encode($respuestaAJAX));
			}
		}
  }

// consulta para la creacion de los datos en la tabla manipuladoras_valoresnomina
$sentenciaCrear = "INSERT INTO manipuladoras_valoresnomina (tipo_complem, tipo, limiteInferior, limiteSuperior, valor) VALUES ('$complemento', '$tipo', '$limiteInferior', '$limiteSuperior', '$valor');";

$respuestaCrear = $Link->query($sentenciaCrear) or die('Error valor manipuladora nomina'. mysqli_error($Link));
  if($respuestaCrear) {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '54', 'Se creó el valor nomina de manipuladora: <strong>".$complemento.' de tipo '. $tipo."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El valor manipuladora nomina se creo exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El valor manipuladora nomina NO se creo exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);
