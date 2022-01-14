<?php 
require_once '../../../db/conexion.php';
require_once '../../../config.php';

// se capturan los datos que vienen en el formulario
$complemento = (isset($_POST['complemento']) && $_POST['complemento'] != '') ? mysqli_real_escape_string($Link, $_POST['complemento']) : '';
$tipo = (isset($_POST['tipo']) && $_POST['tipo'] != '') ? mysqli_real_escape_string($Link, $_POST['tipo']) : '';
$limiteInferior = (isset($_POST['limiteInferior']) && $_POST['limiteInferior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteInferior']) : '';
$limiteSuperior = (isset($_POST['limiteSuperior']) && $_POST['limiteSuperior'] != '') ? mysqli_real_escape_string($Link, $_POST['limiteSuperior']) : '';
$valor = (isset($_POST['valor']) && $_POST['valor'] != '') ? mysqli_real_escape_string($Link, $_POST['valor']) : '';

// validamos que el limite inferior no pueda ser mayor al limite superior
if ($limiteInferior > $limiteSuperior) {
      $respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'El límite inferor no puede ser mayor al límite superior'
      ];
      exit (json_encode($respuestaAJAX));
}

// se valida que los limites no puedan quedar con el mismo valor
if ($limiteInferior == $limiteSuperior) {
      $respuestaAJAX = [
       'estado' => 0,
       'mensaje' => 'El límite inferor no puede ser igual al límite superior'
      ];
      exit (json_encode($respuestaAJAX));
}

// se valida que el limite superior de pago por dia no pueda ser igual o mayor a el limite inferior de conteo por titular
if ($tipo == 1) {
    $consultaLimite = "SELECT limiteInferior FROM manipuladoras_valoresnomina WHERE tipo_complem = '".$complemento. "' AND tipo = 2;";
    $resConsultaLimite = $Link->query($consultaLimite) or die('Error al consultar el límite inferior '. mysqli_error($Link));
    if ($resConsultaLimite->num_rows > 0) {
        while ($DataConsultaLimite = $resConsultaLimite->fetch_assoc()) {
          if ($limiteSuperior >= $DataConsultaLimite['limiteInferior']) {
            $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El límite superior de pago por día no puede ser igual o mayor al límite inferior de pago por titular'
            ];
            exit (json_encode($respuestaAJAX));
          }
        }
    }
}

if ($tipo == 2) {
    $consultaLimite2 = "SELECT limiteSuperior FROM manipuladoras_valoresnomina WHERE tipo_complem = '".$complemento. "' AND tipo = 1;";
    $resConsultaLimite2 = $Link->query($consultaLimite2) or die('Error al consultar el límite superior '. mysqli_error($Link));
    if ($resConsultaLimite2->num_rows > 0) {
        while ($DataConsultaLimite2 = $resConsultaLimite2->fetch_assoc()) {
          if ($limiteInferior <= $DataConsultaLimite2['limiteSuperior']) {
            $respuestaAJAX = [
            'estado' => 0,
            'mensaje' => 'El límite inferior de pago por titular no puede ser igual o menor al límite superior de pago por día'
            ];
            exit (json_encode($respuestaAJAX));
          }
        }
    }
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
				$mensaje = 'El código de complemento ya tiene registrado el tipo de pago por día';
        $respuestaAJAX = [
        'estado' => 0,
        'mensaje' => $mensaje
        ];
        exit (json_encode($respuestaAJAX));
			}
			elseif ($dataComplementos['tipo'] == '2' && $tipo == '2') {
				$mensaje = 'El código de complemento ya tiene registrado el tipo de pago por titular';
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

$respuestaCrear = $Link->query($sentenciaCrear) or die('Error valor manipuladora nómina'. mysqli_error($Link));
  if($respuestaCrear) {
    $consultaBitacora = "INSERT INTO bitacora (fecha, usuario, tipo_accion, observacion) VALUES ('" . date("Y-m-d H-i-s") . "', '" . $_SESSION["idUsuario"] . "', '54', 'Se creó el valor nómina de manipuladora: <strong>".$complemento.' de tipo '. $tipo."</strong>')";
    $Link->query($consultaBitacora) or die ('Unable to execute query. '. mysqli_error($Link));

  	$respuestaAJAX = [
  		'estado' => 1,
  		'mensaje' => 'El valor manipuladora nómina se creó exitosamente.'
  	];
  }
  else
  {
  	$respuestaAJAX = [
  		'estado' => 0,
  		'mensaje' => 'El valor manipuladora nómina NO se creó exitosamente.'
  	];
  }

  echo json_encode($respuestaAJAX);
