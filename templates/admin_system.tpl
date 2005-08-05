{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin system">
	<div class="header">
		<h1>{tr}Cache Administration{/tr}</h1>
	</div>

	<div class="body">
		<table class="data">
			<tr>
				<th colspan="4">{tr}Exterminator{/tr}</th>
			</tr>
			<tr class="odd">
				<td style="width:55%;"><strong>{$smarty.const.TEMP_PKG_PATH}lang/</strong></td>
				<td style="width:15%; text-align:right;">{tr}{$du.lang.cant} files{/tr}</td>
				<td style="width:15%; text-align:right;">{$du.lang.total|kbsize}</td>
				<td style="width:15%; text-align:right;"><a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php?do=lang_cache">{tr}Empty{/tr}</a></td>
			</tr>
			<tr class="even">
				<td><strong>{$smarty.const.TEMP_PKG_PATH}templates_c/</strong></td>
				<td style="text-align:right;">{tr}{$du.templates_c.cant} files{/tr}</td>
				<td style="text-align:right;">{$du.templates_c.total|kbsize}</td>
				<td style="text-align:right;"><a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php?do=templates_c">{tr}Empty{/tr}</a></td>
			</tr>
			<tr class="odd">
				<td><strong>{$smarty.const.TEMP_PKG_PATH}modules/cache/</strong></td>
				<td style="text-align:right;">{tr}{$du.modules.cant} files{/tr}</td>
				<td style="text-align:right;">{$du.modules.total|kbsize}</td>
				<td style="text-align:right;"><a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php?do=modules_cache">{tr}Empty{/tr}</a></td>
			</tr>
		</table>

		<br />

		<table class="data">
			<tr><th colspan="4">{tr}{$langdir} - Templates compiler{/tr}</th></tr>
			{foreach key=key item=item from=$templates}
				<tr class="{cycle values='odd,even'}">
					<td style="width:55%;"><strong>{$key}</strong></td>
					<td style="width:15%; text-align:right;">{tr}{$item.cant} files{/tr}</td>
					<td style="width:15%; text-align:right;">{$item.total|kbsize}</td>
					<td style="width:15%; text-align:right;"><a href="{$smarty.const.KERNEL_PKG_URL}admin/admin_system.php?compiletemplates={$key}">{tr}Compile{/tr}</a></td>
				</tr>
			{/foreach}
		</table>
	</div> {* end .body *}
</div> {* end .admin *}
{/strip}
