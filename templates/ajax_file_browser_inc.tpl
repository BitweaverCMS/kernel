{* too much hassle to provide non-js version of this stuff for now *}
{if $gBitThemes->isJavascriptEnabled()}
	{if $fileList}
		<div style="margin-bottom:1em;cursor:hand;">
			{foreach from=$fileList.dir item=finfo}
				<div class="{cycle values="even,odd"}" style="margin-left:{$finfo.indent}px;" id="{$finfo.relpath|escape}" title="open" onclick='BitFileBrowser.browse(this.id,this.title,"{$smarty.request.ajax_path_conf}");'>
					{booticon iname="fa-folder-closed" id="image-`$finfo.relpath`" iexplain="Open"} {$finfo.name}
				</div>
				<div id="{$finfo.relpath|escape}-bitInsert"></div>
			{/foreach}

			{foreach from=$fileList.file item=finfo}
				{if $finfo.relpath}
					<div class="{cycle values="even,odd"}" style="margin-left:{$finfo.indent}px;" onclick='document.getElementById("ajax_input").value="{$finfo.relpath}"' title="{tr}Last Modified{/tr}: {$finfo.mtime|bit_long_datetime}">
						{biticon iname=text-x-generic iexplain="File"} {$finfo.name} <small class="floatright clearright">{$finfo.size|display_bytes}</small>
					</div>
				{else}
					<div class="{cycle values="even,odd"}" style="margin-left:{$finfo.indent}px;">
						{booticon iname="fa-triangle-exclamation" iexplain="Empty"} [{tr}empty{/tr}]
					</div>
				{/if}
			{/foreach}
		</div>
	{else}
		<h2 id="ajax_load_title"><a href="javascript:void(0);" onclick='BitFileBrowser.load("{$ajax_path_conf}");' style="cursor:hand;">{tr}Load Files{/tr}</a></h2>
		<div id="ajax_load"></div>
		{formhelp note="Click on the folders to open them and on the files to insert the path in the input field."}
	{/if}
{/if}
