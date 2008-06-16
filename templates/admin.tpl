{strip}

<div class="floaticon">
	{if $package}
		{assign var=iconsize value=$gBitSystem->getConfig("site_icon_size")}
		{biticon ipackage="$package" iname="pkg_`$package`" iexplain="$package" iclass="$iconsize icon"}
	{/if}
	{bithelp}
</div>

<div class="admin {$package}">
	<div class="header">
		{if $package}
			<h1>{tr}Configure{/tr}: {tr}{$pageName|default:$page|replace:"_":" "|capitalize}{/tr}</h1>
		{else}
			<h1>{tr}Administration{/tr}</h1>
		{/if}
	</div>

	{* The rest determines which page to include using "page" GET parameter. Default: list-sections
	Add a value in first check when you create a new admin page. *}
	<div class="body">
		{if $file }
			{include file="bitpackage:$package/admin_`$file`.tpl"}
		{else}
			{if $smarty.request.version_check}
				{if $version_info.error.number ne 0}
					{formfeedback error=$version_info.error.string}
				{elseif $version_info}
					{legend legend="Version Check"}
						{if $version_info.compare eq 0 and !$version_info.release}
							{formfeedback success="{tr}Your version is up to date.{/tr}"}
						{elseif $version_info.compare lt 0 or $version_info.release}
							{formfeedback warning="{tr}Your version is not up to date.{/tr}"}
						{elseif $version_info.compare gt 0 or $version_info.release}
							{formfeedback warning="{tr}Seems you are using a test version.{/tr}"}
						{/if}

						<div class="row">
							{formlabel label="Your Version"}
							{forminput}
								<strong>bitweaver {$version_info.local}</strong>
							{/forminput}
						</div>

						{if $version_info.compare lt 0}
							<div class="row">
								{formlabel label="Upgrade"}
								{forminput class=warning}
									<strong>bitweaver {$version_info.upgrade}</strong>
									{formhelp page="ReleaseOneChangelog}
								{/forminput}
							</div>
						{elseif $version_info.compare gt 0}
							<div class="row">
								{formlabel label="Latest Version"}
								{forminput}
									<strong>bitweaver {$version_info.upgrade}</strong>
									{formhelp page="ReleaseOneChangelog}
								{/forminput}
							</div>
						{/if}

						{if $version_info.release}
							<div class="row">
								{formlabel label="Latest Release"}
								{forminput class=warning}
									<strong>bitweaver {$version_info.release}</strong>
									{formhelp page="ReleaseOneChangelog}
								{/forminput}
							</div>
						{/if}
					{/legend}
				{/if}
			{/if}

			<table width="100%" class="menutable">
				<tr>
					{assign var="i" value="1"}
					{foreach key=key item=template from=$adminTemplates}
						<td style="width:25%;vertical-align:top;">
							{box class="`$key`menu menu box" ipackage=$key iname="pkg_`$key`" iexplain="$key" iclass="menuicon" title=$key|capitalize}
								{include file="bitpackage:`$key`/menu_`$key`_admin.tpl"}
							{/box}
						</td>
						{if not ($i++ mod 4)}
							</tr><tr>
						{/if}
					{/foreach}
				</tr>
			</table>
		{/if}
	</div><!-- end .body -->
</div><!-- end .body -->

{/strip}
