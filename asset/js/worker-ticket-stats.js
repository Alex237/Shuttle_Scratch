var totalChart = document.getElementById("totalChart").getContext("2d");
var dataTotal = [
	{
		value: 30,
		color:"#F7464A"
	},
	{
		value : 50,
		color : "#E2EAE9"
	},
	{
		value : 100,
		color : "#D4CCC5"
	},
	{
		value : 40,
		color : "#949FB1"
	},
	{
		value : 120,
		color : "#4D5360"
	}

];
var totalChart = new Chart(totalChart).Doughnut(dataTotal);


