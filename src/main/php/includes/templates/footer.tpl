</div>
<div id = "footer">
	<p>
		<strong>Version:</strong> <a href = "viewVersion.php">{$version}</a>
		&nbsp;&nbsp;&nbsp;&nbsp;
	{if !empty($apiClient)}
		<strong>API Client:</strong> {$apiClient}
	{else}
		<strong>Server time:</strong> <span class ="date">{$date}</span> 
		&nbsp;&nbsp;&nbsp;&nbsp;
		<strong>Client time:</strong> <span><span class = "date">now</span></span>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<strong>DB Queries:</strong> {$queryCount}
	{/if}
	</p>
	<p>
		<a href = "http://www.upsilonproject.io">Upsilon Project</a> &bull;
		<a href = "https://github.com/upsilonproject/upsilon/issues?">Raise Issue (bug, suggestion or feature request)</a> &bull;
		 <a href = "mailto:upsilonproject@googlegroups.com">Email the developers</a>
	</p>
</div>

<script type = "text/javascript">
setupSearchBox();
setupSortableTables();
setupEnhancedSelectBoxes();
</script>
<script type = "text/javascript">
{literal}
require(["dojo/parser", "dojo/query"], function(parser, query) {
	parser.parse();	
	query(".navigationMenuItems").style('display', 'block');

	query('.date').forEach(makeDateHumanReadable);

	{/literal}{if $apiClient}
	showFullscreenButton();
	{/if}{literal}

});
{/literal}

</script>

</body>
</html>
