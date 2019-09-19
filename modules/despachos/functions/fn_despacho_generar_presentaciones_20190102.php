<?php
// Marzo 8 de 2017, se va a calcular de a cuerdo a las presentaciones, cuanto se de be enviar de cada presentación
// tomamos como base la variable cantund3 > 0 dado que la mayoria de los productos tienen hasta la 2.
// Este proceso se repite para cada grupo etario.

// echo "<br><br>Haciendo Calculos<br><br>";
var_dump($auxAlimento);
var_dump($sede);



$cantidadNecesaria = ($auxAlimento["grupo1"] * $sede["grupo1"]) + ($auxAlimento["grupo2"] * $sede["grupo2"]) + ($auxAlimento["grupo3"] * $sede["grupo3"]);

$valor2 = '';
$valor3 = '';
$valor4 = '';
$valor5 = '';
$necesario2 = 0;
$necesario3 = 0;
$necesario4 = 0;
$necesario5 = 0;
$tomadoTotal = 0;

        if($auxAlimento['cantidadund3'] > 0){

          //echo "<br>Cantidad: $cantidadNecesaria";


          //Unidad 2
          //$valor2 = $auxAlimento['cantidadund3'] /  $auxAlimento['cantidadPresentacion'];

          $valor2 = $auxAlimento['factor'] / $auxAlimento['cantidadPresentacion'];
          $necesario = $cantidadNecesaria;
          $necesarioFactor = $cantidadNecesaria / $auxAlimento['cantidadPresentacion'];
          $necesarioEntera = intval($necesario);
          $necesario2 = $necesarioEntera;
          $tomado = $necesarioEntera * ($auxAlimento['factor'] / $auxAlimento['cantidadPresentacion']);
          $saldo = intval($necesarioFactor) - intval($tomado);
          $tomadoTotal = $tomadoTotal + $tomado;




          if( $saldo > 0 && $auxAlimento['cantidadund3'] == 0 ){
            $necesario2 = $necesario2 + 1;
            $tomado = 1 * ($auxAlimento['factor'] / $auxAlimento['cantidadPresentacion']);
            $saldo = 0;
            $tomadoTotal = $tomadoTotal + $tomado;
          }

          // echo "<br>Necesario Factor: $necesarioFactor";
          // echo "<br>Necesario: $necesario";
          // echo "<br>Entera: $necesarioEntera";
          // echo "<br>Tomado: $tomado";
          // echo "<br>Saldo: $saldo";
          // echo "<br>Cantidad: $necesario2";













          // echo "<br><br>xxxx<br><br>";















          // Unidad 3
          $valor3 = $auxAlimento['cantidadund3'] /  $auxAlimento['cantidadPresentacion'];
          $valor = $valor3;
          $necesario = $saldo / $valor;
          $necesarioEntera = intval($necesario);
          $necesario3 = $necesarioEntera;
          $tomado = $necesarioEntera  * $valor;
          $saldo = intval($saldo) - intval($tomado);
          $tomadoTotal = $tomadoTotal + $tomado;
          if($necesario < 1 && $auxAlimento['cantidadund4'] == 0){
            $necesario3 = $necesario3 + 1;
            $saldo = 0;
            $tomado = $valor;
            $tomadoTotal = $tomadoTotal + $tomado;
          }
          // echo "<br>Valor: $valor";
          // echo "<br>Necesario: $necesario";
          // echo "<br>Entera: $necesarioEntera";
          // echo "<br>Tomado: $tomado";
          // echo "<br>Saldo: $saldo";
          // echo "<br>Cantidad: $necesario3";
          // echo "<br>";






          // Unidad 4
          if($saldo > 0 && $auxAlimento['cantidadund4'] > 0  ){
            $valor4 = $auxAlimento['cantidadund4'] /  $auxAlimento['cantidadPresentacion'];
            $valor = $valor4;
            $necesario = $saldo / $valor;
            $necesarioEntera = intval($necesario);
            $necesario4 = $necesarioEntera;
            $tomado = $necesarioEntera  * $valor;

            if($saldo < 1 && $auxAlimento['cantidadund5'] == 0){
              $necesario4 = $necesario4 + 1;
              $saldo = 0;
              $tomado = $valor;
            }else{
              $saldo = intval($saldo) - intval($tomado);
            }
            $tomadoTotal = $tomadoTotal + $tomado;


            if( $saldo > 0 && $auxAlimento['cantidadund5'] == 0 ){
            $necesario4 = $necesario4 + 1;
            $tomado = 1 * ($auxAlimento['cantidadund4'] / $auxAlimento['cantidadPresentacion']);
            $saldo = 0;
            $tomadoTotal = $tomadoTotal + $tomado;
          }

            // echo "<br>Valor: $valor";
            // echo "<br>Necesario: $necesario";
            // echo "<br>Entera: $necesarioEntera";
            // echo "<br>Tomado: $tomado";
            // echo "<br>Saldo: $saldo";
            // echo "<br>Cantidad: $necesario4";
            // echo "<br>";
          }

           // Unidad 5
          if($auxAlimento['cantidadund5'] > 0  ){
            $valor5 = $auxAlimento['cantidadund5'] /  $auxAlimento['cantidadPresentacion'];
            $valor = $valor5;
            $necesario = $saldo / $valor;
            $necesarioEntera = intval($necesario);
            $necesario5 = $necesario5 + $necesarioEntera;
            $tomado = $necesarioEntera  * $valor;
            $saldo = intval($saldo) - intval($tomado);
            $necesario = $saldo / $valor;
            $tomadoTotal = $tomadoTotal + $tomado;





            if( $necesario < 1){
              $necesario5 = $necesario5 + 1;
              $saldo = 0;
              $tomado = $valor;
              $tomadoTotal = $tomadoTotal + $tomado;
            }



            // echo "<br>Valor: $valor";
            // echo "<br>Necesario: $necesario";
            // echo "<br>Entera: $necesarioEntera";
            // echo "<br>Tomado: $tomado";
            // echo "<br>Saldo: $saldo";
            // echo "<br>Cantidad: $necesario5";
            // echo "<br>";
          }

          // Se va a revizar las cantidades de las presentaciones de abajo hacia arriba para el caso de 2 o mas presentaciones
          // pequeñas que tienen el mismo contenido de una presentación mayor.

          // echo "<br>Resumen:";
          // echo "<br>Unidad 2 - $valor2 - $necesario2";
          // echo "<br>Unidad 3 - $valor3 - $necesario3";
          // echo "<br>Unidad 4 - $valor4 - $necesario4";
          // echo "<br>Unidad 5 - $valor5 - $necesario5";

          // Iniciamos con la 5 por que es la unidad más pequeña.

          //Comparando la variable 5 con las anteriores
          if($valor5 != '' && $necesario5 != 0  && $valor5 > 0){

            $aux = $valor5 * $necesario5;
            $aux = intval($aux/$valor2);
            $necesario2 = $necesario2 + $aux;
            $necesario5 = (intval($valor5 * $necesario5) - intval($aux * $valor2)) / $valor5;

            $aux = $valor5 * $necesario5;
            $aux = intval($aux/$valor3);
            $necesario3 = $necesario3 + $aux;
            $necesario5 = (intval($valor5 * $necesario5) - intval($aux * $valor3)) / $valor5;

            $aux = $valor5 * $necesario5;
            $aux = intval($aux/$valor4);
            $necesario4 = $necesario4 + $aux;
            $necesario5 = (intval($valor5 * $necesario5) - intval($aux * $valor4)) / $valor5;
          }


           //Comparando la variable 4 con las anteriores
          if($valor4 != '' && $necesario4 != 0 && $valor4 > 0){

            $aux = $valor4 * $necesario4;
            $aux = intval($aux/$valor2);
            $necesario2 = $necesario2 + $aux;
            $necesario4 = (intval($valor4 * $necesario4) - intval($aux * $valor2)) / $valor4;

            $aux = $valor4 * $necesario4;
            $aux = intval($aux/$valor3);
            $necesario3 = $necesario3 + $aux;
            $necesario4 = (intval($valor4 * $necesario4) - intval($aux * $valor3)) / $valor4;

          }



          //Comparando la variable 3 con las anteriores
          if($valor3 != '' && $necesario3 != 0 && $valor3 > 0){

            $aux = $valor3 * $necesario3;
            $aux = intval($aux/$valor2);
            $necesario2 = $necesario2 + $aux;
            $necesario3 = (intval($valor3 * $necesario3) - intval($aux * $valor2)) / $valor3;

          }





          // echo "<br>Nuevo Resumen:";
          // echo "<br>Unidad 2 - $valor2 - $necesario2";
          // echo "<br>Unidad 3 - $valor3 - $necesario3";
          // echo "<br>Unidad 4 - $valor4 - $necesario4";
          // echo "<br>Unidad 5 - $valor5 - $necesario5";
          // echo "<br><br>";

          $tomadoTotal = $tomadoTotal /  ($auxAlimento['factor'] / $auxAlimento['cantidadPresentacion']);
          // echo "<br>Tomado Total: $tomadoTotal";
        }
        //Termina el calculo de las presentaciones
