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