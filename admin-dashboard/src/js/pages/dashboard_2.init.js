/**
* Theme:  Zircos - Responsive Bootstrap 4 Admin & Dashboard
* Author: Coderthemes
* File:   Dashboard 2
*/

! function($) {
	"use strict";

	var dashboard_2 = function() {
		this.$body = $("body")
		this.$realData = []
	};

	//creates plot graph
	dashboard_2.prototype.createPlotGraph = function(selector, data1, data2, data3, labels, colors, borderColor, bgColor) {
		//shows tooltip
		function showTooltip(x, y, contents) {
			$('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css({
				position : 'absolute',
				top : y + 5,
				left : x + 5
			}).appendTo("body").fadeIn(200);
		}


		$.plot($(selector), [{
			data : data1,
			label : labels[0],
			color : colors[0]
		}, {
			data : data2,
			label : labels[1],
			color : colors[1]
		},{
			data : data3,
			label : labels[2],
			color : colors[2]
		}], {
			series : {
				lines : {
					show : true,
					fill : true,
					lineWidth : 2,
					fillColor : {
						colors : [{
							opacity : 0
						}, {
							opacity : 0.5
						},{
							opacity : 0.6
						}]
					}
				},
				points : {
					show : false
				},
				shadowSize : 0
			},

			grid : {
				hoverable : true,
				clickable : true,
				borderColor : borderColor,
				tickColor : "#f9f9f9",
				borderWidth : 1,
				labelMargin : 10,
				backgroundColor : bgColor
			},
			legend : {
				position : "ne",
				margin : [0, -24],
				noColumns : 0,
				backgroundColor: "transparent",
				labelBoxBorderColor : null,
				labelFormatter : function(label, series) {
					// just add some space to labes
					return '' + label + '&nbsp;&nbsp;';
				},
				width : 30,
				height : 2
			},
			yaxis : {
				axisLabel: "Daily Visits",
				tickColor : 'rgba(108, 120, 151, 0.1)',
				font : {
					color : '#6c7897'
				}
			},
			xaxis : {
				axisLabel: "Last Days",
				tickColor : 'rgba(108, 120, 151, 0.1)',
				font : {
					color : '#6c7897'
				}
			},
			tooltip : true,
			tooltipOpts : {
				content : '%s: Value of %x is %y',
				shifts : {
					x : -60,
					y : 25
				},
				defaultTheme : false
			}
		});
	},
	//end plot graph




	//creates Donut Chart
	dashboard_2.prototype.createDonutGraph = function(selector, labels, datas, colors) {
		var data = [{
			label : labels[0],
			data : datas[0]
		}, {
			label : labels[1],
			data : datas[1]
		}, {
			label : labels[2],
			data : datas[2]
		}, {
			label : labels[3],
			data : datas[3]
		}];
		var options = {
			series : {
				pie : {
					show : true,
					innerRadius : 0.7
				}
			},
			legend : {
				show : true,
				labelFormatter : function(label, series) {
					return '<div style="font-size:14px;">&nbsp;' + label + '</div>'
				},
				labelBoxBorderColor : null,
				margin : 50,
				width : 20,
				padding : 1,
				backgroundColor: "transparent",
			},
			grid : {
				hoverable : true,
				clickable : true
			},
			colors : colors,
			tooltip : true,
			tooltipOpts : {
				content : "%s, %p.0%"
			}
		};

		$.plot($(selector), data, options);
    },


	//initializing various charts and components
	dashboard_2.prototype.init = function() {
		//plot graph data
		var uploads = [[0, 5], [1, 8], [2, 10], [3, 12], [4, 9], [5, 5], [6, 7],[7, 9], [8, 8], [9, 16], [10, 14], [11, 12], [12, 10]];
		var downloads = [[0, 2], [1, 4], [2, 7], [3, 9], [4, 6], [5, 3], [6, 10],[7, 8], [8, 5], [9, 14], [10, 10], [11, 10], [12, 8]];
		var downloads1 = [[0, 1], [1, 3], [2, 6], [3, 7], [4, 4], [5, 2], [6, 8],[7, 6], [8, 4], [9, 10], [10, 8], [11, 14], [12, 5]];
		var plabels = ["Google", "Yahoo","Facebbok"];
		var pcolors = ['#4bd396', '#f5707a','#188ae2'];
		var borderColor = 'rgba(108, 120, 151, 0.1)';
		var bgColor = 'transparent';
		this.createPlotGraph("#website-stats", uploads, downloads, downloads1, plabels, pcolors, borderColor, bgColor);


		//Donut pie graph data
		var donutlabels = ["Series 1", "Series 2", "Series 3", "Series 4"];
		var donutdatas = [35, 20, 10, 20];
		var donutcolors = ['#ff9800', '#8d6e63', "#26a69a", "#7fc1fc"];
		this.createDonutGraph("#donut-chart #donut-chart-container", donutlabels, donutdatas, donutcolors);
	},

	//init dashboard_2
	$.dashboard_2 = new dashboard_2, $.dashboard_2.Constructor =
	dashboard_2

}(window.jQuery),

//initializing dashboard_2
function($) {
	"use strict";
	$.dashboard_2.init()
}(window.jQuery);


$('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
$('#reportrange').daterangepicker({
    format: 'MM/DD/YYYY',
    startDate: moment().subtract(29, 'days'),
    endDate: moment(),
    minDate: '01/01/2012',
    maxDate: '12/31/2025',
    dateLimit: {
        days: 60
    },
    showDropdowns: true,
    showWeekNumbers: true,
    timePicker: false,
    timePickerIncrement: 1,
    timePicker12Hour: true,
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    opens: 'left',
    drops: 'down',
    buttonClasses: ['btn', 'btn-sm'],
    applyClass: 'btn-success',
    cancelClass: 'btn-default',
    separator: ' to ',
    locale: {
        applyLabel: 'Submit',
        cancelLabel: 'Cancel',
        fromLabel: 'From',
        toLabel: 'To',
        customRangeLabel: 'Custom',
        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        firstDay: 1
    }
}, function (start, end, label) {
    console.log(start.toISOString(), end.toISOString(), label);
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
});