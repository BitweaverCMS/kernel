<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
  <base href="{$info.url}" />
	<title>Cached: {$info.url}</title>
  </head>
  <body>
<br />
<table class="other">
<tr><td>{tr}URL{/tr}</td><td>{$info.url}</td></tr>
<tr><td>{tr}Cached{/tr}</td><td>{$info.refresh|bit_long_datetime}</td></tr>
<tr><td colspan="2">{tr}This is a cached version of the page.{/tr} 
<a class="extcache" href="{$ggcacheurl}">{tr}Click here to view the Google cache of the page instead.{/tr}</a>
</td></tr>
</table>
</div>
<br />
<div class="cachedpage">
	{$info.data}
</div>
</body>
</html>
