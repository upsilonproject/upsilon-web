</div>
<div id = "footer">
	<p>
		<strong>Crypto:</strong> <span class = "{if $crypto}good{else}bad{/if}">{if $crypto}on{else}off{/if}</span>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<strong>Version:</strong> <a href = "viewVersion.php">{$version}</a>
		&nbsp;&nbsp;&nbsp;&nbsp;
	{if !empty($apiClient)}
		<strong>API Client:</strong> {$apiClient}
	{else}
		<strong>Time now:</strong> {$date}
		&nbsp;&nbsp;&nbsp;&nbsp;
		<strong>DB Queries:</strong> {$queryCount}
	{/if}
	</p>
	<p>
		<a href = "http://upsilon-project.co.uk">Upsilon Project</a> &bull;
		<a href = "https://github.com/upsilonproject/upsilon/issues?">Raise Issue (bug, suggestion or feature request)</a> &bull;
		 <a href = "mailto:upsilonproject@googlegroups.com">Email the developers</a>
	</p>
</div>

<script type = "text/javascript">
setupSortableTables();
setupEnhancedSelectBoxes();
</script>
<script type = "text/javascript">
{literal}
require(["dojo/parser", "dojo/query"], function(parser, query) {
	parser.parse();	
	query(".navigationMenuItems").style('display', 'block');

	query('.date').forEach(makeDateHumanReadable);
});
{/literal}
</script>

</body>
</html>
