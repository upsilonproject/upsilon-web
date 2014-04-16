{if $tutorialMode}
<div class = "box tutorialMessage">
	<p><strong>Classes</strong> define a standard set of service requirements. By using classes, it is easy to identify what is <em>not</em> being currently checked.<p>
	<p>For example, <tt>database servers</tt> are a class, which should have the requirements of <tt>free disk space</tt>, a running <tt>database service</tt> and <tt>recent software updates</tt>.</p>
	<p style = "font-size: x-small " class = "subtle">This message is being shown because <a href = "preferences.php">tutorial mode</a> is enabled.</p>
</div>
{/if}

<div class = "box" style = "vertical-align: top;">
		<div style = "display:inline-block; width: 40%; vertical-align: top;">
			<h2>
				{if !empty($itemClass.icon)}
				<img src = "resources/images/serviceIcons/{$itemClass.icon}" alt = "serviceIcon" class = "inlineIcon" />
				{/if}

				Detail
			</h2>
			<p><strong>Title:</strong> 
				{foreach from = $listParents item = class}
					<a href = "listClasses.php?id={$class.id}">{$class.title}</a> &raquo;
				{/foreach}
				{$itemClass.title}
			</p>
		</div>

		{if $listRequirements|@count gt 0}
		<div style = "display: inline-block; width: 40%; vertical-align: top;">
			<h3>Requirements ({$listRequirements|@count})</h3>

			<ul>
			{foreach from = $listRequirements item = itemRequirement}
				<li>{$itemRequirement.title} [<a href = "deleteClassRequirement.php?requirement={$itemRequirement.id}">X</a>]</li>
			{/foreach}
			</ul>
		</div>
		{/if}
</div>

<div class = "box">
<h2>Sub Classes</h2>

{if $listSubClasses|@count eq 0}
	<p>No child classes.</p>
{else}
	<p style = "text-align: left">
	{include file = "listClassesTree.tpl" listSubClasses = $listSubClasses}
	</p>
{/if}
</div>

<div class = "box">
<h2>All Instances</h2>

{if $listInstances|@count == 0}
	<p>No class instances. <a href = "createClassInstance.php?parent={$itemClass.id}">Create class instance</a></p>
{else}
<table class = "dataTable hover">
	<thead>
		<tr>
			<th>ID</th>
			<th>Instance title</th>
			<th>Good / Assigned</th>
			<th>Assigned / Requirements</th>
		</tr>
	</thead>

	<tbody>
		{foreach from = $listInstances item = itemInstance}
		<tr>
			<td>{$itemInstance.id}</td>
			<td><a href = "viewClassInstance.php?id={$itemInstance.id}">{$itemInstance.title}</a></td>
			<td class = "{$itemInstance.assignedKarma}">{$itemInstance.goodCount} out of {$itemInstance.assignedCount}</td>
			<td class = "{$itemInstance.overallKarma}">{$itemInstance.assignedCount} / {$itemInstance.totalCount}</td>
		</tr>
		{/foreach}
	<tbody>
</table>
{/if}
</div>
