/* 
 * a.cadevall@insta.fr
 */
var nowTemp = new Date();
var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

var startDate = $('#startDate').datepicker({
	onRender: function(date) {
		return date.valueOf() < now.valueOf() ? '' : '';
	}
}).on('changeDate', function(ev) {
	if (ev.date.valueOf() > deadline.date.valueOf()) {
		var newDate = new Date(ev.date)
		newDate.setDate(newDate.getDate() + 1);
		deadline.setValue(newDate);
	}
	startDate.hide();
	$('#deadline')[0].focus();
}).data('datepicker');
var deadline = $('#deadline').datepicker({
	onRender: function(date) {
		return date.valueOf() <= startDate.date.valueOf() ? 'disabled' : '';
	}
}).on('changeDate', function(ev) {
	deadline.hide();
    var estimatedTilme = parseInt((deadline.date.valueOf()-startDate.date.valueOf())/(24*3600*1000));// deadline.date.valueOf() - startDate.date.valueOf();
    $('#estimatedTime').val(estimatedTilme).focus();
}).data('datepicker');
$("#startDateBtn").click(function(event) {
	startDate.show();
	deadline.hide();
	event.preventDefault();
});

$("#deadlineBtn").click(function(event) {
	deadline.show();
	startDate.hide();
	event.preventDefault();
});

