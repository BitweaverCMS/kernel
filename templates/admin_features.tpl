{* $Header: /cvsroot/bitweaver/_bit_kernel/templates/admin_features.tpl,v 1.1 2005/06/19 04:52:54 bitweaver Exp $ *}
{strip}
{form}
	{jstabs}
		{jstab title="Tiki Settings"}
			<input type="hidden" name="page" value="{$page}" />
			{legend legend="Tiki Settings"}
				{foreach from=$formFeaturesTiki key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
				<div class="row submit">
					<input type="submit" name="tikiTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Tiki Help"}
			{legend legend="Tiki Help Features"}
				{foreach from=$formFeaturesHelp key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
				<div class="row submit">
					<input type="submit" name="helpTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Content Features"}
			{legend legend="Content Features"}
				{foreach from=$formFeaturesContent key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
				<div class="row submit">
					<input type="submit" name="contentTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Administrative Features"}
			{legend legend="Administrative Features"}
				{foreach from=$formFeaturesAdmin key=feature item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$feature}
						{forminput}
							{html_checkboxes name="$feature" values="y" checked=`$gBitSystemPrefs.$feature` labels=false id=$feature}
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}
				<div class="row submit">
					<input type="submit" name="adminTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

		{jstab title="Anonymous Contact"}
			{legend legend="Anonymous Contact"}
				<div class="row">
					{formlabel label="Contact Us" for="feature_contact"}
					{forminput}
						{html_checkboxes name="feature_contact" values="y" checked=`$gBitSystemPrefs.feature_contact` labels=false id="feature_contact"}
						{formhelp note="Enables anonymous users to send a message to a specified user using a form" page="ContactUs"}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Contact user" for="contact_user"}
					{forminput}
						{html_options name="contact_user" output=$users_list values=$users_list selected=$contact_user id="contact_user"}
						{formhelp note="Pick the user who should recieve the meassages sent using the 'Contact Us' feature"}
					{/forminput}
				</div>
				<div class="row submit">
					<input type="submit" name="anonTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}
	{/jstabs}
{/form}
{/strip}
