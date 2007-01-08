{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/admin_features.tpl,v 1.13 2007/01/08 09:48:09 squareing Exp $ *}
{strip}
{form}
	{jstabs}
		{jstab title="bitweaver Settings"}
			<input type="hidden" name="page" value="{$page}" />
			{legend legend="URL Settings"}
				{foreach from=$formBit key=feature item=output}
					<div class="row">
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
					<div class="row">
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
				<div class="row">
					{formlabel label="Home page" for="bit_index"}
					{forminput}
						<select name="bit_index" id="bit_index">
							<option value="my_page"{if $gBitSystem->getConfig('bit_index') eq 'my_page'} selected="selected"{/if}>{tr}My {$gBitSystem->getConfig('site_title')} Page{/tr}</option>
							<option value="user_home"{if $gBitSystem->getConfig('bit_index') eq 'user_home'} selected="selected"{/if}>{tr}User's homepage{/tr}</option>
							<option value="group_home"{if $gBitSystem->getConfig('bit_index') eq 'group_home'} selected="selected"{/if}>{tr}Group home{/tr}</option>
							<option value="users_custom_home"{if $gBitSystem->getConfig('bit_index') eq $gBitSystem->getConfig('site_url_index')} selected="selected"{/if}>{tr}Custom home{/tr}</option>
							{foreach key=name item=package from=$gBitSystem->mPackages }
								{if $package.homeable && $package.installed}
									<option {if $gBitSystem->getConfig('bit_index') eq $package.name}selected="selected"{/if} value="{$package.name}">{$package.name|capitalize}</option>
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

				<div class="row">
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
				<div class="row">
					{formlabel label="Relative Time Display" for="site_display_reltime"}
					{forminput}
						<input type="checkbox" name="site_display_reltime" id="site_display_reltime" {if $gBitSystem->isFeatureActive('site_display_reltime')}checked="checked"{/if} />
						{formhelp note="When you enable this, the date and time display in some areas will change to the relative time since the post has been made instead of using the absolute date."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Long date format" for="site_long_date_format"}
					{forminput}
						<input type="text" name="site_long_date_format" id="site_long_date_format" value="{$gBitSystem->getConfig('site_long_date_format')|escape}" size="50"/>
						{formhelp note="Default: %A %d of %B, %Y"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Short date format" for="site_short_date_format"}
					{forminput}
						<input type="text" name="site_short_date_format" id="site_short_date_format" value="{$gBitSystem->getConfig('site_short_date_format')|escape}" size="50"/>
						{formhelp note="Default: %d %b %Y"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Long time format" for="site_long_time_format"}
					{forminput}
						<input type="text" name="site_long_time_format" id="site_long_time_format" value="{$gBitSystem->getConfig('site_long_time_format')|escape}" size="50"/>
						{formhelp note="Default: %H:%M:%S %Z"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Short time format" for="site_short_time_format"}
					{forminput}
						<input type="text" name="site_short_time_format" id="site_short_time_format" value="{$gBitSystem->getConfig('site_short_time_format')|escape}" size="50"/>
						{formhelp note="Default: %H:%M %Z"}
						{formhelp note="<strong>Online Help</strong>: <a class=\"external\" href=\"http://www.php.net/manual/en/function.strftime.php\">Date and Time Format Help</a>"}
					{/forminput}
				</div>
			{/legend}

			{legend legend="Extended Header"}
				<p class="help">{tr}To improve accessibility on your website further, you can activate the following feature. Most of these settings will not be visible to the common user but if you are using a browser such as <a class="external" href="http://elinks.or.cz">Elinks</a> or have the navigation bar active in <a class="external" href="http://www.opera.com">Opera</a> or the <a class="external" href="http://cdn.mozdev.org/linkToolbar/">linkToolbar</a> extension installed in <a class="external" href="http://www.mozilla.org">Firefox</a> these features will be visible to you. Any values left blank will not be inserted.{/tr}</p>

				{foreach from=$extendedHeader key=feature item=output}
					<div class="row">
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
				<div class="row">
					{formlabel label="Maximum records" for="max_records"}
					{forminput}
						<input size="5" type="text" name="max_records" id="max_records" value="{$gBitSystem->getConfig('max_records')|escape}" />
						{formhelp note="Maximum number of records per page in listings."}
					{/forminput}
				</div>

				{foreach from=$formMisc key=feature item=output}
					<div class="row">
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

	<div class="row submit">
		<input type="submit" name="change_prefs" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}
