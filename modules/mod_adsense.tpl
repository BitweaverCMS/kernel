{if $adSenseActive}
{strip}
	{bitmodule title="$moduleTitle" name="adsense"}
		{adsense client=$modParams.client format=$modParams.format type=$modParams.type channel=$modParams.channel width=$modParams.width height=$modParams.height cpa=$modParams.cpa}
	{/bitmodule}
{/strip}
{/if}
