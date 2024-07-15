<div class = "widgetRef{$ref}"> 
	<p class = "loading" style = "text-align: center"><img src = "resources/images/loading.gif" alt = "loading icon" /></p>
</div>

<script type = "text/Javascript">
	var qp = {$queryParams};
	var base = window.location.href.substring(0, window.location.href.lastIndexOf("/"))
	var url = base + "/{$url}"

	request(url, qp, {$callback}, "{$ref}", "{$repeat}");
</script>


