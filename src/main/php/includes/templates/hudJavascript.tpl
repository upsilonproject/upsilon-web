
</div>
<div>
	<p>
		<span class = "control"><span class = "label">Toggle nighttime</span>: <span class = "keystroke">n</span></span>,
		<span class = "control"><span class = "label">Toggle good groups</span>: <span class = "keystroke">g</span></span>
	</p>

<script type = "text/javascript">
{literal}
require([
	"dojo/ready"
], function() {
	toggleEmptyGroups();
	toggleGroups();
	layoutBoxes(true);
	setInterval(layoutBoxes, 60000);
});
{/literal}
</script>

