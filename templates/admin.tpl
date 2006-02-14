{strip}

{if $package}
	<div class="floaticon">{biticon ipackage="$package" iname="pkg_`$package`" iexplain="$package" iforce=icon}</div>
{/if}
<div class="floaticon">{bithelp}</div>

<div class="admin {$package}">
	{if $package}
		<div class="header">
			<h1>{tr}Configure {$pageName|default:$page|capitalize}{/tr}</h1>
		</div>
	{else}
		<div class="header">
			<h1>{tr}Administration{/tr}</h1>
		</div>
	{/if}

	{* The rest determines which page to include using "page" GET parameter. Default: list-sections
	Add a value in first check when you create a new admin page. *}
	<div class="body">
		{if $file }
			{include file="bitpackage:$package/admin_`$file`.tpl"}
		{else}
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

			<table width="100%" class="menutable">
				<tr>
					<td style="width:25%;vertical-align:top;" rowspan="10">
						{box class="kernelmenu menu box" ipackage=kernel iname="pkg_kernel" iexplain="kernel" idiv="menuicon" title="Kernel"}
							{include file=$kernelTemplate}
						{/box}
					</td>

					{assign var="i" value="1"}
					{foreach key=key item=template from=$adminTemplates}
						{if $key ne "kernel"}
							<td style="width:25%;vertical-align:top;">
								{box class="`$key`menu menu box" ipackage=$key iname="pkg_`$key`" iexplain="$key" idiv="menuicon" title="$key"}
									{include file="bitpackage:`$key`/menu_`$key`_admin.tpl"}
								{/box}
							</td>
							{if not ($i++ mod 3)}
								</tr><tr>
							{/if}
						{/if}
					{/foreach}
				</tr>
			</table>
		{/if}
	</div><!-- end .body -->
</div><!-- end .body -->

{/strip}
