$(document).ready(function(){
	getData(0);
	$('#date').change(function(){
		var thisVal = $(this).val();
		if(thisVal!=0){
			getData(thisVal);		
		}
	});
});

function getData(date){
	ajaxCall( 'api.php', GenerateChart, { mod:'chart_daily', type:'get_daily_value', date:date });
}
function GenerateChart(response){
	var massage		= [];
	var sauna		= [];
	var restaurant	= [];
	var snooker		= [];
	for(var i=0; i<=23; i++){
		/*if(i > 0){
			massage = massage+',';
			sauna	= sauna+',';
			restaurant = restaurant+',';
			snooker = snooker+',';
		}*/
		massage.push(parseInt(response.massage[i]));
		sauna.push(parseInt(response.sauna[i]));
		restaurant.push(parseInt(response.restaurant[i]));
		snooker.push(parseInt(response.snooker[i]));

		/*massage		= massage+response.massage;
		sauna		= sauna+response.sauna;
		restaurant	= restaurant+response.restaurant;
		snooker		= snooker+response.snooker;*/
	}
	console.log(restaurant);
    $('#chart').highcharts({
        title: {
            text: 'Value on '+response.date,
            x: -20 //center
        },
        xAxis: {
            categories: ['10AM', '11AM', '12AM', '1PM', '2PM', '3PM',
                '4PM', '5PM', '6PM', '7PM', '8PM', '9PM', '10PM', '11PM', '12PM', '1AM',
                '2AM', '3AM', '4AM', '5AM', '6AM', '7AM', '8AM', '9AM']
        },
        yAxis: {
            title: {
                text: 'THB (฿)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: 'THB(฿)'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'MASSAGE',
            data: massage
        }, {
            name: 'SAUNA & FITNESS',
            data: sauna
        }, {
            name: 'RESTAURANT',
            data: restaurant
        }, {
            name: 'SNOOKER',
            data: snooker
        }]
    });
}