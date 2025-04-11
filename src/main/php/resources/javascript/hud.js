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
    utcDate = new Date(element.textContent.replace(" ", "T"))
  }

  elementUnixTimestamp = utcDate / 1000
  nowUnixTimestamp = Date.now() / 1000

  if (isNaN(elementUnixTimestamp)) {
    console.log("Could not parse date in element", element);
    return;
  }

  if ((nowUnixTimestamp - elementUnixTimestamp) > 3600) {
    element.parentElement.classList.add("old");
  } else if ((nowUnixTimestamp - elementUnixTimestamp) < 0) {
    element.parentElement.classList.add("bad");
  } else {
    element.parentElement.classList.add("good");
  }

  if (element.classList.contains("relative")) {
    description = toLocalIsoLikeString(utcDate);
    element.textContent = secondsToString(nowUnixTimestamp - elementUnixTimestamp);
  } else {
    description = secondsToString(nowUnixTimestamp - elementUnixTimestamp);
    element.textContent = toLocalIsoLikeString(utcDate);
  }

  description += ". Original: " + utcDate.toString()

  element.title = description

  element.classList.add("hastooltip")
  element.append(tooltip);
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
  const cookieValue = document.cookie.split('; ').find(row => {
    if (row.startsWith(cookieName))  {
      return row.split("=")[1];
    }
  });

  return defaultValue;
}


window.nighttime = cookieOrDefault("nighttime", false);
window.showGoodGroups = cookieOrDefault("groups", false);
window.showEmptyGroups = cookieOrDefault("showEmptyGroups", false);

function toggleEmptyGroups() {
  document.querySelectorAll('.metricGroup').forEach(function(container, index) {
    var services = container.querySelector('.metricList li');

    if (window.showEmptyGroups && services.length == 0) {
      query(container).style('display', 'none');
    }
  });
}

function toggleNightVision() {
  window.nighttime = !window.nighttime;

  var stylesheet = document.querySelector('link[title=nighttime]');

  if (window.nighttime) {
    stylesheet.attr('rel', 'stylesheet');
  } else {
    stylesheet.attr('rel', 'disabled');
  }
}

function toggleSingleGroup(group) {
  //	console.log(group);
}

window.shortcutToggleNighttime = 78;
window.shortcutToggleEmptyGroups = 77;

function setupKeyboardShortcuts() {
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
      case window.shortcutToggleEmptyGroups:
        event.preventDefault();

        window.shortcutToggleEmptyGroups = !window.shortcutToggleEmptyGroups;

        toggleEmptyGroups();

        break;
    }
  });
}


function setupEnhancedSelectBoxes() {
  /**
  require(["dojo/query", "dijit/form/Select", "dojo/_base/array"], function(query, Select, array) {
    var selects = query("select");

    array.forEach(selects, function(entry, index) {
      //		new Select({}, entry);
    });
  });
  */
    }


function setupSortableTables() {
  /**
  var tables = document.querySelectorAll('table.dataTable');

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
  */
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
  let container = document.querySelector('.widgetRef' + ref)
  //		container.empty()

  if (data['listServices'].length > 0) {
    renderServiceList(data['listServices'], container)
  }

  if (data['listClassInstances'].length > 0) {
    renderClassInstances(data['listClassInstances'], container);
  }
}

function renderClassInstances(data, owner) {
  if (owner.classList.contains(".classInstances")) {
    container = owner.children('.classInstances');
  } else {
    let oldContainer = owner.querySelector('.classInstances')

    if (oldContainer != null) {
      oldContainer.remove()
    }

    container = generateElClass('p', 'classInstances');
    container.classList.add('grid')
    owner.appendChild(container);
  }

  remove(owner.querySelector('.loading'))

  data.forEach(function(classInstance, index) {
    let titleLink = document.createElement('a')
    titleLink.setAttribute('href', 'viewClassInstance.php?id=' + classInstance['id'])
    titleLink.innerText = classInstance['title'] 

    let title = document.createElement('h3')
    title.innerText = 'Class: '
    title.appendChild(titleLink)
    container.appendChild(title);

    classInstance['requirements'].forEach((requirement, index) => {
      let el = new DomListItemService(container, true)
      el.setKarma(requirement['karma'], requirement)
      el.setNode(requirement['node'])
      el.setRequirement(requirement)
    })
  });
}

function query(r) {
  if (typeof(r) != "string") {
    console.error(r)
    throw "Query with non string: " + r;
  }

  return document.querySelector(r)
}

function generateElClass(type, cls) {
  let el = document.createElement(type);
  el.classList += cls

  return el
}

function generateElClassText(type, cls, txt) {
  let el = generateElClass(type, cls);
  el.innerText = txt;

  return el;
}

function remove(el) {
  if (el != null) {
    el.remove()
  }
}

function renderServiceList(data, owner) {
  if (typeof(owner) == "string") {
    owner = document.querySelector('.widgetRef' + owner)
  }

  if (!owner.classList.contains('.services')) {
    remove(owner.querySelector('.loading'))

    let domSvc = document.createElement('p');
    domSvc.classList += 'services'
    owner.appendChild(domSvc)
  }

  remove(owner.querySelector('.metricListContainer'))

  container = generateElClass('div', 'metricListContainer');
  owner.appendChild(container);

  container.appendChild(generateElClass('p', 'metricListDescription'));

  if (data.length == 0) {
    let p = document.createElement('span')
    p.innerText = 'Zero services'

    container.appendChild(p)
  } else {
    let list = generateElClass('div', 'metricList');
    list.classList.add('grid')
    container.appendChild(list);

    data.forEach(function(service, index) {
      let el = new DomListItemService(list)
      el.setKarma(service.karma)
      el.setIcon(service.icon)
      el.setLastChangedRelative(service.lastChangedRelative)
      el.setNode(service.node)
      el.setTitleId(service.alias, service.id)

      /**

        text = generateElClass('div', 'metricText');
        text.appendChild('<span class = "metricDetail">' + service.estimatedNextCheckRelative + '</span>');
        text.appendChild('<a href = "viewService.php?id=' + service.id + '"><span class = "metricTitle">' + service.alias + '</span></a>');
        text.appendChild(' on ');
        text.appendChild('<a href = "viewNode.php?identifier=' + service.node + '"><spa>' + service.node + '</span></a>');
        text.appendChild('<div class = "subtle">' + service.output + '</div>');
        metric.append(text);
        */
    });
  }
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

function request(urlString, queryParams, callback, callbackObject, repeat) {
  let url = new URL(urlString, window.location.protocol + "//" + window.location.host)
  for (let k in queryParams) {
    url.searchParams.append(k, queryParams[k])
  }

  function doRequest() {
    fetch(url, { handleAs: "json" })
      .then(data => data.json())
      .then(json => {
        callback(json, callbackObject);
      })
      .catch(err => {
        console.error("err in ajax complete() handle", err)
      })
  }

  doRequest()

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

    header = document.querySelector("#header")

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
  console.log("sel", sel)
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

function filterNodes() {
  window.filterFunc = function() {
    fields = filterGetFieldValues()

    request('json/getNodes.php', fields, function(dat) {
      loadFilterResultsIntoSelect('foo', dat)
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
  /*
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
  */
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

function onDomReady(evt) {
  document.addEventListener("DOMContentLoaded", () => {
    evt()
  });
}
