{strip}

{form class="kernel_`$page`|replace:'packages':'pkg'"}
	<input type="hidden" name="page" value="{$page}" />
	{jstabs}
		{jstab title="Installed"}
			{legend legend="bitweaver packages installed on your system"}
				<p>
				{tr}Packages with checkmarks are currently enabled, packages without are disabled.  To enable or disable a package, check or uncheck it, and click the 'Modify Activation' button. {/tr}
				</p>

				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if $package.installed && !$package.service && !$package.required}
						<div class="row">
							<div class="formlabel">
								<label for="package_{$name}">{biticon ipackage=$name iname="pkg_`$name`" iexplain="$name" iforce=icon}</label>
							</div>
							{forminput}
								<label>
									<input type="checkbox" value="y" name="fPackage[{$name}]" id="package_{$name}" {if $package.active_switch eq 'y' }checked="checked"{/if} />
									&nbsp;
									<strong>{$name|capitalize}</strong>
								</label>
								{formhelp note=`$package.info` package=$name}
							{/forminput}
						</div>
					{/if}
				{/foreach}
				
			{/legend}
			
				

			{legend legend="bitweaver services installed on your system"}
				<p>
					{tr}A service package is a package that allows you to extend the way you display bitweaver content - such as <em>categorising your content</em>. Activating more than one of any service type might lead to conflicts.<br />
					We therefore recommend that you <em>	enable only one of each</em> <strong>service type</strong>.{/tr}
				</p>
				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if $package.installed && $package.service && !$package.required}
						<div class="row">
							<div class="formlabel">
								{if !$package.required}<label for="package_{$name}">{/if}{biticon ipackage=$name iname="pkg_`$name`" iexplain="$name" iforce=icon}{if !$package.required}</label>{/if}
							</div>
							{forminput}
								<label>
									<input type="checkbox" value="y" name="fPackage[{$name}]" id="package_{$name}" {if $package.active_switch eq 'y' }checked="checked"{/if} />
									&nbsp;
									<strong>{$name|capitalize}</strong>
									<br />
									{tr}Service Type{/tr}: <strong>{$package.service|capitalize|replace:"_":" "}</strong>
								</label>
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
		


		{jstab title="Required"}
			{legend legend="Required bitweaver packages installed on your system"}
				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if $package.installed && !$package.service && $package.required}
						<div class="row">
							<div class="formlabel">
								{biticon ipackage=$name iname="pkg_`$name`" iexplain="$name" iforce=icon}
							</div>
							{forminput}
									<strong>{$name|capitalize}</strong>
									{formhelp note=`$package.info` package=$name}
							{/forminput}
						</div>
					{/if}
				{/foreach}
			{/legend}
			{legend legend="Required bitweaver services installed on your system"}
				{foreach key=name item=package from=$gBitSystem->mPackages}
					{if $package.installed && $package.service && $package.required}
						<div class="row">
							<div class="formlabel">
								{biticon ipackage=$name iname="pkg_`$name`" iexplain="$name" iforce=icon}
							</div>
							{forminput}
								<label>
									<strong>{$name|capitalize}</strong>
								</label>
								{formhelp note=`$package.info` package=$name}
							{/forminput}
						</div>
					{/if}
				{/foreach}
			{/legend}
		{/jstab}


		

		{jstab title="Install new packages"}

			{legend legend="bitweaver Packages available for installation"}
			
				<div class="row">
					<div class="formlabel">
						{biticon ipackage=install iname="pkg_install" iexplain="install" iforce=icon}
					</div>
					{forminput}
						<label>
							<strong>Install</strong>
						</label>
						<p><strong><a class="warning" href='{$smarty.const.INSTALL_PKG_URL}install.php?step=3'>{tr}Click here to install more Packages{/tr}&nbsp;&hellip;</a></strong></p>

						{assign var=installfile value="`$smarty.const.INSTALL_PKG_PATH`install.php"|is_file}
						{assign var=installread value="`$smarty.const.INSTALL_PKG_PATH`install.php"|is_readable}
					
						{if $installfile neq 1 and $installread neq 1}
							<p>{tr}You might have to rename your <strong>install/install.done</strong> file back to <strong>install/install.php</strong>.{/tr}</p>
						{/if}
					{/forminput}
				</div>
				
				<hr />

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

		{/jstab}
	{/jstabs}
{/form}

{/strip}
