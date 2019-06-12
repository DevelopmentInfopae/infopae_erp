<?php
  require_once '../../../db/conexion.php';
  require_once '../../../config.php';

  // Declaración de variables.
  $periodo_actual = $_SESSION['periodoActual'];
  $mes = (isset($_POST['mes']) && ! empty($_POST['mes'])) ? $Link->real_escape_string($_POST['mes']) : '';
  $sede = (isset($_POST['sede']) && ! empty($_POST['sede'])) ? $Link->real_escape_string($_POST['sede']) : '';
  $semana = (isset($_POST['semana']) && ! empty($_POST['semana'])) ? $Link->real_escape_string($_POST['semana']) : '';
  $tipo_complemento = (isset($_POST['tipo_complemento']) && !empty($_POST['tipo_complemento'])) ? $Link->real_escape_string($_POST['tipo_complemento']) : '';

  // Consulta que retorna los dias de planillas el mes seleccionado.
  $consultaPlanillaDias = "SELECT D1, D2, D3, D4, D5, D6, D7, D8, D9, D10, D11, D12, D13, D14, D15, D16, D17, D18, D19, D20, D21, D22, D23, D24, D25, D26, D27, D28, D29, D30, D31  FROM planilla_dias WHERE mes = '$mes';";
  $resultadoPlanillaDias = $Link->query($consultaPlanillaDias) or die("Error al consultar planilla_dias: ". $Link->error);
  if ($resultadoPlanillaDias->num_rows > 0)
  {
    while ($registroPlanillasDias = $resultadoPlanillaDias->fetch_assoc())
    {
      $planilla_dias = $registroPlanillasDias;
    }
  }

  // Consulta para determinar las columnas de días de consulta por la semana seleccionada.
  $columnasDiasEntregas_res = $columnasDiasSuma = "";
  $consultaPlanillaSemanas = "SELECT DIA as dia FROM planilla_semanas WHERE semana = '$semana'";
  $resultadoPlanillaSemanas = $Link->query($consultaPlanillaSemanas) or die("Error al consultar planilla_semanas: ". $Link->error);
  if($resultadoPlanillaSemanas->num_rows > 0)
  {
    $indiceDia = 1;
    while($registroPlanillaSemanas = $resultadoPlanillaSemanas->fetch_assoc())
    {
      foreach ($planilla_dias as $clavePlanillasDias => $valorPlanillasDias)
      {
        if ($registroPlanillaSemanas["dia"] == $valorPlanillasDias)
        {
          $columnasDiasEntregas_res .= "SUM(". $clavePlanillasDias .") AS total_D". $indiceDia .", ";
          $indiceDia++;
        }
      }
    }
  }

  //
  $consulta_total_priorizacion_semana = "SELECT $tipo_complemento AS priorizado_dia, $tipo_complemento * (SELECT COUNT(*) FROM planilla_semanas WHERE SEMANA='$semana' AND MES='$mes') AS total_priorizado FROM priorizacion$semana WHERE cod_sede = '$sede'";
  $respuesta_total_priorizacion_semana = $Link->query($consulta_total_priorizacion_semana) or die('Error al consultar total de priorización por semana en priorizacion$semana '. $Link->error);
  if ($respuesta_total_priorizacion_semana->num_rows > 0)
  {
    $total_priorizado_semana = $respuesta_total_priorizacion_semana->fetch_object();

    $priorizado_dia = $total_priorizado_semana->priorizado_dia;
    $total_priorizado = $total_priorizado_semana->total_priorizado;
  }


  $columnasDiasEntregas_res = trim($columnasDiasEntregas_res, ', ');
  $consulta_total_priorizacion_dia = "SELECT $columnasDiasEntregas_res FROM entregas_res_$mes$periodo_actual WHERE cod_sede = '$sede' AND tipo_complem='$tipo_complemento';";
  $respuesta_total_priorizacion_dia = $Link->query($consulta_total_priorizacion_dia) or die('Error al consultar total de priorización en entregas_res$mes$periodo_actual'. $Link->error);
  if ($respuesta_total_priorizacion_dia->num_rows > 0)
  {
    $total_priorizacion_dia = $respuesta_total_priorizacion_dia->fetch_object();

    $total_priorizacion_dia->total_priorizado_dia = $priorizado_dia;
    $total_priorizacion_dia->total_priorizado_semana = $total_priorizado;
  }

  $total_dia_1 = (isset($total_priorizacion_dia->{'total_D1'}) ? $total_priorizacion_dia->{'total_D1'} : 0);
  $total_dia_2 = (isset($total_priorizacion_dia->{'total_D2'}) ? $total_priorizacion_dia->{'total_D2'} : 0);
  $total_dia_3 = (isset($total_priorizacion_dia->{'total_D3'}) ? $total_priorizacion_dia->{'total_D3'} : 0);
  $total_dia_4 = (isset($total_priorizacion_dia->{'total_D4'}) ? $total_priorizacion_dia->{'total_D4'} : 0);
  $total_dia_5 = (isset($total_priorizacion_dia->{'total_D5'}) ? $total_priorizacion_dia->{'total_D5'} : 0);
  $suma_total_dias = $total_dia_1 + $total_dia_2 + $total_dia_3 + $total_dia_4 + $total_dia_5;

  if ($suma_total_dias >= $total_priorizacion_dia->total_priorizado_semana)
  {
    $respuesta_ajax = [
      'estado'=>2,
      'mensaje'=>'La focalización se encuentra completa. Por favor cambie los filtros de búsqueda.',
      'datos'=> json_encode($total_priorizacion_dia)
    ];
    echo json_encode($respuesta_ajax);
    exit();
  }

  $respuesta_ajax = [
    'estado'=>1,
    'datos'=> json_encode($total_priorizacion_dia)
  ];
  echo json_encode($respuesta_ajax);
