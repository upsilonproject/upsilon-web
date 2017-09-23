<!DOCTYPE html>

<html lang = "{$lang}">
<head>
	<title>{$pageTitle} &laquo; {$siteTitle}</title>

	<link rel="stylesheet" href="resources/dojo/dijit/themes/claro/claro.css" />

	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/main.css" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/phone-thin.css" title = "mobile" media = "(max-width: 300px)" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/phone-wide.css" title = "mobile" media = "(max-width: 400px)" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/tablet.css" title = "mobile" media = "(max-width: 400px)" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/driveway.min.css" />

	<link rel = "{if $isNighttime}stylesheet{/if}" type = "text/css" href = "resources/stylesheets/main-nighttime.css" title = "nighttime" />

	<link rel = "shortcut icon" href = "resources/images/icons/logo96pxdarkbg.webp" title = "Shortcut icon" type = "image/webp" />

	<meta name="viewport" content="width=device-width" />
	<meta name="theme-color" content="#000000" />

	<script src="resources/dojo/dojo/dojo.js"></script>
	<script src="resources/javascript/hud.js"></script>
</head>

<body class = "claro">
<div id = "header">
	{if $sessionOptions->drawHeader}
	<div class = "title">
		<img alt = "upsilon logo" src = "resources/images/icons/logo96pxdarkbg.webp" width = "16" style = "margin-right: 1em; margin-bottom: 4px; display: inline-block; vertical-align: middle;" />
		<h1 style = "display: inline-block">
			<a href = "index.php">{$siteTitle}</a>
			&raquo;
			<span class = "navTitle">{$navTitle}</span>
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

	
