
{if $tutorialMode}
<div class = "box tutorialMessage">
	<p><strong>Dashboards</strong> are useful for grouping up lots of information in to one easy to view place. <strong>Widgets</strong> on dashboards are responsible for showing information.</p>
	<p style = "font-size: x-small " class = "subtle">This message is being shown because <a href = "preferences.php">tutorial mode</a> is enabled.</p>
</div>
{/if}

<div class = "grid gc-xl">
{if empty($listInstances)}
	<p>This dashboard is empty. Select <strong>Actions</strong> &raquo; <strong>Add Widget</strong> from the Dashboard menu.</p>
{else}
		{foreach from = $listInstances item = widget}
			{if $widget.instance->isShown()}
			<section>
				<div class = "blockHeader">
				<h2><a href = "{$widget.instance->getHeaderLink()}">{$widget.instance->getTitle()}</a></h2>
				{if $sessionOptions->drawNavigation}
				<ul role = "menubar">
					<li>
					<span class = "menuItemLabel">&#9881;</span>
					<div class = "dropdownContent">
						{include file = "links.tpl" links = $widget.instance->getLinks() skipTitle = true sub = true}
						</div>
					</li>
				</ul>
				{/if}
				</div>

				{$widget.instance->render()}
			</section>
			{/if}
		{/foreach}
{/if}
</div>

{if !empty($hiddenWidgets)}
<h3>Hidden Widgets</h3>
{foreach from = $hiddenWidgets item = itemWidget}
	<a href = "updateWidgetInstance.php?id={$itemWidget.id}">{$itemWidget.instance->getTitle()}</a> (<a href = "deleteWidgetInstance.php?id={$itemWidget.id}">X</a>) . 
{/foreach}
{/if}

<script type = "text/javascript">
{literal}
onDomReady(() => {
	{/literal}
	{if $itemDashboard->isServicesGrouped()}
	toggleGroups();
	{/if}
	{literal}
});
{/literal}
</script>

