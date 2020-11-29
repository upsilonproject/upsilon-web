{if $links->hasLinks()}
{if not isset($sub)}
<nav title = "{$links->getTitle()}">
<ul class = "menu" role = "menubar">
{else}
<ul class = "submenu">
{/if}
	{if not isset($skipTitle)}
		<h3>{$links->getTitle()}</h3>
	{/if}

	{foreach from = $links item = link}
		{if $link.children|count > 0}
			<li class = "hasSubmenu" role = "none">
				<a role = "menuitem" aria-expanded = "false" aria-haspopup = "true" {if not $link.enabled}disabled = "disabled"{/if} class = "menuItemLabel" href = "{$link.url}">{$link.title}</a>
				<div class = "dropdownContent">
						{include file = "links.tpl" links = $link.children skipTitle = true sub = true}
				</div>
			</li>
		{else if $link.separator}
			<span role = "separator" class = "menuSeparator"></span>
		{else}
			<li role = "none">
				<a role = "menuitem" {if not $link.enabled}disabled = "disabled"{/if} class = "menuItemLabel" href = "{$link.url}">{$link.title}</a>
			</li>
		{/if}
	{/foreach}
</ul>
{if not isset($sub)}
</nav>
{/if}
{/if}
