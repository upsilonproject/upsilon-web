	{if isset($filters)}
	{if not isset($filterCallback)}
		<strong>Filters: </strong>
		<form class = "inline filterTracker">
	{else}
		<div class = "filterTracker">
	{/if}
	{foreach from = $filters item = filter}
			<span id = "filterLabel-{$filter.name}" class = "{if $filter.isUsed}good{else}unknown{/if}">{$filter.label}</span>

			{if $filter.type == "hidden"}
				<input type = "hidden" id = "filterInput-{$filter.name}" name = "{$filter.name}" value = "{$filter.value}" />
			{/if}

			{if $filter.type == "bool"}
				<input onblur = "filteringSelectBlur()" id = "filterInput-{$filter.name}" type = "checkbox" {if $filter.isUsed}checked{/if} name = "{$filter.name}">
			{/if}

			{if $filter.type == "string" || $filter.type == "int"}
				<input onblur = "filteringSelectBlur()" id = "filterInput-{$filter.name}" name = "{$filter.name}" value = "{$filter.value}"></input>
			{/if}

			{if $filter.type == "select"}
				<select onblur = "filteringSelectBlur()" id = "filterInput-{$filter.name}" name = "{$filter.name}" data-dojo-type="dijit/form/FilteringSelect">
					<option value = "">-- Any node</option>
				{foreach from = $filter.options item = option}
					<option {if $filter.value == $option.name}selected{/if} value = "{$option.name}">{$option.name}</option>
				{/foreach}
				</select>
			{/if}

			&nbsp;&nbsp;&nbsp;&nbsp;
	{/foreach}

	<script type = "text/javascript">
	window.filters = [];

	{foreach from = $filters item = filter}
		window.filters.push('{$filter.name}');
	{/foreach}
	</script>

	{if isset($filterCallback)}
			<a href = "#" role = "button" onclick = "{$filterCallback}()">Update filter</a>
			<a href = "#" role = "button" onclick = "filteringSelectClear()">X</a>
		</div>
	{else}
			<button type = "submit">Update filter</button>
		</form>
		<br />
	<hr />
	{/if}
	{/if}
