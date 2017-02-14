<p>
	<img src = "resources/images/serviceIcons/{$classInstance.icon}" class = "inlineIcon" />
	<a href = "viewClassInstance.php?id={$classInstance.id}">{$classInstance.title}</a>
</p>

<table>
{foreach from = $listRequirements item = itemRequirement}
	<tr>
		<td>
			{if isset($itemRequirement.icon)}
			<img src = "resources/images/serviceIcons/{$itemRequirement.icon}" />
			{/if}
			<span class = "metricIndicator {$itemRequirement.karma|strtolower}">&nbsp;</span> <a href = "viewService.php?id={$itemRequirement.service}">{$itemRequirement.identifier}</a>
		</td>
		<td><div class = "date">{$itemRequirement.serviceLastUpdated}</div></td>
	</tr>
{/foreach}
</table>
