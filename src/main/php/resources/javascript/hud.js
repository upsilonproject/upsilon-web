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
	'-' + pad(d.getMonth() + 1) +
	'-' + pad(d.getDate()) +
	' ' + pad(d.getHours()) +
	':' + pad(d.getMinutes()) +
	' (local)';
}

function makeDateHumanReadable(element) {
	if (element.textContent == "now") {
		utcDate = new Date((new Date()).toUTCString());
	} else {
		utcDate = new Date(element.textContent.replace(" ", "T") + "Z")
	}

	elementUnixTimestamp = utcDate / 1000
	nowUnixTimestamp = Date.now() / 1000

	if (isNaN(elementUnixTimestamp)) {
		console.log("Could not parse date in element", element);
		return;
	}

	if ((nowUnixTimestamp - elementUnixTimestamp) > 3600) {
		dojo.addClass(element.parentElement, "old");
	} else if ((nowUnixTimestamp - elementUnixTimestamp) < 0) {
		dojo.addClass(element.parentElement, "bad");
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
	return formatUnixTimestamp(date, "week");
}

function formatUnixTimestamp(timestamp, range) {
	var d = new Date(timestamp * 1000);

	if (range == "week") {
		dp = "EEE d MMM"
	} else {
		dp = "HH:mm"
	}

	return dojo.date.locale.format(d, {selector:"date", datePattern: dp });
}

function updateMultipleServiceChart(results) {
	updateChart(results)
}

function updateSingleMetricChart(results) {
	updateChart(results)
}

function updateChart(results) {
	require([
		"dojox/charting/Chart",
		"dojox/charting/themes/Claro",
		"dojo/date/locale",
		"dojo/query",
		"dojo/dom-construct",
		"dojox/charting/plot2d/Indicator",
		"dojox/charting/widget/SelectableLegend",
		"dojox/charting/action2d/Tooltip",
		"dojox/charting/action2d/MouseZoomAndPan",
		"dojo/NodeList-manipulate",
		"dojox/charting/plot2d/Lines",
		"dojox/charting/axis2d/Default"
	], function(Chart, theme, stamp, qquery, construct, ind, Legend, Tooltip, ZaP) {
		var d = qquery('#chartService' + results.chartIndex);

		/*
		xaxis: {mode: "time", timeformat: "%a\n %H:%M"},
		{colors: ["#cecece", '#cecece'] }
		*/

		if (typeof(window.charts[results.chartIndex]) == "undefined") {
			window.charts[results.chartIndex] = new Chart("chartService" + results.chartIndex, {
				title: "Metric: " + results.metric,
				titleFont: "sans-serif",
				axisFont: "sans-serif",
			});

//			window.charts[results.chartIndex].destroy()
		}

		c = window.charts[results.chartIndex];

		c.setTheme(theme);

		c.addAxis("x", {vertical: false, titleOrientation: "away", font: "sans-serif", labelFunc: labelDateAxis });
		c.addAxis("y", {vertical: true, titleOrientation: "axis", font: "sans-serif" });

		window.chartResults = results;

		results.services.forEach(function(service, index) {
			axisData = []

			console.log("plotting service", service);

			if (typeof(service.listMetrics) != "undefined") {
				service.metrics = service.listMetrics;
			}

			service.metrics.forEach(function(result, index) {
				axisData.push({y: result.value, x: result.date, karma: result.karma, tooltip: result.name + ": " + result.value + "<br /><br />" + formatUnixTimestamp(result.date, "week") })
			});

			seriesName = "service " + service.serviceId + "_" + service.field;
			seriesName = service.field;
			//seriesName = service.metrics[0].caption;
			c.addSeries(seriesName, axisData);

		});

		c.addPlot("default", {
			type: "Lines",
			markers: true,
			styleFunc: function(i) {
				if (i.karma in window.karmaColors) {
					r = { fill: "" + window.karmaColors[i.karma] }
					return r;
				} else {
					return { fill: "gray" }
				}
			}
		});

		if (typeof(results.chartIndex) === "number") {
			window.chartMarkings[results.chartIndex].forEach(function(v)  {
				c.addPlot("threshhold", { type: ind, 
					vertical: false,
					lineStroke: { color: "red", style: "ShortDash" },
					values: v,
				});
			}
			);
		}

		new ZaP(c, "default", {axis: "x" });

		if (window.charts[results.chartIndex].legend == null) {
			window.charts[results.chartIndex].legend = new Legend({chart: c, autoScale: true}, "legend" + results.chartIndex);
		}

		tooltip = new Tooltip(c, "default");

		c.render();

		window.charts[results.chartIndex].legend.refresh();
	});
}

function getAxisColor(index) {
	switch(index) {
		default: return "black";
	}
}

window.charts = {};
window.chartMarkings = {};

function fetchServiceMetricResultChart(metric, dataset, chartIndex) {
	console.log("chart " + chartIndex + " datasets", dataset);
	data = {
		"serviceIds[]": dataset.serviceIds,
		"node": dataset.node,
		"metrics[]": metric.split(","),
		"chartIndex": chartIndex,
		"resolution": window.chartResolution,
		"interval": window.chartInterval
	}

	window.serviceResultChartUrl = 'json/viewServiceResultChart.php';

	request(window.serviceResultChartUrl, data, updateMultipleServiceChart);
}

function fetchServiceSingleMetricResultChart(metric, dataset, chartIndex) {
	console.log("chart single metric" + chartIndex + " datasets", dataset)
	
	data = {
		"serviceIds[]": dataset.serviceIds,
		"node": dataset.node,
		"metrics[]": metric.split(","),
		"chartIndex": chartIndex
	}

	request("json/getServiceMetrics.php", data, updateSingleMetricChart)
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

					if (query(desc).length > 0) {
						query(desc)[0].appendChild(indicator);
					}

					if (servicesWarning.length > 0) {
						desc.appendChild(dojo.toDom(' <br /><span class = "warning"><strong>' + servicesWarning.length + '</strong> have a warning</span>.'));
					}
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
	require([
		"dojo/query",
		"dojo/dom-construct"
	], function(query, cons) {
		generate = cons.toDom;

		var icon = query('select[name$="-icon"]')[0].value;

		console.log(icon);

		if (icon != '') {
			icon = 'resources/images/serviceIcons/' + icon;
			
			query('span#serviceIconPreview')[0].innerHTML = ('<img src = "' + icon + '" alt = "serviceIcon" />');
		}

	});
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

			if (typeof result.comment != "undefined" && result.comment != null && result.comment.length > 0) {
				desc = generate('<span class = "subtle">');
				desc.innerHTML = " (" + result.comment + ")";
				row.append(desc);
			}

			list.append(query(row));
		});
	});

}

function renderGroup(data, ref) {
	require([
		"dojo/query",
		"dojo/dom-construct",
		"dojo/NodeList-manipulate",
		'dojo/NodeList-traverse'
	], function(query, construct) {
		generate = construct.toDom

		container = query('.widgetRef' + ref);
		container.empty()

		if (data['listServices'].length > 0) {
			container.append(generate('<h2>Services</h2>'))
			renderServiceList(data['listServices'], container)
		}

		if (data['listClassInstances'].length > 0) {
			container.appendChild(generate('<h2>Class Instances'));
			renderClassInstances(data['listClassInstances'], container);
		}
	});
}

function renderClassInstances(data, owner) {
	require([
		"dojo/dom-construct",
		"dojo/dom-class",
		"dojo/query",
		"dojo/NodeList-manipulate",
		"dojo/NodeList-traverse"
	], function(construct, domClass, query) {
		if (!domClass.contains(owner, ".classInstances")) {
			container = generate('<p class = "classInstances" />');
			owner.appendChild(container);
		} else {
			container = owner.children('.classInstances');
		}

		data.forEach(function(classInstance, index) {
			dom = construct.toDom('<p><a href = "viewClassInstance.php?id=' + classInstance['id'] + '">' + classInstance['title'] + "</a></p>")

			classInstance['requirements'].forEach(function(requirement, index) {
				domRequirement = construct.toDom('<p>&nbsp;</p>');
				indicator = construct.place('<span class = "metricIndicator">&nbsp;</span>', domRequirement);

				txt = construct.place('<div class = "metricText"></div>', domRequirement);

				txt.appendChild(generate(' <span><a href = "addInstanceCoverage.php?requirementId=' + requirement['requirementId'] + '&instanceId=' + requirement['instanceId'] + '">' + requirement['requirementTitle'] + '</a></span> - '));

				if (requirement['serviceIdentifier'] != null) {
					query(indicator).addClass(requirement['karma'].toLowerCase());
					txt.appendChild(generate('<span><a href = "viewService.php?id=' + requirement['service'] + '">' + requirement['serviceIdentifier'] + '</a></span>'));
				} else {
					txt.appendChild(generate('<span class = "bad">Not covered</span>'))
				}

				if (requirement['node'] != null) {
					txt.appendChild(document.createTextNode(' on '));
					txt.appendChild(generate('<a href = "viewNode.php?identifier=' + requirement['node'] + '">' + requirement['node'] + '</a>'));
				}

				txt.appendChild(generate('<div class = "subtle">' + requirement['output'] + '</div>'));

				dom.appendChild(domRequirement)
			});

			container.appendChild(dom);
		});
	});
}

function renderServiceList(data, owner) {
	require([
		"dojo/query",
		"dojo/dom-construct",
		"dojo/dom-class",
		"dojo/NodeList-manipulate",
		"dojo/NodeList-traverse"
	], function(query, construct, domClass) {
		generate = construct.toDom;

		if (typeof(owner) == "string") {
			owner = query('.widgetRef' + owner)
		}

		if (!domClass.contains(owner, '.services')) {
			owner.children('.loading').remove();
			owner.append(generate('<p class = "services" />'))
		}

		owner.children('.metricListContainer').remove();

		container = generate('<div class = "metricListContainer" />');
		owner.append(container);

		container.appendChild(generate('<p class = "metricListDescription" />'));

		list = generate('<ul class = "metricList"></ul>');
		container.appendChild(list);

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
			text.append(' on ');
			text.append('<a href = "viewNode.php?identifier=' + service.node + '"><spa>' + service.node + '</span></a>');
			text.append('<div class = "subtle">' + service.output + '</div>');
			metric.append(text);

			query(list).append(metric);
		});

		toggleGroups();
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

		header = query("#header")

		if (header.length > 0) {
			header[0].appendChild(button);
		}
	});
}

function createOption(val, txt) {
	opt = document.createElement("option");
	opt.value = val;
	opt.text = txt;

	return opt;
}

function filterGetFieldValues() {
	fields = {};

	window.filters.forEach(function(v) {
		htmlInput = document.getElementById('filterInput-' + v)

		if (htmlInput.type == "checkbox") {
			if (htmlInput.checked) {
				fields[v] = true
			}
		} else {
			fields[v] = document.getElementById('filterInput-' + v).value
		}

		if (fields[v] != "" && fields[v] != null) {
			document.getElementById('filterLabel-' + v).classList.add("good");
		} else {
			document.getElementById('filterLabel-' + v).classList.remove("good");
		}
	});

	return fields;
}

function loadFilterResultsIntoSelect(sel, dat) {
	select = document.getElementById(sel);
	currentValue = select.getAttribute("initialvalue");
	window.sel = select;
	
	console.log("Loading results into", select, dat);

	for (i = select.options.length -1; i >= 0; i--) {
		select.remove(i);
	}

	if (dat.length == 0) {
		select.disabled = true;
		select.add(createOption(null, 'Nothing to select...'))
	} else {
		select.disabled = false;

		dat.forEach(function(v) {
			let opt = createOption(v.id, v.identifier);

			select.add(opt);

			if (v.id == currentValue) {
				opt.setAttribute('selected', true);
			}
		});
	}
	
	filteringSelectLoaded(dat.length);
}

function filterCommands() {
	window.filterFunc = function() {
		fields = filterGetFieldValues();

		request('json/getCommands.php', fields, function(dat) {
			loadFilterResultsIntoSelect('UpdateRemoteConfig-command', dat);
		});
	}

	window.filterFunc()
}

function filterClassInstance() {
	window.filterFunc = function() {
		fields = filterGetFieldValues()

		request('json/getClassInstances.php', fields, function(dat) {
			loadFilterResultsIntoSelect('formAddMembership-classInstance', dat);
		});
	}

	window.filterFunc();
}

function filterService() {
	window.filterFunc = function() {
		fields = filterGetFieldValues();

		request('json/getServices.php', fields, function(dat) {
			loadFilterResultsIntoSelect('updateWidgetInstance-service', dat);
		});
	}

	window.filterFunc();
}

function filterInstanceCoverageOptions() {
	window.filterFunc = function() {
		fields = filterGetFieldValues();

		request('json/addInstanceCoverage.php', fields, function(dat) {
			loadFilterResultsIntoSelect('update-service', dat);
		});
	}

	window.filterFunc();
}

function filteringSelectClear() {
	window.filters.forEach(function(name) {
		el = document.getElementById('filterInput-' + name)
		el.value = "";

		el = document.getElementById('filterLabel-' + name)
		el.classList.remove("good")
		el.classList.remove("warning")
		el.classList.add("unknown")
	});

	filteringSelectBlur();
}

function filteringSelectLoading() {
	document.getElementsByClassName('filterTracker')[0].disabled = true;
	document.getElementsByClassName('filterTracker')[0].style.backgroundColor = "lightgray";
	document.getElementById("filteringSelectLoadingIndicator").innerText = "LOADING"
}

function filteringSelectLoaded(count) {
	document.getElementsByClassName('filterTracker')[0].disabled = false;
	document.getElementsByClassName('filterTracker')[0].style.backgroundColor = "white";
	document.getElementById("filteringSelectLoadingIndicator").innerText = count + " results found";
}

function filteringSelectBlur() {
	filteringSelectLoading();
	window.filterFunc();
}

function filteringSelectChanged() {
	lblClasses = document.activeElement.previousElementSibling.classList;
	
	lblClasses.remove("good")
	lblClasses.remove("unknown")
	lblClasses.add("warning")
}

function searchSelect(selectedItem) {
	console.log(selectedItem);
	window.location.href = selectedItem.url;
}

function setupSearchBox() {
	require([
		"dijit/form/FilteringSelect", "dojo/store/JsonRest", "dojo/domReady!"
	], function(FilteringSelect, JsonRest) {
		var searchStore = new JsonRest({
			idProperty: "identifier",
			target: "/json/search.php"
		});

		window.ss = searchStore;

		var fs = new FilteringSelect({
			id: "searchBox",
			autoComplete: false,
			highlightMatch: "all",
			required: false,
			queryExpr: "*${0}*",
			sortByLabel: true,
			store: searchStore,
			hasDownArrow: false,
			placeholder: 'Search',
			pageSize: 10,
			searchAttr: "identifier",
			onChange: function(state) {
				searchSelect(dijit.byId("searchBox").get("item"));
			},
		}, "searchBox").startup();
	});
}

window.chartResolution = 7 * 50;

function changeChartResolution(val) {
	window.chartResolution += val;

	require([
		"dojo/query"
	], function(query) {
		query("#lblResolution").text(window.chartResolution);
	});
}


window.chartInterval = 7;
function changeChartInterval(i) {
	window.chartInterval += i;

	require([
		"dojo/query"
	], function(query) {
		query("#lblInterval").text(window.chartInterval);
	});
}
