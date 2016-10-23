<div class = "box">
	<h2>Service: {$service.name}</h2>

	<p><strong>Service ID:</strong> {$service.id}</p>

	<h3>Used by Nodes</h3>
	<ul>
	{foreach from = $listNodes item = node}
		<li><a href = "viewNode.php?id={$node.id}">{$node.identifier}</a></li>
	{/foreach}
	</ul>
</div>
