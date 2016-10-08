	{if isset($filters)}
		<strong>Filters: </strong>
		<form class = "inline">
	{foreach from = $filters item = filter}
			<span class = "{if $filter.isUsed}good{else}unknown{/if}">{$filter.label}</span>

			{if $filter.type == "bool"}
				<input type = "checkbox" {if $filter.isUsed}checked{/if} name = "{$filter.name}">
			{/if}

			{if $filter.type == "string" || $filter.type == "int"}
				<input name = "{$filter.name}" value = "{$filter.value}"></input>
			{/if}

			{if $filter.type == "select"}
				<select name = "{$filter.name}">
					<option value = "">-- Any node</option>
				{foreach from = $filter.options item = option}
					<option {if $filter.value == $option.name}selected{/if} value = "{$option.name}">{$option.name}</option>
				{/foreach}
				</select>
			{/if}

			&nbsp;&nbsp;&nbsp;&nbsp;
	{/foreach}
			<button type = "submit">Update filter</button>
		</form>
		<br />
	<hr />
	{/if}
