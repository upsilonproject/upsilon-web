{if !isset($messageClass)}
{assign var = "messageClass" value = ""}
{/if}
<div class = "{$messageClass|default}">
	<h3>{$messageTitle|default:"Message"}</h3>
	<p>{$message}</p>
</div>
