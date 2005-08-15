{bitmodule title="$moduleTitle" name="clock"}
	<div style="text-align:center;">
		<embed {if $modParams.width or $modParams.height}style="width:{$modParams.width}px;height:{$modParams.height}px;"{/if} src="{$modParams.src|default:"http://www.internettime.com/Learning/relog.swf"}" quality="high" wmode="transparent" bgcolor="{$modParams.color|default:"#ffffff"}" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</div>
{/bitmodule}
