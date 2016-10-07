window.karmaColors = {}
window.karmaColors["GOOD"] = '#90ee90';
window.karmaColors["BAD"] =  '#fa8072';
window.karmaColors["STALLED"] = '#000099';
window.karmaColors["WARNING"] = '#ffa500';
window.karmaColors["UNKNOWN"] = '#666';

function pad(number) {
	if (number < 10) {
		return '0' + number;
	}

	return number;
}


function toLocalIsoLikeString(d) {
	return d.getFullYear() + 
	'-' + pad(d.getMonth()) +
	'-' + pad(d.getDate()) +
	' ' + pad(d.getHours()) +
	':' + pad(d.getMinutes()) +
	' (local)';
}

function makeDateHumanReadable(element) {
	utcDate = new Date(element.textContent + " UTC")
	elementUnixTimestamp = utcDate / 1000
	nowUnixTimestamp = Date.now() / 1000

	if (isNaN(elementUnixTimestamp)) {
		return;
	}


	if ((nowUnixTimestamp - elementUnixTimestamp) > 3600) {
		dojo.addClass(element.parentElement, "old");
	} else {
		dojo.addClass(element.parentElement, "good");
	}



	if (dojo.hasClass(element, "relative")) {
	description = "<strong>" + toLocalIsoLikeString(utcDate) + "</strong>";
		element.textContent = secondsToString(nowUnixTimestamp - elementUnixTimestamp);
	} else {
		description = "<strong>" + secondsToString(nowUnixTimestamp - elementUnixTimestamp) + "</strong>";
		element.textContent = toLocalIsoLikeString(utcDate);
	}

	description += "<br />Original: " + utcDate.toString()

	dojo.addClass(element, "tooltip")

	new dijit.Tooltip({
		connectId: element,
		label: description,
		showDelay: 0
	});
}

function secondsToString(seconds) {
	seconds = Math.round(seconds)

	var numyears = Math.floor(seconds / 31536000);
	var numdays = Math.floor((seconds % 31536000) / 86400); 
	var numhours = Math.floor(((seconds % 31536000) % 86400) / 3600);
	var numminutes = Math.floor((((seconds % 31536000) % 86400) % 3600) / 60);
	var numseconds = (((seconds % 31536000) % 86400) % 3600) % 60;

	ret = "";

	if (numyears > 0) {
		ret += numyears + " years "
	}

	if (numdays > 0) {
		ret += numdays + " days "
	}

	if (numhours > 0) {
		ret += numhours + " hours "
	}

	if (numminutes > 0) {
		ret += numminutes + " minutes "
	}

	ret += numseconds + " seconds ago"

	return ret;
}

function rawPlot(plot, ctx) {
    var data = plot.getData();
    var axes = plot.getAxes();
    var offset = plot.getPlotOffset();

    for (var i = 0; i < data.length; i++) {
        var series = data[i];
        for (var j = 0; j < series.data.length; j++) {
            var d = (series.data[j]);
            var x = offset.left + axes.xaxis.p2c(d[0]);
            var y = offset.top + axes.yaxis.p2c(d[1]);
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.arc(x,y,4,0,Math.PI*2,true);
            ctx.closePath();           
            ctx.fillStyle = window.karmaColors[d[2]]
            ctx.fill();
        }    
    }
}

function labelDateAxis(date) {
	return formatUnixTimestamp(date);
}

function formatUnixTimestamp(timestamp) {
	var d = new Date(timestamp * 1000);

	return dojo.date.locale.format(d, {selector:"date", datePattern: "HH:mm" });
}

function updateGraph(results) {
	require([
		"dojox/charting/Chart",
		"dojox/charting/themes/Claro",
		"dojo/date/locale",
		"dojox/charting/plot2d/StackedAreas",
		"dojox/charting/axis2d/Default",
		"dojo/query",
		"dojo/dom-construct",
		"dojo/NodeList-manipulate"
	], function(Chart, theme, stamp, qquery, construct) {
		var d = qquery('#graphService' + results.graphIndex);
		d.clear();

		/*
		xaxis: {mode: "time", timeformat: "%a\n %H:%M"},
		{colors: ["#cecece", '#cecece'] }
		*/

		var c = new Chart("graphService" + results.graphIndex, {
			title: "Metric: " + results.metric,
			titleFont: "sans-serif",
			axisFont: "sans-serif",
		});
		c.setTheme(theme);
		c.addPlot("default", {
			type: "StackedAreas",
			markers: true,
		});

		c.addAxis("x", {vertical: false, titleOrientation: "away", font: "sans-serif", labelFunc: labelDateAxis });
		c.addAxis("y", {vertical: true, titleOrientation: "axis", font: "sans-serif" });

		results.services.forEach(function(service, index) {
			axisData = []

			service.metrics.forEach(function(result, index) {
				axisData.push({y: result.value, x: result.date})
			});

			c.addSeries("service " + service.serviceId, axisData);
		});

		c.render();


		window.plots[results.graphIndex] = c;
	});
}

function getAxisColor(index) {
	switch(index) {
		default: return "black";
	}
}

window.plots = {};
window.plotMarkings = {};

function fetchServiceMetricResultGraph(metric, id, graphIndex) {
	data = {
		"services[]": id,
		"metric": metric,
		"graphIndex": graphIndex
	}

	window.serviceResultGraphUrl = 'viewServiceResultGraph.php';

	request(window.serviceResultGraphUrl, data, updateGraph);
}


function layoutBoxes() {
	require([
		"dojo/query"
	], function(query) {
		blocks = query("div.blockContainer");

		if (blocks.length > 0) {
			if (typeof(window.boxLayoutManager) == "undefined") {
				window.boxLayoutManager = new Masonry('div.blockContainer', {itemSelector: 'div.block', gutter: 10, isFitWidth: true });
			}

			console.log("blocks", blocks);

			window.boxLayoutManager.layout();
		}
	});

}

function cookieOrDefault(cookieName, defaultValue) {
	require(["dojo/cookie"], function(cookie) {
		cookieValue = cookie(cookieName)

		if (cookieValue == null) {
			return defaultValue;
		} else {
			return cookieValue;
		}
	});	
}


window.nighttime = cookieOrDefault("nighttime", false);
window.showGoodGroups = cookieOrDefault("groups", false);
window.showEmptyGroups = cookieOrDefault("showEmptyGroups", false);

function toggleEmptyGroups() {
	require([
		"dojo/query"
	], function(query) {
		query('.metricGroup').forEach(function(container, index) {
			var services = query(container).query('.metricList li');

			if (window.showEmptyGroups && services.length == 0) {
				query(container).style('display', 'none');
			}
		});
	});
}

function toggleNightVision() {
	window.nighttime = !window.nighttime;

	require([
		"dojo/query",
	], function (query) {
		var stylesheet = query('link[title=nighttime]');

		if (window.nighttime) {
			stylesheet.attr('rel', 'stylesheet');
		} else {
			stylesheet.attr('rel', 'disabled');
		}
	});
}

function toggleSingleGroup(group) {
//	console.log(group);
}

function toggleGroups() {
	require([
		"dojo/query",
		"dojo/NodeList-manipulate",
		"dojo/NodeList-traverse",
	], function(query) {
		query('.metricListContainer').forEach(function(container, index) {
			var desc = query(container).query('.metricListDescription');
			var services = query(container).query('.metricList li');

			desc.empty();
			services.style('display', 'block');

			var servicesGood = services.query('div span.metricIndicator.good').parent().parent('li');
			var servicesBad = services.query('div span.metricIndicator.bad').parent().parent('li');
			var servicesSkipped = services.query('div span.metricIndicator.skipped').parent().parent('li');
			var servicesWarning = services.query('div span.metricIndicator.warning').parent().parent('li');

			if (!window.showGoodGroups) {
				if ((servicesGood.length + servicesWarning.length) == services.length) {
					servicesGood.style('display', 'none');
					servicesWarning.style('display', 'none');
					var indicator = dojo.toDom('<div style = "display:inline-block"><span class = "metricIndicator good grouped">~</span></div> <div class = "metricText">All <strong>' + servicesGood.length + '</strong> services are good.</div>');

					query(desc)[0].appendChild(indicator);

					if (servicesWarning.length > 0) {
						desc.appendChild(dojo.toDom(' <br /><span class = "warning"><strong>' + servicesWarning.length + '</strong> have a warning</span>.'));
					}

					query(desc).click = console.log; // FIXME
				}

				if (servicesSkipped.length > 0) {
					servicesSkipped.style('display', 'none');
					var indicator = dojo.toDom('<div style = "display:inline-block"><span class = "metricIndicator skipped grouped">~</span></div> <div class = "metricText">Skipped <strong>' + servicesSkipped.length + '</strong> services</div>');
					desc.append(indicator);
				}			
			}
		});
	});
}

window.shortcutToggleNighttime = 78;
window.shortcutToggleEmptyGroups = 77;
window.shortcutToggleGroups = 71;

require(["dojo/dom-construct", "dojo/on", "dojo/query", "dojo/keys", "dojo/domReady!"],
function(domConstruct, on, query, keys) {
        query("body").on("keydown", function(event) {
               if (event.target.localName != "body") {
                        return;
                }

		if (event.ctrlKey) {
			return;
		}

                switch (event.keyCode) {
                case window.shortcutToggleNighttime:
                        event.preventDefault();
                        toggleNightVision();
                        break;
                case window.shortcutToggleGroups:
                        event.preventDefault();

                        window.showGoodGroups = !window.showGoodGroups;

                        toggleGroups();
                        layoutBoxes(false);

                        break;
                case window.shortcutToggleEmptyGroups:
                        event.preventDefault();

                        window.shortcutToggleEmptyGroups = !window.shortcutToggleEmptyGroups;

                        toggleEmptyGroups();

                        break;
                }
        });
});


function setupEnhancedSelectBoxes() {
	require(["dojo/query", "dijit/form/Select", "dojo/_base/array"], function(query, Select, array) {
		var selects = query("select");

		array.forEach(selects, function(entry, index) {
	//		new Select({}, entry);
		});
	});
}


function setupSortableTables() {
	return;

	require([
		"dojo/query"
	], function(query) {
		query("table.dataTable").each(function(tbl) {
			console.log("tbl", tbl);
		});
	});

	$('table.dataTable').dataTable({
		'sDom': 'flpitpil',
		'aaSorting': [[ 1, 'desc ']],
		'oLanguage': {
		'oPaginate': {
			'sNext': '&nbsp;',
			'sPrevious': '&nbsp;'
		}
		}
	});

	$('a.paginate_enabled_next').html('&nbsp;');
	$('a.paginate_enabled_previous').html('&nbsp;');
	$('a.paginate_disabled_next').html('&nbsp;');
	$('a.paginate_disabled_previous').html('&nbsp;');
}

function serviceIconChanged() {
	var icon = $('select[name$="-icon"]').val();

	if (icon != '') {
		icon = 'resources/images/serviceIcons/' + icon;
		
		$('span#serviceIconPreview').html('<img src = "' + icon + '" alt = "serviceIcon" />');
	}
}

function menuButtonClick(address) {
	// Hide your eyes. This will be temporary.
	if (address.indexOf(".php") != -1 || address.indexOf(".html") != -1) {
		window.location = address;
	} else {
		eval(address);
	}
}

function requestRescanWidgets() {
	var proBar = new dijit.ProgressBar();
	proBar.placeAt("body");

	proBar.set("value", 50);
}

function renderSubresults(data, ref) {
	require([
		"dojo/query",
		"dojo/dom-construct",
		"dojo/NodeList-manipulate"
	], function(query, construct) {
		generate = construct.toDom;

		list = query(ref);
		list.empty();

		data.forEach(function(result, index) {
			row = query(generate("<li />"));

			metricIndicator = query(generate('<span class = "metricIndicator">&nbsp;</span>'));
			metricIndicator.addClass(result.karma);
			row.append(metricIndicator);

			row.append(generate("<span>" + result.name + "</span>"));

			if (typeof result.comment != "undefined" && result.comment.length > 0) {
				desc = generate('<span class = "subtle">');
				desc.innerHTML = " (" + result.comment + ")";
				row.append(desc);
			}

			list.append(query(row));
		});
	});

}

function renderServiceList(data, ref) {
	require([
		"dojo/query",
		"dojo/dom-construct",
		"dojo/NodeList-manipulate"
	], function(query, construct) {
		generate = construct.toDom;

		container = query('.widgetRef' + ref);
		container.addClass('metricListContainer');
		container.empty();

		container.append(generate('<p class = "metricListDescription"></p>'));

			list = query(generate('<ul class = "metricList"></ul>'));

			data.forEach(function(service, index) {
				indicator = query(generate('<span class = "metricIndicator" />'));
				indicator.addClass(service.karma.toLowerCase());

				if (service.icon != null) {
					indicator.append(dojo.toDom('<img src = "resources/images/serviceIcons/' + service.icon + '" /><br />'));
				}
				
				indicator.append(dojo.toDom('<span>' + service.lastChangedRelative + '</span>'));
				indicator = query(generate('<div class = "metricIndicatorContainer" />')).append(indicator);

				metric = query(generate('<li />'));
				metric.append(indicator);

				text = query(generate('<div class = "metricText" />'));
				text.append('<span class = "metricDetail">' + service.estimatedNextCheckRelative + '</span>');
				text.append('<a href = "viewService.php?id=' + service.id + '"><span class = "metricTitle">' + service.alias + '</span></a>');
				metric.append(text);

				list.append(metric);
			});
	
		container.append(list);
		toggleGroups();
		layoutBoxes();
	});
}

function renderNewsList(data, ref) {
	require([
		"dojo/query",
		"dojo/dom-construct",
		"dojo/NodeList-manipulate",
		"dojo/date/locale"
	], function(query, construct) {
		container = query(".widgetRef" + ref);
		container.empty();

		data.forEach(function(news, index) {
			storyHtml = query(construct.toDom("<p />"));
			storyHtml.append("<strong>" + formatUnixTimestamp(news['time']) + '</strong> <span class = "subtle">' + news['source'] +  '</span> <a href = "' + news['url'] + '">' + news['title'] + '</a>');
			container.append(storyHtml);
		});

		layoutBoxes();
	});
}

function request(url, queryParams, callback, callbackObject, repeat) {
	function doRequest() {
		require([
			"dojo/request/xhr"
		], function (xhr) {
			xhr(url, { handleAs: "json", query: queryParams }).then(
				function(data) {
					try{
						callback(data, callbackObject);
					} catch (err) {
						console.log("err in ajax complete() handle", err)
					}
				},
				function(err) {
					console.log("err", url, err);
				}
			)
		});
	}
	
	doRequest();

	if (repeat > 0) {
		setInterval(doRequest, repeat);
	}
}

function updateMetricList(ref) {
	request("json/getServices", null, renderServiceList, ref, 1000);
}

function moveSelectOption(listOrigin, listDestination) {
	require([
		"dojo/dom"
	], function(dom) {
		var elListOrigin = dom.byId(listOrigin);
		var elListDestination = dom.byId(listDestination);

		if (elListOrigin.selectedIndex > -1) {
			var selectedItem = elListOrigin.item(elListOrigin.selectedIndex);

			elListDestination.add(selectedItem);
		}	
	});
}

function requestFullScreen(element) {
    // Supports most browsers and their versions.
    var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;

    if (requestMethod) { // Native full screen.
        requestMethod.call(element);
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{F11}");
        }
    }
}

function showFullscreenButton() {
	require([
		"dojo/dom-construct",
		"dojo/query",
		"dojo/NodeList-manipulate",
	], function(construct, query) {
		button = construct.toDom('<button id = "fullscreen" onclick = "requestFullScreen(document.body)">Fullscreen</button>');

		query("#header")[0].appendChild(button);

	});
}


