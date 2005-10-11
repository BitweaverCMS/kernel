<a class="pagetitle" href="{$gBitLoc.XMLRPC_PKG_URL}send_objects.php">{tr}Send objects{/tr}</a>
  
<div class="floaticon">{bithelp}</div>

{if $msg}
<div class="admin box">
<div class="boxtitle">{tr}Transmission results{/tr}</div>
<div class="boxcontent">{$msg}</div>
</div>
{/if}
<br />
<div class="admin box">
<div class="boxtitle">{tr}Send objects to this site{/tr}</div>
<div class="boxcontent">
<form method="post" action="{$gBitLoc.XMLRPC_PKG_URL}send_objects.php">
<input type="hidden" name="sendpages" value="{$form_sendpages|escape}" />
<input type="hidden" name="sendarticles" value="{$form_sendarticles|escape}" />

<table class="panel">
<tr><td>{tr}site{/tr}:</td><td><input type="text" name="site" value="{$site|escape}" /></td></tr>
<tr><td>{tr}path{/tr}:</td><td><input type="text" name="path" value="{$path|escape}" /></td></tr>
<tr><td>{tr}username{/tr}:</td><td><input type="text" name="username" value="{$username|escape}" /></td></tr>
<tr><td>{tr}password{/tr}:</td><td><input type="password" name="password" value="{$password|escape}" /></td></tr>
<tr class="panelsubmitrow"><td colspan="2"><input type="submit" name="send" value="{tr}send{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
<br />

<div class="admin box">
<div class="boxtitle">{tr}Filter{/tr}</div>
<div class="boxcontent">
<form action="{$gBitLoc.XMLRPC_PKG_URL}send_objects.php" method="post">
<input type="hidden" name="sendarticles" value="{$form_sendarticles|escape}" />
<input type="hidden" name="sendpages" value="{$form_sendpages|escape}" />
{tr}filter{/tr}:<input type="text" name="find" /><input type="submit" name="filter" value="{tr}filter{/tr}" /><br />
</form>
</div>
</div>
<br />

{if $gBitUser->hasPermission( 'bit_p_send_pages' )}
<div class="admin box">
<div class="boxtitle">{tr}Send Wiki Pages{/tr}</div>
<div class="boxcontent">
<div class="admin box">
<b>{tr}Pages{/tr}</b>: 
{section name=ix loop=$sendpages}
	{$sendpages[ix]}
{/section}
</div>

<form action="{$gBitLoc.XMLRPC_PKG_URL}send_objects.php" method="post">
<input type="hidden" name="sendpages" value="{$form_sendpages|escape}" />
<input type="hidden" name="sendarticles" value="{$form_sendarticles|escape}" />
<input type="hidden" name="site" value="{$site|escape}" />
<input type="hidden" name="path" value="{$path|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="username" value="{$username|escape}" />
<input type="hidden" name="password" value="{$password|escape}" />
<select name="page_name">
{section name=ix loop=$pages}
<option value="{$pages[ix].page_name|escape}">{$pages[ix].page_name}</option>
{/section}
</select>
<input type="submit" name="addpage" value="{tr}add page{/tr}" />
<input type="submit" name="clearpages" value="{tr}clear{/tr}" />
</form>
</div>
</div>
{/if}

<br />

{if $gBitUser->hasPermission( 'bit_p_send_articles' )}
<div class="admin box">
<div class="boxtitle">{tr}Send Articles{/tr}</div>
<div class="boxcontent">
<div class="admin box">
<b>{tr}Articles{/tr}</b>:
{section name=ix loop=$sendarticles}
{$sendarticles[ix]}
{/section}
</div>

<form action="{$gBitLoc.XMLRPC_PKG_URL}send_objects.php" method="post">
<input type="hidden" name="sendarticles" value="{$form_sendarticles|escape}" />
<input type="hidden" name="sendpages" value="{$form_sendpages|escape}" />
<input type="hidden" name="site" value="{$site|escape}" />
<input type="hidden" name="path" value="{$path|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="username" value="{$username|escape}" />
<input type="hidden" name="password" value="{$password|escape}" />
<select name="article_id">
{section name=ix loop=$articles}
<option value="{$articles[ix].article_id|escape}">{$articles[ix].title}</option>
{/section}
</select>
<input type="submit" name="addarticle" value="{tr}add article{/tr}" />
<input type="submit" name="cleararticles" value="{tr}clear{/tr}" />
</form>
</div>
</div>
{/if}
