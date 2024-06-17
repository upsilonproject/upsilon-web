{if count($listUngroupedServices) > 0}
<p>
	<strong>Ungrouped: </strong>
{foreach from = $listUngroupedServices item = itemUngroupedService}
	<strong><a href = "viewService.php?id={$itemUngroupedService.id}">{$itemUngroupedService.identifier}</a></strong> ({$itemUngroupedService.id}) 
{/foreach}

	<a href = "addGroupMembership.php">Add to groups...</a>
</p>
{/if}
