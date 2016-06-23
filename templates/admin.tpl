{strip}

<div class="floaticon">
	{bithelp}
	{if $package}
		{assign var=iconsize value=$gBitSystem->getConfig("site_icon_size")}
		{biticon ipackage="$package" iname="pkg_`$package`" iexplain="$package" iclass="$iconsize icon"}
	{/if}
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
		{if $adminFile }
			{include file="bitpackage:$package/admin_`$adminFile`.tpl"}
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

						<div class="form-group">
							{formlabel label="Your Version"}
							{forminput}
								<strong>bitweaver {$version_info.local}</strong>
							{/forminput}
						</div>

						{if $version_info.compare < 0}
							<div class="form-group">
								{formlabel label="Upgrade"}
								{forminput class=warning}
									<strong>bitweaver {$version_info.upgrade}</strong>
									{formhelp page="ReleaseTwoChangelog"}
								{/forminput}
							</div>
						{elseif $version_info.compare > 0}
							<div class="form-group">
								{formlabel label="Latest Version"}
								{forminput}
									<strong>bitweaver {$version_info.upgrade}</strong>
									{formhelp page="ReleaseTwoChangelog"}
								{/forminput}
							</div>
						{/if}

						{if $version_info.release}
							<div class="form-group">
								{formlabel label="Latest Release"}
								{forminput class=warning}
									<strong>bitweaver {$version_info.release}</strong>
									{formhelp page="ReleaseTwoChangelog"}
								{/forminput}
							</div>
						{/if}
					{/legend}
				{/if}
			{/if}

			<div class="panel-group row">
				{assign var="i" value="1"}
				{foreach key=key item=template from=$adminTemplates name=adminTemplates}
					<div class="col-md-3 col-sm-6 col-xs-12">
						<div class="panel panel-default">
							<div class="panel-heading {$key}-menu">{biticon iname="pkg_`$key`" ipackage=$key class="menuicon" style="height:24px"} {$key|capitalize}</div>
							<div class="panel-body">{include file="bitpackage:`$key`/menu_`$key`_admin.tpl" packageMenuClass="unstyled"}</div>
						</div>
					</div>
					{if $smarty.foreach.adminTemplates.iteration%4==0}
						{* Add the extra clearfix for only the required viewport *}
						<div class="clearfix visible-md"></div>
						<div class="clearfix visible-lg"></div>
					{/if}
					{if $smarty.foreach.adminTemplates.iteration%2==0}
						{* Add the extra clearfix for only the required viewport *}
						<div class="clearfix visible-sm"></div>
					{/if}
				{/foreach}
			</div>
		{/if}
	</div><!-- end .body -->
	<div class="clear"></div>
</div><!-- end .body -->

{/strip}
