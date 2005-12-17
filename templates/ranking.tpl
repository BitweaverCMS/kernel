{strip}
<div class="ranking">
	<div class="header">
		<h1>{tr}Rankings{/tr}</h1>
	</div>

	<div class="body">
		{form legend="Ranking Settings"}
			<div class="row">
				{formlabel label="Select Attribute" for="which"}
				{forminput}
					<select name="which" id="which">
						{section name=ix loop=$allrankings}
							<option value="{$allrankings[ix].value|escape}" {if $which eq $allrankings[ix].value}selected="selected"{/if}>{$allrankings[ix].name}</option>
						{/section}
					</select>
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="Number of items" for="limit"}
				{forminput}
					<select name="limit" id="limit">
						<option value="10" {if $smarty.request.limit eq 10}selected="selected"{/if}>{tr}Top 10{/tr}</option>
						<option value="20" {if $smarty.request.limit eq 20}selected="selected"{/if}>{tr}Top 20{/tr}</option>
						<option value="50" {if $smarty.request.limit eq 50}selected="selected"{/if}>{tr}Top 50{/tr}</option>
						<option value="100" {if $smarty.request.limit eq 100}selected="selected"{/if}>{tr}Top 100{/tr}</option>
					</select>
				{/forminput}
			</div>

			<div class="row submit">
				<input type="submit" name="selrank" value="{tr}Apply settings{/tr}" />
			</div>
		{/form}

		{section name=ix loop=$rankings}
			<h2>{$rankings[ix].title}&nbsp;&nbsp;&nbsp; <small>[{$rankings[ix].y}]</small></h2>
			<ol>
				{section name=xi loop=$rankings[ix].data}
					<li class="{cycle values="even,odd"}">
						<a href="{$rankings[ix].data[xi].href}">{$rankings[ix].data[xi].name}</a>&nbsp;&nbsp;&nbsp; 
						<small>[{$rankings[ix].data[xi].hits|default:"0"}]</small>
					</li>
				{sectionelse}
					<li>{tr}No records found{/tr}</li>
				{/section}
			</ol>
		{/section}
	</div><!-- end .body -->
</div><!-- end .ranking -->
{/strip}
