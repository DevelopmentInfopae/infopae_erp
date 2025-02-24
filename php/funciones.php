<?php

function is_url_exist($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   return $status;
}



function round_up($number, $precision)
{
    $fig = (int) str_pad('1', $precision, '0');
    return (ceil($number * $fig) / $fig);
}

function round_down($number, $precision)
{
    $fig = (int) str_pad('1', $precision, '0');
    return (floor($number * $fig) / $fig);
}

function print_arrays($array) {
  echo "<pre>";
  var_dump($array);
  echo "</pre>";
  exit();
}

function rountTotalOrdenCompra(float $number) {
  /* 0 => No redondear, 1 => Redondear arriba, 2 => Redondear abajo */
  $redondeo = $_SESSION['p_redondeo_compra'];

  /* 1 => Cada día, 2 => Total, 3 => Cada Sede */
  $tipo = $_SESSION['p_tipo_redondeo_compra'];

  /* 1 => Redondear al entero mas cercano, 2 => Redondear al primer decimal, 3 => Redondear al segundo decimal */
  $rango = $_SESSION['p_rango_redondeo_compra'];

  if ($redondeo === '0') return $number; # Si esta deshabilitado el redondeo retorna el numero sin modificar
  if ($tipo !== '2') return $number; # Si el tipo es diferente de redondeo total retorna el numero sin modificar
  $aux = redondearNumero($number, $redondeo, $rango);
  return $aux;
}


function rountSedeOrdenCompra(float $number) {
  /* 0 => No redondear, 1 => Redondear arriba,2 => Redondear abajo */
  $redondeo = $_SESSION['p_redondeo_compra'];

  /* 1 => Cada día, 2 => Total, 3 => Cada Sede */
  $tipo = $_SESSION['p_tipo_redondeo_compra'];

  /* 1 => Redondear al entero mas cercano, 2 => Redondear al primer decimal, 3 => Redondear al segundo decimal */
  $rango = $_SESSION['p_rango_redondeo_compra'];

  if ($redondeo === '0') return $number; # Si no tiene parametrizado el redondeo retorna el numero sin modificar
  if ($tipo !== '3') return $number; # Si el tipo es diferente a cada Sede retorna el numero sin modificar

  $aux = redondearNumero($number, $redondeo, $rango);
  return $aux;
}

function rountRemision(float $number) {
  /* 0 => No redondear, 1 => Redondear arriba, 2 => Redondear abajo */
  $redondeo = $_SESSION['p_redondeo_remision'];

  /* 1 => Cada día, 2 => Total, 3 => Cada Sede */
  $tipo = $_SESSION['p_rango_redondeo_remision'];

  /* 1 => Redondear al entero mas cercano, 2 => Redondear al primer decimal, 3 => Redondear al segundo decimal */
  $rango = $_SESSION['p_rango_redondeo_remision'];

  if ($redondeo === '0') return $number;

  $aux = redondearNumero($number, $redondeo, $rango);
  return $aux;
}

function redondearNumero(float $numero, string $tipo, string $rango): float {
  switch ($rango) {
      case '1': // Redondear al entero más cercano
          return $tipo === '1' ? ceil($numero) 
               : ($tipo === '2' ? floor($numero) 
               : ($numero < 0.5 ? ceil($numero) : round($numero))); 

      case '2': // Redondear al primer decimal
          return $tipo === '1' ? ceil($numero * 10) / 10 
               : ($tipo === '2' ? floor($numero * 10) / 10 
               : ($numero * 10 - floor($numero * 10) < 0.5 ? ceil($numero * 10) / 10 : round($numero, 1)));

      case '3': // Redondear al segundo decimal
          return $tipo === '1' ? ceil($numero * 100) / 100 
               : ($tipo === '2' ? floor($numero * 100) / 100 
               : ($numero * 100 - floor($numero * 100) < 0.5 ? ceil($numero * 100) / 100 : round($numero, 2)));

      default:
          throw new InvalidArgumentException("Tipo de redondeo inválido");
  }
}

function dd($data){
  echo "<pre>";
  var_dump($data);
  echo "</pre>";
  exit();
}

// Función que corta los numeros en el numero de decimales que se requiera sin redondear
// Ej: $saldo = number_format_unlimited_precision($saldo,3,'.');
function number_format_unlimited_precision($number,$decimales,$decimal){
  $broken_number = explode($decimal,$number);
  if(isset($broken_number[1])){
    $nDecimales = "".$broken_number[1];
    $nDecimales = substr($nDecimales, 0, $decimales); 
    return number_format($broken_number[0]).$decimal.$nDecimales;
  }
  else{
    return number_format($broken_number[0]); 
  }
}

// Igualar decimales cuando el sustraendo tiene mas que el minuendo
// Devuelve el sustraendo con el mismo numero de decimales que el minuendo
// Ej: $consumoDia = decimales_sustranedo($saldo,$consumoDia,'.');
function decimales_sustranedo($minuendo,$sustraendo,$decimal){
  $broken_minuendo = explode($decimal,$minuendo);
  $broken_sustraendo = explode($decimal,$sustraendo);
  if(isset($broken_minuendo[1]) && isset($broken_sustraendo[1])){
    $decimalesMinuendo = "".$broken_minuendo[1];
    $decimalesSustraendo = "".$broken_sustraendo[1];
    $cantidadDecimalesMinuendo = strlen($decimalesMinuendo);
    $decimalesSustraendo = substr($decimalesSustraendo, 0, $cantidadDecimalesMinuendo); 
    $sustraendo = number_format($broken_sustraendo[0]).$decimal.$decimalesSustraendo;
  }
  return $sustraendo; 
}
// Ej: $consumoDia = menosDecimales($saldo,$consumoDia,'.');
function menosDecimales($minuendo,$sustraendo,$decimal){
  $broken_minuendo = explode($decimal,$minuendo);
  $broken_sustraendo = explode($decimal,$sustraendo);
  if(isset($broken_minuendo[1]) && isset($broken_sustraendo[1])){
    $decimalesMinuendo = "".$broken_minuendo[1];
    $decimalesSustraendo = "".$broken_sustraendo[1];
    $mesnosDecimales  = min(strlen($decimalesMinuendo),strlen($decimalesSustraendo)); 
    
      return $mesnosDecimales;
   
  }else{
    return 0;
  }
}

function mesEnLetras($mes){
  $mesLetras = '';
    switch ($mes) {
      case '01':
        $mesLetras = 'Enero';
        break;
      case '01':
        $mesLetras = 'Enero';
        break;
      case '02':
        $mesLetras = 'Febrero';
        break;
      case '03':
        $mesLetras = 'Marzo';
        break;
      case '04':
        $mesLetras = 'Abril';
        break;
      case '05':
        $mesLetras = 'Mayo';
        break;
      case '06':
        $mesLetras = 'Junio';
        break;
      case '07':
        $mesLetras = 'Julio';
        break;
      case '08':
        $mesLetras = 'Agosto';
        break;
      case '09':
        $mesLetras = 'Septiembre';
        break;
      case '10':
        $mesLetras = 'Octubre';
        break;
      case '11':
        $mesLetras = 'Noviembre';
        break;
      case '12':
        $mesLetras = 'Diciembre';
        break;

      default:
        # code...
        break;
    }
    return $mesLetras;
  }