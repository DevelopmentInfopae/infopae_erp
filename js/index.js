$(document).ready(function() {
    buscarCantidadesGraficos(0,1);
    $('#btnActualizarGrafica').click(function(){ actualizarGraficas(); });
    $('.timeOption').click(function(){
        $('.timeOption').removeClass('active');
        $(this).addClass('active');
        var aux = $('.timeOption.active').attr('value');
        buscarCantidadesGraficos(0,aux);
    });

    buscarCantidadesGraficos2(0,1);
    $('#btnActualizarGrafica2').click(function(){ actualizarGraficas2(); });
    $('.timeOption').click(function(){
        $('.timeOption').removeClass('active');
        $(this).addClass('active');
        var aux = $('.timeOption.active').attr('value');
        buscarCantidadesGraficos2(0,aux);
    });
    buscarCatidadesGraficos3();
});

// Linea
var data2 = [[gd(2017, 01, 01), 5], [gd(2017, 01, 23), 20]];
var data3 = [[gd(2017, 1, 1), 800], [gd(2017, 1, 2), 500]];

function gd(year, month, day) {
	return new Date(year, month - 1, day).getTime();
}

function actualizarGraficas() {
    var aux = $('.timeOption.active').attr('value');
    buscarCantidadesGraficos(1,aux);
}

function actualizarGraficas2() {
    // var aux = $('.timeOption.active').attr('value');
    buscarCantidadesGraficos2(1);
}

function buscarCantidadesGraficos(actualizar,timeOption){
    // El parametro actualizar cuando es 0 solo lle el archivo arrays.txt
    // cuando es igual a 1 actualiza el contenido del archivo.
	$.ajax({
		type: "POST",
        data: {
            "actualizar":actualizar,
            "timeOption":timeOption
        },
		url: "functions/fn_datos_graficos_index.php",
		beforeSend: function(){ $('#loader').fadeIn(); },
		success: function(data){
			try {
				var obj = JSON.parse(data);

                $('#listaTotales').html(obj.totales);
                graficar(obj.entregas,obj.barras,timeOption,obj.labels);
			}
			catch(err) {
				$('.debug').html(err.message);
				$('.debug').append('<br/><br/>');
				$('.debug').append(data);
			}
		}
	})
	.always(function(){
        $('#loader').fadeOut();
    	// console.log('Terminó el Ajax');
	});
}

function buscarCantidadesGraficos2(actualizar){
    // El parametro actualizar cuando es 0 solo lle el archivo arrays.txt
    // cuando es igual a 1 actualiza el contenido del archivo.
    $.ajax({
        type: "POST",
        data: {
            "actualizar":actualizar
        },
        url: "functions/fn_datos_graficos2_index.php",
        beforeSend: function(){ $('#loader').fadeIn(); },
        success: function(data){
            try {
                var obj2 = JSON.parse(data);
                $('#listaTotales2').html(obj2.totales);

                graficar2(obj2.entregas,obj2.barras,obj2.labels);
                // console.log(obj.entregas);
            }
            catch(err) {
                $('.debug2').html(err.message);
                $('.debug2').append('<br/><br/>');
                $('.debug2').append(data);
            }
        }
    })
    .always(function(){
        $('#loader').fadeOut();
        // console.log('Terminó el Ajax');
    });
}

function buscarCatidadesGraficos3(){
    $.ajax({
        url: 'functions/fn_datos_grafica3_index.php',
        type: 'POST'
    })
    .done(function(data) {
        console.log(data);
        var obj3 = JSON.parse(data);
        graficar3(obj3.ejecutado, obj3.porEjecutar);
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        // console.log("complete");
    });
    
}

function graficar(data2,data3,timeOption,labels){
            // console.log('Cambios en el dibujo '+timeOption);
            if(timeOption == 1){

                var tickArray = labels;
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
                        color: "#d5d5d5",
                        ticks: $(tickArray).filter(function(i){return this[2] == true})
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
            }else{
                var tickArray = [
                    [1, "Enero", true],
                    [2, "Febrero", true],
                    [3, "Marzo", true],
                    [4, "Abril", true],
                    [5, "Mayo", true],
                    [6, "Junio", true],
                    [7, "Julio", true],
                    [8, "Agosto", true],
                    [9, "Septiembre", true],
                    [10, "Octubre", true],
                    [11, "Noviembre", true],
                    [12, "Diciembre", true]
                ];
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
                    color: "#d5d5d5",
                    ticks: $(tickArray).filter(function(i){return this[2] == true})
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
                    label: "Entregas contratadas",
                    data: data3,
                    color: "#1ab394",
                    bars: {
                        show: true,
                        align: "center",
                        barWidth: anchoBarra,
                        lineWidth:1
                    }

                }, {
                    label: "Entregas ejecutadas",
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


function graficar2(data2,data3,labels){ 
    var lineData = {
        labels: labels,
        datasets: [
            {
                label: 'valor $',
                backgroundColor: 'rgba(26,179,148,0.5)',
                borderColor: "rgba(26,179,148,0.7)",
                pointBackgroundColor: "rgba(28,132,198,1)",
                pointBorderColor: "#fff",
                data: data2
            }
        ]
    };

    var lineOptions = {
        responsive: true
    };


    var ctx = document.getElementById("lineChart").getContext("2d");
    new Chart(ctx, {type: 'line', data: lineData, options:lineOptions});

}

function graficar3(ejecutado, porEjecutar){
    var doughnutData = {
        labels: ["% Ejecutado","% Por Ejecutar" ],
        datasets: [{
            data: [ejecutado, porEjecutar],
            backgroundColor: ["#a3e1d4","#dedede"]
        }]
    } ;


    var doughnutOptions = {
        responsive: true
    };


    var ctx4 = document.getElementById("doughnutChart").getContext("2d");
    new Chart(ctx4, {type: 'doughnut', data: doughnutData, options:doughnutOptions});
}
