{assign var = "excludeBox" value = false}

{if $excludeBox eq true}
	{if !empty($form->getTitle)}
		<h3>{$form->getTitle()}</h3>
	{/if}
{else}
<section class = "{if isset($form->containerClass)}{$form->containerClass}{else}box{/if}">
	<h2>{$form->getTitle()}</h2>
{/if}

	<!-- FORM:{$form->getName()} (rendered by template engine) !-->
	<form enctype = "{$form->getEnctype()}" id = "{$form->getName()}" action = "{$form->getAction()}" method = "post">
		{include file = "formElements.tpl" elements=$elements}

		{if isset($scripts)}
			{foreach from = $scripts item = script}
				<script type = "text/javascript">
				{$script}
				</script>
			{/foreach}
		{/if}
	</form>

{if not $excludeBox eq true}
</section>
{/if}
