<div class="floaticon">{bithelp}</div>

<div class="admin backup">
	<div class="header">
		<h1>{tr}Backups{/tr}</h1>
	</div>

	<div class="body">
		{if $restore eq 'y'}
			{formfeedback warning="Restoring a backup will erase all existing data before populating it with the data in the restore file. If something goes wrong you might loose all your data. We recommend that you first back up your existing database using an external tool.<br /><a href=\"`$smarty.const.KERNEL_PKG_URL`admin/backup.php?rrestore=`$restorefile`\">Click here to restore the selected database</a>"}
		{/if}

		{jstabs}
			{jstab title="Create new Backup"}
				{form legend="Create new Backup"}

					<p>{tr}Creating backups may take a long time. If the process is not completed you will see a blank screen. If so you need to increment the maximum script execution time from your php.ini file{/tr}</p>
					<p><strong>Please note that there is no ecryption added to the backup file when you create a backup.</strong></p>

					<div class="control-group submit">
						<input type="submit" class="btn btn-default" name="generate" value="{tr}Create new Backup{/tr}" />
					</div>
				{/form}
			{/jstab}

			{jstab title="Upload a Backup"}
				{form enctype="multipart/form-data" legend="Upload a Backup"}
					<div class="control-group">
						{formlabel label="Upload Backup"}
						{forminput}
							<input type="hidden" name="MAX_FILE_SIZE" value="10000000000" />
							<input name="userfile1" type="file" />
						{/forminput}
					</div>
					
					<div class="control-group submit">
						<input type="submit" class="btn btn-default" name="upload" value="{tr}upload{/tr}" />
					</div>
				{/form}
			{/jstab}
		{/jstabs}

		<table class="table data">
			<caption>{tr}List of available backups{/tr}</caption>
			<tr>
				<th>{tr}Filename{/tr}</th>
				<th>{tr}Created{/tr}</th>
				<th>{tr}Size{/tr}</th>
				<th>{tr}action{/tr}</th>
			</tr>
			{cycle values="even,odd" print=false}
			{section name=user loop=$backups}
				<tr class="{cycle}">
					<td><a href="{$smarty.const.STORAGE_PKG_URL}backups/{$backups[user].filename}" title="{$backups[user].filename}">{$backups[user].filename|truncate:20:"...":true}</a></td>
					<td>{$backups[user].created|bit_short_datetime}</td>
					<td>{$backups[user].size|string_format:"%.2f"} Mb</td>
					<td class="actionicon">
						<a href="{$smarty.const.KERNEL_PKG_URL}admin/backup.php?remove={$backups[user].filename}">{tr}remove{/tr}</a>
						<a href="{$smarty.const.KERNEL_PKG_URL}admin/backup.php?restore={$backups[user].filename}">{tr}restore{/tr}</a>
					</td>
				</tr>
			{sectionelse}
				<tr class="norecords"><td colspan="5">{tr}No records found{/tr}</td></tr>
			{/section}
		</table>
	</div> {* end .body *}
</div> {* end .admin *}
