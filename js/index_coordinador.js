
$(document).ready(function(){
    var codSede = $('#codSede').val();
	buscarCantidadesGraficos(0,1,codSede);
    $('#btnActualizarGrafica').click(function(){
        actualizarGraficas();
    });

    $('.timeOption').click(function(){
        $('.timeOption').removeClass('active');
        $(this).addClass('active');
        var aux = $('.timeOption.active').attr('value');
        buscarCantidadesGraficos(0,aux,codSede);  
    });	
});

function actualizarGraficas(){
    var codSede = $('#codSede').val();
    // console.log(codSede);
    var aux = $('.timeOption.active').attr('value');
    buscarCantidadesGraficos(1,aux,codSede);
}

function buscarCantidadesGraficos(actualizar,timeOption,codSede){
    var datos = {"actualizar":actualizar,"timeOption":timeOption,"codSede":codSede};
    $.ajax({
        type: "POST",
        data: datos,
        url: "functions/fn_datos_graficos_index_coordinador.php",
        beforeSend: function(){
          $('#loader').fadeIn();    
        },
        success: function(data){
            console.log(data);
            try {
                var obj = JSON.parse(data);
                // console.log(obj.barras);
                // console.log(obj.entregas);
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
    .done(function(){ })
    .fail(function(){ })
    .always(function(){
        $('#loader').fadeOut();  
    });
}

function graficar(data2,data3,timeOption,labels){
    // console.log('Cambios en el dibujo '+timeOption);
    if(timeOption == 1){      
        var tickArray = labels;      
        var anchoBarra = 1; 
        var options = {
        xaxis: {
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
                }, 
                {
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
                }, 
                {
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
                }, 
                {
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