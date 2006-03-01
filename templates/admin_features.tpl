{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/admin_features.tpl,v 1.8 2006/03/01 21:12:28 spiderr Exp $ *}
{strip}
{form}
	{jstabs}
		{jstab title="bitweaver Settings"}
			<input type="hidden" name="page" value="{$page}" />
			{legend legend="bitweaver Settings"}
				{foreach from=$formFeaturesBit key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
				<div class="row submit">
					<input type="submit" name="bitTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}

			{legend legend="Anonymous Contact"}
				<div class="row">
					{formlabel label="Contact Us" for="site_contact"}
					{forminput}
						{html_checkboxes name="site_contact" values="y" checked=`$gBitSystem->getConfig('site_contact')` labels=false id="site_contact"}
						{formhelp note="Enables anonymous users to send a message to a specified user using a form" page="ContactUs"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Contact user" for="contact_user"}
					{forminput}
						{if $users_list}
							{html_options name="contact_user" output=$users_list values=$users_list selected=$gBitSystem->getConfig('contact_user') id="contact_user"}
						{else}
							<input name="contact_user"  value="{$gBitSystem->getConfig('contact_user')}"  id="contact_user" />
						{/if}
						{formhelp note="Pick the user who should recieve the meassages sent using the 'Contact Us' feature"}
					{/forminput}
				</div>
				<div class="row submit">
					<input type="submit" name="anonTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="bitweaver Help"}
			{legend legend="bitweaver Help Features"}
				{foreach from=$formFeaturesHelp key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=$gBitSystem->getConfig($feature) labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
				<div class="row submit">
					<input type="submit" name="helpTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}
{/strip}
