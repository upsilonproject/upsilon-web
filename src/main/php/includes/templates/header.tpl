<!DOCTYPE html>

<html lang = "{$lang}">
<head>
	<title>{$pageTitle} &laquo; {$siteTitle}</title>

	<link rel="stylesheet" href="resources/dojo/dijit/themes/claro/claro.css" />

	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/main.css" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/phone-thin.css" title = "mobile" media = "(max-width: 366px)" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/phone-wide.css" title = "mobile" media = "(max-width: 400px)" />
	<link rel = "stylesheet" type = "text/css" href = "resources/stylesheets/tablet.css" title = "mobile" media = "(max-width: 400px)" />

	<link rel = "manifest" type = "manifest.json" />

	<link rel = "{if $isNighttime}stylesheet{/if}" type = "text/css" href = "resources/stylesheets/main-nighttime.css" title = "nighttime" />

	<link rel = "shortcut icon" href = "resources/images/icons/logo96pxdarkbg.webp" title = "Shortcut icon" type = "image/webp" />

	<meta name="viewport" content="width=device-width" />
	<meta name="theme-color" content="#000000" />
</head>

<body class = "claro">
	<!--
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
	!-->
	{if $sessionOptions->drawNavigation}
	<div id = "header">
		<div class = "navigationMenuItems">
		<table id = "headerStruct"><tr>
			<td class = "media-width-prio-low" rowspan = "2" style = "width: 1%; padding-right: 0em; padding-left: 1em;">
				<a href = "index.php">
					<img alt = "upsilon logo" src = "resources/images/icons/logo96pxdarkbg.webp" width = "24" style = "margin-right: 1em; margin-bottom: 4px; display: inline-block; vertical-align: middle;" />
				</a>
			</td>

			<td class = "headerBlock" style = "padding-left: .6em; padding-top: .2em; font-size: 120%; font-weight: bold;" colspan = "2">
				<span class = "media-width-prio-low">
				<a href = "index.php">{$siteTitle}</a>
				&raquo;
				</span>
				{$navTitle}
			</td>
		</tr><tr>
			<td class = "headerBlock">
				{include file = "links.tpl" links = $generalLinks skipTitle = true}
			</td>
			<td class = "headerBlock media-width-prio-low" style = "text-align: right">
				{include file = "links.tpl" links = $userLinks skipTitle = true}
			</div>
		</tr></table>
		</div>
	</div>
	{/if}

{if $isNighttime && $sessionOPtions->drawBigClock}<p style = "margin: 0; font-size:9em; font-weight: bold; background-color: white; color: black;">{$datetime}</p>{/if}
<div id = "content">

<script src="resources/dojo/dojo/dojo.js.uncompressed.js"></script>
<script src="resources/javascript/hud.js"></script>

