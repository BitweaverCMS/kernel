{strip}
{assign var=id value=$smarty.request.upload_id|default:0}
{assign var=hash_key value=$smarty.request.hash_key|default:$hash_key}
<div class="control-group">
	{formlabel label="Title" for="title-$id"}
	{forminput}
		<input type="text" name="{$hash_key|default:'file'}[{$id}][title]" id="title-{$id}" size="40" />
	{/forminput}
</div>

<div class="control-group">
	{formlabel label="File" for="upload-$id"}
	{forminput}
		<input type="file" name="upload-{$id}" id="upload-{$id}" size="30" />
	{/forminput}
</div>

<div class="control-group">
	{formlabel label="Description" for="edit-$id"}
	{forminput}
		<textarea rows="2" cols="40" name="{$hash_key|default:'file'}[{$id}][edit]" id="edit-{$id}"></textarea>
	{/forminput}
</div>

{if $gBitThemes->isJavascriptEnabled()}
	<div id="upload-slot-{$id+1}">
		<div class="control-group">
			{forminput}
				<a href="javascript:void(0);" onclick="BitAjax.updater('upload-slot-{$id+1}', '{$smarty.const.KERNEL_PKG_URL}upload_slot_inc.php', 'upload_id={$id+1}&amp;hash_key={$hash_key}')">
					{biticon iname=large/list-add iexplain="Add upload slot" iforce="icon"}
				</a>
			{/forminput}
		</div>
		<hr />
	</div>
{/if}
{/strip}
