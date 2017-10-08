
<div>
{foreach from = $elements item = element}
	{if is_array($element)}
		{include file = "formElements.tpl" elements=$element}
	{else}
		{if $element->getType() eq 'ElementHidden'}
			<input type = "hidden" name = "{$element->getName()}" value = "{$element->getValue()}" />
		{elseif $element->getType() eq 'ElementHtml'}
			{$element->getValue()}
		{elseif $element->getType() eq 'submit'}
			<button value = "{$form->getName()}" name = "{$element->getName()}" type = "submit">{$element->getCaption()}</button>
		{else}
			<fieldset>				
				{$element->render()}

				{if $element->description ne ''}
				<p class = "description">{$element->description}</p>
				{/if}

				{if $element->getValidationError() ne ''}
				<br />
				<div class = "labelHolder" style = "margin-left: .8em">&nbsp;</div>
				<div class = "elementHolder">
					<p class = "formValidationError">{$element->getValidationError()}</p>
				</div>
				{/if}
			</fieldset>
		{/if}
	{/if}
{/foreach}

</div>
