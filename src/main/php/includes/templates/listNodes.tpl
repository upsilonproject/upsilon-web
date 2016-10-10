{if $tutorialMode}
<div class = "box tutorialMessage">
	<p><strong>Nodes</strong> are responsible for executing <a href = "index.php">service checks</a> and optionally sending those results their node peers.</p>
	<p style = "font-size: x-small " class = "subtle">This message is being shown because <a href = "preferences.php">tutorial mode</a> is enabled.</p>
</div>
{/if}

<div class = "box">
	<h2>Nodes</h2>

{if empty($listNodes)}
	<p>0 nodes in database.</p> 
	<p>Visit the wiki to understand how to <a href = "http://upsilon-project.co.uk/site/index.php/SetupNodeDatabase">configure your node to write to a database</a>.</p>
{else}
<table class = "hover dataTable">
	<thead>
		<tr>
			<th class = "media-width-prio-low">id</th>
			<th>Title</th>
			<th>Type</th>
			<th class = "media-width-prio-low">Service count</th>
			<th class = "media-width-prio-low">Last updated</th>
			<th>Status</th>
		</tr>
	</thead>

	<tbody>
		{foreach from = $listNodes item = itemNode}
		<tr>
			<td class = "media-width-prio-low"><a class = "node" href = "viewNode.php?id={$itemNode.id}">{$itemNode.id}</a></td>
			<td><a class = "node" href = "viewNode.php?id={$itemNode.id}">{$itemNode.identifier}</a></td>
			<td class = "{$itemNode.versionKarma|strtolower}">{$itemNode.nodeType} (version {$itemNode.instanceApplicationVersion})</td>
			<td class = "media-width-prio-low">{$itemNode.serviceCount}</td>
			<td class = "media-width-prio-low"><span class = "date">{$itemNode.lastUpdated}</span></td>
			<td class = "{$itemNode.karma|strtolower}">{$itemNode.karma}</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/if}
</div>
