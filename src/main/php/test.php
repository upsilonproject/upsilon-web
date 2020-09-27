<?php require_once 'includes/widgets/header.php'; ?>

<div id = "testChart" style = "height: 300px">
</div>

<script type = "text/javascript">

require([
	"dojox/charting/plot2d/StackedAreas",

	], function(StackedAreas) {
class Pipeline extends StackedAreas {
	constructor(chart, kwArgs) {
		super(chart, kwArgs);

		this.hmin = 0;
	};
};

function drawPipeline() {
	require([
		"dojox/charting/Chart",
		"dojox/charting/themes/Claro",
		"dojox/charting/axis2d/Default",
	], function (Chart, theme) {
		c = new Chart("testChart", {
			title: "title"
		});

		c.setTheme(theme)
		c.addAxis("x");
		c.addAxis("y");
		c.addPlot("default", {
			type: Pipeline,
			markers: true
		});

		c.addSeries("triage", [9, 5, 13, 9, 15]);
		c.addSeries("action", [15, 8, 2, 4, 6]);

		c.render();
	});
}

drawPipeline();

});
</script>
