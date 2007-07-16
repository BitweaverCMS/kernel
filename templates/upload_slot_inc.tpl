{strip}
{assign var=id value=$smarty.request.upload_id|default:0}
<div class="row">
	{formlabel label="Title" for="title-$id"}
	{forminput}
		<input type="text" name="{$name|default:'file'}[{$id}][title]" id="title-{$id}" size="50" />
		<br />
		<input type="file" name="upload-{$id}" id="upload-{$id}" size="35" />
	{/forminput}
</div>

<div class="row">
	{formlabel label="Description" for="edit-$id"}
	{forminput}
		<textarea rows="2" cols="50" name="{$name|default:'file'}[{$id}][edit]" id="edit-{$id}"></textarea>
	{/forminput}
</div>

{if $gBitThemes->isJavascriptEnabled()}
	<div id="upload-slot-{$id+1}">
		<div class="row">
			{forminput}
				<a href="javascript:ajax_updater('upload-slot-{$id+1}', '{$smarty.const.KERNEL_PKG_URL}upload_slot_inc.php', 'upload_id={$id+1}&amp;name={$name}')">
					{biticon iname=large/list-add iexplain="Add upload slot" iforce="icon"}
				</a>
			{/forminput}
		</div>
		<hr />
	</div>
{/if}
{/strip}
