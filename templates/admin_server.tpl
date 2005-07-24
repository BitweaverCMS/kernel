{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/admin_server.tpl,v 1.1.1.1.2.2 2005/07/24 11:59:48 squareing Exp $ *}
{strip}
{form}
	<input type="hidden" name="page" value="{$page}" />

	{jstabs}
		{jstab title="Server Settings"}
			{legend legend="Page Setup"}
				<div class="row">
					{formfeedback warning="After changing the browser title, you might have to login again."}
					{formlabel label="Site title" for="siteTitle"}
					{forminput}
						<input size="50" type="text" name="siteTitle" id="siteTitle" value="{$siteTitle|escape}" />
						{formhelp note="The title of your site. The title appears in the banner area and in the browsers top bar."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site slogan" for="site_slogan"}
					{forminput}
						<input size="50" type="text" name="site_slogan" id="site_slogan" value="{$gBitSystemPrefs.site_slogan|escape}" />
						{formhelp note="This slogan is (usually) shown below the site title."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site Description" for="site_description"}
					{forminput}
						<input size="50" type="text" name="site_description" id="site_description" maxlength="180" value="{$gBitSystemPrefs.site_description|escape}" />
						{formhelp note="This text is used to describe your site to search engines. Some search engines use this information to create a summary of your site.<br />The text you enter here will not be visible anywhere.<br />The Limit for search engines is <strong>180</strong> characters."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site Keywords" for="site_keywords"}
					{forminput}
						<textarea cols="80" rows="5" name="site_keywords" id="site_keywords">{$gBitSystemPrefs.site_keywords|escape}</textarea>
						{formhelp note="Keywords are used to tell search engines what your page is for. Based on these keywords, your site can be categorised better and searches will give you higher rankings.<br />The text you enter here will not be visible anywhere.<br />The Limit for search engines is <strong>900</strong> characters.<br />(due to db restrictions, this list of words will be cut off at 250 chars)."}
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="serverTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Miscellaneous"}
			{legend legend="Server Settings"}
				<div class="row">
					{formfeedback warning="Please make sure you know what you are doing as setting this wrongly could prevent you from logging in again."}
					{formlabel label="Server name" for="feature_server_name"}
					{forminput}
						<input size="50" type="text" name="feature_server_name" id="feature_server_name" value="{$gBitSystemPrefs.feature_server_name|escape}" />
						{formhelp note="This value should be something like <strong>yourhome.com</strong> and is used for absolute URIs.<br />This setting does <strong>not</strong> require a trailing slash."}
					{/forminput}
				</div>

				<div class="row">
					{if !$gBitSystem->hasValidSenderEmail()}
						{formfeedback error="Site emailer return address is not valid!"}
					{/if}
					{formlabel label="Site Emailer return address" for="sender_email"}
					{forminput}
						<input size="50" type="text" name="sender_email" id="sender_email" value="{$gBitSystemPrefs.sender_email|escape}" />
						{formhelp note="When users recieve an automatically generated email, this is the email address that will be used as return address."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Centralized user upload directory" for="centralized_upload_dir"}
					{forminput}
						<input size="50" type="text" name="centralized_upload_dir" id="centralized_upload_dir" value="{$centralized_upload_dir|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Temporary directory" for="tmpDir"}
					{forminput}
						<input size="50" type="text" name="tmpDir" id="tmpDir" value="{$tmpDir|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formfeedback warning="After changing this setting you might have to log in again."}
					{formlabel label="Store session data in database" for="session_db"}
					{forminput}
						{html_checkboxes name="session_db" values="y" checked=$gBitSystemPrefs.session_db labels=false id="session_db"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Session lifetime in seconds" for="session_lifetime"}
					{forminput}
						<input size="5" type="text" name="session_lifetime" id="session_lifetime" value="{$gBitSystemPrefs.session_lifetime}" />
						{formhelp note="well, i think it's in seconds - needs to be confirmed."}
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="serverTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Site Access"}
			{legend legend="Site Access"}
				<div class="row">
					{formlabel label="Disallow access to the site" for="site_closed"}
					{forminput}
						{html_checkboxes name="site_closed" values="y" checked=$gBitSystemPrefs.site_closed labels=false id="site_closed"}
						{formhelp note="Disallow access to the site (except for those with permission)"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Message to display when site is closed" for="site_closed_msg"}
					{forminput}
						<input type="text" name="site_closed_msg" id="site_closed_msg" value="{$gBitSystemPrefs.site_closed_msg}" size="50" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Disallow access when load is above the threshold" for="use_load_threshold"}
					{forminput}
						{html_checkboxes name="use_load_threshold" values="y" checked=$BitSystemPrefs.use_load_threshold labels=false id="use_load_threshold"}
						{formhelp note="Disallow access when load is above the threshold (except for those with permission)"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Max average server load threshold in the last minute" for="load_threshold"}
					{forminput}
						<input type="text" name="load_threshold" id="load_threshold" value="{$gBitSystemPrefs.load_threshold}" size="5" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Message to display when server is too busy" for="site_busy_msg"}
					{forminput}
						<input type="text" name="site_busy_msg" id="site_busy_msg" value="{$gBitSystemPrefs.site_busy_msg}" size="50" />
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="siteTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Proxy"}
			{legend legend="Proxy Settings"}
				<div class="row">
					{formlabel label="Use proxy" for="use_proxy"}
					{forminput}
						{html_checkboxes name="use_proxy" values="y" checked=$use_proxy labels=false id="use_proxy"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Proxy Host" for="proxy_host"}
					{forminput}
						<input type="text" name="proxy_host" id="proxy_host" value="{$proxy_host|escape}" size="50" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Proxy port" for="proxy_port"}
					{forminput}
						<input type="text" name="proxy_port" id="proxy_port" value="{$proxy_port|escape}" size="50" />
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="proxyTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}

{/strip}
