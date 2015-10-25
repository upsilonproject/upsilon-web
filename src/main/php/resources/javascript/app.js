if (typeof String.prototype.endsWith !== 'function') {
	String.prototype.endsWith = function(suffix) {
		return this.indexOf(suffix, this.length - suffix.length) !== -1;
	};
}

function filterReset() {
	return true;
}

function onLoad() {
	try {
		setupHeader();
		setupToolbar();
		setupRootContainer();

		reqUpdatePermissions();

		loadHash();
	} catch (err) {
		displayError(err);
	}
}

function main() {
	require([
		"dojo/request",
		"dojo/domReady!"
	], function() {
		onLoad();
	});
}
 
function applyPermissionsToToolbar() {
	require([
		"dijit/registry"
	], function(registry){
		permissions = window.permissions;

		registry.byId("mniDashboard").set("disabled", !permissions.viewDashboard);
		registry.byId("mniServices").set("disabled", !permissions.viewServices);
		registry.byId("mniNodes").set("disabled", !permissions.viewNodes);
		registry.byId("mniClasses").set("disabled", !permissions.viewClasses);
		registry.byId("mniMaintPeriods").set("disabled", !permissions.viewMaintPeriods);
		registry.byId("mniPreferences").set("disabled", !permissions.loggedIn);
		registry.byId("mniLogout").set("disabled", !permissions.loggedIn);
		registry.byId("mniLogin").set("disabled", permissions.loggedIn);

		window.registry = registry;
	});
} 

function loadUpdatePermissions(perms) {
	window.permissions = perms;
	
	applyPermissionsToToolbar(perms);
} 

function reqUpdatePermissions() {
	window.permissions = { loggedIn: false };

	var req = newJsonReq();
	req.url = "json/sessionPermissions";
	req.load = loadUpdatePermissions;
	req.get();
}

function displayError(err) {
	window.alert("General Error: " + err);
}

function createButton(config) {
	var filterer = {}

	require([
		"dijit/form/Button",
	], function(Button) {
		filterer = new Button({
			label: config.title, 
			onClick: config.onClick
		});
	});

	return filterer;
}

function newGrid(configuration) {
	require([
		"gridx/Grid",
		"dojo/store/Memory",
		"gridx/core/model/cache/Sync",
		"gridx/modules/VirtualVScroller",
		"gridx/modules/ColumnResizer",
		"gridx/modules/Filter",
		"gridx/modules/filter/FilterBar",
		"gridx/modules/Bar",
		"gridx/modules/NestedSort",
		"gridx/modules/filter/QuickFilter",
		"gridx/support/Summary",
		"gridx/support/QuickFilter",
		"gridx/modules/extendedSelect/Row",
		"gridx/modules/RowHeader",
		"gridx/modules/IndirectSelect",
		"dijit/form/Button"
	], function (Grid, MemoryStore, Cache, scroller, resizer, filter, filterBar, bar, nestedSort, mfQuickFilter, summary, QuickFilter, extendedSelectRow, rowHeader, IndirectSelect, Button) {
		gridConfiguration = {
			columnWidthAutoResize: true,
			id: configuration.id,
			cacheClass: Cache, 
			store: new MemoryStore(),
			structure: configuration.structure,
			barTop: [ 
				summary, 
				{pluginClass: createButton, title: "All", filterFunc: filterReset},
				{pluginClass: QuickFilter, style: 'text-align: right'}, 
			],
			modules: [
				scroller, resizer, filter, bar, nestedSort, filterBar, IndirectSelect, extendedSelectRow, rowHeader
			]	
		};

		if (typeof(configuration.filters) != "undefined") {
			configuration.filters.forEach(function(e, i) {
				gridConfiguration.barTop.splice(2, 0, {
					pluginClass: createButton, title: e.title, onClick: function(fff) { 
						console.log(fff)
						config.grid.filter.setFilter(e.filterFunc);
					}
				});
			})
		}

		if (typeof(configuration.buttons) != "undefined") {
			configuration.buttons.forEach(function(e, i) {
				gridConfiguration.barTop.splice(2, 0, {
					pluginClass: createButton, title: e.title, onClick: e.onClick 
				});
			});
		}

		grid = new Grid(gridConfiguration);

		grid.filterBar.closeButton = false;
		grid.filterBar.refresh();
		grid.startup();
	});

}

function initGridUsergroups() {
	configuration = {
		id: "gridUsergroups",
		structure: [
			{field:"id", name: "ID", width: "5%"},
			{field:"title", name: "Title"},
		],
	}

	newGrid(configuration);
}


function initGridUsers() {
	newGrid({
		id: "gridUsers",
		structure: [
			{field:"id", name: "ID", width: "5%"},
			{field:"username", name: "Username"},
		],
	});
}

function onClickClassesUpdate(a, b, c, d) {
	console.log("update", a, b, c, d, this)
}

function initGridClasses() {
	setIconSource("serviceIcons");

	newGrid({
		id: "gridClasses",
		structure: [
			{field:"id", name: "ID", width: "5%"},
			{field:"icon", width: "5%", decorator: iconDecorator},
			{field:"title", name: "Title"},
		],
		buttons: [
			{title: "Update", onClick: onClickClassesUpdate}
		]
	});
}

function karmaStyler(a, b, c) {
	contents = a.data();

	switch (contents) {
		case "GOOD": return "good";
		case "BAD": return "bad";
		case "SKIPPED": return "skipped";
		case "WARNING": return "warning";
		case "UNKNOWN":
		default: return "";
	}
}

function initGridServices() {
	newGrid({
		id: "gridServices",
		structure: [
			{field:"identifier", name: "Identifier"},
			{field:"lastUpdated", name: "Last Updated"},
			{field:"output", name: "Output", class: "code"},
			{field:"karma", name: "Karma", class: karmaStyler}
		],
	});
}

function filterCommandsNone() {
	return false;
}

function filterNodesWithProblems(rowData, rowId) {
	console.log("filtering nodes with problems");;
	return true;
}

function initGridNodes() {
	newGrid({
		id: "gridNodes",
		structure: [
			{field: "identifier", name: "Identifier"},
			{field: "instanceApplicationVersion", name: "Version", hidden: true, width: "8%"},
			{field: "nodeType", name: "Type"},
			{field: "serviceCount", name: "Service count"},
			{field: "lastUpdated", name: "Last updated Relative"},
			{field: "karma", name: "Karma", class: karmaStyler, width: "8%"},
		],
		filters: [
			{title: "Nodes with Problems", filterFunc: filterNodesWithProblems}
		]
	})
}

function loadUsergroups(usergroups) {
	require([
	     "dijit/registry",
	     "dojo/store/Memory",
	     "dojo/domReady!" 
     ], function (registry, Store) {
	    setTitle("Usergroups");

		if (!registry.byId("gridUsergroups")) {
			initGridUsergroups(); 
		} 
		  
		grid = registry.byId("gridUsergroups"); 
		grid.setStore(new Store({data: usergroups}));

		setContentElement(grid);
	});
}


function loadUsers(users) {
	require([
	     "dijit/registry",
	     "dojo/store/Memory",
	     "dojo/domReady!" 
     ], function (registry, Store) {
	    	setTitle("Users");

		if (!registry.byId("gridUsers")) {
			initGridUsers(); 
		} 
		  
		grid = registry.byId("gridUsers"); 
		grid.setStore(new Store({data: users}));

		setContentElement(grid);
	});
}

function loadListClasses(classes) {
	require([
	     "dijit/registry",
	     "dojo/store/Memory",
	     "dojo/domReady!" 
     ], function (registry, Store) {
	    	setTitle("Classes");

		if (!registry.byId("gridClasses")) {
			initGridClasses(); 
		} 
		  
		grid = registry.byId("gridClasses"); 
		grid.setStore(new Store({data: classes}));

		setContentElement(grid);
	});
}
 
function loadListNodes(nodes) {
	require([
	     "dijit/registry",
	     "dojo/store/Memory",
	     "dojo/domReady!" 
     ], function (registry, Store) {
	    setTitle("Nodes");

		if (!registry.byId("gridNodes")) {
			initGridNodes(); 
		} 
		  
		grid = registry.byId("gridNodes"); 
		grid.setStore(new Store({data: nodes}));

		setContentElement(grid);
	});
}

function mniClassesClicked() {
	var req = newJsonReq();
	req.url = "json/listClassImmediateChildren?id=1",
	req.load = loadListClasses,
	req.get();
}

function mniNodesClicked() {
	setHash("nodes");

	var req = newJsonReq();
	req.url = "json/listNodes",
	req.load = loadListNodes,
	req.get();
}

function renderButton(el){
	ret = null;

	ret2 = require([
		"dijit/form/Button"
	], function(Button) {
		this.ret = new Button({id: el.id, label: el.caption});
	});

	return ret;
}

function renderInput(el) {
	ret = null;

	require([
		"dijit/form/TextBox"
	], function(TextBox) {
		this.ret = new TextBox();	
	});

	return ret;
}

function renderElement(el) {
	ret = null;

	require([
		"dojo/dom-construct"
	], function(cons) {
		this.ret = cons.create('div', { innerHTML: 'foo' });
	});

	return ret;
}

function renderFormElements(res, cp) {
	console.log("render form elements", res);
	res.forEach(function(el) {
		if (el instanceof Array) {
			renderFormElements(el, cp);
			return;
		}

		switch(el.type) {
			case "ElementButton": 
				cp.addChild(renderButton(el));
				break;
			case 'ElementInput':
				cp.addChild(renderInput(el));
			default:
		}
	});
}

function getPreferencesForm() {
	ret = null;

	require([
		"dijit/layout/ContentPane",
	], function(cp) {
		cp = new cp({ 
			content: "foo"	
		});

		req = newJsonReq();
		req.url = "ajax-form.php?action=getElements";
		req.get(function(res) {
			renderFormElements(res, cp)	
		});

		this.ret = cp;
	});

	return ret;
}

function loadHash() {
	switch(window.location.hash) {
		case "#preferences": mniPreferencesClicked(); break;
		case "#nodes": mniNodesClicked(); break;
	}
}

function setHash(hash) {
	window.location.hash = hash;
}

function mniPreferencesClicked() {
	setHash("preferences");

	require([
		"dijit/Dialog",
	], function(Dialog) {
		dlg = new Dialog({
			title: "Preferences",
			content: getPreferencesForm(),
			style: "width: 800px"
		});
	});

	dlg.show();
}

function mniLogoutClicked() {
	req = newJsonReq();
	req.url = "json/logout";
	req.load = loadLogout;
	req.get();
}

function mniUsersClicked() {
	req = newJsonReq();
	req.url = "json/listUsers";
	req.load = loadUsers;
	req.get();
}

function mniUserGroupsClicked() {
	req = newJsonReq();
	req.url = "json/listUsergroups";
	req.load = loadUsergroups;
	req.get();
}


function setupToolbar() {
	require([
		"dijit/MenuBar",
		"dijit/MenuBarItem",
		"dijit/MenuItem",
		"dijit/PopupMenuBarItem", 
		"dijit/DropDownMenu", 
		"dijit/MenuSeparator",
	], function(MenuBar, MenuBarItem, MenuItem, PopupMenuBarItem, DropDownMenu, MenuSeparator) {
		window.mainToolbar = new MenuBar({title: "Main Menu"});
		mainToolbar.placeAt("wrapper");
		mainToolbar.startup();  

		mainToolbar.addChild(new MenuBarItem({id: "mniDashboard", label: "Dashboard", onClick: mniDashboardClicked, disabled: true }));
		
		menuServices = new DropDownMenu({});
		menuServices.addChild(new MenuItem({id: "mniServices", label: "Full List", onClick: mniServicesClicked, disabled: true, accelKey: 's' }));
		menuServices.addChild(new MenuSeparator());
		menuServices.addChild(new MenuItem({id: "mniCommands", label: "Commands", onClick: mniCommandsClicked }));
		menuServices.addChild(new MenuItem({label: "Groups", onClick: mniGroupsClicked, disabled: true }));
		menuServices.addChild(new MenuItem({id: "mniMaintPeriods", label: "Maintenence Periods", onClick: mniMaintPeriodsClicked, disabled: true }));
		mainToolbar.addChild(new PopupMenuBarItem({label: "Services", popup: menuServices}));

		mainToolbar.addChild(new MenuBarItem({id: "mniNodes", label: "Nodes", onClick: mniNodesClicked, disabled: true})); 

		mainToolbar.addChild(new MenuBarItem({id: "mniClasses", label: "Classes", onClick: mniClassesClicked, disabled: true})); 

		menuSystem = new DropDownMenu();
		menuSystem.addChild(new MenuItem({id: "mniUsers", label: "Users", onClick: mniUsersClicked, disabled: false }));
		menuSystem.addChild(new MenuItem({id: "mniUsergroups", label: "User Groups", onClick: mniUserGroupsClicked, disabled: false }));
		menuSystem.addChild(new MenuSeparator());
		menuSystem.addChild(new MenuItem({label: "Classic Console", onClick: gotoClassic}));
		menuSystem.addChild(new MenuSeparator());
		menuSystem.addChild(new MenuItem({id: "mniPreferences", label: "Preferences", onClick: mniPreferencesClicked, disabled: true}));
		menuSystem.addChild(new MenuItem({id: "mniLogout", label: "Logout", onClick: mniLogoutClicked, disabled: true }));
		menuSystem.addChild(new MenuItem({id: "mniLogin", label: "Login", onClick: showFormLogin, disabled: true }));
		mainToolbar.addChild(new PopupMenuBarItem({label: "System", popup: menuSystem}));
	});
}

function gotoClassic() {
	window.location = "index.php";
}

function loadGetServices(services) {
	require([
	     "dijit/registry",
	     "dojo/store/Memory",
	     "dojo/domReady!" 
     ], function (registry, Store) {
    	setTitle("Services");

		console.log("gridServices", registry.byId("gridServices"))

		if (!registry.byId("gridServices")) {
			initGridServices(); 
		} 
		  
		grid = registry.byId("gridServices"); 
		grid.setStore(new Store({data: services}));

		setContentElement(grid);
	});
}

function mniDashboardClicked() {
	reqDashboard(5);
}

function loadLogout() {
	require([
		"dijit/registry"
	], function (registry) {
		reqUpdatePermissions();
	});
}

function loadLogin(res, a, b, c) {
	reqUpdatePermissions();
}

function showFormLogin() {
	require([
		"dijit/layout/ContentPane",
	], function(container) {
		var username = window.prompt("username?");
		var password = window.prompt("password");
		reqLogin(username, password);
	});
}

function errorLogin() {
	window.alert("Login failed.");
}

function reqLogin(username, password) {
	var req = newJsonReq();
	req.url = "json/authenticate";
	req.content = {
		username: username,
		password: password,
	};
	req.load = loadLogin;
	req.error = errorLogin;
	req.get();
}

function getBasePath() {
	ret = "";

	pathElements = window.location.pathname.split("/");

	pathElements.forEach(function(e, i) {
		if (i == pathElements.length - 1) {
			return;
		} else {
			ret += e + "/"
		}
	});
	
	return window.location.origin + "/" + ret
}

function newJsonReq(url) {
	return {
		url: getBasePath() + url,
		handleAs: "json", 
		error: displayError,
		get: function(loadFunc) {
			if (this.load == null) {
				this.load = loadFunc
			}

			if (!this.url.indexOf(".php") == -1) {
				this.url += ".php";
			}

			dojo.xhrGet(this); 
		} 
	}
}

function renderWidgetEvents() {}
function renderWidgetTasks() {}
function renderWidgetGraphMetrics(widget) {
	console.log(widget);
}
function renderWidgetListMetrics() {}
function renderWidgetListSubresults() {}
function renderWidgetServicesFromGroup() {}

function renderWidgetProblemServices(widget, container) {
	var req = newJsonReq();
	req.url = "json/getServices";
	req.load = function(services) {
		list = "<h2>Problem Services</h2>";

		dojo.forEach(services, function(service) {
			list += '<span class = "metricIndictator ' + service.karma.toLowerCase() + '">' + service.lastChangedRelative + '</span> ' + service.identifier + "<br />"; 
		}); 
		
		container.set("content", list); 
	};
	req.get();  
}

function renderWidgetNodes(widget, container) {
	var req = newJsonReq();
	req.url = "json/listNodes",
	req.load = function(nodes) {
		define([ 
			"dojo/_base/declare",
			"dijit/_WidgetBase",
			"dijit/_TemplatedMixin",
			"dojo/text!/upsilon/upsilon-web/src/main/php/resources/templatesclient/example.tpl"
		], function(declare, _WidgetBase, _TemplatedMixin, tpl) {
			return declare([_WidgetBase, _TemplatedMixin], {
				message: "Hello World"
			});
		});

		nodeList = "<h2>Nodes</h2><ul class = 'metricList'>";
		
		dojo.forEach(nodes, function(node) { 
			nodeList += '<li><div class = "metricIndicatorContainer"><span class = "metricIndicator ' + node.karma.toLowerCase() + '">' + node.karma + '</span><span class = "metricText">' + node.identifier + "</span></div></li>"; 
		});

		nodeList += "</ul>";
		
		container.set("content", nodeList);
	};
	
	
	req.get();
}

function loadDashboard(dashboard) {
	require([
	    "dijit/layout/StackContainer",
	    "dijit/layout/ContentPane",
	    "dijit/registry",
	    "dojo/dom-construct",
    ], function(Container, ContentPane, registry, domcon){
	    	setTitle("Dashboard: " + dashboard.dashboard.title);

		if (!registry.byId("dashboardWidgetContainer")) {
			var container = new Container({id: "dashboardWidgetContainer", class: "blockContainer"});
			container.placeAt("content");
		}
		 
		var container = registry.byId("dashboardWidgetContainer");
		
		dojo.forEach(dashboard.widgetInstances, function(widget) {
			if (!registry.byId("widget" + widget.id)) {
				var widgetContent = new ContentPane({
					id: "widget" + widget.id,
					class: "block",
				});   
				widgetContent.set("content", "<h2>" + widget.class + "</h2><div>Undefined Widget Content</div>");
				container.addChild(widgetContent);
			}   
			
			var cp = registry.byId("widget" + widget.id);
			var renderFunction = "renderWidget" + widget.class;
			console.log(renderFunction);
			window[renderFunction](widget, cp);
		});

		setContentElement(container);
		
		layoutBoxes();
	});

}

function reqDashboard(dashboard) {
	var req = newJsonReq();
	req.url = "json/getDashboard",
	req.content = { id: dashboard }, 
	req.load = loadDashboard,
	req.get();
}

function reqGetServices() {
	var req = newJsonReq();
	req.url = "json/getServices";
	req.load = loadGetServices;
	req.get();
}

function serviceGroupsModel() {
	getItem = function () {
		console.log("yoo");
	}
}

function loadGridContentView(id, data, title, gridConfig) {
	require([
	     "dijit/registry",
	     "dojo/store/Memory",
	     "dojo/domReady!" 
    ], function (registry, Store) {
	    	setTitle(title);

		if (!registry.byId(id)) {
			newGrid(gridConfig)
		} 
		  
		grid = registry.byId(id); 
		grid.setStore(new Store({data: data}));

		setContentElement(grid);
	});
}

function loadGetMaintPeriods(maintPeriods) {
	loadGridContentView("gridMaintPeriods", maintPeriods, "Maintainence Periods", {
		id: "gridMaintPeriods",
		structure: [
			{field:"id", name: "ID", width: "5%"},
			{field:"title", name: "Title"},
		],
	})
}

function iconDecorator(icon) {
	console.log(icon);
	if (icon == null) {
		return '<div>&nbsp;</div>';
	}

	return '<div style = "text-align: center"><img src = "resources/images/' + window.iconSource + '/' + icon + '"/></div>';
}

function setIconSource(dir) {
	window.iconSource = dir;
}

function loadGetCommands(commands) {
	setIconSource("serviceIcons");

	loadGridContentView("gridCommands", commands, "Commands", {
		id: "gridCommands",
		structure: [
			{field:"id", name: "ID", width: "5%"},
			{field:"icon", decorator: iconDecorator, width: '5%'},
			{field:"commandIdentifier", name: "Identifier"},
			{field:"serviceCount", name: "Services"},
		],
		filters: [
			{title: "None", filterFunc: filterCommandsNone}
		]
	})
}

function mniCommandsClicked() {
	var req = newJsonReq();
	req.url = "json/getCommands";
	req.load = loadGetCommands;
	req.get();
}

function mniGroupsClicked() {}

function mniMaintPeriodsClicked() {
	var req = newJsonReq();
	req.url = "json/listMaintPeriods";
	req.load = loadGetMaintPeriods;
	req.get();	
}

function mniServicesClicked() {
	reqGetServices();
}

function setTitle(newTitle) {
	require(["dijit/registry"], function(registry){
		registry.byId("title").setContent('<span class = "pageTitle">Upsilon &raquo; <h1>' + newTitle + '</h1></span>');
	});

	document.title = newTitle;
}

function setupHeader() {
	require([
		"dijit/layout/ContentPane",
	], function (ContentPane) {
		header = new ContentPane({
			id: "header",
			content: new ContentPane({id: "title", content: 'Untitled page'}),
		});

		header.placeAt("wrapper");
	});

	setTitle("Home");
}

function clickedTreeNode(item) {
	switchContentToGroup(item);
}

function switchContentToGroup(group) {
	setContentElement("Group:" + group.title + "<br />ID:" + group.id);
}

function setContentElement(contentToSet) {
	console.log("set content", contentToSet);
	require([
		"dijit/registry",
	], function(registry) {
		content = registry.byId("content");

		if (content.containerNode.children.length > 0) {
			if (content.containerNode.children[0].id == contentToSet.id) {
				// don't set the same content twice
				return;
			}
		}	

		content.set("content", contentToSet);
	});
}

function setupRootContainer() {
	require([
		"dijit/registry",
		"dijit/layout/BorderContainer",
		"dijit/layout/ContentPane",
		"dijit/Tree",
		"dojo/store/JsonRest",
		"dijit/tree/ObjectStoreModel",
		"dojo/store/Memory"
	], function(registry, BorderContainer, ContentPane, Tree, JsonRestStore, ObjectStoreModel, Memory) {
		if (registry.byId("navTree") == null) {
			contentBody = new ContentPane({
				id: "content",
				content: '<div style = "text-align: center; padding: 2em; ">Welcome to <strong>upsilon-web</strong></div>',
				region: "center",
				style: "padding: 0;",
			});

			store = new JsonRestStore({
				target: getBasePath() + "/json/getServiceGroup/",
				getRoot: function (onItem, onError) {
					this.get(1).then(onItem, onError);
				},
				getChildren: function(group, onComplete, onError) {
					onComplete(group.listSubgroups);
				},
				getLabel: function(group) {
					return group.title;
				},
				mayHaveChildren: function(o) {
					return true;
				}
			});

			tree = new Tree({
				id: "navTree",
				region: "center",
				model: store,
				onClick: clickedTreeNode
			});
			tree.startup();

			rootContainer = new BorderContainer({
				id: "rootContainer",
				liveSplitters: true,
			});

			contentTree = new ContentPane({ content: tree, region: "left", splitter: true, style: "width: 20%; padding: 0px;"});
			contentTree.startup();

			rootContainer.addChild(contentTree);
			rootContainer.addChild(contentBody);
			rootContainer.placeAt("wrapper");
			rootContainer.startup();
		}
	});
}

