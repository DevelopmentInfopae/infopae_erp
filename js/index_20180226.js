            // Linea
            var data2 = [
                [gd(2017, 01, 01), 5], [gd(2017, 01, 23), 20]
            ];
            console.log(data2);

            var data3 = [
                [gd(2017, 1, 1), 800], [gd(2017, 1, 2), 500]
            ];


			function gd(year, month, day) {
				return new Date(year, month - 1, day).getTime();
			}




$(document).ready(function(){
	console.log('Iniciando la busqueda de los valores para las graficas.');
	buscarCantidadesGraficos(0,1);
    $('#btnActualizarGrafica').click(function(){
        actualizarGraficas();
    });

    $('.timeOption').click(function(){
        $('.timeOption').removeClass('active');
        $(this).addClass('active');
        var aux = $('.timeOption.active').attr('value');
        buscarCantidadesGraficos(0,aux);
    
    });
	
});







    


function actualizarGraficas(){
    console.log('Actualizar graficas');
    var aux = $('.timeOption.active').attr('value');
    buscarCantidadesGraficos(1,aux);
}

//timeOption  (semana 1/mes 2)
function buscarCantidadesGraficos(actualizar,timeOption){
    console.log('Leer cantidades.');
    // El parametro actualizar cuando es 0 solo lle el archivo arrays.txt
    // cuando es igual a 1 actualiza el contenido del archivo.
    var datos = {"actualizar":actualizar,"timeOption":timeOption};
	$.ajax({
		type: "POST",
        data: datos,
		url: "functions/fn_datos_graficos_index.php",
		beforeSend: function(){
		  $('#loader').fadeIn();	
		},
		success: function(data){
			try {
				var obj = JSON.parse(data);
				console.log(obj.barras);
                console.log(obj.entregas);
                graficar(obj.entregas,obj.barras,timeOption);
                
	

			}
			catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
			}
		}
	})
	.done(function(){ })
	.fail(function(){ })
	.always(function(){
        $('#loader').fadeOut();  
    	console.log('Terminó el Ajax');
	});
}


function graficar(data2,data3,timeOption){


            console.log('Cambios en el dibujo '+timeOption);
            if(timeOption == 1){
                var anchoBarra = 24 * 60 * 60 * 600;

            var options = {
                xaxis: {
                    mode: "time",
                    tickSize: [30, "day"],
                    tickLength: 0,
                    axisLabel: "Date",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Arial',
                    axisLabelPadding: 10,
                    color: "#d5d5d5"
                },
                yaxes: [{
                    position: "left",
                    //max: cantidadMaxima,
                    color: "#d5d5d5",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Arial',
                    axisLabelPadding: 3
                }, {
                    position: "right",
                    clolor: "#d5d5d5",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: ' Arial',
                    axisLabelPadding: 67
                }
                ],
                legend: {
                    noColumns: 1,
                    labelBoxBorderColor: "#000000",
                    position: "nw"
                },
                grid: {
                    hoverable: false,
                    borderWidth: 0
                }
            };


            }else{
                var anchoBarra = 1; 
                var options = {
                xaxis: {
                    // mode: "time",
                    // tickSize: [30, "day"],
                    tickLength: 0,
                    axisLabel: "Date",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: 'Arial',
                    axisLabelPadding: 0,
                    color: "#d5d5d5"
                },
                yaxes: [{
                    // position: "left",
                    // max: 1000000,
                    // color: "#d5d5d5",
                    // axisLabelUseCanvas: true,
                    // axisLabelFontSizePixels: 12,
                    // axisLabelFontFamily: 'Arial',
                    // axisLabelPadding: 0
                }, {
                    position: "right",
                    clolor: "#d5d5d5",
                    axisLabelUseCanvas: true,
                    axisLabelFontSizePixels: 12,
                    axisLabelFontFamily: ' Arial',
                    axisLabelPadding: 0
                }
                ],
                legend: {
                    noColumns: 1,
                    labelBoxBorderColor: "#000000",
                    position: "nw"
                },
                grid: {
                    hoverable: false,
                    borderWidth: 0
                },
                legend: {
                    show: true
                },
                tooltip: true,
                tooltipOpts: {
                    content: "x: %x, y: %y"
                }
            };   
            }




            var dataset = [
                {
                    label: "Entregas programadas",
                    data: data3,
                    color: "#1ab394",
                    bars: {
                        show: true,
                        align: "center",
                        barWidth: anchoBarra,
                        lineWidth:1
                    }

                }, {
                    label: "Entregas realizadas",
                    data: data2,
                    yaxis: 1,
                    color: "#1C84C6",
                    lines: {
                        lineWidth:1,
                            show: true,
                            fill: true,
                        fillColor: {
                            colors: [{
                                opacity: 0.2
                            }, {
                                opacity: 0.4
                            }]
                        }
                    },
                    splines: {
                        show: false,
                        tension: 0.6,
                        lineWidth: 1,
                        fill: 0.1
                    },
                }
            ];


         



         

            var previousPoint = null, previousLabel = null;

            $.plot($("#flot-dashboard-chart"), dataset, options);
}