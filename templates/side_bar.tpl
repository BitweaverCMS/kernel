{strip}
	<div id="cssmenu">
	<ul>
		<li><a href="/wiki/">Home</a><ul></ul></li>
		<li>
			{section name=ix loop=$menu}
				{if $menu[ix].pos ne ''}
					{if $menu[ix].first}<ul>{else}</li>{/if}
					{if $menu[ix].last}</ul>{else}
						<li><a href="{$menu[ix].display_url}">
						{if $menu[ix].page_alias}{$menu[ix].page_alias}{else}{$menu[ix].title|escape}{/if}</a>
					{/if}
				{/if}
				<ul>
				{section name=six loop=$menu[ix].sub}
					<li><a href="{$menu[ix].sub[six].display_url}">
						{if $menu[ix].sub[six].page_alias}{$menu[ix].sub[six].page_alias}
						{else}{$menu[ix].sub[six].title|escape}{/if}
					</a></li>
				{/section}
			  	</ul>
			{/section}
		</li>
	</ul><!-- end outermost .toc -->
	</div>
{/strip}
