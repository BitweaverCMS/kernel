<div class="floaticon">{bithelp}</div>

<div class="admin sitemaps">
	<div class="header">
		<h1>{tr}Sitemaps{/tr}</h1>
	</div>

	<div class="body">
		{tr}Root Sitemap{/tr} <a href="{$smarty.const.BIT_ROOT_URI}sitemap.php">{$smarty.const.BIT_ROOT_URI}sitemap.php</a>
			<dl>
			{foreach from=$gSiteMapHash item=mapHash key=package}
				<dt>{$package}</dt>
				<dd>{tr}URL{/tr}: <a href="{$mapHash.loc}">{$mapHash.loc}</a></dd>
				<dd>{tr}Last Modified{/tr}: {$mapHash.lastMod}</dd>
			{foreachelse}
				<dt>{tr}No sitemaps found.{/tr}</dt>
			{/foreach}
			</dl>	
	</div><!-- end .body -->
</div><!-- end .sitemaps -->
