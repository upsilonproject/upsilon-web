<div class = "widgetRef{$ref}"> 
	<p class = "loading" style = "text-align: center"><img src = "resources/images/loading.gif" alt = "loading icon" /></p>
</div>

<script type = "text/Javascript">
	var qp = {$queryParams};
	request("{$url}", qp, {$callback}, "{$ref}", "{$repeat}");
</script>


