Number.prototype.moneyfmt = function(c, d, t){
	var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

var dates = $('#from, #to').datepicker({
	defaultDate: "+1w",
	changeMonth: true,
	dateFormat: 'yy-mm-dd',
	onSelect: function(selectedDate) {
		var option = this.id == "from" ? "minDate" : "maxDate";
		var instance = $(this).data("datepicker");
		var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
		dates.not(this).datepicker("option", option, date);
	}
});
var mmToMonth = new Array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
function showLocalDate(timestamp)
{
  var dt = new Date(timestamp);
  var mm = mmToMonth[dt.getMonth()];
  return dt.getDate()+ "-"+ mm+ "-" +dt.getFullYear();
}
$(function(){
var options = {
	    lines:{show:true},
	    points:{show:true},
	    xaxis: {
		mode:'time',
		timeformat: "%d-%m-%y",
		monthNames : mmToMonth,
		ticks:5
		    

		},
	    yaxis: {
		    tickFormatter: function(val, axis){
				return moneysimbol+val.moneyfmt(0);
			},
            labelWidth: 80,
            backgroundColor:"#000",
            color:"#000",
            tickColor: "#000"
		},
	   	minTickSize: [1, "day"],
	   	grid: {
		    hoverable: true,
		    canvasText: {show: true, font:"sans 8px" },
            backgroundColor:"#fff"
		}

};
var legend = {
		show: true,
		position: "ne",
		backgroundOpacity: 0,
};
var data= [];
var placeholder = $("#canvas");


function split(val) {
	return val.split(/,\s*/);
}
function extractLast(term) {
	return split(term).pop();
}
function showTooltip(x, y, contents) {
	/*<![CDATA[*/
    $('<div id="tooltip">' + contents + '<\/div>').css( {
	/*]]>*/
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5,
        border: '1px solid #fdd',
        padding: '2px',
        'background-color': '#f4f4f4',
        opacity: 0.80
    }).appendTo("body").fadeIn(200);
}
$("input:checkbox").click(function(){
	var startdate = $("#from").val();
	var enddate = $("#to").val();
	var selected = [];
	var ui= $("input:checkbox:checked");
	$.each(ui,function(i,val){
	    selected.push(val.value);
	});
	selected = selected.toString();
	$.ajax({
	    url:jsonaddress ,
	    dataType:'json',
	    data:{
		"ac": selected,
		"startDate": startdate,
		"endDate": enddate
	    },
	    success:function(data){
		plot = $.plot(placeholder, data, options)

	    }
	});
	var previousPoint = null;
	placeholder.bind("plothover", function (event, pos, item) {
	    $("#x").text(pos.x.toFixed(2));
	    $("#y").text(pos.y.toFixed(2));
	        if (item) {
	            if (previousPoint != item.datapoint) {
	                previousPoint = item.datapoint;

	                $("#tooltip").remove();
	                var x = item.datapoint[0].toFixed(2),
	                    y = item.datapoint[1].toFixed(2);

	                showTooltip(item.pageX, item.pageY,
	                             moneysimbol+" "+item.datapoint[1].moneyfmt(4,'.',',') + " / " + showLocalDate(item.datapoint[0])
	                             );

	            }
	        }
	        else {
	            $("#tooltip").remove();
	            previousPoint = null;
	        }

	});
});





});
