<!DOCTYPE html>

<html>
<head>
	<title>{$siteTitle} &raquo; {$title|default:'Untitled page'}</title>

	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/main.css" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/mobile.css" title = "mobile" media = "(max-device-width: 480px)" />

	<link rel = "{if $isNighttime}stylesheet{/if}" type = "text/css" href = "resources/stylesheets/main-nighttime.css" title = "nighttime" />

	<link rel = "shortcut icon" href = "resources/images/icons/logo96pxdarkbg.png" title = "Shortcut icon" type = "image/png" />

	<meta name="viewport" content="width=device-width" />

	<script src="resources/javascript/masonry.js"></script>
	<script src="resources/dojo/dojo/dojo.js"></script>
	<script src="resources/javascript/hud.js"></script>

	<link rel="stylesheet" href="resources/dojo/dijit/themes/claro/claro.css" />

	</head>

<body class = "claro">
<div id = "header">
	{if $sessionOptions->drawHeader}
	<div class = "title">
		<h1>
			<a href = "index.php">{$siteTitle}</a>
			&raquo;
			<span class = "pageTitle">{$title|default:'Untitled page'}</span>
		</h1>
	</div>
	{/if}
</div>
	{if $sessionOptions->drawNavigation}
	<div class = "navigationMenuItems">
		<div>
		{if $generalLinks->hasLinks()}
			{include file = "links.tpl" links = $generalLinks skipTitle = true}
		{/if}
		</div>
	</div>
	{/if}

{if $isNighttime && $sessionOPtions->drawBigClock}<p style = "margin: 0; font-size:9em; font-weight: bold; background-color: white; color: black;">{$datetime}</p>{/if}
<div id = "content">

	
