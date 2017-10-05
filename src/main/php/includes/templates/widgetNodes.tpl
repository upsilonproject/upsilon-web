{if empty($listNodes)}
	<p>0 nodes in database.</p> 
	<p>Visit the wiki to understand how to <a href = "http://upsilon-project.co.uk/site/index.php/SetupNodeDatabase">configure your node to write to a database</a>.</p>
{else}
	<table class = "hover">
	<tr>
		<th>Name</th>
		<th>Last Updated</th>
	</tr>
{foreach from = $listNodes item = node} 
	<tr>
		<td>
		{if $sessionOptions->drawNavigation}<a href = "viewNode.php?id={$node.id}">{/if}
		{$node.identifier}
		{if $sessionOptions->drawNavigation}</a>{/if}

		<span class = "subtle">({$node.serviceType})</span>

		</td>
		<td>
			<span class = "date">{$node.lastUpdated}</span>
		</td>
	</tr>
{/foreach}
	</table>
{/if}
