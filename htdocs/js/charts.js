(function($, undefined){

$(function(){
    $('.distribution-test-chart').each(function() {
        var $this = $(this),
            categories = [],
            series = { 
                duration: [],
                memory: []
            };
        
        $.each($this.data('series') || {}, function(k, v) {
            $.each(v, function(dk, dv) {
                if (k == "duration") {
                    categories.push(dk);
                }
                series[k].push(dv);
            });
        });
        
        chart = new Highcharts.Chart({
            chart: {
                renderTo: this
            },
            credits: {
                enabled: false
            },
            title: {
                text: $this.data('title')
            },
            subtitle: {
                text: $this.data('subtitle')
            },
            zoom: "xy",
            xAxis: [{
                categories: categories,
                title: {
                    text: 'Factor'
                },
                min: 0
            }],
            yAxis: [{ // Primary yAxis
                title: {
                    text: 'Duration',
                    style: {
                        color: '#89A54E'
                    }
                },
                labels: {
                    formatter: function() {
                        return this.value + 's';
                    },
                    style: {
                        color: '#89A54E'
                    }
                },
                min: 0
            }, { // Secondary yAxis
                title: {
                    text: 'Memory',
                    style: {
                        color: '#4572A7'
                    }
                },
                labels: {
                    formatter: function() {
                        return this.value +'MB';
                    },
                    style: {
                        color: '#4572A7'
                    }
                },
                min: 0,
                opposite: true
            }],
            tooltip: {
                formatter: function() {
                    var text = '<b>At Factor ' + this.x + "</b>:";
                    $.each(this.points, function(i, point){
                        text += '<br />' + point.series.name + ': ' 
                            + this.y.toFixed(4) 
                            + (point.series.name == 'Memory' ? 'MB' : 's');
                    });
                    
                    return text;
                },
                shared: true
            },
            legend: {
                enabled: false
            },
            series: [{
                name: 'Memory',
                color: '#4572A7',
                type: 'spline',
                yAxis: 1,
                data: series.memory

            }, {
                name: 'Duration',
                color: '#89A54E',
                type: 'spline',
                data: series.duration
            }]
       });
       
    });
    $('.test-chart').each(function() {
        var $this = $(this),
            categories = [],
            _type = $this.data('type') || "spline",
            yaxis = {
                name: $this.data('axisName'),
                abbr: $this.data('axisAbbr')
            },
            series = [];

        var first = true;
        $.each($this.data('series') || {}, function(k, v) {
            var data = [];
            $.each(v, function(dk, dv) {
                if (first) {
                    categories.push(dk);
                }
                data.push(dv);
            });

            series.push({
                name: k,
                type: _type,
                data: data
            });

            first = false;
        });
        
        chart = new Highcharts.Chart({
            chart: {
                renderTo: this
            },
            credits: {
                enabled: false
            },
            title: {
                text: $this.data('title')
            },
            subtitle: {
                text: $this.data('subtitle')
            },
            xAxis: [{
                categories: categories,
                title: {
                    text: 'Factor'
                }
            }],
            yAxis: { // Secondary yAxis
                title: {
                    text: yaxis.name
                },
                labels: {
                    formatter: function() {
                        return this.value + yaxis.abbr;
                    }
                },
                min: 0
            },
            tooltip: {
                formatter: function() {
                    var text = '<b>At Factor ' + this.x + "</b>:"
                        + '<br />' + this.series.name + ': ' 
                        + this.y.toFixed(4) 
                        + (yaxis.name == 'Memory' ? 'MB' : 's');
                        
                    return text;
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'center',
                y: 30
                
            },
            series: series
       });
       
    });

});

})(jQuery);