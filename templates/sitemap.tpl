<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach from=$gSiteMapHash item=map}
{if $map.loc}
	 <url>
		<loc>{$map.loc}</loc>
		{if $map.lastmod}
			<lastmod>{$map.lastmod}</lastmod>
		{/if}
		{if $map.changefreq}
			<changefreq>{$map.changefreq}</changefreq>
		{/if}
		{if $map.priority}
			<priority>{$map.priority}</priority>
		{/if}
	 </url>
{/if}
{/foreach}
</urlset>
