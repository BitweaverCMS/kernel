{* $Header$ *}
{strip}
{form}
	{jstabs}
		{jstab title="bitweaver Settings"}
			<input type="hidden" name="page" value="{$page}" />
			{legend legend="URL Settings"}
				{foreach from=$formBit key=feature item=output}
					<div class="control-group">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
			{/legend}

			{legend legend="bitweaver Help Features"}
				{foreach from=$formHelp key=feature item=output}
					<div class="control-group">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
			{/legend}
		{/jstab}

		{jstab title="Homepage Settings"}
			{legend legend="Homepage Settings"}
				<div class="control-group">
					{formlabel label="Home page" for="bit_index"}
					{forminput}
						<select name="bit_index" id="bit_index">
							<option value="my_page"{if $gBitSystem->getConfig('bit_index') eq 'my_page'} selected="selected"{/if}>{tr}My {$gBitSystem->getConfig('site_title')} Page{/tr}</option>
							<option value="user_home"{if $gBitSystem->getConfig('bit_index') eq 'user_home'} selected="selected"{/if}>{tr}User's homepage{/tr}</option>
							{if $role_model }
								<option value="role_home"{if $gBitSystem->getConfig('bit_index') eq 'role_home'} selected="selected"{/if}>{tr}Role home{/tr}</option>
							{else}
								<option value="group_home"{if $gBitSystem->getConfig('bit_index') eq 'group_home'} selected="selected"{/if}>{tr}Group home{/tr}</option>
							{/if}
							<option value="users_custom_home"{if $gBitSystem->getConfig('bit_index') eq $gBitSystem->getConfig('site_url_index')} selected="selected"{/if}>{tr}Custom home{/tr}</option>
							{foreach key=name item=package from=$gBitSystem->mPackages}
								{if $package.homeable && $package.installed}
									<option {if $gBitSystem->getConfig('bit_index') eq $package.dir}selected="selected"{/if} value="{$package.dir}">{$package.dir|capitalize}</option>
								{/if}
							{/foreach}
						</select>
						{formhelp note="Pick your site's homepage. This is where they will be redirected, when they access a link to your homepage.
							<dl><dt>My bitweaver Page</dt><dd>This page contains all links the user can access with his/her current permissions.</dd>
								<dt>User's Homepage</dt><dd>This is the user's public homepage</dd>
								<dt>Group Home</dt><dd>You can define an individual home page for a group of users using this option. To define home pages, please access the <em>Groups and Permissions</em>.</dd>
								<dt>Custom Home</dt><dd>You can define any url as your bitweaver homepage. This could be an introductory page with links or a flash introduction...</dd>
								<dt>Package Homes</dt><dd>Here you can set a particular package that will serve as your home page. If you want to select an individual homepage from the exisiting ones, please access the <br /><em>Administration --> 'Package' --> 'Package' Settings</em> page.</dd>
							</dl>"}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="URI for custom home" for="site_url_index"}
					{forminput}
						<input type="text" id="site_url_index" name="site_url_index" value="{$gBitSystem->getConfig('site_url_index')|escape}" size="50" />
						{formhelp note="Use a specific URI to direct users to a particular page when accessing your site. Can be used to have an introductory page.<br />To activate this, please select <em>Custom home</em> above."}
					{/forminput}
				</div>
			{/legend}
		{/jstab}

		{jstab title="Miscellaneous"}
			{legend legend="Date and Time Formats"}
				<div class="control-group">
					{formlabel label="Relative Time Display" for="site_display_reltime"}
					{forminput}
						<input type="checkbox" name="site_display_reltime" id="site_display_reltime" {if $gBitSystem->isFeatureActive('site_display_reltime')}checked="checked"{/if} />
						{formhelp note="If enabled, the date and time will be displayed relative to the time of posting (on occasion), e.g., 'Yesterday' instead of 'January 1, 1970'. "}
					{/forminput}
				</div>
				
				<div class="control-group">
					{formlabel label="Long date" for="site_long_date_format"}
					{forminput}
						<input type="text" name="site_long_date_format" id="site_long_date_format" value="{$gBitSystem->getConfig('site_long_date_format')|escape}" size="50"/>
						{formhelp note="Default: %A %d of %B, %Y"}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Short date" for="site_short_date_format"}
					{forminput}
						<input type="text" name="site_short_date_format" id="site_short_date_format" value="{$gBitSystem->getConfig('site_short_date_format')|escape}" size="50"/>
						{formhelp note="Default: %d %b %Y"}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Long time" for="site_long_time_format"}
					{forminput}
						<input type="text" name="site_long_time_format" id="site_long_time_format" value="{$gBitSystem->getConfig('site_long_time_format')|escape}" size="50"/>
						{formhelp note="Default: %H:%M:%S %Z"}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Short time" for="site_short_time_format"}
					{forminput}
						<input type="text" name="site_short_time_format" id="site_short_time_format" value="{$gBitSystem->getConfig('site_short_time_format')|escape}" size="50"/>
						{formhelp note="Default: %H:%M %Z"}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Long date and time" for="site_long_datetime_format"}
					{forminput}
						<input type="text" name="site_long_datetime_format" id="site_long_datetime_format" value="{$gBitSystem->getConfig('site_long_datetime_format')|escape}" size="50"/>
						{formhelp note="Default: %A %d of %B, %Y (%H:%M:%S %Z)"}
					{/forminput}
				</div>
				<div class="control-group">
					{formlabel label="Short date and time" for="site_short_datetime_format"}
					{forminput}
						<input type="text" name="site_short_datetime_format" id="site_short_datetime_format" value="{$gBitSystem->getConfig('site_short_datetime_format')|escape}" size="50"/>
						{formhelp note="Default: %a %d of %b, %Y (%H:%M %Z)"}
					{/forminput}
				</div>
				<div class="control-group">
					{formlabel label="Online Help:"}
					{forminput}
						{formhelp note="<a class=\"external\" href=\"http://www.php.net/manual/`$bitlanguage`/function.strftime.php\">Conversion specifiers for date and time formats</a> (php.net)"}
					{/forminput}
				</div>

			{/legend}

			{legend legend="Extended Header"}
				<p class="help">{tr}To improve accessibility on your website further, you can activate the following feature. Most of these settings will not be visible to the common user but if you are using a browser such as <a class="external" href="http://elinks.or.cz">Elinks</a> or have the navigation bar active in <a class="external" href="http://www.opera.com">Opera</a> or the <a class="external" href="http://cdn.mozdev.org/linkToolbar/">linkToolbar</a> extension installed in <a class="external" href="http://www.mozilla.org">Firefox</a> these features will be visible to you. Any values left blank will not be inserted.{/tr}</p>

				{foreach from=$extendedHeader key=feature item=output}
					<div class="control-group">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{if $output.type == 'checkbox'}
								{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
							{elseif $output.type == 'text'}
								<input size="50" type="text" name="{$feature}" id="{$feature}" value="{$gBitSystem->getConfig($feature)|escape}" />
							{/if}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
			{/legend}

			{legend legend="Other stuff"}
				<div class="control-group">
					{formlabel label="Maximum records" for="max_records"}
					{forminput}
						<input size="5" type="text" name="max_records" id="max_records" value="{$gBitSystem->getConfig('max_records')|escape}" />
						{formhelp note="Maximum number of records per page in listings."}
					{/forminput}
				</div>

				{foreach from=$formMisc key=feature item=output}
					<div class="control-group">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
			{/legend}

		{/jstab}
	{/jstabs}

	<div class="control-group submit">
		<input type="submit" class="btn" name="change_prefs" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
