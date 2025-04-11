<!DOCTYPE html>

<html lang = "{$lang}">
<head>
	<title>{$pageTitle} &laquo; {$siteTitle}</title>

	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/brightandsimple/style.css" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/extra.css" />

	<link rel = "manifest" type = "manifest.json" />

	<link rel = "{if $isNighttime}stylesheet{/if}" type = "text/css" href = "resources/stylesheets/main-nighttime.css" title = "nighttime" />

	<link rel = "shortcut icon" href = "resources/images/icons/logo96pxdarkbg.webp" title = "Shortcut icon" type = "image/webp" />

	<meta name="viewport" content="width=device-width" />
	<meta name="theme-color" content="#000000" />
</head>

<body>
	{if $sessionOptions->drawNavigation}
	<header title = "header" class = "grid-display">
		<a href = "#content" class = "a11yhidden">Skip to content</a>

		<a id = "headerLogo" href = "index.php">
			<img alt = "upsilon logo" src = "resources/images/icons/logo96pxdarkbg.webp" width = "24" id = "logo" />
		</a>

		<h1><a href = "index.php">{$siteTitle}</a>
			&raquo; {$navTitle}
		</h1>
		
		<div title = "search box" id = "searchBox">
			<input placeholder = "Search">
		</div>

		<div id = "generalLinks" role = "none">
			{include file = "links.tpl" links = $generalLinks skipTitle = true}
		</div>

		<div id = "adminLinks" role = "none">
			{include file = "links.tpl" links = $userLinks skipTitle = true}
		</div>
	</header>
	{/if}

{if $isNighttime && $sessionOPtions->drawBigClock}<p style = "margin: 0; font-size:9em; font-weight: bold; background-color: white; color: black;">{$datetime}</p>{/if}
<main title = "content" id = "content">

<script src="resources/javascript/modules.js" type = "module"></script>
<script src="resources/javascript/hud.js"></script>


