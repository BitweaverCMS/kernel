{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/admin_server.tpl,v 1.11 2006/06/30 14:14:16 spiderr Exp $ *}
{strip}
{form}
	<input type="hidden" name="page" value="{$page}" />

	{jstabs}
		{jstab title="Server Settings"}
			{legend legend="Page Setup"}
				<div class="row">
					{formfeedback warning="After changing the browser title, you might have to login again."}
					{formlabel label="Site title" for="site_title"}
					{forminput}
						<input size="50" type="text" name="site_title" id="site_title" value="{$gBitSystem->getConfig('site_title')|escape}" />
						{formhelp note="The title of your site. The title appears in the banner area and in the browsers top bar."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site slogan" for="site_slogan"}
					{forminput}
						<input size="50" type="text" name="site_slogan" id="site_slogan" value="{$gBitSystem->getConfig('site_slogan')|escape}" />
						{formhelp note="This slogan is (usually) shown below the site title."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site Description" for="site_description"}
					{forminput}
						<input size="50" type="text" name="site_description" id="site_description" maxlength="180" value="{$gBitSystem->getConfig('site_description')|escape}" />
						{formhelp note="This text is used to describe your site to search engines. Some search engines use this information to create a summary of your site.<br />The text you enter here will not be visible anywhere.<br />The Limit for search engines is <strong>180</strong> characters."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Site Keywords" for="site_keywords"}
					{forminput}
						<textarea cols="50" rows="5" name="site_keywords" id="site_keywords">{$gBitSystem->getConfig('site_keywords')|escape}</textarea>
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
					{formlabel label="Server name" for="kernel_server_name"}
					{forminput}
						<input size="50" type="text" name="kernel_server_name" id="kernel_server_name" value="{$gBitSystem->getConfig('kernel_server_name')|escape}" />
						{formhelp note="This value should be something like <strong>yourhome.com</strong> and is used for absolute URIs.<br />This setting does <strong>not</strong> require a trailing slash."}
					{/forminput}
				</div>

				<div class="row">
					{if !$gBitSystem->hasValidSenderEmail()}
						{formfeedback error="Site emailer return address is not valid!"}
					{/if}
					{formlabel label="Site Emailer return address" for="site_sender_email"}
					{forminput}
						<input size="50" type="text" name="site_sender_email" id="site_sender_email" value="{$gBitSystem->getConfig('site_sender_email')|escape}" />
						{formhelp note="When users recieve an automatically generated email, this is the email address that will be used as return address."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Centralized user upload directory" for="site_upload_dir"}
					{forminput}
						<input size="50" type="text" name="site_upload_dir" id="site_upload_dir" value="{$gBitSystem->getConfig('site_upload_dir',$smarty.const.STORAGE_PKG_URL)|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Temporary directory" for="site_temp_dir"}
					{forminput}
						<input size="50" type="text" name="site_temp_dir" id="site_temp_dir" value="{$gBitSystem->getConfig('site_temp_dir',$smarty.const.TEMP_PKG_PATH)|escape}" />
					{/forminput}
				</div>

				<div class="row">
					{formfeedback warning="After changing this setting you might have to log in again."}
					{formlabel label="Store session data in database" for="site_store_session_db"}
					{forminput}
						{html_checkboxes name="site_store_session_db" values="y" checked=$gBitSystem->getConfig('site_store_session_db') labels=false id="site_store_session_db"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Session lifetime in seconds" for="site_session_lifetime"}
					{forminput}
						<input size="5" type="text" name="site_session_lifetime" id="site_session_lifetime" value="{$gBitSystem->getConfig('site_session_lifetime')}" /> {tr}seconds{/tr}
						{formhelp note=""}
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
						{html_checkboxes name="site_closed" values="y" checked=$gBitSystem->getConfig('site_closed') labels=false id="site_closed"}
						{formhelp note="Disallow access to the site (except for those with permission)"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Message to display when site is closed" for="site_closed_msg"}
					{forminput}
						<input type="text" name="site_closed_msg" id="site_closed_msg" value="{$gBitSystem->getConfig('site_closed_msg')}" size="50" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Disallow access when load is above the threshold" for="site_load_threshold"}
					{forminput}
						{html_checkboxes name="site_load_threshold" values="y" checked=$BitSystemPrefs.site_load_threshold labels=false id="site_load_threshold"}
						{formhelp note="Disallow access when load is above the threshold (except for those with permission)"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Max average server load threshold in the last minute" for="site_load_threshold"}
					{forminput}
						<input type="text" name="site_load_threshold" id="site_load_threshold" value="{$gBitSystem->getConfig('site_load_threshold')}" size="5" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Message to display when server is too busy" for="site_busy_msg"}
					{forminput}
						<input type="text" name="site_busy_msg" id="site_busy_msg" value="{$gBitSystem->getConfig('site_busy_msg')}" size="50" />
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
					{formlabel label="Use proxy" for="site_use_proxy"}
					{forminput}
						{html_checkboxes name="site_use_proxy" values="y" checked=$site_use_proxy labels=false id="site_use_proxy"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Proxy Host" for="site_proxy_host"}
					{forminput}
						<input type="text" name="site_proxy_host" id="site_proxy_host" value="{$site_proxy_host|escape}" size="50" />
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Proxy port" for="site_proxy_port"}
					{forminput}
						<input type="text" name="site_proxy_port" id="site_proxy_port" value="{$site_proxy_port|escape}" size="50" />
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
