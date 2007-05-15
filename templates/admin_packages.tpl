{strip}

{form}
	<input type="hidden" name="page" value="{$page}" />
	{jstabs}
		{jstab title="Installed Packages"}
			{legend legend="bitweaver Packages that are installed on your system"}
				<p>
				{tr}Packages with checkmarks are currently enabled, packages without are disabled.  To enable or disable a package, check or uncheck it, and click the 'Modify Activation' button. {/tr}
				</p>
				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if $package.installed && !$package.service}
						<div class="row">
							<div class="formlabel">
								{if !$package.required}<label for="package_{$name}">{/if}{biticon ipackage=$name iname="pkg_`$name`" iexplain="$name" iforce=icon}{if !$package.required}</label>{/if}
							</div>
							{forminput}
								{if $package.required}
									<strong>{$name|capitalize}</strong>: <em>required</em>
								{else}
									<label>
										<strong>{$name|capitalize}</strong>: <input type="checkbox" value="y" name="fPackage[{$name}]" id="package_{$name}" {if $package.active_switch eq 'y' }checked="checked"{/if}/>
									</label>
								{/if}
								{formhelp note=`$package.info` package=$name}
							{/forminput}
						</div>
					{/if}
				{/foreach}
			{/legend}

			{legend legend="bitweaver Services"}
				<p>
					{tr}A service package is a package that allows you to extend the way you display bitweaver content - such as <em>categorising your content</em>. Activating more than one of any service type might lead to conflicts.<br />
					We therefore recommend that you <strong>enable only one of each service type</strong>.{/tr}
				</p>
				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if $package.installed && $package.service}
						<div class="row">
							<div class="formlabel">
								{if !$package.required}<label for="package_{$name}">{/if}{biticon ipackage=$name iname="pkg_`$name`" iexplain="$name" iforce=icon}{if !$package.required}</label>{/if}
							</div>
							{forminput}
								{if $package.required}
									<strong>{$name|capitalize}</strong>: <em>required</em>
								{else}
									<label>
										<strong>{$name|capitalize}</strong>: <input type="checkbox" value="y" name="fPackage[{$name}]" id="package_{$name}" {if $package.active_switch eq 'y' }checked="checked"{/if}/>
										<br />{tr}Service Type{/tr}: <strong>{$package.service|capitalize|replace:"_":" "}</strong>
									</label>
								{/if}
								{formhelp note=`$package.info` package=$name}
							{/forminput}
						</div>
					{/if}
				{/foreach}
			{/legend}

			<div class="row submit">
				<input type="submit" name="features" value="{tr}Modify Activation{/tr}"/>
			</div>
		{/jstab}

		{jstab title="Install Packages"}
			{box title="How to install bitweaver Packages"}
				{tr}To install more packages, please run the <a href='{$smarty.const.INSTALL_PKG_URL}install.php?step=3'>installer</a> to choose your desired packages.{/tr}
				<br />
				<small><strong>{tr}Note{/tr}</strong> : {tr}you might have to rename your 'install/install.done' file back to 'install/install.php' to be able to install more packages{/tr}</small>
			{/box}

			<br />

			{legend legend="bitweaver Packages available for installation"}
				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if ((1 or $package.tables) && !$package.required && !$package.installed) }
						<div class="row">
							<div class="formlabel">
								{biticon ipackage=$name iname="pkg_`$name`" iexplain="$name" iforce=icon}
							</div>
							{forminput}
								{$name|capitalize}
								{formhelp note=`$package.info` package=$name}
							{/forminput}
						</div>
					{/if}
				{/foreach}
			{/legend}

			<br />

			{box title="How to install bitweaver Packages"}
				{tr}To install more packages, please run the <a href='{$smarty.const.INSTALL_PKG_URL}install.php?step=3'>installer</a> to choose your desired packages.{/tr}
				<br />
				<small><strong>{tr}Note{/tr}</strong> : {tr}if you renamed your 'install/install.php' or changed the CHMOD permissions, you'll have to revert those changes to proceed.{/tr}</small>
			{/box}
		{/jstab}
	{/jstabs}
{/form}

{/strip}
