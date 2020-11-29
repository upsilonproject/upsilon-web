<div>
</div>

<div class = "box">
<h2>upsilon-web</h2>

<p>
This is the upsilon-web installer, it checks the system for any obvious errors and generates a config.php file for you. It doesn't make any automated changes to the site, just helps things along a little.
</p>
</div>

<div class = "box">
{if isset($configFile)}
<h2>Your config.php file</h2>
<p class = "formValidationError">The installer was unable to automatically save your config file, this is normally because of the webserver security configuration. Specifically, the error is: {$configFailReason}.</p>
<p>The config file needs to be copied and pasted in to a file (using notepad, or similar), saved as config.php and uploaded to includes/config.php using SFTP, SCP, FTP or similar.</p>

<textarea rows = "10" cols = "40" style = "width: 100%;">{$configFile|htmlentities}</textarea>
{else}
<h2>System tests</h2>
<p>These are a few basic tests that check your system prior to installation. If any of the items below are "FAIL", you should fix them.</p>

<table>
<thead>
<tr>
<th>Test</th>
<th>Result</th>
</tr>
</thead>

<tbody>
{foreach from = $installationTests key = "testName" item = "testResult"}
<tr>
<td>{$testName}</td>
{if $testResult}
<td class = "good"><strong>PASS</strong></td>
{else}
<td class = "bad"><strong>FAIL</strong></td>
{/if}
</tr>
{/foreach}
</tbody>
</table>
{/if}
<p>Read the <a href = "http://docs.upsilonproject.io/#_installation">installation guide</a> for more help.</p>
{if isset($configFile)}
<li><a href = "installer.php">Start from scratch</a>, loosing all changes. </li>
<li><a href = "/">Return to index</a></li>
{/if}
</ul>
</div>

