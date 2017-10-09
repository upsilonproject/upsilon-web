{if $links->hasLinks()}
{if not empty($sub)}
<div data-dojo-type = "dijit/DropDownMenu">
{else}
<div data-dojo-type = "dijit/MenuBar" data-dojo-props = "passivePopupDelay: 1, popupDelay: 1">
{/if}
	{if not isset($skipTitle)}
		<div data-dojo-type = "dijit/MenuItem">
			{$links->getTitle()}
		</div>
	{/if}

	{foreach from = $links item = link}
		
		{if count($link.children) > 0}
			<div {if not $link.enabled}disabled = "disabled"{/if} data-dojo-type = "dijit/PopupMenu{if empty($sub)}Bar{/if}Item">
				<span>{$link.title}</span>

				{include file = "links.tpl" links = $link.children skipTitle = true sub = true}
			</div>
		{else}	
			{if $link.separator}
				<span data-dojo-type = "dijit/MenuSeparator"></span>
			{else}
				{if not empty($sub)}
				<div {if not $link.enabled}disabled = "disabled"{/if} data-dojo-type = "dijit/MenuItem" data-dojo-props = "onClick: function() {literal}{{/literal} menuButtonClick('{$link.url}'){literal}}{/literal}" >
				{else}
					<div {if not $link.enabled}disabled = "disabled"{/if} data-dojo-type = "dijit/MenuBarItem" data-dojo-props = "onClick: function() {literal}{{/literal} menuButtonClick('{$link.url}'){literal}}{/literal}" >
				{/if}

				<span>{$link.title}</span>
				</div>
			{/if}
		{/if}
	{/foreach}
</div>
{/if}
