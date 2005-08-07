<div class="floaticon">{bithelp}</div>

<div class="admin notifications">
	<div class="header">
		<h1>{tr}EMail notifications{/tr}</h1>
	</div>

	<div class="body">
		{form legend="Add Notification"}
			<input type="hidden" name="find" value="{$find|escape}" />
			<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
			<input type="hidden" name="offset" value="{$offset|escape}" />

			<div class="row">
				{formlabel label="Event" for="event"}
				{forminput}
					<select name="event" id="event">
						{foreach from=$events key=ename item=etext}
							<option value="{$ename}">{$etext}</option>
						{/foreach}
					</select>
					{formhelp note="Pick the event that triggers an email dispatch."}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Email address" for="femail"}
				{forminput}
					<input type="text" id="femail" name="email" />
					<br />
					<a href="#" onclick="javascript:document.getElementById('femail').value='{$cuser_mail}'">{tr}use your email{/tr}</a>
					<br />
					<a href="#" onclick="javascript:document.getElementById('femail').value='{$admin_mail}'">{tr}use admin email{/tr}</a>
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="add" value="{tr}add{/tr}" />
			</div>
		{/form}

		{minifind sort_mode=$sort_mode find=$find}

		<table class="data">
			<caption>{tr}Active Notifications{/tr}</caption>
			<tr>
				<th>{smartlink ititle="Event" isort="event" offset=$offset idefault=1}</th>
				<th>{smartlink ititle="Object" isort="object" offset=$offset}</th>
				<th>{smartlink ititle="Email" isort="email" offset=$offset}</th>
				<th>{tr}action{/tr}</th>
			</tr>

			{section name=user loop=$channels}
				<tr class="{cycle values='odd,even'}">
					<td>{$channels[user].event}</td>
					<td>{$channels[user].object}</td>
					<td>{$channels[user].email}</td>
					<td class="actionicon">{smartlink ititle="remove" ibiticon="liberty/delete" offset=$offset removeevent=`$channels[user].event` object=`$channels[user].object` email=`$channels[user].email`}</td>
				</tr>
			{sectionelse}
				<tr class="norecords"><td colspan="4">{tr}No records found{/tr}</td></tr>
			{/section}
		</table>

		{pagination}
	</div><!-- end .body -->
</div><!-- end .notifications -->
