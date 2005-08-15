{bitmodule title="$moduleTitle" name="clock"}
	<div style="text-align:center;">
		{if $modParams.src eq 'text'}
			{$smarty.now|bit_short_time}
		{elseif $modParams.src eq 'javascript'}
			{literal}
			<div id="js_clock" style="height:2.4em;font-family:monospace;">
				<script language="javascript">
					function js_clock(){
						var clock_time = new Date();
						var clock_hours = clock_time.getHours();
						var clock_minutes = clock_time.getMinutes();
						var clock_seconds = clock_time.getSeconds();
						if (clock_hours < 10){clock_hours = "0" + clock_hours;}
						if (clock_minutes < 10){clock_minutes = "0" + clock_minutes;}
						if (clock_seconds < 10){clock_seconds = "0" + clock_seconds;}
						var clock_div = document.getElementById('js_clock');
						clock_div.innerHTML = clock_hours + ":" + clock_minutes + ":" + clock_seconds;
						setTimeout("js_clock()", 1000);
					}
					js_clock();
				</script>
			</div>
			{/literal}
		{elseif $modParams.src eq 'javascript12'}
			{literal}
			<div id="js_clock" style="font-family:monospace;">
				<script language="javascript">
					function js_clock(){
						var clock_time = new Date();
						var clock_hours = clock_time.getHours();
						var clock_minutes = clock_time.getMinutes();
						var clock_seconds = clock_time.getSeconds();
						var clock_suffix = "AM";
						if (clock_hours > 11){clock_suffix = "PM";clock_hours = clock_hours - 12;}
						if (clock_hours == 0){clock_hours = 12;}
						if (clock_hours < 10){clock_hours = "0" + clock_hours;}
						if (clock_minutes < 10){clock_minutes = "0" + clock_minutes;}
						if (clock_seconds < 10){clock_seconds = "0" + clock_seconds;}
						var clock_div = document.getElementById('js_clock');
						clock_div.innerHTML = clock_hours + ":" + clock_minutes + ":" + clock_seconds + " " + clock_suffix;
						setTimeout("js_clock()", 1000);
					}
					js_clock();
				</script>
			</div>
			{/literal}
		{else}
			<embed {if $modParams.width or $modParams.height}style="width:{$modParams.width}px;height:{$modParams.height}px;"{/if} src="{$modParams.src|default:"http://www.internettime.com/Learning/relog.swf"}" quality="high" wmode="transparent" bgcolor="{$modParams.color|default:"#ffffff"}" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		{/if}
	</div>
{/bitmodule}
