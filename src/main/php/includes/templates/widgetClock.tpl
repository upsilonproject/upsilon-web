<br /><div style = "text-align: center">
	<div id = "clock" style = "font-size: x-large; text-align: center;">CLOCK</div>
	<p class = "subtle">Client side time</p>
</div>

<script type = "text/javascript">
	{literal}
	function clockWidgetTick() {
		var now = new Date();
		var txt = now.toISOString().substr(11, 8);

		document.querySelector('#clock').innerHTML = txt;

		setTimeout(() => { clockWidgetTick() }, 1000);
	}

	clockWidgetTick();
	{/literal}
</script>
